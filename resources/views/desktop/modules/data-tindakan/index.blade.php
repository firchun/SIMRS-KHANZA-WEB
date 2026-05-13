<div x-data="{
    sidebarItems: [
        { key: 'jalan', label: 'Tindakan Rawat Jalan', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
        { key: 'inap', label: 'Tindakan Rawat Inap', icon: 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z' },
        { key: 'operasi', label: 'Tindakan Operasi', icon: 'M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zM12 8v8M8 12h8' },
        { key: 'lab-pk', label: 'Tindakan Lab PK', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'lab-pa', label: 'Tindakan Lab PA', icon: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9z' },
        { key: 'lab-mb', label: 'Tindakan Lab MB', icon: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' },
        { key: 'radiologi', label: 'Tindakan Radiologi', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z' },
    ],
    activeSidebar: 'jalan',
    searchQuery: '',
    items: [],
    totalItems: 0,
    loading: false,

    init() {
        this.loadData();
        this.$watch('activeSidebar', () => { this.searchQuery = ''; this.loadData(); });
    },

    async loadData() {
        this.loading = true;
        try {
            const params = new URLSearchParams();
            if (this.searchQuery) params.set('q', this.searchQuery);
            params.set('limit', '200');
            const res = await this.$store.api.get('/data-tindakan/list/' + this.activeSidebar + '?' + params.toString());
            this.items = res?.data || [];
            this.totalItems = res?.total || 0;
        } catch (e) { console.error(e); }
        this.loading = false;
    },

    get totalTarif() {
        return this.items.reduce((sum, item) => sum + Number(item.tarif || 0), 0);
    },

    formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
}" class="flex h-full" style="color:var(--text-primary)">

    {{-- Sidebar --}}
    <div class="w-56 shrink-0 flex flex-col overflow-hidden" style="border-right:1px solid var(--border);background-color:var(--bg-muted)">
        <div class="px-4 py-3 border-b" style="border-color:var(--border)">
            <div class="text-sm font-semibold">Data Tindakan</div>
            <div class="text-[10px]" style="color:var(--text-muted)">Master Tarif & Tindakan</div>
        </div>
        <div @mousedown.stop class="flex-1 overflow-y-auto py-1">
            <template x-for="item in sidebarItems" :key="item.key">
                <button @mousedown.stop @click="activeSidebar = item.key"
                    class="w-full text-left px-3 py-2.5 transition-colors flex items-center gap-2.5"
                    :class="activeSidebar === item.key ? 'bg-black/10 dark:bg-white/15' : 'hover:bg-black/5 dark:hover:bg-white/5'"
                    style="color:var(--text-primary)">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        :style="activeSidebar === item.key ? 'color:var(--accent-blue)' : ''">
                        <path :d="item.icon"/>
                    </svg>
                    <span class="text-xs" x-text="item.label"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        <div @mousedown.stop class="flex items-center gap-3 px-4 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
            <div class="flex-1">
                <div class="text-sm font-semibold" x-text="sidebarItems.find(i => i.key === activeSidebar)?.label"></div>
                <div class="text-[10px]" style="color:var(--text-muted)">
                    <span x-text="items.length + ' / ' + totalItems + ' item'"></span>
                    <span class="ml-2 font-medium" x-text="'Total: ' + formatRupiah(totalTarif)"></span>
                </div>
            </div>
            <div @mousedown.stop class="relative">
                <svg class="absolute left-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="loadData" @mousedown.stop placeholder="Cari tindakan..."
                    class="form-input text-xs pl-7 pr-2 py-1.5 w-48" style="background-color:var(--bg-input)">
            </div>
        </div>

        {{-- Table --}}
        <div @mousedown.stop class="flex-1 overflow-y-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr style="background-color:var(--bg-muted);color:var(--text-muted)" class="text-[11px] uppercase tracking-wide sticky top-0">
                        <th class="text-left px-4 py-2 font-medium">Kode</th>
                        <th class="text-left px-4 py-2 font-medium">Nama Tindakan</th>
                        <th class="text-right px-4 py-2 font-medium">Tarif</th>
                        <th class="text-left px-4 py-2 font-medium">Kategori</th>
                        <th class="text-center px-4 py-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, i) in items" :key="item.kode">
                        <tr style="color:var(--text-primary)"
                            :style="i % 2 === 0 ? 'background-color:transparent' : 'background-color:var(--bg-muted)'"
                            class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2 font-mono" x-text="item.kode"></td>
                            <td class="px-4 py-2" x-text="item.nama"></td>
                            <td class="px-4 py-2 text-right font-medium" x-text="formatRupiah(item.tarif)"></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-[10px]" style="background-color:rgba(59,130,246,0.1);color:var(--accent-blue)" x-text="item.kategori"></span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-medium"
                                    :style="item.status_label === 'Aktif' ? 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)' : 'background-color:rgba(239,68,68,0.1);color:rgb(239,68,68)'"
                                    x-text="item.status_label"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && items.length === 0">
                        <tr>
                            <td colspan="5" class="text-center py-8" style="color:var(--text-muted)">
                                Tidak ada data tindakan
                            </td>
                        </tr>
                    </template>
                    <tr x-show="loading">
                        <td colspan="5" class="text-center py-4 text-xs" style="color:var(--text-muted)">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
