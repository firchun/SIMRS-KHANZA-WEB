<div x-data="{
    pasien: null,
    jenis: 'igd',
    activeTab: 'penanganan',
    activeSidebar: null,
    sidebarQ: '',
    sidebarItems: [
        { key: 'riwayat', label: 'Riwayat Pasien', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        { key: 'resep', label: 'Input Resep', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'copy-resep', label: 'Copy Resep', icon: 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z' },
        { key: 'resep-luar', label: 'Resep Luar', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'obat-bhp', label: 'Input Obat & BHP', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
        { key: 'data-obat', label: 'Data Obat & BHP', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4' },
        { key: 'berkas', label: 'Berkas Digital', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'lab', label: 'Permintaan Lab', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'radiologi', label: 'Permintaan Radiologi', icon: 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' },
        { key: 'konsultasi', label: 'Konsultasi Medik', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
        { key: 'operasi', label: 'Jadwal Operasi', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
        { key: 'surat-kontrol', label: 'Surat Kontrol', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
        { key: 'kamar-inap', label: 'Kamar Inap', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
        { key: 'keperawatan-igd', label: 'Awal Keperawatan IGD', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
        { key: 'keperawatan-umum', label: 'Awal Keperawatan Umum', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
        { key: 'observasi-igd', label: 'Observasi IGD', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
        { key: 'observasi-bayi', label: 'Observasi Bayi', icon: 'M12 2l7 4.5v5.5c0 4.5-3.5 8.5-7 9.5-3.5-1-7-5-7-9.5V6.5L12 2z' },
        { key: 'observasi-hd', label: 'Observasi Hemodialisa', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'mc', label: 'Medical Checkup', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    ],
    mainTabs: [
        { key: 'penanganan', label: 'Penanganan Dokter & Petugas' },
        { key: 'pemeriksaan', label: 'Pemeriksaan (SOAP)' },
        { key: 'diagnosa', label: 'Diagnosa' },
        { key: 'catatan', label: 'Catatan Dokter' },
    ],

    get penangananPrefix() { return this.jenis === 'ranap' ? '/ranap/' : '/igd/'; },
    get soapPrefix() { return this.jenis === 'ranap' ? '/ranap/' : '/tindakan/'; },

    get filteredSidebar() {
        if (!this.sidebarQ) return this.sidebarItems;
        const q = this.sidebarQ.toLowerCase();
        return this.sidebarItems.filter(i => i.label.toLowerCase().includes(q));
    },

    penangananList: [],
    jnsPerawatanList: [],
    dokterList: [],
    petugasList: [],
    pnForm: { kd_jenis_prw: '', nm_perawatan: '', kd_dokter: '', nm_dokter: '', nip: '', nm_petugas: '', tgl_perawatan: '', jam_rawat: '', biaya_rawat: 0 },
    pnLoading: false,
    pnSearch: '',
    pnShowDropdown: false,

    get filteredJnsPerawatan() {
        const q = this.pnSearch.toLowerCase();
        if (!q) return this.jnsPerawatanList.slice(0, 20);
        return this.jnsPerawatanList.filter(i =>
            i.nm_perawatan.toLowerCase().includes(q) ||
            i.kd_jenis_prw.toLowerCase().includes(q)
        ).slice(0, 50);
    },

    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data) {
            if (win.data.jenis) this.jenis = win.data.jenis;
            if (win.data.pasien) this.pasien = win.data.pasien;
        }
        const stateKey = this.jenis === 'ranap' ? '__ranapTindakanState' : '__tindakanState';
        const saved = window[stateKey] = window[stateKey] || {};
        const st = saved[winId];
        if (st) {
            if (st.activeTab) this.activeTab = st.activeTab;
            if (st.activeSidebar) this.activeSidebar = st.activeSidebar;
        }
        this.pnForm.tgl_perawatan = new Date().toISOString().slice(0,10);
        this.pnForm.jam_rawat = new Date().toTimeString().slice(0,5);
        this.loadJnsPerawatan();
        this.loadDokter();
        this.loadPetugas();
        if (this.pasien) this.loadPenanganan();
        if (this.activeTab === 'pemeriksaan') this.loadSoapHistory();
    },

    async loadPenanganan() {
        if (!this.pasien?.no_rawat) return;
        this.pnLoading = true;
        try {
            const res = await this.$store.api.get(this.penangananPrefix + this.pasien.no_rawat + '/penanganan');
            this.penangananList = res || [];
        } catch (e) { console.error(e); }
        this.pnLoading = false;
    },

    async loadJnsPerawatan() {
        try {
            const res = await this.$store.api.get('/tindakan/jns-perawatan');
            this.jnsPerawatanList = res || [];
        } catch (e) { console.error(e); }
    },

    async loadDokter() {
        try {
            const res = await this.$store.api.get('/pasien/dokter-list');
            this.dokterList = res || [];
        } catch (e) { console.error(e); }
    },

    async loadPetugas() {
        try {
            const res = await this.$store.api.get('/tindakan/petugas-list');
            this.petugasList = res || [];
        } catch (e) { console.error(e); }
    },

    pilihTindakan(item) {
        this.pnForm.kd_jenis_prw = item.kd_jenis_prw;
        this.pnForm.nm_perawatan = item.nm_perawatan;
        this.pnForm.biaya_rawat = Number(item.total_byrdrpr || item.total_byrdr || 0);
        this.pnSearch = item.nm_perawatan;
        this.pnShowDropdown = false;
    },

    resetPnForm() {
        this.pnForm = {
            kd_jenis_prw: '', nm_perawatan: '', kd_dokter: '', nm_dokter: '',
            nip: '', nm_petugas: '', tgl_perawatan: new Date().toISOString().slice(0,10),
            jam_rawat: new Date().toTimeString().slice(0,5), biaya_rawat: 0
        };
        this.pnSearch = '';
    },

    async simpanPenanganan() {
        if (!this.pnForm.kd_jenis_prw || !this.pnForm.kd_dokter || !this.pnForm.nip) return;
        this.pnLoading = true;
        try {
            await this.$store.api.post(this.penangananPrefix + this.pasien.no_rawat + '/penanganan', this.pnForm);
            this.$store.api.cacheBust(this.penangananPrefix + this.pasien.no_rawat + '/penanganan');
            this.resetPnForm();
            await this.loadPenanganan();
        } catch (e) { console.error(e); }
        this.pnLoading = false;
    },

    async hapusPenanganan(item) {
        if (!confirm('Hapus penanganan ini?')) return;
        this.pnLoading = true;
        try {
            await this.$store.api.delete(this.penangananPrefix + this.pasien.no_rawat + '/penanganan', {
                kd_jenis_prw: item.kd_jenis_prw,
                kd_dokter: item.kd_dokter,
                nip: item.nip,
                tgl_perawatan: item.tgl_perawatan,
                jam_rawat: item.jam_rawat,
            });
            this.$store.api.cacheBust(this.penangananPrefix + this.pasien.no_rawat + '/penanganan');
            await this.loadPenanganan();
        } catch (e) { console.error(e); }
        this.pnLoading = false;
    },

    riwayatTab: 'kunjungan',
    kunjunganList: [],
    soapList: [],
    riwayatLoading: false,

    soapForm: { keluhan: '', pemeriksaan: '', penilaian: '', instruksi: '', evaluasi: '', tensi: '', suhu_tubuh: '', nadi: '', respirasi: '', spo2: '', gcs: '', kesadaran: '', tinggi: '', berat: '', lingkar_perut: '', alergi: '', nip: '' },
    soapHistory: [],
    soapLoading: false,
    soapSaving: false,
    showGrafik: false,
    grafikLoading: false,
    grafikData: [],

    setRiwayatTab(key) {
        this.riwayatTab = key;
        this.loadRiwayat();
    },

    async loadRiwayat() {
        if (!this.pasien?.no_rkm_medis) return;
        this.riwayatLoading = true;
        try {
            if (this.riwayatTab === 'kunjungan') {
                const res = await this.$store.api.get('/tindakan/riwayat-kunjungan/' + this.pasien.no_rkm_medis);
                this.kunjunganList = res || [];
            } else {
                const res = await this.$store.api.get(this.soapPrefix + 'riwayat-soap/' + this.pasien.no_rkm_medis);
                this.soapList = res || [];
            }
        } catch (e) { console.error(e); }
        this.riwayatLoading = false;
    },

    saveState() {
        const key = this.jenis === 'ranap' ? '__ranapTindakanState' : '__tindakanState';
        const s = window[key] = window[key] || {};
        const e = this.$el.closest('[data-window-id]');
        const wid = e?.dataset?.windowId;
        if (wid) {
            s[wid] = { activeTab: this.activeTab, activeSidebar: this.activeSidebar };
        }
    },

    setSidebar(key) {
        this.activeSidebar = key;
        this.activeTab = null;
        this.saveState();
        if (key === 'riwayat') {
            this.riwayatTab = 'kunjungan';
            this.loadRiwayat();
        }
    },

    setTab(key) {
        this.activeTab = key;
        this.activeSidebar = null;
        this.saveState();
        if (key === 'pemeriksaan') {
            this.loadSoapHistory();
        }
    },

    async loadSoapHistory() {
        if (!this.pasien?.no_rawat) return;
        this.soapLoading = true;
        try {
            const res = await this.$store.api.get(this.soapPrefix + 'soap-list/' + this.pasien.no_rawat);
            this.soapHistory = res || [];
        } catch (e) { console.error(e); }
        this.soapLoading = false;
    },

    async saveSoap() {
        if (!this.pasien?.no_rawat) return;
        this.soapSaving = true;
        try {
            await this.$store.api.post(this.soapPrefix + 'soap/' + this.pasien.no_rawat, this.soapForm);
            this.$store.api.cacheBust(this.soapPrefix + 'soap-list/' + this.pasien.no_rawat);
            this.$store.api.cacheBust(this.soapPrefix + 'riwayat-soap/' + this.pasien.no_rkm_medis);
            this.resetSoapForm();
            await this.loadSoapHistory();
        } catch (e) { console.error(e); }
        this.soapSaving = false;
    },

    resetSoapForm() {
        this.soapForm = {
            keluhan: '', pemeriksaan: '', penilaian: '', instruksi: '', evaluasi: '',
            tensi: '', suhu_tubuh: '', nadi: '', respirasi: '', spo2: '',
            gcs: '', kesadaran: '', tinggi: '', berat: '', lingkar_perut: '',
            alergi: '', nip: '',
        };
    },

    async loadGrafik() {
        if (!this.pasien?.no_rkm_medis) return;
        this.grafikLoading = true;
        try {
            const res = await this.$store.api.get(this.soapPrefix + 'soap-grafik/' + this.pasien.no_rkm_medis);
            this.grafikData = res || [];
        } catch (e) { console.error(e); }
        this.grafikLoading = false;
    },

    openGrafik() {
        this.showGrafik = true;
        this.loadGrafik();
    },

    closeGrafik() {
        this.showGrafik = false;
        this.grafikData = [];
    },

    formatRupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
}" class="flex flex-col h-full"
    style="color:var(--text-primary)">

    {{-- Patient Info Header --}}
    <template x-if="pasien">
        <div class="flex items-center gap-3 px-3 py-2 border-b shrink-0" style="background-color:var(--bg-muted);border-color:var(--border)">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white bg-red-500" x-text="(pasien.nm_pasien || '?').charAt(0)"></div>
            <div class="flex-1">
                <div class="text-sm font-bold" x-text="pasien.nm_pasien || 'Pasien'"></div>
                <div class="text-[11px]" style="color:var(--text-muted)">
                    <span x-text="pasien.no_rkm_medis || '-'"></span>
                    <span class="mx-1">|</span>
                    <span x-text="pasien.no_rawat || '-'"></span>
                    <span class="mx-1">|</span>
                    <span x-text="pasien.umur || '-'"></span>
                    <span class="mx-1">|</span>
                    <span x-text="pasien.jk === 'L' ? 'Laki-laki' : pasien.jk === 'P' ? 'Perempuan' : ''"></span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] px-2 py-0.5 rounded font-medium bg-blue-100 text-blue-700" x-text="pasien.jenis_bayar || '-'"></span>
                <span class="text-[11px] px-2 py-0.5 rounded font-medium" x-text="pasien.stts_daftar || ''"></span>
            </div>
        </div>
    </template>

    {{-- Main Layout: Sidebar + Content --}}
    <div class="flex flex-1 overflow-hidden">
        {{-- Sidebar --}}
        <div class="w-52 shrink-0 flex flex-col border-r overflow-hidden" style="border-color:var(--border);background-color:var(--bg-muted)">
            <div class="px-2 py-1.5 border-b shrink-0" style="border-color:var(--border)">
                <input type="text" x-model="sidebarQ" placeholder="Cari menu..." class="form-input text-[11px] py-1 w-full">
            </div>
            <div class="flex-1 overflow-y-auto min-h-0">
                <template x-for="item in filteredSidebar" :key="item.key">
                    <button @mousedown.stop @click="setSidebar(item.key)"
                        class="w-full text-left px-3 py-2 text-xs flex items-center gap-2 hover:bg-gray-100 dark:hover:bg-gray-700 border-b transition-colors"
                        :class="activeSidebar === item.key ? 'bg-blue-50 dark:bg-blue-900/20 font-semibold' : ''"
                        style="border-color:var(--border)">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path :d="item.icon"/>
                        </svg>
                        <span x-text="item.label"></span>
                    </button>
                </template>
                <div x-show="!filteredSidebar.length" class="px-3 py-4 text-xs text-center" style="color:var(--text-muted)">Menu tidak ditemukan</div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Tab Bar --}}
            <div class="flex border-b shrink-0 overflow-x-auto" style="border-color:var(--border);background-color:var(--bg-muted)">
                <template x-for="tab in mainTabs" :key="tab.key">
                    <button @mousedown.stop @click="setTab(tab.key)"
                        class="px-4 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"
                        :class="activeTab === tab.key ? '' : 'border-transparent'"
                        :style="activeTab === tab.key ? 'border-color:#2563eb;color:#2563eb' : 'color:var(--text-secondary)'">
                        <span x-text="tab.label"></span>
                    </button>
                </template>
            </div>

            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto p-4 min-h-0">
                <div x-show="activeTab === 'penanganan'" class="flex flex-col h-full space-y-3">
                    <div class="shrink-0">
                        <h3 class="text-sm font-bold" style="color:#2563eb">Penanganan Dokter & Petugas</h3>
                        <div class="text-xs" style="color:var(--text-muted)">
                            No.Rawat: <strong x-text="pasien?.no_rawat || '-'"></strong>
                            <span class="mx-1">|</span>Total: <strong x-text="penangananList.length + ' item'"></strong>
                        </div>
                    </div>

                    {{-- Form Input --}}
                    <div @mousedown.stop class="grid grid-cols-4 gap-2 p-3 rounded border shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                        <div class="relative" @mousedown.stop>
                            <label class="text-[10px] font-medium uppercase">Tindakan</label>
                            <input type="text" x-model="pnSearch" @mousedown.stop @focus="pnShowDropdown = true" @input="pnShowDropdown = true" @click.outside="pnShowDropdown = false"
                                placeholder="Cari tindakan..." class="form-input text-xs w-full mt-0.5 py-1">
                            <div x-show="pnShowDropdown && filteredJnsPerawatan.length" x-cloak
                                class="absolute z-20 mt-0.5 w-full rounded shadow-lg border max-h-48 overflow-y-auto"
                                style="background-color:var(--bg-elevated);border-color:var(--border)">
                                <template x-for="item in filteredJnsPerawatan" :key="item.kd_jenis_prw">
                                    <button @mousedown.stop @click="pilihTindakan(item)"
                                        class="w-full text-left px-2 py-1.5 text-xs hover:bg-black/10 dark:hover:bg-white/10 border-b"
                                        style="border-color:var(--border)">
                                        <span x-text="item.nm_perawatan"></span>
                                        <span class="text-[10px] ml-1" style="color:var(--text-muted)" x-text="'(' + formatRupiah(Number(item.total_byrdrpr || item.total_byrdr || 0)) + ')'"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="pnShowDropdown && !filteredJnsPerawatan.length" x-cloak
                                class="absolute z-20 mt-0.5 w-full rounded shadow-lg border p-2 text-xs" style="background-color:var(--bg-elevated);border-color:var(--border);color:var(--text-muted)">
                                Tidak ditemukan
                            </div>
                            <div x-show="pnForm.nm_perawatan" class="text-[10px] mt-0.5 truncate font-medium" x-text="pnForm.nm_perawatan"></div>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium uppercase">Dokter</label>
                            <select @mousedown.stop x-model="pnForm.kd_dokter" class="form-input text-xs w-full mt-0.5 py-1">
                                <option value="">Pilih Dokter</option>
                                <template x-for="d in dokterList" :key="d.kd_dokter">
                                    <option :value="d.kd_dokter" x-text="d.nm_dokter"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium uppercase">Petugas</label>
                            <select @mousedown.stop x-model="pnForm.nip" class="form-input text-xs w-full mt-0.5 py-1">
                                <option value="">Pilih Petugas</option>
                                <template x-for="p in petugasList" :key="p.nip">
                                    <option :value="p.nip" x-text="p.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex items-end gap-1" @mousedown.stop>
                            <div class="flex-1">
                                <label class="text-[10px] font-medium uppercase">Biaya</label>
                                <div class="text-xs font-semibold mt-0.5 py-1" x-text="formatRupiah(pnForm.biaya_rawat)"></div>
                            </div>
                            <button @mousedown.stop @click="simpanPenanganan" :disabled="pnLoading || !pnForm.kd_jenis_prw || !pnForm.kd_dokter || !pnForm.nip"
                                class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
                                style="background-color:var(--accent-blue);color:#fff"
                                :style="(!pnForm.kd_jenis_prw || !pnForm.kd_dokter || !pnForm.nip) ? 'opacity:0.5' : ''"
                                x-text="pnLoading ? '...' : 'Simpan'"></button>
                        </div>
                    </div>

                    {{-- Tabel Riwayat Penanganan --}}
                    <div @mousedown.stop class="flex-1 overflow-y-auto min-h-0 rounded border" style="border-color:var(--border)">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="sticky top-0 text-[10px] uppercase" style="background-color:var(--bg-muted);color:var(--text-muted)">
                                    <th class="text-left px-2 py-1.5 font-medium">Tanggal</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Jam</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Tindakan</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Dokter</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Petugas</th>
                                    <th class="text-right px-2 py-1.5 font-medium">Biaya</th>
                                    <th class="text-center px-2 py-1.5 font-medium w-16">Status</th>
                                    <th class="text-center px-2 py-1.5 font-medium w-10">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in penangananList" :key="item.kd_jenis_prw + item.tgl_perawatan + item.jam_rawat">
                                    <tr style="color:var(--text-primary);border-color:var(--border)" class="border-t" :class="i % 2 === 0 ? '' : 'bg-black/5 dark:bg-white/5'">
                                        <td class="px-2 py-1.5" x-text="item.tgl_perawatan"></td>
                                        <td class="px-2 py-1.5" x-text="item.jam_rawat"></td>
                                        <td class="px-2 py-1.5" x-text="item.nm_perawatan || '-'"></td>
                                        <td class="px-2 py-1.5" x-text="item.nm_dokter || '-'"></td>
                                        <td class="px-2 py-1.5" x-text="item.nm_petugas || '-'"></td>
                                        <td class="px-2 py-1.5 text-right font-medium" x-text="formatRupiah(item.biaya_rawat)"></td>
                                        <td class="px-2 py-1.5 text-center">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium"
                                                :style="item.stts_bayar === 'Sudah' ? 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)' : 'background-color:rgba(239,68,68,0.1);color:rgb(239,68,68)'"
                                                x-text="item.stts_bayar"></span>
                                        </td>
                                        <td class="px-2 py-1.5 text-center">
                                            <button @mousedown.stop @click="hapusPenanganan(item)"
                                                class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                                style="color:var(--accent-red)" title="Hapus">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!penangananList.length && !pnLoading">
                                    <td colspan="8" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada penanganan</td>
                                </tr>
                                <tr x-show="pnLoading">
                                    <td colspan="8" class="text-center py-2 text-xs" style="color:var(--text-muted)">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="activeTab === 'pemeriksaan'" class="flex flex-col h-full space-y-3">
                    <div class="shrink-0">
                        <h3 class="text-sm font-bold" style="color:#16a34a">Pemeriksaan (SOAP)</h3>
                        <div class="text-xs" style="color:var(--text-muted)">
                            No.Rawat: <strong x-text="pasien?.no_rawat || '-'"></strong>
                            <span class="mx-1">|</span>Riwayat: <strong x-text="soapHistory.length + ' entry'"></strong>
                        </div>
                    </div>

                    {{-- SOAP Form --}}
                    <div @mousedown.stop class="p-3 rounded border shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                        <div class="grid grid-cols-2 gap-3">
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase" style="color:#6366f1">S - Subjektif</label>
                                <textarea x-model="soapForm.keluhan" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5" rows="3" placeholder="Keluhan pasien..."></textarea>
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase" style="color:#16a34a">O - Objektif</label>
                                <textarea x-model="soapForm.pemeriksaan" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5" rows="3" placeholder="Tanda vital, hasil pemeriksaan..."></textarea>
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase" style="color:#dc2626">A - Assessment</label>
                                <textarea x-model="soapForm.penilaian" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5" rows="3" placeholder="Diagnosis / analisa..."></textarea>
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase" style="color:#9333ea">P - Plan</label>
                                <textarea x-model="soapForm.instruksi" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5" rows="3" placeholder="Rencana tata laksana..."></textarea>
                            </div>
                        </div>

                        {{-- Vitals --}}
                        <div class="grid grid-cols-6 gap-2 mt-3">
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Tensi</label>
                                <input type="text" x-model="soapForm.tensi" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="120/80">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Suhu</label>
                                <input type="text" x-model="soapForm.suhu_tubuh" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="36.5">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Nadi</label>
                                <input type="text" x-model="soapForm.nadi" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="80">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Respirasi</label>
                                <input type="text" x-model="soapForm.respirasi" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="20">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">SpO2</label>
                                <input type="text" x-model="soapForm.spo2" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="98">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">GCS</label>
                                <input type="text" x-model="soapForm.gcs" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="15">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Kesadaran</label>
                                <select x-model="soapForm.kesadaran" @mousedown.stop class="form-input text-xs w-full mt-0.5 py-1">
                                    <option value="">Pilih</option>
                                    <option value="Compos Mentis">Compos Mentis</option>
                                    <option value="Apatis">Apatis</option>
                                    <option value="Somnolen">Somnolen</option>
                                    <option value="Sopor">Sopor</option>
                                    <option value="Koma">Koma</option>
                                    <option value="Delirium">Delirium</option>
                                </select>
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Tinggi (cm)</label>
                                <input type="text" x-model="soapForm.tinggi" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="165">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Berat (kg)</label>
                                <input type="text" x-model="soapForm.berat" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="65">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Lingkar Perut</label>
                                <input type="text" x-model="soapForm.lingkar_perut" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="80">
                            </div>
                            <div @mousedown.stop>
                                <label class="text-[10px] font-medium uppercase">Alergi</label>
                                <input type="text" x-model="soapForm.alergi" @mousedown.stop
                                    class="form-input text-xs w-full mt-0.5 py-1" placeholder="Tidak ada">
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 mt-3" @mousedown.stop>
                            <button @mousedown.stop @click="saveSoap" :disabled="soapSaving"
                                class="px-4 py-1.5 rounded text-xs font-medium transition-colors"
                                style="background-color:#16a34a;color:#fff"
                                :style="soapSaving ? 'opacity:0.5' : ''"
                                x-text="soapSaving ? 'Menyimpan...' : 'Simpan SOAP'"></button>
                            <button @mousedown.stop @click="resetSoapForm"
                                class="px-3 py-1.5 rounded text-xs font-medium"
                                style="background-color:var(--bg-elevated);color:var(--text-secondary);border:1px solid var(--border)">Reset</button>
                            <button @mousedown.stop @click="openGrafik"
                                class="px-3 py-1.5 rounded text-xs font-medium"
                                style="background-color:var(--bg-elevated);color:var(--text-secondary);border:1px solid var(--border)">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                    </svg>
                                    Grafik Vital Sign
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- SOAP History Table --}}
                    <div @mousedown.stop class="flex-1 overflow-y-auto min-h-0 rounded border" style="border-color:var(--border)">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="sticky top-0 text-[10px] uppercase" style="background-color:var(--bg-muted);color:var(--text-muted)">
                                    <th class="text-left px-2 py-1.5 font-medium">Tanggal</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Jam</th>
                                    <th class="text-left px-2 py-1.5 font-medium">S</th>
                                    <th class="text-left px-2 py-1.5 font-medium">O</th>
                                    <th class="text-left px-2 py-1.5 font-medium">A</th>
                                    <th class="text-left px-2 py-1.5 font-medium">P</th>
                                    <th class="text-center px-2 py-1.5 font-medium">Tensi</th>
                                    <th class="text-center px-2 py-1.5 font-medium">Nadi</th>
                                    <th class="text-center px-2 py-1.5 font-medium">Suhu</th>
                                    <th class="text-center px-2 py-1.5 font-medium">RR</th>
                                    <th class="text-center px-2 py-1.5 font-medium">SpO2</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, i) in soapHistory" :key="item.no_rawat + item.tgl_perawatan + item.jam_rawat">
                                    <tr style="color:var(--text-primary);border-color:var(--border)" class="border-t" :class="i % 2 === 0 ? '' : 'bg-black/5 dark:bg-white/5'">
                                        <td class="px-2 py-1.5" x-text="item.tgl_perawatan"></td>
                                        <td class="px-2 py-1.5" x-text="item.jam_rawat?.slice(0,5)"></td>
                                        <td class="px-2 py-1.5 max-w-[120px] truncate" x-text="item.keluhan || '-'" :title="item.keluhan"></td>
                                        <td class="px-2 py-1.5 max-w-[120px] truncate" x-text="item.pemeriksaan || '-'" :title="item.pemeriksaan"></td>
                                        <td class="px-2 py-1.5 max-w-[120px] truncate" x-text="item.penilaian || '-'" :title="item.penilaian"></td>
                                        <td class="px-2 py-1.5 max-w-[120px] truncate" x-text="item.instruksi || '-'" :title="item.instruksi"></td>
                                        <td class="px-2 py-1.5 text-center" x-text="item.tensi || '-'"></td>
                                        <td class="px-2 py-1.5 text-center" x-text="item.nadi || '-'"></td>
                                        <td class="px-2 py-1.5 text-center" x-text="item.suhu_tubuh || '-'"></td>
                                        <td class="px-2 py-1.5 text-center" x-text="item.respirasi || '-'"></td>
                                        <td class="px-2 py-1.5 text-center" x-text="item.spo2 || '-'"></td>
                                    </tr>
                                </template>
                                <tr x-show="!soapLoading && !soapHistory.length">
                                    <td colspan="11" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada data pemeriksaan</td>
                                </tr>
                                <tr x-show="soapLoading">
                                    <td colspan="11" class="text-center py-2 text-xs" style="color:var(--text-muted)">Memuat...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="activeTab === 'diagnosa'" class="space-y-4">
                    <h3 class="text-sm font-bold" style="color:#dc2626">Diagnosa</h3>
                    <div class="max-w-xl space-y-3">
                        <div>
                            <label class="text-xs font-medium">Diagnosa Utama</label>
                            <input type="text" class="form-input text-xs w-full mt-1" placeholder="Ketik atau cari diagnosa...">
                        </div>
                        <div>
                            <label class="text-xs font-medium">Diagnosa Sekunder</label>
                            <input type="text" class="form-input text-xs w-full mt-1" placeholder="Ketik atau cari diagnosa...">
                        </div>
                        <div>
                            <label class="text-xs font-medium">Kode ICD 10</label>
                            <input type="text" class="form-input text-xs w-full mt-1" placeholder="Cari kode ICD 10...">
                        </div>
                    </div>
                    <button class="btn btn-primary text-xs px-4 py-1.5" style="background-color:#dc2626">Simpan Diagnosa</button>
                </div>

                <div x-show="activeTab === 'catatan'" class="space-y-4">
                    <h3 class="text-sm font-bold" style="color:#9333ea">Catatan Dokter</h3>
                    <div class="max-w-xl">
                        <label class="text-xs font-medium">Catatan Perkembangan</label>
                        <textarea class="form-input text-xs w-full mt-1" rows="6" placeholder="Catatan dokter..."></textarea>
                    </div>
                    <button class="btn btn-primary text-xs px-4 py-1.5" style="background-color:#9333ea">Simpan Catatan</button>
                </div>

                {{-- Sidebar: Riwayat Pasien --}}
                <div x-show="activeSidebar === 'riwayat'" class="flex flex-col h-full space-y-3">
                    <div class="shrink-0">
                        <h3 class="text-sm font-bold" style="color:#6366f1">Riwayat Pasien</h3>
                        <div class="text-xs" style="color:var(--text-muted)">
                            <span x-text="pasien?.nm_pasien || '-'"></span>
                            <span class="mx-1">|</span>
                            <span x-text="pasien?.no_rkm_medis || '-'"></span>
                        </div>
                    </div>
                    {{-- Sub-tabs --}}
                    <div class="flex border-b shrink-0" style="border-color:var(--border)">
                        <button @mousedown.stop @click="setRiwayatTab('kunjungan')"
                            class="px-3 py-1.5 text-xs font-medium border-b-2 transition-colors"
                            :class="riwayatTab === 'kunjungan' ? '' : 'border-transparent'"
                            :style="riwayatTab === 'kunjungan' ? 'border-color:#6366f1;color:#6366f1' : 'color:var(--text-secondary)'">
                            Kunjungan
                        </button>
                        <button @mousedown.stop @click="setRiwayatTab('soap')"
                            class="px-3 py-1.5 text-xs font-medium border-b-2 transition-colors"
                            :class="riwayatTab === 'soap' ? '' : 'border-transparent'"
                            :style="riwayatTab === 'soap' ? 'border-color:#6366f1;color:#6366f1' : 'color:var(--text-secondary)'">
                            SOAP / CPPT
                        </button>
                    </div>
                    {{-- Tab: Kunjungan --}}
                    <div x-show="riwayatTab === 'kunjungan'" class="flex-1 overflow-y-auto min-h-0" @mousedown.stop>
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="sticky top-0 text-[10px] uppercase" style="background-color:var(--bg-muted);color:var(--text-muted)">
                                    <th class="text-left px-2 py-1.5 font-medium">Tanggal</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Poli</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Dokter</th>
                                    <th class="text-left px-2 py-1.5 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="k in kunjunganList" :key="k.no_rawat">
                                    <tr class="border-t" style="border-color:var(--border);color:var(--text-primary)" :class="kunjunganList.indexOf(k) % 2 === 0 ? '' : 'bg-black/5 dark:bg-white/5'">
                                        <td class="px-2 py-1.5" x-text="k.tgl_registrasi"></td>
                                        <td class="px-2 py-1.5" x-text="k.nm_poli || '-'"></td>
                                        <td class="px-2 py-1.5" x-text="k.nm_dokter || '-'"></td>
                                        <td class="px-2 py-1.5">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium"
                                                :style="k.stts_rawat === 'Sudah' ? 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)' : k.stts_rawat === 'Belum' ? 'background-color:rgba(234,179,8,0.1);color:rgb(234,179,8)' : 'background-color:rgba(59,130,246,0.1);color:rgb(59,130,246)'"
                                                x-text="k.stts_rawat || '-'"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!riwayatLoading && !kunjunganList.length">
                                    <td colspan="4" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada kunjungan</td>
                                </tr>
                                <tr x-show="riwayatLoading">
                                    <td colspan="4" class="text-center py-2 text-xs" style="color:var(--text-muted)">Memuat...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Tab: SOAP / CPPT --}}
                    <div x-show="riwayatTab === 'soap'" class="flex-1 overflow-y-auto min-h-0 space-y-2" @mousedown.stop>
                        <template x-for="s in soapList" :key="s.no_rawat + s.tgl_perawatan + s.jam_rawat">
                            <div class="p-2 rounded border text-xs" style="border-color:var(--border);background-color:var(--bg-muted)">
                                <div class="flex items-center gap-2 text-[10px] mb-1" style="color:var(--text-muted)">
                                    <span x-text="s.tgl_perawatan"></span>
                                    <span x-text="s.jam_rawat"></span>
                                    <span x-text="s.nm_dokter ? '- ' + s.nm_dokter : ''"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-1">
                                    <div x-show="s.keluhan">
                                        <span class="font-medium" style="color:#6366f1">S - </span>
                                        <span x-text="s.keluhan"></span>
                                    </div>
                                    <div x-show="s.pemeriksaan_objektif">
                                        <span class="font-medium" style="color:#16a34a">O - </span>
                                        <span x-text="s.pemeriksaan_objektif"></span>
                                    </div>
                                    <div x-show="s.penilaian">
                                        <span class="font-medium" style="color:#dc2626">A - </span>
                                        <span x-text="s.penilaian"></span>
                                    </div>
                                    <div x-show="s.instruksi">
                                        <span class="font-medium" style="color:#9333ea">P - </span>
                                        <span x-text="s.instruksi"></span>
                                    </div>
                                </div>
                                <div x-show="s.tensi || s.suhu_tubuh || s.nadi" class="mt-1 text-[10px]" style="color:var(--text-muted)">
                                    <span x-show="s.tensi" x-text="'TD: ' + s.tensi"></span>
                                    <span x-show="s.suhu_tubuh" class="ml-2" x-text="'S: ' + s.suhu_tubuh + '°C'"></span>
                                    <span x-show="s.nadi" class="ml-2" x-text="'N: ' + s.nadi"></span>
                                    <span x-show="s.respirasi" class="ml-2" x-text="'RR: ' + s.respirasi"></span>
                                    <span x-show="s.spo2" class="ml-2" x-text="'SpO2: ' + s.spo2 + '%'"></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="!riwayatLoading && !soapList.length" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada data SOAP/CPPT</div>
                        <div x-show="riwayatLoading" class="text-center py-2 text-xs" style="color:var(--text-muted)">Memuat...</div>
                    </div>
                </div>

                {{-- Sidebar placeholder for other menus --}}
                <div x-show="activeSidebar && activeSidebar !== 'riwayat' && activeTab === null" class="flex items-center justify-center h-48 text-xs" style="color:var(--text-muted)">
                    <p>Konten <strong x-text="sidebarItems.find(i => i.key === activeSidebar)?.label || activeSidebar"></strong> akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Vital Sign Modal --}}
    <div x-show="showGrafik" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @mousedown.stop class="w-[700px] max-h-[80vh] flex flex-col rounded-lg shadow-2xl" style="background-color:var(--bg-elevated)">
            <div class="flex items-center justify-between px-4 py-2 border-b shrink-0" style="border-color:var(--border)">
                <h3 class="text-sm font-bold" style="color:#16a34a">Grafik Vital Sign</h3>
                <button @mousedown.stop @click="closeGrafik" class="p-1 rounded hover:bg-black/10 dark:hover:bg-white/10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto min-h-0 p-4">
                <div x-show="grafikLoading" class="text-center py-6 text-xs" style="color:var(--text-muted)">Memuat data grafik...</div>
                <div x-show="!grafikLoading && !grafikData.length" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada data vital sign</div>
                <div x-show="!grafikLoading && grafikData.length">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-[10px] uppercase" style="color:var(--text-muted)">
                                <th class="text-left px-2 py-1 font-medium">Tanggal</th>
                                <th class="text-center px-2 py-1 font-medium">Tensi</th>
                                <th class="text-center px-2 py-1 font-medium">Nadi</th>
                                <th class="text-center px-2 py-1 font-medium">Suhu</th>
                                <th class="text-center px-2 py-1 font-medium">RR</th>
                                <th class="text-center px-2 py-1 font-medium">SpO2</th>
                                <th class="text-center px-2 py-1 font-medium">GCS</th>
                                <th class="text-center px-2 py-1 font-medium">Kesadaran</th>
                                <th class="text-center px-2 py-1 font-medium">TB</th>
                                <th class="text-center px-2 py-1 font-medium">BB</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="g in grafikData" :key="g.tgl_perawatan + g.jam_rawat">
                                <tr class="border-t" style="border-color:var(--border);color:var(--text-primary)">
                                    <td class="px-2 py-1" x-text="g.tgl_perawatan + ' ' + (g.jam_rawat?.slice(0,5) || '')"></td>
                                    <td class="px-2 py-1 text-center font-medium" x-text="g.tensi || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.nadi || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.suhu_tubuh || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.respirasi || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.spo2 || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.gcs || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.kesadaran || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.tinggi || '-'"></td>
                                    <td class="px-2 py-1 text-center" x-text="g.berat || '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
