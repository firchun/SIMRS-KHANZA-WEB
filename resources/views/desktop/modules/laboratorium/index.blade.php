<div x-data="{
    tabs: [
        { key: 'pk', label: 'PK (Patologi Klinik)' },
        { key: 'pa', label: 'PA (Patologi Anatomi)' },
        { key: 'mb', label: 'MB (Mikrobiologi)' },
    ],
    activeTab: 'pk',

    sampleForm: { tgl_sampel: '', jam_sampel: '' },
    showSampleForm: false,
    lisLoading: false,

    list: [],
    selectedPermintaan: null,
    expandedOrder: null,
    detailItems: [],
    tgl1: '',
    tgl2: '',
    q: '',
    filterStatus: '',
    loading: false,
    msg: '',
    err: '',

    init() {
        const s = window.__labState || {};
        this.tgl1 = s.tgl1 || new Date().toISOString().slice(0,10);
        this.tgl2 = s.tgl2 || new Date().toISOString().slice(0,10);
        this.filterStatus = s.filterStatus || '';
        this.$watch('tgl1', v => { (window.__labState = window.__labState || {}).tgl1 = v; });
        this.$watch('tgl2', v => { (window.__labState = window.__labState || {}).tgl2 = v; });
        this.$watch('filterStatus', v => { (window.__labState = window.__labState || {}).filterStatus = v || ''; });
        this.loadData();
    },

    async loadData() {
        this.loading = true;
        try {
            const params = new URLSearchParams({ tgl1: this.tgl1, tgl2: this.tgl2 });
            if (this.q) params.set('q', this.q);
            if (this.filterStatus) params.set('status', this.filterStatus);
            const res = await this.$store.api.get('/lab/list?' + params.toString());
            this.list = res.list || [];
        } catch (e) { this.err = e.message || 'Gagal memuat data'; }
        this.loading = false;
    },

    filter() { this.loadData(); },

    openSampleForm() {
        const now = new Date();
        this.sampleForm = {
            tgl_sampel: now.toISOString().slice(0,10),
            jam_sampel: now.toTimeString().slice(0,5),
        };
        this.showSampleForm = true;
    },

    async submitSample() {
        const p = this.selectedPermintaan;
        if (!p) return;
        this.loading = true; this.err = '';
        try {
            await this.$store.api.put('/lab/sample', {
                noorder: p.noorder,
                tgl_sampel: this.sampleForm.tgl_sampel,
                jam_sampel: this.sampleForm.jam_sampel,
            });
            this.msg = 'Sample berhasil disimpan';
            this.showSampleForm = false;
            const idx = this.list.findIndex(r => r.noorder === p.noorder);
            if (idx >= 0) {
                this.list[idx].tgl_diterima = this.sampleForm.tgl_sampel;
                this.list[idx].jam_diterima = this.sampleForm.jam_sampel;
                this.list[idx].diterima = 'Diterima';
                this.selectedPermintaan = this.list[idx];
            }
        } catch (e) { this.err = e.message || 'Gagal simpan sample'; }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    selectPermintaan(r) {
        if (this.expandedOrder === r.noorder) {
            this.expandedOrder = null;
            this.selectedPermintaan = null;
            this.detailItems = [];
            return;
        }
        this.selectedPermintaan = r;
        this.expandedOrder = r.noorder;
        this.showSampleForm = false;
        this.loadDetail(r.noorder);
    },

    async loadDetail(noorder) {
        try {
            const res = await this.$store.api.get('/lab/detail/' + encodeURIComponent(noorder));
            this.detailItems = res.items || [];
        } catch { this.detailItems = []; }
    },

    async kirimKeLis() {
        const p = this.selectedPermintaan;
        if (!p) return;
        this.lisLoading = true; this.err = '';
        try {
            await new Promise(r => setTimeout(r, 5000));
            await this.$store.api.post('/lab/kirim-lis', { noorder: p.noorder });
            this.msg = 'Berhasil dikirim ke LIS';
        } catch (e) { this.err = e.message || 'Gagal kirim ke LIS'; }
        this.lisLoading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    async tarikDariLis() {
        const p = this.selectedPermintaan;
        if (!p) return;
        this.lisLoading = true; this.err = '';
        try {
            await new Promise(r => setTimeout(r, 5000));
            await this.$store.api.post('/lab/tarik-lis', { noorder: p.noorder });
            this.msg = 'Berhasil tarik dari LIS';
            await this.loadDetail(p.noorder);
        } catch (e) { this.err = e.message || 'Gagal tarik dari LIS'; }
        this.lisLoading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    openHasilForm() {
        const p = this.selectedPermintaan;
        if (!p) return;
        this.$store.windows.open({
            key: 'laboratorium-hasil',
            label: 'Input Hasil - ' + p.noorder,
            icon: 'biotech',
            width: 900,
            height: 680,
        }, {
            noorder: p.noorder,
            no_rawat: p.no_rawat,
            nm_pasien: p.nm_pasien,
        });
    },

    formatRupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },

    statusClass(s) {
        if (s === 'selesai' || s === 'Selesai') return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
        return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300';
    },
}" class="flex flex-col h-full overflow-hidden" style="color:var(--text-primary);font-size:13px">

    {{-- Tabs --}}
    <div class="flex items-center gap-0 border-b shrink-0"
        style="border-color:var(--border);background-color:var(--bg-muted)">
        <template x-for="tab in tabs" :key="tab.key">
            <button @mousedown.stop @click="activeTab = tab.key"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
                :class="activeTab === tab.key ? 'font-semibold' : ''"
                :style="activeTab === tab.key ? 'border-color:var(--accent-blue);color:var(--accent-blue)' : 'border-color:transparent;color:var(--text-secondary)'"
                x-text="tab.label"></button>
        </template>
    </div>

    {{-- PK Tab --}}
    <template x-if="activeTab === 'pk'">
        <div class="flex flex-col h-full overflow-hidden">

            {{-- Toolbar --}}
            <div class="flex items-center gap-2 px-3 py-1.5 border-b shrink-0 flex-wrap"
                style="background-color:var(--bg-muted);border-color:var(--border)">
                <button @mousedown.stop @click="loadData()" class="btn btn-secondary text-sm px-2 py-1">Refresh</button>
                <template x-if="selectedPermintaan">
                    <div class="flex items-center gap-1">
                        <span class="w-px h-4" style="background-color:var(--border)"></span>
                        <button @mousedown.stop @click="openSampleForm" title="Sample"
                            class="p-1.5 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors"
                            style="color:var(--text-secondary)">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path
                                    d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                            </svg>
                            Sampel
                        </button>
                        <template x-if="selectedPermintaan.tgl_diterima">
                            <div class="flex items-center gap-1">
                                <button @mousedown.stop @click="openHasilForm" title="Input Hasil"
                                    class="p-1.5 rounded hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors"
                                    style="color:var(--text-secondary)">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </button>
                                <button @mousedown.stop @click="kirimKeLis" :disabled="lisLoading" title="Kirim ke LIS"
                                    class="p-1.5 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors disabled:opacity-40"
                                    style="color:var(--accent-blue)">
                                    <template x-if="lisLoading">
                                        <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </template>
                                    <template x-if="!lisLoading">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5">
                                            <path
                                                d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m-3-3v9m0 0l-3-3m3 3l3-3" />
                                        </svg>
                                    </template>
                                </button>
                                <button @mousedown.stop @click="tarikDariLis" :disabled="lisLoading"
                                    title="Tarik dari LIS"
                                    class="p-1.5 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors disabled:opacity-40"
                                    style="color:var(--text-secondary)">
                                    <template x-if="lisLoading">
                                        <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </template>
                                    <template x-if="!lisLoading">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5">
                                            <path
                                                d="M15 15.75H16.5a2.25 2.25 0 002.25-2.25v-9A2.25 2.25 0 0016.5 2.25h-9A2.25 2.25 0 005.25 4.5v9a2.25 2.25 0 002.25 2.25H9m6 3l-3 3m0 0l-3-3m3 3V9" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="msg">
                    <span class="text-sm px-2 py-0.5 rounded bg-green-100 text-green-700" x-text="msg"></span>
                </template>
                <template x-if="err">
                    <span class="text-sm px-2 py-0.5 rounded bg-red-100 text-red-700" x-text="err"></span>
                </template>
            </div>

            {{-- Filter --}}
            <div class="flex items-center gap-2 px-3 py-1 border-b shrink-0 flex-wrap"
                style="background-color:var(--bg-muted);border-color:var(--border)">
                <label class="text-sm font-medium" style="color:var(--text-muted)">Tgl Permintaan:</label>
                <input @mousedown.stop type="date" x-model="tgl1" class="form-input text-sm w-28 py-0.5">
                <span class="text-sm" style="color:var(--text-muted)">s/d</span>
                <input @mousedown.stop type="date" x-model="tgl2" class="form-input text-sm w-28 py-0.5">
                <button @mousedown.stop @click="tgl1 = new Date().toISOString().slice(0,10); tgl2 = tgl1; filter()"
                    class="btn btn-secondary text-[12px] px-1.5 py-0.5">Hari Ini</button>
                <select @mousedown.stop x-model="filterStatus" @change="filter" class="form-select text-sm w-28 py-0.5">
                    <option value="">Semua</option>
                    <option value="belum">Belum Selesai</option>
                    <option value="selesai">Selesai</option>
                </select>
                <input @mousedown.stop type="text" x-model="q" placeholder="Cari pasien/no. RM..."
                    class="form-input text-sm w-40 py-0.5">
                <button @mousedown.stop @click="filter()" class="btn btn-secondary text-sm px-2 py-0.5">Cari</button>
            </div>

            {{-- Form Sample --}}
            <template x-if="showSampleForm && selectedPermintaan">
                <div class="border-b shrink-0 px-3 py-2 space-y-2"
                    style="border-color:var(--border);background-color:rgba(14,165,233,0.06)">
                    <div class="flex items-center gap-3 text-sm mb-2">
                        <span class="font-semibold">Sample:</span>
                        <span x-text="selectedPermintaan.nm_pasien"></span>
                        <span class="text-[12px] font-mono" style="color:var(--text-muted)"
                            x-text="'(' + selectedPermintaan.noorder + ')'"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[12px] font-medium" style="color:var(--text-muted)">Tanggal
                                Sample</label>
                            <input @mousedown.stop type="date" x-model="sampleForm.tgl_sampel"
                                class="form-input text-sm w-full">
                        </div>
                        <div>
                            <label class="text-[12px] font-medium" style="color:var(--text-muted)">Jam Sample</label>
                            <input @mousedown.stop type="time" x-model="sampleForm.jam_sampel"
                                class="form-input text-sm w-full">
                        </div>
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button @mousedown.stop @click="submitSample" :disabled="loading"
                            class="btn bg-blue-600 hover:bg-blue-700 text-white text-[12px] px-3 py-1 rounded font-medium"
                            x-text="loading ? 'Menyimpan...' : 'Simpan'"></button>
                        <button @mousedown.stop @click="showSampleForm = false"
                            class="btn btn-secondary text-[12px] px-2 py-1">Batal</button>
                    </div>
                </div>
            </template>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto min-h-0">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="sticky top-0" style="background-color:var(--bg-muted)">
                            <th class="border p-1 text-left w-6"></th>
                            <th class="border p-1 text-left">No.Rawat</th>
                            <th class="border p-1 text-left">No. order</th>
                            <th class="border p-1 text-left">Permintaan</th>
                            <th class="border p-1 text-left">Sampel</th>
                            <th class="border p-1 text-left">Hasil</th>
                            <th class="border p-1 text-left">Perujuk</th>
                            <th class="border p-1 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="r in list" :key="r.noorder">

                            <tr @click="selectPermintaan(r)"
                                class="border cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                                style="vertical-align: top;"
                                :class="expandedOrder === r.noorder ? 'bg-blue-100 dark:bg-blue-900' : ''">

                                <!-- Expand icon -->
                                <td class=" border p-1 text-left w-6" x-text="expandedOrder === r.noorder ? '▼' : '▶'">
                                </td>

                                <!-- No Rawat -->
                                <td class=" border p-1 text-left">
                                    <div x-text="r.no_rawat"></div>
                                    <div class="text-[12px] text-gray-500">
                                        <span x-text="'(' + r.no_rkm_medis + ')'"></span>
                                        <span x-text="r.nm_pasien"></span>
                                    </div>
                                </td>

                                <!-- No Order -->
                                <td class=" border p-1 text-left" x-text="r.noorder"></td>



                                <!-- Tanggal -->
                                <td class=" border p-1 text-center">
                                    <div x-text="r.tgl_permintaan"></div>
                                    <div x-text="r.jam_permintaan" class="px-2 bg-gray-100 dark:bg-gray-700 rounded">
                                    </div>
                                </td>
                                <!-- Tanggal -->
                                <td class=" border p-1 text-center">
                                    <div x-text="r.tgl_sampel"></div>
                                    <div x-text="r.jam_sampel" class="px-2 bg-gray-100 dark:bg-gray-700 rounded"></div>
                                </td>
                                <!-- Tanggal -->
                                <td class=" border p-1 text-center">
                                    <div x-text="r.tgl_hasil"></div>
                                    <div x-text="r.jam_hasil" class="px-2 bg-gray-100 dark:bg-gray-700 rounded"></div>
                                </td>

                                <!-- Dokter -->
                                <td class=" border p-1 text-left" x-text="r.nm_dokter || '-'"></td>

                                <!-- Status -->
                                <td class="border p-1 text-left">
                                    <span class="text-[12px] px-2 py-1 rounded font-medium"
                                        :class="statusClass(r.status)" x-text="r.status">
                                    </span>
                                </td>


                                <!-- </tr> -->
                                <!-- <tr :style="expandedOrder === r.noorder ? '' : 'display:none'"> -->
                                <td :style="expandedOrder === r.noorder ? '' : 'display:none'" colspan="8"
                                    class="p-0 border-x border-b" style="border-color:var(--border)">
                                    <div class="px-8 py-2 space-y-2" style="background-color:var(--bg-primary)">
                                        <div class="flex items-center gap-3 text-[12px]">
                                            <span class="font-semibold" x-text="r.nm_pasien"></span>
                                            <span class="font-mono" style="color:var(--text-muted)"
                                                x-text="r.noorder + ' | ' + r.no_rkm_medis"></span>
                                        </div>
                                        <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                                            <table class="table-default text-sm w-full">
                                                <thead>
                                                    <tr>
                                                        <th class="px-2 py-1 text-[12px]">Pemeriksaan</th>
                                                        <th class="px-2 py-1 text-[12px]">Item</th>
                                                        <th class="px-2 py-1 text-[12px]">Satuan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template x-for="(d, idx) in detailItems"
                                                        :key="d.kd_jenis_prw + '_' + d.id_template">
                                                        <tr>
                                                            <td class="px-2 py-1 text-[12px] font-medium"
                                                                x-text="d.nm_perawatan || '-'"></td>
                                                            <td class="px-2 py-1 text-[12px]"
                                                                x-text="d.nm_template || '-'">
                                                            </td>
                                                            <td class="px-2 py-1 text-[12px]" x-text="d.satuan || '-'">
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <tr x-show="!detailItems.length">
                                                        <td colspan="3" class="text-center py-3 text-[12px]"
                                                            style="color:var(--text-muted)">Tidak ada item</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-[12px]" style="color:var(--text-muted)">
                                            <template x-if="r.tgl_diterima">
                                                <span>Sample diterima: <span
                                                        x-text="r.tgl_diterima + ' ' + (r.jam_diterima || '')"></span></span>
                                            </template>
                                            <template x-if="!r.tgl_diterima">
                                                <span>Sample: <span class="text-yellow-600 dark:text-yellow-400">Belum
                                                        diterima</span></span>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!list.length && !loading">
                            <td colspan="8" class="p-4 text-center" style="color:var(--text-muted)">Tidak ada permintaan
                                lab</td>
                        </tr>
                        <tr x-show="loading">
                            <td colspan="8" class="p-2 text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    {{-- PA Tab --}}
    <template x-if="activeTab === 'pa'">
        <div class="flex items-center justify-center h-full">
            <p class="text-sm" style="color:var(--text-muted)">Modul PA (Patologi Anatomi) — dalam pengembangan</p>
        </div>
    </template>

    {{-- MB Tab --}}
    <template x-if="activeTab === 'mb'">
        <div class="flex items-center justify-center h-full">
            <p class="text-sm" style="color:var(--text-muted)">Modul MB (Mikrobiologi) — dalam pengembangan</p>
        </div>
    </template>
</div>