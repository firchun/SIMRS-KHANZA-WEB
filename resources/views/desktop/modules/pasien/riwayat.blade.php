<div x-data="{
    pasien: null,
    activeTab: 'kunjungan',
    tabs: [
        { key: 'kunjungan', label: 'Kunjungan', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
        { key: 'soap', label: 'SOAP / CPP T', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' },
        { key: 'diagnosa', label: 'Diagnosa', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'tindakan', label: 'Tindakan', icon: 'M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z' },
        { key: 'resep', label: 'Resep', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'tagihan', label: 'Tagihan', icon: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z' },
    ],

    // Dummy data per tab
    kunjunganList: [
        { tgl: '2026-05-12', jenis: 'Ralan', poli: 'Penyakit Dalam', dokter: 'dr. Andi Pratama, Sp.PD', status: 'Selesai', biaya: 450000 },
        { tgl: '2026-05-10', jenis: 'IGD', poli: 'IGD', dokter: 'dr. Jaga', status: 'Selesai', biaya: 325000 },
        { tgl: '2026-05-05', jenis: 'Ralan', poli: 'Anak', dokter: 'dr. Budi Santoso, Sp.A', status: 'Selesai', biaya: 280000 },
        { tgl: '2026-04-28', jenis: 'Ralan', poli: 'Penyakit Dalam', dokter: 'dr. Andi Pratama, Sp.PD', status: 'Selesai', biaya: 380000 },
        { tgl: '2026-04-15', jenis: 'Ranap', poli: '-', dokter: 'dr. Andi Pratama, Sp.PD', status: 'Pulang', biaya: 2500000 },
    ],
    soapList: [
        { tgl: '2026-05-12', jam: '09:15', subjektif: 'Nyeri ulu hati', objektif: 'TD 130/80, Nadi 88', assessment: 'Dyspepsia', plan: 'Antasida, kontrol 1 minggu' },
        { tgl: '2026-05-10', jam: '14:30', subjektif: 'Demam 38°C sejak 2 hari', objektif: 'TD 120/70, Nadi 100', assessment: 'Febris Observation', plan: 'Parasetamol, laboratorium' },
        { tgl: '2026-05-05', jam: '10:00', subjektif: 'Batuk pilek 3 hari', objektif: 'TD 110/70, Nadi 80', assessment: 'ISPA', plan: 'Antibiotik, mukolitik' },
        { tgl: '2026-04-28', jam: '08:45', subjektif: 'Kontrol DM tipe 2', objektif: 'GDS 180', assessment: 'DM Tipe 2 terkontrol', plan: 'Metformin 2x500, diet' },
    ],
    diagnosaList: [
        { tgl: '2026-05-12', kode: 'K30', nama: 'Dyspepsia', tingkat: 'Utama', petugas: 'dr. Andi Pratama' },
        { tgl: '2026-05-10', kode: 'R50.9', nama: 'Febris Observation', tingkat: 'Utama', petugas: 'dr. Jaga' },
        { tgl: '2026-05-10', kode: 'J06.9', nama: 'ISPA', tingkat: 'Sekunder', petugas: 'dr. Jaga' },
        { tgl: '2026-05-05', kode: 'J00', nama: 'Common Cold', tingkat: 'Utama', petugas: 'dr. Budi Santoso' },
        { tgl: '2026-04-28', kode: 'E11', nama: 'DM Tipe 2', tingkat: 'Utama', petugas: 'dr. Andi Pratama' },
        { tgl: '2026-04-15', kode: 'I10', nama: 'Hipertensi', tingkat: 'Utama', petugas: 'dr. Andi Pratama' },
    ],
    tindakanList: [
        { tgl: '2026-05-12', nama: 'EKG', biaya: 150000, petugas: 'dr. Andi Pratama' },
        { tgl: '2026-05-12', nama: 'Laboratorium Darah Lengkap', biaya: 120000, petugas: 'Petugas Lab' },
        { tgl: '2026-05-10', nama: 'Infus RL', biaya: 75000, petugas: 'Perawat Jaga' },
        { tgl: '2026-05-10', nama: 'Injeksi Antipiretik', biaya: 35000, petugas: 'Perawat Jaga' },
        { tgl: '2026-04-15', nama: 'Rontgen Thorax', biaya: 200000, petugas: 'Radiografer' },
    ],
    resepList: [
        { tgl: '2026-05-12', obat: 'Antasida Sirup', jumlah: 1, satuan: 'Botol', aturan: '3x1 sdm' },
        { tgl: '2026-05-12', obat: 'Omeprazole', jumlah: 10, satuan: 'Tablet', aturan: '2x1' },
        { tgl: '2026-05-10', obat: 'Parasetamol 500mg', jumlah: 12, satuan: 'Tablet', aturan: '3x1' },
        { tgl: '2026-05-05', obat: 'Amoxicillin', jumlah: 15, satuan: 'Kapsul', aturan: '3x1' },
        { tgl: '2026-05-05', obat: 'GG Sirup', jumlah: 1, satuan: 'Botol', aturan: '3x1 sdm' },
        { tgl: '2026-04-28', obat: 'Metformin 500mg', jumlah: 60, satuan: 'Tablet', aturan: '2x1' },
    ],
    tagihanList: [
        { tgl: '2026-05-12', jenis: 'Ralan', total: 450000, dibayar: 450000, status: 'Lunas', metode: 'Tunai' },
        { tgl: '2026-05-10', jenis: 'IGD', total: 325000, dibayar: 100000, status: 'Sebagian', metode: 'BPJS' },
        { tgl: '2026-05-05', jenis: 'Ralan', total: 280000, dibayar: 280000, status: 'Lunas', metode: 'Debit' },
        { tgl: '2026-04-28', jenis: 'Ralan', total: 380000, dibayar: 380000, status: 'Lunas', metode: 'Tunai' },
        { tgl: '2026-04-15', jenis: 'Ranap', total: 2500000, dibayar: 2500000, status: 'Lunas', metode: 'BPJS' },
    ],

    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data?.pasien) this.pasien = win.data.pasien;
        if (win?.data?.activeTab) this.activeTab = win.data.activeTab;
    },

    formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },
}" class="flex h-full flex-col" style="color:var(--text-primary)">

    {{-- Patient Info --}}
    <template x-if="pasien">
        <div class="flex items-center gap-3 px-3 py-2 border-b shrink-0" style="background-color:var(--bg-muted);border-color:var(--border)">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold text-white" x-text="pasien.nama.charAt(0)"></div>
            <div>
                <div class="text-sm font-bold" x-text="pasien.nama"></div>
                <div class="text-[10px]" style="color:var(--text-muted)">
                    <span x-text="pasien.no_rm"></span>
                    <span class="mx-1">|</span>
                    <span x-text="pasien.nik"></span>
                    <span class="mx-1">|</span>
                    <span x-text="pasien.kota || '-'"></span>
                </div>
            </div>
        </div>
    </template>

    {{-- Tab Bar --}}
    <div class="flex border-b shrink-0 overflow-x-auto" style="border-color:var(--border);background-color:var(--bg-muted)">
        <template x-for="tab in tabs" :key="tab.key">
            <button @click="activeTab = tab.key"
                class="flex items-center gap-1.5 px-3 py-2 text-[11px] font-medium whitespace-nowrap border-b-2 transition-colors"
                :class="activeTab === tab.key ? '' : 'border-transparent'"
                :style="activeTab === tab.key ? 'border-color:var(--accent-blue);color:var(--accent-blue)' : 'color:var(--text-secondary)'">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path :d="tab.icon"/>
                </svg>
                <span x-text="tab.label"></span>
            </button>
        </template>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4">

        {{-- KUNJUNGAN --}}
        <template x-if="activeTab === 'kunjungan'">
            <div class="space-y-3">
                <h4 class="text-xs font-semibold" style="color:var(--accent-blue)">Riwayat Kunjungan</h4>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Status</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="k in kunjunganList" :key="k.tgl + k.jenis">
                                <tr>
                                    <td class="text-xs" x-text="k.tgl"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="{'bg-red-100 dark:bg-red-900/30 text-red-600': k.jenis==='IGD','bg-green-100 dark:bg-green-900/30 text-green-600': k.jenis==='Ralan','bg-blue-100 dark:bg-blue-900/30 text-blue-600': k.jenis==='Ranap'}" x-text="k.jenis"></span></td>
                                    <td class="text-xs" x-text="k.poli"></td>
                                    <td class="text-xs" x-text="k.dokter"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:var(--bg-hover)" x-text="k.status"></span></td>
                                    <td class="text-xs font-medium" x-text="formatRupiah(k.biaya)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- SOAP --}}
        <template x-if="activeTab === 'soap'">
            <div class="space-y-3">
                <h4 class="text-xs font-semibold" style="color:#16A34A">Riwayat SOAP / Catatan Perkembangan</h4>
                <div class="space-y-2">
                    <template x-for="s in soapList" :key="s.tgl + s.jam">
                        <div class="rounded border p-3 text-xs space-y-1" style="border-color:var(--border)">
                            <div class="flex justify-between text-[10px]" style="color:var(--text-muted)">
                                <span class="font-medium" x-text="s.tgl + ' ' + s.jam"></span>
                            </div>
                            <div><span style="color:var(--text-muted)">S:</span> <span x-text="s.subjektif"></span></div>
                            <div><span style="color:var(--text-muted)">O:</span> <span x-text="s.objektif"></span></div>
                            <div><span style="color:var(--text-muted)">A:</span> <span x-text="s.assessment"></span></div>
                            <div><span style="color:var(--text-muted)">P:</span> <span x-text="s.plan"></span></div>
                        </div>
                    </template>
                    <template x-if="!soapList.length">
                        <p class="text-xs" style="color:var(--text-muted)">Belum ada catatan SOAP</p>
                    </template>
                </div>
            </div>
        </template>

        {{-- DIAGNOSA --}}
        <template x-if="activeTab === 'diagnosa'">
            <div class="space-y-3">
                <h4 class="text-xs font-semibold" style="color:#9333EA">Riwayat Diagnosa</h4>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode ICD</th>
                                <th>Diagnosa</th>
                                <th>Tingkat</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="d in diagnosaList" :key="d.tgl + d.kode">
                                <tr>
                                    <td class="text-xs" x-text="d.tgl"></td>
                                    <td class="font-mono text-xs" x-text="d.kode"></td>
                                    <td class="text-xs font-medium" x-text="d.nama"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:var(--bg-hover)" x-text="d.tingkat"></span></td>
                                    <td class="text-xs" x-text="d.petugas"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- TINDAKAN --}}
        <template x-if="activeTab === 'tindakan'">
            <div class="space-y-3">
                <h4 class="text-xs font-semibold" style="color:#EA580C">Riwayat Tindakan</h4>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tindakan</th>
                                <th>Biaya</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="t in tindakanList" :key="t.tgl + t.nama">
                                <tr>
                                    <td class="text-xs" x-text="t.tgl"></td>
                                    <td class="text-xs font-medium" x-text="t.nama"></td>
                                    <td class="text-xs" x-text="formatRupiah(t.biaya)"></td>
                                    <td class="text-xs" x-text="t.petugas"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- RESEP --}}
        <template x-if="activeTab === 'resep'">
            <div class="space-y-3">
                <h4 class="text-xs font-semibold" style="color:#EC4899">Riwayat Resep</h4>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Obat</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Aturan Pakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="r in resepList" :key="r.tgl + r.obat">
                                <tr>
                                    <td class="text-xs" x-text="r.tgl"></td>
                                    <td class="text-xs font-medium" x-text="r.obat"></td>
                                    <td class="text-xs" x-text="r.jumlah"></td>
                                    <td class="text-xs" x-text="r.satuan"></td>
                                    <td class="text-xs" x-text="r.aturan"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- TAGIHAN --}}
        <template x-if="activeTab === 'tagihan'">
            <div class="space-y-3">
                <h4 class="text-xs font-semibold" style="color:#EAB308">Riwayat Tagihan</h4>
                <div class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Total</th>
                                <th>Dibayar</th>
                                <th>Status</th>
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="t in tagihanList" :key="t.tgl + t.jenis">
                                <tr>
                                    <td class="text-xs" x-text="t.tgl"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="{'bg-red-100 dark:bg-red-900/30 text-red-600': t.jenis==='IGD','bg-green-100 dark:bg-green-900/30 text-green-600': t.jenis==='Ralan','bg-blue-100 dark:bg-blue-900/30 text-blue-600': t.jenis==='Ranap'}" x-text="t.jenis"></span></td>
                                    <td class="text-xs font-medium" x-text="formatRupiah(t.total)"></td>
                                    <td class="text-xs" x-text="formatRupiah(t.dibayar)"></td>
                                    <td><span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="{'bg-green-100 dark:bg-green-900/30 text-green-600': t.status==='Lunas','bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600': t.status==='Sebagian','bg-red-100 dark:bg-red-900/30 text-red-600': t.status==='Belum Lunas'}" x-text="t.status"></span></td>
                                    <td class="text-xs" x-text="t.metode"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</div>
