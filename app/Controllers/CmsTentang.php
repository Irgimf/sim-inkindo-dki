<?php

namespace App\Controllers;

use App\Libraries\TentangCmsSchema;
use App\Models\CmsOrgGroupModel;
use App\Models\CmsOrgMemberModel;
use App\Models\CmsOrgUnitModel;
use App\Models\CmsTentangProfileModel;

class CmsTentang extends BaseController
{
    protected $profileModel;
    protected $unitModel;
    protected $groupModel;
    protected $memberModel;

    public function __construct()
    {
        TentangCmsSchema::ensureInitialized();
        $this->profileModel = new CmsTentangProfileModel();
        $this->unitModel    = new CmsOrgUnitModel();
        $this->groupModel   = new CmsOrgGroupModel();
        $this->memberModel  = new CmsOrgMemberModel();
    }

    public function index()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $profile = $this->profileModel->first();
        if (!$profile) {
            $profile = TentangCmsSchema::getDefaultProfileData();
            $profile['id'] = 0;
        }

        $units = $this->unitModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
        $unitSlugs = array_column($units, 'slug');

        $groups = $this->groupModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
        $members = $this->memberModel->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();

        $groupsByUnit = [];
        $groupMap = [];
        foreach ($groups as $g) {
            $slug = (string) ($g['unit_slug'] ?? '');
            $groupsByUnit[$slug][] = $g;
            $groupMap[(int) $g['id']] = $g;
        }

        $membersByUnit = [];
        $activeCountByUnit = [];
        foreach ($unitSlugs as $s) {
            $membersByUnit[$s] = [];
            $activeCountByUnit[$s] = 0;
        }
        foreach ($members as $m) {
            $slug = (string) ($m['unit_slug'] ?? '');
            $membersByUnit[$slug][] = $m;
            if ((int) ($m['is_active'] ?? 1) === 1) {
                $activeCountByUnit[$slug] = ($activeCountByUnit[$slug] ?? 0) + 1;
            }
        }

        $activeTab = trim((string) $this->request->getGet('tab'));
        if (!in_array($activeTab, ['profil', 'struktur'], true)) {
            $activeTab = 'profil';
        }

        $activeUnit = trim((string) $this->request->getGet('unit'));
        if (!in_array($activeUnit, $unitSlugs, true)) {
            $activeUnit = $unitSlugs[0] ?? 'dpph';
        }

        return view('cms/tentang', [
            'title'             => 'Admin CMS - Tentang Kami',
            'profile'           => $profile,
            'units'             => $units,
            'groupsByUnit'      => $groupsByUnit,
            'groupMap'          => $groupMap,
            'membersByUnit'     => $membersByUnit,
            'activeCountByUnit' => $activeCountByUnit,
            'activeTab'         => $activeTab,
            'activeUnit'        => $activeUnit,
        ]);
    }

    private function resolveProfileImage(string $cropField, string $fileField, string $existingField, string $fallback): string
    {
        $croppedData = (string) $this->request->getPost($cropField);
        if ($croppedData !== '') {
            $saved = $this->handleBase64ImageUpload($croppedData, 'cms_uploads', 'tentang_');
            if ($saved) {
                return $saved;
            }
        }

        $uploaded = $this->handleSingleUpload($fileField, ['jpg', 'jpeg', 'png', 'webp', 'gif'], 'cms_uploads', 'tentang_');
        if ($uploaded) {
            return $uploaded;
        }

        $existing = trim((string) $this->request->getPost($existingField));
        return $existing !== '' ? $existing : $fallback;
    }

    private function normalizeListInputs($rawItems): array
    {
        if (!is_array($rawItems)) {
            return [];
        }
        $cleaned = [];
        foreach ($rawItems as $item) {
            $txt = trim((string) $item);
            if ($txt !== '') {
                $cleaned[] = $txt;
            }
        }
        return $cleaned;
    }

    public function saveProfile()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $defaults = TentangCmsSchema::getDefaultProfileData();
        $heroTitle = trim((string) $this->request->getPost('hero_title')) ?: $defaults['hero_title'];
        $heroSubtitle = trim((string) $this->request->getPost('hero_subtitle'));
        $sejarahHeading = trim((string) $this->request->getPost('sejarah_heading')) ?: $defaults['sejarah_heading'];
        $sejarahContent = trim((string) $this->request->getPost('sejarah_content'));
        $visimisiHeading = trim((string) $this->request->getPost('visimisi_heading')) ?: $defaults['visimisi_heading'];
        $visiTitle = trim((string) $this->request->getPost('visi_title')) ?: 'Visi';
        $visiText = trim((string) $this->request->getPost('visi_text'));
        $misiTitle = trim((string) $this->request->getPost('misi_title')) ?: 'Misi';
        $kodeEtikHeading = trim((string) $this->request->getPost('kode_etik_heading')) ?: $defaults['kode_etik_heading'];
        $kodeEtikSubtitle = trim((string) $this->request->getPost('kode_etik_subtitle'));

        $misiItems = $this->normalizeListInputs($this->request->getPost('misi_items'));
        $kodeEtikItems = $this->normalizeListInputs($this->request->getPost('kode_etik_items'));

        $payload = [
            'hero_title'           => $heroTitle,
            'hero_subtitle'        => $heroSubtitle,
            'hero_image'           => $this->resolveProfileImage('cropped_hero_image', 'hero_image_file', 'existing_hero_image', $defaults['hero_image']),
            'sejarah_heading'      => $sejarahHeading,
            'sejarah_content'      => $sejarahContent,
            'sejarah_image'        => $this->resolveProfileImage('cropped_sejarah_image', 'sejarah_image_file', 'existing_sejarah_image', $defaults['sejarah_image']),
            'visimisi_heading'     => $visimisiHeading,
            'visimisi_bg_image'    => $this->resolveProfileImage('cropped_visimisi_bg_image', 'visimisi_bg_file', 'existing_visimisi_bg_image', $defaults['visimisi_bg_image']),
            'visi_title'           => $visiTitle,
            'visi_text'            => $visiText,
            'misi_title'           => $misiTitle,
            'misi_items_json'      => json_encode($misiItems, JSON_UNESCAPED_UNICODE),
            'kode_etik_heading'    => $kodeEtikHeading,
            'kode_etik_subtitle'   => $kodeEtikSubtitle,
            'kode_etik_items_json' => json_encode($kodeEtikItems, JSON_UNESCAPED_UNICODE),
            'updated_at'           => date('Y-m-d H:i:s'),
        ];

        $existing = $this->profileModel->first();
        if ($existing) {
            $this->profileModel->update((int) $existing['id'], $payload);
        } else {
            $this->profileModel->insert($payload);
        }

        session()->setFlashdata('pesan', 'Konten Profil Organisasi (Sejarah, Visi & Misi, Gambar, dan Kode Etik) pada halaman Tentang Kami berhasil diperbarui.');
        return redirect()->to('/cms/tentang?tab=profil');
    }

    public function saveUnit()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $unitId    = (int) $this->request->getPost('unit_id');
        $slug      = trim((string) $this->request->getPost('slug'));
        $nama      = trim((string) $this->request->getPost('nama'));
        $deskripsi = trim((string) $this->request->getPost('deskripsi'));
        $introText = trim((string) $this->request->getPost('intro_text'));

        if ($unitId <= 0 || $nama === '') {
            session()->setFlashdata('error', 'Judul sub-unit Struktur Organisasi wajib diisi.');
            return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($slug ?: 'dpph'));
        }

        $unit = $this->unitModel->find($unitId);
        if ($unit) {
            $this->unitModel->update($unitId, [
                'nama'       => $nama,
                'deskripsi'  => $deskripsi !== '' ? $deskripsi : null,
                'intro_text' => $introText !== '' ? $introText : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            session()->setFlashdata('pesan', 'Informasi Sub-Unit "' . $nama . '" berhasil diperbarui.');
        }

        return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unit['slug'] ?? $slug ?: 'dpph'));
    }

    public function saveGroup()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $groupId      = (int) $this->request->getPost('group_id');
        $unitSlug     = trim((string) $this->request->getPost('unit_slug')) ?: 'dpph';
        $namaKelompok = trim((string) $this->request->getPost('nama_kelompok'));
        $kodeBadge    = trim((string) $this->request->getPost('kode_badge'));
        $tipeTampilan = trim((string) $this->request->getPost('tipe_tampilan'));
        $sectionLabel = trim((string) $this->request->getPost('section_label'));
        $isFullWidth  = $this->request->getPost('is_full_width') ? 1 : 0;
        $urutan       = max(1, (int) ($this->request->getPost('urutan') ?: 1));
        $isActive     = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;

        if (!in_array($tipeTampilan, ['leader', 'officers', 'bidang', 'badan'], true)) {
            $tipeTampilan = ($unitSlug === 'badan') ? 'badan' : 'officers';
        }

        if ($namaKelompok === '') {
            session()->setFlashdata('error', 'Nama kelompok / bidang / badan wajib diisi.');
            return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
        }

        $payload = [
            'unit_slug'     => $unitSlug,
            'nama_kelompok' => $namaKelompok,
            'kode_badge'    => $kodeBadge !== '' ? $kodeBadge : null,
            'tipe_tampilan' => $tipeTampilan,
            'section_label' => $sectionLabel !== '' ? $sectionLabel : null,
            'is_full_width' => $isFullWidth,
            'urutan'        => $urutan,
            'is_active'     => $isActive ? 1 : 0,
        ];

        if ($groupId > 0 && ($existing = $this->groupModel->find($groupId))) {
            $this->groupModel->update($groupId, $payload);
            session()->setFlashdata('pesan', 'Kelompok / Bidang "' . $namaKelompok . '" berhasil diperbarui.');
            $unitSlug = (string) ($existing['unit_slug'] ?? $unitSlug);
        } else {
            $this->groupModel->insert($payload);
            session()->setFlashdata('pesan', 'Kelompok / Bidang "' . $namaKelompok . '" berhasil ditambahkan.');
        }

        return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
    }

    public function deleteGroup($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $group = $this->groupModel->find((int) $id);
        $unitSlug = 'dpph';
        if ($group) {
            $unitSlug = (string) ($group['unit_slug'] ?? 'dpph');
            $this->memberModel->where('group_id', (int) $id)->delete();
            $this->groupModel->delete((int) $id);
            session()->setFlashdata('pesan', 'Kelompok "' . ($group['nama_kelompok'] ?? '') . '" beserta pengurus di dalamnya berhasil dihapus.');
        }

        return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
    }

    public function addMember()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $unitSlug   = trim((string) $this->request->getPost('unit_slug')) ?: 'dpph';
        $groupId    = (int) $this->request->getPost('group_id');
        $jabatan    = trim((string) $this->request->getPost('jabatan'));
        $nama       = trim((string) $this->request->getPost('nama'));
        $badge      = trim((string) $this->request->getPost('keterangan_badge'));
        $urutan     = (int) $this->request->getPost('urutan');
        $isHigh     = $this->request->getPost('is_highlight') ? 1 : 0;
        $isActive   = $this->request->getPost('is_active') !== null ? (int) $this->request->getPost('is_active') : 1;

        if ($jabatan === '' || $nama === '') {
            session()->setFlashdata('error', 'Teks Jabatan dan Nama Pengurus wajib diisi.');
            return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
        }

        // Ensure valid group exists for this unit; auto-create default group for empty sub-units (e.g. DPPL, Komite, Setprov).
        $validGroup = null;
        if ($groupId > 0) {
            $validGroup = $this->groupModel->where('id', $groupId)->where('unit_slug', $unitSlug)->first();
        }
        if (!$validGroup) {
            $validGroup = $this->groupModel->where('unit_slug', $unitSlug)->orderBy('urutan', 'ASC')->first();
        }
        if (!$validGroup) {
            $unitRow = $this->unitModel->where('slug', $unitSlug)->first();
            $unitLabel = $unitRow['nama'] ?? strtoupper($unitSlug);
            $this->groupModel->insert([
                'unit_slug'     => $unitSlug,
                'nama_kelompok' => 'Daftar Pengurus ' . $unitLabel,
                'kode_badge'    => null,
                'tipe_tampilan' => ($unitSlug === 'badan') ? 'badan' : 'officers',
                'section_label' => null,
                'is_full_width' => 0,
                'urutan'        => 1,
                'is_active'     => 1,
            ]);
            $groupId = (int) $this->groupModel->getInsertID();
        } else {
            $groupId = (int) $validGroup['id'];
        }

        if ($urutan <= 0) {
            $last = $this->memberModel->where('unit_slug', $unitSlug)->orderBy('urutan', 'DESC')->first();
            $urutan = $last ? ((int) $last['urutan'] + 1) : 1;
        }

        $this->memberModel->insert([
            'unit_slug'        => $unitSlug,
            'group_id'         => $groupId,
            'jabatan'          => $jabatan,
            'nama'             => $nama,
            'keterangan_badge' => $badge !== '' ? $badge : null,
            'is_highlight'     => $isHigh,
            'urutan'           => $urutan,
            'is_active'        => $isActive ? 1 : 0,
        ]);

        session()->setFlashdata('pesan', 'Pengurus baru "' . $nama . '" (' . $jabatan . ') berhasil ditambahkan.');
        return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
    }

    public function updateMembersTable()
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $unitSlug = trim((string) $this->request->getPost('unit_slug')) ?: 'dpph';
        $rows     = $this->request->getPost('members');

        if (!is_array($rows) || empty($rows)) {
            session()->setFlashdata('error', 'Tidak ada data baris tabel yang dikirim untuk disimpan.');
            return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
        }

        $unitGroups = $this->groupModel->where('unit_slug', $unitSlug)->findAll();
        $validGroupIds = array_map(static fn ($g) => (int) $g['id'], $unitGroups);

        $updated = 0;
        foreach ($rows as $memberId => $data) {
            $id = (int) $memberId;
            if ($id <= 0 || !is_array($data)) {
                continue;
            }
            $existing = $this->memberModel->find($id);
            if (!$existing || (string) $existing['unit_slug'] !== $unitSlug) {
                continue;
            }

            $jabatan = trim((string) ($data['jabatan'] ?? ''));
            $nama    = trim((string) ($data['nama'] ?? ''));
            if ($jabatan === '' || $nama === '') {
                continue;
            }

            $postedGroupId = (int) ($data['group_id'] ?? $existing['group_id']);
            if (!in_array($postedGroupId, $validGroupIds, true)) {
                $postedGroupId = (int) $existing['group_id'];
            }

            $urutan = max(1, (int) ($data['urutan'] ?? $existing['urutan']));
            $badge  = trim((string) ($data['keterangan_badge'] ?? ''));

            $this->memberModel->update($id, [
                'group_id'         => $postedGroupId > 0 ? $postedGroupId : null,
                'jabatan'          => $jabatan,
                'nama'             => $nama,
                'keterangan_badge' => $badge !== '' ? $badge : null,
                'urutan'           => $urutan,
                'is_highlight'     => !empty($data['is_highlight']) ? 1 : 0,
                'is_active'        => !empty($data['is_active']) ? 1 : 0,
            ]);
            $updated++;
        }

        session()->setFlashdata('pesan', $updated . ' baris data pengurus & jabatan pada unit ' . strtoupper($unitSlug) . ' berhasil diperbarui.');
        return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
    }

    public function deleteMember($id)
    {
        if ($redir = $this->ensureAdmin()) {
            return $redir;
        }

        $member = $this->memberModel->find((int) $id);
        $unitSlug = 'dpph';
        if ($member) {
            $unitSlug = (string) ($member['unit_slug'] ?? 'dpph');
            $this->memberModel->delete((int) $id);
            session()->setFlashdata('pesan', 'Pengurus "' . ($member['nama'] ?? '') . '" berhasil dihapus dari tabel.');
        }

        return redirect()->to('/cms/tentang?tab=struktur&unit=' . urlencode($unitSlug));
    }
}

