<div x-data="{
    list: [],
    filteredList: [],
    total: 0,
    counts: { menunggu: 0, diperiksa: 0, selesai: 0 },
    tgl1: '',
    tgl2: '',
    q: '',
    filterPoli: '',
    filterDokter: '',
    filterStatus: '',
    loading: false,
    selectedRows: [],
    selectedPatient: null,
    contextPatient: null,
    msg: '',
    err: '',
    poliList: [],
    dokterList: [],
    formShow: false,
    form: { no_rkm_medis: '', nm_pasien: '', kd_poli: '', kd_dokter: '', nm_dokter: '', kd_pj: 'A09', p_jawab: '', almt_pj: '', hubunganpj: '', tgl_registrasi: new Date().toISOString().slice(0,10), jam_reg: new Date().toTimeString().slice(0,5) },
    editMode: false,
    editForm: { kd_poli: '', kd_dokter: '', nm_dokter: '', kd_pj: '', p_jawab: '', almt_pj: '', hubunganpj: '', tgl_registrasi: '', jam_reg: '' },

    init() {
        const s = window.__ralanState = window.__ralanState || {};
        this.tgl1 = s.tgl1 || new Date().toISOString().slice(0,10);
        this.tgl2 = s.tgl2 || new Date().toISOString().slice(0,10);
        this.q = s.q || '';
        this.filterPoli = s.filterPoli || '';
        this.filterDokter = s.filterDokter || '';
        this.filterStatus = s.filterStatus || '';
        this.$watch('tgl1', v => { (window.__ralanState = window.__ralanState || {}).tgl1 = v; });
        this.$watch('tgl2', v => { (window.__ralanState = window.__ralanState || {}).tgl2 = v; });
        this.$watch('q', v => { (window.__ralanState = window.__ralanState || {}).q = v || ''; });
        this.$watch('filterPoli', v => { (window.__ralanState = window.__ralanState || {}).filterPoli = v || ''; });
        this.$watch('filterDokter', v => { (window.__ralanState = window.__ralanState || {}).filterDokter = v || ''; });
        this.$watch('filterStatus', v => { (window.__ralanState = window.__ralanState || {}).filterStatus = v || ''; });
        this.loadFilters();
        this.loadData();
    },

    async loadFilters() {
        try {
            const [poliRes, dokterRes] = await Promise.all([
                this.$store.api.get('/ralan/poli-list'),
                this.$store.api.get('/ralan/dokter-list'),
            ]);
            this.poliList = poliRes || [];
            this.dokterList = dokterRes || [];
        } catch (e) {}
    },

    async loadData() {
        this.loading = true;
        try {
            const params = new URLSearchParams({ tgl1: this.tgl1, tgl2: this.tgl2 });
            if (this.q) params.set('q', this.q);
            if (this.filterPoli) params.set('poli', this.filterPoli);
            if (this.filterDokter) params.set('dokter', this.filterDokter);
            if (this.filterStatus) params.set('status', this.filterStatus);
            const res = await this.$store.api.get('/ralan/list?' + params.toString());
            this.list = res.list || [];
            this.total = res.total || 0;
            this.counts = res.counts || { menunggu: 0, diperiksa: 0, selesai: 0 };
            this.filteredList = [...this.list];
        } catch (e) { console.log(e); }
        this.loading = false;
    },

    filter() { this.loadData(); },

    formatRupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    },

    get sttsClass() {
        return {
            'Belum': 'text-yellow-600', 'Sudah': 'text-green-600', 'Batal': 'text-gray-400',
            'Dirujuk': 'text-blue-600', 'Dirawat': 'text-purple-600', 'Meninggal': 'text-red-600',
            'Pulang Paksa': 'text-orange-600'
        };
    },

    selectRow(r) {
        this.selectedPatient = this.selectedPatient?.no_rawat === r.no_rawat ? null : r;
    },

    contextHandler(e, p) {
        e.preventDefault();
        this.contextPatient = p;
        this.selectedPatient = p;
        const menu = this.$refs.contextMenu;
        menu.style.left = e.clientX + 'px';
        menu.style.top = e.clientY + 'px';
        menu.classList.remove('hidden');
    },

    closeContext() {
        const menu = this.$refs.contextMenu;
        if (menu) menu.classList.add('hidden');
        this.contextPatient = null;
    },

    openRanap() {
        const p = this.contextPatient || this.selectedPatient;
        if (!p) return;
        this.closeContext();
        this.$store.windows.open({ key: 'ranap-admission', label: 'Kamar Inap', icon: 'home', width: 800, height: 600 }, { no_rawat: p.no_rawat, pasien: p });
    },

    openRalan() {
        const p = this.contextPatient || this.selectedPatient;
        if (!p) return;
        this.closeContext();
        this.$store.windows.open({ key: 'ralan-examination', label: 'Pemeriksaan', icon: 'stethoscope', width: 1200, height: 750 }, { no_rawat: p.no_rawat, pasien: p });
    },

    openBridging(type) {
        const p = this.contextPatient || this.selectedPatient;
        if (!p) return;
        this.closeContext();
        const titles = { sep: 'SEP BPJS', 'surat-kontrol': 'Surat Kontrol', prb: 'PRB' };
        this.$store.windows.open({ key: 'bridging-' + type, label: titles[type], icon: 'file-text', width: 700, height: 500 }, { no_rawat: p.no_rawat, pasien: p });
    },

    toggleRow(no_rawat) {
        const i = this.selectedRows.indexOf(no_rawat);
        if (i > -1) this.selectedRows.splice(i, 1);
        else this.selectedRows.push(no_rawat);
    },

    toggleAll() {
        if (this.selectedRows.length === this.filteredList.length) this.selectedRows = [];
        else this.selectedRows = this.filteredList.map(r => r.no_rawat);
    },

    async deleteSelected() {
        const items = this.selectedRows.length ? this.selectedRows : (this.selectedPatient ? [this.selectedPatient.no_rawat] : []);
        if (!items.length || !confirm('Hapus ' + items.length + ' data?')) return;
        for (const nr of items) {
            try { await this.$store.api.post('/ralan/delete', { no_rawat: nr }); } catch {}
        }
        this.selectedRows = [];
        this.selectedPatient = null;
        await this.loadData();
    },

    async updateStatus(no_rawat, stts) {
        try {
            await this.$store.api.put('/ralan/status', { no_rawat, stts });
            this.msg = 'Status berhasil diupdate';
            await this.loadData();
        } catch (e) {
            this.err = e.message || 'Gagal update status';
        }
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    resetForm() {
        this.form = { no_rkm_medis: '', nm_pasien: '', kd_poli: '', kd_dokter: '', nm_dokter: '', kd_pj: 'A09', p_jawab: '', almt_pj: '', hubunganpj: '', tgl_registrasi: new Date().toISOString().slice(0,10), jam_reg: new Date().toTimeString().slice(0,5) };
        this.selectedRows = [];
        this.selectedPatient = null;
    },

    async searchPatient() {
        if (this.form.no_rkm_medis.length < 2) return;
        try {
            const res = await this.$store.api.get('/pasien/search?q=' + encodeURIComponent(this.form.no_rkm_medis));
            if (res.length) {
                this.form.no_rkm_medis = res[0].no_rkm_medis;
                this.form.nm_pasien = res[0].nm_pasien;
            }
        } catch {}
    },

    async submitForm() {
        if (!this.form.no_rkm_medis || !this.form.kd_dokter || !this.form.kd_poli) {
            this.err = 'Pasien, Poli, dan Dokter harus diisi';
            return;
        }
        this.loading = true;
        try {
            await this.$store.api.post('/ralan/register', {...this.form});
            this.msg = 'Registrasi berhasil';
            this.formShow = false;
            this.resetForm();
            await this.loadData();
        } catch (e) {
            this.err = e.message || 'Gagal registrasi';
        }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    openEdit() {
        const p = this.selectedPatient;
        if (!p) return;
        this.editForm = {
            kd_poli: p.kd_poli || '',
            kd_dokter: p.kd_dokter || '',
            nm_dokter: p.nama_dokter || '',
            kd_pj: p.kd_pj || 'A09',
            p_jawab: p.p_jawab || '',
            almt_pj: p.almt_pj || '',
            hubunganpj: p.hubunganpj || '',
            tgl_registrasi: p.tgl_registrasi || new Date().toISOString().slice(0,10),
            jam_reg: p.jam_reg || new Date().toTimeString().slice(0,5),
        };
        this.editMode = true;
    },

    cancelEdit() {
        this.editMode = false;
    },

    async saveEdit() {
        if (!this.selectedPatient) return;
        this.loading = true;
        try {
            await this.$store.api.put('/ralan/update', {
                no_rawat: this.selectedPatient.no_rawat,
                kd_poli: this.editForm.kd_poli,
                kd_dokter: this.editForm.kd_dokter,
                kd_pj: this.editForm.kd_pj,
                p_jawab: this.editForm.p_jawab,
                almt_pj: this.editForm.almt_pj,
                hubunganpj: this.editForm.hubunganpj,
                tgl_registrasi: this.editForm.tgl_registrasi,
                jam_reg: this.editForm.jam_reg,
            });
            this.msg = 'Data berhasil diupdate';
            this.editMode = false;
            this.selectedPatient = null;
            await this.loadData();
        } catch (e) {
            this.err = e.message || 'Gagal update data';
        }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    }
}" @click.away="closeContext" @keydown.escape="closeContext" class="flex flex-col h-full overflow-hidden"
    style="color:var(--text-primary);font-size:13px">

    {{-- Top Toolbar --}}
    <div @mousedown.stop class="flex items-center gap-2 px-3 py-1.5 border-b shrink-0"
        style="background-color:var(--bg-muted);border-color:var(--border)">

        <div class="flex-1"></div>

    </div>


    {{-- Filter Bar --}}
    <div @mousedown.stop class="flex items-center gap-2 px-3 py-1 border-b shrink-0 flex-wrap"
        style="background-color:var(--bg-muted);border-color:var(--border)">
        <button @mousedown.stop @click="loadData()" class="btn btn-secondary text-xs px-2 py-1">Refresh</button>
        <label class="text-xs font-medium" style="color:var(--text-muted)">Tanggal:</label>
        <input @mousedown.stop type="date" x-model="tgl1" class="form-input text-xs w-28 py-0.5">
        <span class="text-xs" style="color:var(--text-muted)">s/d</span>
        <input @mousedown.stop type="date" x-model="tgl2" class="form-input text-xs w-28 py-0.5">

        <div class="w-px h-4" style="background-color:var(--border)"></div>

        <select @mousedown.stop x-model="filterPoli" @change="filter" class="form-select text-xs w-36 py-0.5">
            <option value="">Semua Poli</option>
            <template x-for="p in poliList" :key="p.kd_poli">
                <option :value="p.kd_poli" x-text="p.nm_poli + ' (' + p.kd_poli + ')'"></option>
            </template>
        </select>

        <select @mousedown.stop x-model="filterDokter" @change="filter" class="form-select text-xs w-40 py-0.5">
            <option value="">Semua Dokter</option>
            <template x-for="d in dokterList" :key="d.kd_dokter">
                <option :value="d.kd_dokter" x-text="d.nm_dokter"></option>
            </template>
        </select>

        <select @mousedown.stop x-model="filterStatus" @change="filter" class="form-select text-xs w-32 py-0.5">
            <option value="">Semua Status</option>
            <option value="Belum">Menunggu</option>
            <option value="Sudah">Sudah</option>
            <option value="Dirujuk">Dirujuk</option>
            <option value="Dirawat">Dirawat</option>
            <option value="Batal">Batal</option>
            <option value="Meninggal">Meninggal</option>
            <option value="Pulang Paksa">Pulang Paksa</option>
        </select>

        <input @mousedown.stop type="text" x-model="q" placeholder="Cari..." class="form-input text-xs w-32 py-0.5">
        <button @mousedown.stop @click="filter()" class="btn btn-secondary text-xs px-2 py-0.5">Cari</button>
        <template x-if="msg">
            <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700" x-text="msg"></span>
        </template>
        <template x-if="err">
            <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700" x-text="err"></span>
        </template>
    </div>

    {{-- Table --}}
    <div @mousedown.stop class="flex-1 overflow-auto min-h-0">
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="sticky top-0 bg-gray-100 dark:bg-gray-800">
                    <th class="border p-1 text-left w-5"><input type="checkbox" @change="toggleAll"
                            :checked="selectedRows.length === filteredList.length && filteredList.length > 0"></th>
                    <th class="border p-1 text-left">No.Reg</th>
                    <th class="border p-1 text-left">No.Rawat</th>
                    <th class="border p-1 text-left">Tanggal</th>
                    <th class="border p-1 text-left">Pasien</th>
                    <th class="border p-1 text-left">Poli</th>
                    <th class="border p-1 text-left">Dokter</th>
                    <th class="border p-1 text-left">Biaya</th>
                    <th class="border p-1 text-left">Status</th>
                    <th class="border p-1 text-left">Cara Bayar</th>
                    <th class="border p-1 text-left">Bayar</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="r in filteredList" :key="r.no_rawat">
                    <tr @contextmenu="contextHandler($event, r)" @click="selectRow(r)" @dblclick="openRalan()"
                        :class="selectedPatient?.no_rawat === r.no_rawat ? 'bg-blue-100 dark:bg-blue-900' : ''"
                        class="border dark:hover:bg-gray-700 hover:bg-gray-50 cursor-pointer">
                        <td class="p-1"><input type="checkbox" :checked="selectedRows.includes(r.no_rawat)"
                                @change="toggleRow(r.no_rawat)" @click.stop></td>
                        <td class="p-1 font-mono" x-text="r.no_reg"></td>
                        <td class="p-1 font-mono text-[10px]" x-text="r.no_rawat"></td>
                        <td class="p-1 text-[10px]" x-text="r.tgl_registrasi"></td>
                        <td class="p-1 font-medium" x-text="r.nm_pasien"></td>
                        <td class="p-1" x-text="r.nm_poli || r.kd_poli"></td>
                        <td class="p-1" x-text="r.nama_dokter"></td>
                        <td class="p-1" x-text="formatRupiah(r.biaya_reg)"></td>
                        <td class="p-1" :class="sttsClass[r.stts]" x-text="r.stts"></td>
                        <td class="p-1 text-[10px]" x-text="r.jenis_bayar"></td>
                        <td class="p-1 text-[10px]" x-text="r.status_bayar"></td>
                    </tr>
                </template>
                <tr x-show="!filteredList.length && !loading">
                    <td colspan="11" class="p-4 text-center text-gray-500">Tidak ada data pasien rawat jalan</td>
                </tr>
                <tr x-show="loading">
                    <td colspan="11" class="p-2 text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Context Menu --}}
    <div x-ref="contextMenu" @click.away="closeContext"
        class="hidden fixed z-[9999] w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded shadow-lg text-sm">
        <div class="px-2 py-1.5 font-bold border-b bg-gray-50 dark:bg-gray-800" x-text="contextPatient?.nm_pasien">
        </div>
        <button @mousedown.stop @click="openRalan()"
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Pemeriksaan
        </button>
        <button @mousedown.stop @click="openRanap"
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Kamar Inap
        </button>
        <div class="border-t my-1"></div>
        <div class="px-2 py-1 font-medium text-gray-700 dark:text-gray-200 dark:bg-gray-800 bg-yellow-50">Status</div>
        <button @mousedown.stop @click="updateStatus(contextPatient?.no_rawat, 'Sudah')"
            class="w-full text-left px-4 py-1.5 hover:bg-green-50 text-green-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500"></span> Sudah
        </button>
        <button @mousedown.stop @click="updateStatus(contextPatient?.no_rawat, 'Belum')"
            class="w-full text-left px-4 py-1.5 hover:bg-yellow-50 text-yellow-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Belum
        </button>
        <button @mousedown.stop @click="updateStatus(contextPatient?.no_rawat, 'Dirujuk')"
            class="w-full text-left px-4 py-1.5 hover:bg-blue-50 text-blue-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Dirujuk
        </button>
        <button @mousedown.stop @click="updateStatus(contextPatient?.no_rawat, 'Dirawat')"
            class="w-full text-left px-4 py-1.5 hover:bg-purple-50 text-purple-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-purple-500"></span> Dirawat
        </button>
        <div class="border-t my-1"></div>
        <button @mousedown.stop @click="openBridging('sep')"
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            SEP BPJS
        </button>
        <button @mousedown.stop @click="openBridging('surat-kontrol')"
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Surat Kontrol
        </button>
        <button @mousedown.stop @click="openBridging('prb')"
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            PRB
        </button>
    </div>
</div>