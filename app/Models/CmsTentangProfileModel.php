<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Stores editable Hero, Sejarah, Visi & Misi, and Kode Etik content for Tentang Kami.
 */
class CmsTentangProfileModel extends Model
{
    protected $table            = 'cms_tentang_profile';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'sejarah_heading',
        'sejarah_content',
        'sejarah_image',
        'visimisi_heading',
        'visimisi_bg_image',
        'visi_title',
        'visi_text',
        'misi_title',
        'misi_items_json',
        'kode_etik_heading',
        'kode_etik_subtitle',
        'kode_etik_items_json',
        'updated_at',
    ];

    protected $useTimestamps = false;
}

