<div x-data="{
    active: 'dashboard',
    sidebar: [
        { key: 'dashboard', label: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', dash: true },
        { key: 'industri', label: 'Industri Farmasi', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
        { key: 'jenis', label: 'Jenis Obat/BHP', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'kategori', label: 'Kategori Obat/BHP', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
        { key: 'golongan', label: 'Golongan Obat/BHP', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4' },
        { key: 'databarang', label: 'Data Obat/BHP', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
        { key: 'kodesatuan', label: 'Kode Satuan', icon: 'M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12' },
        { key: 'jasafarmasi', label: 'Jasa Farmasi', icon: 'M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4' },
    ],
    jfTab: 'rawat-jalan',

    industriList: [],
    jenisList: [],
    kategoriList: [],
    golonganList: [],
    kodesatuanList: [],
    barangList: [],
    barangTotal: 0,
    barangPage: 1,
    barangLastPage: 0,
    barangPerPage: 50,
    barangQ: '',
    loading: false,
    dashData: null,

    async fetchDashboard() {
        try { this.dashData = await this.$store.api.get('/apotek/dashboard'); } catch { this.dashData = null; }
    },

    async fetchIndustri() {
        try { this.industriList = (await this.$store.api.get('/apotek/industri')) || []; } catch { this.industriList = []; }
    },

    async fetchJenis() {
        try { this.jenisList = (await this.$store.api.get('/apotek/jenis')) || []; } catch { this.jenisList = []; }
    },

    async fetchKategori() {
        try { this.kategoriList = (await this.$store.api.get('/apotek/kategori')) || []; } catch { this.kategoriList = []; }
    },

    async fetchGolongan() {
        try { this.golonganList = (await this.$store.api.get('/apotek/golongan')) || []; } catch { this.golonganList = []; }
    },

    async fetchKodesatuan() {
        try { this.kodesatuanList = (await this.$store.api.get('/apotek/kodesatuan')) || []; } catch { this.kodesatuanList = []; }
    },

    async fetchBarang() {
        this.loading = true;
        try {
            const params = new URLSearchParams({ page: this.barangPage, perPage: this.barangPerPage });
            if (this.barangQ) params.set('q', this.barangQ);
            const res = await this.$store.api.get('/apotek/databarang?' + params.toString());
            this.barangList = res?.data || [];
            this.barangTotal = res?.total || 0;
            this.barangPage = res?.page || 1;
            this.barangLastPage = res?.lastPage || 0;
        } catch { this.barangList = []; }
        this.loading = false;
    },

    cariBarang() {
        this.barangPage = 1;
        this.fetchBarang();
    },

    init() {
        const saved = window.__apotekState;
        if (saved && saved.active) this.active = saved.active;
        this.fetchDashboard();
        this.fetchIndustri();
        this.fetchJenis();
        this.fetchKategori();
        this.fetchGolongan();
        this.fetchKodesatuan();
        if (this.active === 'databarang') this.fetchBarang();
        this.$watch('active', val => { window.__apotekState = { active: val }; });
    },

    setActive(key) {
        this.active = key;
        if (key === 'databarang' && !this.barangList.length) this.fetchBarang();
    },

    sidebarClick(e) {
        const btn = e.target.closest('[data-key]');
        if (btn) this.setActive(btn.dataset.key);
    },

    formatRupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
} " class="flex h-full gap-0"
    style="color:var(--text-primary)">

    {{-- Sidebar --}}
    <div @click.stop="sidebarClick" class="w-48 shrink-0 flex flex-col overflow-y-auto border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
        <template x-for="(item, i) in sidebar" :key="item.key">
            <div>
                <button :data-key="item.key"
                    class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-left transition-colors border-l-2 w-full cursor-pointer"
                    :class="active === item.key ? 'font-semibold' : ''"
                    :style="active === item.key ? 'background-color:var(--bg-hover);border-color:var(--accent-blue);color:var(--accent-blue)' : 'border-color:transparent;color:var(--text-secondary)'">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path :d="item.icon"/>
                    </svg>
                    <span x-text="item.label"></span>
                </button>
                <div x-show="item.dash" class="border-b mx-2 my-1" style="border-color:var(--border)"></div>
            </div>
        </template>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0" style="color:var(--text-primary)">

        {{-- DASHBOARD --}}
        <template x-if="active === 'dashboard'">
            <div class="space-y-4">
                <div>
                    <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Dashboard Farmasi</h2>
                    <p class="text-[11px] mt-0.5" style="color:var(--text-muted)">Ringkasan data obat dan BHP</p>
                </div>

                <template x-if="!dashData">
                    <div class="flex items-center justify-center py-12">
                        <div class="flex items-center gap-2 text-xs" style="color:var(--text-muted)">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Memuat data dashboard...
                        </div>
                    </div>
                </template>

                <template x-if="dashData">
                    <div class="space-y-4">

                        {{-- Stats Cards --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            <div @click="setActive('industri')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Industri Farmasi</div>
                                <div class="text-2xl font-bold" style="color:#3b82f6" x-text="dashData.counts.industri.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-blue-500">Klik untuk lihat data</span></div>
                            </div>
                            <div @click="setActive('jenis')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Jenis Obat/BHP</div>
                                <div class="text-2xl font-bold" style="color:#10b981" x-text="dashData.counts.jenis.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-emerald-500">Klik untuk lihat data</span></div>
                            </div>
                            <div @click="setActive('kategori')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(245,158,11,0.08);border-color:rgba(245,158,11,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Kategori Obat/BHP</div>
                                <div class="text-2xl font-bold" style="color:#f59e0b" x-text="dashData.counts.kategori.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-amber-500">Klik untuk lihat data</span></div>
                            </div>
                            <div @click="setActive('golongan')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(139,92,246,0.08);border-color:rgba(139,92,246,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Golongan Obat/BHP</div>
                                <div class="text-2xl font-bold" style="color:#8b5cf6" x-text="dashData.counts.golongan.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-violet-500">Klik untuk lihat data</span></div>
                            </div>
                            <div @click="setActive('kodesatuan')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(236,72,153,0.08);border-color:rgba(236,72,153,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Kode Satuan</div>
                                <div class="text-2xl font-bold" style="color:#ec4899" x-text="dashData.counts.kodesatuan.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-pink-500">Klik untuk lihat data</span></div>
                            </div>
                            <div @click="setActive('databarang')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(14,116,144,0.08);border-color:rgba(14,116,144,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Data Obat/BHP</div>
                                <div class="text-2xl font-bold" style="color:#0e7490" x-text="dashData.counts.databarang.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-cyan-600">Klik untuk lihat data</span></div>
                            </div>
                            <div @click="setActive('databarang')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Barang Habis</div>
                                <div class="text-2xl font-bold" style="color:#ef4444" x-text="dashData.counts.barang_habis.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-red-500">Klik ke Data Obat</span></div>
                            </div>
                            <div @click="setActive('databarang')" class="rounded-lg border p-3 cursor-pointer transition-transform hover:scale-[1.02]" style="background-color:rgba(249,115,22,0.08);border-color:rgba(249,115,22,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Barang Kritis</div>
                                <div class="text-2xl font-bold" style="color:#f97316" x-text="dashData.counts.barang_kritis.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)"><span class="text-orange-500">Klik ke Data Obat</span></div>
                            </div>
                        </div>

                        {{-- Obat Habis Table --}}
                        <div class="rounded-lg border p-3" style="border-color:var(--border)">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-semibold" style="color:var(--text-primary)">Obat/BHP dengan Stok 0 (Habis)</h3>
                                <span class="text-[10px] px-1.5 py-0.5 rounded font-medium bg-red-100 dark:bg-red-900/30 text-red-600" x-text="dashData.obat_habis.length + ' item'"></span>
                            </div>
                            <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                                <table class="table-default text-xs">
                                    <thead><tr><th>Kode</th><th>Nama Obat</th><th>Stok</th><th>Satuan</th></tr></thead>
                                    <tbody>
                                        <template x-for="o in dashData.obat_habis" :key="o.kode">
                                            <tr>
                                                <td class="font-mono text-[10px]" x-text="o.kode"></td>
                                                <td class="font-medium" x-text="o.nama"></td>
                                                <td class="text-right font-mono font-semibold text-red-500" x-text="o.stok"></td>
                                                <td x-text="o.satuan"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="!dashData.obat_habis.length">
                                            <td colspan="4" class="text-center py-4 text-xs" style="color:var(--text-muted)">Tidak ada obat habis</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Per-Unit Stock Monitoring --}}
                        <div>
                            <h3 class="text-xs font-semibold mb-3" style="color:var(--text-primary)">Stok Terbanyak per Unit</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <template x-for="u in dashData.stok_unit" :key="u.unit">
                                    <div class="rounded-lg border p-3" style="border-color:var(--border)">
                                        <h4 class="text-xs font-semibold mb-2" style="color:var(--text-primary)" x-text="u.unit"></h4>
                                        <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                                            <table class="table-default text-xs">
                                                <thead><tr><th>Kode</th><th>Nama Barang</th><th>Stok</th></tr></thead>
                                                <tbody>
                                                    <template x-for="item in u.items" :key="item.kode">
                                                        <tr>
                                                            <td class="font-mono text-[10px]" x-text="item.kode"></td>
                                                            <td class="font-medium" x-text="item.nama"></td>
                                                            <td class="text-right font-mono font-semibold" x-text="item.stok.toLocaleString()"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!dashData.stok_unit.length" class="col-span-full text-center py-6 text-xs" style="color:var(--text-muted)">Data stok per unit tidak tersedia</div>
                            </div>
                        </div>

                        {{-- Stok Masuk/Keluar Summary --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="rounded-lg border p-3" style="background-color:rgba(16,185,129,0.05);border-color:rgba(16,185,129,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Masuk Hari Ini</div>
                                <div class="text-xl font-bold" style="color:#10b981" x-text="dashData.stok_masuk_keluar.hari_ini.masuk.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)">Item diterima</div>
                            </div>
                            <div class="rounded-lg border p-3" style="background-color:rgba(239,68,68,0.05);border-color:rgba(239,68,68,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Keluar Hari Ini</div>
                                <div class="text-xl font-bold" style="color:#ef4444" x-text="dashData.stok_masuk_keluar.hari_ini.keluar.toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)">Item dikeluarkan</div>
                            </div>
                            <div class="rounded-lg border p-3" style="background-color:rgba(245,158,11,0.05);border-color:rgba(245,158,11,0.2)">
                                <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)">Sisa Stok</div>
                                <div class="text-xl font-bold" style="color:#f59e0b" x-text="(dashData.stok_masuk_keluar.hari_ini.masuk - dashData.stok_masuk_keluar.hari_ini.keluar).toLocaleString()"></div>
                                <div class="text-[10px] mt-1" style="color:var(--text-muted)">Net hari ini</div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </template>

        {{-- INDUSTRI FARMASI --}}
        <template x-if="active === 'industri'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Industri Farmasi</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Nama Industri</th><th>Alamat</th><th>Kota</th><th>Telpon</th><th>NPWP</th><th>Status</th></tr></thead>
                        <tbody>
                            <template x-for="d in industriList" :key="d.kode_industri">
                                <tr>
                                    <td><code class="text-[10px] px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="d.kode_industri"></code></td>
                                    <td class="font-medium" x-text="d.nama_industri"></td>
                                    <td class="text-xs" style="color:var(--text-secondary)" x-text="d.alamat || '-'"></td>
                                    <td class="text-xs" x-text="d.kota || '-'"></td>
                                    <td class="text-xs" x-text="d.telpon || '-'"></td>
                                    <td class="text-xs font-mono" x-text="d.npwp || '-'"></td>
                                    <td><span class="badge" :class="(d.status == '1' || d.status == 1) ? 'badge-success' : 'badge-danger'" x-text="(d.status == '1' || d.status == 1) ? 'Aktif' : 'Tidak Aktif'"></span></td>
                                </tr>
                            </template>
                            <tr x-show="!industriList.length">
                                <td colspan="7" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- JENIS OBAT/BHP --}}
        <template x-if="active === 'jenis'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Jenis Obat/BHP</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Nama Jenis</th></tr></thead>
                        <tbody>
                            <template x-for="d in jenisList" :key="d.kdjns">
                                <tr>
                                    <td><code class="text-[10px] px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="d.kdjns"></code></td>
                                    <td class="font-medium" x-text="d.nama"></td>
                                </tr>
                            </template>
                            <tr x-show="!jenisList.length">
                                <td colspan="2" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- KATEGORI OBAT/BHP --}}
        <template x-if="active === 'kategori'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Kategori Obat/BHP</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Nama Kategori</th></tr></thead>
                        <tbody>
                            <template x-for="d in kategoriList" :key="d.kode">
                                <tr>
                                    <td><code class="text-[10px] px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="d.kode"></code></td>
                                    <td class="font-medium" x-text="d.nama"></td>
                                </tr>
                            </template>
                            <tr x-show="!kategoriList.length">
                                <td colspan="2" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- GOLONGAN OBAT/BHP --}}
        <template x-if="active === 'golongan'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Golongan Obat/BHP</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Nama Golongan</th></tr></thead>
                        <tbody>
                            <template x-for="d in golonganList" :key="d.kode">
                                <tr>
                                    <td><code class="text-[10px] px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="d.kode"></code></td>
                                    <td class="font-medium" x-text="d.nama"></td>
                                </tr>
                            </template>
                            <tr x-show="!golonganList.length">
                                <td colspan="2" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- DATA OBAT/BHP --}}
        <template x-if="active === 'databarang'">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Data Obat/BHP</h3>
                    <span class="text-xs" style="color:var(--text-muted)" x-text="barangTotal + ' item'"></span>
                </div>
                <div class="flex items-center gap-2" @mousedown.stop>
                    <input type="text" x-model="barangQ" placeholder="Cari nama/kode barang..."
                        class="form-input text-xs w-64 py-1" @keydown.enter="cariBarang">
                    <button @mousedown.stop @click="cariBarang" class="btn btn-secondary text-xs px-2 py-1">Cari</button>
                    <select x-model="barangPerPage" @change="fetchBarang" class="form-select text-xs w-20 py-1">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default text-xs">
                        <thead><tr><th>Kode</th><th>Nama Barang</th><th>Satuan</th><th>Jenis</th><th>Kategori</th><th>Golongan</th><th>Harga Beli</th><th>Harga Jual</th><th>Stok</th><th>Stok Min</th><th>Status</th></tr></thead>
                        <tbody>
                            <template x-for="d in barangList" :key="d.kode_brng">
                                <tr>
                                    <td class="font-mono text-[10px]" x-text="d.kode_brng"></td>
                                    <td class="font-medium" x-text="d.nama_brng"></td>
                                    <td class="text-[10px]" x-text="d.satuan || '-'"></td>
                                    <td class="text-[10px]" x-text="d.jenis_nama || '-'"></td>
                                    <td class="text-[10px]" x-text="d.kategori_nama || '-'"></td>
                                    <td class="text-[10px]" x-text="d.golongan_nama || '-'"></td>
                                    <td class="text-right font-mono text-[10px]" x-text="formatRupiah(d.harga_beli)"></td>
                                    <td class="text-right font-mono text-[10px]" x-text="formatRupiah(d.harga_jual)"></td>
                                    <td class="text-right font-mono text-[10px]" x-text="d.stok" :class="d.stok <= d.stok_minimal ? 'text-red-500 font-semibold' : ''"></td>
                                    <td class="text-right font-mono text-[10px]" x-text="d.stok_minimal || 0"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="d.status == '1' || d.status == 1 ? 'badge-success' : 'badge-danger'" x-text="d.status == '1' || d.status == 1 ? 'Aktif' : 'Tidak Aktif'"></span></td>
                                </tr>
                            </template>
                            <tr x-show="!barangList.length && !loading">
                                <td colspan="11" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data</td>
                            </tr>
                            <tr x-show="loading">
                                <td colspan="11" class="text-center py-2 text-xs" style="color:var(--text-muted)">Memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div x-show="barangLastPage > 1" class="flex items-center justify-between" @mousedown.stop>
                    <span class="text-xs" style="color:var(--text-muted)" x-text="'Halaman ' + barangPage + ' dari ' + barangLastPage"></span>
                    <div class="flex gap-1">
                        <button @mousedown.stop @click="barangPage = Math.max(1, barangPage - 1); fetchBarang()" :disabled="barangPage <= 1"
                            class="px-2 py-1 text-xs rounded border" style="border-color:var(--border)" :style="barangPage <= 1 ? 'opacity:0.4' : ''">Sebelumnya</button>
                        <button @mousedown.stop @click="barangPage = Math.min(barangLastPage, barangPage + 1); fetchBarang()" :disabled="barangPage >= barangLastPage"
                            class="px-2 py-1 text-xs rounded border" style="border-color:var(--border)" :style="barangPage >= barangLastPage ? 'opacity:0.4' : ''">Selanjutnya</button>
                    </div>
                </div>
            </div>
        </template>

        {{-- KODE SATUAN --}}
        <template x-if="active === 'kodesatuan'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Kode Satuan</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Satuan</th></tr></thead>
                        <tbody>
                            <template x-for="d in kodesatuanList" :key="d.kode_sat">
                                <tr>
                                    <td><code class="text-[10px] px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="d.kode_sat"></code></td>
                                    <td class="font-medium" x-text="d.satuan"></td>
                                </tr>
                            </template>
                            <tr x-show="!kodesatuanList.length">
                                <td colspan="2" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- JASA FARMASI --}}
        <template x-if="active === 'jasafarmasi'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Jasa Farmasi</h3>
                <div class="flex items-center gap-1 border-b pb-1" style="border-color:var(--border)">
                    <button @click="jfTab = 'rawat-jalan'"
                        class="px-3 py-1.5 text-xs rounded-t font-medium transition-colors cursor-pointer"
                        :style="jfTab === 'rawat-jalan' ? 'background-color:var(--accent-blue);color:white' : 'color:var(--text-secondary);hover:background-color:var(--bg-hover)'">
                        Rawat Jalan
                    </button>
                    <button @click="jfTab = 'rawat-inap'"
                        class="px-3 py-1.5 text-xs rounded-t font-medium transition-colors cursor-pointer"
                        :style="jfTab === 'rawat-inap' ? 'background-color:var(--accent-blue);color:white' : 'color:var(--text-secondary);hover:background-color:var(--bg-hover)'">
                        Rawat Inap
                    </button>
                </div>

                <template x-if="jfTab === 'rawat-jalan'">
                    <div class="flex items-center justify-center py-16 text-xs" style="color:var(--text-muted)">
                        Data Rawat Jalan belum tersedia
                    </div>
                </template>

                <template x-if="jfTab === 'rawat-inap'">
                    <div class="flex items-center justify-center py-16 text-xs" style="color:var(--text-muted)">
                        Data Rawat Inap belum tersedia
                    </div>
                </template>
            </div>
        </template>

    </div>
</div>
