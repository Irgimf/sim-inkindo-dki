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
     * Single Source of Truth:
     * `companies`, `sbu`, dan `sbu_projects` kini menjadi sumber data tunggal
     * bagi sim-inkindo-dki maupun inkindo-dki-web, sehingga tidak perlu lagi
     * menduplikasi data ke tabel `members`.
     */
    public function syncCompanyToMember(int $companyId): void
    {
        // Pertahankan method sebagai no-op untuk kompatibilitas pemanggil lama.
        self::ensureJenisSbuColumn();
    }

    /**
     * Delete a company along with its SBUs and SBU projects atomically.
     */
    public function deleteCompanyAndMember(int $companyId): void
    {
        $company = $this->find($companyId);
        if (!$company) {
            return;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $sbuRows = $db->table('sbu')->select('id')->where('company_id', $companyId)->get()->getResultArray();
        $sbuIds = array_values(array_filter(array_map(static fn ($s) => (int) ($s['id'] ?? 0), $sbuRows)));
        if (!empty($sbuIds)) {
            $db->table('sbu_projects')->whereIn('sbu_id', $sbuIds)->delete();
            $db->table('sbu')->whereIn('id', $sbuIds)->delete();
        }

        $this->delete($companyId);
        $db->transComplete();
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
