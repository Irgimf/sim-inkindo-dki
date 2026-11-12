<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Stores metadata for the 7 organizational sub-units (DPPH, DPPL, Badan, Komite, DKP, DPOP, Setprov).
 */
class CmsOrgUnitModel extends Model
{
    protected $table            = 'cms_org_units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'slug',
        'nama',
        'deskripsi',
        'intro_text',
        'icon',
        'urutan',
        'updated_at',
    ];

    protected $useTimestamps = false;
}

