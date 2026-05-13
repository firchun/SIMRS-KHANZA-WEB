<div x-data="{
    no_rawat: null,
    pasien: null,
    saving: false,
    err: '',
    msg: '',
    form: {
        no_sep: '',
        tgl_sep: new Date().toISOString().slice(0,10),
        jns_pelayanan: '1',
        diagnosa: '',
        poli_tujuan: '',
        no_kartu: '',
        nama_peserta: '',
    },
    init() {
        const data = this.$store.windows.items.find(w => w.id === this.$el.closest('[data-window-id]')?.dataset?.windowId)?.data;
        if (data) {
            this.no_rawat = data.no_rawat;
            this.pasien = data.pasien;
        }
    }
}" class="p-4 space-y-3">
    <div class="flex items-center gap-2 mb-3">
        <div class="w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
            <svg class="w-4 h-4 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div class="text-sm font-semibold">SEP BPJS</div>
            <div class="text-[10px]" style="color:var(--text-muted)">Surat Eligibilitas Peserta</div>
        </div>
    </div>
    <p class="text-xs" style="color:var(--text-secondary)">Modul Bridging BPJS SEP akan diimplementasikan sesuai referensi DlgBridgingSEP.java</p>
</div>
