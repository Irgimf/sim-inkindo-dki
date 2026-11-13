<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::login');

// Auth Routes (Anggota)
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/processLogin', 'Auth::processLogin');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/processRegister', 'Auth::processRegister');
$routes->get('/auth/logout', 'Auth::logout');

// Auth Routes (Mitra Kerja)
$routes->get('/mitra/login', 'Auth::mitraLogin');
$routes->post('/mitra/processLogin', 'Auth::processMitraLogin');
$routes->get('/mitra/register', 'Auth::mitraRegister');
$routes->post('/mitra/processRegister', 'Auth::processMitraRegister');

// Dashboard Route
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Perusahaan Routes
$routes->group('perusahaan', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Perusahaan::index');
    $routes->get('create', 'Perusahaan::create');
    $routes->post('store', 'Perusahaan::store');
    $routes->get('edit/(:num)', 'Perusahaan::edit/$1');
    $routes->post('update/(:num)', 'Perusahaan::update/$1');
    $routes->get('delete/(:num)', 'Perusahaan::delete/$1');
});

// SBU Routes
$routes->group('sbu', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Sbu::index');
    $routes->get('detail/(:num)', 'Sbu::detail/$1');
    $routes->get('create', 'Sbu::create');
    $routes->post('store', 'Sbu::store');
    $routes->get('edit/(:num)', 'Sbu::edit/$1');
    $routes->post('update/(:num)', 'Sbu::update/$1');
    $routes->get('delete/(:num)', 'Sbu::delete/$1');

    // Projects (Pekerjaan)
    $routes->get('projects/(:num)', 'Sbu::projects/$1');
    $routes->post('storeProject/(:num)', 'Sbu::storeProject/$1');
    $routes->post('updateProject/(:num)', 'Sbu::updateProject/$1');
    $routes->get('deleteProject/(:num)', 'Sbu::deleteProject/$1');
});

// Mitra Kerja Management Routes
$routes->group('mitra', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Mitra::index');
    $routes->get('create', 'Mitra::create');
    $routes->post('store', 'Mitra::store');
    $routes->get('edit/(:num)', 'Mitra::edit/$1');
    $routes->post('update/(:num)', 'Mitra::update/$1');
    $routes->get('delete/(:num)', 'Mitra::delete/$1');

    // Persetujuan Pengajuan Mitra Kerja (Admin)
    $routes->post('approve/(:num)', 'Mitra::approve/$1');
    $routes->post('reject/(:num)', 'Mitra::reject/$1');

    // Detail Produk Mitra Kerja
    $routes->get('products/(:num)', 'Mitra::products/$1');
    $routes->post('storeProduct/(:num)', 'Mitra::storeProduct/$1');
    $routes->post('updateProduct/(:num)', 'Mitra::updateProduct/$1');
    $routes->get('deleteProduct/(:num)', 'Mitra::deleteProduct/$1');

    // Pesan Masuk Mitra Kerja
    $routes->get('messages', 'Mitra::messages');
    $routes->post('markMessageRead/(:num)', 'Mitra::markMessageRead/$1');
    $routes->get('deleteMessage/(:num)', 'Mitra::deleteMessage/$1');
});

// Admin CMS Berita & Informasi & Halaman Utama Routes
$routes->group('cms', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Cms::index');
    $routes->get('homepage', 'Cms::homepage');
    $routes->post('updateHomepageSlots', 'Cms::updateHomepageSlots');
    $routes->post('updatePublikasiSlots', 'Cms::updatePublikasiSlots');
    $routes->post('banner/save', 'Cms::saveHeroBanner');
    $routes->get('banner/delete/(:num)', 'Cms::deleteHeroBanner/$1');
    $routes->get('banner/toggle/(:num)', 'Cms::toggleHeroBanner/$1');
    $routes->post('phrase/save', 'Cms::saveHeroPhrase');
    $routes->get('phrase/delete/(:num)', 'Cms::deleteHeroPhrase/$1');
    $routes->get('phrase/toggle/(:num)', 'Cms::toggleHeroPhrase/$1');
    $routes->post('background/save', 'Cms::saveHeroBackground');
    $routes->get('background/delete/(:num)', 'Cms::deleteHeroBackground/$1');
    $routes->get('background/toggle/(:num)', 'Cms::toggleHeroBackground/$1');
    $routes->get('webAsset/(:any)', 'Cms::webAsset/$1');
    $routes->get('tentang', 'CmsTentang::index');
    $routes->post('tentang/profile/save', 'CmsTentang::saveProfile');
    $routes->post('tentang/unit/save', 'CmsTentang::saveUnit');
    $routes->post('tentang/group/save', 'CmsTentang::saveGroup');
    $routes->get('tentang/group/delete/(:num)', 'CmsTentang::deleteGroup/$1');
    $routes->post('tentang/member/add', 'CmsTentang::addMember');
    $routes->post('tentang/member/updateTable', 'CmsTentang::updateMembersTable');
    $routes->get('tentang/member/delete/(:num)', 'CmsTentang::deleteMember/$1');
    $routes->get('regulasi', 'CmsRegulasi::index');
    $routes->post('regulasi/save', 'CmsRegulasi::save');
    $routes->get('regulasi/delete/(:num)', 'CmsRegulasi::delete/$1');
    $routes->get('lelang', 'CmsLelang::index');
    $routes->post('lelang/portal/save', 'CmsLelang::savePortal');
    $routes->get('lelang/portal/delete/(:num)', 'CmsLelang::deletePortal/$1');
    $routes->post('lelang/peluang/save', 'CmsLelang::savePeluang');
    $routes->get('ketentuan-mitra', 'CmsKetentuanMitra::index');
    $routes->post('ketentuan-mitra/page/save', 'CmsKetentuanMitra::savePage');
    $routes->post('ketentuan-mitra/item/save', 'CmsKetentuanMitra::saveItem');
    $routes->get('ketentuan-mitra/item/delete/(:num)', 'CmsKetentuanMitra::deleteItem/$1');
    $routes->get('backgrounds', 'CmsBackground::index');
    $routes->post('backgrounds/save', 'CmsBackground::save');
    $routes->get('backgrounds/reset/(:num)', 'CmsBackground::reset/$1');
    $routes->get('footer-contact', 'CmsFooterContact::index');
    $routes->post('footer-contact/save', 'CmsFooterContact::save');
    $routes->get('create', 'Cms::create');
    $routes->post('store', 'Cms::store');
    $routes->get('edit/(:num)', 'Cms::edit/$1');
    $routes->post('update/(:num)', 'Cms::update/$1');
    $routes->get('delete/(:num)', 'Cms::delete/$1');
});

// Users Routes (Admin only)
$routes->group('users', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('delete/(:num)', 'Users::delete/$1');
});

// Pencarian Routes
$routes->group('pencarian', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Pencarian::index');
    $routes->get('exportExcel', 'Pencarian::exportExcel');
});
