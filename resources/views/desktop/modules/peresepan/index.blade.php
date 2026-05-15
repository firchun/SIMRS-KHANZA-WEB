<div x-data="{
    activeSidebar: 'permintaan',
    sidebarQ: '',
    sidebarItems: [
        { key: 'permintaan', label: 'Permintaan Resep', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'resep-dokter', label: 'Resep Dokter', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'no-resep', label: 'No Resep', icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z' },
        { key: 'resep-keluar', label: 'Resep Keluar', icon: 'M13 7V3a1 1 0 00-1-1H5a1 1 0 00-1 1v4m8 0l-4 4m4-4h6m-6 0v6a1 1 0 001 1h4a1 1 0 001-1v-4m-6 0H5' },
    ],
    permintaanTab: 'igd',
    permintaanTabs: [
        { key: 'igd', label: 'IGD' },
        { key: 'operasi', label: 'Operasi' },
        { key: 'rawat-jalan', label: 'Rawat Jalan' },
        { key: 'rawat-inap', label: 'Rawat Inap' },
        { key: 'resep-pulang', label: 'Resep Pulang' },
    ],

    get filteredSidebar() {
        if (!this.sidebarQ) return this.sidebarItems;
        const q = this.sidebarQ.toLowerCase();
        return this.sidebarItems.filter(i => i.label.toLowerCase().includes(q));
    },

    setSidebar(key) {
        this.activeSidebar = key;
    },
}">
    <div class="flex h-full" style="color:var(--text-primary)">
        {{-- Sidebar --}}
        <div class="w-52 shrink-0 flex flex-col border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
            <div class="px-2 py-1.5 border-b shrink-0" style="border-color:var(--border)">
                <input type="text" x-model="sidebarQ" placeholder="Cari menu..." class="form-input text-[11px] py-1 w-full">
            </div>
            <div class="flex-1 overflow-y-auto min-h-0">
                <template x-for="item in filteredSidebar" :key="item.key">
                    <button @click="setSidebar(item.key)"
                        class="w-full text-left px-3 py-2.5 text-xs flex items-center gap-2 hover:bg-gray-100 dark:hover:bg-gray-700 border-b transition-colors"
                        :class="activeSidebar === item.key ? 'bg-blue-50 dark:bg-blue-900/20 font-semibold' : ''"
                        style="border-color:var(--border)">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path :d="item.icon"/>
                        </svg>
                        <span x-text="item.label"></span>
                    </button>
                </template>
                <div x-show="!filteredSidebar.length" class="px-3 py-4 text-xs text-center" style="color:var(--text-muted)">Menu tidak ditemukan</div>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Permintaan Resep --}}
            <div x-show="activeSidebar === 'permintaan'" class="flex flex-col h-full">
                <div class="shrink-0">
                    {{-- Tabs --}}
                    <div class="flex border-b" style="border-color:var(--border);background-color:var(--bg-muted)">
                        <template x-for="tab in permintaanTabs" :key="tab.key">
                            <button @click="permintaanTab = tab.key"
                                class="px-4 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"
                                :class="permintaanTab === tab.key ? '' : 'border-transparent'"
                                :style="permintaanTab === tab.key ? 'border-color:#2563eb;color:#2563eb' : 'color:var(--text-secondary)'">
                                <span x-text="tab.label"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Search & Filter Bar --}}
                    <div class="flex items-center gap-2 px-3 py-2 border-b" style="border-color:var(--border);background-color:var(--bg-muted)">
                        <input type="text" placeholder="Cari pasien / resep..." class="form-input text-xs py-1 flex-1">
                        <input type="date" class="form-input text-xs py-1 w-36">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0 p-4">
                    <div class="flex flex-col items-center justify-center h-48 text-xs" style="color:var(--text-muted)">
                        <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-medium">Belum ada data</p>
                        <p class="mt-1" x-text="'Tidak ada permintaan resep ' + (permintaanTabs.find(t => t.key === permintaanTab)?.label || '').toLowerCase()"></p>
                    </div>
                </div>
            </div>

            {{-- Resep Dokter --}}
            <div x-show="activeSidebar === 'resep-dokter'" class="flex flex-col h-full">
                <div class="flex items-center gap-2 px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                    <input type="text" placeholder="Cari dokter / resep..." class="form-input text-xs py-1 flex-1">
                    <input type="date" class="form-input text-xs py-1 w-36">
                </div>
                <div class="flex-1 overflow-y-auto min-h-0 p-4">
                    <div class="flex flex-col items-center justify-center h-48 text-xs" style="color:var(--text-muted)">
                        <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                            <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <p class="font-medium">Belum ada data</p>
                        <p class="mt-1">Tidak ada resep dokter</p>
                    </div>
                </div>
            </div>

            {{-- No Resep --}}
            <div x-show="activeSidebar === 'no-resep'" class="flex flex-col h-full">
                <div class="flex items-center gap-2 px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                    <input type="text" placeholder="Cari no resep..." class="form-input text-xs py-1 flex-1">
                    <input type="date" class="form-input text-xs py-1 w-36">
                </div>
                <div class="flex-1 overflow-y-auto min-h-0 p-4">
                    <div class="flex flex-col items-center justify-center h-48 text-xs" style="color:var(--text-muted)">
                        <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <p class="font-medium">Belum ada data</p>
                        <p class="mt-1">Tidak ada nomor resep</p>
                    </div>
                </div>
            </div>

            {{-- Resep Keluar --}}
            <div x-show="activeSidebar === 'resep-keluar'" class="flex flex-col h-full">
                <div class="flex items-center gap-2 px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
                    <input type="text" placeholder="Cari resep keluar..." class="form-input text-xs py-1 flex-1">
                    <input type="date" class="form-input text-xs py-1 w-36">
                </div>
                <div class="flex-1 overflow-y-auto min-h-0 p-4">
                    <div class="flex flex-col items-center justify-center h-48 text-xs" style="color:var(--text-muted)">
                        <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                            <path d="M13 7V3a1 1 0 00-1-1H5a1 1 0 00-1 1v4m8 0l-4 4m4-4h6m-6 0v6a1 1 0 001 1h4a1 1 0 001-1v-4m-6 0H5"/>
                        </svg>
                        <p class="font-medium">Belum ada data</p>
                        <p class="mt-1">Tidak ada resep keluar</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
