<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsHeroBannerModel extends Model
{
    protected $table            = 'cms_hero_banners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mode_sumber',
        'berita_id',
        'subjudul',
        'judul',
        'gambar',
        'target_url',
        'urutan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

