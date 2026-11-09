<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsHeroBackgroundModel extends Model
{
    protected $table            = 'cms_hero_backgrounds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul',
        'gambar',
        'urutan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

