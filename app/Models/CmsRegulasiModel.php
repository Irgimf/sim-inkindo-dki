<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsRegulasiModel extends Model
{
    protected $table            = 'cms_regulasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kategori',
        'sub_kategori',
        'judul',
        'deskripsi',
        'file_pdf',
        'file_name',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

