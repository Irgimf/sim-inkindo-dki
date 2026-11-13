<?php

namespace App\Controllers;

class CmsFooterContact extends BaseController
{
    public function __construct()
    {
        $this->ensureTableAndSeed();
    }

    private function ensureTableAndSeed(): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        if (! $db->tableExists('cms_footer_contact')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `cms_footer_contact` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `bio` TEXT NULL,
                    `alamat` TEXT NULL,
                    `telepon_1` VARCHAR(60) NOT NULL,
                    `telepon_2` VARCHAR(60) NULL DEFAULT NULL,
                    `fax` VARCHAR(60) NULL DEFAULT NULL,
                    `email_1` VARCHAR(150) NOT NULL,
                    `email_2` VARCHAR(150) NULL DEFAULT NULL,
                    `jam_operasional` VARCHAR(150) NOT NULL,
                    `youtube_url` VARCHAR(255) NULL DEFAULT NULL,
                    `facebook_url` VARCHAR(255) NULL DEFAULT NULL,
                    `x_url` VARCHAR(255) NULL DEFAULT NULL,
                    `instagram_url` VARCHAR(255) NULL DEFAULT NULL,
                    `copyright_text` VARCHAR(255) NOT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");

            $db->table('cms_footer_contact')->insert([
                'bio'             => 'Ikatan Nasional Konsultan Indonesia (INKINDO) Provinsi DKI Jakarta merupakan wadah asosiasi perusahaan jasa konsultansi independen yang profesional dan berintegritas.',
                'alamat'          => 'Jl. Pertani No. 7, Duren Tiga - Pancoran, Jakarta Selatan 12760, DKI Jakarta',
                'telepon_1'       => '+62217942940',
                'telepon_2'       => '+6282112079750',
                'fax'             => '+62217942941',
                'email_1'         => 'sekretariat@inkindo-dki.org',
                'email_2'         => 'inkindo_dki@yahoo.com',
                'jam_operasional' => 'Senin - Jumat: 08.00 - 16.30 WIB',
                'youtube_url'     => 'https://www.youtube.com/@inkindodkiofficial',
                'facebook_url'    => 'https://www.facebook.com/inkindodki',
                'x_url'           => 'https://x.com/inkindoD',
                'instagram_url'   => 'https://www.instagram.com/inkindo_dki/',
                'copyright_text'  => '© 2026 DPP INKINDO DKI Jakarta. Hak Cipta Dilindungi.',
                'updated_at'      => $now,
            ]);
        }
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $db   = \Config\Database::connect();
        $data = $db->table('cms_footer_contact')->orderBy('id', 'ASC')->get()->getRowArray() ?? [];

        return view('cms/footer_contact', [
            'title' => 'Admin CMS - Footer & Contact',
            'data'  => $data,
        ]);
    }

    public function save()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $payload = [
            'bio'             => trim((string) ($this->request->getPost('bio') ?? '')),
            'alamat'          => trim((string) ($this->request->getPost('alamat') ?? '')),
            'telepon_1'       => trim((string) ($this->request->getPost('telepon_1') ?? '')),
            'telepon_2'       => trim((string) ($this->request->getPost('telepon_2') ?? '')),
            'fax'             => trim((string) ($this->request->getPost('fax') ?? '')),
            'email_1'         => trim((string) ($this->request->getPost('email_1') ?? '')),
            'email_2'         => trim((string) ($this->request->getPost('email_2') ?? '')),
            'jam_operasional' => trim((string) ($this->request->getPost('jam_operasional') ?? '')),
            'youtube_url'     => trim((string) ($this->request->getPost('youtube_url') ?? '')),
            'facebook_url'    => trim((string) ($this->request->getPost('facebook_url') ?? '')),
            'x_url'           => trim((string) ($this->request->getPost('x_url') ?? '')),
            'instagram_url'   => trim((string) ($this->request->getPost('instagram_url') ?? '')),
            'copyright_text'  => trim((string) ($this->request->getPost('copyright_text') ?? '')),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        if ($payload['alamat'] === '' || $payload['telepon_1'] === '' || $payload['email_1'] === '') {
            return redirect()->to('/cms/footer-contact')->with('error', 'Alamat, Telepon Utama, dan Email Utama wajib diisi.');
        }

        $db       = \Config\Database::connect();
        $existing = $db->table('cms_footer_contact')->orderBy('id', 'ASC')->get()->getRowArray();

        if ($existing) {
            $db->table('cms_footer_contact')->where('id', $existing['id'])->update($payload);
        } else {
            $db->table('cms_footer_contact')->insert($payload);
        }

        return redirect()->to('/cms/footer-contact')->with('pesan', 'Pengaturan Footer & Kontak (Hubungi Kami) berhasil diperbarui.');
    }
}

