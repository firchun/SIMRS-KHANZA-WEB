<div x-data="{
    list: [],
    total: 0,
    counts: { total: 0, belum: 0, sudah: 0 },
    tgl1: '',
    tgl2: '',
    q: '',
    filterStatus: '',
    loading: false,
    selectedPatient: null,
    selectedRows: [],
    contextPatient: null,
    msg: '',
    err: '',

    init() {
        const s = window.__ranapState = window.__ranapState || {};
        this.tgl1 = s.tgl1 || new Date().toISOString().slice(0,10);
        this.tgl2 = s.tgl2 || new Date().toISOString().slice(0,10);
        this.q = s.q || '';
        this.filterStatus = s.filterStatus || 'belum';
        this.$watch('tgl1', v => { (window.__ranapState = window.__ranapState || {}).tgl1 = v; });
        this.$watch('tgl2', v => { (window.__ranapState = window.__ranapState || {}).tgl2 = v; });
        this.$watch('q', v => { (window.__ranapState = window.__ranapState || {}).q = v || ''; });
        this.$watch('filterStatus', v => { (window.__ranapState = window.__ranapState || {}).filterStatus = v || ''; });
        this.loadData();
    },

    async loadData() {
        this.loading = true;
        try {
            const params = new URLSearchParams({ tgl1: this.tgl1, tgl2: this.tgl2 });
            if (this.q) params.set('q', this.q);
            if (this.filterStatus) params.set('status', this.filterStatus);
            const res = await this.$store.api.get('/ranap/list?' + params.toString());
            this.list = res.list || [];
            this.counts = res.counts || { total: 0, belum: 0, sudah: 0 };
            this.total = this.list.length;
        } catch (e) { console.log(e); }
        this.loading = false;
    },

    filter() { this.loadData(); },

    formatRupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    },

    statusClass(stts) {
        if (stts === '-') return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300';
        if (stts === 'Sehat' || stts === 'Sembuh' || stts === 'Membaik') return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
        if (stts === 'Meninggal') return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300';
        if (stts === 'Rujuk' || stts === 'Pulang Paksa') return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300';
        return 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300';
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

    toggleRow(no_rawat) {
        const i = this.selectedRows.indexOf(no_rawat);
        if (i > -1) this.selectedRows.splice(i, 1);
        else this.selectedRows.push(no_rawat);
    },

    toggleAll() {
        if (this.selectedRows.length === this.list.length) this.selectedRows = [];
        else this.selectedRows = this.list.map(r => r.no_rawat);
    },

    openTindakan(r) {
        const pasien = {
            no_rawat: r.no_rawat,
            no_rkm_medis: r.no_rkm_medis,
            nm_pasien: r.nm_pasien,
            jk: r.jk,
            tgl_lahir: r.tgl_lahir,
            umur: r.lama + ' hr',
            stts_daftar: 'Ranap',
            jenis_bayar: r.png_jawab || '-',
        };
        this.$store.windows.open({
            key: 'ranap-tindakan',
            label: 'Tindakan - ' + (r.nm_pasien || 'Ranap'),
            icon: 'stethoscope',
            width: 1200,
            height: 750,
        }, { pasien, jenis: 'ranap' });
    },

    kamarList: [],
    showPindahKamar: false,
    showPulangkan: false,
    pindahForm: { kd_kamar: '', trf_kamar: 0, tgl_keluar: '', jam_keluar: '', diagnosa_akhir: '' },
    pulangForm: { tgl_keluar: '', jam_keluar: '', stts_pulang: 'Sehat', diagnosa_akhir: '' },
    sttsPulangOpts: ['Sehat', 'Sembuh', 'Membaik', 'Meninggal', 'Rujuk', 'Pulang Paksa', 'Atas Persetujuan Dokter'],

    async loadKamarList() {
        try { this.kamarList = await this.$store.api.get('/ranap/kamar-list') || []; } catch {}
    },

    openPindahKamar() {
        const p = this.selectedPatient;
        if (!p) return;
        this.pindahForm = { kd_kamar: '', trf_kamar: 0, tgl_keluar: new Date().toISOString().slice(0,10), jam_keluar: new Date().toTimeString().slice(0,5), diagnosa_akhir: '' };
        this.loadKamarList();
        this.showPindahKamar = true;
        this.showPulangkan = false;
    },

    onPindahKamarChange() {
        const k = this.kamarList.find(k => k.kd_kamar === this.pindahForm.kd_kamar);
        this.pindahForm.trf_kamar = k ? k.trf_kamar : 0;
    },

    get selectedPindahKamar() {
        return this.kamarList.find(k => k.kd_kamar === this.pindahForm.kd_kamar);
    },

    async submitPindahKamar() {
        const p = this.selectedPatient;
        if (!p) return;
        if (!this.pindahForm.kd_kamar) { this.err = 'Pilih kamar baru'; return; }
        this.loading = true; this.err = '';
        try {
            await this.$store.api.put('/ranap/pindah-kamar', {
                no_rawat: p.no_rawat,
                kd_kamar: this.pindahForm.kd_kamar,
                trf_kamar: this.pindahForm.trf_kamar,
                tgl_keluar: this.pindahForm.tgl_keluar,
                jam_keluar: this.pindahForm.jam_keluar,
            });
            this.msg = 'Kamar berhasil dipindahkan';
            this.showPindahKamar = false;
            await this.loadData();
        } catch (e) { this.err = e.message || 'Gagal pindah kamar'; }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    openPulangkan() {
        const p = this.selectedPatient;
        if (!p) return;
        this.pulangForm = { tgl_keluar: new Date().toISOString().slice(0,10), jam_keluar: new Date().toTimeString().slice(0,5), stts_pulang: 'Sehat', diagnosa_akhir: '' };
        this.showPulangkan = true;
        this.showPindahKamar = false;
    },

    async submitPulangkan() {
        const p = this.selectedPatient;
        if (!p) return;
        this.loading = true; this.err = '';
        try {
            await this.$store.api.put('/ranap/pulangkan', {
                no_rawat: p.no_rawat,
                tgl_keluar: this.pulangForm.tgl_keluar,
                jam_keluar: this.pulangForm.jam_keluar,
                stts_pulang: this.pulangForm.stts_pulang,
            });
            this.msg = 'Pasien berhasil dipulangkan';
            this.showPulangkan = false;
            this.selectedPatient = null;
            await this.loadData();
        } catch (e) { this.err = e.message || 'Gagal pulangkan'; }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    showWaktuMasuk: false,
    showWaktuKeluar: false,
    waktuMasukForm: { tgl_masuk: '', jam_masuk: '' },
    waktuKeluarForm: { tgl_keluar: '', jam_keluar: '' },

    openWaktuMasuk() {
        const p = this.selectedPatient;
        if (!p) return;
        this.waktuMasukForm = { tgl_masuk: p.tgl_masuk || new Date().toISOString().slice(0,10), jam_masuk: p.jam_masuk || new Date().toTimeString().slice(0,5) };
        this.showWaktuMasuk = true;
        this.showWaktuKeluar = false;
    },

    async submitWaktuMasuk() {
        const p = this.selectedPatient;
        if (!p) return;
        this.loading = true; this.err = '';
        try {
            await this.$store.api.put('/ranap/ubah-waktu-masuk', { no_rawat: p.no_rawat, tgl_masuk: this.waktuMasukForm.tgl_masuk, jam_masuk: this.waktuMasukForm.jam_masuk });
            this.msg = 'Waktu masuk berhasil diubah';
            this.showWaktuMasuk = false;
            await this.loadData();
        } catch (e) { this.err = e.message || 'Gagal ubah waktu'; }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    openWaktuKeluar() {
        const p = this.selectedPatient;
        if (!p) return;
        this.waktuKeluarForm = { tgl_keluar: p.tgl_keluar || new Date().toISOString().slice(0,10), jam_keluar: p.jam_keluar || new Date().toTimeString().slice(0,5) };
        this.showWaktuKeluar = true;
        this.showWaktuMasuk = false;
    },

    async submitWaktuKeluar() {
        const p = this.selectedPatient;
        if (!p) return;
        this.loading = true; this.err = '';
        try {
            await this.$store.api.put('/ranap/ubah-waktu-keluar', { no_rawat: p.no_rawat, tgl_keluar: this.waktuKeluarForm.tgl_keluar, jam_keluar: this.waktuKeluarForm.jam_keluar });
            this.msg = 'Waktu keluar berhasil diubah';
            this.showWaktuKeluar = false;
            await this.loadData();
        } catch (e) { this.err = e.message || 'Gagal ubah waktu'; }
        this.loading = false;
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    async hapusRanap() {
        const p = this.selectedPatient;
        if (!p) return;
        if (!confirm('Hapus data rawat inap ' + p.no_rawat + '?')) return;
        try {
            await this.$store.api.delete('/ranap/' + p.no_rawat);
            this.msg = 'Data berhasil dihapus';
            this.selectedPatient = null;
            await this.loadData();
        } catch (e) { this.err = e.message || 'Gagal hapus'; }
        setTimeout(() => { this.msg = ''; this.err = ''; }, 3000);
    },

    showRiwayatKamar: false,
    riwayatKamarList: [],
    riwayatKamarLoading: false,

    async openRiwayatKamar() {
        const p = this.selectedPatient;
        if (!p) return;
        this.riwayatKamarLoading = true;
        this.err = '';
        try {
            const res = await this.$store.api.get('/ranap/riwayat-kamar?no_rawat=' + encodeURIComponent(p.no_rawat));
            this.riwayatKamarList = res.list || [];
        } catch (e) { this.riwayatKamarList = []; this.err = e.message || 'Gagal memuat riwayat'; }
        this.riwayatKamarLoading = false;
        this.showRiwayatKamar = true;
        this.showPindahKamar = false;
        this.showPulangkan = false;
        this.showWaktuMasuk = false;
        this.showWaktuKeluar = false;
    },
} " @click.away="closeContext" @keydown.escape="closeContext" class="flex flex-col h-full overflow-hidden"
    style="color:var(--text-primary);font-size:13px">

    {{-- Top Toolbar --}}
    <div @mousedown.stop class="flex items-center gap-2 px-3 py-1.5 border-b shrink-0 flex-wrap"
        style="background-color:var(--bg-muted);border-color:var(--border)">
        <button @mousedown.stop @click="loadData()" class="btn btn-secondary text-xs px-2 py-1">Refresh</button>
        <template x-if="selectedPatient">
            <div class="flex items-center gap-1.5">
                <span class="w-px h-4" style="background-color:var(--border)"></span>
                <button @mousedown.stop @click="openPindahKamar" class="btn btn-warning text-[11px] px-1.5 py-0.5">Pindah Kamar</button>
                <button @mousedown.stop @click="openPulangkan" class="btn btn-primary text-[11px] px-1.5 py-0.5" style="background-color:#3B82F6">Pulangkan</button>
                <button @mousedown.stop @click="hapusRanap" class="btn btn-danger text-[11px] px-1.5 py-0.5">Hapus</button>
                <button @mousedown.stop @click="openWaktuMasuk" class="btn btn-secondary text-[11px] px-1.5 py-0.5">Waktu Masuk</button>
                <button @mousedown.stop @click="openWaktuKeluar" class="btn btn-secondary text-[11px] px-1.5 py-0.5">Waktu Keluar</button>
                <button @mousedown.stop @click="openRiwayatKamar" class="btn btn-secondary text-[11px] px-1.5 py-0.5">Riwayat Kamar</button>
            </div>
        </template>
        <template x-if="msg">
            <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700" x-text="msg"></span>
        </template>
        <template x-if="err">
            <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700" x-text="err"></span>
        </template>
    </div>

    {{-- Form Pindah Kamar --}}
    <template x-if="showPindahKamar && selectedPatient">
        <div class="border-b shrink-0 px-3 py-2 space-y-2"
            style="border-color:var(--border);background-color:rgba(234,179,8,0.06)">
            <div class="flex items-center gap-3 text-xs mb-2">
                <span class="font-semibold">Pindah Kamar:</span>
                <span x-text="selectedPatient.nm_pasien"></span>
                <span class="text-[10px] font-mono" style="color:var(--text-muted)" x-text="'(' + selectedPatient.no_rawat + ') ' + selectedPatient.nm_bangsal + ' - ' + selectedPatient.kd_kamar"></span>
            </div>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Kamar Baru *</label>
                    <select @mousedown.stop x-model="pindahForm.kd_kamar" @change="onPindahKamarChange" class="form-select text-xs w-full">
                        <option value="">-- Pilih --</option>
                        <template x-for="k in kamarList" :key="k.kd_kamar">
                            <option :value="k.kd_kamar" x-text="k.kd_kamar + ' - ' + (k.nm_bangsal || '-') + ' (' + k.kelas + ') ' + formatRupiah(k.trf_kamar)"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tarif Baru</label>
                    <input @mousedown.stop type="text" :value="formatRupiah(pindahForm.trf_kamar)" class="form-input text-xs w-full" disabled>
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tgl Keluar (dari kamar lama)</label>
                    <input @mousedown.stop type="date" x-model="pindahForm.tgl_keluar" class="form-input text-xs w-full">
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jam Keluar</label>
                    <input @mousedown.stop type="time" x-model="pindahForm.jam_keluar" class="form-input text-xs w-full">
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <button @mousedown.stop @click="submitPindahKamar" :disabled="loading"
                    class="btn bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] px-3 py-1 rounded font-medium"
                    x-text="loading ? 'Proses...' : 'Simpan Pindah Kamar'"></button>
                <button @mousedown.stop @click="showPindahKamar = false" class="btn btn-secondary text-[11px] px-2 py-1">Batal</button>
            </div>
        </div>
    </template>

    {{-- Form Pulangkan --}}
    <template x-if="showPulangkan && selectedPatient">
        <div class="border-b shrink-0 px-3 py-2 space-y-2"
            style="border-color:var(--border);background-color:rgba(59,130,246,0.06)">
            <div class="flex items-center gap-3 text-xs mb-2">
                <span class="font-semibold">Pulangkan Pasien:</span>
                <span x-text="selectedPatient.nm_pasien"></span>
                <span class="text-[10px] font-mono" style="color:var(--text-muted)" x-text="'(' + selectedPatient.no_rawat + ')'"></span>
            </div>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tgl Keluar</label>
                    <input @mousedown.stop type="date" x-model="pulangForm.tgl_keluar" class="form-input text-xs w-full">
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jam Keluar</label>
                    <input @mousedown.stop type="time" x-model="pulangForm.jam_keluar" class="form-input text-xs w-full">
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Status Pulang *</label>
                    <select @mousedown.stop x-model="pulangForm.stts_pulang" class="form-select text-xs w-full">
                        <template x-for="s in sttsPulangOpts" :key="s">
                            <option :value="s" x-text="s"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Diagnosa Akhir</label>
                    <input @mousedown.stop type="text" x-model="pulangForm.diagnosa_akhir" class="form-input text-xs w-full" placeholder="Opsional">
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <button @mousedown.stop @click="submitPulangkan" :disabled="loading"
                    class="btn bg-blue-600 hover:bg-blue-700 text-white text-[11px] px-3 py-1 rounded font-medium"
                    x-text="loading ? 'Proses...' : 'Pulangkan'"></button>
                <button @mousedown.stop @click="showPulangkan = false" class="btn btn-secondary text-[11px] px-2 py-1">Batal</button>
            </div>
        </div>
    </template>

    {{-- Form Waktu Masuk --}}
    <template x-if="showWaktuMasuk && selectedPatient">
        <div class="border-b shrink-0 px-3 py-2 space-y-2"
            style="border-color:var(--border);background-color:rgba(34,197,94,0.06)">
            <div class="flex items-center gap-3 text-xs mb-2">
                <span class="font-semibold">Ubah Waktu Masuk:</span>
                <span x-text="selectedPatient.nm_pasien"></span>
                <span class="text-[10px] font-mono" style="color:var(--text-muted)" x-text="'(' + selectedPatient.no_rawat + ')'"></span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tanggal Masuk</label>
                    <input @mousedown.stop type="date" x-model="waktuMasukForm.tgl_masuk" class="form-input text-xs w-full">
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jam Masuk</label>
                    <input @mousedown.stop type="time" x-model="waktuMasukForm.jam_masuk" class="form-input text-xs w-full">
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <button @mousedown.stop @click="submitWaktuMasuk" :disabled="loading"
                    class="btn bg-green-600 hover:bg-green-700 text-white text-[11px] px-3 py-1 rounded font-medium"
                    x-text="loading ? 'Proses...' : 'Simpan'"></button>
                <button @mousedown.stop @click="showWaktuMasuk = false" class="btn btn-secondary text-[11px] px-2 py-1">Batal</button>
            </div>
        </div>
    </template>

    {{-- Form Waktu Keluar --}}
    <template x-if="showWaktuKeluar && selectedPatient">
        <div class="border-b shrink-0 px-3 py-2 space-y-2"
            style="border-color:var(--border);background-color:rgba(168,85,247,0.06)">
            <div class="flex items-center gap-3 text-xs mb-2">
                <span class="font-semibold">Ubah Waktu Keluar:</span>
                <span x-text="selectedPatient.nm_pasien"></span>
                <span class="text-[10px] font-mono" style="color:var(--text-muted)" x-text="'(' + selectedPatient.no_rawat + ')'"></span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tanggal Keluar</label>
                    <input @mousedown.stop type="date" x-model="waktuKeluarForm.tgl_keluar" class="form-input text-xs w-full">
                </div>
                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jam Keluar</label>
                    <input @mousedown.stop type="time" x-model="waktuKeluarForm.jam_keluar" class="form-input text-xs w-full">
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <button @mousedown.stop @click="submitWaktuKeluar" :disabled="loading"
                    class="btn bg-purple-600 hover:bg-purple-700 text-white text-[11px] px-3 py-1 rounded font-medium"
                    x-text="loading ? 'Proses...' : 'Simpan'"></button>
                <button @mousedown.stop @click="showWaktuKeluar = false" class="btn btn-secondary text-[11px] px-2 py-1">Batal</button>
            </div>
        </div>
    </template>

    {{-- Riwayat Kamar --}}
    <template x-if="showRiwayatKamar && selectedPatient">
        <div class="border-b shrink-0 px-3 py-2 space-y-2"
            style="border-color:var(--border);background-color:rgba(14,165,233,0.06)">
            <div class="flex items-center justify-between text-xs mb-2">
                <div class="flex items-center gap-3">
                    <span class="font-semibold">Riwayat Kamar:</span>
                    <span x-text="selectedPatient.nm_pasien"></span>
                    <span class="text-[10px] font-mono" style="color:var(--text-muted)" x-text="'(' + selectedPatient.no_rawat + ')'"></span>
                </div>
                <button @mousedown.stop @click="showRiwayatKamar = false" class="text-[16px] leading-none px-1 hover:text-red-500" style="color:var(--text-muted)">&times;</button>
            </div>
            <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                <table class="table-default text-xs">
                    <thead>
                        <tr>
                            <th>Bangsal</th>
                            <th>Kamar</th>
                            <th>Kelas</th>
                            <th>Tgl.Masuk</th>
                            <th>Jam Masuk</th>
                            <th>Tgl.Keluar</th>
                            <th>Jam Keluar</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(r, idx) in riwayatKamarList" :key="idx">
                            <tr :class="r.keterangan === 'Kamar Saat Ini' ? 'bg-green-50 dark:bg-green-900/20' : ''">
                                <td x-text="r.nm_bangsal"></td>
                                <td class="font-mono" x-text="r.kd_kamar"></td>
                                <td x-text="r.kelas"></td>
                                <td x-text="r.tgl_masuk"></td>
                                <td x-text="r.jam_masuk || '-'"></td>
                                <td x-text="r.tgl_keluar || '-'"></td>
                                <td x-text="r.jam_keluar || '-'"></td>
                                <td>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-medium"
                                        :class="r.keterangan === 'Kamar Saat Ini' ? 'bg-green-100 text-green-700 dark:bg-green-800/40 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
                                        x-text="r.keterangan"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="riwayatKamarLoading">
                            <td colspan="8" class="text-center py-4 text-xs" style="color:var(--text-muted)">Memuat...</td>
                        </tr>
                        <tr x-show="!riwayatKamarLoading && riwayatKamarList.length === 0">
                            <td colspan="8" class="text-center py-4 text-xs" style="color:var(--text-muted)">Tidak ada riwayat</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    {{-- Filter Bar --}}
    <div @mousedown.stop class="flex items-center gap-2 px-3 py-1 border-b shrink-0 flex-wrap"
        style="background-color:var(--bg-muted);border-color:var(--border)">
        <label class="text-xs font-medium" style="color:var(--text-muted)">Tanggal Masuk:</label>
        <input @mousedown.stop type="date" x-model="tgl1" class="form-input text-xs w-28 py-0.5">
        <span class="text-xs" style="color:var(--text-muted)">s/d</span>
        <input @mousedown.stop type="date" x-model="tgl2" class="form-input text-xs w-28 py-0.5">
        <button @mousedown.stop
            @click="tgl1 = new Date().toISOString().slice(0,10); tgl2 = new Date().toISOString().slice(0,10); filter()"
            class="btn btn-secondary text-[11px] px-1.5 py-0.5">Hari Ini</button>
        <button @mousedown.stop
            @click="tgl2 = new Date().toISOString().slice(0,10); tgl1 = new Date(Date.now() - 7*24*60*60*1000).toISOString().slice(0,10); filter()"
            class="btn btn-secondary text-[11px] px-1.5 py-0.5">7 Hari</button>
        <button @mousedown.stop
            @click="tgl2 = new Date().toISOString().slice(0,10); tgl1 = new Date(Date.now() - 30*24*60*60*1000).toISOString().slice(0,10); filter()"
            class="btn btn-secondary text-[11px] px-1.5 py-0.5">30 Hari</button>

        <div class="w-px h-4" style="background-color:var(--border)"></div>

        <select @mousedown.stop x-model="filterStatus" @change="filter" class="form-select text-xs w-36 py-0.5">
            <option value="">Semua Status</option>
            <option value="belum">Belum Pulang</option>
            <option value="sudah">Sudah Pulang</option>
            <option value="pindah">Pindah Kamar</option>
        </select>

        <input @mousedown.stop type="text" x-model="q" placeholder="Cari pasien/no. RM..."
            class="form-input text-xs w-40 py-0.5">
        <button @mousedown.stop @click="filter()" class="btn btn-secondary text-xs px-2 py-0.5">Cari</button>

    </div>

    {{-- Table --}}
    <div @mousedown.stop class="flex-1 overflow-auto min-h-0">
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="sticky top-0 bg-gray-100 dark:bg-gray-800">
                    <th class="border p-1 text-left w-5"><input type="checkbox" @change="toggleAll"
                            :checked="selectedRows.length === list.length && list.length > 0"></th>
                    <th class="border p-1 text-left">No.Rawat</th>
                    <th class="border p-1 text-left">Pasien</th>
                    <th class="border p-1 text-left">Bangsal</th>
                    <th class="border p-1 text-left">Kamar</th>
                    <th class="border p-1 text-left">Kelas</th>
                    <th class="border p-1 text-left">Tgl.Masuk</th>
                    <th class="border p-1 text-left">Tgl.Keluar</th>
                    <th class="border p-1 text-left">Lama</th>
                    <th class="border p-1 text-left">Biaya Kamar</th>
                    <th class="border p-1 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="r in list" :key="r.no_rawat">
                    <tr @contextmenu="contextHandler($event, r)" @click="selectRow(r)" @dblclick="openTindakan(r)"
                        :class="selectedPatient?.no_rawat === r.no_rawat ? 'bg-blue-100 dark:bg-blue-900' : ''"
                        class="border dark:hover:bg-gray-700 hover:bg-gray-50 cursor-pointer">
                        <td class="p-1"><input type="checkbox" :checked="selectedRows.includes(r.no_rawat)"
                                @change="toggleRow(r.no_rawat)" @click.stop></td>
                        <td class="p-1 font-mono text-[10px]" x-text="r.no_rawat"></td>
                        <td class="p-1 font-medium">
                            <span x-text="r.nm_pasien"></span>
                            <span class="text-[10px]" style="color:var(--text-muted)"
                                x-text="'(' + r.no_rkm_medis + ')'"></span>
                        </td>
                        <td class="p-1" x-text="r.nm_bangsal"></td>
                        <td class="p-1" x-text="r.kd_kamar"></td>
                        <td class="p-1 text-[10px]" x-text="r.kelas"></td>
                        <td class="p-1">
                            <div x-text="r.tgl_masuk"></div>
                            <div class="text-[9px] font-mono" style="color:var(--text-muted)" x-text="r.jam_masuk || ''"></div>
                        </td>
                        <td class="p-1">
                            <div x-text="r.tgl_keluar || '-'"></div>
                            <div class="text-[9px] font-mono" style="color:var(--text-muted)" x-text="r.jam_keluar || ''"></div>
                        </td>
                        <td class="p-1" x-text="r.lama + ' hr'"></td>
                        <td class="p-1" x-text="formatRupiah(r.ttl_biaya)"></td>
                        <td class="p-1">
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-medium"
                                :class="statusClass(r.stts_pulang)"
                                x-text="r.stts_pulang === '-' ? 'Dirawat' : r.stts_pulang">
                            </span>
                        </td>
                    </tr>
                </template>
                <tr x-show="!list.length && !loading">
                    <td colspan="11" class="p-4 text-center text-gray-500">Tidak ada data rawat inap</td>
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
        <button @mousedown.stop @click="closeContext(); openTindakan(contextPatient)"
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Pemeriksaan
        </button>
        <button @mousedown.stop
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            Visite
        </button>
        <button @mousedown.stop
            class="w-full text-left px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Catatan
        </button>
        <div class="border-t my-1"></div>
        <button @mousedown.stop
            class="w-full text-left px-4 py-1.5 hover:bg-blue-50 text-blue-700 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Pulangkan
        </button>
    </div>
</div>