<div x-data="{
    noorder: '',
    no_rawat: '',
    nm_pasien: '',
    groups: [],
    formData: {},
    loading: false,
    saving: false,
    msg: '',
    err: '',

    init() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win?.data) {
            this.noorder = win.data.noorder || '';
            this.no_rawat = win.data.no_rawat || '';
            this.nm_pasien = win.data.nm_pasien || '';
        }
        if (this.noorder) this.loadData();
    },

    closeWin() {
        const el = this.$el.closest('[data-window-id]');
        const winId = el?.dataset?.windowId;
        const win = this.$store.windows.items.find(w => w.id === winId);
        if (win) this.$store.windows.close(win.id);
    },

    async loadData() {
        this.loading = true;
        try {
            const res = await this.$store.api.get('/lab/data-hasil/' + encodeURIComponent(this.noorder));
            this.groups = res.groups || [];
            this.nm_pasien = this.nm_pasien || res.no_rkm_medis || '';
            this.initFormData(res.existing || []);
        } catch (e) { this.err = e.message || 'Gagal memuat data'; }
        this.loading = false;
    },

    initFormData(existing) {
        const fd = {};
        for (const g of this.groups) {
            for (const t of g.templates) {
                const key = g.kd_jenis_prw + '_' + t.id_template;
                const exist = existing.find(
                    e => e.kd_jenis_prw === g.kd_jenis_prw && e.id_template === t.id_template
                );
                fd[key] = { nilai: exist?.nilai || '', keterangan: exist?.keterangan || '' };
            }
        }
        this.formData = fd;
    },

    async submit() {
        this.saving = true;
        this.msg = '';
        this.err = '';
        try {
            const items = [];
            for (const g of this.groups) {
                for (const t of g.templates) {
                    const key = g.kd_jenis_prw + '_' + t.id_template;
                    const d = this.formData[key];
                    items.push({
                        kd_jenis_prw: g.kd_jenis_prw,
                        id_template: t.id_template,
                        nilai: d?.nilai || '',
                        keterangan: d?.keterangan || '',
                    });
                }
            }
            await this.$store.api.post('/lab/simpan-hasil', {
                noorder: this.noorder,
                kategori: 'PK',
                items,
            });
            this.msg = 'Hasil berhasil disimpan';
            setTimeout(() => this.closeWin(), 1500);
        } catch (e) { this.err = e.message || 'Gagal menyimpan'; }
        this.saving = false;
    },
}" class="flex flex-col h-full overflow-hidden" style="font-size:13px;color:var(--text-primary)">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-2 border-b shrink-0"
        style="background-color:var(--bg-muted);border-color:var(--border)">
        <div class="flex items-center gap-3">
            <h3 class="text-sm font-semibold" x-text="noorder"></h3>

            <span class="text-[12px]" style="color:var(--text-muted)"
                x-text="nm_pasien ? '(' + nm_pasien + ')' : ''"></span>
        </div>

    </div>

    {{-- Messages --}}
    <template x-if="msg">
        <div class="px-4 py-2 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-b shrink-0"
            style="border-color:var(--border)" x-text="msg"></div>
    </template>
    <template x-if="err">
        <div class="px-4 py-2 text-sm bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-b shrink-0"
            style="border-color:var(--border)" x-text="err"></div>
    </template>

    {{-- Loading --}}
    <template x-if="loading">
        <div class="flex items-center justify-center flex-1">
            <p class="text-sm" style="color:var(--text-muted)">Memuat data...</p>
        </div>
    </template>

    {{-- Form --}}
    <template x-if="!loading">
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <template x-for="(g, gi) in groups" :key="g.kd_jenis_prw">
                <div class="rounded border" style="border-color:var(--border)">
                    <div class="px-3 py-1.5 text-sm font-semibold flex items-center justify-between"
                        style="background-color:var(--bg-muted);border-bottom:1px solid var(--border)">
                        <span x-text="g.nm_perawatan"></span>
                        <span class="text-[12px] font-normal" style="color:var(--text-muted)"
                            x-text="'Rp ' + Number(g.tarif || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background-color:var(--bg-muted)">
                                <th class="text-left px-3 py-1 border-r font-medium"
                                    style="border-color:var(--border);width:30%">Pemeriksaan</th>
                                <th class="text-left px-3 py-1 border-r font-medium"
                                    style="border-color:var(--border);width:35%">Hasil</th>
                                <th class="text-left px-3 py-1 border-r font-medium"
                                    style="border-color:var(--border);width:15%">Satuan</th>
                                <th class="text-left px-3 py-1 font-medium" style="width:20%">Nilai Rujukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(t, ti) in g.templates" :key="t.id_template">
                                <tr class="border-t" style="border-color:var(--border)">
                                    <td class="px-3 py-1 text-[12px] font-medium border-r"
                                        style="border-color:var(--border)" x-text="t.pemeriksaan"></td>
                                    <td class="px-3 py-1 border-r" style="border-color:var(--border)">
                                        <input type="text"
                                            x-model="formData[g.kd_jenis_prw + '_' + t.id_template]?.nilai"
                                            class="form-input text-sm w-full py-0.5 px-1" placeholder="Input hasil...">
                                    </td>
                                    <td class="px-3 py-1 text-[12px] border-r" style="border-color:var(--border)"
                                        x-text="t.satuan || '-'"></td>
                                    <td class="px-3 py-1 text-[12px]" style="color:var(--text-muted)"
                                        x-text="t.nilai_rujukan || '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
            <template x-if="!groups.length">
                <p class="text-sm text-center py-8" style="color:var(--text-muted)">Tidak ada item pemeriksaan</p>
            </template>
        </div>
    </template>

    {{-- Footer --}}
    <div class="flex items-center justify-end gap-2 px-4 py-2 border-t shrink-0"
        style="border-color:var(--border);background-color:var(--bg-muted)">
        <button @click="closeWin" class="btn btn-secondary text-sm px-3 py-1">Batal</button>
        <button @click="submit" :disabled="saving || loading"
            class="btn bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-1 rounded font-medium disabled:opacity-50"
            x-text="saving ? 'Menyimpan...' : 'Simpan Hasil'"></button>
    </div>

</div>