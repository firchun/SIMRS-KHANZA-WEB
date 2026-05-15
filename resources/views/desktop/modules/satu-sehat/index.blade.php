<div x-data="{
    activeSidebar: 'dashboard-pengiriman',
    sidebarQ: '',
    dashboardLoading: false,
    sidebarItems: [
        { key: 'dashboard-pengiriman', label: 'Dashboard Pengiriman', icon: 'M3 3v18h18V3H3zm4 14V7m4 10v-7m4 7v-4m4 4V3', dash: true },
        { key: 'encounter', label: 'Encounter', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
        { key: 'condition', label: 'Condition', icon: 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        { key: 'observation', label: 'Observation', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
        { key: 'procedure', label: 'Procedure', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'medication', label: 'Medication', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'medication-request', label: 'MedicationRequest', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        { key: 'medication-dispense', label: 'MedicationDispense', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
        { key: 'service-request', label: 'ServiceRequest', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
        { key: 'clinical-impression', label: 'ClinicalImpression', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
        { key: 'immunization', label: 'Immunization', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
        { key: 'medication-statement', label: 'MedicationStatement', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'care-plan', label: 'CarePlan', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
        { key: 'specimen', label: 'Specimen', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' },
        { key: 'diagnostic-report', label: 'DiagnosticReport', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { key: 'episode-of-care', label: 'EpisodeOfCare', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
        { key: 'diet', label: 'Diet', icon: 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A2.701 2.701 0 003 15.546m15-4.778c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A2.701 2.701 0 003 10.768' },
    ],

    get filteredSidebar() {
        if (!this.sidebarQ) return this.sidebarItems;
        const q = this.sidebarQ.toLowerCase();
        return this.sidebarItems.filter(i =>
            i.key.toLowerCase().includes(q) ||
            i.label.toLowerCase().includes(q)
        );
    },

    setSidebar(key) {
        this.activeSidebar = key;
    },

    dashboardTgl1: new Date(Date.now() - 60 * 24 * 60 * 60 * 1000).toISOString().slice(0,10),
    dashboardTgl2: new Date().toISOString().slice(0,10),
    dashboardCounts: [],
    dashboardTotalCount: 0,

    cardColors: [
        { key: 'encounter', bg: 'bg-blue-500/10', border: 'border-blue-500/20', text: 'text-blue-600' },
        { key: 'condition', bg: 'bg-emerald-500/10', border: 'border-emerald-500/20', text: 'text-emerald-600' },
        { key: 'observation', bg: 'bg-amber-500/10', border: 'border-amber-500/20', text: 'text-amber-600' },
        { key: 'procedure', bg: 'bg-violet-500/10', border: 'border-violet-500/20', text: 'text-violet-600' },
        { key: 'medication', bg: 'bg-rose-500/10', border: 'border-rose-500/20', text: 'text-rose-600' },
        { key: 'medication-request', bg: 'bg-cyan-500/10', border: 'border-cyan-500/20', text: 'text-cyan-600' },
        { key: 'medication-dispense', bg: 'bg-teal-500/10', border: 'border-teal-500/20', text: 'text-teal-600' },
        { key: 'service-request', bg: 'bg-orange-500/10', border: 'border-orange-500/20', text: 'text-orange-600' },
        { key: 'clinical-impression', bg: 'bg-pink-500/10', border: 'border-pink-500/20', text: 'text-pink-600' },
        { key: 'immunization', bg: 'bg-green-500/10', border: 'border-green-500/20', text: 'text-green-600' },
        { key: 'medication-statement', bg: 'bg-indigo-500/10', border: 'border-indigo-500/20', text: 'text-indigo-600' },
        { key: 'care-plan', bg: 'bg-purple-500/10', border: 'border-purple-500/20', text: 'text-purple-600' },
        { key: 'specimen', bg: 'bg-yellow-500/10', border: 'border-yellow-500/20', text: 'text-yellow-600' },
        { key: 'diagnostic-report', bg: 'bg-red-500/10', border: 'border-red-500/20', text: 'text-red-600' },
        { key: 'episode-of-care', bg: 'bg-sky-500/10', border: 'border-sky-500/20', text: 'text-sky-600' },
        { key: 'diet', bg: 'bg-lime-500/10', border: 'border-lime-500/20', text: 'text-lime-600' },
    ],

    init() {
        this.fetchDashboard();
    },

    getColor(key) {
        return this.cardColors.find(c => c.key === key) || { bg: 'bg-gray-500/10', border: 'border-gray-500/20', text: 'text-gray-500' };
    },

    async fetchDashboard() {
        this.dashboardLoading = true;
        try {
            const res = await this.$store.api.get('/satusehat/dashboard?tgl1=' + this.dashboardTgl1 + '&tgl2=' + this.dashboardTgl2);
            this.dashboardCounts = res.data || [];
            this.dashboardTotalCount = res.total || 0;
        } catch (e) {
            this.dashboardCounts = [];
            this.dashboardTotalCount = 0;
        } finally {
            this.dashboardLoading = false;
        }
    },

    get dashboardTotal() {
        return this.dashboardTotalCount;
    },

    get selectedLabel() {
        return this.sidebarItems.find(i => i.key === this.activeSidebar)?.label || this.activeSidebar;
    },
}"
    class="flex h-full" style="color:var(--text-primary)">

    {{-- Sidebar --}}
    <div class="w-56 shrink-0 flex flex-col border-r" style="border-color:var(--border);background-color:var(--bg-muted)">
        <div class="px-4 py-3 border-b shrink-0" style="border-color:var(--border)">
            <div class="text-sm font-semibold">Bridging Satu sehat</div>
            <div class="text-[10px]" style="color:var(--text-muted)">FHIR Resources</div>
        </div>
        <div class="px-2 py-1.5 border-b shrink-0" style="border-color:var(--border)">
            <input type="text" x-model="sidebarQ" placeholder="Cari resource..." class="form-input text-[11px] py-1 w-full">
        </div>
        <div class="flex-1 overflow-y-auto min-h-0 py-1">
            <template x-for="(item, i) in filteredSidebar" :key="item.key">
                <div>
                    <button @click="setSidebar(item.key)"
                        class="w-full text-left px-3 py-1.5 text-xs flex items-center gap-2 hover:bg-black/5 dark:hover:bg-white/5 transition-colors rounded mx-1"
                        :class="activeSidebar === item.key ? 'bg-blue-50 dark:bg-blue-900/20 font-semibold' : ''"
                        style="color:var(--text-primary)">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path :d="item.icon"/>
                        </svg>
                        <span x-text="item.label"></span>
                    </button>
                    {{-- Separator after dashboard --}}
                    <div x-show="item.dash" class="border-b mx-3 my-1.5" style="border-color:var(--border)"></div>
                </div>
            </template>
            <div x-show="!filteredSidebar.length" class="px-3 py-4 text-xs text-center" style="color:var(--text-muted)">Resource tidak ditemukan</div>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Toolbar --}}
        <div class="flex items-center gap-2 px-4 py-2 border-b shrink-0" style="border-color:var(--border);background-color:var(--bg-muted)">
            <svg class="w-4 h-4 shrink-0" style="color:var(--accent-blue)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/>
            </svg>
            <span class="text-xs font-medium" x-text="selectedLabel"></span>
            <span class="text-[10px] px-1.5 py-0.5 rounded" style="background-color:rgba(59,130,246,0.1);color:rgb(59,130,246)">FHIR R4</span>
            <div class="flex-1"></div>
            <button class="p-1 rounded hover:bg-black/10 dark:hover:bg-white/10" title="Sync">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                </svg>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="flex-1 overflow-y-auto min-h-0 p-4" style="background-color:var(--bg-elevated)">
            {{-- Dashboard Pengiriman --}}
            <template x-if="activeSidebar === 'dashboard-pengiriman'">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold" style="color:var(--text-primary)">Dashboard Pengiriman</h2>
                            <p class="text-[11px] mt-0.5" style="color:var(--text-muted)">Ringkasan jumlah resource FHIR yang telah dikirim</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="date" x-model="dashboardTgl1" @change.debounce="fetchDashboard" class="form-input text-[11px] py-1 w-36">
                            <span class="text-[11px]" style="color:var(--text-muted)">s/d</span>
                            <input type="date" x-model="dashboardTgl2" @change.debounce="fetchDashboard" class="form-input text-[11px] py-1 w-36">
                        </div>
                    </div>

                    <template x-if="dashboardLoading">
                        <div class="flex items-center justify-center py-16">
                            <svg class="w-6 h-6 animate-spin" style="color:var(--text-muted)" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="ml-2 text-xs" style="color:var(--text-muted)">Memuat data...</span>
                        </div>
                    </template>
                    <template x-if="!dashboardLoading">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5">
                            <template x-for="item in dashboardCounts" :key="item.key">
                                <div class="rounded-lg border p-3 transition-all hover:shadow-sm hover:-translate-y-0.5"
                                    :class="[getColor(item.key).bg, getColor(item.key).border]">
                                    <div class="text-[10px] font-medium uppercase tracking-wider mb-1" style="color:var(--text-muted)" x-text="item.label"></div>
                                    <div class="text-xl font-bold" :class="item.count !== null ? getColor(item.key).text : 'text-gray-400'" x-text="item.count !== null ? item.count.toLocaleString() : '-'"></div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="mt-4 flex items-center gap-2 text-[11px]" style="color:var(--text-muted)">
                        <span class="font-medium">Total:</span>
                        <span class="text-base font-bold" style="color:var(--text-primary)" x-text="dashboardTotal.toLocaleString()"></span>
                        <span class="ml-auto">Base URL: https://api-satusehat.kemkes.go.id/fhir-r4/v1</span>
                    </div>
                </div>
            </template>

            {{-- Other resource pages --}}
            <template x-if="activeSidebar !== 'dashboard-pengiriman'">
                <div class="flex flex-col items-center justify-center h-64 text-xs" style="color:var(--text-muted)">
                    <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                        <path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/>
                    </svg>
                    <p class="text-sm font-medium">Resource <span x-text="selectedLabel"></span></p>
                    <p class="mt-1">Data akan ditampilkan setelah integrasi dengan SATU SEHAT</p>
                    <p class="text-[10px] mt-3 px-3 py-1.5 rounded" style="background-color:rgba(59,130,246,0.08);color:var(--accent-blue)">
                        Base URL: https://api-satusehat.kemkes.go.id/fhir-r4/v1
                    </p>
                </div>
            </template>
        </div>
    </div>
</div>
