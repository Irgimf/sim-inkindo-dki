<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraProductModel extends Model
{
    protected $table            = 'mitra_products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mitra_id',
        'nama_produk',
        'kategori_produk',
        'deskripsi_produk',
        'spesifikasi',
        'gambar_produk',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

