# SIMRS Khanza — Web Desktop

Aplikasi **SIMRS Khanza** berbasis web dengan antarmuka desktop (windowed UI). Dibangun di atas database SIMRS Khanza yang sudah ada, menyediakan modul-modul klinis, farmasi, dan kasir dalam bentuk Single Page Application dengan tampilan seperti sistem operasi desktop.

> **Fokus**: Sistem informasi rumah sakit — pendaftaran, IGD, rawat jalan, rawat inap, kasir, apotek/farmasi, bridging BPJS.

---

## Teknologi

| Bagian | Teknologi |
|--------|-----------|
| **Backend** | Laravel 11, PHP 8.2 |
| **Frontend** | Alpine.js 3, Tailwind CSS 3 |
| **Auth** | Laravel Sanctum (token-based) |
| **Database** | MySQL (SIMRS Khanza existing schema) + SQLite (Laravel internal) |
| **Build** | Vite 6 |
| **PWA** | Service Worker + Manifest |
| **Dependensi JS** | Axios, Alpine.js |
| **Dependensi PHP** | Laravel, Sanctum, Tinker |

---

## Struktur Folder

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # 13 API controllers
│   │   │   │   ├── ApotekController.php
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── IcdController.php
│   │   │   │   ├── IgdController.php
│   │   │   │   ├── JadwalController.php
│   │   │   │   ├── KasirController.php
│   │   │   │   ├── PasienController.php
│   │   │   │   ├── RalanController.php
│   │   │   │   ├── RanapController.php
│   │   │   │   ├── RegistrasiController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   └── TindakanController.php
│   │   │   └── DesktopController.php   # Serves SPA views
│   │   └── ─
│   └── Models/                   # 12 Eloquent models
│
├── resources/
│   ├── css/app.css               # Tailwind + custom CSS + theme variables
│   ├── js/
│   │   ├── app.js                # Alpine store & component definitions
│   │   ├── bootstrap.js          # Axios setup
│   │   └── desktop/
│   │       ├── api.js            # API client (fetch + cache)
│   │       └── window-manager.js # Window management (open/close/drag/resize)
│   └── views/
│       ├── auth/login.blade.php
│       ├── layouts/desktop.blade.php
│       ├── desktop/
│       │   ├── index.blade.php
│       │   ├── start-menu.blade.php
│       │   ├── taskbar.blade.php
│       │   ├── window-container.blade.php
│       │   └── modules/          # 23 module views
│       └── welcome.blade.php
│
├── routes/
│   ├── api.php                   # ±85 endpoint definitions
│   ├── web.php                   # Login & SPA routes
│   └── console.php
│
├── public/
│   ├── build/assets/             # Compiled Vite output
│   ├── icons/                    # PWA icons
│   └── sw.js                     # Service Worker
│
├── config/                       # 11 Laravel config files
├── database/
│   ├── migrations/               # Schema migrations
│   └── seeders/                  # Seeders (users, master data)
└── tests/
```

---

## Modul Saat Ini

| Modul | File View | Fitur |
|-------|-----------|-------|
| **Registrasi** | `registrasi/index` | Pendaftaran pasien baru, cari pasien, pilih poli/dokter |
| **IGD** | `igd/index`, `igd/tindakan` | Daftar pasien IGD, triage, tindakan, diagnosis, edit pasien |
| **Ralan** | `ralan/queue` | Antrian rawat jalan, pemeriksaan, resep |
| **Ranap** | `ranap/index`, `ranap/admission` | Daftar rawat inap, admit, pindah kamar, pulangkan, riwayat kamar, ubah waktu |
| **Kasir** | `kasir/index`, `kasir/nota` | Tagihan rawat jalan, rawat inap, kamar, laporan |
| **Apotek** | `apotek/index` | Master data: industri farmasi, jenis/kategori/golongan obat, data barang, kode satuan |
| **Pasien** | `pasien/index`, `pasien/detail`, `pasien/riwayat` | Cari, tambah, edit pasien, detail & riwayat kunjungan |
| **Jadwal** | `jadwal/index` | Jadwal praktik dokter, master poli, master penjab (jenis bayar) |
| **Data Tindakan** | `data-tindakan/index` | Master tarif tindakan per jenis |
| **ICD** | `icd/index` | Pencarian ICD-10 dan ICD-9 |
| **Bridging BPJS** | `bridging/sep`, `bridging/surat-kontrol`, `bridging/prb` | SEP, Surat Kontrol, PRB |
| **Berkas Perawatan** | `berkas-perawatan/index` | Berkas perawatan pasien |
| **Settings** | `settings/index` | Identitas rumah sakit, depo, industri farmasi |
| **Chatbot** | `chatbot/index` | Asisten AI |
| **Developers** | `developers/index` | Informasi teknis & debug |

---

## Instalasi & Setup

### Prasyarat
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL (database SIMRS Khanza yang sudah ada)

### Langkah

```bash
# 1. Clone & masuk direktori
git clone <url-repo> khanza-web
cd khanza-web

# 2. Install dependencies
composer install
npm install

# 3. Copy environment & atur koneksi database
cp .env.example .env
# Edit .env: atur DB connection ke database SIMRS Khanza

# 4. Generate key & storage link
php artisan key:generate
php artisan storage:link

# 5. Jalankan migrasi (untuk tabel internal Laravel)
php artisan migrate

# 6. Build frontend
npm run build

# 7. Jalankan server
php artisan serve
```

### Database SIMRS Khanza

Aplikasi ini terhubung ke database **SIMRS Khanza** yang sudah ada (`sik`). Konfigurasi koneksi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sik
DB_USERNAME=root
DB_PASSWORD=
```

### Login

Sistem login menggunakan akun **SIMRS Khanza** yang sudah ada (tabel `user` / `admin`), diverifikasi dengan `AES_DECRYPT`.

---

## API Endpoint

Semua endpoint API berada di prefix `/api/` dan diamankan dengan **Laravel Sanctum** (Bearer token).

### Publik
| Method | Path | Keterangan |
|--------|------|------------|
| POST | `/api/login` | Login |

### Terautentikasi
| Grup | Endpoints |
|------|-----------|
| **Dashboard** | `GET /dashboard/stats`, `GET /pasien/search-location` |
| **Pasien** | CRUD `/pasien`, search, dokter-list, search-regperiksa |
| **Registrasi** | today-list, store, dokter-by-poli, petugas-list |
| **IGD** | list, dashboard, register, status, update, tindakan, triage, diagnosis |
| **Ralan** | list, poli-list, dokter-list, dashboard, queue, register, examination, resep |
| **Ranap** | list, dashboard, admit, pindah-kamar, pulangkan, destroy, ubah-waktu, kamar-list, riwayat-kamar |
| **Kasir** | rajal, ranap, kamar, laporan |
| **Tindakan** | jns-perawatan, petugas-list, penanganan (IGD/Ranap), riwayat, SOAP |
| **Apotek** | industri, jenis, kategori, golongan, kodesatuan, databarang |
| **ICD** | search |
| **Jadwal** | praktek, poli CRUD, penjab CRUD |
| **Settings** | identitas (get/update), depo-list, industri-farmasi |
| **Data Tindakan** | list/{jenis} |

---

## Pengembangan

```bash
# Jalankan dev server dengan hot-reload
npm run dev

# Build untuk production
npm run build
```

---

## Lisensi

MIT
