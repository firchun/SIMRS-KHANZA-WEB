<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Registrasi, IgdTriage, RalanKunjungan, RanapAdmisi};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $today = now()->startOfDay();

        $igd = Registrasi::where('jenis', 'IGD')->whereDate('created_at', $today);
        $ralan = Registrasi::where('jenis', 'RALAN')->whereDate('created_at', $today);
        $ranap = RanapAdmisi::whereNull('tgl_keluar');

        return response()->json([
            'hari_ini' => [
                'total' => Registrasi::whereDate('created_at', $today)->count(),
                'igd' => $igd->count(),
                'ralan' => $ralan->count(),
                'ranap' => $ranap->count(),
            ],
            'igd' => [
                'total' => $igd->count(),
                'menunggu' => (clone $igd)->where('status', 'antri')->count(),
                'diperiksa' => (clone $igd)->where('status', 'diperiksa')->count(),
                'selesai' => (clone $igd)->where('status', 'selesai')->count(),
            ],
            'ralan' => [
                'total' => $ralan->count(),
                'menunggu' => (clone $ralan)->where('status', 'antri')->count(),
                'diperiksa' => (clone $ralan)->where('status', 'diperiksa')->count(),
                'selesai' => (clone $ralan)->where('status', 'selesai')->count(),
            ],
            'ranap' => [
                'total' => RanapAdmisi::count(),
                'rawat_inap' => $ranap->count(),
                'hari_ini_masuk' => RanapAdmisi::whereDate('tgl_masuk', today())->count(),
                'hari_ini_keluar' => RanapAdmisi::whereDate('tgl_keluar', today())->count(),
            ],
        ]);
    }

    public function searchPatientLocation(Request $request)
    {
        $s = $request->q;
        if (!$s || strlen($s) < 2) {
            return response()->json([]);
        }

        $patients = \App\Models\Pasien::where(function($q) use ($s) {
            $q->where('nama', 'like', "%{$s}%")
              ->orWhere('no_rm', 'like', "%{$s}%")
              ->orWhere('nik', 'like', "%{$s}%");
        })->limit(15)->get();

        $results = $patients->map(function ($p) {
            $latestReg = Registrasi::with(['igdTriage', 'ralanKunjungan', 'ranapAdmisi'])
                ->where('pasien_id', $p->id)
                ->latest()
                ->first();

            $location = null;
            $moduleKey = null;
            $data = [];

            if ($latestReg) {
                if ($latestReg->jenis === 'IGD' && $latestReg->status !== 'selesai') {
                    $location = 'IGD';
                    $moduleKey = 'igd-treatment';
                    $data = ['registrasi_id' => $latestReg->id];
                } elseif ($latestReg->jenis === 'RALAN' && $latestReg->status !== 'selesai') {
                    $location = 'Ralan - ' . ($latestReg->poli ?? 'Umum');
                    $moduleKey = 'ralan-queue';
                    $data = ['kunjungan' => $latestReg->id];
                } elseif ($latestReg->jenis === 'RANAP' && $latestReg->status === 'rawat_inap') {
                    $admisi = $latestReg->ranapAdmisi->first();
                    $location = 'Ranap - ' . ($admisi->bangsal ?? 'Kamar ' . ($admisi->no_kamar ?? '-'));
                    $moduleKey = 'ranap-care';
                    $data = ['admisi_id' => $admisi->id ?? null];
                } else {
                    $location = ucfirst(strtolower($latestReg->jenis)) . ' (selesai)';
                }
            }

            return [
                'id' => $p->id,
                'no_rm' => $p->no_rm,
                'nama' => $p->nama,
                'nik' => $p->nik,
                'jenis_kelamin' => $p->jenis_kelamin,
                'location' => $location,
                'module_key' => $moduleKey,
                'data' => $data,
                'tgl_registrasi' => $latestReg ? $latestReg->tgl_registrasi : null,
                'jenis_registrasi' => $latestReg ? $latestReg->jenis : null,
                'status_registrasi' => $latestReg ? $latestReg->status : null,
            ];
        });

        return response()->json($results);
    }
}
