<div x-data="{
    active: 'akun',
    menu: [
        { key: 'akun', label: 'Nama Akun', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
        { key: 'tampilan', label: 'Tampilan', icon: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' },
        { key: 'identitas', label: 'Identitas RS', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
        { key: 'depo', label: 'Depo Farmasi', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'bangsal', label: 'Bangsal & Kamar', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
        { key: 'api', label: 'API Bridging', icon: 'M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.858 15.355-5.858 21.213 0' },
        { key: 'jenisbayar', label: 'Jenis Bayar', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' },
        { key: 'lokasifarmasi', label: 'Lokasi Farmasi', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z' },
        { key: 'chatbot', label: 'Chatbot AI', icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z' },
        { key: 'poli', label: 'Poli', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    ],
    akun: { nama: 'Admin SIMRS', email: 'admin@simrs.khanza', role: 'Super Administrator', username: 'admin', foto: '' },
    identitas: { nama_instansi: '', alamat_instansi: '', kabupaten: '', propinsi: '', kontak: '', email: '', kode_ppk: '', kode_ppkinhealth: '', kode_ppkkemenkes: '' },
    depoList: [],

    bangsalList: [
        { nama: 'Flamboyan', kelas: 'I', kapasitas: 20, terisi: 15, tersedia: 5 },
        { nama: 'Anggrek', kelas: 'II', kapasitas: 25, terisi: 20, tersedia: 5 },
        { nama: 'Melati', kelas: 'II', kapasitas: 30, terisi: 18, tersedia: 12 },
        { nama: 'Mawar', kelas: 'III', kapasitas: 40, terisi: 35, tersedia: 5 },
        { nama: 'Dahlia', kelas: 'III', kapasitas: 35, terisi: 28, tersedia: 7 },
        { nama: 'Kamboja', kelas: 'VIP', kapasitas: 10, terisi: 6, tersedia: 4 },
        { nama: 'Cempaka', kelas: 'VIP', kapasitas: 8, terisi: 5, tersedia: 3 },
    ],
    apiBridging: [
        { nama: 'BPJS API', url: 'https://apijkn.bpjs-kesehatan.go.id', status: 'Terhubung', key: '***********' },
        { nama: 'Satu Sehat (SATUSEHAT)', url: 'https://api.satusehat.kemkes.go.id', status: 'Terhubung', key: '***********' },
        { nama: 'INACBG', url: 'https://inacbg.kemkes.go.id', status: 'Tidak Terhubung', key: '-' },
        { nama: 'SIPP', url: 'https://sipp.kemkes.go.id', status: 'Terhubung', key: '***********' },
    ],
    lokasiFarmasi: [
        { unit: 'IGD (Instalasi Gawat Darurat)', depo: 'Depo Farmasi IGD', petugas: 'Apt. Sari Dewi, S.Farm', jam: '24 Jam' },
        { unit: 'Rawat Jalan (Rajal)', depo: 'Depo Farmasi Rawat Jalan', petugas: 'Apt. Budi Santoso, S.Farm', jam: '07:00 - 21:00' },
        { unit: 'Rawat Inap (Ranap)', depo: 'Depo Farmasi Rawat Inap', petugas: 'Apt. Ani Rahmawati, S.Farm', jam: '24 Jam' },
        { unit: 'VIP / Eksekutif', depo: 'Depo Farmasi VIP', petugas: 'Apt. Dian Pratama, S.Farm', jam: '24 Jam' },
    ],
    tampilan: { tema: 'light', bahasa: 'id', font_size: '14', jam_format: '24' },
    poliList: [],
    showPoliForm: false,
    editingPoli: null,
    poliForm: { kd_poli: '', nm_poli: '', registrasi: 0, registrasilama: 0, status: '1' },
    penjabList: [],
    showPenjabForm: false,
    editingPenjab: null,
    penjabForm: { kd_pj: '', png_jawab: '', nama_perusahaan: '', alamat: '', no_telp: '', attn: '', status: '1' },
    showAiKey: false,
    testPrompt: '',
    testResult: '',
    testOk: false,
    testing: false,
    async fetchPoli() {
        try {
            const res = await this.$store.api.get('/jadwal/poli');
            this.poliList = res || [];
        } catch (e) { this.poliList = []; }
    },

    openPoliForm(p) {
        if (p) {
            this.editingPoli = p;
            this.poliForm = { kd_poli: p.kd_poli, nm_poli: p.nm_poli, registrasi: p.registrasi ?? 0, registrasilama: p.registrasilama ?? 0, status: p.status };
        } else {
            this.editingPoli = null;
            this.poliForm = { kd_poli: '', nm_poli: '', registrasi: 0, registrasilama: 0, status: '1' };
        }
        this.showPoliForm = true;
    },

    async savePoli() {
        if (!this.poliForm.kd_poli || !this.poliForm.nm_poli) return;
        try {
            if (this.editingPoli) {
                await this.$store.api.put('/jadwal/poli/' + this.editingPoli.kd_poli, this.poliForm);
                this.$store.api.cacheBust('/jadwal/poli');
            } else {
                await this.$store.api.post('/jadwal/poli', this.poliForm);
                this.$store.api.cacheBust('/jadwal/poli');
            }
            await this.fetchPoli();
            this.showPoliForm = false;
            this.editingPoli = null;
        } catch (e) {
            alert('Gagal menyimpan: ' + (e.message || 'Unknown error'));
        }
    },

    async hapusPoli(p) {
        if (!confirm('Yakin ingin menghapus ' + p.nm_poli + '?')) return;
        try {
            await this.$store.api.delete('/jadwal/poli/' + p.kd_poli);
            this.$store.api.cacheBust('/jadwal/poli');
            await this.fetchPoli();
        } catch (e) {
            alert('Gagal menghapus');
        }
    },

    async testKoneksi() {
        if (!this.testPrompt || !$store.ai.isConfigured()) return;
        this.testing = true;
        this.testResult = '';
        try {
            const res = await fetch($store.ai.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + $store.ai.apiKey },
                body: JSON.stringify({ model: $store.ai.model, messages: [{ role: 'user', content: this.testPrompt }], max_tokens: 100 })
            });
            if (!res.ok) { this.testOk = false; this.testResult = 'Error: ' + res.status + ' ' + res.statusText; return; }
            const json = await res.json();
            this.testOk = true;
            this.testResult = json.choices?.[0]?.message?.content || JSON.stringify(json).slice(0,200);
        } catch (e) {
            this.testOk = false;
            this.testResult = 'Gagal: ' + e.message;
        }
        finally { this.testing = false; }
    },

    async fetchPenjab() {
        try {
            const res = await this.$store.api.get('/jadwal/penjab');
            this.penjabList = res || [];
        } catch (e) { this.penjabList = []; }
    },

    openPenjabForm(p) {
        if (p) {
            this.editingPenjab = p;
            this.penjabForm = { kd_pj: p.kd_pj, png_jawab: p.png_jawab, nama_perusahaan: p.nama_perusahaan ?? '', alamat: p.alamat ?? '', no_telp: p.no_telp ?? '', attn: p.attn ?? '', status: p.status };
        } else {
            this.editingPenjab = null;
            this.penjabForm = { kd_pj: '', png_jawab: '', nama_perusahaan: '', alamat: '', no_telp: '', attn: '', status: '1' };
        }
        this.showPenjabForm = true;
    },

    async savePenjab() {
        if (!this.penjabForm.kd_pj || !this.penjabForm.png_jawab) return;
        try {
            if (this.editingPenjab) {
                await this.$store.api.put('/jadwal/penjab/' + this.editingPenjab.kd_pj, this.penjabForm);
                this.$store.api.cacheBust('/jadwal/penjab');
            } else {
                await this.$store.api.post('/jadwal/penjab', this.penjabForm);
                this.$store.api.cacheBust('/jadwal/penjab');
            }
            await this.fetchPenjab();
            this.showPenjabForm = false;
            this.editingPenjab = null;
        } catch (e) {
            alert('Gagal menyimpan: ' + (e.message || 'Unknown error'));
        }
    },

    async hapusPenjab(p) {
        if (!confirm('Yakin ingin menghapus ' + p.png_jawab + '?')) return;
        try {
            await this.$store.api.delete('/jadwal/penjab/' + p.kd_pj);
            this.$store.api.cacheBust('/jadwal/penjab');
            await this.fetchPenjab();
        } catch (e) {
            alert('Gagal menghapus');
        }
    },

    async fetchIdentitas() {
        try {
            const res = await this.$store.api.get('/identitas');
            if (res) {
                this.identitas = {
                    nama_instansi: res.nama_instansi ?? '',
                    alamat_instansi: res.alamat_instansi ?? '',
                    kabupaten: res.kabupaten ?? '',
                    propinsi: res.propinsi ?? '',
                    kontak: res.kontak ?? '',
                    email: res.email ?? '',
                    kode_ppk: res.kode_ppk ?? '',
                    kode_ppkinhealth: res.kode_ppkinhealth ?? '',
                    kode_ppkkemenkes: res.kode_ppkkemenkes ?? '',
                };
            }
        } catch (e) {}
    },

    async saveIdentitas() {
        try {
            await this.$store.api.put('/identitas', this.identitas);
            this.$store.api.cacheBust('/identitas');
            alert('Identitas berhasil disimpan');
        } catch (e) {
            alert('Gagal menyimpan: ' + (e.message || 'Unknown error'));
        }
    },

    async fetchDepo() {
        try {
            const res = await this.$store.api.get('/settings/depo-list');
            this.depoList = res || [];
        } catch (e) { this.depoList = []; }
    },

    init() {
        this.fetchPoli();
        this.fetchPenjab();
        this.fetchIdentitas();
        this.fetchDepo();
    },
}" @mousedown.stop class="flex h-full gap-0">
    <div class="w-48 shrink-0 flex flex-col overflow-y-auto border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
        <template x-for="item in menu" :key="item.key">
            <button @mousedown.stop @click="active = item.key"
                class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-left transition-colors border-l-2"
                :class="active === item.key ? 'font-semibold' : ''"
                :style="active === item.key ? 'background-color:var(--bg-hover);border-color:var(--accent-blue);color:var(--accent-blue)' : 'border-color:transparent;color:var(--text-secondary)'">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path :d="item.icon"/>
                </svg>
                <span x-text="item.label"></span>
            </button>
        </template>
    </div>

    <div class="flex-1 overflow-y-auto p-5" style="color:var(--text-primary)">
        <template x-if="active === 'akun'">
            <div class="space-y-4 max-w-lg">
                <h3 class="text-base font-semibold">Nama Akun</h3>
                <div class="flex items-center gap-4 pb-4 border-b" style="border-color:var(--border)">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xl font-bold shadow">A</div>
                    <div>
                        <p class="font-medium" x-text="akun.nama"></p>
                        <p class="text-xs" style="color:var(--text-muted)" x-text="akun.role"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="form-label">Nama Lengkap</label><input type="text" x-model="akun.nama" class="form-input text-xs"></div>
                    <div><label class="form-label">Username</label><input type="text" x-model="akun.username" class="form-input text-xs"></div>
                    <div class="col-span-2"><label class="form-label">Email</label><input type="email" x-model="akun.email" class="form-input text-xs"></div>
                    <div class="col-span-2"><label class="form-label">Role</label><input type="text" x-model="akun.role" class="form-input text-xs" disabled></div>
                </div>
                <div class="pt-2"><button class="btn btn-primary text-xs">Simpan Perubahan</button></div>
            </div>
        </template>

        <template x-if="active === 'tampilan'">
            <div class="space-y-4 max-w-lg">
                <h3 class="text-base font-semibold">Pengaturan Tampilan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b" style="border-color:var(--border)">
                        <div><p class="text-sm">Mode Gelap/Terang</p><p class="text-xs" style="color:var(--text-muted)">Sesuaikan tema tampilan</p></div>
                        <button @click="$store.theme.toggle()" class="px-3 py-1 rounded text-xs font-medium" :class="$store.theme.mode === 'dark' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'" x-text="$store.theme.mode === 'dark' ? 'Gelap' : 'Terang'"></button>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b" style="border-color:var(--border)">
                        <div><p class="text-sm">Bahasa</p><p class="text-xs" style="color:var(--text-muted)">Bahasa antarmuka</p></div>
                        <select x-model="tampilan.bahasa" class="form-select text-xs w-24">
                            <option value="id">Indonesia</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b" style="border-color:var(--border)">
                        <div><p class="text-sm">Ukuran Font</p><p class="text-xs" style="color:var(--text-muted)">Ukuran teks antarmuka</p></div>
                        <select x-model="tampilan.font_size" class="form-select text-xs w-20">
                            <option value="12">12px</option>
                            <option value="14">14px</option>
                            <option value="16">16px</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b" style="border-color:var(--border)">
                        <div><p class="text-sm">Format Jam</p><p class="text-xs" style="color:var(--text-muted)">Tampilan jam di taskbar</p></div>
                        <select x-model="tampilan.jam_format" class="form-select text-xs w-20">
                            <option value="24">24 Jam</option>
                            <option value="12">12 Jam</option>
                        </select>
                    </div>
                </div>
                <div class="pt-2"><button class="btn btn-primary text-xs">Simpan Pengaturan</button></div>
            </div>
        </template>

        <template x-if="active === 'identitas'">
            <div class="space-y-4 max-w-lg">
                <h3 class="text-base font-semibold">Identitas Faskes</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2"><label class="form-label">Nama Instansi</label><input @mousedown.stop type="text" x-model="identitas.nama_instansi" class="form-input text-xs font-semibold"></div>
                    <div class="col-span-2"><label class="form-label">Alamat</label><input @mousedown.stop type="text" x-model="identitas.alamat_instansi" class="form-input text-xs"></div>
                    <div><label class="form-label">Kabupaten</label><input @mousedown.stop type="text" x-model="identitas.kabupaten" class="form-input text-xs"></div>
                    <div><label class="form-label">Provinsi</label><input @mousedown.stop type="text" x-model="identitas.propinsi" class="form-input text-xs"></div>
                    <div><label class="form-label">Kontak</label><input @mousedown.stop type="text" x-model="identitas.kontak" class="form-input text-xs"></div>
                    <div><label class="form-label">Email</label><input @mousedown.stop type="text" x-model="identitas.email" class="form-input text-xs"></div>
                    <div><label class="form-label">Kode PPK</label><input @mousedown.stop type="text" x-model="identitas.kode_ppk" class="form-input text-xs"></div>
                    <div><label class="form-label">Kode PPK Inhealth</label><input @mousedown.stop type="text" x-model="identitas.kode_ppkinhealth" class="form-input text-xs"></div>
                    <div class="col-span-2"><label class="form-label">Kode PPK Kemkes</label><input @mousedown.stop type="text" x-model="identitas.kode_ppkkemenkes" class="form-input text-xs"></div>
                </div>
                <div class="pt-2"><button @mousedown.stop @click="saveIdentitas" class="btn btn-primary text-xs">Simpan</button></div>
            </div>
        </template>

        <template x-if="active === 'depo'">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Depo Farmasi</h3>
                    <button class="btn btn-primary text-xs" disabled>+ Tambah Depo</button>
                </div>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode Depo</th><th>Nama Depo</th><th>Jenis</th><th>Bangsal</th></tr></thead>
                        <tbody>
                            <template x-for="(d, i) in depoList" :key="i">
                                <tr>
                                    <td class="font-mono text-xs" x-text="d.kd_depo"></td>
                                    <td class="font-medium" x-text="d.nama"></td>
                                    <td><span class="badge text-[10px]" :class="d.jenis === 'Ralan' ? 'badge-success' : 'badge-info'" x-text="d.jenis"></span></td>
                                    <td style="color:var(--text-secondary)" x-text="d.nm_bangsal || '-'"></td>
                                </tr>
                            </template>
                            <tr x-show="!depoList.length">
                                <td colspan="4" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data depo farmasi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <template x-if="active === 'bangsal'">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Bangsal & Kamar</h3>
                    <button class="btn btn-primary text-xs">+ Tambah Bangsal</button>
                </div>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Bangsal</th><th>Kelas</th><th>Kapasitas</th><th>Terisi</th><th>Tersedia</th><th class="w-24">Status</th></tr></thead>
                        <tbody>
                            <template x-for="(b, i) in bangsalList" :key="i">
                                <tr>
                                    <td class="font-medium" x-text="b.nama"></td>
                                    <td><span class="badge" :class="b.kelas === 'VIP' ? 'badge-info' : 'badge-success'" x-text="'Kelas ' + b.kelas"></span></td>
                                    <td x-text="b.kapasitas"></td>
                                    <td x-text="b.terisi"></td>
                                    <td><span :class="b.tersedia <= 3 ? 'text-red-500 dark:text-red-400 font-medium' : ''" x-text="b.tersedia"></span></td>
                                    <td><span class="badge" :class="b.tersedia === 0 ? 'badge-danger' : b.tersedia <= 3 ? 'badge-warning' : 'badge-success'" x-text="b.tersedia === 0 ? 'Penuh' : b.tersedia <= 3 ? 'Hampir Penuh' : 'Tersedia'"></span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <template x-if="active === 'api'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">API Bridging</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Nama</th><th>URL Endpoint</th><th>Status</th><th>API Key</th><th class="w-20"></th></tr></thead>
                        <tbody>
                            <template x-for="(a, i) in apiBridging" :key="i">
                                <tr>
                                    <td class="font-medium" x-text="a.nama"></td>
                                    <td class="text-xs" style="color:var(--text-secondary)" x-text="a.url"></td>
                                    <td><span class="badge" :class="a.status === 'Terhubung' ? 'badge-success' : 'badge-danger'" x-text="a.status"></span></td>
                                    <td class="text-xs font-mono" style="color:var(--text-muted)" x-text="a.key"></td>
                                    <td><button class="text-xs px-2 py-1 rounded hover:bg-black/5 dark:hover:bg-white/10" style="color:var(--accent-blue)">Ubah</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <template x-if="active === 'jenisbayar'">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Jenis Bayar</h3>
                    <button @mousedown.stop @click="openPenjabForm(null)" class="btn btn-primary text-xs">+ Tambah</button>
                </div>
                <div @mousedown.stop class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Nama</th><th>Perusahaan</th><th>Telepon</th><th>Status</th><th class="w-28"></th></tr></thead>
                        <tbody>
                            <template x-for="j in penjabList" :key="j.kd_pj">
                                <tr>
                                    <td><code class="text-xs px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="j.kd_pj"></code></td>
                                    <td class="font-medium" x-text="j.png_jawab"></td>
                                    <td class="text-xs" style="color:var(--text-secondary)" x-text="j.nama_perusahaan || '-'"></td>
                                    <td class="text-xs" x-text="j.no_telp || '-'"></td>
                                    <td><span class="badge" :class="j.status === '1' ? 'badge-success' : 'badge-danger'" x-text="j.status_label"></span></td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <button @mousedown.stop @click="openPenjabForm(j)" class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:var(--bg-hover)">Edit</button>
                                            <button @mousedown.stop @click="hapusPenjab(j)" class="text-[10px] px-1.5 py-0.5 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!penjabList.length">
                                <td colspan="6" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data jenis bayar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- FORM PANEL --}}
                <template x-if="showPenjabForm">
                    <div @mousedown.stop class="rounded-lg border p-4 space-y-3 max-w-md" style="border-color:var(--border)">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold" x-text="editingPenjab ? 'Edit Jenis Bayar' : 'Jenis Bayar Baru'" style="color:var(--accent-blue)"></h4>
                            <button @mousedown.stop @click="showPenjabForm = false" class="text-xs" style="color:var(--text-muted)">&times;</button>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Kode *</label>
                            <input @mousedown.stop type="text" x-model="penjabForm.kd_pj" maxlength="3" class="form-input text-xs w-full" :disabled="editingPenjab">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Nama Jenis Bayar *</label>
                            <input @mousedown.stop type="text" x-model="penjabForm.png_jawab" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Nama Perusahaan</label>
                            <input @mousedown.stop type="text" x-model="penjabForm.nama_perusahaan" class="form-input text-xs w-full">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Alamat</label>
                            <input @mousedown.stop type="text" x-model="penjabForm.alamat" class="form-input text-xs w-full">
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Telepon</label>
                                <input @mousedown.stop type="text" x-model="penjabForm.no_telp" class="form-input text-xs w-full">
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Attn</label>
                                <input @mousedown.stop type="text" x-model="penjabForm.attn" class="form-input text-xs w-full">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Status</label>
                            <select @mousedown.stop x-model="penjabForm.status" class="form-select text-xs w-full">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button @mousedown.stop @click="savePenjab" class="btn btn-primary text-xs px-3 py-1.5 flex-1">Simpan</button>
                            <button @mousedown.stop @click="showPenjabForm = false" class="btn btn-secondary text-xs px-3 py-1.5">Batal</button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="active === 'lokasifarmasi'">
            <div class="space-y-4">
                <h3 class="text-base font-semibold">Lokasi Tiap Farmasi</h3>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Unit</th><th>Depo Farmasi</th><th>Petugas</th><th>Jam Operasional</th></tr></thead>
                        <tbody>
                            <template x-for="(l, i) in lokasiFarmasi" :key="i">
                                <tr>
                                    <td class="font-medium" x-text="l.unit"></td>
                                    <td style="color:var(--text-secondary)" x-text="l.depo"></td>
                                    <td style="color:var(--text-secondary)" x-text="l.petugas"></td>
                                    <td><span class="badge badge-info" x-text="l.jam"></span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <template x-if="active === 'poli'">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Data Poli</h3>
                    <button @mousedown.stop @click="openPoliForm(null)" class="btn btn-primary text-xs">+ Tambah Poli</button>
                </div>
                <div @mousedown.stop class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead><tr><th>Kode</th><th>Nama Poli</th><th>Registrasi</th><th>Registrasi Lama</th><th>Status</th><th class="w-28"></th></tr></thead>
                        <tbody>
                            <template x-for="(p, i) in poliList" :key="p.kd_poli">
                                <tr>
                                    <td><code class="text-xs px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="p.kd_poli"></code></td>
                                    <td class="font-medium" x-text="p.nm_poli"></td>
                                    <td class="text-xs" x-text="p.registrasi"></td>
                                    <td class="text-xs" x-text="p.registrasilama"></td>
                                    <td><span class="badge" :class="p.status === '1' ? 'badge-success' : 'badge-danger'" x-text="p.status_label"></span></td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <button @mousedown.stop @click="openPoliForm(p)" class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:var(--bg-hover)">Edit</button>
                                            <button @mousedown.stop @click="hapusPoli(p)" class="text-[10px] px-1.5 py-0.5 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!poliList.length">
                                <td colspan="6" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data poli</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- FORM PANEL --}}
                <template x-if="showPoliForm">
                    <div @mousedown.stop class="rounded-lg border p-4 space-y-3 max-w-md" style="border-color:var(--border)">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold" x-text="editingPoli ? 'Edit Poli' : 'Poli Baru'" style="color:var(--accent-blue)"></h4>
                            <button @mousedown.stop @click="showPoliForm = false" class="text-xs" style="color:var(--text-muted)">&times;</button>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Kode Poli *</label>
                            <input @mousedown.stop type="text" x-model="poliForm.kd_poli" maxlength="5" class="form-input text-xs w-full" :disabled="editingPoli">
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Nama Poli *</label>
                            <input @mousedown.stop type="text" x-model="poliForm.nm_poli" class="form-input text-xs w-full">
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Registrasi</label>
                                <input @mousedown.stop type="number" x-model="poliForm.registrasi" class="form-input text-xs w-full">
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Registrasi Lama</label>
                                <input @mousedown.stop type="number" x-model="poliForm.registrasilama" class="form-input text-xs w-full">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Status</label>
                            <select @mousedown.stop x-model="poliForm.status" class="form-select text-xs w-full">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button @mousedown.stop @click="savePoli" class="btn btn-primary text-xs px-3 py-1.5 flex-1">Simpan</button>
                            <button @mousedown.stop @click="showPoliForm = false" class="btn btn-secondary text-xs px-3 py-1.5">Batal</button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="active === 'chatbot'">
            <div class="space-y-4 max-w-lg">
                <h3 class="text-base font-semibold" style="color:var(--accent-blue)">Chatbot AI</h3>
                <p class="text-xs" style="color:var(--text-muted)">Konfigurasi endpoint dan API key untuk asisten AI.</p>

                <div class="rounded-lg border p-4 space-y-3" style="border-color:var(--border)">
                    <div>
                        <label class="form-label text-xs">Endpoint URL</label>
                        <input type="url" x-model="$store.ai.endpoint" placeholder="https://api.openai.com/v1/chat/completions" class="form-input text-xs w-full">
                        <p class="text-[10px] mt-0.5" style="color:var(--text-muted)">Contoh: OpenAI, Ollama, atau endpoint kompatibel OpenAI lainnya</p>
                    </div>
                    <div>
                        <label class="form-label text-xs">API Key</label>
                        <div class="relative">
                            <input :type="showAiKey ? 'text' : 'password'" x-model="$store.ai.apiKey" placeholder="sk-..." class="form-input text-xs w-full pr-8">
                            <button @click="showAiKey = !showAiKey" class="absolute right-1.5 top-1/2 -translate-y-1/2 text-xs" style="color:var(--text-muted)" x-text="showAiKey ? 'S' : 'L'"></button>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-xs">Model</label>
                        <input type="text" x-model="$store.ai.model" placeholder="gpt-3.5-turbo" class="form-input text-xs w-full">
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <span class="w-2 h-2 rounded-full" :class="$store.ai.isConfigured() ? 'bg-green-500' : 'bg-red-500'"></span>
                        <span class="text-xs" x-text="$store.ai.isConfigured() ? 'AI siap digunakan' : 'Endpoint atau API Key belum diisi'"></span>
                    </div>
                    <button @click="$store.ai.save()" class="btn btn-primary text-xs px-4 py-1.5">Simpan Konfigurasi</button>
                </div>

                <div class="rounded-lg border p-4 space-y-2" style="border-color:var(--border);background-color:var(--bg-muted)">
                    <h4 class="text-xs font-semibold">Coba Test Koneksi</h4>
                    <p class="text-[10px]" style="color:var(--text-muted)">Kirim pesan test untuk memastikan konfigurasi berfungsi.</p>
                    <div class="flex gap-2">
                        <input type="text" x-model="testPrompt" placeholder="Pesan test..." class="form-input text-xs flex-1">
                        <button @click="testKoneksi" class="btn btn-secondary text-xs px-3 py-1.5" x-text="testing ? '...' : 'Test'"></button>
                    </div>
                    <div x-show="testResult" class="text-xs rounded p-2" :class="testOk ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300'" x-text="testResult"></div>
                </div>
            </div>
        </template>
    </div>
</div>
