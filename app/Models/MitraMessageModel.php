<?php

namespace App\Models;

use CodeIgniter\Model;

class MitraMessageModel extends Model
{
    protected $table            = 'mitra_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'mitra_id',
        'nama_perusahaan',
        'email_perusahaan',
        'no_telepon',
        'nama_pic',
        'pesan',
        'is_read',
        'created_at',
    ];

    protected $useTimestamps = false;
}

