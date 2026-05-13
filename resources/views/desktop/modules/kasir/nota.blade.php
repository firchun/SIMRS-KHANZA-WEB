<div x-data="{
    tagihan: null,
    paymentMethod: 'tunai',
    jumlahBayar: 0,

    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data?.tagihan) {
            this.tagihan = win.data.tagihan;
            this.jumlahBayar = this.tagihan.sisa;
        }
    },

    formatRupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    },

    prosesBayar() {
        if (!this.jumlahBayar || this.jumlahBayar <= 0 || !this.tagihan) return;
        this.tagihan.dibayar += this.jumlahBayar;
        this.tagihan.sisa = this.tagihan.total - this.tagihan.dibayar;
        this.tagihan.status = this.tagihan.sisa <= 0 ? 'Lunas' : 'Sebagian';

        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);

        const kwt = {
            no: 'KWT-' + String(Date.now()).slice(-5),
            pasien: this.tagihan.nama,
            tgl: new Date().toISOString().split('T')[0],
            total: this.jumlahBayar,
            metode: this.paymentMethod === 'tunai' ? 'Tunai' : this.paymentMethod === 'debit' ? 'Debit' : this.paymentMethod === 'qris' ? 'QRIS' : 'BPJS',
            petugas: 'Admin Kasir'
        };
        if (win?.data?.kasirData) {
            win.data.kasirData.unshift(kwt);
        }

        this.$store.windows.close(winId);
    },

    batal() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        this.$store.windows.close(winId);
    }
}" class="p-4 space-y-4 h-full overflow-y-auto" style="color:var(--text-primary)">
    <template x-if="!tagihan">
        <div class="flex items-center justify-center h-full">
            <p class="text-xs" style="color:var(--text-muted)">Data tagihan tidak ditemukan</p>
        </div>
    </template>

    <template x-if="tagihan">
        <div class="space-y-4">
            <div class="text-center border-b pb-3" style="border-color:var(--border)">
                <h3 class="text-base font-bold" style="color:#EAB308">NOTA PEMBAYARAN</h3>
                <p class="text-[10px]" style="color:var(--text-muted)">RS KHANZA - Sehat Bersama</p>
            </div>

            <div class="space-y-2 rounded-lg border p-3" style="border-color:var(--border)">
                <div class="flex justify-between text-xs py-1">
                    <span style="color:var(--text-secondary)">Pasien</span>
                    <span class="font-medium" x-text="tagihan.nama"></span>
                </div>
                <div class="flex justify-between text-xs py-1">
                    <span style="color:var(--text-secondary)">No. RM</span>
                    <span class="font-mono font-medium" x-text="tagihan.no"></span>
                </div>
                <div class="flex justify-between text-xs py-1">
                    <span style="color:var(--text-secondary)">Penjamin</span>
                    <span x-text="tagihan.penjamin"></span>
                </div>
                <div class="flex justify-between text-xs py-1" x-show="tagihan.poli">
                    <span style="color:var(--text-secondary)">Poli</span>
                    <span x-text="tagihan.poli"></span>
                </div>
                <div class="flex justify-between text-xs py-1" x-show="tagihan.bangsal">
                    <span style="color:var(--text-secondary)">Bangsal</span>
                    <span x-text="tagihan.bangsal + ' - ' + (tagihan.kamar || '')"></span>
                </div>
            </div>

            <div class="rounded-lg border p-3 space-y-2" style="border-color:var(--border)">
                <div class="flex justify-between text-xs py-1">
                    <span style="color:var(--text-secondary)">Total Tagihan</span>
                    <span class="font-semibold" x-text="formatRupiah(tagihan.total)"></span>
                </div>
                <div class="flex justify-between text-xs py-1">
                    <span style="color:var(--text-secondary)">Sudah Dibayar</span>
                    <span class="font-semibold" x-text="formatRupiah(tagihan.dibayar)"></span>
                </div>
                <div class="flex justify-between text-xs py-1 border-t pt-2" style="border-color:var(--border)">
                    <span class="font-semibold">Sisa Tagihan</span>
                    <span class="font-bold text-red-500" x-text="formatRupiah(tagihan.sisa)"></span>
                </div>
            </div>

            <div class="rounded-lg border p-3 space-y-3" style="border-color:var(--border)">
                <label class="text-[10px] font-medium" style="color:var(--text-muted)">Metode Pembayaran</label>
                <div class="flex gap-2">
                    <template x-for="m in [{k:'tunai',l:'Tunai'},{k:'debit',l:'Debit'},{k:'qris',l:'QRIS'},{k:'bpjs',l:'BPJS'}]">
                        <button @click="paymentMethod = m.k" class="flex-1 text-[10px] py-1.5 rounded border text-center font-medium transition-colors"
                            :class="paymentMethod === m.k ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600' : 'border-transparent'"
                            :style="paymentMethod === m.k ? '' : 'border-color:var(--border);color:var(--text-secondary)'" x-text="m.l"></button>
                    </template>
                </div>

                <div>
                    <label class="text-[10px] font-medium" style="color:var(--text-muted)">Jumlah Bayar</label>
                    <input type="number" x-model="jumlahBayar" :max="tagihan?.sisa" class="form-input text-xs w-full mt-1">
                </div>

                <div class="flex justify-between text-xs py-1">
                    <span style="color:var(--text-secondary)">Kembalian</span>
                    <span class="font-medium text-green-500" x-text="formatRupiah(Math.max(0, (parseInt(jumlahBayar) || 0) - (tagihan?.sisa || 0)))"></span>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <button @click="batal()" class="btn btn-secondary text-xs px-4 py-1.5 flex-1">Batal</button>
                <button @click="prosesBayar()" :disabled="!jumlahBayar || jumlahBayar <= 0"
                    class="btn btn-primary text-xs px-4 py-1.5 flex-1 font-semibold"
                    style="background-color:#EAB308"
                    :style="(!jumlahBayar || jumlahBayar <= 0) ? 'opacity:0.5' : ''">
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </template>
</div>
