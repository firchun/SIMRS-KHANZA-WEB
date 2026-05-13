<div x-data="{
    pasien: null,
    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data) {
            this.pasien = win.data.pasien || win.data;
        }
    },
    openModule(key, data) {
        const mod = window.findModuleByKey ? window.findModuleByKey(key) : null;
        if (mod) this.$store.windows.open(mod, data);
    },
}" class="flex flex-col h-full p-4 space-y-4" style="color:var(--text-primary)">

    <div class="flex items-center gap-3 pb-3 border-b" style="border-color:var(--border)">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold text-white bg-blue-500"
            x-text="(pasien?.nm_pasien || '?').charAt(0)"></div>
        <div class="flex-1">
            <div class="text-base font-bold" x-text="pasien?.nm_pasien || 'Pasien'"></div>
            <div class="text-xs" style="color:var(--text-muted)">
                <span x-text="'No. RM: ' + (pasien?.no_rkm_medis || '-')"></span>
                <span class="mx-2">|</span>
                <span x-text="pasien?.jk === 'L' ? 'Laki-laki' : pasien?.jk === 'P' ? 'Perempuan' : '-'"></span>
            </div>
        </div>
        <div class="text-right text-xs" style="color:var(--text-muted)">
            <div x-text="pasien?.umur || '-'"></div>
            <div x-show="pasien?.tgl_lahir" x-text="'Lahir: ' + pasien.tgl_lahir"></div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="p-3 rounded border" style="border-color:var(--border);background-color:var(--bg-muted)">
            <h4 class="text-xs font-bold uppercase mb-2" style="color:var(--text-secondary)">Data Registrasi</h4>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">No. Rawat</span>
                    <span class="font-medium" x-text="pasien?.no_rawat || '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Tanggal Masuk</span>
                    <span class="font-medium" x-text="pasien?.tgl_registrasi || '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Jam Masuk</span>
                    <span class="font-medium" x-text="pasien?.jam_reg ? pasien.jam_reg.slice(0,5) : '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Poli</span>
                    <span class="font-medium" x-text="pasien?.nm_poli || pasien?.kd_poli || '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Dokter</span>
                    <span class="font-medium" x-text="pasien?.nm_dokter || '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Status</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium"
                        :style="pasien?.stts === 'Sudah' ? 'background-color:rgba(34,197,94,0.1);color:rgb(34,197,94)' : 'background-color:rgba(234,179,8,0.1);color:rgb(234,179,8)'"
                        x-text="pasien?.stts || '-'"></span>
                </div>
            </div>
        </div>

        <div class="p-3 rounded border" style="border-color:var(--border);background-color:var(--bg-muted)">
            <h4 class="text-xs font-bold uppercase mb-2" style="color:var(--text-secondary)">Data Pasien</h4>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">NIK</span>
                    <span class="font-medium" x-text="pasien?.no_ktp || '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Tgl. Lahir</span>
                    <span class="font-medium" x-text="pasien?.tgl_lahir || '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span style="color:var(--text-muted)">Jenis Kelamin</span>
                    <span class="font-medium" x-text="pasien?.jk === 'L' ? 'Laki-laki' : pasien?.jk === 'P' ? 'Perempuan' : '-'"></span>
                </div>
                <div class="flex justify-between items-start">
                    <span style="color:var(--text-muted)">Alamat</span>
                    <span class="font-medium text-right max-w-[200px]" x-text="pasien?.alamat || '-'"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-3 rounded border" style="border-color:var(--border);background-color:var(--bg-muted)">
        <h4 class="text-xs font-bold uppercase mb-2" style="color:var(--text-secondary)">Aksi</h4>
        <div class="flex flex-wrap gap-2">
            <button @click="openModule('igd-tindakan', { no_rawat: pasien?.no_rawat, pasien: pasien })"
                class="px-3 py-1.5 rounded text-xs font-medium text-white transition-colors"
                style="background-color:#dc2626">
                Buka Tindakan IGD
            </button>
            <button @click="openModule('pasien-riwayat', { no_rkm_medis: pasien?.no_rkm_medis })"
                class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
                style="background-color:var(--bg-elevated);color:var(--text-secondary);border:1px solid var(--border)">
                Riwayat Pasien
            </button>
        </div>
    </div>

</div>
