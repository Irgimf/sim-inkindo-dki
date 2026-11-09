<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsBeritaModel extends Model
{
    protected $table            = 'cms_berita';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipe',
        'judul',
        'slug',
        'kategori_label',
        'badge',
        'tanggal',
        'tanggal_label',
        'penulis',
        'lokasi_meta',
        'waktu_kegiatan',
        'biaya_kegiatan',
        'kuota_kegiatan',
        'ukuran_file',
        'jumlah_halaman',
        'gambar',
        'file_pdf',
        'external_url',
        'ringkasan',
        'konten',
        'galeri_foto_json',
        'homepage_slot',
        'publikasi_slot',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'berita-' . time();
        }

        $db = \Config\Database::connect();
        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            $builder = $db->table('cms_berita')->where('slug', $slug);
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

