import './bootstrap';
import Alpine from 'alpinejs';
import { windowManager } from './desktop/window-manager';
import { api } from './desktop/api';

Alpine.store('windows', windowManager);
Alpine.store('api', api);
Alpine.store('ui', {
    startMenuOpen: false,
    notifOpen: false,
    toggleStartMenu() { this.startMenuOpen = !this.startMenuOpen; this.notifOpen = false; },
    closeStartMenu() { this.startMenuOpen = false; },
    toggleNotif() { this.notifOpen = !this.notifOpen; this.startMenuOpen = false; },
    closeNotif() { this.notifOpen = false; }
});

Alpine.store('errors', {
    items: [],
    add(info) {
        this.items.push(info);
    },
    clear() {
        this.items = [];
    }
});

Alpine.store('theme', {
    mode: localStorage.getItem('theme') || 'light',
    init() {
        this.apply();
    },
    toggle() {
        this.mode = this.mode === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.mode);
        this.apply();
    },
    apply() {
        document.documentElement.classList.toggle('dark', this.mode === 'dark');
    }
});

Alpine.store('ai', {
    endpoint: localStorage.getItem('ai_endpoint') || '',
    apiKey: localStorage.getItem('ai_api_key') || '',
    model: localStorage.getItem('ai_model') || 'gpt-3.5-turbo',
    save() {
        localStorage.setItem('ai_endpoint', this.endpoint);
        localStorage.setItem('ai_api_key', this.apiKey);
        localStorage.setItem('ai_model', this.model);
    },
    isConfigured() {
        return !!(this.endpoint && this.apiKey);
    }
});

Alpine.store('igdFilter', {
    tgl1: new Date().toISOString().slice(0,10),
    tgl2: new Date().toISOString().slice(0,10),
    q: ''
});

Alpine.store('auth', {
    user: null,
    token: localStorage.getItem('token'),
    async init() {
        if (this.token) {
            try {
                const res = await api.get('/user');
                this.user = res;
            } catch {
                this.token = null;
                localStorage.removeItem('token');
            }
        }
    },
    async login(id_user, password) {
        const res = await fetch('/login', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_user, password })
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Login failed');
        this.token = json.token;
        this.user = json.user;
        localStorage.setItem('token', this.token);
        return json;
    },
    logout() {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
    }
});

Alpine.data('desktopIcon', (module) => ({
    open() {
        const store = Alpine.store('windows');
        const win = {
            id: 'win-' + (++store.counter),
            module: module.key,
            title: module.label,
            icon: module.icon,
            data: {},
            zIndex: ++store.zIndex,
            minimized: false,
            x: 40 + (store.items.length * 20) % 600,
            y: 40 + (store.items.length * 20) % 400,
            width: module.width || 640,
            height: module.height || 480,
        };
        store.items = [...store.items, win];
    }
}));

Alpine.data('desktopWidget', () => ({
    stats: null,
    loading: true,
    init() {
        this.loadStats();
    },
    async loadStats() {
        this.loading = true;
        try {
            this.stats = await this.$store.api.get('/dashboard/stats');
        } catch {}
        finally { this.loading = false; }
    }
}));

Alpine.data('taskbarSearch', () => ({
    query: '',
    results: [],
    open: false,
    selectedIndex: -1,
    loading: false,
    async search() {
        if (this.query.length < 2) { this.results = []; this.open = false; return; }
        this.loading = true;
        try {
            this.results = await this.$store.api.get('/pasien/search-regperiksa?q=' + encodeURIComponent(this.query));
            this.open = this.results.length > 0;
            this.selectedIndex = -1;
        } catch { this.results = []; this.open = false; }
        finally { this.loading = false; }
    },
    selectPatient(p) {
        if (p.module_key) {
            const mod = window.findModuleByKey ? window.findModuleByKey(p.module_key) : null;
            if (mod) {
                this.$store.windows.open(mod, p.data);
            }
        }
        this.query = '';
        this.results = [];
        this.open = false;
    },
    keydown(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
        } else if (e.key === 'Enter' && this.selectedIndex >= 0) {
            e.preventDefault();
            this.selectPatient(this.results[this.selectedIndex]);
        }
    }
}));

Alpine.data('ralanQueue', () => ({
    queue: [], queueCount: 0, filterPoli: '', tanggal: new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}),
    async init() { await this.loadQueue(); },
    async loadQueue() { try { const params = new URLSearchParams(); if (this.filterPoli) params.set('poli', this.filterPoli); this.queue = (await this.$store.api.get('/ralan/queue?' + params.toString())) || []; this.queueCount = this.queue.length; } catch {} },
    startExam(q) { this.$store.windows.open({key: 'ralan-examination', label: 'Pemeriksaan - ' + q.nama_pasien, icon: 'stethoscope', width: 860, height: 640}, {kunjungan: q}); }
}));

Alpine.data('ralanExamination', () => ({
    selectedPatient: null, pasienInfo: '', examStarted: false, saving: false, kunjunganId: null,
    exam: { sistolik: 120, diastolik: 80, nadi: 80, suhu: 36.5, rr: 20, spo2: 98, tinggi: '', berat: '', keluhan: '', diagnosis: '', tindakan: '' },
    newResep: { obat: '', aturan_pakai: '', satuan: '', jumlah: 1 }, resepList: [],
    init() {
        const data = this.$store.windows.items.find(w => w.id === this.$el.closest('[data-window-id]')?.dataset?.windowId)?.data;
        if (data?.kunjungan) { this.selectedPatient = data.kunjungan; this.pasienInfo = data.kunjungan.poli; this.examStarted = true; this.kunjunganId = data.kunjungan.id; }
    },
    addResep() { if (!this.newResep.obat) return; this.resepList.push({...this.newResep}); this.newResep = { obat: '', aturan_pakai: '', satuan: '', jumlah: 1 }; },
    async saveExamination() {
        this.saving = true;
        try {
            if (this.kunjunganId) { await this.$store.api.put('/ralan/' + this.kunjunganId + '/examination', { diagnosis: this.exam.diagnosis, tindakan: this.exam.tindakan, status_pulang: 'selesai' }); }
            for (const r of this.resepList) { await this.$store.api.post('/ralan/' + this.kunjunganId + '/resep', { obat: r.obat, aturan_pakai: r.aturan_pakai, satuan: r.satuan, jumlah: r.jumlah }); }
            alert('Pemeriksaan tersimpan');
        } catch (e) { alert(e.message); }
        finally { this.saving = false; }
    },
    async completeExamination() { await this.saveExamination(); this.examStarted = false; this.selectedPatient = null; this.resepList = []; this.exam = { sistolik: 120, diastolik: 80, nadi: 80, suhu: 36.5, rr: 20, spo2: 98, tinggi: '', berat: '', keluhan: '', diagnosis: '', tindakan: '' }; this.resepList = []; }
}));

Alpine.data('ralanBilling', () => ({
    bills: [], total: 0, filterDate: new Date().toISOString().slice(0,10), tanggal: new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}),
    async init() { await this.loadBilling(); },
    async loadBilling() { try { this.bills = (await this.$store.api.get('/ralan/dashboard?tgl=' + this.filterDate)).polis || []; this.total = this.bills.reduce((s, b) => s + (b.total_biaya || 0), 0); } catch {} }
}));

Alpine.data('ranapAdmission', () => ({
    searchQuery: '', searchResults: [], selectedPatient: null, admission: { bangsal: '', kelas: '', no_kamar: '', dokter_merawat: '', diagnosis_masuk: '', catatan_masuk: '' }, activeAdmissions: [], availableBeds: 0, saving: false,
    async init() { await this.loadActive(); },
    async searchPatient() { if (this.searchQuery.length < 2) { this.searchResults = []; return; } try { this.searchResults = await this.$store.api.get('/pasien/search?q=' + encodeURIComponent(this.searchQuery)); } catch { this.searchResults = []; } },
    selectPatient(p) { this.selectedPatient = p; this.searchResults = []; this.searchQuery = ''; },
    async loadActive() { try { const res = await this.$store.api.get('/ranap/admissions'); this.activeAdmissions = res.active || []; this.availableBeds = res.available_beds || 0; } catch {} },
    async admitPatient() {
        this.saving = true;
        try {
            const reg = await this.$store.api.post('/registrasi', { pasien_id: this.selectedPatient.id, jenis: 'Ranap', tgl_registrasi: new Date().toISOString().slice(0,16) });
            await this.$store.api.post('/ranap/admit', { registrasi_id: reg.id, no_kamar: this.admission.no_kamar, kelas: this.admission.kelas, bangsal: this.admission.bangsal, diagnosis_masuk: this.admission.diagnosis_masuk, dokter_merawat: this.admission.dokter_merawat, catatan_masuk: this.admission.catatan_masuk, cara_masuk: 'rawat_inap' });
            alert('Admisi berhasil'); this.selectedPatient = null; this.admission = { bangsal: '', kelas: '', no_kamar: '', dokter_merawat: '', diagnosis_masuk: '', catatan_masuk: '' }; await this.loadActive();
        } catch (e) { alert(e.message); }
        finally { this.saving = false; }
    }
}));

Alpine.data('ranapCare', () => ({
    activePatients: [], activeCount: 0, selectedPatient: null, saving: false, tindakanList: [], catatanList: [], visiteList: [],
    newTindakan: { nama: '', biaya: 0 }, newCatatan: { jenis: 'SOAP', isi: '' }, newVisite: { dokter: '', diagnosis: '', terapi: '' }, _idx: 0,
    async init() { await this.loadData(); },
    async loadData() { try { this.activePatients = (await this.$store.api.get('/ranap/admissions')).active || []; this.activeCount = this.activePatients.length; } catch {} },
    selectPatient(a) { this.selectedPatient = a; this.tindakanList = a.tindakan || []; this.catatanList = a.catatan || []; this.visiteList = a.visite || []; },
    addTindakan() { if (!this.newTindakan.nama) return; this.tindakanList.push({...this.newTindakan, _idx: ++this._idx, dokter: this.$store.auth.user?.name || ''}); this.newTindakan = { nama: '', biaya: 0 }; },
    addCatatan() { if (!this.newCatatan.isi) return; this.catatanList.push({...this.newCatatan, _idx: ++this._idx}); this.newCatatan = { jenis: 'SOAP', isi: '' }; },
    addVisite() { if (!this.newVisite.dokter) return; this.visiteList.push({...this.newVisite, _idx: ++this._idx}); this.newVisite = { dokter: '', diagnosis: '', terapi: '' }; },
    async saveAll() {
        this.saving = true;
        try {
            const admId = this.selectedPatient.id;
            for (const t of this.tindakanList) { if (!t.id) await this.$store.api.post('/ranap/' + admId + '/tindakan', { tindakan: t.nama, jumlah: 1, tarif: t.biaya || 0 }); }
            for (const c of this.catatanList) { if (!c.id) await this.$store.api.post('/ranap/' + admId + '/catatan', { catatan: c.isi, jenis: c.jenis }); }
            for (const v of this.visiteList) { if (!v.id) await this.$store.api.post('/ranap/' + admId + '/visite', { dokter: v.dokter, hasil_pemeriksaan: v.diagnosis, instruksi: v.terapi }); }
            alert('Data tersimpan'); await this.loadData();
        } catch (e) { alert(e.message); }
        finally { this.saving = false; }
    }
}));

Alpine.data('ranapDischarge', () => ({
    activePatients: [], selectedPatient: null, saving: false, lamaRawat: 0,
    discharge: { tgl_keluar: new Date().toISOString().slice(0,10), status_pulang: 'sembuh', diagnosis_keluar: '', keadaan_keluar: '' },
    async init() { await this.loadData(); },
    async loadData() { try { this.activePatients = (await this.$store.api.get('/ranap/admissions')).active || []; } catch {} },
    selectForDischarge(a) { this.selectedPatient = a; const masuk = new Date(a.tgl_masuk); const keluar = new Date(); this.lamaRawat = Math.ceil((keluar - masuk) / (1000 * 60 * 60 * 24)) || 1; },
    async doDischarge() {
        if (!confirm('Yakin ingin memulangkan pasien ' + this.selectedPatient.nama_pasien + '?')) return;
        this.saving = true;
        try { await this.$store.api.put('/ranap/' + this.selectedPatient.id + '/discharge', this.discharge); alert('Pasien berhasil dipulangkan'); this.selectedPatient = null; await this.loadData(); }
        catch (e) { alert(e.message); }
        finally { this.saving = false; }
    }
}));

window.Alpine = Alpine;

window.onerror = function(message, source, lineno, colno, error) {
    try {
        Alpine.store('errors').add({
            message: typeof message === 'object' ? (message.message || String(message)) : String(message),
            type: error?.name || 'Error',
            file: source || '',
            line: lineno ? lineno + (colno ? ':' + colno : '') : '',
            stack: error?.stack || '',
            timestamp: new Date().toISOString(),
        });
    } catch {}
    return true;
};

window.onunhandledrejection = function(event) {
    try {
        const reason = event?.reason;
        Alpine.store('errors').add({
            message: reason?.message || String(reason || 'Unhandled Promise Rejection'),
            type: 'PromiseRejection',
            file: '',
            line: '',
            stack: reason?.stack || '',
            timestamp: new Date().toISOString(),
        });
    } catch {}
};

Alpine.start();