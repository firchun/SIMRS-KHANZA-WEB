<div x-data="{
    active: 'praktek',
    menu: [
        { key: 'praktek', label: 'Jadwal Praktek', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
        { key: 'lab', label: 'Jadwal Praktek Lab', icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
        { key: 'hfis', label: 'Jadwal HFIS', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    ],
    hari: ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
    loading: false,
    praktekList: [],
    labList: [
        { jenis: 'PK (Patologi Klinik)', pemeriksaan: 'Darah Rutin, Kimia Darah, Urinalisis', hari: ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'], jam_mulai: '07:00', jam_selesai: '20:00', petugas: 'Drs. Ahmad Fauzi, S.Pd' },
        { jenis: 'PK (Patologi Klinik)', pemeriksaan: 'Hormon & Imunologi', hari: ['Senin','Rabu','Jumat'], jam_mulai: '07:00', jam_selesai: '15:00', petugas: 'Dra. Budiarti, M.Si' },
        { jenis: 'MB (Mikrobiologi)', pemeriksaan: 'Kultur Bakteri, Tes Kepekaan', hari: ['Senin','Selasa','Kamis','Jumat'], jam_mulai: '07:00', jam_selesai: '17:00', petugas: 'dr. Catur Wibisono, Sp.MK' },
        { jenis: 'MB (Mikrobiologi)', pemeriksaan: 'Serologi & PCR', hari: ['Senin','Rabu','Jumat','Sabtu'], jam_mulai: '08:00', jam_selesai: '16:00', petugas: 'Dian Kurniawan, S.Si' },
        { jenis: 'PA (Patologi Anatomi)', pemeriksaan: 'PA Jaringan, PA Sitologi', hari: ['Selasa','Kamis','Sabtu'], jam_mulai: '08:00', jam_selesai: '15:00', petugas: 'dr. Elisa Rahmawati, Sp.PA' },
        { jenis: 'PA (Patologi Anatomi)', pemeriksaan: 'Imunohistokimia', hari: ['Rabu','Jumat'], jam_mulai: '08:00', jam_selesai: '14:00', petugas: 'dr. Elisa Rahmawati, Sp.PA' },
    ],
    hfisList: [
        { kode: 'HFIS-001', nama: 'Kunjungan Rawat Jalan', jenis: 'Pelayanan', jadwal: 'Setiap Hari', jam: '08:00 - 20:00', petugas: 'Petugas Pendaftaran' },
        { kode: 'HFIS-002', nama: 'Kunjungan IGD', jenis: 'Pelayanan', jadwal: 'Setiap Hari', jam: '24 Jam', petugas: 'Perawat IGD' },
        { kode: 'HFIS-003', nama: 'Pelayanan Farmasi', jenis: 'Farmasi', jadwal: 'Senin - Sabtu', jam: '07:00 - 21:00', petugas: 'Apoteker' },
        { kode: 'HFIS-004', nama: 'Pelayanan Laboratorium', jenis: 'Penunjang', jadwal: 'Senin - Sabtu', jam: '07:00 - 20:00', petugas: 'Analis Lab' },
        { kode: 'HFIS-005', nama: 'Pelayanan Radiologi', jenis: 'Penunjang', jadwal: 'Senin - Jumat', jam: '08:00 - 16:00', petugas: 'Radiografer' },
        { kode: 'HFIS-006', nama: 'Rekam Medis', jenis: 'Administrasi', jadwal: 'Senin - Jumat', jam: '08:00 - 16:00', petugas: 'Petugas RM' },
        { kode: 'HFIS-007', nama: 'Konsultasi Gizi', jenis: 'Pelayanan', jadwal: 'Senin - Jumat', jam: '09:00 - 15:00', petugas: 'Ahli Gizi' },
        { kode: 'HFIS-008', nama: 'Fisioterapi', jenis: 'Pelayanan', jadwal: 'Senin - Sabtu', jam: '08:00 - 16:00', petugas: 'Fisioterapis' },
    ],
    filterHari: '',
    filterPoli: '',

    get filteredPraktek() {
        return this.praktekList.filter(p =>
            (!this.filterHari || p.hari_kerja === this.filterHari) &&
            (!this.filterPoli || p.nm_poli === this.filterPoli)
        );
    },

    async fetchPraktek() {
        this.loading = true;
        try {
            const res = await this.$store.api.get('/jadwal/praktek');
            this.praktekList = res || [];
        } catch (e) {
            console.error(e);
            this.praktekList = [];
        } finally {
            this.loading = false;
        }
    },

    init() {
        this.fetchPraktek();
    }
}" @mousedown.stop class="flex h-full gap-0">
    <div @mousedown.stop class="w-44 shrink-0 flex flex-col overflow-y-auto border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
        <template x-for="item in menu" :key="item.key">
            <button @mousedown.stop @click="active = item.key; filterHari = ''; filterPoli = ''"
                class="flex items-center gap-2.5 px-3 py-3 text-xs text-left transition-colors border-l-2"
                :class="active === item.key ? 'font-semibold' : ''"
                :style="active === item.key ? 'background-color:var(--bg-hover);border-color:var(--accent-blue);color:var(--accent-blue)' : 'border-color:transparent;color:var(--text-secondary)'">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path :d="item.icon"/>
                </svg>
                <div>
                    <div x-text="item.label" class="text-xs"></div>
                    <div class="text-[10px]" style="color:var(--text-muted)">Jadwal &amp; Ruang</div>
                </div>
            </button>
        </template>
    </div>

    <div @mousedown.stop class="flex-1 overflow-y-auto p-4 space-y-4 min-h-0" style="color:var(--text-primary)">

        {{-- JADWAL PRAKTEK --}}
        <template x-if="active === 'praktek'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Jadwal Praktek Dokter</h3>
                    <template x-if="loading">
                        <span class="text-xs" style="color:var(--text-muted)">Memuat...</span>
                    </template>
                    <div class="flex items-center gap-2">
                        <select @mousedown.stop x-model="filterHari" class="form-select text-xs w-28">
                            <option value="">Semua Hari</option>
                            <template x-for="h in hari" :key="h">
                                <option :value="h" x-text="h"></option>
                            </template>
                        </select>
                        <select @mousedown.stop x-model="filterPoli" class="form-select text-xs w-36">
                            <option value="">Semua Poli</option>
                            <template x-for="p in [...new Set(praktekList.map(d => d.nm_poli))]" :key="p">
                                <option :value="p" x-text="p"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div @mousedown.stop class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Dokter</th>
                                <th>Poli</th>
                                <th>Hari</th>
                                <th>Jam Praktek</th>
                                <th>Kuota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="d in filteredPraktek" :key="d.kd_dokter + d.hari_kerja">
                                <tr>
                                    <td @mousedown.stop class="font-medium whitespace-nowrap" x-text="d.nm_dokter"></td>
                                    <td @mousedown.stop><span class="badge badge-info" x-text="d.nm_poli"></span></td>
                                    <td @mousedown.stop class="whitespace-nowrap text-xs font-medium" x-text="d.hari_kerja"></td>
                                    <td @mousedown.stop class="whitespace-nowrap text-xs" x-text="d.jam_mulai + ' - ' + d.jam_selesai"></td>
                                    <td @mousedown.stop class="text-xs" x-text="d.kuota" style="color:var(--text-secondary)"></td>
                                </tr>
                            </template>
                            <tr x-show="!loading && filteredPraktek.length === 0">
                                <td colspan="5" class="text-center py-6 text-xs" style="color:var(--text-muted)">Tidak ada jadwal yang cocok</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- JADWAL LAB --}}
        <template x-if="active === 'lab'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Jadwal Praktek Laboratorium</h3>
                    <div class="flex items-center gap-2">
                        <select @mousedown.stop x-model="filterHari" class="form-select text-xs w-28">
                            <option value="">Semua Hari</option>
                            <template x-for="h in hari" :key="h">
                                <option :value="h" x-text="h"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div class="rounded-lg p-3 border" style="background-color:rgba(59,130,246,0.06);border-color:rgba(59,130,246,0.2)">
                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">PK</div>
                        <div class="text-xs" style="color:var(--text-muted)">Patologi Klinik</div>
                        <div class="text-xs font-medium mt-1" style="color:var(--text-secondary)">Darah, Kimia, Urine, Hormon</div>
                    </div>
                    <div class="rounded-lg p-3 border" style="background-color:rgba(16,185,129,0.06);border-color:rgba(16,185,129,0.2)">
                        <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">MB</div>
                        <div class="text-xs" style="color:var(--text-muted)">Mikrobiologi</div>
                        <div class="text-xs font-medium mt-1" style="color:var(--text-secondary)">Kultur, Serologi, PCR</div>
                    </div>
                    <div class="rounded-lg p-3 border" style="background-color:rgba(168,85,247,0.06);border-color:rgba(168,85,247,0.2)">
                        <div class="text-lg font-bold text-purple-600 dark:text-purple-400">PA</div>
                        <div class="text-xs" style="color:var(--text-muted)">Patologi Anatomi</div>
                        <div class="text-xs font-medium mt-1" style="color:var(--text-secondary)">Jaringan, Sitologi, IHK</div>
                    </div>
                </div>

                <div @mousedown.stop class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Pemeriksaan</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="l in labList.filter(p => !filterHari || p.hari.includes(filterHari))" :key="l.jenis + l.pemeriksaan">
                                <tr>
                                    <td @mousedown.stop><span class="badge" :class="l.jenis.includes('PK') ? 'badge-info' : l.jenis.includes('MB') ? 'badge-success' : 'badge-warning'" x-text="l.jenis"></span></td>
                                    <td @mousedown.stop class="font-medium text-xs" x-text="l.pemeriksaan"></td>
                                    <td @mousedown.stop>
                                        <div class="flex gap-1 flex-wrap">
                                            <template x-for="h in hari">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded" :class="l.hari.includes(h) ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 font-medium' : 'text-gray-400 dark:text-gray-600'" x-text="h.substring(0,3)"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td @mousedown.stop class="whitespace-nowrap text-xs" x-text="l.jam_mulai + ' - ' + l.jam_selesai"></td>
                                    <td @mousedown.stop class="text-xs" style="color:var(--text-secondary)" x-text="l.petugas"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- JADWAL HFIS --}}
        <template x-if="active === 'hfis'">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">Jadwal HFIS</h3>
                    <select @mousedown.stop x-model="filterHari" class="form-select text-xs w-32">
                        <option value="">Semua</option>
                        <option value="Pelayanan">Pelayanan</option>
                        <option value="Farmasi">Farmasi</option>
                        <option value="Penunjang">Penunjang</option>
                        <option value="Administrasi">Administrasi</option>
                    </select>
                </div>
                <div @mousedown.stop class="overflow-x-auto rounded border" style="border-color:var(--border)">
                    <table class="table-default">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Layanan</th>
                                <th>Jenis</th>
                                <th>Jadwal</th>
                                <th>Jam</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="h in hfisList.filter(p => !filterHari || p.jenis === filterHari)" :key="h.kode">
                                <tr>
                                    <td @mousedown.stop><code class="text-xs px-1.5 py-0.5 rounded font-mono" style="background-color:var(--bg-hover)" x-text="h.kode"></code></td>
                                    <td @mousedown.stop class="font-medium text-xs" x-text="h.nama"></td>
                                    <td @mousedown.stop><span class="badge badge-info" x-text="h.jenis"></span></td>
                                    <td @mousedown.stop class="text-xs" x-text="h.jadwal"></td>
                                    <td @mousedown.stop class="whitespace-nowrap text-xs" x-text="h.jam"></td>
                                    <td @mousedown.stop class="text-xs" style="color:var(--text-secondary)" x-text="h.petugas"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</div>