# SIM-INKINDO DKI

**SIM-INKINDO DKI (Sistem Informasi & Manajemen INKINDO DKI)** adalah aplikasi web berbasis **CodeIgniter 4** yang berfungsi sebagai portal manajemen data anggota, portal kemitraan, dan Admin CMS terpadu untuk **INKINDO DKI Jakarta**.

> **Catatan Penamaan:** Aplikasi ini sebelumnya bernama *Sistem Manajemen Data PT* dan dikembangkan lebih lanjut menjadi **SIM-INKINDO DKI** untuk mencerminkan cakupannya yang menyeluruh (Manajemen PT & SBU, Portal Mitra Kerja, serta Admin CMS Web Profile).

## Fitur & Modul Utama

- **Dashboard (`Dashboard`)**: Ringkasan data perusahaan anggota dan kategori SBU.
- **Manajemen Data Perusahaan & SBU (`Perusahaan`, `Sbu`)**: Pengelolaan profil perusahaan anggota PT, data SBU, klasifikasi/subklasifikasi, dan daftar proyek.
- **Portal & Manajemen Mitra Kerja (`Mitra`)**: Pendaftaran/login mitra, pengelolaan profil perusahaan mitra, logo, pengajuan & perpanjangan masa kemitraan, katalog produk, serta pesan masuk mitra.
- **Admin CMS Web Profile (`Cms`)**: Pengelolaan konten **Berita & Informasi** serta pengaturan **Halaman Utama** Web Profile INKINDO DKI Jakarta beserta sinkronisasi aset ke Web Profile.
- **Pencarian Khusus (`Pencarian`)**: Fitur pencarian data internal terfilter.
- **Kelola Pengguna & Autentikasi (`Users`, `Auth`)**: Manajemen akun Admin, Anggota (PT), dan Mitra Kerja.

## Stack Teknologi

- **Framework**: CodeIgniter 4 (PHP)
- **Database**: MySQL (`inkindo_db`, driver `MySQLi`)
- **UI**: Bootstrap 5, Bootstrap Icons, DataTables

## Menjalankan Secara Lokal

1. Pastikan konfigurasi database pada `.env` sudah sesuai (`inkindo_db`).
2. Jalankan server pengembangan CodeIgniter 4:

```bash
php spark serve
```

Secara default aplikasi berjalan di `http://localhost:8080/` dan terhubung dengan Web Profile INKINDO DKI (`http://localhost:8000`).
