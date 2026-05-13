<div x-show="$store.ui.startMenuOpen" @click.outside="$store.ui.closeStartMenu()" x-cloak
    x-data="{ query: '', modules: {{ Js::from($modules) }} }"
    class="start-menu" style="z-index:99999">
    <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-800">
        <p class="font-bold text-sm text-white" x-text="$store.auth.user?.name || 'User'"></p>
        <p class="text-xs text-blue-200" x-text="$store.auth.user?.role || 'Operator'"></p>
    </div>
    <div class="px-3 py-2" style="background-color:var(--bg-elevated)">
        <div class="flex items-center gap-1.5 px-2 py-1.5 rounded" style="background-color:var(--bg-input);border:1px solid var(--border)">
            <svg class="w-3.5 h-3.5 shrink-0" style="color:var(--text-muted)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" x-model="query" placeholder="Cari menu..."
                class="w-full bg-transparent text-xs outline-none" style="color:var(--text-primary)">
            <button x-show="query" @click="query = ''" class="text-xs p-0.5 rounded hover:bg-black/10 dark:hover:bg-white/10" style="color:var(--text-muted)">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div class="max-h-64 overflow-y-auto py-1" style="background-color:var(--bg-elevated)">
        <template x-for="mod in modules.filter(m => !query || m.label.toLowerCase().includes(query.toLowerCase()) || m.desc.toLowerCase().includes(query.toLowerCase()))" :key="mod.key">
            <button @click="query = ''; $store.ui.closeStartMenu(); $store.windows.open(mod)"
                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/10 transition-colors text-left" style="color:var(--text-primary)">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow" :class="mod.color + ' bg-gradient-to-br'">
                    <span x-text="mod.short"></span>
                </div>
                <div>
                    <div class="font-medium" x-text="mod.label"></div>
                    <div class="text-xs" style="color:var(--text-muted)" x-text="mod.desc"></div>
                </div>
            </button>
        </template>
        <div x-show="query && modules.filter(m => m.label.toLowerCase().includes(query.toLowerCase()) || m.desc.toLowerCase().includes(query.toLowerCase())).length === 0"
            class="px-4 py-6 text-center text-xs" style="color:var(--text-muted)">
            Tidak ada menu yang cocok
        </div>
    </div>
    <div class="border-t p-2" style="border-color:var(--border);background-color:var(--bg-elevated)">
        <button @click="$store.auth.logout(); window.location.href='/login'"
            class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-black/5 dark:hover:bg-white/10 rounded transition-colors" style="color:var(--accent-red)">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
        </button>
    </div>
</div>
