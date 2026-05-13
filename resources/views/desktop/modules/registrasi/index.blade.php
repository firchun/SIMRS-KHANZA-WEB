<div x-data="{
    activeStep: 'pasien',
    searchQuery: '',
    searchResults: [],
    selectedPasien: null,
    showNewForm: false,
    showEditForm: false,
    editPasienData: {},
    contextMenu: null,
    newPasien: { nm_pasien: '', no_ktp: '', tgl_lahir: '', jk: 'L', alamat: '' },
    loading: false,
    poliList: [],
    dpjpList: [],
    petugasList: [],
    selectedPoli: '',
    selectedDokter: '',
    todayQueue: [],
    loketList: ['Loket 1', 'Loket 2', 'Loket 3', 'Loket 4'],
    selectedLoket: localStorage.getItem('reg_loket') || 'Loket 1',
    repeatCount: parseInt(localStorage.getItem('reg_repeat') || '2'),
    customQueueNo: '',
    lastCallNo: null,
    lastCallLoket: null,
    speaking: false,
    showHistory: false,
    callHistory: JSON.parse(localStorage.getItem('reg_call_history') || '[]'),

    get filteredDpjp() {
        if (!this.selectedPoli) return [];
        return this.dpjpList.filter(d => d.kd_poli === this.selectedPoli);
    },

    get nextQueue() {
        return this.todayQueue.length + 1;
    },

    async cariPasien() {
        const q = this.searchQuery.trim();
        if (q.length < 2) { this.searchResults = []; return; }
        try {
            const res = await this.$store.api.get('/pasien/search?q=' + encodeURIComponent(q));
            this.searchResults = res || [];
        } catch (e) {
            this.searchResults = [];
        }
    },

    pilihPasien(p) {
        this.selectedPasien = p;
        this.searchQuery = '';
        this.searchResults = [];
        this.showNewForm = false;
        this.activeStep = 'registrasi';
    },

    resetForm() {
        this.selectedPasien = null;
        this.selectedPoli = '';
        this.selectedDokter = '';
        this.searchQuery = '';
        this.searchResults = [];
        this.showNewForm = false;
        this.showEditForm = false;
        this.contextMenu = null;
        this.newPasien = { nm_pasien: '', no_ktp: '', tgl_lahir: '', jk: 'L', alamat: '' };
        this.activeStep = 'pasien';
    },

    editPasien() {
        this.editPasienData = {
            no_rkm_medis: this.selectedPasien.no_rkm_medis || this.selectedPasien.id,
            nm_pasien: this.selectedPasien.nama || this.selectedPasien.nm_pasien || '',
            no_ktp: this.selectedPasien.nik || this.selectedPasien.no_ktp || '',
            tgl_lahir: this.selectedPasien.tgl_lahir || '',
            jk: this.selectedPasien.jk || 'L',
            alamat: this.selectedPasien.alamat || '',
        };
        this.showEditForm = true;
        this.contextMenu = null;
    },

    async simpanEditPasien() {
        if (!this.editPasienData.nm_pasien.trim() || !this.editPasienData.no_ktp.trim()) return;
        try {
            await this.$store.api.put('/pasien/update/' + this.editPasienData.no_rkm_medis, {
                nm_pasien: this.editPasienData.nm_pasien,
                no_ktp: this.editPasienData.no_ktp,
                tgl_lahir: this.editPasienData.tgl_lahir,
                jk: this.editPasienData.jk,
                alamat: this.editPasienData.alamat,
            });
            this.$store.api.cacheBust('/pasien/search');
            this.selectedPasien.nama = this.editPasienData.nm_pasien;
            this.selectedPasien.nik = this.editPasienData.no_ktp;
            this.showEditForm = false;
        } catch (e) {
            alert('Gagal menyimpan');
        }
    },

    async hapusPasien() {
        if (!confirm('Yakin ingin menghapus data ' + (this.selectedPasien.nama || this.selectedPasien.nm_pasien) + '?')) return;
        try {
            await this.$store.api.delete('/pasien/delete/' + (this.selectedPasien.no_rkm_medis || this.selectedPasien.id));
            this.$store.api.cacheBust('/pasien/search');
            this.selectedPasien = null;
            this.contextMenu = null;
            this.activeStep = 'pasien';
        } catch (e) {
            alert('Gagal menghapus');
        }
    },

    openContextMenu(e) {
        e.preventDefault();
        this.contextMenu = { x: e.clientX, y: e.clientY };
    },

    closeContextMenu() {
        this.contextMenu = null;
    },

    doBridging(jenis) {
        alert('Bridging ' + jenis + ' untuk ' + (this.selectedPasien.nama || this.selectedPasien.nm_pasien));
        this.contextMenu = null;
    },

    callNext() {
        const next = this.todayQueue.find(q => q.status === 'Menunggu');
        if (!next) return;
        this.todayQueue.forEach(q => { if (q.status === 'Dipanggil') q.status = 'Selesai'; });
        next.status = 'Dipanggil';
        this.lastCallNo = next.no_reg || next.no_rawat;
        this.lastCallLoket = this.selectedLoket;
        this.addHistory(next.no_reg || next.no_rawat, this.selectedLoket, 'next');
        this.speakCall(next.no_reg || next.no_rawat, this.selectedLoket);
    },

    repeatLastCall() {
        if (this.lastCallNo === null) return;
        this.speakCall(this.lastCallNo, this.lastCallLoket);
    },

    callCustom() {
        const no = parseInt(this.customQueueNo);
        if (!no || no < 1) return;
        this.lastCallNo = no;
        this.lastCallLoket = this.selectedLoket;
        this.addHistory(no, this.selectedLoket, 'custom');
        this.speakCall(no, this.selectedLoket);
        this.customQueueNo = '';
    },

    speakCall(no, loket) {
        if (!window.speechSynthesis) return;
        window.speechSynthesis.cancel();
        const loketNum = loket.replace(/\D/g, '') || '1';
        const ulang = this.repeatCount;
        let count = 0;
        this.speaking = true;
        const speak = () => {
            if (count >= ulang) { this.speaking = false; return; }
            const utterance = new SpeechSynthesisUtterance(`Panggilan untuk nomor antrian ${no} ke loket ${loketNum}`);
            utterance.lang = 'id-ID';
            utterance.rate = 0.9;
            utterance.onend = () => { count++; setTimeout(speak, 800); };
            utterance.onerror = () => { this.speaking = false; };
            speechSynthesis.speak(utterance);
        };
        speak();
    },

    addHistory(no, loket, type) {
        this.callHistory.unshift({
            no, loket, type,
            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        });
        if (this.callHistory.length > 20) this.callHistory = this.callHistory.slice(0, 20);
        localStorage.setItem('reg_call_history', JSON.stringify(this.callHistory));
    },

    async simpanPasienBaru() {
        if (!this.newPasien.nm_pasien.trim() || !this.newPasien.no_ktp.trim()) return;
        try {
            const res = await this.$store.api.post('/pasien/store', {
                nm_pasien: this.newPasien.nm_pasien,
                no_ktp: this.newPasien.no_ktp,
                tgl_lahir: this.newPasien.tgl_lahir,
                jk: this.newPasien.jk,
                alamat: this.newPasien.alamat,
            });
            this.$store.api.cacheBust('/pasien/search');
            this.selectedPasien = {
                id: res.no_rkm_medis,
                no_rkm_medis: res.no_rkm_medis,
                no_rm: res.no_rkm_medis,
                nama: res.nm_pasien,
                nm_pasien: res.nm_pasien,
                nik: res.no_ktp,
                no_ktp: res.no_ktp,
                tgl_lahir: res.tgl_lahir,
                jk: res.jk,
                alamat: res.alamat,
            };
            this.showNewForm = false;
            this.activeStep = 'registrasi';
        } catch (e) {
            alert('Gagal menyimpan pasien');
        }
    },

    async simpanRegistrasi() {
        if (!this.selectedPasien || !this.selectedPoli || !this.selectedDokter) return;
        try {
            await this.$store.api.post('/registrasi/store', {
                no_rkm_medis: this.selectedPasien.no_rkm_medis || this.selectedPasien.id,
                kd_poli: this.selectedPoli,
                kd_dokter: this.selectedDokter,
            });
            this.$store.api.cacheBust('/registrasi/today');
            await this.fetchTodayQueue();
            this.resetForm();
        } catch (e) {
            alert('Gagal menyimpan registrasi');
        }
    },

    saveLoket() { localStorage.setItem('reg_loket', this.selectedLoket); },
    saveRepeat() { localStorage.setItem('reg_repeat', this.repeatCount.toString()); },

    async fetchPoli() {
        try {
            const res = await this.$store.api.get('/jadwal/poli');
            this.poliList = (res || []).filter(p => p.kd_poli !== 'IGDK');
        } catch (e) { this.poliList = []; }
    },

    async fetchDpjp() {
        try {
            const res = await this.$store.api.get('/registrasi/dokter-by-poli');
            this.dpjpList = res || [];
        } catch (e) { this.dpjpList = []; }
    },

    async fetchTodayQueue() {
        try {
            const res = await this.$store.api.get('/registrasi/today');
            this.todayQueue = res || [];
        } catch (e) { this.todayQueue = []; }
    },

    formatDate(tgl) {
        if (!tgl) return '-';
        const d = new Date(tgl + 'T00:00:00');
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    },

    getStatusStyle(status) {
        if (status === 'Menunggu') return 'background-color:rgba(245,158,11,0.1);color:rgb(245,158,11)';
        if (status === 'Dipanggil') return 'background-color:rgba(59,130,246,0.1);color:var(--accent-blue)';
        if (status === 'Selesai') return 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)';
        return '';
    },

    init() {
        this.fetchPoli();
        this.fetchDpjp();
        this.fetchTodayQueue();
    }
}" @mousedown.stop class="flex flex-col h-full" style="color:var(--text-primary)">

    {{-- Toolbar Row 1: Core actions --}}
    <div @mousedown.stop class="flex items-center gap-2 px-4 py-2 border-b shrink-0 flex-wrap" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="flex items-center gap-1.5">
            <span class="text-[10px] font-medium" style="color:var(--text-muted)">Loket</span>
            <select @mousedown.stop x-model="selectedLoket" @change="saveLoket"
                class="form-input text-xs py-1 px-2 w-22" style="background-color:var(--bg-input)">
                <template x-for="lok in loketList" :key="lok">
                    <option :value="lok" x-text="lok"></option>
                </template>
            </select>
        </div>

        <div class="w-px h-5" style="background-color:var(--border)"></div>

        <button @mousedown.stop @click="callNext"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium transition-colors"
            style="background-color:var(--accent-blue);color:#fff"
            @mouseenter="$el.style.opacity='0.9'" @mouseleave="$el.style.opacity='1'">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            Panggil Selanjutnya
        </button>

        <button @mousedown.stop @click="repeatLastCall" :disabled="lastCallNo === null"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium transition-colors"
            :style="lastCallNo !== null ? 'background-color:rgba(245,158,11,0.15);color:rgb(245,158,11)' : 'background-color:var(--bg-elevated);color:var(--text-muted)'"
            @mouseenter="lastCallNo !== null ? $el.style.backgroundColor='rgba(245,158,11,0.25)' : ''"
            @mouseleave="lastCallNo !== null ? $el.style.backgroundColor='rgba(245,158,11,0.15)' : ''">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/>
            </svg>
            Ulangi
        </button>

        <div class="w-px h-5" style="background-color:var(--border)"></div>

        <div class="flex items-center gap-1">
            <span class="text-[10px] font-medium" style="color:var(--text-muted)">No.</span>
            <input @mousedown.stop type="number" x-model="customQueueNo" @keydown.enter="callCustom" min="1" placeholder="0"
                class="form-input text-xs py-1 px-2 w-14 text-center" style="background-color:var(--bg-input)">
            <button @mousedown.stop @click="callCustom" :disabled="!customQueueNo"
                class="px-2.5 py-1 rounded text-xs font-medium transition-colors"
                style="background-color:var(--accent-blue);color:#fff"
                :style="!customQueueNo ? 'opacity:0.4' : ''"
                @mouseenter="customQueueNo ? $el.style.opacity='0.9' : ''"
                @mouseleave="customQueueNo ? $el.style.opacity='1' : ''">Panggil</button>
        </div>
    </div>

    {{-- Toolbar Row 2: Settings & indicators --}}
    <div @mousedown.stop class="flex items-center gap-2 px-4 py-2 border-b shrink-0 flex-wrap" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="flex items-center gap-1.5">
            <span class="text-[10px] font-medium" style="color:var(--text-muted)">Ulang</span>
            <input @mousedown.stop type="number" x-model="repeatCount" @change="saveRepeat" min="1" max="10"
                class="form-input text-xs py-1 px-1 w-12 text-center" style="background-color:var(--bg-input)">
            <span class="text-[10px]" style="color:var(--text-muted)">kali</span>
        </div>

        <button @mousedown.stop @click="repeatLastCall" :disabled="lastCallNo === null || speaking"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium transition-colors"
            :style="lastCallNo !== null && !speaking
                ? 'background-color:rgba(34,197,94,0.12);color:rgb(34,197,94)'
                : 'background-color:var(--bg-elevated);color:var(--text-muted)'"
            @mouseenter="lastCallNo !== null && !speaking ? $el.style.backgroundColor='rgba(34,197,94,0.2)' : ''"
            @mouseleave="lastCallNo !== null && !speaking ? $el.style.backgroundColor='rgba(34,197,94,0.12)' : ''">
            <template x-if="!speaking">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/>
                </svg>
            </template>
            <template x-if="speaking">
                <svg class="w-3.5 h-3.5 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
                </svg>
            </template>
            <span x-text="speaking ? 'Memutar...' : 'Suara'"></span>
        </button>

        <div class="w-px h-5" style="background-color:var(--border)"></div>

        <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded text-xs font-semibold"
            :style="lastCallNo ? 'background-color:rgba(59,130,246,0.1);color:var(--accent-blue)' : ''">
            <span style="color:var(--text-muted)">Antrian:</span>
            <span x-text="lastCallNo ? 'No. ' + lastCallNo : '-'"></span>
        </div>

        <button @mousedown.stop @click="showHistory = !showHistory"
            class="flex items-center gap-1 px-2 py-1.5 rounded text-xs transition-colors"
            :style="showHistory ? 'background-color:var(--bg-hover)' : ''"
            style="color:var(--text-muted)"
            @mouseenter="$el.style.backgroundColor='var(--bg-hover)'"
            @mouseleave="showHistory ? '' : ($el.style.backgroundColor='transparent')">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            Riwayat
        </button>

        <div class="flex-1"></div>

        <button @mousedown.stop @click="resetForm"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-medium transition-colors"
            style="background-color:var(--accent-blue);color:#fff"
            @mouseenter="$el.style.opacity='0.9'" @mouseleave="$el.style.opacity='1'">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Regis Baru
        </button>
    </div>

    {{-- History bar --}}
    <template x-if="showHistory && callHistory.length > 0">
        <div @mousedown.stop class="flex items-center gap-1.5 px-4 py-1.5 border-b overflow-x-auto shrink-0" style="border-color:var(--border);background-color:var(--bg-elevated)">
            <span class="text-[10px] font-semibold uppercase tracking-wide shrink-0" style="color:var(--text-muted)">Riwayat:</span>
            <template x-for="(h, i) in callHistory.slice(0, 15)" :key="i">
                <span class="flex items-center gap-1 px-2 py-0.5 rounded text-[10px] shrink-0"
                    :style="i === 0 ? 'background-color:var(--bg-muted)' : ''"
                    style="color:var(--text-primary)">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0"
                        :style="h.type === 'next' ? 'background-color:var(--accent-blue)' : 'background-color:rgb(245,158,11)'"></span>
                    <span x-text="'No.' + h.no"></span>
                    <span style="color:var(--text-muted)" x-text="'→ ' + h.loket"></span>
                    <span style="color:var(--text-muted)" x-text="h.time"></span>
                </span>
            </template>
        </div>
    </template>

    {{-- Main --}}
    <div @mousedown.stop class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0">

        {{-- Step 1: Cari / Pilih Pasien --}}
        <div @mousedown.stop class="rounded-lg border" style="border-color:var(--border);background-color:var(--bg-elevated)">
            <div class="flex items-center gap-2 px-4 py-2.5 border-b" style="border-color:var(--border);background-color:var(--bg-muted)">
                <div class="flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold" style="background-color:var(--accent-blue);color:#fff">1</div>
                <span class="text-xs font-semibold">Data Pasien</span>
                <template x-if="selectedPasien">
                    <span class="flex items-center gap-1 ml-auto text-[10px]" style="color:var(--accent-green)">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span x-text="selectedPasien.nama || selectedPasien.nm_pasien"></span>
                    </span>
                </template>
            </div>
            <div class="p-4">
                <template x-if="!selectedPasien && !showNewForm && !showEditForm">
                    <div>
                        <div class="flex gap-2 mb-3">
                            <div class="flex-1 relative">
                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                                </svg>
                                <input @mousedown.stop type="text" x-model="searchQuery" @input.debounce="cariPasien" placeholder="Cari pasien (nama / No. RM / NIK)..."
                                    class="form-input text-xs w-full pl-8 pr-2 py-2" style="background-color:var(--bg-input)">
                            </div>
                            <button @mousedown.stop @click="showNewForm = true"
                                class="flex items-center gap-1 px-3 py-2 rounded text-xs font-medium transition-colors shrink-0"
                                style="background-color:var(--accent-blue);color:#fff"
                                @mouseenter="$el.style.opacity='0.9'" @mouseleave="$el.style.opacity='1'">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Pasien Baru
                            </button>
                        </div>
                        <template x-if="searchResults.length > 0">
                            <div class="space-y-1 max-h-48 overflow-y-auto">
                                <template x-for="p in searchResults" :key="p.no_rkm_medis || p.id">
                                    <button @mousedown.stop @click="pilihPasien(p)"
                                        class="w-full text-left flex items-center gap-3 px-3 py-2 rounded transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                        style="color:var(--text-primary)">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold" style="background-color:var(--accent-blue);color:#fff" x-text="(p.nama || p.nm_pasien).charAt(0)"></div>
                                        <div class="flex-1">
                                            <div class="text-xs font-medium" x-text="p.nama || p.nm_pasien"></div>
                                            <div class="text-[10px]" style="color:var(--text-muted)">
                                                <span x-text="p.no_rm || p.no_rkm_medis"></span>
                                                <span class="mx-1">•</span>
                                                <span x-text="p.nik || p.no_ktp"></span>
                                            </div>
                                        </div>
                                        <span class="text-[10px]" style="color:var(--text-muted)" x-text="p.jk === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- New Patient Form --}}
                <template x-if="showNewForm && !selectedPasien">
                    <div @mousedown.stop class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Nama Lengkap <span style="color:var(--accent-red)">*</span></label>
                                <input @mousedown.stop type="text" x-model="newPasien.nm_pasien" placeholder="Nama pasien"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            </div>
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">NIK <span style="color:var(--accent-red)">*</span></label>
                                <input @mousedown.stop type="text" x-model="newPasien.no_ktp" placeholder="Nomor NIK"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Tanggal Lahir</label>
                                <input @mousedown.stop type="date" x-model="newPasien.tgl_lahir"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            </div>
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Jenis Kelamin</label>
                                <select @mousedown.stop x-model="newPasien.jk"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Alamat</label>
                            <textarea @mousedown.stop x-model="newPasien.alamat" rows="2" placeholder="Alamat lengkap"
                                class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button @mousedown.stop @click="showNewForm = false"
                                class="px-3 py-1.5 rounded text-xs transition-colors" style="color:var(--text-primary)"
                                @mouseenter="$el.style.backgroundColor='var(--bg-hover)'"
                                @mouseleave="$el.style.backgroundColor='transparent'">Batal</button>
                            <button @mousedown.stop @click="simpanPasienBaru"
                                class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
                                style="background-color:var(--accent-blue);color:#fff"
                                @mouseenter="$el.style.opacity='0.9'" @mouseleave="$el.style.opacity='1'">Simpan & Pilih</button>
                        </div>
                    </div>
                </template>

                {{-- Edit Patient Form --}}
                <template x-if="showEditForm">
                    <div @mousedown.stop class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Nama Lengkap <span style="color:var(--accent-red)">*</span></label>
                                <input @mousedown.stop type="text" x-model="editPasienData.nm_pasien" placeholder="Nama pasien"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            </div>
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">NIK <span style="color:var(--accent-red)">*</span></label>
                                <input @mousedown.stop type="text" x-model="editPasienData.no_ktp" placeholder="Nomor NIK"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Tanggal Lahir</label>
                                <input @mousedown.stop type="date" x-model="editPasienData.tgl_lahir"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            </div>
                            <div>
                                <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Jenis Kelamin</label>
                                <select @mousedown.stop x-model="editPasienData.jk"
                                    class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Alamat</label>
                            <textarea @mousedown.stop x-model="editPasienData.alamat" rows="2" placeholder="Alamat lengkap"
                                class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button @mousedown.stop @click="showEditForm = false"
                                class="px-3 py-1.5 rounded text-xs transition-colors" style="color:var(--text-primary)"
                                @mouseenter="$el.style.backgroundColor='var(--bg-hover)'"
                                @mouseleave="$el.style.backgroundColor='transparent'">Batal</button>
                            <button @mousedown.stop @click="simpanEditPasien"
                                class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
                                style="background-color:var(--accent-blue);color:#fff"
                                @mouseenter="$el.style.opacity='0.9'" @mouseleave="$el.style.opacity='1'">Simpan Perubahan</button>
                        </div>
                    </div>
                </template>

                {{-- Selected Patient Card --}}
                <template x-if="selectedPasien && !showNewForm && !showEditForm">
                    <div @contextmenu="openContextMenu($event)" class="relative">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold" style="background-color:var(--accent-blue);color:#fff">
                                <span x-text="(selectedPasien.nama || selectedPasien.nm_pasien).charAt(0)"></span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold" x-text="selectedPasien.nama || selectedPasien.nm_pasien"></div>
                                <div class="text-[10px]" style="color:var(--text-muted)">
                                    <span x-text="selectedPasien.no_rm || selectedPasien.no_rkm_medis"></span>
                                    <span class="mx-1">•</span>
                                    <span x-text="selectedPasien.nik || selectedPasien.no_ktp"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @mousedown.stop @click="editPasien"
                                    class="flex items-center gap-1 px-2 py-1 rounded text-[10px] transition-colors" style="color:var(--accent-blue)"
                                    @mouseenter="$el.style.backgroundColor='rgba(59,130,246,0.1)'"
                                    @mouseleave="$el.style.backgroundColor='transparent'">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </button>
                                <button @mousedown.stop @click="hapusPasien"
                                    class="flex items-center gap-1 px-2 py-1 rounded text-[10px] transition-colors" style="color:var(--accent-red)"
                                    @mouseenter="$el.style.backgroundColor='rgba(239,68,68,0.1)'"
                                    @mouseleave="$el.style.backgroundColor='transparent'">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                    </svg>
                                    Hapus
                                </button>
                                <button @mousedown.stop @click="selectedPasien = null; searchQuery = ''"
                                    class="text-[10px] px-2 py-1 rounded transition-colors" style="color:var(--text-muted)"
                                    @mouseenter="$el.style.backgroundColor='var(--bg-hover)'"
                                    @mouseleave="$el.style.backgroundColor='transparent'">Ganti</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-xs" style="color:var(--text-muted)">
                            <div>
                                <span class="text-[10px] font-medium uppercase block" style="color:var(--text-muted)">Tgl Lahir</span>
                                <span style="color:var(--text-primary)" x-text="formatDate(selectedPasien.tgl_lahir)"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-medium uppercase block" style="color:var(--text-muted)">Jenis Kelamin</span>
                                <span style="color:var(--text-primary)" x-text="selectedPasien.jk === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-medium uppercase block" style="color:var(--text-muted)">Alamat</span>
                                <span style="color:var(--text-primary)" x-text="selectedPasien.alamat"></span>
                            </div>
                        </div>

                        {{-- Context Menu --}}
                        <template x-teleport="body">
                            <div x-show="contextMenu" x-cloak
                                :style="'position:fixed;left:' + contextMenu?.x + 'px;top:' + contextMenu?.y + 'px;z-index:10000'"
                                @click.outside="closeContextMenu"
                                class="w-44 rounded-lg shadow-xl py-1 overflow-hidden"
                                style="background-color:var(--bg-elevated);border:1px solid var(--border)">
                                <div class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wide" style="color:var(--text-muted)">Bridging</div>
                                <button @mousedown.stop @click="doBridging('SEP BPJS')"
                                    class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                    style="color:var(--text-primary)">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--accent-blue)">
                                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    SEP BPJS
                                </button>
                                <button @mousedown.stop @click="doBridging('Surat Kontrol')"
                                    class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                    style="color:var(--text-primary)">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--accent-green)">
                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Surat Kontrol
                                </button>
                                <button @mousedown.stop @click="doBridging('PRB')"
                                    class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                    style="color:var(--text-primary)">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--accent-yellow)">
                                        <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    PRB
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- Step 2: Registrasi Baru --}}
        <div @mousedown.stop class="rounded-lg border" style="border-color:var(--border);background-color:var(--bg-elevated)">
            <div class="flex items-center gap-2 px-4 py-2.5 border-b" style="border-color:var(--border);background-color:var(--bg-muted)">
                <div class="flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold"
                    :style="selectedPasien ? 'background-color:var(--accent-blue);color:#fff' : 'background-color:var(--bg-hover);color:var(--text-muted)'">2</div>
                <span class="text-xs font-semibold">Registrasi Baru</span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">Poliklinik Tujuan <span style="color:var(--accent-red)">*</span></label>
                        <select @mousedown.stop x-model="selectedPoli"
                            class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)">
                            <option value="">-- Pilih Poliklinik --</option>
                            <template x-for="poli in poliList" :key="poli.kd_poli">
                                <option :value="poli.kd_poli" x-text="poli.nm_poli + ' (' + poli.kd_poli + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-medium uppercase tracking-wide" style="color:var(--text-muted)">DPJP / Dokter <span style="color:var(--accent-red)">*</span></label>
                        <select @mousedown.stop x-model="selectedDokter"
                            class="form-input text-xs w-full mt-1" style="background-color:var(--bg-input)"
                            :disabled="!selectedPoli">
                            <option value="">-- Pilih Dokter --</option>
                            <template x-for="dokter in filteredDpjp" :key="dokter.kd_dokter">
                                <option :value="dokter.kd_dokter" x-text="dokter.nm_dokter"></option>
                            </template>
                        </select>
                        <template x-if="!selectedPoli">
                            <p class="text-[10px] mt-1" style="color:var(--text-muted)">Pilih poliklinik terlebih dahulu</p>
                        </template>
                    </div>
                </div>
                <template x-if="selectedPoli && selectedDokter && selectedPasien">
                    <div class="mt-4 p-3 rounded-lg flex items-center justify-between" style="background-color:var(--bg-muted)">
                        <div>
                            <div class="text-xs font-semibold">Konfirmasi Registrasi</div>
                            <div class="text-[10px]" style="color:var(--text-muted)">
                                <span x-text="selectedPasien.nama || selectedPasien.nm_pasien"></span>
                                <span class="mx-1">→</span>
                                <span x-text="poliList.find(p => p.kd_poli == selectedPoli)?.nm_poli"></span>
                                <span class="mx-1">•</span>
                                <span x-text="dpjpList.find(d => d.kd_dokter == selectedDokter)?.nm_dokter"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold" style="color:var(--accent-blue)" x-text="'No. ' + nextQueue"></div>
                            <div class="text-[10px]" style="color:var(--text-muted)">Nomor Antrian</div>
                        </div>
                    </div>
                </template>
                <div class="mt-4 flex gap-2">
                    <button @mousedown.stop @click="simpanRegistrasi"
                        :disabled="!selectedPasien || !selectedPoli || !selectedDokter"
                        class="flex items-center gap-1.5 px-4 py-2 rounded text-xs font-medium transition-colors"
                        style="background-color:var(--accent-blue);color:#fff"
                        :style="(!selectedPasien || !selectedPoli || !selectedDokter) ? 'opacity:0.4' : ''"
                        @mouseenter="(selectedPasien && selectedPoli && selectedDokter) ? $el.style.opacity='0.9' : ''"
                        @mouseleave="(selectedPasien && selectedPoli && selectedDokter) ? $el.style.opacity='1' : ''">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                        </svg>
                        Simpan Registrasi
                    </button>
                </div>
            </div>
        </div>

        {{-- Antrian Hari Ini --}}
        <div @mousedown.stop class="rounded-lg border" style="border-color:var(--border);background-color:var(--bg-elevated)">
            <div class="flex items-center gap-2 px-4 py-2.5 border-b" style="border-color:var(--border);background-color:var(--bg-muted)">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--accent-blue)">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs font-semibold">Antrian Hari Ini</span>
                <span class="text-[10px] ml-auto" style="color:var(--text-muted)" x-text="todayQueue.length + ' pasien'"></span>
            </div>
            <div class="overflow-y-auto max-h-48">
                <table class="w-full text-xs">
                    <thead>
                        <tr style="color:var(--text-muted)" class="text-[10px] uppercase tracking-wide">
                            <th class="text-left px-4 py-2 font-medium">No. Reg</th>
                            <th class="text-left px-4 py-2 font-medium">Nama</th>
                            <th class="text-left px-4 py-2 font-medium">Poliklinik</th>
                            <th class="text-left px-4 py-2 font-medium">DPJP</th>
                            <th class="text-left px-4 py-2 font-medium">Jam</th>
                            <th class="text-center px-4 py-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(q, i) in todayQueue" :key="q.no_rawat">
                            <tr style="color:var(--text-primary)"
                                :style="i % 2 === 0 ? 'background-color:transparent' : 'background-color:var(--bg-muted)'"
                                class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-2 font-mono font-medium text-[10px]" x-text="q.no_reg"></td>
                                <td class="px-4 py-2 whitespace-nowrap" x-text="q.nm_pasien"></td>
                                <td class="px-4 py-2" x-text="q.nm_poli"></td>
                                <td class="px-4 py-2 whitespace-nowrap" x-text="q.nm_dokter"></td>
                                <td class="px-4 py-2 text-[10px]" x-text="q.jam_reg"></td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium"
                                        :style="getStatusStyle(q.status)"
                                        x-text="q.status"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!todayQueue.length">
                            <td colspan="6" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada registrasi hari ini</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>