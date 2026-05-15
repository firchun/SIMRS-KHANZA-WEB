<div x-data="{
    activeSidebar: 'dashboard-pengiriman',
    sidebarQ: '',
    loading: false,
    sidebarItems: [
        { key: 'dashboard-pengiriman', label: 'Dashboard Pengiriman', icon: 'M3 3v18h18V3H3zm4 14V7m4 10v-7m4 7v-4m4 4V3', dash: true },
        { key: 'data-sep', label: 'Data Sep', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'data-antrian', label: 'Data Antrian', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
        { key: 'cek-nik', label: 'Cek NIK', icon: 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z' },
        { key: 'cek-peserta', label: 'Cek Peserta', icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0' },
        { key: 'kamar-applicare', label: 'Kamar Applicare', icon: 'M3.75 6h16.5M3.75 12h16.5M3.75 18h16.5' },
        { key: 'surat-kontrol', label: 'Surat Kontrol', icon: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10' },
        { key: 'program-prb', label: 'Program PRB', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'jadwal-hfis', label: 'Jadwal HFIS', icon: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5' },
        { key: 'icare', label: 'Icare', icon: 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75' },
        { key: 'pcare', label: 'Pcare', icon: 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342' },
    ],

    // Data states
    dashboardData: null,
    pesertaResult: null,
    sepResult: null,
    antreanData: null,
    kamarData: null,
    suratKontrolData: null,
    prbData: null,
    jadwalPoli: [],
    jadwalDokter: [],
    icareResult: null,

    // Form inputs
    searchNomor: '',
    searchTgl: new Date().toISOString().slice(0,10),
    sepNomor: '',
    antreanTgl: new Date().toISOString().slice(0,10),
    sepForm: { no_kartu: '', tgl_sep: new Date().toISOString().slice(0,10), jns_pelayanan: '1', kd_ppk: '', kddiagnosa: '', no_rujukan: '', kd_poli: '', nm_poli: '', kd_dpjp: '', nm_dpjp: '' },
    suratKontrolForm: { nomor_sep: '', tgl_rencana: '', kd_poli: '', kd_dokter: '' },
    prbForm: { nomor_sep: '', kd_diagnosa: '', kd_obat: '' },
    icareForm: { nomor_kartu: '', kode_dokter: '' },
    pcareForm: { nomor: '', tgl: new Date().toISOString().slice(0,10) },

    tgl1: new Date().toISOString().slice(0,10),
    tgl2: new Date().toISOString().slice(0,10),

    // MJKN API URL tester
    miknUrl: localStorage.getItem('mikn_api_url') || '',
    miknStatus: null,
    miknResponse: '',

    get filteredSidebar() {
        if (!this.sidebarQ) return this.sidebarItems;
        const q = this.sidebarQ.toLowerCase();
        return this.sidebarItems.filter(i =>
            i.key.toLowerCase().includes(q) ||
            i.label.toLowerCase().includes(q)
        );
    },

    setSidebar(key) {
        this.activeSidebar = key;
    },

    get selectedLabel() {
        return this.sidebarItems.find(i => i.key === this.activeSidebar)?.label || this.activeSidebar;
    },

    init() {
        this.fetchDashboard();
    },

    async fetchDashboard() {
        this.loading = true;
        try {
            this.dashboardData = await this.$store.api.get('/bpjs/dashboard?tgl1=' + this.tgl1 + '&tgl2=' + this.tgl2);
        } catch {}
        this.loading = false;
    },

    async cariPeserta() {
        this.loading = true;
        try {
            this.pesertaResult = await this.$store.api.get('/bpjs/peserta?nomor=' + encodeURIComponent(this.searchNomor) + '&tanggal=' + this.searchTgl);
        } catch (e) { this.pesertaResult = { metaData: { code: '500', message: e.message } }; }
        this.loading = false;
    },

    async cariSep() {
        this.loading = true;
        try {
            this.sepResult = await this.$store.api.get('/bpjs/sep/' + this.sepNomor);
        } catch (e) { this.sepResult = { metaData: { code: '500', message: e.message } }; }
        this.loading = false;
    },

    async fetchAntrean() {
        this.loading = true;
        try {
            this.antreanData = await this.$store.api.get('/bpjs/antrean/tanggal/' + this.antreanTgl);
        } catch (e) { this.antreanData = null; }
        this.loading = false;
    },

    async fetchKamar() {
        this.loading = true;
        try {
            this.kamarData = await this.$store.api.get('/bpjs/kamar-applicare?start=0&limit=50');
        } catch (e) { this.kamarData = null; }
        this.loading = false;
    },

    async fetchJadwal() {
        this.loading = true;
        try {
            const [poli, dokter] = await Promise.all([
                this.$store.api.get('/bpjs/jadwal-hfis/poli'),
                this.$store.api.get('/bpjs/jadwal-hfis/dokter'),
            ]);
            this.jadwalPoli = poli?.response || [];
            this.jadwalDokter = dokter?.response || [];
        } catch {}
        this.loading = false;
    },

    async testMiknUrl() {
        if (!this.miknUrl) return;
        this.miknStatus = 'loading';
        this.miknResponse = '';
        try {
            localStorage.setItem('mikn_api_url', this.miknUrl);
            const res = await fetch(this.miknUrl, { method: 'GET', mode: 'cors', cache: 'no-store' });
            const text = await res.text();
            if (text.includes('Selamat Datang di Web Service Antrean BPJS Mobile JKN FKTL')) {
                this.miknStatus = 'online';
                this.miknResponse = text;
            } else {
                this.miknStatus = 'offline';
                this.miknResponse = text || '(response kosong)';
            }
        } catch (e) {
            this.miknStatus = 'offline';
            this.miknResponse = e.message;
        }
    },
}"
    class="flex h-full" style="color:var(--text-primary)">

    {{-- Sidebar --}}
    <div class="w-56 shrink-0 flex flex-col border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="px-4 py-3 border-b shrink-0" style="border-color:var(--border)">
            <div class="text-sm font-semibold">Bridging BPJS</div>
            <div class="text-[10px]" style="color:var(--text-muted)">Integrasi BPJS Kesehatan</div>
        </div>
        <div class="px-2 py-1.5 border-b shrink-0" style="border-color:var(--border)">
            <input type="text" x-model="sidebarQ" placeholder="Cari menu..." class="form-input text-[11px] py-1 w-full">
        </div>
        <div class="flex-1 overflow-y-auto min-h-0 py-1">
            <template x-for="(item, i) in filteredSidebar" :key="item.key">
                <div>
                    <button @click="setSidebar(item.key)"
                        class="w-full text-left px-3 py-1.5 text-xs flex items-center gap-2 hover:bg-black/5 dark:hover:bg-white/5 transition-colors rounded mx-1"
                        :class="activeSidebar === item.key ? 'bg-blue-50 dark:bg-blue-900/20 font-semibold' : ''"
                        style="color:var(--text-primary)">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path :d="item.icon"/>
                        </svg>
                        <span x-text="item.label"></span>
                    </button>
                    <div x-show="item.dash" class="border-b mx-3 my-1.5" style="border-color:var(--border)"></div>
                </div>
            </template>
            <div x-show="!filteredSidebar.length" class="px-3 py-4 text-xs text-center" style="color:var(--text-muted)">Menu tidak ditemukan</div>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="flex items-center gap-2 px-4 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
            <svg class="w-4 h-4 shrink-0" style="color:var(--accent-cyan)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
            <span class="text-xs font-medium" x-text="selectedLabel"></span>
            <span class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:rgba(6,182,212,0.1);color:rgb(6,182,212)">BPJS</span>
            <div class="flex-1"></div>
            <template x-if="loading">
                <svg class="w-4 h-4 animate-spin" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </template>
        </div>

        <div class="flex-1 overflow-y-auto min-h-0 p-4" style="background-color:var(--bg-elevated)">

            {{-- ==================== DASHBOARD ==================== --}}
            <template x-if="activeSidebar === 'dashboard-pengiriman'">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Dashboard BPJS</h2>
                            <p class="text-[11px] mt-0.5" style="color:var(--text-muted)">Ringkasan bridging BPJS Kesehatan</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5">
                                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Dari</label>
                                <input type="date" x-model="tgl1" class="form-input text-[11px] py-1 w-32">
                                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Sampai</label>
                                <input type="date" x-model="tgl2" class="form-input text-[11px] py-1 w-32">
                            </div>
                            <button @click="fetchDashboard" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div class="rounded-lg border p-3 mb-4" style="border-color:var(--border);background-color:var(--bg-muted)">
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0" style="color:var(--accent-cyan)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>
                                </svg>
                                <span class="text-xs font-medium" style="color:var(--text-primary)">URL API MJKN</span>
                            </div>
                            <input type="url" x-model="miknUrl" placeholder="http://ipserverws:port/api-bpjsfktl" class="form-input text-[11px] py-1 flex-1 min-w-[200px]">
                            <button @click="testMiknUrl" :disabled="miknStatus === 'loading'"
                                class="px-3 py-1.5 text-xs rounded font-medium text-white flex items-center gap-1.5"
                                :class="miknStatus === 'loading' ? 'bg-gray-400 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700'">
                                <template x-if="miknStatus === 'loading'">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </template>
                                <template x-if="miknStatus !== 'loading'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </template>
                                Test Koneksi
                            </button>
                            <template x-if="miknStatus">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="w-2 h-2 rounded-full inline-block"
                                        :class="miknStatus === 'online' ? 'bg-green-500' : 'bg-red-500'"></span>
                                    <span class="font-medium"
                                        :class="miknStatus === 'online' ? 'text-green-600' : 'text-red-500'"
                                        x-text="miknStatus === 'online' ? 'Online' : 'Offline'"></span>
                                </div>
                            </template>
                        </div>
                        <template x-if="miknResponse">
                            <div class="mt-2 text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded p-2 max-h-20 overflow-y-auto"
                                :class="miknStatus === 'online' ? 'text-green-700 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                x-text="miknResponse"></div>
                        </template>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
                        <div class="rounded-lg border p-3" style="background-color:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Antrian Dilayani</div>
                            <div class="text-2xl font-bold" style="color:#3b82f6" x-text="dashboardData?.antrian_dilayani?.toLocaleString() || '-'"></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)" x-text="dashboardData?.tgl1 + ' s/d ' + dashboardData?.tgl2"></div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(245,158,11,0.08);border-color:rgba(245,158,11,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Waktu Tunggu</div>
                            <div class="text-2xl font-bold" style="color:#f59e0b"><span x-text="dashboardData?.waktu_tunggu || '-'"></span> <span class="text-sm font-normal">menit</span></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)">Rata-rata (estimasi)</div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">SEP Ranap</div>
                            <div class="text-2xl font-bold" style="color:#10b981" x-text="dashboardData?.sep_ranap?.toLocaleString() || '-'"></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)">Bulan ini</div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(139,92,246,0.08);border-color:rgba(139,92,246,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">SEP Rajal</div>
                            <div class="text-2xl font-bold" style="color:#8b5cf6" x-text="dashboardData?.sep_rajal?.toLocaleString() || '-'"></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)">Bulan ini</div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Status API MJKN</div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-2 h-2 rounded-full inline-block" :class="dashboardData?.status_api_mjkn?.status === 'Online' ? 'bg-green-500' : 'bg-red-500'"></span>
                                <span class="text-sm font-bold" style="color:#22c55e" x-text="dashboardData?.status_api_mjkn?.status || '-'"></span>
                            </div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)" x-text="'Response: ' + (dashboardData?.status_api_mjkn?.latency || '-')"></div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(236,72,153,0.08);border-color:rgba(236,72,153,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Koneksi BPJS</div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-2 h-2 rounded-full inline-block" :class="dashboardData?.koneksi_bpjs?.status === 'Terhubung' ? 'bg-green-500' : 'bg-red-500'"></span>
                                <span class="text-sm font-bold" style="color:#22c55e" x-text="dashboardData?.koneksi_bpjs?.status || '-'"></span>
                            </div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)" x-text="'Latency: ' + (dashboardData?.koneksi_bpjs?.latency || '-')"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="rounded-lg border p-3" style="background-color:rgba(14,116,144,0.08);border-color:rgba(14,116,144,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Total Bed Aktif</div>
                            <div class="text-2xl font-bold" style="color:#0e7490" x-text="dashboardData?.total_bed?.toLocaleString() || '-'"></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)">Seluruh ruang rawat</div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(217,119,6,0.08);border-color:rgba(217,119,6,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Bed Terisi</div>
                            <div class="text-2xl font-bold" style="color:#d97706" x-text="dashboardData?.bed_terisi?.toLocaleString() || '-'"></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)" x-text="(dashboardData?.total_bed ? ((dashboardData?.bed_terisi / dashboardData?.total_bed) * 100).toFixed(1) : '-') + '% BOR'"></div>
                        </div>
                        <div class="rounded-lg border p-3" style="background-color:rgba(124,58,237,0.08);border-color:rgba(124,58,237,0.2)">
                            <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Mutasi Kamar</div>
                            <div class="text-2xl font-bold" style="color:#7c3aed" x-text="dashboardData?.mutasi_kamar?.toLocaleString() || '-'"></div>
                            <div class="text-[10px] mt-1" style="color:var(--text-muted)" x-text="dashboardData?.tgl1 + ' s/d ' + dashboardData?.tgl2"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="rounded-lg border p-3" style="border-color:var(--border)">
                            <h3 class="text-xs font-semibold mb-3" style="color:var(--text-primary)">Ringkasan SEP per Jenis</h3>
                            <div class="space-y-2">
                                <template x-for="item in (dashboardData?.ringkasan_sep || [])" :key="item.label">
                                    <div class="flex items-center justify-between text-xs">
                                        <span style="color:var(--text-secondary)" x-text="item.label"></span>
                                        <span class="font-semibold" x-text="item.count?.toLocaleString()"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="rounded-lg border p-3" style="border-color:var(--border)">
                            <h3 class="text-xs font-semibold mb-3" style="color:var(--text-primary)">Status Layanan</h3>
                            <div class="space-y-2.5">
                                <template x-for="item in (dashboardData?.status_layanan || [])" :key="item.name">
                                    <div class="flex items-center justify-between text-xs">
                                        <span style="color:var(--text-secondary)" x-text="item.name"></span>
                                        <span class="flex items-center gap-2">
                                            <span class="text-[10px]" style="color:var(--text-muted)" x-text="item.latency"></span>
                                            <span class="w-1.5 h-1.5 rounded-full inline-block" :class="item.status === 'Online' ? 'bg-green-500' : 'bg-red-500'"></span>
                                            <span :class="item.status === 'Online' ? 'text-green-600' : 'text-red-500'" x-text="item.status"></span>
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ==================== DATA SEP ==================== --}}
            <template x-if="activeSidebar === 'data-sep'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Data SEP</h2>
                    <div class="flex gap-2">
                        <input type="text" x-model="sepNomor" placeholder="Nomor SEP" class="form-input text-xs py-1.5 w-64">
                        <button @click="cariSep" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Cari</button>
                    </div>
                    <template x-if="sepResult">
                        <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(sepResult, null, 2)"></pre>
                    </template>
                    <div class="border-t pt-4" style="border-color:var(--border)">
                        <h3 class="text-xs font-semibold mb-3" style="color:var(--text-primary)">Buat SEP Baru</h3>
                        <div class="grid grid-cols-2 gap-3 max-w-xl">
                            <template x-for="(val, key) in sepForm" :key="key">
                                <div>
                                    <label class="text-[10px] font-medium mb-1 block" style="color:var(--text-muted)" x-text="key.replace(/_/g,' ')"></label>
                                    <input type="text" x-model="sepForm[key]" class="form-input text-[11px] py-1 w-full">
                                </div>
                            </template>
                        </div>
                        <button @click="$store.api.post('/bpjs/sep/insert', sepForm).then(r => sepResult = r).catch(e => sepResult = {error: e.message})" class="mt-3 px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Simpan SEP</button>
                    </div>
                </div>
            </template>

            {{-- ==================== DATA ANTRIAN ==================== --}}
            <template x-if="activeSidebar === 'data-antrian'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Data Antrian</h2>
                    <div class="flex gap-2 items-center">
                        <input type="date" x-model="antreanTgl" class="form-input text-xs py-1 w-40">
                        <button @click="fetchAntrean" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Tampilkan</button>
                    </div>
                    <template x-if="antreanData">
                        <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(antreanData, null, 2)"></pre>
                    </template>
                </div>
            </template>

            {{-- ==================== CEK NIK ==================== --}}
            <template x-if="activeSidebar === 'cek-nik'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Cek NIK</h2>
                    <p class="text-[11px]" style="color:var(--text-muted)">Cari data peserta BPJS berdasarkan NIK</p>
                    <div class="flex gap-2 items-center max-w-md">
                        <input type="text" x-model="searchNomor" placeholder="16 digit NIK" maxlength="16" class="form-input text-xs py-1.5 w-64">
                        <input type="date" x-model="searchTgl" class="form-input text-xs py-1.5 w-36">
                        <button @click="cariPeserta" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Cari</button>
                    </div>
                    <template x-if="pesertaResult">
                        <div class="rounded-lg border p-3 max-w-xl" style="border-color:var(--border)">
                            <div class="text-[10px] font-medium mb-2" style="color:var(--text-muted)" x-text="'Kode: ' + (pesertaResult.metaData?.code || '-') + ' | ' + (pesertaResult.metaData?.message || '-')"></div>
                            <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(pesertaResult.response || pesertaResult, null, 2)"></pre>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ==================== CEK PESERTA ==================== --}}
            <template x-if="activeSidebar === 'cek-peserta'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Cek Peserta</h2>
                    <p class="text-[11px]" style="color:var(--text-muted)">Cari data peserta BPJS berdasarkan Nomor Kartu</p>
                    <div class="flex gap-2 items-center max-w-md">
                        <input type="text" x-model="searchNomor" placeholder="Nomor Kartu BPJS" class="form-input text-xs py-1.5 w-64">
                        <input type="date" x-model="searchTgl" class="form-input text-xs py-1.5 w-36">
                        <button @click="cariPeserta" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Cari</button>
                    </div>
                    <template x-if="pesertaResult">
                        <div class="rounded-lg border p-3 max-w-xl" style="border-color:var(--border)">
                            <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(pesertaResult.response || pesertaResult, null, 2)"></pre>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ==================== KAMAR APPLICARE ==================== --}}
            <template x-if="activeSidebar === 'kamar-applicare'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Kamar Applicare</h2>
                    <button @click="fetchKamar" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Muat Data Kamar</button>
                    <template x-if="kamarData">
                        <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(kamarData, null, 2)"></pre>
                    </template>
                </div>
            </template>

            {{-- ==================== SURAT KONTROL ==================== --}}
            <template x-if="activeSidebar === 'surat-kontrol'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Surat Kontrol</h2>
                    <div class="grid grid-cols-2 gap-3 max-w-lg">
                        <template x-for="(val, key) in suratKontrolForm" :key="key">
                            <div>
                                <label class="text-[10px] font-medium mb-1 block" style="color:var(--text-muted)" x-text="key.replace(/_/g,' ')"></label>
                                <input type="text" x-model="suratKontrolForm[key]" class="form-input text-[11px] py-1 w-full">
                            </div>
                        </template>
                    </div>
                    <button @click="$store.api.post('/bpjs/surat-kontrol/insert', suratKontrolForm).then(r => suratKontrolData = r).catch(e => suratKontrolData = {error: e.message})" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Simpan Surat Kontrol</button>
                    <template x-if="suratKontrolData">
                        <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(suratKontrolData, null, 2)"></pre>
                    </template>
                </div>
            </template>

            {{-- ==================== PROGRAM PRB ==================== --}}
            <template x-if="activeSidebar === 'program-prb'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Program PRB</h2>
                    <p class="text-[11px]" style="color:var(--text-muted)">Program Rujuk Balik (PRB)</p>
                    <div class="grid grid-cols-2 gap-3 max-w-lg">
                        <template x-for="(val, key) in prbForm" :key="key">
                            <div>
                                <label class="text-[10px] font-medium mb-1 block" style="color:var(--text-muted)" x-text="key.replace(/_/g,' ')"></label>
                                <input type="text" x-model="prbForm[key]" class="form-input text-[11px] py-1 w-full">
                            </div>
                        </template>
                    </div>
                    <button @click="$store.api.post('/bpjs/prb/insert', prbForm).then(r => prbData = r).catch(e => prbData = {error: e.message})" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Simpan PRB</button>
                    <template x-if="prbData">
                        <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(prbData, null, 2)"></pre>
                    </template>
                </div>
            </template>

            {{-- ==================== JADWAL HFIS ==================== --}}
            <template x-if="activeSidebar === 'jadwal-hfis'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Jadwal HFIS</h2>
                    <button @click="fetchJadwal" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Muat Jadwal</button>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-xs font-medium mb-2" style="color:var(--text-muted)">Poli</h3>
                            <div class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 h-48 overflow-y-auto">
                                <template x-for="(item, i) in jadwalPoli" :key="i">
                                    <div class="py-0.5" x-text="item.kode + ' - ' + item.nama"></div>
                                </template>
                                <div x-show="!jadwalPoli.length" style="color:var(--text-muted)">Belum ada data</div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xs font-medium mb-2" style="color:var(--text-muted)">Dokter</h3>
                            <div class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 h-48 overflow-y-auto">
                                <template x-for="(item, i) in jadwalDokter" :key="i">
                                    <div class="py-0.5" x-text="item.kode + ' - ' + item.nama"></div>
                                </template>
                                <div x-show="!jadwalDokter.length" style="color:var(--text-muted)">Belum ada data</div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ==================== ICARE ==================== --}}
            <template x-if="activeSidebar === 'icare'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Icare</h2>
                    <p class="text-[11px]" style="color:var(--text-muted)">Validasi pasien i-Care</p>
                    <div class="grid grid-cols-2 gap-3 max-w-lg">
                        <div>
                            <label class="text-[10px] font-medium mb-1 block" style="color:var(--text-muted)">Nomor Kartu / NIK</label>
                            <input type="text" x-model="icareForm.nomor_kartu" class="form-input text-[11px] py-1 w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium mb-1 block" style="color:var(--text-muted)">Kode Dokter</label>
                            <input type="text" x-model="icareForm.kode_dokter" class="form-input text-[11px] py-1 w-full">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="$store.api.post('/bpjs/icare/fkrtl', { param: icareForm.nomor_kartu, kodedokter: icareForm.kode_dokter }).then(r => icareResult = r).catch(e => icareResult = {error: e.message})" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-cyan-600 hover:bg-cyan-700">Cek FKRTL</button>
                        <button @click="$store.api.post('/bpjs/icare/fktp', { param: icareForm.nomor_kartu }).then(r => icareResult = r).catch(e => icareResult = {error: e.message})" class="px-3 py-1.5 text-xs rounded font-medium text-white bg-emerald-600 hover:bg-emerald-700">Cek FKTP</button>
                    </div>
                    <template x-if="icareResult">
                        <pre class="text-[11px] font-mono bg-black/5 dark:bg-white/5 rounded-lg p-3 overflow-x-auto" x-text="JSON.stringify(icareResult, null, 2)"></pre>
                    </template>
                </div>
            </template>

            {{-- ==================== PCARE ==================== --}}
            <template x-if="activeSidebar === 'pcare'">
                <div class="space-y-4">
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Pcare</h2>
                    <p class="text-[11px]" style="color:var(--text-muted)">Modul PCare (dalam pengembangan)</p>
                    <div class="flex gap-2 items-center max-w-md">
                        <input type="text" x-model="pcareForm.nomor" placeholder="Nomor Peserta" class="form-input text-xs py-1.5 w-64">
                        <input type="date" x-model="pcareForm.tgl" class="form-input text-xs py-1.5 w-36">
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>