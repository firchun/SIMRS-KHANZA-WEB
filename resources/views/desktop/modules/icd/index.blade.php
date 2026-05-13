<div x-data="{
    activeTab: 'icd10',
    searchQuery: '',
    results: [],
    loading: false,
    selected: null,

    async search() {
        if (this.searchQuery.length < 1) { this.results = []; return; }
        this.loading = true;
        try {
            const res = await this.$store.api.get('/icd/search?q=' + encodeURIComponent(this.searchQuery) + '&type=' + this.activeTab);
            this.results = res || [];
        } catch (e) { console.error(e); }
        this.loading = false;
    },

    setTab(key) {
        this.activeTab = key;
        this.selected = null;
        if (this.searchQuery.length >= 1) this.search();
    },

    select(item) {
        this.selected = item;
    },
}" class="flex flex-col h-full" style="color:var(--text-primary)">

    <div class="px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" x-model="searchQuery" @input.debounce.300ms="search" @keydown.enter="search"
                placeholder="Cari kode atau nama penyakit/tindakan..."
                class="w-full bg-transparent text-xs outline-none" style="color:var(--text-primary)">
        </div>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <div class="w-36 shrink-0 flex flex-col border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
            <button @click="setTab('icd10')"
                class="w-full text-left px-3 py-2 text-xs font-medium border-l-2 transition-colors"
                :class="activeTab === 'icd10' ? '' : 'border-transparent'"
                :style="activeTab === 'icd10' ? 'border-color:#2563eb;color:#2563eb;background-color:rgba(37,99,235,0.05)' : ''">
                ICD 10<br><span class="text-[10px] font-normal" style="color:var(--text-muted)">Penyakit</span>
            </button>
            <button @click="setTab('icd9')"
                class="w-full text-left px-3 py-2 text-xs font-medium border-l-2 transition-colors"
                :class="activeTab === 'icd9' ? '' : 'border-transparent'"
                :style="activeTab === 'icd9' ? 'border-color:#16a34a;color:#16a34a;background-color:rgba(22,163,74,0.05)' : ''">
                ICD 9<br><span class="text-[10px] font-normal" style="color:var(--text-muted)">Tindakan/Diagnosa</span>
            </button>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto min-h-0">
                <div x-show="loading" class="text-center py-8 text-xs" style="color:var(--text-muted)">Mencari...</div>
                <div x-show="!loading && !results.length && searchQuery.length >= 1" class="text-center py-8 text-xs" style="color:var(--text-muted)">Tidak ditemukan</div>
                <div x-show="!loading && !results.length && searchQuery.length < 1" class="text-center py-8 text-xs" style="color:var(--text-muted)">Ketik untuk mencari</div>

                <template x-if="activeTab === 'icd10' && results.length">
                    <div>
                        <template x-for="r in results" :key="r.kd_penyakit">
                            <button @click="select(r)"
                                class="w-full text-left px-3 py-2 border-b text-xs transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                style="border-color:var(--border)"
                                :class="selected?.kd_penyakit === r.kd_penyakit ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-[11px] px-1 py-0.5 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 shrink-0" x-text="r.kd_penyakit"></span>
                                    <span x-text="r.nm_penyakit"></span>
                                </div>
                                <div class="flex gap-2 mt-0.5 text-[10px]" style="color:var(--text-muted)">
                                    <span x-show="r.status" class="px-1 rounded" :style="r.status === 'Menular' ? 'background-color:rgba(239,68,68,0.1);color:rgb(239,68,68)' : 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)'" x-text="r.status"></span>
                                    <span x-show="r.keterangan" x-text="r.keterangan"></span>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="activeTab === 'icd9' && results.length">
                    <div>
                        <template x-for="r in results" :key="r.kode">
                            <button @click="select(r)"
                                class="w-full text-left px-3 py-2 border-b text-xs transition-colors hover:bg-black/5 dark:hover:bg-white/5"
                                style="border-color:var(--border)"
                                :class="selected?.kode === r.kode ? 'bg-green-50 dark:bg-green-900/20' : ''">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-[11px] px-1 py-0.5 rounded bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 shrink-0" x-text="r.kode"></span>
                                    <span x-text="r.deskripsi_panjang || r.deskripsi_pendek"></span>
                                </div>
                                <div x-show="r.deskripsi_pendek" class="mt-0.5 text-[10px]" style="color:var(--text-muted)">
                                    <span x-text="r.deskripsi_pendek"></span>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <div x-show="results.length" class="text-[10px] px-3 py-1 border-t shrink-0 text-right" style="border-color:var(--border);color:var(--text-muted)">
                <span x-text="results.length + ' ditemukan'"></span>
            </div>
        </div>

        <div x-show="selected" class="w-64 shrink-0 border-l p-3 overflow-y-auto" style="border-color:var(--border);background-color:var(--bg-muted)">
            <template x-if="activeTab === 'icd10' && selected">
                <div class="space-y-2 text-xs">
                    <h4 class="font-bold text-sm" style="color:#2563eb">Detail ICD 10</h4>
                    <div>
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Kode</label>
                        <div class="font-mono font-bold" x-text="selected.kd_penyakit"></div>
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Penyakit</label>
                        <div x-text="selected.nm_penyakit"></div>
                    </div>
                    <div x-show="selected.ciri_ciri">
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Ciri-ciri</label>
                        <div class="text-[11px]" x-text="selected.ciri_ciri"></div>
                    </div>
                    <div x-show="selected.keterangan">
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Keterangan</label>
                        <div x-text="selected.keterangan"></div>
                    </div>
                    <div x-show="selected.status">
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Status</label>
                        <span class="px-1 py-0.5 rounded text-[10px]" :style="selected.status === 'Menular' ? 'background-color:rgba(239,68,68,0.1);color:rgb(239,68,68)' : 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)'" x-text="selected.status"></span>
                    </div>
                </div>
            </template>
            <template x-if="activeTab === 'icd9' && selected">
                <div class="space-y-2 text-xs">
                    <h4 class="font-bold text-sm" style="color:#16a34a">Detail ICD 9</h4>
                    <div>
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Kode</label>
                        <div class="font-mono font-bold" x-text="selected.kode"></div>
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Deskripsi</label>
                        <div x-text="selected.deskripsi_panjang || '-'"></div>
                    </div>
                    <div x-show="selected.deskripsi_pendek">
                        <label class="text-[10px] uppercase font-medium" style="color:var(--text-muted)">Deskripsi Pendek</label>
                        <div x-text="selected.deskripsi_pendek"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
