<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DesktopController extends Controller
{
    protected $modules;

    public function __construct()
    {
        $this->modules = [
            ['key' => 'igd', 'label' => 'IGD', 'icon' => 'emergency', 'short' => 'ER', 'color' => 'from-red-500 to-red-700', 'desc' => 'IGD Registrasi & Tindakan', 'width' => 960, 'height' => 680],
            ['key' => 'ranap', 'label' => 'Rawat Inap', 'icon' => 'hotel', 'short' => 'RI', 'color' => 'from-indigo-500 to-indigo-700', 'desc' => 'Rawat Inap & Kamar', 'width' => 1060, 'height' => 680],
            ['key' => 'pasien', 'label' => 'Pasien', 'icon' => 'badge', 'short' => 'PS', 'color' => 'from-blue-500 to-blue-700', 'desc' => 'Data, Registrasi & Riwayat Pasien', 'width' => 960, 'height' => 680],
            ['key' => 'ralan', 'label' => 'Rawat Jalan', 'icon' => 'stethoscope', 'short' => 'RJ', 'color' => 'from-green-500 to-green-700', 'desc' => 'Rawat Jalan & Pemeriksaan', 'width' => 1060, 'height' => 680],
            ['key' => 'settings', 'label' => 'Settings', 'icon' => 'settings', 'short' => 'ST', 'color' => 'from-slate-500 to-slate-700', 'desc' => 'Aplikasi & Pengaturan', 'width' => 960, 'height' => 680],
            ['key' => 'developers', 'label' => 'Developers API', 'icon' => 'code', 'short' => 'DV', 'color' => 'from-gray-500 to-gray-700', 'desc' => 'Dokumentasi REST API', 'width' => 960, 'height' => 700],
            ['key' => 'jadwal', 'label' => 'Jadwal', 'icon' => 'calendar', 'short' => 'JD', 'color' => 'from-cyan-500 to-cyan-700', 'desc' => 'Jadwal Praktek & Lab', 'width' => 960, 'height' => 660],
            ['key' => 'kasir', 'label' => 'Kasir', 'icon' => 'receipt', 'short' => 'KS', 'color' => 'from-yellow-500 to-yellow-700', 'desc' => 'Pembayaran & Laporan', 'width' => 1060, 'height' => 700],
            ['key' => 'about', 'label' => 'Tentang Aplikasi', 'icon' => 'info', 'short' => 'AB', 'color' => 'from-blue-500 to-cyan-500', 'desc' => 'Tentang & Lisensi Aplikasi', 'width' => 520, 'height' => 420],
            ['key' => 'peresepan', 'label' => 'Peresepan', 'icon' => 'receipt_long', 'short' => 'RX', 'color' => 'from-emerald-500 to-emerald-700', 'desc' => 'Manajemen Resep & Obat', 'width' => 960, 'height' => 680],
            ['key' => 'satu-sehat', 'label' => 'Bridging Satu sehat', 'icon' => 'shield_cross', 'short' => 'SS', 'color' => 'from-blue-500 to-indigo-800', 'desc' => 'Integrasi SATU SEHAT KEMKES FHIR', 'width' => 960, 'height' => 680],
            ['key' => 'bridging-bpjs', 'label' => 'Bridging BPJS', 'icon' => 'sync_alt', 'short' => 'BP', 'color' => 'from-sky-500 to-cyan-700', 'desc' => 'Integrasi BPJS Kesehatan', 'width' => 960, 'height' => 680],
            ['key' => 'registrasi', 'label' => 'Registrasi', 'icon' => 'clipboard', 'short' => 'RG', 'color' => 'from-rose-500 to-rose-700', 'desc' => 'Registrasi & Antrian Pasien', 'width' => 800, 'height' => 680],
            ['key' => 'berkas-perawatan', 'label' => 'Berkas Perawatan', 'icon' => 'folder', 'short' => 'BP', 'color' => 'from-orange-500 to-orange-700', 'desc' => 'Folder Dokumen Pasien', 'width' => 960, 'height' => 680],
            ['key' => 'data-tindakan', 'label' => 'Data Tindakan', 'icon' => 'list_alt', 'short' => 'DT', 'color' => 'from-teal-500 to-teal-700', 'desc' => 'Master Tarif & Tindakan Medis', 'width' => 900, 'height' => 640],
            ['key' => 'chatbot', 'label' => 'Chatbot AI', 'icon' => 'chat', 'short' => 'AI', 'color' => 'from-emerald-500 to-emerald-700', 'desc' => 'Asisten Kecerdasan Buatan', 'width' => 480, 'height' => 640],
            ['key' => 'apotek', 'label' => 'Apotik/Farmasi', 'icon' => 'local_pharmacy', 'short' => 'AF', 'color' => 'from-green-600 to-green-800', 'desc' => 'Data Obat & Farmasi', 'width' => 1100, 'height' => 700],
            ['key' => 'laboratorium', 'label' => 'Laboratorium', 'icon' => 'biotech', 'short' => 'LB', 'color' => 'from-violet-500 to-violet-700', 'desc' => 'Lab PK, PA & MB', 'width' => 1060, 'height' => 680],
            ['key' => 'icd', 'label' => 'ICD', 'icon' => 'medical_services', 'short' => 'IC', 'color' => 'from-purple-500 to-purple-700', 'desc' => 'ICD 10 Penyakit & ICD 9 Tindakan', 'width' => 860, 'height' => 620],
            ['key' => 'pasien-detail', 'label' => 'Detail Pasien', 'icon' => 'info', 'short' => 'PD', 'color' => 'from-blue-500 to-blue-700', 'desc' => 'Detail Pasien & Registrasi', 'width' => 640, 'height' => 520],
        ];
    }

    public function index()
    {
        return view('desktop.index', ['modules' => $this->modules]);
    }

    public function module($module)
    {
        $view = match ($module) {
            'error' => 'desktop.modules.error',
            'igd' => 'desktop.modules.igd.index',
            'igd-tindakan' => 'desktop.modules.igd.tindakan',
            'grafik-vital-sign' => 'desktop.modules.igd.grafik-vital-sign',
            'ralan-examination' => 'desktop.modules.igd.tindakan',
            'ranap-admission' => 'desktop.modules.ranap.admission',
            'pasien' => 'desktop.modules.pasien.index',
            'pasien-riwayat' => 'desktop.modules.pasien.riwayat',
            'settings' => 'desktop.modules.settings.index',
            'developers' => 'desktop.modules.developers.index',
            'jadwal' => 'desktop.modules.jadwal.index',
            'kasir' => 'desktop.modules.kasir.index',
            'kasir-nota' => 'desktop.modules.kasir.nota',
            'ranap' => 'desktop.modules.ranap.index',
            'ranap-tindakan' => 'desktop.modules.igd.tindakan',
            'ralan' => 'desktop.modules.ralan.queue',
            'registrasi' => 'desktop.modules.registrasi.index',
            'berkas-perawatan' => 'desktop.modules.berkas-perawatan.index',
            'data-tindakan' => 'desktop.modules.data-tindakan.index',
            'icd' => 'desktop.modules.icd.index',
            'about' => 'desktop.modules.about.index',
            'peresepan' => 'desktop.modules.peresepan.index',
            'satu-sehat' => 'desktop.modules.satu-sehat.index',
            'apotek' => 'desktop.modules.apotek.index',
            'laboratorium' => 'desktop.modules.laboratorium.index',
            'laboratorium-hasil' => 'desktop.modules.laboratorium.hasil',
            'chatbot' => 'desktop.modules.chatbot.index',
            'pasien-detail' => 'desktop.modules.pasien.detail',
            'bridging-bpjs' => 'desktop.modules.bridging-bpjs.index',
            'bridging-sep' => 'desktop.modules.bridging.sep',
            'bridging-surat-kontrol' => 'desktop.modules.bridging.surat-kontrol',
            'bridging-prb' => 'desktop.modules.bridging.prb',
            default => null,
        };

        if ($view && view()->exists($view)) {
            return view($view);
        }

        return response('<div class="flex items-center justify-center h-full text-gray-400"><p>Module <strong>' . e($module) . '</strong> not available</p></div>');
    }
}
