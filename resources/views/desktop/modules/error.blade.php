<div x-data="{
    errors: [],
    openIdx: null,
    init() {
        this.errors = this.$store.errors.items;
    }
}" class="flex flex-col h-full" style="color:var(--text-primary);background-color:var(--window-bg)">

    {{-- Header --}}
    <div class="flex items-center justify-between px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
            <span class="text-sm font-semibold">Error Log</span>
            <span class="text-[11px] px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-medium" x-text="errors.length"></span>
        </div>
        <button @click="$store.errors.clear(); errors = $store.errors.items"
            class="text-[11px] px-2 py-1 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
            style="color:var(--text-muted)">Hapus Semua</button>
    </div>

    {{-- Error List --}}
    <div class="flex-1 overflow-y-auto min-h-0">
        <template x-if="!errors.length">
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <svg class="w-8 h-8 mx-auto mb-2" style="color:var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs" style="color:var(--text-muted)">Tidak ada error</p>
                </div>
            </div>
        </template>

        <template x-for="(err, idx) in errors" :key="idx">
            <div class="border-b" style="border-color:var(--border)">
                {{-- Error entry header --}}
                <button @mousedown.stop @click="openIdx = openIdx === idx ? null : idx"
                    class="w-full flex items-start gap-2 px-3 py-2 text-left hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium truncate" x-text="err.type"></span>
                            <span class="text-[10px] shrink-0" style="color:var(--text-muted)" x-text="err.timestamp ? new Date(err.timestamp).toLocaleTimeString('id-ID') : ''"></span>
                        </div>
                        <p class="text-[11px] truncate mt-0.5" style="color:var(--text-secondary)" x-text="err.message"></p>
                    </div>
                    <svg class="w-3 h-3 mt-1 shrink-0 transition-transform" style="color:var(--text-muted)"
                        :class="openIdx === idx ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                {{-- Error detail --}}
                <div x-show="openIdx === idx" class="px-3 pb-2 space-y-1 text-[11px] font-mono" style="color:var(--text-secondary)">
                    <template x-if="err.file">
                        <div><span class="font-semibold">File:</span> <span class="break-all" x-text="err.file"></span></div>
                    </template>
                    <template x-if="err.line">
                        <div><span class="font-semibold">Line:</span> <span x-text="err.line"></span></div>
                    </template>
                    <template x-if="err.stack">
                        <div>
                            <span class="font-semibold">Stack:</span>
                            <pre class="mt-0.5 whitespace-pre-wrap break-all text-[10px] leading-relaxed" x-text="err.stack"></pre>
                        </div>
                    </template>
                    <template x-if="!err.file && !err.line && !err.stack">
                        <div style="color:var(--text-muted)">Tidak ada detail tambahan</div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>
