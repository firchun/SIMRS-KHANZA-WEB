<div x-data="{
    active: 'auth',
    q: '',
    sections: [
        { key: 'auth', label: 'Auth' },
        { key: 'dashboard', label: 'Dashboard' },
        { key: 'pasien', label: 'Pasien' },
        { key: 'registrasi', label: 'Registrasi' },
        { key: 'igd', label: 'IGD' },
        { key: 'ralan', label: 'Ralan' },
        { key: 'ranap', label: 'Ranap' },
        { key: 'tindakan', label: 'Tindakan / SOAP' },
        { key: 'kasir', label: 'Kasir' },
        { key: 'jadwal', label: 'Jadwal & Poli' },
        { key: 'penjab', label: 'Penjab / Jenis Bayar' },
        { key: 'settings', label: 'Settings' },
        { key: 'icd', label: 'ICD' },
        { key: 'data-tindakan', label: 'Data Tindakan' },
        { key: 'apotek', label: 'Apotek / Farmasi' },
        { key: 'lab', label: 'Laboratorium' },
        { key: 'satusehat', label: 'Satu Sehat (FHIR)' },
        { key: 'bpjs', label: 'BPJS Kesehatan' },
    ],
    endpoints: {
        auth: [
            { method: 'POST', path: '/api/login', auth: false, desc: 'Login dan dapatkan token', body: '{ id_user, password }', res: '{ token, user }' },
            { method: 'GET', path: '/api/user', auth: true, desc: 'Data user yang sedang login', body: '-', res: '{ id_user, nama, username }' },
            { method: 'POST', path: '/api/logout', auth: true, desc: 'Hapus sesi token', body: '-', res: '{ message }' },
        ],
        dashboard: [
            { method: 'GET', path: '/api/dashboard/stats', auth: true, desc: 'Statistik kunjungan hari ini (IGD/Ralan/Ranap)', body: '-', res: '{ kunjungan, igd, ralan, ranap }' },
            { method: 'GET', path: '/api/pasien/search-location?q=', auth: true, desc: 'Cari pasien dengan lokasi terkini (taskbar search)', body: '-', res: '[{ id, nama, location, module_key }]' },
        ],
        pasien: [
            { method: 'GET', path: '/api/pasien', auth: true, desc: 'Daftar semua pasien', body: '-', res: '[{ no_rkm_medis, nm_pasien, no_ktp, tgl_lahir, jk }]' },
            { method: 'GET', path: '/api/pasien/search?q=', auth: true, desc: 'Cari pasien by nama/no_rkm_medis/no_ktp', body: '-', res: '[{ no_rkm_medis, nm_pasien, no_ktp, tgl_lahir, jk }]' },
            { method: 'GET', path: '/api/pasien/{no_rkm_medis}', auth: true, desc: 'Detail pasien by no_rkm_medis', body: '-', res: '{ no_rkm_medis, nm_pasien, ... }' },
            { method: 'POST', path: '/api/pasien/store', auth: true, desc: 'Tambah pasien baru', body: '{ no_rkm_medis, nm_pasien, no_ktp, tgl_lahir, jk, alamat }', res: '{ message, no_rkm_medis }' },
            { method: 'PUT', path: '/api/pasien/update/{no_rkm_medis}', auth: true, desc: 'Update data pasien', body: '{ nm_pasien, alamat, tgl_lahir, jk }', res: '{ message }' },
            { method: 'DELETE', path: '/api/pasien/delete/{no_rkm_medis}', auth: true, desc: 'Hapus pasien', body: '-', res: '{ message }' },
            { method: 'PUT', path: '/api/pasien/{pasien}', auth: true, desc: 'Update pasien (apiResource)', body: '{ nm_pasien, alamat }', res: '{ message }' },
            { method: 'DELETE', path: '/api/pasien/{pasien}', auth: true, desc: 'Hapus pasien (apiResource)', body: '-', res: '{ message }' },
            { method: 'GET', path: '/api/pasien/dokter-list', auth: true, desc: 'Daftar semua dokter aktif', body: '-', res: '[{ kd_dokter, nm_dokter }]' },
            { method: 'GET', path: '/api/pasien/search-regperiksa?q=', auth: true, desc: 'Cari pasien + reg_periksa terkini', body: '-', res: '[{ no_rawat, nm_pasien, no_rkm_medis, module_key }]' },
        ],
        registrasi: [
            { method: 'GET', path: '/api/registrasi/today', auth: true, desc: 'Daftar registrasi hari ini', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/registrasi/store', auth: true, desc: 'Buat registrasi baru (reg_periksa)', body: '{ no_rkm_medis, kd_poli, kd_dokter, kd_pj, stts_daftar }', res: '{ message, no_rawat }' },
            { method: 'GET', path: '/api/registrasi/dokter-by-poli?kd_poli=', auth: true, desc: 'Dokter berdasarkan poli', body: '-', res: '[{ kd_dokter, nm_dokter }]' },
            { method: 'GET', path: '/api/registrasi/petugas', auth: true, desc: 'Daftar petugas (pendaftaran)', body: '-', res: '[{ nip, nama }]' },
        ],
        igd: [
            { method: 'GET', path: '/api/igd/list?tgl1=&tgl2=&status=&q=', auth: true, desc: 'Daftar kunjungan IGD (filterable)', body: '-', res: '{ list: [...], counts }' },
            { method: 'GET', path: '/api/igd/dashboard', auth: true, desc: 'Dashboard IGD hari ini', body: '-', res: '{ total, kunjungan }' },
            { method: 'POST', path: '/api/igd/register', auth: true, desc: 'Registrasi pasien IGD', body: '{ no_rawat, kd_dokter, nip }', res: '{ message }' },
            { method: 'PUT', path: '/api/igd/status', auth: true, desc: 'Update status periksa IGD', body: '{ no_rawat, stts }', res: '{ message }' },
            { method: 'PUT', path: '/api/igd/update', auth: true, desc: 'Update data kunjungan IGD', body: '{ no_rawat, kd_dokter }', res: '{ message }' },
            { method: 'POST', path: '/api/igd/delete', auth: true, desc: 'Hapus kunjungan IGD', body: '{ no_rawat }', res: '{ message }' },
            { method: 'POST', path: '/api/igd/{registrasi}/triage', auth: true, desc: 'Input triage pasien IGD', body: '{ triase, td, nadi, suhu }', res: '{ message }' },
            { method: 'POST', path: '/api/igd/{registrasi}/tindakan', auth: true, desc: 'Tambah tindakan IGD', body: '{ kd_jenis_prw, jumlah, tarif }', res: '{ message }' },
            { method: 'POST', path: '/api/igd/{registrasi}/diagnosis', auth: true, desc: 'Tambah diagnosis IGD', body: '{ kode_icd, diagnosis, jenis }', res: '{ message }' },
        ],
        ralan: [
            { method: 'GET', path: '/api/ralan/list?tgl1=&tgl2=&poli=&dokter=&status=&q=', auth: true, desc: 'Daftar kunjungan Ralan (filterable)', body: '-', res: '{ list: [...], counts }' },
            { method: 'GET', path: '/api/ralan/dashboard', auth: true, desc: 'Dashboard Ralan per poli', body: '-', res: '{ list, total }' },
            { method: 'GET', path: '/api/ralan/queue?poli=', auth: true, desc: 'Antrian Ralan hari ini per poli', body: '-', res: '[{ no_rawat, nm_pasien, nm_poli, nm_dokter }]' },
            { method: 'GET', path: '/api/ralan/poli-list', auth: true, desc: 'Daftar poli aktif', body: '-', res: '[{ kd_poli, nm_poli }]' },
            { method: 'GET', path: '/api/ralan/dokter-list', auth: true, desc: 'Daftar dokter aktif', body: '-', res: '[{ kd_dokter, nm_dokter }]' },
            { method: 'POST', path: '/api/ralan/register', auth: true, desc: 'Registrasi pasien Ralan', body: '{ no_rawat, kd_dokter, kd_poli }', res: '{ message }' },
            { method: 'PUT', path: '/api/ralan/status', auth: true, desc: 'Update status periksa Ralan', body: '{ no_rawat, stts }', res: '{ message }' },
            { method: 'PUT', path: '/api/ralan/update', auth: true, desc: 'Update data kunjungan Ralan', body: '{ no_rawat, kd_dokter }', res: '{ message }' },
            { method: 'POST', path: '/api/ralan/delete', auth: true, desc: 'Hapus kunjungan Ralan', body: '{ no_rawat }', res: '{ message }' },
            { method: 'POST', path: '/api/ralan/{registrasi}/start', auth: true, desc: 'Mulai pemeriksaan pasien', body: '{ keluhan, tensi, suhu }', res: '{ message }' },
            { method: 'PUT', path: '/api/ralan/{kunjungan}/examination', auth: true, desc: 'Update hasil pemeriksaan', body: '{ diagnosis, tindakan, status_pulang }', res: '{ message }' },
            { method: 'POST', path: '/api/ralan/{kunjungan}/resep', auth: true, desc: 'Tambah resep obat', body: '{ obat, jumlah, aturan_pakai }', res: '{ message }' },
        ],
        ranap: [
            { method: 'GET', path: '/api/ranap/dashboard', auth: true, desc: 'Dashboard Rawat Inap', body: '-', res: '{ total_kamar, bed_terisi, hari_ini_masuk, hari_ini_keluar }' },
            { method: 'GET', path: '/api/ranap/list?tgl1=&tgl2=&status=&q=', auth: true, desc: 'Daftar pasien rawat inap (filterable)', body: '-', res: '{ list: [...], counts }' },
            { method: 'GET', path: '/api/ranap/kamar-list', auth: true, desc: 'Daftar kamar tersedia', body: '-', res: '[{ kd_kamar, nm_bangsal, kelas, trf_kamar, status }]' },
            { method: 'GET', path: '/api/ranap/riwayat-kamar?q=', auth: true, desc: 'Riwayat pemakaian kamar pasien', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/ranap/admit', auth: true, desc: 'Admisi pasien ke kamar inap', body: '{ no_rawat, kd_kamar, kd_dokter, diagnosa_awal }', res: '{ message }' },
            { method: 'PUT', path: '/api/ranap/pindah-kamar', auth: true, desc: 'Pindah kamar pasien rawat inap', body: '{ no_rawat, kd_kamar_baru }', res: '{ message }' },
            { method: 'PUT', path: '/api/ranap/pulangkan', auth: true, desc: 'Pulangkan pasien rawat inap', body: '{ no_rawat }', res: '{ message }' },
            { method: 'PUT', path: '/api/ranap/ubah-waktu-masuk', auth: true, desc: 'Ubah tanggal/jam masuk rawat', body: '{ no_rawat, tgl_masuk, jam_masuk }', res: '{ message }' },
            { method: 'PUT', path: '/api/ranap/ubah-waktu-keluar', auth: true, desc: 'Ubah tanggal/jam keluar rawat', body: '{ no_rawat, tgl_keluar, jam_keluar }', res: '{ message }' },
            { method: 'DELETE', path: '/api/ranap/{no_rawat}', auth: true, desc: 'Hapus kunjungan rawat inap', body: '-', res: '{ message }' },
            { method: 'GET', path: '/api/ranap/dpjp-doctors', auth: true, desc: 'Daftar dokter untuk DPJP', body: '-', res: '[{ kd_dokter, nm_dokter }]' },
            { method: 'GET', path: '/api/ranap/dpjp/{no_rawat}', auth: true, desc: 'Daftar DPJP pasien rawat inap', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/ranap/dpjp', auth: true, desc: 'Tambah DPJP pasien rawat inap', body: '{ no_rawat, kd_dokter }', res: '{ message }' },
            { method: 'DELETE', path: '/api/ranap/dpjp/{no_rawat}/{kd_dokter}', auth: true, desc: 'Hapus DPJP pasien rawat inap', body: '-', res: '{ message }' },
        ],
        tindakan: [
            { method: 'GET', path: '/api/tindakan/jns-perawatan?q=', auth: true, desc: 'Cari jenis perawatan/tindakan', body: '-', res: '[{ kd_jenis_prw, nm_perawatan, total_byrdrpr }]' },
            { method: 'GET', path: '/api/tindakan/petugas-list', auth: true, desc: 'Daftar petugas tindakan', body: '-', res: '[{ nip, nama }]' },
            { method: 'GET', path: '/api/tindakan/riwayat-kunjungan/{no_rkm_medis}', auth: true, desc: 'Riwayat kunjungan pasien', body: '-', res: '[{ no_rawat, tgl_registrasi, nm_dokter, nm_poli }]' },
            { method: 'GET', path: '/api/tindakan/riwayat-soap/{no_rkm_medis}', auth: true, desc: 'Riwayat SOAP pasien (IGD/Ralan)', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/ranap/riwayat-soap/{no_rkm_medis}', auth: true, desc: 'Riwayat SOAP pasien (Ranap)', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/tindakan/soap-list/{no_rawat}', auth: true, desc: 'Riwayat SOAP per rawat (IGD/Ralan)', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/tindakan/soap/{no_rawat}', auth: true, desc: 'Simpan SOAP (IGD/Ralan)', body: '{ keluhan, pemeriksaan, penilaian, instruksi, tensi, nadi, suhu }', res: '{ message }' },
            { method: 'GET', path: '/api/tindakan/soap-grafik/{no_rkm_medis}', auth: true, desc: 'Data grafik vital sign (IGD/Ralan)', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/tindakan/soap-grafik-rawat/{no_rawat}', auth: true, desc: 'Data grafik vital sign per rawat', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/ranap/soap-list/{no_rawat}', auth: true, desc: 'Riwayat SOAP per rawat (Ranap)', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/ranap/soap/{no_rawat}', auth: true, desc: 'Simpan SOAP (Ranap)', body: '{ keluhan, pemeriksaan, penilaian, instruksi, tensi, nadi, suhu }', res: '{ message }' },
            { method: 'GET', path: '/api/ranap/soap-grafik/{no_rkm_medis}', auth: true, desc: 'Data grafik vital sign (Ranap)', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/igd/{no_rawat}/penanganan', auth: true, desc: 'Daftar penanganan IGD', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/igd/{no_rawat}/penanganan', auth: true, desc: 'Simpan penanganan IGD', body: '{ kd_jenis_prw, kd_dokter, nip, tgl_perawatan, jam_rawat }', res: '{ message }' },
            { method: 'DELETE', path: '/api/igd/{no_rawat}/penanganan', auth: true, desc: 'Hapus penanganan IGD', body: '{ kd_jenis_prw, kd_dokter, nip, tgl_perawatan, jam_rawat }', res: '{ message }' },
            { method: 'GET', path: '/api/ranap/{no_rawat}/penanganan', auth: true, desc: 'Daftar penanganan Ranap', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/ranap/{no_rawat}/penanganan', auth: true, desc: 'Simpan penanganan Ranap', body: '{ kd_jenis_prw, kd_dokter, nip, tgl_perawatan, jam_rawat }', res: '{ message }' },
            { method: 'DELETE', path: '/api/ranap/{no_rawat}/penanganan', auth: true, desc: 'Hapus penanganan Ranap', body: '{ kd_jenis_prw, kd_dokter, nip, tgl_perawatan, jam_rawat }', res: '{ message }' },
        ],
        kasir: [
            { method: 'GET', path: '/api/kasir/rajal?tgl1=&tgl2=&status=&q=', auth: true, desc: 'Tagihan rawat jalan per periode', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/kasir/ranap?tgl1=&tgl2=&status=&q=', auth: true, desc: 'Tagihan rawat inap per periode', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/kasir/kamar?tgl1=&tgl2=', auth: true, desc: 'Tagihan kamar rawat inap per periode', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/kasir/laporan?tgl1=&tgl2=&period=', auth: true, desc: 'Laporan keuangan (harian/mingguan/bulanan)', body: '-', res: '{ rajal, ranap, total }' },
        ],
        jadwal: [
            { method: 'GET', path: '/api/jadwal/praktek', auth: true, desc: 'Jadwal praktek dokter', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/jadwal/poli', auth: true, desc: 'Daftar poliklinik', body: '-', res: '[{ kd_poli, nm_poli, registrasi, status }]' },
            { method: 'POST', path: '/api/jadwal/poli', auth: true, desc: 'Tambah poli baru', body: '{ kd_poli, nm_poli, registrasi, status }', res: '{ message }' },
            { method: 'PUT', path: '/api/jadwal/poli/{kd_poli}', auth: true, desc: 'Update data poli', body: '{ nm_poli, registrasi, status }', res: '{ message }' },
            { method: 'DELETE', path: '/api/jadwal/poli/{kd_poli}', auth: true, desc: 'Hapus poli', body: '-', res: '{ message }' },
        ],
        penjab: [
            { method: 'GET', path: '/api/jadwal/penjab', auth: true, desc: 'Daftar jenis bayar / penjamin', body: '-', res: '[{ kd_pj, png_jawab, status }]' },
            { method: 'POST', path: '/api/jadwal/penjab', auth: true, desc: 'Tambah jenis bayar baru', body: '{ kd_pj, png_jawab, status }', res: '{ message }' },
            { method: 'PUT', path: '/api/jadwal/penjab/{kd_pj}', auth: true, desc: 'Update jenis bayar', body: '{ png_jawab, status }', res: '{ message }' },
            { method: 'DELETE', path: '/api/jadwal/penjab/{kd_pj}', auth: true, desc: 'Hapus jenis bayar', body: '-', res: '{ message }' },
        ],
        settings: [
            { method: 'GET', path: '/api/identitas', auth: true, desc: 'Ambil data identitas faskes', body: '-', res: '{ nama_instansi, alamat_instansi, kabupaten, propinsi, kontak, email }' },
            { method: 'PUT', path: '/api/identitas', auth: true, desc: 'Update identitas faskes', body: '{ nama_instansi, alamat_instansi, kabupaten, propinsi, kontak, email }', res: '{ message }' },
            { method: 'GET', path: '/api/settings/depo-list', auth: true, desc: 'Daftar depo farmasi', body: '-', res: '[{ kd_bangsal, nm_bangsal }]' },
            { method: 'GET', path: '/api/settings/industri-farmasi', auth: true, desc: 'Daftar industri farmasi', body: '-', res: '[{ kode_industri, nama_industri }]' },
        ],
        icd: [
            { method: 'GET', path: '/api/icd/search?q=', auth: true, desc: 'Cari diagnosis ICD 10 & tindakan ICD 9', body: '-', res: '{ icd10: [...], icd9: [...] }' },
        ],
        'data-tindakan': [
            { method: 'GET', path: '/api/data-tindakan/list/{jenis}?q=&limit=', auth: true, desc: 'Master tarif tindakan (jalan/inap/operasi/lab/radiologi)', body: '-', res: '{ total, data: [...] }' },
        ],
        apotek: [
            { method: 'GET', path: '/api/apotek/dashboard', auth: true, desc: 'Dashboard farmasi (counts, stok, mutasi)', body: '-', res: '{ counts, obat_habis, stok_unit, stok_masuk_keluar }' },
            { method: 'GET', path: '/api/apotek/industri', auth: true, desc: 'Daftar industri farmasi', body: '-', res: '[{ kode_industri, nama_industri, alamat, kota, telpon, npwp, status }]' },
            { method: 'GET', path: '/api/apotek/jenis', auth: true, desc: 'Daftar jenis obat/BHP', body: '-', res: '[{ kdjns, nama }]' },
            { method: 'GET', path: '/api/apotek/kategori', auth: true, desc: 'Daftar kategori obat/BHP', body: '-', res: '[{ kode, nama }]' },
            { method: 'GET', path: '/api/apotek/golongan', auth: true, desc: 'Daftar golongan obat/BHP', body: '-', res: '[{ kode, nama }]' },
            { method: 'GET', path: '/api/apotek/kodesatuan', auth: true, desc: 'Daftar kode satuan', body: '-', res: '[{ kode_sat, satuan }]' },
            { method: 'GET', path: '/api/apotek/databarang?q=&page=&perPage=', auth: true, desc: 'Data obat/BHP (searchable, paginated)', body: '-', res: '{ data: [...], total, page, lastPage }' },
        ],
        lab: [
            { method: 'GET', path: '/api/lab/list?tgl1=&tgl2=&status=&q=', auth: true, desc: 'Daftar permintaan laboratorium', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/lab/detail/{noorder}', auth: true, desc: 'Detail permintaan lab', body: '-', res: '{ permintaan, detail: [...] }' },
            { method: 'PUT', path: '/api/lab/sample', auth: true, desc: 'Update status pengambilan sampel', body: '{ noorder, tgl_sampel, jam_sampel }', res: '{ message }' },
            { method: 'PUT', path: '/api/lab/hasil', auth: true, desc: 'Update hasil pemeriksaan', body: '{ noorder, kd_jenis_prw, hasil }', res: '{ message }' },
            { method: 'GET', path: '/api/lab/data-hasil/{noorder}', auth: true, desc: 'Data hasil pemeriksaan lab', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/lab/simpan-hasil', auth: true, desc: 'Simpan hasil pemeriksaan lab', body: '{ noorder, detail: [...] }', res: '{ message }' },
            { method: 'GET', path: '/api/lab/templates', auth: true, desc: 'Template hasil lab', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/lab/perawatan/{kategori}', auth: true, desc: 'Jenis perawatan lab per kategori', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/lab/kirim-lis', auth: true, desc: 'Kirim data ke LIS', body: '{ noorder }', res: '{ message }' },
            { method: 'POST', path: '/api/lab/tarik-lis', auth: true, desc: 'Tarik hasil dari LIS', body: '{ noorder }', res: '{ message }' },
        ],
        satusehat: [
            { method: 'GET', path: '/api/satusehat/dashboard', auth: true, desc: 'Dashboard SATU SEHAT (counts per resource)', body: '-', res: '{ counts: {...} }' },
            { method: 'GET', path: '/api/satusehat/token', auth: true, desc: 'Cek/token akses SATU SEHAT', body: '-', res: '{ token, expires_in }' },
            { method: 'GET', path: '/api/satusehat/{resource}/{id}', auth: true, desc: 'Get resource by ID (Encounter, Condition, etc)', body: '-', res: '{ resourceType, id, ... }' },
            { method: 'GET', path: '/api/satusehat/{resource}/nik/{nik}', auth: true, desc: 'Get resource by NIK pasien', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/satusehat/patient/{id}', auth: true, desc: 'Get patient SATU SEHAT by ID', body: '-', res: '{ resourceType, id, name, ... }' },
            { method: 'GET', path: '/api/satusehat/patient/nik/{nik}', auth: true, desc: 'Cari patient SATU SEHAT by NIK', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/satusehat/patient', auth: true, desc: 'Buat patient BARU ke SATU SEHAT', body: '{ no_rkm_medis }', res: '{ message, id }' },
            { method: 'POST', path: '/api/satusehat/encounter', auth: true, desc: 'Kirim encounter ke SATU SEHAT', body: '{ no_rawat }', res: '{ message, id }' },
            { method: 'POST', path: '/api/satusehat/condition', auth: true, desc: 'Kirim diagnosis ke SATU SEHAT', body: '{ no_rawat, kode_icd }', res: '{ message, id }' },
            { method: 'POST', path: '/api/satusehat/observation', auth: true, desc: 'Kirim observasi ke SATU SEHAT', body: '{ no_rawat, jenis, nilai }', res: '{ message, id }' },
        ],
        bpjs: [
            { method: 'GET', path: '/api/bpjs/dashboard?tgl1=&tgl2=', auth: true, desc: 'Dashboard BPJS', body: '-', res: '{ antrian, sep, status_api, kamar }' },
            { method: 'GET', path: '/api/bpjs/peserta?nomor=&tanggal=', auth: true, desc: 'Cari peserta BPJS by no_kartu/NIK', body: '-', res: '{ peserta, metaData }' },
            { method: 'GET', path: '/api/bpjs/sep/{nomor}', auth: true, desc: 'Cari SEP by nomor SEP', body: '-', res: '{ sep, metaData }' },
            { method: 'POST', path: '/api/bpjs/sep/insert', auth: true, desc: 'Buat SEP baru', body: '{ no_kartu, tgl_sep, kd_poli, kd_diagnosa, ... }', res: '{ sep, metaData }' },
            { method: 'GET', path: '/api/bpjs/referensi/diagnosa?diagnosa=', auth: true, desc: 'Referensi diagnosis BPJS', body: '-', res: '{ diagnosa: [...] }' },
            { method: 'GET', path: '/api/bpjs/referensi/poli?poli=', auth: true, desc: 'Referensi poli BPJS', body: '-', res: '{ poli: [...] }' },
            { method: 'GET', path: '/api/bpjs/referensi/faskes?faskes=', auth: true, desc: 'Referensi faskes BPJS', body: '-', res: '{ faskes: [...] }' },
            { method: 'GET', path: '/api/bpjs/referensi/dpjp?dpjp=', auth: true, desc: 'Referensi DPJP BPJS', body: '-', res: '{ dpjp: [...] }' },
            { method: 'GET', path: '/api/bpjs/antrean/ref-poli', auth: true, desc: 'Referensi poli antrean BPJS', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/bpjs/antrean/ref-dokter', auth: true, desc: 'Referensi dokter antrean BPJS', body: '-', res: '[...]' },
            { method: 'GET', path: '/api/bpjs/antrean/ref-jadwal', auth: true, desc: 'Referensi jadwal antrean BPJS', body: '-', res: '[...]' },
            { method: 'POST', path: '/api/bpjs/antrean/tambah', auth: true, desc: 'Tambah antrean BPJS', body: '{ nomor_kartu, kd_poli, kd_dokter, ... }', res: '{ response, metaData }' },
            { method: 'GET', path: '/api/bpjs/antrean/dashboard', auth: true, desc: 'Dashboard antrean BPJS', body: '-', res: '{ data: [...] }' },
            { method: 'GET', path: '/api/bpjs/antrean/tanggal/{tanggal}', auth: true, desc: 'Antrean per tanggal', body: '-', res: '{ data: [...] }' },
            { method: 'GET', path: '/api/bpjs/kamar-applicare', auth: true, desc: 'Data kamar Applicare', body: '-', res: '{ data: [...] }' },
            { method: 'GET', path: '/api/bpjs/kamar-applicare/ref', auth: true, desc: 'Referensi kamar Applicare', body: '-', res: '{ data: [...] }' },
            { method: 'POST', path: '/api/bpjs/kamar-applicare/update', auth: true, desc: 'Update kamar Applicare', body: '{ kd_kamar, status }', res: '{ message }' },
            { method: 'POST', path: '/api/bpjs/surat-kontrol/insert', auth: true, desc: 'Buat surat kontrol', body: '{ nomor_sep, tgl_rencana, kd_poli, kd_dokter }', res: '{ response }' },
            { method: 'GET', path: '/api/bpjs/surat-kontrol/{nomor}', auth: true, desc: 'Cari surat kontrol by nomor', body: '-', res: '{ data: [...] }' },
            { method: 'GET', path: '/api/bpjs/surat-kontrol?noka=', auth: true, desc: 'Cari surat kontrol by no kartu', body: '-', res: '{ data: [...] }' },
            { method: 'POST', path: '/api/bpjs/prb/insert', auth: true, desc: 'Buat PRB', body: '{ nomor_sep, kd_diagnosa, kd_obat }', res: '{ response }' },
            { method: 'GET', path: '/api/bpjs/prb/cari?nomor_sep=', auth: true, desc: 'Cari PRB by SEP', body: '-', res: '{ data: [...] }' },
            { method: 'GET', path: '/api/bpjs/prb/rekap?bulan=&tahun=', auth: true, desc: 'Rekap PRB', body: '-', res: '{ data: [...] }' },
            { method: 'GET', path: '/api/bpjs/jadwal-hfis/poli', auth: true, desc: 'Jadwal HFIS poli', body: '-', res: '{ response: [...] }' },
            { method: 'GET', path: '/api/bpjs/jadwal-hfis/dokter', auth: true, desc: 'Jadwal HFIS dokter', body: '-', res: '{ response: [...] }' },
            { method: 'POST', path: '/api/bpjs/jadwal-hfis/update', auth: true, desc: 'Update jadwal HFIS', body: '{ kd_poli, kd_dokter, hari, jam_mulai, jam_selesai }', res: '{ message }' },
            { method: 'POST', path: '/api/bpjs/icare/fkrtl', auth: true, desc: 'Icare FKRTL', body: '{ nomor_kartu, kode_dokter }', res: '{ response }' },
            { method: 'POST', path: '/api/bpjs/icare/fktp', auth: true, desc: 'Icare FKTP', body: '{ nomor_kartu }', res: '{ response }' },
        ],
    },

    get filteredSections() {
        if (!this.q) return this.sections;
        const q = this.q.toLowerCase();
        return this.sections.filter(s => {
            const eps = this.endpoints[s.key] || [];
            return s.label.toLowerCase().includes(q) || eps.some(e =>
                e.path.toLowerCase().includes(q) || e.desc.toLowerCase().includes(q)
            );
        });
    },

    get activeEndpoints() {
        return this.endpoints[this.active] || [];
    }
}" class="flex h-full gap-0"
    style="color:var(--text-primary)">

    {{-- Sidebar --}}
    <div class="w-44 shrink-0 flex flex-col border-r overflow-hidden" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="px-2 py-1.5 border-b shrink-0" style="border-color:var(--border)">
            <input type="text" x-model="q" placeholder="Cari endpoint..." class="form-input text-[11px] py-1 w-full">
        </div>
        <div class="flex-1 overflow-y-auto min-h-0">
            <template x-for="s in filteredSections" :key="s.key">
                <button @mousedown.stop @click="active = s.key"
                    class="w-full flex items-center gap-2 px-3 py-2 text-xs text-left transition-colors border-l-2"
                    :class="active === s.key ? 'font-semibold' : ''"
                    :style="active === s.key ? 'background-color:var(--bg-hover);border-color:var(--accent-blue);color:var(--accent-blue)' : 'border-color:transparent;color:var(--text-secondary)'">
                    <span x-text="s.label"></span>
                    <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded-full" style="background-color:var(--bg-hover);color:var(--text-muted)" x-text="endpoints[s.key]?.length || 0"></span>
                </button>
            </template>
            <div x-show="!filteredSections.length" class="px-3 py-4 text-xs text-center" style="color:var(--text-muted)">Tidak ditemukan</div>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0" style="color:var(--text-primary)">
        <template x-for="(ep, i) in activeEndpoints" :key="i">
            <div class="rounded-lg border" style="border-color:var(--border);background-color:var(--bg-card)">
                <div class="flex items-center gap-2 px-3 py-2 border-b" style="border-color:var(--border);background-color:var(--bg-header)">
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase whitespace-nowrap"
                        :class="ep.method === 'GET' ? 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30' : ep.method === 'POST' ? 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30' : ep.method === 'PUT' ? 'text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30' : 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30'"
                        x-text="ep.method"></span>
                    <code class="text-xs font-mono font-medium break-all" x-text="ep.path"></code>
                    <template x-if="!ep.auth">
                        <span class="text-[10px] ml-auto px-1.5 py-0.5 rounded-full whitespace-nowrap bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Tanpa Auth</span>
                    </template>
                    <template x-if="ep.auth">
                        <span class="text-[10px] ml-auto px-1.5 py-0.5 rounded-full whitespace-nowrap bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Auth</span>
                    </template>
                </div>
                <div class="p-3 space-y-2 text-xs">
                    <p style="color:var(--text-secondary)" x-text="ep.desc"></p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-[10px] font-medium" style="color:var(--text-muted)">Request Body</span>
                            <pre class="mt-0.5 p-1.5 rounded text-[10px] font-mono overflow-x-auto" style="background-color:var(--bg-muted);color:var(--text-secondary)" x-text="ep.body"></pre>
                        </div>
                        <div>
                            <span class="text-[10px] font-medium" style="color:var(--text-muted)">Response</span>
                            <pre class="mt-0.5 p-1.5 rounded text-[10px] font-mono overflow-x-auto" style="background-color:var(--bg-muted);color:var(--text-secondary)" x-text="ep.res"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div x-show="!activeEndpoints.length" class="text-center py-8 text-xs" style="color:var(--text-muted)">Tidak ada endpoint</div>
    </div>
</div>
