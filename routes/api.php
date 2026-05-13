<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\RegistrasiController;
use App\Http\Controllers\Api\IgdController;
use App\Http\Controllers\Api\RalanController;
use App\Http\Controllers\Api\RanapController;
use App\Http\Controllers\Api\TindakanController;
use App\Http\Controllers\Api\IcdController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\KasirController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ApotekController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/pasien/search-location', [DashboardController::class, 'searchPatientLocation']);
    Route::get('/pasien/search', [PasienController::class, 'search']);
    Route::post('/pasien/store', [PasienController::class, 'storePasien']);
    Route::put('/pasien/update/{no_rkm_medis}', [PasienController::class, 'updatePasien']);
    Route::delete('/pasien/delete/{no_rkm_medis}', [PasienController::class, 'destroyPasien']);
    Route::get('/pasien/dokter-list', [PasienController::class, 'dokterList']);
    Route::get('/pasien/search-regperiksa', [PasienController::class, 'searchRegPeriksa']);
    Route::apiResource('pasien', PasienController::class);

    Route::get('/registrasi/today', [RegistrasiController::class, 'todayList']);
    Route::post('/registrasi/store', [RegistrasiController::class, 'storeRegPeriksa']);
    Route::get('/registrasi/dokter-by-poli', [RegistrasiController::class, 'dokterByPoli']);
    Route::get('/registrasi/petugas', [RegistrasiController::class, 'petugasList']);

    Route::get('/igd/list', [IgdController::class, 'index']);
    Route::get('/igd/dashboard', [IgdController::class, 'dashboard']);
    Route::post('/igd/register', [IgdController::class, 'register']);
    Route::put('/igd/status', [IgdController::class, 'updateStatus']);
    Route::put('/igd/update', [IgdController::class, 'update']);
    Route::post('/igd/delete', [IgdController::class, 'destroy']);
    Route::post('/igd/{registrasi}/triage', [IgdController::class, 'triage']);
    Route::post('/igd/{registrasi}/tindakan', [IgdController::class, 'tindakan']);
    Route::post('/igd/{registrasi}/diagnosis', [IgdController::class, 'diagnosis']);

    Route::get('/ralan/list', [RalanController::class, 'list']);
    Route::get('/ralan/poli-list', [RalanController::class, 'poliList']);
    Route::get('/ralan/dokter-list', [RalanController::class, 'dokterList']);
    Route::get('/ralan/dashboard', [RalanController::class, 'dashboard']);
    Route::get('/ralan/queue', [RalanController::class, 'queue']);
    Route::post('/ralan/register', [RalanController::class, 'register']);
    Route::put('/ralan/status', [RalanController::class, 'updateStatus']);
    Route::put('/ralan/update', [RalanController::class, 'update']);
    Route::post('/ralan/delete', [RalanController::class, 'destroy']);
    Route::post('/ralan/{registrasi}/start', [RalanController::class, 'startExamination']);
    Route::put('/ralan/{kunjungan}/examination', [RalanController::class, 'updateExamination']);
    Route::post('/ralan/{kunjungan}/resep', [RalanController::class, 'addResep']);

    Route::get('/kasir/rajal', [KasirController::class, 'rajal']);
    Route::get('/kasir/ranap', [KasirController::class, 'ranap']);
    Route::get('/kasir/kamar', [KasirController::class, 'kamar']);
    Route::get('/kasir/laporan', [KasirController::class, 'laporan']);

    // Tindakan / Penanganan
    Route::get('/tindakan/jns-perawatan', [TindakanController::class, 'jnsPerawatan']);
    Route::get('/tindakan/petugas-list', [TindakanController::class, 'petugasList']);
    Route::get('/igd/{no_rawat}/penanganan', [TindakanController::class, 'getPenangananIgd']);
    Route::post('/igd/{no_rawat}/penanganan', [TindakanController::class, 'savePenangananIgd']);
    Route::delete('/igd/{no_rawat}/penanganan', [TindakanController::class, 'deletePenangananIgd']);
    Route::get('/ranap/{no_rawat}/penanganan', [TindakanController::class, 'getPenangananRanap']);
    Route::post('/ranap/{no_rawat}/penanganan', [TindakanController::class, 'savePenangananRanap']);
    Route::delete('/ranap/{no_rawat}/penanganan', [TindakanController::class, 'deletePenangananRanap']);

    // Riwayat Pasien
    Route::get('/tindakan/riwayat-kunjungan/{no_rkm_medis}', [TindakanController::class, 'riwayatKunjungan']);
    Route::get('/tindakan/riwayat-soap/{no_rkm_medis}', [TindakanController::class, 'riwayatSoap']);
    Route::get('/ranap/riwayat-soap/{no_rkm_medis}', [TindakanController::class, 'riwayatSoapRanap']);

    // SOAP Pemeriksaan
    Route::get('/tindakan/soap-list/{no_rawat}', [TindakanController::class, 'getSoapList']);
    Route::post('/tindakan/soap/{no_rawat}', [TindakanController::class, 'saveSoap']);
    Route::get('/tindakan/soap-grafik/{no_rkm_medis}', [TindakanController::class, 'soapGrafik']);
    Route::get('/ranap/soap-list/{no_rawat}', [TindakanController::class, 'getSoapListRanap']);
    Route::post('/ranap/soap/{no_rawat}', [TindakanController::class, 'saveSoapRanap']);
    Route::get('/ranap/soap-grafik/{no_rkm_medis}', [TindakanController::class, 'soapGrafikRanap']);

    // ICD 10 & ICD 9
    Route::get('/icd/search', [IcdController::class, 'search']);

    // Jadwal
    Route::get('/jadwal/praktek', [JadwalController::class, 'praktek']);
    Route::get('/jadwal/poli', [JadwalController::class, 'poliList']);
    Route::post('/jadwal/poli', [JadwalController::class, 'poliStore']);
    Route::put('/jadwal/poli/{kd_poli}', [JadwalController::class, 'poliUpdate']);
    Route::delete('/jadwal/poli/{kd_poli}', [JadwalController::class, 'poliDestroy']);

    // Penjab / Jenis Bayar
    Route::get('/jadwal/penjab', [JadwalController::class, 'penjabList']);
    Route::post('/jadwal/penjab', [JadwalController::class, 'penjabStore']);
    Route::put('/jadwal/penjab/{kd_pj}', [JadwalController::class, 'penjabUpdate']);
    Route::delete('/jadwal/penjab/{kd_pj}', [JadwalController::class, 'penjabDestroy']);

    Route::get('/identitas', [SettingsController::class, 'identitas']);
    Route::put('/identitas', [SettingsController::class, 'updateIdentitas']);
    Route::get('/settings/depo-list', [SettingsController::class, 'depoList']);
    Route::get('/settings/industri-farmasi', [SettingsController::class, 'industriFarmasi']);

    // Apotek / Farmasi
    Route::get('/apotek/industri', [ApotekController::class, 'industri']);
    Route::get('/apotek/jenis', [ApotekController::class, 'jenis']);
    Route::get('/apotek/kategori', [ApotekController::class, 'kategori']);
    Route::get('/apotek/golongan', [ApotekController::class, 'golongan']);
    Route::get('/apotek/kodesatuan', [ApotekController::class, 'kodesatuan']);
    Route::get('/apotek/databarang', [ApotekController::class, 'databarang']);

    // Data Tindakan (Master Tarif)
    Route::get('/data-tindakan/list/{jenis}', [TindakanController::class, 'dataList']);

    Route::get('/ranap/dashboard', [RanapController::class, 'dashboard']);
    Route::get('/ranap/list', [RanapController::class, 'list']);
    Route::get('/ranap/kamar-list', [RanapController::class, 'kamarList']);
    Route::get('/ranap/riwayat-kamar', [RanapController::class, 'riwayatKamar']);
    Route::post('/ranap/admit', [RanapController::class, 'admit']);
    Route::put('/ranap/pindah-kamar', [RanapController::class, 'pindahKamar']);
    Route::put('/ranap/pulangkan', [RanapController::class, 'pulangkan']);
    Route::delete('/ranap/{no_rawat}', [RanapController::class, 'destroy']);
    Route::put('/ranap/ubah-waktu-masuk', [RanapController::class, 'ubahWaktuMasuk']);
    Route::put('/ranap/ubah-waktu-keluar', [RanapController::class, 'ubahWaktuKeluar']);
});
