<div x-data="{
    no_rawat: null,
    pasien: null,
    init() {
        const data = this.$store.windows.items.find(w => w.id === this.$el.closest('[data-window-id]')?.dataset?.windowId)?.data;
        if (data) {
            this.no_rawat = data.no_rawat;
            this.pasien = data.pasien;
        }
    }
}" class="p-4 space-y-3">
    <div class="flex items-center gap-2 mb-3">
        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
            <svg class="w-4 h-4 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
        </div>
        <div>
            <div class="text-sm font-semibold">PRB BPJS</div>
            <div class="text-[10px]" style="color:var(--text-muted)">Program Rujuk Balik BPJS</div>
        </div>
    </div>
    <p class="text-xs" style="color:var(--text-secondary)">Modul Bridging BPJS PRB akan diimplementasikan sesuai referensi DlgBridgingPRB.java</p>
</div>
