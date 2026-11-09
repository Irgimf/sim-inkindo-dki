<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table            = 'companies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'member_id',
        'nama_pt',
        'nama_pjbu',
        'no_kta',
        'status',
        'kualifikasi',
        'wilayah',
        'kategori_sbu',
        'alamat_pt',
        'alamat_surat',
        'telepon',
        'fax',
        'email',
        'website',
    ];

    /**
     * Map Kategori SBU from CodeIgniter format to Laravel (ProjectWeb) format.
     */
    public static function mapKategoriToLaravel(string $kategori): string
    {
        $kategori = trim($kategori);
        if ($kategori === 'NJK dan JK' || $kategori === 'Jasa Konstruksi dan Non Jasa Konstruksi') {
            return 'Jasa Konstruksi dan Non Jasa Konstruksi';
        }
        if ($kategori === 'Tidak Punya NJK dan JK' || $kategori === 'Tidak Punya Keduanya') {
            return 'Tidak Punya Keduanya';
        }
        if ($kategori === 'Non Jasa Konstruksi') {
            return 'Non Jasa Konstruksi';
        }
        return 'Jasa Konstruksi';
    }

    /**
     * Map Kategori SBU from Laravel (ProjectWeb) format to CodeIgniter format.
     */
    public static function mapKategoriToCi4(string $sbu): string
    {
        $sbu = trim($sbu);
        if ($sbu === 'Jasa Konstruksi dan Non Jasa Konstruksi' || $sbu === 'NJK dan JK') {
            return 'NJK dan JK';
        }
        if ($sbu === 'Tidak Punya Keduanya' || $sbu === 'Tidak Punya NJK dan JK') {
            return 'Tidak Punya NJK dan JK';
        }
        if ($sbu === 'Non Jasa Konstruksi') {
            return 'Non Jasa Konstruksi';
        }
        return 'Jasa Konstruksi';
    }

    /**
     * Determine parent SBU classification label from Kode KBLI for ProjectWeb detail modal.
     */
    public static function getClassificationLabel(string $kodeKbli, string $judulKbli = ''): string
    {
        $kode = strtoupper(trim($kodeKbli));
        if (str_starts_with($kode, 'PR1')) {
            return 'PR1 - Perencanaan Penataan Ruang';
        }
        if (str_starts_with($kode, 'PR2')) {
            return 'PR2 - Pengawasan Penataan Ruang';
        }
        if (str_starts_with($kode, 'AR')) {
            return 'AR - Perencanaan Arsitektur';
        }
        if (str_starts_with($kode, 'RK')) {
            return 'RK - Rekayasa';
        }
        if (str_starts_with($kode, 'AL')) {
            return 'AL - Pengembangan Pemanfaatan Ruang & Lanskap';
        }
        if (str_starts_with($kode, '702')) {
            return '702 - Konsultasi Manajemen dan Bisnis';
        }
        return $judulKbli ? "{$kode} - {$judulKbli}" : $kode;
    }

    /**
     * Ensure `jenis_sbu` column exists on `sbu` table.
     */
    public static function ensureJenisSbuColumn(): void
    {
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('sbu') && !$db->fieldExists('jenis_sbu', 'sbu')) {
                $db->query("ALTER TABLE `sbu` ADD COLUMN `jenis_sbu` VARCHAR(50) NULL DEFAULT NULL AFTER `company_id`");
            }
        } catch (\Throwable $e) {
            // Ignore if already exists
        }
    }

    /**
     * Resolve whether an SBU row belongs to 'Jasa Konstruksi' or 'Non Jasa Konstruksi'.
     */
    public static function resolveJenisSbu(?string $jenisSbu, string $kodeKbli = '', string $companyKategori = 'Jasa Konstruksi'): string
    {
        $kat = trim($companyKategori);
        if ($kat === 'Non Jasa Konstruksi') {
            return 'Non Jasa Konstruksi';
        }
        if ($kat === 'Jasa Konstruksi') {
            return 'Jasa Konstruksi';
        }

        // Company has both JK and NJK ('NJK dan JK' / 'Jasa Konstruksi dan Non Jasa Konstruksi')
        $jenis = trim((string) $jenisSbu);
        if ($jenis === 'Non Jasa Konstruksi' || strtoupper($jenis) === 'NJK') {
            return 'Non Jasa Konstruksi';
        }
        if ($jenis === 'Jasa Konstruksi' || strtoupper($jenis) === 'JK') {
            return 'Jasa Konstruksi';
        }

        // Fallback inference from Kode KBLI for legacy rows without explicit jenis_sbu
        $kode = strtoupper(trim($kodeKbli));
        if ($kode !== '' && (preg_match('/^[0-9]/', $kode) || str_starts_with($kode, 'NJK') || str_starts_with($kode, 'SC') || str_starts_with($kode, 'MS'))) {
            return 'Non Jasa Konstruksi';
        }
        return 'Jasa Konstruksi';
    }

    /**
     * Sync a single company and its SBU rows to the `members` table used by ProjectWeb (Laravel).
     */
    public function syncCompanyToMember(int $companyId): void
    {
        self::ensureJenisSbuColumn();

        $company = $this->find($companyId);
        if (!$company) {
            return;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('members')) {
            return;
        }

        // Build sbu_items JSON directly 1-to-1 from `sbu` table for this company (without parent PR1/PR2 grouping)
        $sbuRows = $db->table('sbu')->where('company_id', $companyId)->orderBy('id', 'ASC')->get()->getResultArray();
        $sbuItems = [];
        foreach ($sbuRows as $s) {
            $subLabel = trim(($s['kode_kbli'] ?? '') . ' - ' . ($s['judul_kbli'] ?? ''), ' -');
            $kelas = $s['kualifikasi'] ?: ($company['kualifikasi'] ?: 'Menengah');
            $masa = !empty($s['masa_berlaku']) ? date('d-m-Y', strtotime($s['masa_berlaku'])) : '-';
            $jenisSbu = self::resolveJenisSbu($s['jenis_sbu'] ?? null, (string) ($s['kode_kbli'] ?? ''), (string) ($company['kategori_sbu'] ?? 'Jasa Konstruksi'));

            if ($subLabel !== '') {
                $sbuItems[] = [
                    'code'        => $subLabel,
                    'jenis'       => $jenisSbu,
                    'kelas'       => $kelas,
                    'masaBerlaku' => $masa,
                    'subbidang'   => [],
                ];
            }
        }
        $sbuItemsJson = json_encode($sbuItems, JSON_UNESCAPED_UNICODE);

        $now = date('Y-m-d H:i:s');
        $memberData = [
            'name'            => $company['nama_pt'],
            'npa'             => $company['no_kta'],
            'status'          => !empty($company['status']) ? $company['status'] : 'Penuh',
            'kualifikasi'     => !empty($company['kualifikasi']) ? $company['kualifikasi'] : 'Menengah',
            'wilayah'         => !empty($company['wilayah']) ? $company['wilayah'] : 'Jakarta Selatan',
            'sbu'             => self::mapKategoriToLaravel($company['kategori_sbu'] ?? 'Jasa Konstruksi'),
            'alamat_domisili' => $company['alamat_pt'] ?? '-',
            'alamat_surat'    => !empty($company['alamat_surat']) ? $company['alamat_surat'] : ($company['alamat_pt'] ?? '-'),
            'telepon'         => !empty($company['telepon']) ? $company['telepon'] : '-',
            'fax'             => !empty($company['fax']) ? $company['fax'] : '-',
            'email'           => !empty($company['email']) ? $company['email'] : '-',
            'website'         => !empty($company['website']) ? $company['website'] : '-',
            'sbu_items'       => $sbuItemsJson,
            'updated_at'      => $now,
        ];

        $membersTable = $db->table('members');
        $existingMember = null;

        if (!empty($company['member_id'])) {
            $existingMember = $membersTable->where('id', $company['member_id'])->get()->getRowArray();
        }
        if (!$existingMember && !empty($company['no_kta'])) {
            $existingMember = $membersTable->where('npa', $company['no_kta'])->get()->getRowArray();
        }

        if ($existingMember) {
            $membersTable->where('id', $existingMember['id'])->update($memberData);
            if (empty($company['member_id']) || (int)$company['member_id'] !== (int)$existingMember['id']) {
                $db->table('companies')->where('id', $companyId)->update(['member_id' => $existingMember['id']]);
            }
        } else {
            $memberData['created_at'] = $now;
            $membersTable->insert($memberData);
            $newMemberId = $db->insertID();
            $db->table('companies')->where('id', $companyId)->update(['member_id' => $newMemberId]);
        }
    }

    /**
     * Delete a company along with its SBUs, SBU projects, and corresponding record in `members`.
     */
    public function deleteCompanyAndMember(int $companyId): void
    {
        $company = $this->find($companyId);
        if (!$company) {
            return;
        }

        $db = \Config\Database::connect();

        // Delete SBU projects and SBU records
        $sbus = $db->table('sbu')->where('company_id', $companyId)->get()->getResultArray();
        foreach ($sbus as $s) {
            $db->table('sbu_projects')->where('sbu_id', $s['id'])->delete();
        }
        $db->table('sbu')->where('company_id', $companyId)->delete();

        // Delete from members table in ProjectWeb
        if ($db->tableExists('members')) {
            if (!empty($company['member_id'])) {
                $db->table('members')->where('id', $company['member_id'])->delete();
            } elseif (!empty($company['no_kta'])) {
                $db->table('members')->where('npa', $company['no_kta'])->delete();
            }
        }

        $this->delete($companyId);
    }

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
