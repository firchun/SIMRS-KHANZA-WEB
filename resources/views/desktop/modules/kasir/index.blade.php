<div x-data="{
    active: 'rajal',
    menu: [
        { key: 'rajal', label: 'Tagihan Rajal', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'kamar', label: 'Tagihan Kamar', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
        { key: 'ranap', label: 'Tagihan Ranap', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
        { key: 'laporan', label: 'Laporan Keuangan', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
        { key: 'cetak', label: 'Pengaturan Cetak', icon: 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z' },
    ],

    rajalList: [],
    kamarList: [],
    ranapList: [],
    rajalLoading: false,
    kamarLoading: false,
    ranapLoading: false,
    tgl1: '',
    tgl2: '',
    searchRajal: '',
    searchKamar: '',
    searchRanap: '',
    filterRajalStatus: '',
    filterRanapStatus: '',
    rajalTotals: { total_tagihan: 0, total_dibayar: 0, total_sisa: 0 },
    kamarTotalBiaya: 0,
    ranapTotals: { total_tagihan: 0, total_dibayar: 0, total_sisa: 0 },
    selectedTagihan: null,

    reportPeriod: 'hari',
    reportTgl1: '',
    reportTgl2: '',
    laporanList: [],
    laporanTotal: {},
    laporanLoading: false,

    printSettings: {
        printer: 'EPSON TM-U220',
        ukuran_kertas: '58mm',
        kop_kiri: 'RS KHANZA',
        kop_kanan: 'Sehat Bersama',
        footer: 'Terima Kasih',
        header_telp: '(021) 1234-5678',
        header_alamat: 'Jl. Kesehatan No. 1, Jakarta',
        cetak_otomatis: true,
        jumlah_copy: 1,
        format_tgl: 'dd/mm/yyyy',
    },

    init() {
        const today = new Date().toISOString().slice(0,10);
        this.tgl1 = today;
        this.tgl2 = today;
        this.reportTgl1 = new Date(Date.now() - 30*24*60*60*1000).toISOString().slice(0,10);
        this.reportTgl2 = today;
        this.loadRajal();
    },

    formatRupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    },

    async loadRajal() {
        this.rajalLoading = true;
        try {
            const res = await this.$store.api.get('/kasir/rajal?tgl1=' + this.tgl1 + '&tgl2=' + this.tgl2);
            this.rajalList = res.list || [];
            this.rajalTotals = { total_tagihan: res.total_tagihan || 0, total_dibayar: res.total_dibayar || 0, total_sisa: res.total_sisa || 0 };
        } catch (e) { console.log(e); }
        this.rajalLoading = false;
    },

    async loadKamar() {
        this.kamarLoading = true;
        try {
            const res = await this.$store.api.get('/kasir/kamar?tgl1=' + this.tgl1 + '&tgl2=' + this.tgl2);
            this.kamarList = res.list || [];
            this.kamarTotalBiaya = res.total_biaya || 0;
        } catch (e) { console.log(e); }
        this.kamarLoading = false;
    },

    async loadRanap() {
        this.ranapLoading = true;
        try {
            const res = await this.$store.api.get('/kasir/ranap?tgl1=' + this.tgl1 + '&tgl2=' + this.tgl2);
            this.ranapList = res.list || [];
            this.ranapTotals = { total_tagihan: res.total_tagihan || 0, total_dibayar: res.total_dibayar || 0, total_sisa: res.total_sisa || 0 };
        } catch (e) { console.log(e); }
        this.ranapLoading = false;
    },

    async loadLaporan() {
        this.laporanLoading = true;
        try {
            const params = 'period=' + this.reportPeriod + '&tgl1=' + this.reportTgl1 + '&tgl2=' + this.reportTgl2;
            const res = await this.$store.api.get('/kasir/laporan?' + params);
            this.laporanList = res.list || [];
            this.laporanTotal = res.total || {};
        } catch (e) { console.log(e); }
        this.laporanLoading = false;
    },

    setActive(key) {
        this.active = key;
        if (key === 'rajal') this.loadRajal();
        if (key === 'kamar') this.loadKamar();
        if (key === 'ranap') this.loadRanap();
        if (key === 'laporan') this.loadLaporan();
    },

    filteredRajal() {
        let s = this.searchRajal.toLowerCase();
        return this.rajalList.filter(p =>
            (!this.filterRajalStatus || p.status === this.filterRajalStatus) &&
            (!s || p.nm_pasien.toLowerCase().includes(s) || p.no_rkm_medis.toLowerCase().includes(s) || p.no_rawat.toLowerCase().includes(s))
        );
    },

    filteredKamar() {
        let s = this.searchKamar.toLowerCase();
        return this.kamarList.filter(p =>
            !s || p.nm_pasien.toLowerCase().includes(s) || p.no_rkm_medis.toLowerCase().includes(s) || p.no_rawat.toLowerCase().includes(s)
        );
    },

    filteredRanap() {
        let s = this.searchRanap.toLowerCase();
        return this.ranapList.filter(p =>
            (!this.filterRanapStatus || p.status === this.filterRanapStatus) &&
            (!s || p.nm_pasien.toLowerCase().includes(s) || p.no_rkm_medis.toLowerCase().includes(s) || p.no_rawat.toLowerCase().includes(s))
        );
    },

    openBayar(item) {
        this.selectedTagihan = item;
        this.$store.windows.open(
            { key: 'kasir-nota', label: 'Nota - ' + item.nm_pasien, icon: 'receipt', width: 420, height: 540 },
            { tagihan: item, fromList: this.active }
        );
    },

    get statusClass() {
        return {
            'Lunas': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
            'Belum Lunas': 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
            'Sebagian': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
        };
    },
}" class="flex h-full gap-0">

    {{-- SIDEBAR --}}
    <div class="w-44 shrink-0 flex flex-col overflow-y-auto border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
        <template x-for="item in menu" :key="item.key">
            <button @mousedown.stop @click="setActive(item.key)"
                class="flex items-center gap-2.5 px-3 py-3 text-xs text-left transition-colors border-l-2"
                :class="active === item.key ? 'font-semibold' : ''"
                :style="active === item.key ? 'background-color:var(--bg-hover);border-color:#EAB308;color:#EAB308' : 'border-color:transparent;color:var(--text-secondary)'">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path :d="item.icon"/>
                </svg>
                <div>
                    <div x-text="item.label" class="text-xs"></div>
                    <div class="text-[10px]" style="color:var(--text-muted)">
                        <span x-show="item.key === 'rajal'" x-text="'Sisa: ' + formatRupiah(rajalTotals.total_sisa)"></span>
                        <span x-show="item.key === 'kamar'" x-text="'Total: ' + formatRupiah(kamarTotalBiaya)"></span>
                        <span x-show="item.key === 'ranap'" x-text="'Sisa: ' + formatRupiah(ranapTotals.total_sisa)"></span>
                        <span x-show="item.key === 'laporan'">Grafik & Data</span>
                        <span x-show="item.key === 'cetak'">Konfigurasi Printer</span>
                    </div>
                </div>
            </button>
        </template>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4" style="color:var(--text-primary)">

        {{-- TAGIHAN RAJAL --}}
        <template x-if="active === 'rajal'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Tagihan Rawat Jalan</h3>
                    <div class="flex items-center gap-2">
                        <input @mousedown.stop type="date" x-model="tgl1" class="form-input text-xs w-28 py-0.5">
                        <span class="text-xs" style="color:var(--text-muted)">s/d</span>
                        <input @mousedown.stop type="date" x-model="tgl2" class="form-input text-xs w-28 py-0.5">
                        <button @mousedown.stop @click="loadRajal()" class="btn btn-secondary text-xs px-2 py-0.5">Cari</button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input @mousedown.stop type="text" x-model="searchRajal" placeholder="Cari pasien/no. RM..." class="form-input text-xs w-48">
                    <select @mousedown.stop x-model="filterRajalStatus" class="form-select text-xs w-28">
                        <option value="">Semua Status</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Belum Lunas">Belum Lunas</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(234,179,8,0.06);border-color:rgba(234,179,8,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Total Tagihan</div>
                        <div class="text-sm font-bold" x-text="formatRupiah(rajalTotals.total_tagihan)"></div>
                    </div>
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(34,197,94,0.06);border-color:rgba(34,197,94,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Sudah Dibayar</div>
                        <div class="text-sm font-bold text-green-600 dark:text-green-400" x-text="formatRupiah(rajalTotals.total_dibayar)"></div>
                    </div>
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(239,68,68,0.06);border-color:rgba(239,68,68,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Sisa Tagihan</div>
                        <div class="text-sm font-bold text-red-600 dark:text-red-400" x-text="formatRupiah(rajalTotals.total_sisa)"></div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>No. Rawat</th>
                                <th>Pasien</th>
                                <th>Tgl</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Penjamin</th>
                                <th>Total</th>
                                <th>Dibayar</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="p in filteredRajal()" :key="p.no_rawat">
                                <tr>
                                    <td class="font-mono text-[10px]" x-text="p.no_rawat"></td>
                                    <td class="font-medium whitespace-nowrap" x-text="p.nm_pasien"></td>
                                    <td class="text-xs" x-text="p.tgl_registrasi"></td>
                                    <td><span class="badge badge-info" x-text="p.nm_poli"></span></td>
                                    <td class="text-xs" x-text="p.nm_dokter"></td>
                                    <td class="text-xs" x-text="p.penjamin || '-'"></td>
                                    <td class="text-xs font-medium" x-text="formatRupiah(p.total_tagihan)"></td>
                                    <td class="text-xs" x-text="formatRupiah(p.dibayar)"></td>
                                    <td class="text-xs font-medium" :class="p.sisa > 0 ? 'text-red-500' : 'text-green-500'" x-text="formatRupiah(p.sisa)"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="statusClass[p.status] || ''" x-text="p.status"></span></td>
                                    <td>
                                        <button @mousedown.stop @click="openBayar(p)" x-show="p.sisa > 0" class="btn btn-primary text-[10px] px-2 py-0.5">Bayar</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="rajalLoading">
                                <td colspan="11" class="text-center py-6 text-xs" style="color:var(--text-muted)">Loading...</td>
                            </tr>
                            <tr x-show="!rajalLoading && filteredRajal().length === 0">
                                <td colspan="11" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada tagihan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- TAGIHAN KAMAR --}}
        <template x-if="active === 'kamar'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Tagihan Kamar</h3>
                    <div class="flex items-center gap-2">
                        <input @mousedown.stop type="date" x-model="tgl1" class="form-input text-xs w-28 py-0.5">
                        <span class="text-xs" style="color:var(--text-muted)">s/d</span>
                        <input @mousedown.stop type="date" x-model="tgl2" class="form-input text-xs w-28 py-0.5">
                        <button @mousedown.stop @click="loadKamar()" class="btn btn-secondary text-xs px-2 py-0.5">Cari</button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input @mousedown.stop type="text" x-model="searchKamar" placeholder="Cari pasien/no. RM..." class="form-input text-xs w-48">
                </div>

                <div class="grid grid-cols-1 gap-2 mb-2">
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(234,179,8,0.06);border-color:rgba(234,179,8,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Total Biaya Kamar</div>
                        <div class="text-sm font-bold" x-text="formatRupiah(kamarTotalBiaya)"></div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>No. Rawat</th>
                                <th>Pasien</th>
                                <th>Bangsal</th>
                                <th>Kamar</th>
                                <th>Kelas</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Lama</th>
                                <th>Tarif</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="p in filteredKamar()" :key="p.no_rawat + p.tgl_masuk">
                                <tr>
                                    <td class="font-mono text-[10px]" x-text="p.no_rawat"></td>
                                    <td class="font-medium whitespace-nowrap" x-text="p.nm_pasien"></td>
                                    <td class="text-xs" x-text="p.nm_bangsal || '-'"></td>
                                    <td class="text-xs font-mono" x-text="p.kd_kamar || '-'"></td>
                                    <td class="text-xs" x-text="p.kelas || '-'"></td>
                                    <td class="text-xs" x-text="p.tgl_masuk"></td>
                                    <td class="text-xs" x-text="p.tgl_keluar || '-'"></td>
                                    <td class="text-xs" x-text="p.lama + ' hr'"></td>
                                    <td class="text-xs" x-text="formatRupiah(p.trf_kamar)"></td>
                                    <td class="text-xs font-medium" x-text="formatRupiah(p.ttl_biaya)"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="p.status === 'Dirawat' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'" x-text="p.status"></span></td>
                                </tr>
                            </template>
                            <tr x-show="kamarLoading">
                                <td colspan="11" class="text-center py-6 text-xs" style="color:var(--text-muted)">Loading...</td>
                            </tr>
                            <tr x-show="!kamarLoading && filteredKamar().length === 0">
                                <td colspan="11" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada tagihan kamar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- TAGIHAN RANAP --}}
        <template x-if="active === 'ranap'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Tagihan Rawat Inap</h3>
                    <div class="flex items-center gap-2">
                        <input @mousedown.stop type="date" x-model="tgl1" class="form-input text-xs w-28 py-0.5">
                        <span class="text-xs" style="color:var(--text-muted)">s/d</span>
                        <input @mousedown.stop type="date" x-model="tgl2" class="form-input text-xs w-28 py-0.5">
                        <button @mousedown.stop @click="loadRanap()" class="btn btn-secondary text-xs px-2 py-0.5">Cari</button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input @mousedown.stop type="text" x-model="searchRanap" placeholder="Cari pasien/no. RM..." class="form-input text-xs w-48">
                    <select @mousedown.stop x-model="filterRanapStatus" class="form-select text-xs w-28">
                        <option value="">Semua Status</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Belum Lunas">Belum Lunas</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(234,179,8,0.06);border-color:rgba(234,179,8,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Total Tagihan</div>
                        <div class="text-sm font-bold" x-text="formatRupiah(ranapTotals.total_tagihan)"></div>
                    </div>
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(34,197,94,0.06);border-color:rgba(34,197,94,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Sudah Dibayar</div>
                        <div class="text-sm font-bold text-green-600 dark:text-green-400" x-text="formatRupiah(ranapTotals.total_dibayar)"></div>
                    </div>
                    <div class="rounded-lg p-2.5 border" style="background-color:rgba(239,68,68,0.06);border-color:rgba(239,68,68,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Sisa Tagihan</div>
                        <div class="text-sm font-bold text-red-600 dark:text-red-400" x-text="formatRupiah(ranapTotals.total_sisa)"></div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>No. Rawat</th>
                                <th>Pasien</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Bangsal</th>
                                <th>Kamar</th>
                                <th>Kelas</th>
                                <th>Total</th>
                                <th>Dibayar</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="p in filteredRanap()" :key="p.no_rawat">
                                <tr>
                                    <td class="font-mono text-[10px]" x-text="p.no_rawat"></td>
                                    <td class="font-medium whitespace-nowrap" x-text="p.nm_pasien"></td>
                                    <td class="text-xs" x-text="p.tgl_masuk || '-'"></td>
                                    <td class="text-xs" x-text="p.tgl_keluar || '-'"></td>
                                    <td class="text-xs" x-text="p.nm_bangsal || '-'"></td>
                                    <td class="text-xs" x-text="p.kd_kamar || '-'"></td>
                                    <td class="text-xs" x-text="p.kelas || '-'"></td>
                                    <td class="text-xs font-medium" x-text="formatRupiah(p.total_tagihan)"></td>
                                    <td class="text-xs" x-text="formatRupiah(p.dibayar)"></td>
                                    <td class="text-xs font-medium" :class="p.sisa > 0 ? 'text-red-500' : 'text-green-500'" x-text="formatRupiah(p.sisa)"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="statusClass[p.status] || ''" x-text="p.status"></span></td>
                                    <td>
                                        <button @mousedown.stop @click="openBayar(p)" x-show="p.sisa > 0" class="btn btn-primary text-[10px] px-2 py-0.5">Bayar</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="ranapLoading">
                                <td colspan="12" class="text-center py-6 text-xs" style="color:var(--text-muted)">Loading...</td>
                            </tr>
                            <tr x-show="!ranapLoading && filteredRanap().length === 0">
                                <td colspan="12" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada tagihan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- LAPORAN KEUANGAN --}}
        <template x-if="active === 'laporan'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Laporan Keuangan</h3>
                    <div class="flex items-center gap-2">
                        <input @mousedown.stop type="date" x-model="reportTgl1" class="form-input text-xs w-28 py-0.5">
                        <span class="text-xs" style="color:var(--text-muted)">s/d</span>
                        <input @mousedown.stop type="date" x-model="reportTgl2" class="form-input text-xs w-28 py-0.5">
                        <select @mousedown.stop x-model="reportPeriod" @change="loadLaporan" class="form-select text-xs w-24 py-0.5">
                            <option value="hari">Harian</option>
                            <option value="bulan">Bulanan</option>
                        </select>
                        <button @mousedown.stop @click="loadLaporan()" class="btn btn-secondary text-xs px-2 py-0.5">Tampilkan</button>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 mb-2">
                    <div class="rounded-lg p-3 border" style="background-color:rgba(59,130,246,0.06);border-color:rgba(59,130,246,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Total Pendapatan</div>
                        <div class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="formatRupiah(laporanTotal.total || 0)"></div>
                    </div>
                    <div class="rounded-lg p-3 border" style="background-color:rgba(16,185,129,0.06);border-color:rgba(16,185,129,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Rawat Jalan</div>
                        <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400" x-text="formatRupiah(laporanTotal.rajal || 0)"></div>
                    </div>
                    <div class="rounded-lg p-3 border" style="background-color:rgba(168,85,247,0.06);border-color:rgba(168,85,247,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Rawat Inap</div>
                        <div class="text-sm font-bold text-purple-600 dark:text-purple-400" x-text="formatRupiah(laporanTotal.ranap || 0)"></div>
                    </div>
                    <div class="rounded-lg p-3 border" style="background-color:rgba(234,179,8,0.06);border-color:rgba(234,179,8,0.2)">
                        <div class="text-[10px]" style="color:var(--text-muted)">Rata-rata / Periode</div>
                        <div class="text-sm font-bold text-yellow-600 dark:text-yellow-400" x-text="formatRupiah(Math.round(laporanTotal.rata_rata || 0))"></div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th x-text="reportPeriod === 'hari' ? 'Tanggal' : 'Bulan'"></th>
                                <th>Rawat Jalan</th>
                                <th>Rawat Inap</th>
                                <th>Total</th>
                                <th x-show="reportPeriod === 'hari'">Pasien</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="r in laporanList" :key="r.tgl || r.bulan">
                                <tr>
                                    <td class="font-medium text-xs" x-text="r.tgl || r.bulan"></td>
                                    <td class="text-xs" x-text="formatRupiah(r.rajal)"></td>
                                    <td class="text-xs" x-text="formatRupiah(r.ranap)"></td>
                                    <td class="text-xs font-bold" x-text="formatRupiah(r.total)"></td>
                                    <td x-show="reportPeriod === 'hari'" class="text-xs" x-text="r.jumlah_pasien + ' pasien'"></td>
                                </tr>
                            </template>
                            <tr x-show="laporanLoading">
                                <td colspan="5" class="text-center py-6 text-xs" style="color:var(--text-muted)">Loading...</td>
                            </tr>
                            <tr x-show="!laporanLoading && laporanList.length === 0">
                                <td colspan="5" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data laporan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- PENGATURAN CETAK --}}
        <template x-if="active === 'cetak'">
            <div class="space-y-3">
                <h3 class="text-base font-semibold">Pengaturan Cetak</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-lg border p-4 space-y-4" style="border-color:var(--border)">
                        <h4 class="text-sm font-semibold">Konfigurasi Printer</h4>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Printer</label>
                            <select @mousedown.stop x-model="printSettings.printer" class="form-select text-xs w-full">
                                <option>EPSON TM-U220</option>
                                <option>EPSON TM-T88V</option>
                                <option>EPSON LX-310</option>
                                <option>Canon LBP-6030</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Ukuran Kertas</label>
                            <select @mousedown.stop x-model="printSettings.ukuran_kertas" class="form-select text-xs w-full">
                                <option>58mm</option>
                                <option>80mm</option>
                                <option>A4</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jumlah Copy</label>
                            <input @mousedown.stop type="number" x-model="printSettings.jumlah_copy" min="1" max="5" class="form-input text-xs w-full">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" x-model="printSettings.cetak_otomatis" class="form-checkbox">
                            <span class="text-xs">Cetak otomatis setelah pembayaran</span>
                        </div>
                    </div>
                    <div class="rounded-lg border p-4 space-y-4" style="border-color:var(--border)">
                        <h4 class="text-sm font-semibold">Kop & Footer Kwitansi</h4>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Kop Kiri</label>
                            <input @mousedown.stop type="text" x-model="printSettings.kop_kiri" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Kop Kanan</label>
                            <input @mousedown.stop type="text" x-model="printSettings.kop_kanan" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Alamat</label>
                            <input @mousedown.stop type="text" x-model="printSettings.header_alamat" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Telepon</label>
                            <input @mousedown.stop type="text" x-model="printSettings.header_telp" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Footer</label>
                            <input @mousedown.stop type="text" x-model="printSettings.footer" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Format Tanggal</label>
                            <select @mousedown.stop x-model="printSettings.format_tgl" class="form-select text-xs w-full">
                                <option>dd/mm/yyyy</option>
                                <option>yyyy-mm-dd</option>
                                <option>dd Month yyyy</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button @mousedown.stop class="btn btn-secondary text-xs px-4 py-1.5">Reset</button>
                    <button @mousedown.stop class="btn btn-primary text-xs px-4 py-1.5" style="background-color:#EAB308">Simpan Pengaturan</button>
                </div>
            </div>
        </template>
    </div>
</div>
