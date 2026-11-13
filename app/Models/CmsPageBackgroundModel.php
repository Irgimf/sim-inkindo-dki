<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsPageBackgroundModel extends Model
{
    protected $table            = 'cms_page_backgrounds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'page_key',
        'page_name',
        'section_label',
        'image_path',
        'default_path',
        'updated_at',
    ];
}

