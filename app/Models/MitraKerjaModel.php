<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraKerjaModel extends Model
{
    protected $table            = 'mitra_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nama_perusahaan',
        'slug',
        'kategori_bisnis',
        'logo',
        'deskripsi',
        'alamat',
        'telepon',
        'email',
        'website',
        'durasi_tahun',
        'status_kemitraan',
        'alasan_penolakan',
        'approved_at',
        'expired_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function refreshExpiredStatuses(): void
    {
        $today = date('Y-m-d');
        $this->db->table($this->table)
            ->where('status_kemitraan', 'active')
            ->where('expired_at IS NOT NULL', null, false)
            ->where('expired_at <', $today)
            ->update(['status_kemitraan' => 'expired']);
    }

    public function generateUniqueSlug(string $namaPerusahaan, ?int $ignoreId = null): string
    {
        $baseSlug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $namaPerusahaan), '-'));
        if ($baseSlug === '') {
            $baseSlug = 'mitra-kerja';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $builder = $this->where('slug', $slug);
            if ($ignoreId !== null) {
                $builder->where('id !=', $ignoreId);
            }
            if ($builder->countAllResults() === 0) {
                break;
            }
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }
}
