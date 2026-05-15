@extends('layouts.desktop')

@section('content')
<div class="flex-1 relative overflow-hidden"
    @click="if($store.windows.items.length) $store.windows.focus($store.windows.items[$store.windows.items.length-1].id)"
    @contextmenu.prevent="$store.ui.closeStartMenu()">

    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-blue-900 dark:to-gray-900">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMwMDAiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyek0zNiAyNHYySDI0di0yaDEyeiIvPjwvZz48L2c+PC9zdmc+')] opacity-50 dark:opacity-100 dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyek0zNiAyNHYySDI0di0yaDEyeiIvPjwvZz48L2c+PC9zdmc+')]"></div>
    </div>

    <div class="absolute inset-0 p-4 flex gap-3">
        <div class="flex flex-col flex-wrap gap-x-2 gap-y-1.5 content-start h-full shrink-0" style="width:226px">
            @foreach($modules as $mod)
            <div x-data="desktopIcon({{ Js::from($mod) }})"
                @dblclick="open()"
                class="desktop-icon text-center" style="width:70px">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $mod['color'] }} flex items-center justify-center text-white shadow-lg mb-1 mx-auto">
                    <i class="text-2xl">{!! $mod['icon_raw'] ?? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>' !!}</i>
                </div>
                <span class="text-[11px] leading-tight text-center block truncate" style="color:var(--text-secondary)">{{ $mod['label'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="flex-1">
            <div x-data="desktopWidget()" class="widget-card h-full overflow-auto">
                <template x-if="loading">
                    <div class="flex items-center justify-center h-full" style="color:var(--text-muted)">
                        <p>Memuat data...</p>
                    </div>
                </template>
                <template x-if="!loading && stats">
                    <div>
                        <h2 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color:var(--text-primary)">
                            <svg class="w-4 h-4" style="color:var(--accent-blue)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-8 4 4 4-6"/></svg>
                            Statistik Hari Ini
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-4">
                            <div class="rounded-lg p-3 border" style="background-color:rgba(220,38,38,0.08);border-color:rgba(220,38,38,0.2)">
                                <div class="text-2xl font-bold" style="color:var(--accent-red)" x-text="stats.hari_ini.igd"></div>
                                <div class="text-xs" style="color:var(--text-muted)">IGD</div>
                            </div>
                            <div class="rounded-lg p-3 border" style="background-color:rgba(22,163,74,0.08);border-color:rgba(22,163,74,0.2)">
                                <div class="text-2xl font-bold" style="color:var(--accent-green)" x-text="stats.hari_ini.ralan"></div>
                                <div class="text-xs" style="color:var(--text-muted)">Ralan</div>
                            </div>
                            <div class="rounded-lg p-3 border" style="background-color:rgba(147,51,234,0.08);border-color:rgba(147,51,234,0.2)">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" x-text="stats.hari_ini.ranap"></div>
                                <div class="text-xs" style="color:var(--text-muted)">Ranap</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div>
                                <h3 class="text-xs font-medium mb-2" style="color:var(--text-muted)">IGD</h3>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-yellow-500" :style="'width:' + (stats.igd.total ? (stats.igd.menunggu/stats.igd.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.igd.menunggu + ' menunggu'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-blue-500" :style="'width:' + (stats.igd.total ? (stats.igd.diperiksa/stats.igd.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.igd.diperiksa + ' diperiksa'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-green-500" :style="'width:' + (stats.igd.total ? (stats.igd.selesai/stats.igd.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.igd.selesai + ' selesai'"></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xs font-medium mb-2" style="color:var(--text-muted)">Ralan</h3>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-yellow-500" :style="'width:' + (stats.ralan.total ? (stats.ralan.menunggu/stats.ralan.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.ralan.menunggu + ' menunggu'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-blue-500" :style="'width:' + (stats.ralan.total ? (stats.ralan.diperiksa/stats.ralan.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.ralan.diperiksa + ' diperiksa'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-green-500" :style="'width:' + (stats.ralan.total ? (stats.ralan.selesai/stats.ralan.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.ralan.selesai + ' selesai'"></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xs font-medium mb-2" style="color:var(--text-muted)">Ranap</h3>
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-purple-500" :style="'width:' + (stats.ranap.total ? (stats.ranap.rawat_inap/stats.ranap.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.ranap.rawat_inap + ' rawat inap'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-blue-500" :style="'width:' + (stats.ranap.total ? (stats.ranap.hari_ini_masuk/stats.ranap.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.ranap.hari_ini_masuk + ' masuk'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 rounded-full bg-green-500" :style="'width:' + (stats.ranap.total ? (stats.ranap.hari_ini_keluar/stats.ranap.total*100) : 0) + '%'"></div>
                                        <span class="text-xs" style="color:var(--text-muted)" x-text="stats.ranap.hari_ini_keluar + ' keluar'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BOR & Kamar --}}
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color:var(--text-primary)">
                                <svg class="w-4 h-4" style="color:var(--accent-purple)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path d="M3.75 6h16.5M3.75 12h16.5M3.75 18h16.5"/>
                                </svg>
                                Bed Occupancy
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                {{-- BOR Card --}}
                                <div class="rounded-lg p-3 border" style="background-color:rgba(147,51,234,0.08);border-color:rgba(147,51,234,0.2)">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs" style="color:var(--text-muted)">BOR</span>
                                        <span class="text-lg font-bold" style="color:#9333ea" x-text="stats.ranap.bor + '%'"></span>
                                    </div>
                                    <div class="h-2 rounded-full overflow-hidden" style="background-color:rgba(147,51,234,0.15)">
                                        <div class="h-full rounded-full transition-all duration-500" style="background-color:#9333ea" :style="'width:' + stats.ranap.bor + '%'"></div>
                                    </div>
                                    <div class="flex justify-between mt-1 text-[10px]" style="color:var(--text-muted)">
                                        <span x-text="'Terisi: ' + stats.ranap.occupied_bed"></span>
                                        <span x-text="'Tersedia: ' + stats.ranap.available_bed"></span>
                                    </div>
                                </div>

                                {{-- Total Bed Card --}}
                                <div class="rounded-lg p-3 border" style="background-color:rgba(59,130,246,0.08);border-color:rgba(59,130,246,0.2)">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs" style="color:var(--text-muted)">Total Tempat Tidur</span>
                                        <span class="text-lg font-bold" style="color:#3b82f6" x-text="stats.ranap.total_bed"></span>
                                    </div>
                                    <div class="flex gap-3 mt-1">
                                        <div class="flex items-center gap-1 text-[10px]" style="color:var(--text-muted)">
                                            <span class="w-2 h-2 rounded-full inline-block" style="background-color:#ef4444"></span>
                                            <span x-text="'Terisi ' + stats.ranap.occupied_bed"></span>
                                        </div>
                                        <div class="flex items-center gap-1 text-[10px]" style="color:var(--text-muted)">
                                            <span class="w-2 h-2 rounded-full inline-block" style="background-color:#22c55e"></span>
                                            <span x-text="'Kosong ' + stats.ranap.available_bed"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Per Class Breakdown --}}
                            <div class="space-y-2">
                                <template x-for="k in stats.ranap.kelas" :key="k.kelas">
                                    <div class="rounded-lg p-2.5 border" style="border-color:var(--border);background-color:var(--bg-muted)">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-medium" x-text="'Kelas ' + k.kelas"></span>
                                            <span class="text-[10px]" style="color:var(--text-muted)" x-text="k.terisi + '/' + k.total + ' terisi'"></span>
                                        </div>
                                        <div class="h-2 rounded-full overflow-hidden" style="background-color:rgba(34,197,94,0.15)">
                                            <div class="h-full rounded-full" style="background:linear-gradient(90deg,#ef4444,#f97316,#22c55e)" :style="'width:' + (k.total ? (k.terisi/k.total*100) : 0) + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!stats.ranap.kelas?.length" class="text-center py-4 text-xs" style="color:var(--text-muted)">Tidak ada data kamar</div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @include('desktop.window-container')
</div>

@include('desktop.start-menu')

@include('desktop.taskbar')

<script>
window.modules = {{ Js::from($modules) }};
window.findModuleByKey = function(key) {
    return window.modules.find(m => m.key === key) || null;
};
</script>
@endsection
