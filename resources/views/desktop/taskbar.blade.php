<div class="taskbar" style="z-index:99999">
    <button @click="$store.ui.toggleStartMenu()"
        class="flex items-center gap-2 px-3 py-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors text-sm"
        :class="$store.ui.startMenuOpen ? 'bg-black/5 dark:bg-white/20' : ''" style="color:var(--text-primary)">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <line x1="9" y1="3" x2="9" y2="21"/>
            <line x1="15" y1="3" x2="15" y2="21"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="3" y1="15" x2="21" y2="15"/>
        </svg>
        <span class="hidden sm:inline font-semibold">Start</span>
    </button>

    <button @click="$store.windows.open({key:'chatbot',label:'Chatbot AI',icon:'chat',width:480,height:640},{})"
        class="flex items-center gap-1.5 px-2 py-1.5 mx-1 rounded text-xs transition-colors"
        style="color:var(--text-primary)"
        @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor='transparent'"
        title="Chatbot AI">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            <circle cx="12" cy="13" r="6" stroke-width="1" opacity="0.3"/>
        </svg>
        <span class="hidden sm:inline text-[11px] font-medium">Chat</span>
    </button>

    <div x-data="taskbarSearch()" @click.outside="open = false" class="relative mx-2 w-64">
        <div class="flex items-center bg-gray-100 dark:bg-gray-700/80 rounded px-2 py-1 gap-1.5 border border-gray-300 dark:border-gray-600/60 focus-within:border-blue-500/60">
            <svg class="w-3.5 h-3.5 shrink-0" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text"
                x-model="query"
                @input="search()"
                @keydown="keydown($event)"
                @focus="query.length >= 2 && results.length && (open = true)"
                placeholder="Cari pasien..."
                class="w-full bg-transparent text-xs outline-none placeholder-gray-400 dark:placeholder-gray-500" style="color:var(--text-primary)">
            <template x-if="loading">
                <svg class="w-3 h-3 animate-spin" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </template>
        </div>

        <div x-show="open && results.length" x-cloak
            class="search-dropdown absolute bottom-full mb-1 left-0 right-0 max-h-80 overflow-y-auto rounded-lg shadow-xl" style="background-color:var(--bg-elevated);border:1px solid var(--border)">
            <template x-for="(p, i) in results" :key="p.id">
                <button @click="selectPatient(p)"
                    class="w-full text-left px-3 py-2 transition-colors border-b last:border-0" style="border-color:var(--border);color:var(--text-primary)"
                    :class="i === selectedIndex ? 'bg-black/5 dark:bg-white/10' : ''"
                    @mouseenter="selectedIndex = i">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-medium" x-text="p.nama"></span>
                        <span class="text-[10px]" style="color:var(--text-muted)" x-text="p.no_rm"></span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <template x-if="p.location">
                            <span class="badge" :class="{
                                'badge-danger': p.location?.includes('IGD'),
                                'badge-success': p.location?.includes('Ralan'),
                                'badge-info': p.location?.includes('Ranap'),
                                'badge-warning': !p.location?.includes('IGD') && !p.location?.includes('Ralan') && !p.location?.includes('Ranap')
                            }" x-text="p.location"></span>
                        </template>
                        <span class="text-[10px]" style="color:var(--text-muted)" x-text="p.jenis_registrasi + ' • ' + (p.tgl_registrasi ? new Date(p.tgl_registrasi).toLocaleDateString('id-ID') : '-')"></span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <div class="flex-1 flex items-center gap-0.5 mx-2 overflow-x-auto">
        <template x-for="win in $store.windows.items" :key="win.id">
            <button @click="$store.windows.restore(win.id)"
                class="flex items-center gap-2 px-3 py-1.5 rounded text-xs transition-colors truncate max-w-[160px] border-l" style="border-color:var(--border);color:var(--text-primary)"
                :class="win.zIndex === $store.windows.zIndex ? 'bg-black/10 dark:bg-white/15' : ''"
                @mouseenter="$el.style.backgroundColor='var(--bg-hover)'" @mouseleave="$el.style.backgroundColor=''">
                <span x-text="win.title" class="truncate"></span>
            </button>
        </template>
    </div>

    <div class="relative flex items-center gap-1 px-2 text-xs" style="color:var(--text-secondary)">
        <button @click="$store.windows.open({key:'error',label:'Error',icon:'warning',width:400,height:300},{})"
            class="relative p-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
            title="Lihat Error">
            <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <span x-show="$store.errors.items.length"
                class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-red-500 rounded-full text-[8px] text-white font-bold flex items-center justify-center"
                x-text="$store.errors.items.length"></span>
        </button>
        <button @click="$store.theme.toggle()"
            class="p-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
            :title="$store.theme.mode === 'light' ? 'Mode Gelap' : 'Mode Terang'">
            <svg x-show="$store.theme.mode === 'light'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
            <svg x-show="$store.theme.mode === 'dark'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
        </button>
        <button x-show="$store.windows.items.length" @click="$store.windows.clearAll()"
            class="p-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
            title="Tutup Semua Window">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
        </button>
        <button @click="$store.ui.toggleNotif()"
            class="flex items-center gap-1 px-1.5 py-1 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
            :class="$store.ui.notifOpen ? 'bg-black/5 dark:bg-white/10' : ''">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            <span x-text="new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'})"></span>
            <span x-text="new Date().toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'})"></span>
        </button>

        <div x-show="$store.ui.notifOpen" @click.outside="$store.ui.closeNotif()" x-cloak
            class="absolute bottom-full right-0 mb-1 w-80 rounded-lg shadow-2xl overflow-hidden"
            style="background-color:var(--bg-elevated);border:1px solid var(--border);z-index:calc(var(--z-ui) + 1)">
            <div class="px-4 py-3 border-b" style="border-color:var(--border);background-color:var(--bg-header)">
                <p class="text-sm font-semibold" style="color:var(--text-primary)">Notifikasi</p>
            </div>
            <div class="p-8 text-center">
                <svg class="w-10 h-10 mx-auto mb-2" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                <p class="text-sm" style="color:var(--text-muted)">Belum ada notifikasi</p>
            </div>
        </div>
    </div>
</div>
