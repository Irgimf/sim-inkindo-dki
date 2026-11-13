<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsMitraKetentuanItemModel extends Model
{
    protected $table            = 'cms_mitra_ketentuan_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'jenis',
        'ikon',
        'judul',
        'deskripsi',
        'urutan',
    ];
}

