<div x-data="{
    active: 'industri',
    sidebar: [
        { key: 'industri', label: 'Industri Farmasi', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
        { key: 'jenis', label: 'Jenis Obat/BHP', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'kategori', label: 'Kategori Obat/BHP', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
        { key: 'golongan', label: 'Golongan Obat/BHP', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4' },
        { key: 'databarang', label: 'Data Obat/BHP', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
        { key: 'kodesatuan', label: 'Kode Satuan', icon: 'M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12' },
    ],

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
        <template x-for="item in sidebar" :key="item.key">
            <button :data-key="item.key"
                class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-left transition-colors border-l-2 cursor-pointer"
                :class="active === item.key ? 'font-semibold' : ''"
                :style="active === item.key ? 'background-color:var(--bg-hover);border-color:var(--accent-blue);color:var(--accent-blue)' : 'border-color:transparent;color:var(--text-secondary)'">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path :d="item.icon"/>
                </svg>
                <span x-text="item.label"></span>
            </button>
        </template>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0" style="color:var(--text-primary)">

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

    </div>
</div>
