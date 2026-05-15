<div x-data="{
    noRawat: '',
    noRkmMedis: '',
    pasien: null,
    loading: false,
    data: [],
    charts: [],

    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data) {
            this.noRawat = win.data.no_rawat || '';
            this.noRkmMedis = win.data.no_rkm_medis || '';
            this.pasien = win.data;
        }
        this.load();
    },

    async load() {
        if (!this.noRawat) return;
        this.loading = true;
        this.destroyCharts();
        try {
            const res = await this.$store.api.get('/tindakan/soap-grafik-rawat/' + encodeURIComponent(this.noRawat));
            this.data = res || [];
            this.$nextTick(() => this.renderCharts());
        } catch (e) { console.error(e); }
        this.loading = false;
    },

    destroyCharts() {
        this.charts.forEach(c => c.destroy());
        this.charts = [];
    },

    renderCharts() {
        if (!this.data.length || typeof Chart === 'undefined') return;

        const labels = this.data.map(d => {
            const t = d.tgl_perawatan || '';
            const j = (d.jam_rawat || '').slice(0, 5);
            return t + ' ' + j;
        });

        const parseTensi = (v) => {
            if (!v) return null;
            const parts = v.split('/');
            return { sistolik: parseFloat(parts[0]), diastolik: parseFloat(parts[1]) };
        };

        const make = (id, label, data, color, opts = {}) => {
            const ctx = document.getElementById(id);
            if (!ctx) return;
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label,
                        data,
                        borderColor: color,
                        backgroundColor: color + '20',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        spanGaps: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { font: { size: 9 }, maxRotation: 45 }, grid: { display: false } },
                        y: { beginAtZero: false, grid: { color: '#e5e7eb' }, ticks: { font: { size: 9 } } }
                    },
                    ...opts,
                }
            });
            this.charts.push(chart);
        };

        const sistolik = this.data.map(d => { const p = parseTensi(d.tensi); return p?.sistolik ?? null; });
        const diastolik = this.data.map(d => { const p = parseTensi(d.tensi); return p?.diastolik ?? null; });

        make('chart-tensi', 'Tensi Sistolik', sistolik, '#ef4444', {
            plugins: { legend: { display: true, labels: { font: { size: 9 }, boxWidth: 12 } } }
        });
        make('chart-tensi-diastolik', 'Tensi Diastolik', diastolik, '#f97316', {
            plugins: { legend: { display: true, labels: { font: { size: 9 }, boxWidth: 12 } } }
        });
        make('chart-nadi', 'Nadi', this.data.map(d => parseFloat(d.nadi) || null), '#3b82f6');
        make('chart-suhu', 'Suhu', this.data.map(d => parseFloat(d.suhu_tubuh) || null), '#22c55e');
        make('chart-respirasi', 'Respirasi', this.data.map(d => parseFloat(d.respirasi) || null), '#a855f7');
        make('chart-spo2', 'SpO2', this.data.map(d => parseFloat(d.spo2) || null), '#06b6d4');
        make('chart-gcs', 'GCS', this.data.map(d => parseFloat(d.gcs) || null), '#ec4899', {
            scales: { y: { beginAtZero: true, max: 15, grid: { color: '#e5e7eb' }, ticks: { font: { size: 9 } } },
                      x: { ticks: { font: { size: 9 }, maxRotation: 45 }, grid: { display: false } } }
        });
    },

    close() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        if (winId) this.$store.windows.close(winId);
    },
}"
    class="flex flex-col h-full" style="color:var(--text-primary)">

    {{-- Header --}}
    <div class="flex items-center justify-between px-3 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" style="color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
            </svg>
            <h3 class="text-sm font-bold" style="color:#16a34a">Grafik Vital Sign</h3>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px]" style="color:var(--text-muted)" x-text="'No.Rawat: ' + (noRawat || '-')"></span>
            <button @click="load" class="p-1 rounded hover:bg-black/10 dark:hover:bg-white/10" title="Refresh">
                <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto min-h-0 p-4">
        <div x-show="loading" class="text-center py-6 text-xs" style="color:var(--text-muted)">Memuat data grafik...</div>
        <div x-show="!loading && !data.length" class="text-center py-6 text-xs" style="color:var(--text-muted)">Belum ada data vital sign</div>
        <div x-show="!loading && data.length" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#ef4444">Tensi Sistolik</h4>
                    <div class="h-28"><canvas id="chart-tensi"></canvas></div>
                </div>
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#f97316">Tensi Diastolik</h4>
                    <div class="h-28"><canvas id="chart-tensi-diastolik"></canvas></div>
                </div>
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#3b82f6">Nadi</h4>
                    <div class="h-28"><canvas id="chart-nadi"></canvas></div>
                </div>
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#22c55e">Suhu</h4>
                    <div class="h-28"><canvas id="chart-suhu"></canvas></div>
                </div>
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#a855f7">Respirasi</h4>
                    <div class="h-28"><canvas id="chart-respirasi"></canvas></div>
                </div>
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#06b6d4">SpO2</h4>
                    <div class="h-28"><canvas id="chart-spo2"></canvas></div>
                </div>
                <div>
                    <h4 class="text-[10px] font-semibold uppercase mb-1" style="color:#ec4899">GCS</h4>
                    <div class="h-28"><canvas id="chart-gcs"></canvas></div>
                </div>
            </div>

            {{-- Detail Table --}}
            <div class="mt-4">
                <h4 class="text-[10px] font-semibold uppercase mb-2" style="color:var(--text-muted)">Detail Data</h4>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-[10px] uppercase" style="color:var(--text-muted)">
                            <th class="text-left px-2 py-1 font-medium">Waktu</th>
                            <th class="text-center px-2 py-1 font-medium">Tensi</th>
                            <th class="text-center px-2 py-1 font-medium">Nadi</th>
                            <th class="text-center px-2 py-1 font-medium">Suhu</th>
                            <th class="text-center px-2 py-1 font-medium">RR</th>
                            <th class="text-center px-2 py-1 font-medium">SpO2</th>
                            <th class="text-center px-2 py-1 font-medium">GCS</th>
                            <th class="text-center px-2 py-1 font-medium">Kesadaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="g in data" :key="g.tgl_perawatan + g.jam_rawat">
                            <tr class="border-t" style="border-color:var(--border);color:var(--text-primary)">
                                <td class="px-2 py-1" x-text="g.tgl_perawatan + ' ' + (g.jam_rawat?.slice(0,5) || '')"></td>
                                <td class="px-2 py-1 text-center font-medium" x-text="g.tensi || '-'"></td>
                                <td class="px-2 py-1 text-center" x-text="g.nadi || '-'"></td>
                                <td class="px-2 py-1 text-center" x-text="g.suhu_tubuh || '-'"></td>
                                <td class="px-2 py-1 text-center" x-text="g.respirasi || '-'"></td>
                                <td class="px-2 py-1 text-center" x-text="g.spo2 || '-'"></td>
                                <td class="px-2 py-1 text-center" x-text="g.gcs || '-'"></td>
                                <td class="px-2 py-1 text-center" x-text="g.kesadaran || '-'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
