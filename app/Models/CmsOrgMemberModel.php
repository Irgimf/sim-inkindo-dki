<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Stores individual personnel entries with editable role/jabatan text for Struktur Organisasi.
 */
class CmsOrgMemberModel extends Model
{
    protected $table            = 'cms_org_members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_slug',
        'group_id',
        'jabatan',
        'nama',
        'keterangan_badge',
        'is_highlight',
        'urutan',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

