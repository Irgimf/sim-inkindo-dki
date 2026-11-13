<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsLelangPortalModel extends Model
{
    protected $table            = 'cms_lelang_portal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'ikon',
        'kategori',
        'judul',
        'deskripsi',
        'teks_tombol',
        'url',
        'urutan',
    ];
}

