<div x-data="{
    partitions: [
        { key: 'all', label: 'Semua Pasien', icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z', count: 22 },
        { key: 'ranap', label: 'Rawat Inap', icon: 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z', count: 8 },
        { key: 'ralan', label: 'Rawat Jalan', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', count: 9 },
        { key: 'lab', label: 'Laboratorium', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', count: 3 },
        { key: 'radiologi', label: 'Radiologi', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', count: 2 },
        { key: 'lainnya', label: 'Lainnya', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', count: 0 },
    ],
    activePartition: 'all',
    viewMode: 'grid',
    searchQuery: '',
    selectedId: null,
    openedPatient: null,
    showUpload: false,
    uploadForm: { no_rkm_medis: '', nm_pasien: '', tgl_masuk: '', jenis_berkas: '', file: null },
    jenisBerkasList: [
        'Ringkasan Medis', 'Hasil Laboratorium', 'Hasil Radiologi', 'SOAP / CPPT',
        'Resep Obat', 'Laporan Operasi', 'EKG', 'USG', 'CT Scan', 'MRI',
        'Surat Rujukan', 'Surat Kontrol', 'Surat Sehat', 'Lainnya',
    ],

    patients: [
        { id: 1, no_rm: 'RM-001', nama: 'Budi Santoso', nik: '3273010505800001', tgl_lahir: '1980-05-05', jk: 'L', partisi: 'ranap', diagnosa: 'Demam Berdarah', tgl_masuk: '2026-05-10', ruangan: 'Anggrek-201' },
        { id: 2, no_rm: 'RM-002', nama: 'Siti Rahmawati', nik: '3273016512900002', tgl_lahir: '1990-12-25', jk: 'P', partisi: 'ralan', diagnosa: 'Hipertensi', tgl_masuk: '2026-05-12', poli: 'Penyakit Dalam' },
        { id: 3, no_rm: 'RM-003', nama: 'Ahmad Fauzi', nik: '3273011108750003', tgl_lahir: '1975-08-11', jk: 'L', partisi: 'ranap', diagnosa: 'Appendisitis', tgl_masuk: '2026-05-09', ruangan: 'Mawar-305' },
        { id: 4, no_rm: 'RM-004', nama: 'Dewi Anggraini', nik: '3273012203900004', tgl_lahir: '1990-03-22', jk: 'P', partisi: 'ralan', diagnosa: 'DM Tipe 2', tgl_masuk: '2026-05-12', poli: 'Penyakit Dalam' },
        { id: 5, no_rm: 'RM-005', nama: 'Rudi Hartono', nik: '3273011506600005', tgl_lahir: '1960-06-15', jk: 'L', partisi: 'ranap', diagnosa: 'Stroke Ringan', tgl_masuk: '2026-05-08', ruangan: 'ICU-02' },
        { id: 6, no_rm: 'RM-006', nama: 'Fitriani', nik: '3273014508900006', tgl_lahir: '1990-08-05', jk: 'P', partisi: 'ralan', diagnosa: 'ISPA', tgl_masuk: '2026-05-12', poli: 'Umum' },
        { id: 7, no_rm: 'RM-007', nama: 'Bambang Susilo', nik: '3273011003700007', tgl_lahir: '1970-03-10', jk: 'L', partisi: 'lab', diagnosa: 'Cek Darah Rutin', tgl_masuk: '2026-05-11', jenis_lab: 'PK' },
        { id: 8, no_rm: 'RM-008', nama: 'Ratna Dewi', nik: '3273015501800008', tgl_lahir: '1980-01-15', jk: 'P', partisi: 'ralan', diagnosa: 'Mual & Muntah', tgl_masuk: '2026-05-12', poli: 'Kandungan' },
        { id: 9, no_rm: 'RM-009', nama: 'Hendra Gunawan', nik: '3273012006900009', tgl_lahir: '1969-06-20', jk: 'L', partisi: 'radiologi', diagnosa: 'Fraktur Tibia', tgl_masuk: '2026-05-10', jenis_radio: 'Rontgen' },
        { id: 10, no_rm: 'RM-010', nama: 'Maya Sari', nik: '3273016108950010', tgl_lahir: '1995-08-21', jk: 'P', partisi: 'ranap', diagnosa: 'Typoid', tgl_masuk: '2026-05-11', ruangan: 'Melati-102' },
        { id: 11, no_rm: 'RM-011', nama: 'Agus Prasetyo', nik: '3273011212770011', tgl_lahir: '1977-12-12', jk: 'L', partisi: 'lab', diagnosa: 'Cek Kolesterol', tgl_masuk: '2026-05-12', jenis_lab: 'Kimia Darah' },
        { id: 12, no_rm: 'RM-012', nama: 'Nurul Hidayah', nik: '3273014302820012', tgl_lahir: '1982-02-03', jk: 'P', partisi: 'radiologi', diagnosa: 'Nyeri Punggung', tgl_masuk: '2026-05-11', jenis_radio: 'MRI' },
    ],

    patientDocs: {
        1: [
            { nama: 'Ringkasan Medis', icon: 'file-text', tgl: '2026-05-10', size: '245 KB' },
            { nama: 'Hasil Laboratorium', icon: 'flask', tgl: '2026-05-10', size: '120 KB' },
            { nama: 'SOAP / CPPT', icon: 'edit-3', tgl: '2026-05-10', size: '89 KB' },
            { nama: 'Resep Obat', icon: 'file', tgl: '2026-05-10', size: '45 KB' },
            { nama: 'Hasil Radiologi', icon: 'image', tgl: '2026-05-11', size: '1.2 MB' },
        ],
        2: [
            { nama: 'Ringkasan Medis', icon: 'file-text', tgl: '2026-05-12', size: '210 KB' },
            { nama: 'SOAP / CPPT', icon: 'edit-3', tgl: '2026-05-12', size: '67 KB' },
            { nama: 'Resep Obat', icon: 'file', tgl: '2026-05-12', size: '32 KB' },
        ],
        3: [
            { nama: 'Ringkasan Medis', icon: 'file-text', tgl: '2026-05-09', size: '310 KB' },
            { nama: 'Hasil Laboratorium', icon: 'flask', tgl: '2026-05-09', size: '150 KB' },
            { nama: 'Hasil Radiologi', icon: 'image', tgl: '2026-05-09', size: '890 KB' },
            { nama: 'SOAP / CPPT', icon: 'edit-3', tgl: '2026-05-09', size: '95 KB' },
            { nama: 'Laporan Operasi', icon: 'file', tgl: '2026-05-10', size: '420 KB' },
            { nama: 'Resep Obat', icon: 'file', tgl: '2026-05-10', size: '55 KB' },
        ],
        5: [
            { nama: 'Ringkasan Medis', icon: 'file-text', tgl: '2026-05-08', size: '280 KB' },
            { nama: 'Hasil Laboratorium', icon: 'flask', tgl: '2026-05-08', size: '135 KB' },
            { nama: 'SOAP / CPPT', icon: 'edit-3', tgl: '2026-05-08', size: '78 KB' },
            { nama: 'Resep Obat', icon: 'file', tgl: '2026-05-08', size: '40 KB' },
            { nama: 'Hasil Radiologi', icon: 'image', tgl: '2026-05-09', size: '2.1 MB' },
        ],
        10: [
            { nama: 'Ringkasan Medis', icon: 'file-text', tgl: '2026-05-11', size: '195 KB' },
            { nama: 'Hasil Laboratorium', icon: 'flask', tgl: '2026-05-11', size: '110 KB' },
            { nama: 'SOAP / CPPT', icon: 'edit-3', tgl: '2026-05-11', size: '72 KB' },
            { nama: 'Resep Obat', icon: 'file', tgl: '2026-05-11', size: '38 KB' },
        ],
    },

    get filteredPatients() {
        let list = this.patients;
        if (this.activePartition !== 'all') {
            list = list.filter(p => p.partisi === this.activePartition);
        }
        const q = this.searchQuery.toLowerCase();
        if (q) {
            list = list.filter(p =>
                p.nama.toLowerCase().includes(q) ||
                p.no_rm.toLowerCase().includes(q) ||
                p.diagnosa.toLowerCase().includes(q)
            );
        }
        return list;
    },

    get currentFolderLabel() {
        if (this.openedPatient) return this.openedPatient.nama;
        return this.partitions.find(p => p.key === this.activePartition)?.label || 'Berkas Perawatan';
    },

    get currentPath() {
        if (this.openedPatient) {
            return ['Berkas Perawatan', this.partitions.find(p => p.key === this.activePartition)?.label, this.openedPatient.nama];
        }
        return ['Berkas Perawatan', this.partitions.find(p => p.key === this.activePartition)?.label];
    },

    openPatient(p) {
        this.openedPatient = p;
        this.selectedId = null;
    },

    goBack() {
        this.openedPatient = null;
        this.selectedId = null;
    },

    selectPatient(id) {
        this.selectedId = this.selectedId === id ? null : id;
    },

    openFolder(id) {
        this.openPatient(this.patients.find(p => p.id === id));
    },

    getFileIcon(icon) {
        const icons = {
            'file-text': 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z M14 2v6h6 M16 13H8 M16 17H8 M10 9H8',
            'flask': 'M9 3h6v5a4 4 0 014 4v5a2 2 0 01-2 2H7a2 2 0 01-2-2v-5a4 4 0 014-4V3z M9 3v5a4 4 0 00-4 4v5h14v-5a4 4 0 00-4-4V3',
            'edit-3': 'M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7 M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z',
            'image': 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'file': 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z M14 2v6h6',
        };
        return icons[icon] || icons['file'];
    },

    formatDate(tgl) {
        if (!tgl) return '';
        const d = new Date(tgl + 'T00:00:00');
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    },

    uploadSearchResults: [],
    uploadSearchLoading: false,
    async searchPasienForUpload() {
        const q = this.uploadForm.no_rkm_medis;
        if (q.length < 2) { this.uploadSearchResults = []; return; }
        this.uploadSearchLoading = true;
        try {
            const res = await this.$store.api.get('/pasien/search?q=' + encodeURIComponent(q));
            this.uploadSearchResults = (res || []).slice(0, 10);
        } catch (e) { console.error(e); }
        this.uploadSearchLoading = false;
    },

    selectPasienUpload(p) {
        this.uploadForm.no_rkm_medis = p.no_rkm_medis;
        this.uploadForm.nm_pasien = p.nm_pasien;
        this.uploadSearchResults = [];
    },

    openUpload() {
        this.showUpload = true;
        this.uploadForm = { no_rkm_medis: '', nm_pasien: '', tgl_masuk: '', jenis_berkas: '', file: null };
    },

    closeUpload() {
        this.showUpload = false;
        this.uploadSearchResults = [];
    },

    handleFileSelect(e) {
        this.uploadForm.file = e.target.files?.[0] || null;
    },

    async submitUpload() {
        const f = this.uploadForm;
        if (!f.no_rkm_medis || !f.jenis_berkas || !f.file) return;
        try {
            const formData = new FormData();
            formData.append('no_rkm_medis', f.no_rkm_medis);
            formData.append('tgl_masuk', f.tgl_masuk);
            formData.append('jenis_berkas', f.jenis_berkas);
            formData.append('file', f.file);
            const token = localStorage.getItem('token');
            const res = await fetch('/api/berkas-perawatan/upload', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                body: formData,
            });
            if (!res.ok) throw new Error((await res.json()).message || 'Upload gagal');
            this.closeUpload();
        } catch (e) { console.error(e); }
    }
}" class="flex h-full" style="color:var(--text-primary)">

    {{-- Sidebar --}}
    <div class="w-52 shrink-0 flex flex-col overflow-hidden"
        style="background-color:var(--bg-muted);border-right:1px solid var(--border)">
        <div class=" border-b" style="border-color:var(--border)">
            <div class="px-3 py-2">
                <div class="text-sm font-semibold">Berkas Perawatan</div>
                <div class="text-[10px]" style="color:var(--text-muted)">Folder Pasien</div>
            </div>
            <div class="px-3 border-t py-2" style="border-color:var(--border)">
                <button @click="openUpload"
                    class="w-full btn-primary flex items-center gap-2 px-3 py-2 rounded text-xs font-medium transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Upload Berkas
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-2">
            <div class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wide" style="color:var(--text-muted)">
                Partisi</div>
            <template x-for="part in partitions" :key="part.key">
                <button @click="activePartition = part.key; openedPatient = null"
                    class="w-full text-left px-3 py-2 transition-colors flex items-center gap-2.5 rounded mx-1"
                    :class="activePartition === part.key && !openedPatient ? 'bg-black/10 dark:bg-white/15' : 'hover:bg-black/5 dark:hover:bg-white/5'"
                    style="color:var(--text-primary)">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none"
                        :stroke="activePartition === part.key ? 'var(--accent-blue)' : 'currentColor'"
                        stroke-width="1.5">
                        <path :d="part.icon" />
                    </svg>
                    <span class="text-xs flex-1" x-text="part.label"></span>
                    <span class="text-[10px]" style="color:var(--text-muted)" x-text="part.count"></span>
                </button>
            </template>
            {{-- Divider --}}


        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Toolbar --}}
        <div class="flex items-center gap-2 px-3 py-1.5 border-b shrink-0"
            style="border-color:var(--border);background-color:var(--bg-muted)">
            <button @click="goBack" :disabled="!openedPatient" class="p-1 rounded transition-colors"
                :style="openedPatient ? 'color:var(--text-primary)' : 'color:var(--text-muted)'"
                @mouseenter="openedPatient ? $el.style.backgroundColor='var(--bg-hover)' : ''"
                @mouseleave="$el.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="flex items-center gap-1 text-xs" style="color:var(--text-muted)">
                <template x-for="(seg, i) in currentPath" :key="i">
                    <span class="flex items-center gap-1">
                        <span x-show="i > 0" class="mx-0.5">/</span>
                        <span :style="i === currentPath.length - 1 ? 'color:var(--text-primary);font-weight:500' : ''"
                            x-text="seg"></span>
                    </span>
                </template>
            </div>
            <div class="flex-1"></div>
            <div class="flex items-center gap-0.5 border-r pr-2 mr-2" style="border-color:var(--border)">
                <button @click="viewMode = 'grid'" class="p-1 rounded transition-colors"
                    :style="viewMode === 'grid' ? 'color:var(--accent-blue);background-color:rgba(59,130,246,0.1)' : 'color:var(--text-muted)'"
                    @mouseenter="$el.style.backgroundColor='var(--bg-hover)'"
                    @mouseleave="viewMode === 'grid' ? '' : $el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                    </svg>
                </button>
                <button @click="viewMode = 'list'" class="p-1 rounded transition-colors"
                    :style="viewMode === 'list' ? 'color:var(--accent-blue);background-color:rgba(59,130,246,0.1)' : 'color:var(--text-muted)'"
                    @mouseenter="$el.style.backgroundColor='var(--bg-hover)'"
                    @mouseleave="viewMode === 'list' ? '' : $el.style.backgroundColor='transparent'">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="relative">
                <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3 h-3" style="color:var(--text-muted)"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="M21 21l-4.35-4.35" />
                </svg>
                <input type="text" x-model="searchQuery" placeholder="Cari pasien..."
                    class="form-input text-xs pl-7 pr-2 py-1 w-44" style="background-color:var(--bg-input)">
            </div>
        </div>

        {{-- Content Area --}}
        <div class="flex-1 overflow-y-auto p-4" style="background-color:var(--bg-elevated)">

            {{-- Patient Folders (icon view) --}}
            <template x-if="!openedPatient">
                <div>
                    <template x-if="viewMode === 'grid'">
                        <div class="flex flex-wrap gap-4">
                            <template x-for="p in filteredPatients" :key="p.id">
                                <div @click="selectPatient(p.id)" @dblclick="openFolder(p.id)"
                                    class="w-28 p-2 rounded-lg transition-all cursor-pointer text-center"
                                    :class="selectedId === p.id ? 'bg-blue-100 dark:bg-blue-900/30 ring-2 ring-blue-400' : 'hover:bg-black/5 dark:hover:bg-white/5'"
                                    style="color:var(--text-primary)">
                                    <div class="flex justify-center mb-1">
                                        <svg class="w-14 h-14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#d97706"
                                            stroke-width="0.5">
                                            <path
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-[11px] leading-tight truncate max-w-full" x-text="p.nama"></div>
                                    <div class="text-[9px] text-gray-500 dark:text-gray-400 truncate" x-text="p.no_rm">
                                    </div>
                                </div>
                            </template>
                            <template x-if="filteredPatients.length === 0">
                                <div class="w-full text-center py-12" style="color:var(--text-muted)">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1">
                                        <path
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    <p class="text-sm">Tidak ada pasien</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="viewMode === 'list'">
                        <table class="w-full text-xs">
                            <thead>
                                <tr style="color:var(--text-muted)" class="text-[11px] uppercase tracking-wide">
                                    <th class="text-left px-3 py-2 font-medium w-8"></th>
                                    <th class="text-left px-3 py-2 font-medium">Nama Pasien</th>
                                    <th class="text-left px-3 py-2 font-medium">No. RM</th>
                                    <th class="text-left px-3 py-2 font-medium">Diagnosa</th>
                                    <th class="text-left px-3 py-2 font-medium">Tgl Masuk</th>
                                    <th class="text-left px-3 py-2 font-medium">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="p in filteredPatients" :key="p.id">
                                    <tr @click="selectPatient(p.id)" @dblclick="openFolder(p.id)"
                                        class="transition-colors cursor-pointer"
                                        :class="selectedId === p.id ? 'bg-blue-100 dark:bg-blue-900/30' : 'hover:bg-black/5 dark:hover:bg-white/5'"
                                        style="color:var(--text-primary)">
                                        <td class="px-3 py-2">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#f59e0b" stroke="#d97706"
                                                stroke-width="0.5">
                                                <path
                                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                            </svg>
                                        </td>
                                        <td class="px-3 py-2 font-medium" x-text="p.nama"></td>
                                        <td class="px-3 py-2 font-mono text-[10px]" style="color:var(--text-muted)"
                                            x-text="p.no_rm"></td>
                                        <td class="px-3 py-2" x-text="p.diagnosa"></td>
                                        <td class="px-3 py-2" style="color:var(--text-muted)"
                                            x-text="formatDate(p.tgl_masuk)"></td>
                                        <td class="px-3 py-2">
                                            <span class="text-[10px] px-2 py-0.5 rounded"
                                                :style="p.partisi === 'ranap' ? 'background-color:rgba(139,92,246,0.1);color:rgb(139,92,246)' : p.partisi === 'ralan' ? 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)' : p.partisi === 'lab' ? 'background-color:rgba(245,158,11,0.1);color:rgb(245,158,11)' : p.partisi === 'radiologi' ? 'background-color:rgba(236,72,153,0.1);color:rgb(236,72,153)' : 'background-color:rgba(107,114,128,0.1);color:rgb(107,114,128)'"
                                                x-text="p.ruangan || p.poli || p.jenis_lab || p.jenis_radio || '-'">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                </div>
            </template>

            {{-- Patient Files (inside folder) --}}
            <template x-if="openedPatient">
                <div>
                    {{-- Patient info header --}}
                    <div class="flex items-center gap-3 mb-4 p-3 rounded-lg" style="background-color:var(--bg-muted)">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold"
                            style="background-color:var(--accent-blue);color:#fff">
                            <span x-text="openedPatient.nama.charAt(0)"></span>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold" x-text="openedPatient.nama"></div>
                            <div class="text-[10px]" style="color:var(--text-muted)">
                                <span x-text="openedPatient.no_rm"></span>
                                <span class="mx-1">•</span>
                                <span x-text="openedPatient.jk === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                                <span class="mx-1">•</span>
                                <span x-text="openedPatient.diagnosa"></span>
                            </div>
                        </div>
                        <span class="text-[10px]" style="color:var(--text-muted)"
                            x-text="filteredPatients.length + ' folder'"></span>
                    </div>

                    {{-- Files grid --}}
                    <template x-if="viewMode === 'grid'">
                        <div class="flex flex-wrap gap-4">
                            <template x-for="(doc, i) in (patientDocs[openedPatient.id] || [])" :key="i">
                                <div class="w-24 p-2 rounded-lg transition-all cursor-pointer text-center hover:bg-black/5 dark:hover:bg-white/5"
                                    style="color:var(--text-primary)">
                                    <div class="flex justify-center mb-1">
                                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
                                            stroke-width="1">
                                            <path :d="getFileIcon(doc.icon)" />
                                        </svg>
                                    </div>
                                    <div class="text-[10px] leading-tight truncate max-w-full" x-text="doc.nama"></div>
                                    <div class="text-[8px]" style="color:var(--text-muted)" x-text="doc.size"></div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="viewMode === 'list'">
                        <table class="w-full text-xs">
                            <thead>
                                <tr style="color:var(--text-muted)" class="text-[11px] uppercase tracking-wide">
                                    <th class="text-left px-3 py-2 font-medium">Nama Dokumen</th>
                                    <th class="text-left px-3 py-2 font-medium">Tanggal</th>
                                    <th class="text-left px-3 py-2 font-medium">Ukuran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(doc, i) in (patientDocs[openedPatient.id] || [])" :key="i">
                                    <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors cursor-pointer"
                                        style="color:var(--text-primary)">
                                        <td class="px-3 py-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                                stroke="#3b82f6" stroke-width="1.5">
                                                <path :d="getFileIcon(doc.icon)" />
                                            </svg>
                                            <span x-text="doc.nama"></span>
                                        </td>
                                        <td class="px-3 py-2" style="color:var(--text-muted)"
                                            x-text="formatDate(doc.tgl)"></td>
                                        <td class="px-3 py-2" style="color:var(--text-muted)" x-text="doc.size"></td>
                                    </tr>
                                </template>
                                <template x-if="(patientDocs[openedPatient.id] || []).length === 0">
                                    <tr>
                                        <td colspan="3" class="text-center py-8" style="color:var(--text-muted)">
                                            Belum ada dokumen
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                </div>
            </template>
        </div>

        {{-- Status bar --}}
        <div class="flex items-center gap-3 px-3 py-1 border-t shrink-0 text-[10px]"
            style="border-color:var(--border);background-color:var(--bg-muted);color:var(--text-muted)">
            <template x-if="!openedPatient">
                <span x-text="filteredPatients.length + ' item'"></span>
            </template>
            <template x-if="openedPatient">
                <span x-text="(patientDocs[openedPatient.id] || []).length + ' dokumen'"></span>
            </template>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div x-show="showUpload" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div @mousedown.stop class="w-[480px] max-h-[80vh] flex flex-col rounded-lg shadow-2xl"
            style="background-color:var(--bg-elevated)">
            <div class="flex items-center justify-between px-4 py-2 border-b shrink-0"
                style="border-color:var(--border)">
                <h3 class="text-sm font-bold">Upload Berkas</h3>
                <button @click="closeUpload" class="p-1 rounded hover:bg-black/10 dark:hover:bg-white/10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto min-h-0 p-4 space-y-4">
                {{-- No RM --}}
                <div>
                    <label class="text-xs font-medium">No. Rekam Medis</label>
                    <div class="relative mt-0.5">
                        <input type="text" x-model="uploadForm.no_rkm_medis"
                            @input.debounce.300ms="searchPasienForUpload" placeholder="Ketik no. rekam medis..."
                            class="form-input text-xs w-full py-1.5 pl-2 pr-8">
                        <svg x-show="uploadSearchLoading"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 animate-spin"
                            style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                    </div>
                    {{-- Search Results Dropdown --}}
                    <div x-show="uploadSearchResults.length" x-cloak
                        class="mt-0.5 rounded border shadow-lg max-h-32 overflow-y-auto"
                        style="border-color:var(--border);background-color:var(--bg-elevated)">
                        <template x-for="p in uploadSearchResults" :key="p.no_rkm_medis">
                            <button @click="selectPasienUpload(p)"
                                class="w-full text-left px-2 py-1.5 text-xs hover:bg-black/10 dark:hover:bg-white/10 border-b"
                                style="border-color:var(--border)">
                                <span class="font-medium" x-text="p.no_rkm_medis"></span>
                                <span class="ml-1" style="color:var(--text-muted)" x-text="'- ' + p.nm_pasien"></span>
                            </button>
                        </template>
                    </div>
                    {{-- Selected Patient --}}
                    <div x-show="uploadForm.nm_pasien" class="mt-1 text-[11px]" style="color:var(--accent-green)">
                        <span x-text="uploadForm.nm_pasien"></span>
                    </div>
                </div>

                {{-- Tanggal Masuk --}}
                <div>
                    <label class="text-xs font-medium">Tanggal Masuk</label>
                    <input type="date" x-model="uploadForm.tgl_masuk" class="form-input text-xs w-full mt-0.5 py-1.5">
                </div>

                {{-- Jenis Berkas --}}
                <div>
                    <label class="text-xs font-medium">Jenis Berkas</label>
                    <select x-model="uploadForm.jenis_berkas" class="form-input text-xs w-full mt-0.5 py-1.5">
                        <option value="">Pilih jenis berkas...</option>
                        <template x-for="jb in jenisBerkasList" :key="jb">
                            <option :value="jb" x-text="jb"></option>
                        </template>
                    </select>
                </div>

                {{-- File --}}
                <div>
                    <label class="text-xs font-medium">File</label>
                    <div class="mt-0.5">
                        <input type="file" @change="handleFileSelect"
                            class="block w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:cursor-pointer file:bg-blue-500 file:text-white"
                            style="color:var(--text-primary)">
                    </div>
                    <div x-show="uploadForm.file" class="mt-1 text-[10px]" style="color:var(--text-muted)"
                        x-text="uploadForm.file?.name"></div>
                </div>
            </div>
            <div class="flex justify-end gap-2 px-4 py-3 border-t shrink-0"
                style="border-color:var(--border);background-color:var(--bg-muted)">
                <button @click="closeUpload" class="px-3 py-1.5 rounded text-xs font-medium"
                    style="background-color:var(--bg-elevated);color:var(--text-secondary);border:1px solid var(--border)">Batal</button>
                <button @click="submitUpload"
                    :disabled="!uploadForm.no_rkm_medis || !uploadForm.jenis_berkas || !uploadForm.file"
                    class="px-4 py-1.5 rounded text-xs font-medium transition-colors"
                    style="background-color:var(--accent-blue);color:#fff"
                    :style="!uploadForm.no_rkm_medis || !uploadForm.jenis_berkas || !uploadForm.file ? 'opacity:0.5' : ''">Upload</button>
            </div>
        </div>
    </div>
</div>