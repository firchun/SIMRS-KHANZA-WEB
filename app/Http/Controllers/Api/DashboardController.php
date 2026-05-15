<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Registrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $today = date('Y-m-d');

        // IGD: reg_periksa with kd_poli = 'IGDK'
        $igd = DB::table('reg_periksa')->where('kd_poli', 'IGDK')->where('tgl_registrasi', $today);
        $igdCounts = (clone $igd)
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN stts='Belum' THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN stts='Sudah' THEN 1 ELSE 0 END) as diperiksa")
            ->selectRaw("SUM(CASE WHEN stts IN ('Dirujuk','Dirawat','Meninggal','Pulang Paksa') THEN 1 ELSE 0 END) as selesai")
            ->first();

        // Ralan: reg_periksa with kd_poli != 'IGDK' (excluding Ranap)
        $ralan = DB::table('reg_periksa')->where('kd_poli', '!=', 'IGDK')->where('tgl_registrasi', $today);
        $ralanCounts = (clone $ralan)
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN stts='Belum' THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN stts='Sudah' THEN 1 ELSE 0 END) as diperiksa")
            ->selectRaw("SUM(CASE WHEN stts IN ('Dirujuk','Dirawat','Meninggal','Pulang Paksa') THEN 1 ELSE 0 END) as selesai")
            ->first();

        // Ranap: kamar stats + BOR
        $ranapTotal = DB::table('kamar_inap')->count();
        $ranapRawatInap = DB::table('kamar_inap')->where('stts_pulang', '-')->count();
        $ranapMasuk = DB::table('kamar_inap')->where('tgl_masuk', $today)->count();
        $ranapKeluar = DB::table('kamar_inap')->where('tgl_keluar', $today)->count();

        // Kamar / Bed occupancy
        $totalBed = DB::table('kamar')->where('statusdata', '1')->count();
        $occupiedBed = DB::table('kamar')->where('statusdata', '1')->where('status', 'ISI')->count();
        $availableBed = $totalBed - $occupiedBed;
        $bor = $totalBed > 0 ? round(($occupiedBed / $totalBed) * 100, 1) : 0;

        // Per-class room breakdown
        $kelasStats = DB::table('kamar')
            ->select('kelas')
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN status = 'ISI' THEN 1 ELSE 0 END) as terisi")
            ->where('statusdata', '1')
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        // Available rooms per class for pie chart
        $kelasPie = $kelasStats->map(fn($k) => [
            'kelas' => $k->kelas,
            'total' => (int) $k->total,
            'terisi' => (int) $k->terisi,
            'tersedia' => (int) $k->total - (int) $k->terisi,
        ]);

        return response()->json([
            'hari_ini' => [
                'total' => ($igdCounts->total ?? 0) + ($ralanCounts->total ?? 0),
                'igd' => $igdCounts->total ?? 0,
                'ralan' => $ralanCounts->total ?? 0,
                'ranap' => $ranapRawatInap,
            ],
            'igd' => [
                'total' => $igdCounts->total ?? 0,
                'menunggu' => $igdCounts->menunggu ?? 0,
                'diperiksa' => $igdCounts->diperiksa ?? 0,
                'selesai' => $igdCounts->selesai ?? 0,
            ],
            'ralan' => [
                'total' => $ralanCounts->total ?? 0,
                'menunggu' => $ralanCounts->menunggu ?? 0,
                'diperiksa' => $ralanCounts->diperiksa ?? 0,
                'selesai' => $ralanCounts->selesai ?? 0,
            ],
            'ranap' => [
                'total' => $ranapTotal,
                'rawat_inap' => $ranapRawatInap,
                'hari_ini_masuk' => $ranapMasuk,
                'hari_ini_keluar' => $ranapKeluar,
                'bor' => $bor,
                'total_bed' => $totalBed,
                'occupied_bed' => $occupiedBed,
                'available_bed' => $availableBed,
                'kelas' => $kelasPie,
            ],
        ]);
    }

    public function searchPatientLocation(Request $request)
    {
        $s = $request->q;
        if (!$s || strlen($s) < 2) {
            return response()->json([]);
        }

        $patients = DB::table('pasien')
            ->where(function ($q) use ($s) {
                $q->where('nm_pasien', 'like', "%{$s}%")
                  ->orWhere('no_rkm_medis', 'like', "%{$s}%")
                  ->orWhere('no_ktp', 'like', "%{$s}%");
            })
            ->limit(15)
            ->get();

        $results = $patients->map(function ($p) {
            $noRkm = $p->no_rkm_medis;

            $latestReg = DB::table('reg_periksa')
                ->where('no_rkm_medis', $noRkm)
                ->orderByDesc('tgl_registrasi')
                ->orderByDesc('jam_reg')
                ->first();

            $location = null;
            $moduleKey = null;
            $data = [];

            if ($latestReg) {
                $isIgd = $latestReg->kd_poli === 'IGDK';
                $isRanap = DB::table('kamar_inap')
                    ->where('no_rawat', $latestReg->no_rawat)
                    ->where('stts_pulang', '-')
                    ->exists();

                if ($isIgd && $latestReg->stts === 'Belum') {
                    $location = 'IGD';
                    $moduleKey = 'igd-treatment';
                    $data = ['no_rawat' => $latestReg->no_rawat];
                } elseif ($isRanap) {
                    $kamar = DB::table('kamar_inap')
                        ->where('no_rawat', $latestReg->no_rawat)
                        ->where('stts_pulang', '-')
                        ->leftJoin('bangsal', 'kamar_inap.kd_kamar', '=', 'bangsal.kd_bangsal')
                        ->select('bangsal.nm_bangsal')
                        ->first();
                    $location = 'Ranap - ' . ($kamar->nm_bangsal ?? '-');
                    $moduleKey = 'ranap-care';
                    $data = ['no_rawat' => $latestReg->no_rawat];
                } elseif (!$isIgd && !$isRanap && $latestReg->stts === 'Belum') {
                    $poli = DB::table('poliklinik')->where('kd_poli', $latestReg->kd_poli)->value('nm_poli');
                    $location = 'Ralan - ' . ($poli ?? 'Umum');
                    $moduleKey = 'ralan-queue';
                    $data = ['no_rawat' => $latestReg->no_rawat];
                } else {
                    $location = 'Selesai';
                }
            }

            return [
                'no_rkm_medis' => $p->no_rkm_medis,
                'nm_pasien' => $p->nm_pasien,
                'no_ktp' => $p->no_ktp,
                'jk' => $p->jk,
                'tgl_lahir' => $p->tgl_lahir,
                'location' => $location,
                'module_key' => $moduleKey,
                'data' => $data,
                'tgl_registrasi' => $latestReg->tgl_registrasi ?? null,
            ];
        });

        return response()->json($results);
    }
}
