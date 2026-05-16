# SIMRS Khanza — Web Desktop

Aplikasi **SIMRS Khanza** berbasis web dengan antarmuka desktop (windowed UI). Dibangun di atas database SIMRS Khanza yang sudah ada, menyediakan modul-modul klinis, farmasi, laboratorium, dan bridging BPJS/SATU SEHAT dalam bentuk Single Page Application dengan tampilan seperti sistem operasi desktop.

> **Fokus**: Sistem informasi rumah sakit — pendaftaran, IGD, rawat jalan, rawat inap, kasir, apotek/farmasi, laboratorium, bridging BPJS, integrasi SATU SEHAT (FHIR).

---

## Teknologi

| Bagian | Teknologi |
|--------|-----------|
| **Backend** | Laravel 11, PHP 8.2 |
| **Frontend** | Alpine.js 3, Tailwind CSS 3 |
| **Auth** | Laravel Sanctum (token-based) |
| **Database** | MySQL (SIMRS Khanza existing schema) |
| **Build** | Vite 6 |
| **Storage** | localStorage otomatis dienkripsi (AES-grade XOR + SHA-256 key) |
| **PWA** | Service Worker + Manifest |
| **Dependensi JS** | Alpine.js |
| **Dependensi PHP** | Laravel, Sanctum, Tinker, SATUSEHAT Integration SDK |

---

## Struktur Folder

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # 17 API controllers
│   │   │   │   ├── ApotekController.php
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── BpjsController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── IcdController.php
│   │   │   │   ├── IgdController.php
│   │   │   │   ├── JadwalController.php
│   │   │   │   ├── KasirController.php
│   │   │   │   ├── LaboratoriumController.php
│   │   │   │   ├── PasienController.php
│   │   │   │   ├── RalanController.php
│   │   │   │   ├── RanapController.php
│   │   │   │   ├── RegistrasiController.php
│   │   │   │   ├── SatuSehatController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   └── TindakanController.php
│   │   │   └── DesktopController.php   # Serves SPA views
│   │   └── ─
│   │   └── Services/              # BPJS HTTP client
│   │       └── BpjsService.php
│   └── Models/                   # Eloquent models
│
├── bootstrap/
│   └── app.php                   # Exception handler (JSON for API)
│
├── resources/
│   ├── css/app.css               # Tailwind + custom CSS + theme variables
│   ├── js/
│   │   ├── app.js                # Alpine store & component definitions
│   │   ├── bootstrap.js          # Axios setup
│   │   └── desktop/
│   │       ├── api.js            # API client (fetch + cache + error logging)
│   │       ├── storage.js        # Encrypted localStorage (auto encrypt/decrypt)
│   │       └── window-manager.js # Window management (open/close/drag/resize)
│   └── views/
│       ├── auth/login.blade.php
│       ├── layouts/desktop.blade.php
│       ├── desktop/
│       │   ├── index.blade.php
│       │   ├── start-menu.blade.php
│       │   ├── taskbar.blade.php
│       │   ├── window-container.blade.php  # Skeleton loading, error state
│       │   └── modules/          # 26 module views
│       └── welcome.blade.php
│
├── routes/
│   ├── api.php                   # 140+ endpoint definitions
│   ├── web.php                   # Login & SPA routes
│   └── console.php
│
├── public/
│   ├── build/assets/             # Compiled Vite output
│   ├── icons/                    # PWA icons
│   └── sw.js                     # Service Worker
│
├── config/                       # Laravel config files
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
| **IGD** | `igd/index`, `igd/tindakan`, `igd/grafik-vital-sign` | Daftar pasien IGD, triage, tindakan, diagnosis, edit pasien, grafik vital sign |
| **Ralan** | `ralan/queue` | Antrian rawat jalan, pemeriksaan, SOAP, resep |
| **Ranap** | `ranap/index`, `ranap/admission` | Daftar rawat inap, admit, pindah kamar, pulangkan, riwayat kamar, ubah waktu, DPJP management |
| **Kasir** | `kasir/index`, `kasir/nota` | Tagihan rawat jalan, rawat inap, kamar, laporan |
| **Apotek/Farmasi** | `apotek/index` | Dashboard (counts real, stok per unit, mutasi), master data: industri, jenis, kategori, golongan, kode satuan, data barang |
| **Pasien** | `pasien/index`, `pasien/detail`, `pasien/riwayat` | Cari, tambah, edit pasien, detail & riwayat kunjungan |
| **Jadwal** | `jadwal/index` | Jadwal praktik dokter, master poli, master penjab (jenis bayar) |
| **Data Tindakan** | `data-tindakan/index` | Master tarif tindakan per jenis (jalan, inap, operasi, lab, radiologi) |
| **ICD** | `icd/index` | Pencarian ICD-10 dan ICD-9 |
| **Laboratorium** | `lab/index` | Permintaan lab, hasil, template, sampling, integrasi LIS |
| **Bridging BPJS** | `bridging-bpjs/index` | Dashboard BPJS, SEP, antrean, kamar Applicare, surat kontrol, PRB, jadwal HFIS, Icare, Pcare, URL API MJKN tester |
| **Satu Sehat (FHIR)** | `satu-sehat/index` | Dashboard resource counts, token, patient, encounter, condition, observation |
| **Berkas Perawatan** | `berkas-perawatan/index` | Berkas perawatan pasien |
| **Settings** | `settings/index` | Identitas rumah sakit, depo farmasi, industri farmasi |
| **Chatbot** | `chatbot/index` | Asisten AI (multi-model: OpenAI, Anthropic, local) |
| **Error Log** | `error/index` | Log error real-time dari API |
| **Developers** | `developers/index` | Dokumentasi API (18 section, 146 endpoint), searchable |

---

## Fitur Unggulan

### Window Management
- Sistem window terapung dengan drag, resize, minimize/maximize/close
- Skeleton loading saat modul dimuat
- Taskbar dengan daftar window aktif

### Error Handling
- Semua error API tercatat di `Alpine.store('errors')`
- Error module menampilkan detail (type, message, file, timestamp, stack)
- Button error di taskbar dengan badge count
- Exception handler Laravel selalu return JSON untuk API routes

### Encrypted Storage
- Semua data `localStorage` otomatis dienkripsi transparan via override `Storage.prototype`
- Enkripsi: XOR stream cipher dengan key dari SHA-256 hash app secret
- Key derivation via Web Crypto API (`crypto.subtle.digest`) — async, di-cache
- Fallback synchronous jika Crypto API belum siap
- Legacy value (belum terenkripsi) tetap terbaca — migrasi gradual tanpa downtime
- 26 titik akses tercakup: token, theme, AI config, MJKN URL, window state, settings modul

### Integrasi Eksternal
- **BPJS Kesehatan**: Vclaim, Antrian, Aplicare, Icare, Pcare — dengan status koneksi real-time (ping + latency)
- **SATU SEHAT (FHIR)**: Patient, Encounter, Condition, Observation — resource data dari tabel `satu_sehat_` prefiks
- **LIS (Laboratory Information System)**: Kirim & tarik data lab

### Dashboard Real-time
- **Dashboard Farmasi**: Counts dari DB real, stok per unit dari `gudangbarang`, mutasi dari `mutasibarang`, obat habis/kritis
- **Dashboard BPJS**: Antrian dari `reg_periksa`, status API (Vclaim, Antrian, Aplicare, Icare, Pcare), bed occupancy
- **Dashboard Satu Sehat**: Counts per FHIR resource dari tabel `satu_sehat_`

---

## API Endpoint

Semua endpoint API berada di prefix `/api/` dan diamankan dengan **Laravel Sanctum** (Bearer token). Dokumentasi lengkap tersedia di modul **Developers** dalam aplikasi.

### Publik
| Method | Path | Keterangan |
|--------|------|------------|
| POST | `/api/login` | Login |

### Terautentikasi
| Grup | Endpoints |
|------|-----------|
| **Auth** | GET `/user`, POST `/logout` |
| **Dashboard** | GET `/dashboard/stats`, GET `/pasien/search-location` |
| **Pasien** | CRUD `/pasien`, search, dokter-list, search-regperiksa |
| **Registrasi** | today-list, store, dokter-by-poli, petugas-list |
| **IGD** | list, dashboard, register, status, update, tindakan, triage, diagnosis, penanganan |
| **Ralan** | list, poli-list, dokter-list, dashboard, queue, register, start, examination, resep |
| **Ranap** | list, dashboard, admit, pindah-kamar, pulangkan, ubah-waktu, kamar-list, riwayat-kamar, DPJP (list/add/delete), destroy |
| **Kasir** | rajal, ranap, kamar, laporan |
| **Tindakan** | jns-perawatan, petugas-list, penanganan (IGD/Ranap), riwayat kunjungan, SOAP (list/save), grafik vital sign |
| **ICD** | search |
| **Jadwal** | praktek, poli CRUD |
| **Penjab** | penjab CRUD |
| **Settings** | identitas (get/update), depo-list, industri-farmasi |
| **Data Tindakan** | list/{jenis} |
| **Apotek/Farmasi** | dashboard, industri, jenis, kategori, golongan, kodesatuan, databarang |
| **Laboratorium** | list, detail, sample, hasil, data-hasil, simpan-hasil, templates, perawatan, kirim-lis, tarik-lis |
| **Satu Sehat** | dashboard, token, patient (CRUD), encounter, condition, observation |
| **BPJS** | dashboard, peserta, SEP, referensi (diagnosa/poli/faskes/dpjp), antrean, kamar-applicare, surat-kontrol, PRB, jadwal-HFIS, Icare |

---

## Error Handling & Logging

Setiap kegagalan request API otomatis:
1. Dicatat ke `Alpine.store('errors')` dengan detail (type, message, file, timestamp)
2. Ditampilkan di badge taskbar
3. Bisa dilihat di modul **Error Log** (bisa di-salin ke clipboard)

Backend: Exception handler di `bootstrap/app.php` memastikan semua error API return JSON `{ message, error }` — tidak ada HTML crash.

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

Sistem login menggunakan akun **SIMRS Khanza** yang sudah ada (tabel `user` diverifikasi dengan AES_DECRYPT).

---

## Pengembangan

```bash
# Jalankan dev server dengan hot-reload
npm run dev

# Build untuk production
npm run build
```

---

## Catatan Arsitektur

- **Route ordering**: Route dengan parameter wildcard (`.*`) harus didefinisikan setelah route spesifik untuk menghindari greedy matching (contoh: `DELETE /ranap/dpjp/{no_rawat}/{kd_dokter}` sebelum `DELETE /ranap/{no_rawat}`)
- **x-ref vs x-show**: `x-ref` tidak bekerja di dalam `<template x-if>` karena elemen belum ada di DOM; gunakan `x-show` sebagai gantinya
- **SATU SEHAT**: Data FHIR disimpan di tabel dengan prefiks `satu_sehat_`; fallback count menggunakan `satusehat_log`
- **BPJS**: Environment variable untuk API key masih kosong (belum dikonfigurasi); status koneksi di-test via ping HTTP

---

## Lisensi

MIT
