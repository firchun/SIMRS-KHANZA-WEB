<div x-data="{
    query: '',
    showForm: false,
    editing: null,
    selectedPatient: null,
    contextMenu: { show: false, x: 0, y: 0 },
    loading: false,
    error: null,
    pasienList: [],
    form: {
        nm_pasien: '',
        no_ktp: '',
        jk: 'L',
        tmp_lahir: '',
        tgl_lahir: '',
        gol_darah: 'A',
        agama: 'Islam',
        pnd: 'SMA',
        pekerjaan: '',
        no_tlp: '',
        alamat: '',
        stts_nikah: 'BELUM MENIKAH',
        keluarga: 'DIRI SENDIRI',
        namakeluarga: '',
        kd_pj: '',
        no_peserta: '',
    },

    async fetchList() {
        this.loading = true;
        this.error = null;
        try {
            const params = new URLSearchParams();
            if (this.query) params.set('q', this.query);
            const res = await this.$store.api.get('/pasien/search?' + params.toString());
            this.pasienList = res || [];
            if (this.query && !this.pasienList.length) {
                this.pasienList = [];
            }
        } catch (e) {
            this.error = 'Gagal memuat data pasien';
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    get filteredList() {
        return this.pasienList;
    },

    resetForm() {
        this.form = {
            nm_pasien: '',
            no_ktp: '',
            jk: 'L',
            tmp_lahir: '',
            tgl_lahir: '',
            gol_darah: 'A',
            agama: 'Islam',
            pnd: 'SMA',
            pekerjaan: '',
            no_tlp: '',
            alamat: '',
            stts_nikah: 'BELUM MENIKAH',
            keluarga: 'DIRI SENDIRI',
            namakeluarga: '',
            kd_pj: '',
            no_peserta: '',
        };
    },

    openForm(p) {
        if (p) {
            this.editing = p;
            this.form = {
                nm_pasien: p.nama || p.nm_pasien || '',
                no_ktp: p.nik || p.no_ktp || '',
                jk: p.jk || 'L',
                tmp_lahir: p.tmp_lahir || '',
                tgl_lahir: p.tgl_lahir || '',
                gol_darah: p.gol_darah || 'A',
                agama: p.agama || 'Islam',
                pnd: p.pnd || 'SMA',
                pekerjaan: p.pekerjaan || '',
                no_tlp: p.no_tlp || '',
                alamat: p.alamat || '',
                stts_nikah: p.stts_nikah || 'BELUM MENIKAH',
                keluarga: p.keluarga || 'DIRI SENDIRI',
                namakeluarga: p.namakeluarga || '',
                kd_pj: p.kd_pj || '',
                no_peserta: p.no_peserta || '',
            };
        } else {
            this.editing = null;
            this.resetForm();
        }
        this.showForm = true;
    },

    async savePasien() {
        if (!this.form.nm_pasien) return;
        this.loading = true;
        this.error = null;
        try {
            if (this.editing) {
                const no_rkm_medis = this.editing.no_rkm_medis || this.editing.no_rm || this.editing.id;
                await this.$store.api.put('/pasien/update/' + no_rkm_medis, this.form);
                this.$store.api.cacheBust('/pasien/search');
            } else {
                await this.$store.api.post('/pasien/store', this.form);
                this.$store.api.cacheBust('/pasien/search');
            }
            await this.fetchList();
            this.showForm = false;
            this.editing = null;
            this.resetForm();
        } catch (e) {
            this.error = 'Gagal menyimpan data';
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    async hapusPasien(p) {
        if (!confirm('Yakin ingin menghapus ' + (p.nama || p.nm_pasien) + '?')) return;
        this.loading = true;
        this.error = null;
        try {
            const no_rkm_medis = p.no_rkm_medis || p.no_rm || p.id;
            await this.$store.api.delete('/pasien/delete/' + no_rkm_medis);
            this.$store.api.cacheBust('/pasien/search');
            await this.fetchList();
        } catch (e) {
            this.error = 'Gagal menghapus data';
            console.error(e);
        } finally {
            this.loading = false;
        }
    },

    openRiwayat(p, tab) {
        this.$store.windows.open(
            { key: 'pasien-riwayat', label: 'Riwayat - ' + (p.nama || p.nm_pasien), icon: 'list', width: 860, height: 600 },
            { pasien: p, activeTab: tab || 'kunjungan' }
        );
    },

    contextHandler(e, p) {
        e.preventDefault();
        this.selectedPatient = p;
        this.contextMenu = { show: true, x: e.clientX, y: e.clientY };
    },

    closeContext() { this.contextMenu.show = false; },

    umur(tgl) {
        if (!tgl) return '-';
        const d = new Date(tgl);
        const now = new Date();
        let usia = now.getFullYear() - d.getFullYear();
        if (now.getMonth() < d.getMonth() || (now.getMonth() === d.getMonth() && now.getDate() < d.getDate())) usia--;
        return usia + ' thn';
    },

    init() {
        this.fetchList();
    }
}" @mousedown.stop class="flex h-full flex-col" style="color:var(--text-primary)">

    {{-- TOP BAR --}}
    <div @mousedown.stop class="flex items-center justify-between px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="flex items-center gap-2 flex-1">
            <svg class="w-5 h-5" style="color:var(--accent-blue)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-sm font-bold">Data Pasien</h3>
            <div @mousedown.stop class="flex-1 max-w-xs ml-2">
                <input type="text" x-model="query" @input.debounce="fetchList" placeholder="Cari nama / No. RM / NIK..." class="form-input text-xs w-full">
            </div>
            <template x-if="loading">
                <span class="text-xs" style="color:var(--text-muted)">Memuat...</span>
            </template>
        </div>
        <button @mousedown.stop @click="openForm(null)" class="btn btn-primary text-xs px-3 py-1.5 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
            Tambah Pasien
        </button>
    </div>

    {{-- ERROR BANNER --}}
    <template x-if="error">
        <div @mousedown.stop class="px-3 py-1.5 text-xs text-red-600 bg-red-50 dark:bg-red-900/20 border-b flex items-center gap-2" style="border-color:var(--border)">
            <span x-text="error"></span>
            <button @mousedown.stop @click="error = null" class="ml-auto">&times;</button>
        </div>
    </template>

    <div class="flex-1 overflow-y-auto p-3 flex gap-3 min-h-0">
        {{-- PATIENT TABLE --}}
        <div @mousedown.stop class="flex-1 overflow-x-auto rounded border" style="border-color:var(--border)">
            <table class="table-default">
                <thead>
                    <tr>
                        <th>No. RM</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Usia</th>
                        <th>JK</th>
                        <th>Gol Darah</th>
                        <th>Telp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="p in filteredList" :key="p.no_rkm_medis || p.id">
                        <tr @contextmenu="contextHandler($event, p)" class="cursor-context-menu hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td @mousedown.stop class="font-mono text-xs font-medium" x-text="p.no_rkm_medis || p.no_rm"></td>
                            <td @mousedown.stop class="font-mono text-xs" x-text="p.nik || p.no_ktp"></td>
                            <td @mousedown.stop class="font-medium whitespace-nowrap" x-text="p.nama || p.nm_pasien"></td>
                            <td @mousedown.stop class="text-xs" x-text="p.umur || umur(p.tgl_lahir)"></td>
                            <td @mousedown.stop x-text="p.jk"></td>
                            <td @mousedown.stop class="text-xs font-mono" x-text="p.gol_darah || '-'"></td>
                            <td @mousedown.stop class="text-xs" x-text="p.no_tlp || '-'"></td>
                            <td @mousedown.stop>
                                <div class="flex items-center gap-1">
                                    <button @mousedown.stop @click="openForm(p)" class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:var(--bg-hover)">Edit</button>
                                    <button @mousedown.stop @click="hapusPasien(p)" class="text-[10px] px-1.5 py-0.5 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">Hapus</button>
                                    <button @mousedown.stop @click="openRiwayat(p, 'kunjungan')" class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:var(--bg-hover)">Riwayat</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!filteredList.length && !loading">
                        <td colspan="8" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada data pasien</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- FORM PANEL --}}
        <template x-if="showForm">
            <div @mousedown.stop class="w-80 shrink-0 space-y-3 overflow-y-auto" style="max-height:calc(100% - 0.5rem)">
                <div class="rounded-lg border p-3 space-y-2.5" style="border-color:var(--border)">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold" x-text="editing ? 'Edit Pasien' : 'Pasien Baru'" style="color:var(--accent-blue)"></h4>
                        <button @mousedown.stop @click="showForm = false" class="text-xs" style="color:var(--text-muted)">&times;</button>
                    </div>

                    <div>
                        <label class="text-[10px] font-medium" style="color:var(--text-muted)">NIK</label>
                        <input @mousedown.stop type="text" x-model="form.no_ktp" maxlength="20" class="form-input text-xs w-full">
                    </div>
                    <div>
                        <label class="text-[10px] font-medium" style="color:var(--text-muted)">Nama Lengkap *</label>
                        <input @mousedown.stop type="text" x-model="form.nm_pasien" class="form-input text-xs w-full">
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tempat Lahir</label>
                            <input @mousedown.stop type="text" x-model="form.tmp_lahir" class="form-input text-xs w-full">
                        </div>
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Tgl Lahir</label>
                            <input @mousedown.stop type="date" x-model="form.tgl_lahir" class="form-input text-xs w-full">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">JK</label>
                            <select @mousedown.stop x-model="form.jk" class="form-select text-xs w-full">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Gol. Darah</label>
                            <select @mousedown.stop x-model="form.gol_darah" class="form-select text-xs w-full">
                                <option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option><option value="-">-</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Agama</label>
                            <select @mousedown.stop x-model="form.agama" class="form-select text-xs w-full">
                                <option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Pendidikan</label>
                            <select @mousedown.stop x-model="form.pnd" class="form-select text-xs w-full">
                                <option value="TS">Tidak Sekolah</option><option value="TK">TK</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="SLTA/SEDERAJAT">SLTA/Sederajat</option>
                                <option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-medium" style="color:var(--text-muted)">Pekerjaan</label>
                        <input @mousedown.stop type="text" x-model="form.pekerjaan" class="form-input text-xs w-full">
                    </div>
                    <div>
                        <label class="text-[10px] font-medium" style="color:var(--text-muted)">Status Nikah</label>
                        <select @mousedown.stop x-model="form.stts_nikah" class="form-select text-xs w-full">
                            <option value="BELUM MENIKAH">Belum Menikah</option>
                            <option value="MENIKAH">Menikah</option>
                            <option value="JANDA">Janda</option>
                            <option value="DUDHA">Duda</option>
                            <option value="JOMBLO">Jomblo</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-medium" style="color:var(--text-muted)">No. Telepon</label>
                        <input @mousedown.stop type="text" x-model="form.no_tlp" class="form-input text-xs w-full">
                    </div>
                    <div>
                        <label class="text-[10px] font-medium" style="color:var(--text-muted)">Alamat</label>
                        <textarea @mousedown.stop x-model="form.alamat" class="form-input text-xs w-full" rows="2"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Penanggung Jawab</label>
                            <select @mousedown.stop x-model="form.keluarga" class="form-select text-xs w-full">
                                <option value="DIRI SENDIRI">Diri Sendiri</option>
                                <option value="AYAH">Ayah</option>
                                <option value="IBU">Ibu</option>
                                <option value="ISTRI">Istri</option>
                                <option value="SUAMI">Suami</option>
                                <option value="SAUDARA">Saudara</option>
                                <option value="ANAK">Anak</option>
                                <option value="LAIN-LAIN">Lain-lain</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Nama PJ</label>
                            <input @mousedown.stop type="text" x-model="form.namakeluarga" class="form-input text-xs w-full">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">Cara Bayar</label>
                            <input @mousedown.stop type="text" x-model="form.kd_pj" class="form-input text-xs w-full" maxlength="3" placeholder="Kode">
                        </div>
                        <div class="flex-1">
                            <label class="text-[10px] font-medium" style="color:var(--text-muted)">No. Peserta</label>
                            <input @mousedown.stop type="text" x-model="form.no_peserta" class="form-input text-xs w-full" placeholder="BPJS">
                        </div>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button @mousedown.stop @click="savePasien" class="btn btn-primary text-xs px-3 py-1.5 flex-1">Simpan</button>
                        <button @mousedown.stop @click="showForm = false" class="btn btn-secondary text-xs px-3 py-1.5">Batal</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- CONTEXT MENU --}}
    <template x-teleport="body">
        <div x-show="contextMenu.show" @click="closeContext" @contextmenu.prevent="closeContext"
            class="fixed inset-0 z-[9999]" style="background:transparent">
            <div @click.stop @contextmenu.prevent @mousedown.stop
                class="absolute w-48 rounded-lg border shadow-xl py-1"
                :style="'left:' + contextMenu.x + 'px;top:' + contextMenu.y + 'px;background-color:var(--bg-primary);border-color:var(--border);color:var(--text-primary)'">
                <div class="px-3 py-1.5 text-[10px] font-medium border-b" style="border-color:var(--border);color:var(--text-secondary)" x-text="selectedPatient?.nama || selectedPatient?.nm_pasien"></div>

                <button @mousedown.stop @click="closeContext; openRiwayat(selectedPatient, 'kunjungan')" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Kunjungan
                </button>
                <button @mousedown.stop @click="closeContext; openRiwayat(selectedPatient, 'soap')" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Riwayat SOAP
                </button>
                <button @mousedown.stop @click="closeContext; openRiwayat(selectedPatient, 'diagnosa')" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Riwayat Diagnosa
                </button>
                <button @mousedown.stop @click="closeContext; openRiwayat(selectedPatient, 'tindakan')" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    Riwayat Tindakan
                </button>
                <button @mousedown.stop @click="closeContext; openRiwayat(selectedPatient, 'resep')" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-pink-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Riwayat Resep
                </button>

                <div class="border-t my-1" style="border-color:var(--border)"></div>

                <button @mousedown.stop @click="closeContext; openRiwayat(selectedPatient, 'tagihan')" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-yellow-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                    Riwayat Tagihan
                </button>
                <button @mousedown.stop @click="closeContext; openForm(selectedPatient)" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2" style="color:var(--text-primary)" @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Data Pasien
                </button>
            </div>
        </div>
    </template>
</div>