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
use App\Http\Controllers\Api\LaboratoriumController;
use App\Http\Controllers\Api\SatuSehatController;
use App\Http\Controllers\Api\BpjsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::pattern('no_rawat', '.*');
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
    Route::get('/tindakan/soap-grafik-rawat/{no_rawat}', [TindakanController::class, 'soapGrafikByRawat']);
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
    Route::get('/apotek/dashboard', [ApotekController::class, 'dashboard']);
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
    Route::put('/ranap/ubah-waktu-masuk', [RanapController::class, 'ubahWaktuMasuk']);
    Route::put('/ranap/ubah-waktu-keluar', [RanapController::class, 'ubahWaktuKeluar']);
    Route::get('/ranap/dpjp-doctors', [RanapController::class, 'dpjpDoctors']);
    Route::get('/ranap/dpjp/{no_rawat}', [RanapController::class, 'dpjpList']);
    Route::post('/ranap/dpjp', [RanapController::class, 'dpjpAdd']);
    Route::delete('/ranap/dpjp/{no_rawat}/{kd_dokter}', [RanapController::class, 'dpjpDelete']);
    Route::delete('/ranap/{no_rawat}', [RanapController::class, 'destroy']);

    // Laboratorium
    Route::get('/lab/list', [LaboratoriumController::class, 'index']);
    Route::get('/lab/detail/{noorder}', [LaboratoriumController::class, 'detail']);
    Route::put('/lab/sample', [LaboratoriumController::class, 'sample']);
    Route::post('/lab/kirim-lis', [LaboratoriumController::class, 'kirimLis']);
    Route::post('/lab/tarik-lis', [LaboratoriumController::class, 'tarikLis']);
    Route::put('/lab/hasil', [LaboratoriumController::class, 'updateHasil']);
    Route::get('/lab/data-hasil/{noorder}', [LaboratoriumController::class, 'dataHasil']);
    Route::post('/lab/simpan-hasil', [LaboratoriumController::class, 'simpanHasil']);
    Route::get('/lab/templates', [LaboratoriumController::class, 'templates']);
    Route::get('/lab/perawatan/{kategori}', [LaboratoriumController::class, 'perawatan']);

    // SATUSEHAT Integration
    Route::prefix('satusehat')->group(function () {
        Route::get('/dashboard', [SatuSehatController::class, 'dashboard']);
        Route::get('/token', [SatuSehatController::class, 'token']);
        Route::get('/{resource}/{id}', [SatuSehatController::class, 'getById']);
        Route::get('/{resource}/nik/{nik}', [SatuSehatController::class, 'getByNik']);
        Route::post('/encounter', [SatuSehatController::class, 'createEncounter']);
        Route::post('/condition', [SatuSehatController::class, 'createCondition']);
        Route::post('/observation', [SatuSehatController::class, 'createObservation']);
        Route::post('/patient', [SatuSehatController::class, 'createPatient']);
        Route::get('/patient/{id}', [SatuSehatController::class, 'getPatient']);
        Route::get('/patient/nik/{nik}', [SatuSehatController::class, 'getPatientByNik']);
    });

    // BPJS Bridging
    Route::prefix('bpjs')->group(function () {
        Route::get('/dashboard', [BpjsController::class, 'dashboard']);
        Route::get('/peserta', [BpjsController::class, 'cariPeserta']);
        Route::get('/sep/{nomor}', [BpjsController::class, 'cariSep']);
        Route::post('/sep/insert', [BpjsController::class, 'insertSep']);
        Route::get('/referensi/diagnosa', [BpjsController::class, 'referensiDiagnosa']);
        Route::get('/referensi/poli', [BpjsController::class, 'referensiPoli']);
        Route::get('/referensi/faskes', [BpjsController::class, 'referensiFaskes']);
        Route::get('/referensi/dpjp', [BpjsController::class, 'referensiDpjp']);
        Route::get('/antrean/ref-poli', [BpjsController::class, 'antreanRefPoli']);
        Route::get('/antrean/ref-dokter', [BpjsController::class, 'antreanRefDokter']);
        Route::get('/antrean/ref-jadwal', [BpjsController::class, 'antreanRefJadwal']);
        Route::post('/antrean/tambah', [BpjsController::class, 'antreanTambah']);
        Route::get('/antrean/dashboard', [BpjsController::class, 'antreanDashboard']);
        Route::get('/antrean/tanggal/{tanggal}', [BpjsController::class, 'antreanPerTanggal']);
        Route::get('/kamar-applicare', [BpjsController::class, 'kamarApplicare']);
        Route::get('/kamar-applicare/ref', [BpjsController::class, 'kamarApplicareRef']);
        Route::post('/kamar-applicare/update', [BpjsController::class, 'kamarApplicareUpdate']);
        Route::post('/surat-kontrol/insert', [BpjsController::class, 'suratKontrolInsert']);
        Route::get('/surat-kontrol/{nomor}', [BpjsController::class, 'suratKontrolCari']);
        Route::get('/surat-kontrol', [BpjsController::class, 'suratKontrolByNoka']);
        Route::post('/prb/insert', [BpjsController::class, 'prbInsert']);
        Route::get('/prb/cari', [BpjsController::class, 'prbCari']);
        Route::get('/prb/rekap', [BpjsController::class, 'prbRekap']);
        Route::get('/jadwal-hfis/poli', [BpjsController::class, 'jadwalHfisPoli']);
        Route::get('/jadwal-hfis/dokter', [BpjsController::class, 'jadwalHfisDokter']);
        Route::post('/jadwal-hfis/update', [BpjsController::class, 'jadwalHfisUpdate']);
        Route::post('/icare/fkrtl', [BpjsController::class, 'icareFkrtl']);
        Route::post('/icare/fktp', [BpjsController::class, 'icareFktp']);
    });
});
