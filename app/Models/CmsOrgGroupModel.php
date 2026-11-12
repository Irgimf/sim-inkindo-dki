<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Stores logical groupings (Pimpinan, Kesekretariatan, Bidang, Badan, etc.) within each org sub-unit.
 */
class CmsOrgGroupModel extends Model
{
    protected $table            = 'cms_org_groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_slug',
        'nama_kelompok',
        'kode_badge',
        'tipe_tampilan',
        'section_label',
        'is_full_width',
        'urutan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

