<div x-data="{
    pasien: null,
    no_rawat: '',
    kamarList: [],
    dokterList: [],
    loading: false,
    msg: '',
    err: '',
    form: {
        kd_kamar: '',
        trf_kamar: 0,
        diagnosa_awal: '',
        tgl_masuk: new Date().toISOString().slice(0,10),
        jam_masuk: new Date().toTimeString().slice(0,5),
        kd_dokter: '',
    },

    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data) {
            this.pasien = win.data.pasien || null;
            this.no_rawat = win.data.no_rawat || (this.pasien?.no_rawat) || '';
        }
        if (!this.pasien && this.no_rawat) {
            this.loadPasien();
        }
        this.loadKamar();
        this.loadDokter();

        if (window.__ranapAdmissionState) {
            Object.assign(this.form, window.__ranapAdmissionState);
        }
        this.$watch('form', (val) => {
            window.__ranapAdmissionState = { ...val };
        }, { deep: true });
    },

    async loadPasien() {
        try {
            const res = await this.$store.api.get('/pasien/search?q=' + encodeURIComponent(this.no_rawat));
            if (res?.length) this.pasien = res[0];
        } catch {}
    },

    async loadKamar() {
        try {
            const res = await this.$store.api.get('/ranap/kamar-list');
            this.kamarList = res || [];
        } catch (e) { console.log('load kamar error', e); }
    },

    async loadDokter() {
        try {
            const res = await this.$store.api.get('/ralan/dokter-list');
            this.dokterList = res || [];
        } catch (e) { console.log('load dokter error', e); }
    },

    onKamarChange() {
        const k = this.kamarList.find(k => k.kd_kamar === this.form.kd_kamar);
        this.form.trf_kamar = k ? k.trf_kamar : 0;
    },

    get selectedKamar() {
        return this.kamarList.find(k => k.kd_kamar === this.form.kd_kamar);
    },

    async submit() {
        if (!this.form.kd_kamar) { this.err = 'Pilih kamar'; return; }
        if (!this.form.kd_dokter) { this.err = 'Pilih DPJP'; return; }
        if (!this.form.diagnosa_awal) { this.err = 'Diagnosa awal harus diisi'; return; }
        this.loading = true;
        this.err = '';
        try {
            await this.$store.api.post('/ranap/admit', {
                no_rawat: this.no_rawat,
                no_rkm_medis: this.pasien?.no_rkm_medis,
                kd_kamar: this.form.kd_kamar,
                trf_kamar: this.form.trf_kamar,
                kd_dokter: this.form.kd_dokter,
                diagnosa_awal: this.form.diagnosa_awal,
                tgl_masuk: this.form.tgl_masuk,
                jam_masuk: this.form.jam_masuk,
            });
            this.msg = 'Pasien berhasil masuk Kamar Inap';
            window.__ranapAdmissionState = null;
            setTimeout(() => {
                const win = this.$store.windows.items.find(w => w.id === this.$el.closest('[data-window-id]')?.dataset?.windowId);
                if (win) this.$store.windows.close(win.id);
            }, 1500);
        } catch (e) {
            this.err = e.message || 'Gagal mendaftarkan kamar inap';
        }
        this.loading = false;
    },

    formatRupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    },
}" class="p-4 space-y-4 overflow-y-auto h-full"
    style="color:var(--text-primary);font-size:13px">

    <template x-if="msg">
        <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs px-3 py-2 rounded" x-text="msg"></div>
    </template>
    <template x-if="err">
        <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs px-3 py-2 rounded" x-text="err"></div>
    </template>

    {{-- Data Pasien --}}
    <div class="rounded-lg border p-3" style="border-color:var(--border)">
        <h4 class="text-xs font-bold mb-2" style="color:var(--accent-blue)">Data Pasien</h4>
        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
            <div class="flex justify-between">
                <span style="color:var(--text-muted)">No.Rawat</span>
                <span class="font-medium font-mono" x-text="no_rawat || '-'"></span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--text-muted)">No.RM</span>
                <span class="font-medium" x-text="pasien?.no_rkm_medis || '-'"></span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--text-muted)">Pasien</span>
                <span class="font-medium" x-text="pasien?.nm_pasien || '-'"></span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--text-muted)">JK</span>
                <span x-text="pasien?.jk || '-'"></span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--text-muted)">Tgl.Lahir</span>
                <span x-text="pasien?.tgl_lahir || '-'"></span>
            </div>
            <div class="flex justify-between">
                <span style="color:var(--text-muted)">Cara Bayar</span>
                <span x-text="pasien?.jenis_bayar || '-'"></span>
            </div>
        </div>
    </div>

    {{-- Form Kamar Inap --}}
    <div class="rounded-lg border p-3 space-y-3" style="border-color:var(--border)">
        <h4 class="text-xs font-bold" style="color:var(--accent-blue)">Form Masuk Kamar Inap</h4>

        <div>
            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Pilih Kamar *</label>
            <select @mousedown.stop x-model="form.kd_kamar" @change="onKamarChange" class="form-select text-xs w-full">
                <option value="">-- Pilih Kamar --</option>
                <template x-for="k in kamarList" :key="k.kd_kamar">
                    <option :value="k.kd_kamar" x-text="k.kd_kamar + ' - ' + (k.nm_bangsal || '-') + ' (' + k.kelas + ') - ' + formatRupiah(k.trf_kamar)"></option>
                </template>
            </select>
        </div>

        <template x-if="selectedKamar">
            <div class="grid grid-cols-2 gap-2 text-xs rounded p-2" style="background-color:var(--bg-muted)">
                <div><span style="color:var(--text-muted)">Bangsal:</span> <span class="font-medium" x-text="selectedKamar?.nm_bangsal"></span></div>
                <div><span style="color:var(--text-muted)">Kelas:</span> <span x-text="selectedKamar?.kelas"></span></div>
                <div><span style="color:var(--text-muted)">Tarif/hari:</span> <span class="font-medium" x-text="formatRupiah(selectedKamar?.trf_kamar)"></span></div>
                <div><span style="color:var(--text-muted)">Status:</span>
                    <span :class="selectedKamar?.status === 'KOSONG' ? 'text-green-600' : 'text-red-600'" x-text="selectedKamar?.status === 'KOSONG' ? 'Tersedia' : selectedKamar?.status">
                    </span>
                </div>
            </div>
        </template>

        <div>
            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tarif Kamar</label>
            <input @mousedown.stop type="text" :value="formatRupiah(form.trf_kamar)" class="form-input text-xs w-full" disabled>
        </div>

        <div>
            <label class="text-[10px] font-medium" style="color:var(--text-muted)">DPJP (Dokter Penanggung Jawab) *</label>
            <select @mousedown.stop x-model="form.kd_dokter" class="form-select text-xs w-full">
                <option value="">-- Pilih DPJP --</option>
                <template x-for="d in dokterList" :key="d.kd_dokter">
                    <option :value="d.kd_dokter" x-text="d.nm_dokter + ' (' + d.kd_dokter + ')'"></option>
                </template>
            </select>
        </div>

        <div>
            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Diagnosa Awal *</label>
            <textarea @mousedown.stop x-model="form.diagnosa_awal" class="form-input text-xs w-full" rows="2" placeholder="Diagnosa saat masuk"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tanggal Masuk</label>
                <input @mousedown.stop type="date" x-model="form.tgl_masuk" class="form-input text-xs w-full">
            </div>
            <div>
                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jam Masuk</label>
                <input @mousedown.stop type="time" x-model="form.jam_masuk" class="form-input text-xs w-full">
            </div>
        </div>
    </div>

    {{-- Tombol --}}
    <div class="flex gap-2 pt-1">
        <button @mousedown.stop @click="submit" :disabled="loading"
            class="btn bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-1.5 rounded flex-1 font-medium"
            x-text="loading ? 'Menyimpan...' : 'Masukkan Kamar Inap'"></button>
        <button @mousedown.stop @click="() => { const w = $store.windows.items.find(x => x.id === $el.closest('[data-window-id]')?.dataset?.windowId); if(w) $store.windows.close(w.id); }"
            class="btn btn-secondary text-xs px-3 py-1.5">Batal</button>
    </div>
</div>
