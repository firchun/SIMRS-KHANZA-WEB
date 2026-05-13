<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function rajal(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;
        $status = $request->status;

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('rawat_jl_drpr', 'reg_periksa.no_rawat', '=', 'rawat_jl_drpr.no_rawat')
            ->where('reg_periksa.kd_poli', '!=', 'IGDK')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
            ->groupBy('reg_periksa.no_rawat');

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.no_rawat', 'LIKE', "%$q%");
            });
        }

        if ($status) {
            if ($status === 'Lunas') {
                $query->where('reg_periksa.status_bayar', 'Sudah Bayar');
            } elseif ($status === 'Belum Lunas') {
                $query->where('reg_periksa.status_bayar', 'Belum Bayar');
            }
        }

        $list = $query->select(
            'reg_periksa.no_rawat',
            'reg_periksa.no_reg',
            'reg_periksa.tgl_registrasi',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'poliklinik.nm_poli',
            'dokter.nm_dokter as nm_dokter',
            'penjab.png_jawab as penjamin',
            'reg_periksa.biaya_reg',
            'reg_periksa.status_bayar',
            DB::raw('COALESCE(SUM(rawat_jl_drpr.biaya_rawat), 0) as total_tindakan'),
            DB::raw('COALESCE(SUM(rawat_jl_drpr.biaya_rawat), 0) + reg_periksa.biaya_reg as total_tagihan')
        )
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->orderBy('reg_periksa.jam_reg', 'desc')
        ->get()
        ->map(function ($r) {
            $dibayar = $r->status_bayar === 'Sudah Bayar' ? $r->total_tagihan : 0;
            return [
                'no_rawat' => $r->no_rawat,
                'no_reg' => $r->no_reg,
                'no_rkm_medis' => $r->no_rkm_medis,
                'nm_pasien' => $r->nm_pasien,
                'tgl_registrasi' => $r->tgl_registrasi,
                'nm_poli' => $r->nm_poli,
                'nm_dokter' => $r->nm_dokter,
                'penjamin' => $r->penjamin,
                'biaya_reg' => (float) $r->biaya_reg,
                'total_tindakan' => (float) $r->total_tindakan,
                'total_tagihan' => (float) $r->total_tagihan,
                'dibayar' => $dibayar,
                'sisa' => $r->total_tagihan - $dibayar,
                'status' => $r->status_bayar === 'Sudah Bayar' ? 'Lunas' : 'Belum Lunas',
            ];
        });

        return response()->json([
            'list' => $list,
            'total_tagihan' => $list->sum('total_tagihan'),
            'total_dibayar' => $list->sum('dibayar'),
            'total_sisa' => $list->sum('sisa'),
        ]);
    }

    public function ranap(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;
        $status = $request->status;

        $sub = DB::table('rawat_inap_drpr')
            ->select('no_rawat', DB::raw('COALESCE(SUM(biaya_rawat),0) as total_tindakan'))
            ->groupBy('no_rawat');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoinSub($sub, 'tindakan_sum', 'reg_periksa.no_rawat', '=', 'tindakan_sum.no_rawat')
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.no_rawat', 'LIKE', "%$q%");
            });
        }

        if ($status) {
            if ($status === 'Lunas') {
                $query->where('reg_periksa.status_bayar', 'Sudah Bayar');
            } elseif ($status === 'Belum Lunas') {
                $query->where('reg_periksa.status_bayar', 'Belum Bayar');
            }
        }

        $list = $query->select(
            'reg_periksa.no_rawat',
            'reg_periksa.no_reg',
            'reg_periksa.tgl_registrasi',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'kamar_inap.tgl_masuk',
            'kamar_inap.tgl_keluar',
            'kamar_inap.trf_kamar',
            'kamar_inap.ttl_biaya as biaya_kamar',
            'kamar_inap.lama as lama_inap',
            'bangsal.nm_bangsal',
            'kamar.kd_kamar',
            'kamar.kelas',
            'reg_periksa.biaya_reg',
            'reg_periksa.status_bayar',
            DB::raw('COALESCE(tindakan_sum.total_tindakan, 0) as total_tindakan')
        )
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->get()
        ->map(function ($r) {
            $total = (float) $r->biaya_reg + (float) $r->biaya_kamar + (float) $r->total_tindakan;
            $dibayar = $r->status_bayar === 'Sudah Bayar' ? $total : 0;
            return [
                'no_rawat' => $r->no_rawat,
                'no_reg' => $r->no_reg,
                'no_rkm_medis' => $r->no_rkm_medis,
                'nm_pasien' => $r->nm_pasien,
                'tgl_registrasi' => $r->tgl_registrasi,
                'tgl_masuk' => $r->tgl_masuk,
                'tgl_keluar' => $r->tgl_keluar,
                'nm_bangsal' => $r->nm_bangsal,
                'kd_kamar' => $r->kd_kamar,
                'kelas' => $r->kelas,
                'lama_inap' => (int) $r->lama_inap,
                'total_tagihan' => $total,
                'dibayar' => $dibayar,
                'sisa' => $total - $dibayar,
                'status' => $r->status_bayar === 'Sudah Bayar' ? 'Lunas' : 'Belum Lunas',
            ];
        });

        return response()->json([
            'list' => $list,
            'total_tagihan' => $list->sum('total_tagihan'),
            'total_dibayar' => $list->sum('dibayar'),
            'total_sisa' => $list->sum('sisa'),
        ]);
    }

    public function kamar(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;

        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('kamar_inap.tgl_masuk', [$tgl1, $tgl2]);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('kamar_inap.no_rawat', 'LIKE', "%$q%");
            });
        }

        $list = $query->select(
            'kamar_inap.no_rawat',
            'kamar_inap.tgl_masuk',
            'kamar_inap.jam_masuk',
            'kamar_inap.tgl_keluar',
            'kamar_inap.jam_keluar',
            'kamar_inap.lama',
            'kamar_inap.trf_kamar',
            'kamar_inap.ttl_biaya',
            'kamar_inap.stts_pulang',
            'kamar_inap.kd_kamar',
            'kamar.kelas',
            'bangsal.nm_bangsal',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'reg_periksa.status_bayar',
        )
        ->orderBy('kamar_inap.tgl_masuk', 'desc')
        ->get()
        ->map(fn($r) => [
            'no_rawat' => $r->no_rawat,
            'no_rkm_medis' => $r->no_rkm_medis,
            'nm_pasien' => $r->nm_pasien,
            'tgl_masuk' => $r->tgl_masuk,
            'jam_masuk' => $r->jam_masuk,
            'tgl_keluar' => $r->tgl_keluar,
            'jam_keluar' => $r->jam_keluar,
            'lama' => (int) $r->lama,
            'trf_kamar' => (float) $r->trf_kamar,
            'ttl_biaya' => (float) $r->ttl_biaya,
            'stts_pulang' => $r->stts_pulang,
            'kd_kamar' => $r->kd_kamar,
            'kelas' => $r->kelas,
            'nm_bangsal' => $r->nm_bangsal,
            'status' => $r->stts_pulang === '-' ? 'Dirawat' : 'Keluar',
        ]);

        return response()->json([
            'list' => $list,
            'total_biaya' => $list->sum('ttl_biaya'),
        ]);
    }

    public function laporan(Request $request)
    {
        $period = $request->period ?? 'hari';
        $tgl1 = $request->tgl1 ?? date('Y-m-d', strtotime('-30 days'));
        $tgl2 = $request->tgl2 ?? date('Y-m-d');

        if ($period === 'hari') {
            $rajal = DB::table('reg_periksa')
                ->leftJoin('rawat_jl_drpr', 'reg_periksa.no_rawat', '=', 'rawat_jl_drpr.no_rawat')
                ->where('reg_periksa.kd_poli', '!=', 'IGDK')
                ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                ->groupBy('reg_periksa.tgl_registrasi')
                ->select(
                    'reg_periksa.tgl_registrasi as tgl',
                    DB::raw('COUNT(DISTINCT reg_periksa.no_rawat) as jumlah_pasien'),
                    DB::raw('COALESCE(SUM(rawat_jl_drpr.biaya_rawat), 0) + SUM(reg_periksa.biaya_reg) as total_rajal')
                )
                ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                ->get();

            $ranap = DB::table('reg_periksa')
                ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->leftJoin('rawat_inap_drpr', 'reg_periksa.no_rawat', '=', 'rawat_inap_drpr.no_rawat')
                ->where('reg_periksa.status_lanjut', 'Ranap')
                ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                ->groupBy('reg_periksa.tgl_registrasi')
                ->select(
                    'reg_periksa.tgl_registrasi as tgl',
                    DB::raw('SUM(kamar_inap.ttl_biaya) + COALESCE(SUM(rawat_inap_drpr.biaya_rawat), 0) as total_ranap')
                )
                ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                ->get();

            $merged = collect($rajal)->keyBy('tgl');
            foreach ($ranap as $r) {
                if ($merged->has($r->tgl)) {
                    $merged[$r->tgl]->total_ranap = $r->total_ranap;
                } else {
                    $merged[$r->tgl] = (object) [
                        'tgl' => $r->tgl,
                        'jumlah_pasien' => 0,
                        'total_rajal' => 0,
                        'total_ranap' => $r->total_ranap,
                    ];
                }
            }

            $list = $merged->sortByDesc('tgl')->values()->map(function ($r) {
                return [
                    'tgl' => $r->tgl,
                    'rajal' => (float) ($r->total_rajal ?? 0),
                    'ranap' => (float) ($r->total_ranap ?? 0),
                    'total' => (float) ($r->total_rajal ?? 0) + (float) ($r->total_ranap ?? 0),
                    'jumlah_pasien' => (int) ($r->jumlah_pasien ?? 0),
                ];
            });

            $total_all = [
                'rajal' => $list->sum('rajal'),
                'ranap' => $list->sum('ranap'),
                'total' => $list->sum('total'),
                'rata_rata' => $list->count() > 0 ? $list->avg('total') : 0,
            ];
        } else {
            $rajal = DB::table('reg_periksa')
                ->leftJoin('rawat_jl_drpr', 'reg_periksa.no_rawat', '=', 'rawat_jl_drpr.no_rawat')
                ->where('reg_periksa.kd_poli', '!=', 'IGDK')
                ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                ->groupBy(DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m')"))
                ->select(
                    DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m') as bulan"),
                    DB::raw('COALESCE(SUM(rawat_jl_drpr.biaya_rawat), 0) + SUM(reg_periksa.biaya_reg) as total_rajal')
                )
                ->orderBy('bulan', 'desc')
                ->get();

            $ranap = DB::table('reg_periksa')
                ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->leftJoin('rawat_inap_drpr', 'reg_periksa.no_rawat', '=', 'rawat_inap_drpr.no_rawat')
                ->where('reg_periksa.status_lanjut', 'Ranap')
                ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                ->groupBy(DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m')"))
                ->select(
                    DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m') as bulan"),
                    DB::raw('SUM(kamar_inap.ttl_biaya) + COALESCE(SUM(rawat_inap_drpr.biaya_rawat), 0) as total_ranap')
                )
                ->orderBy('bulan', 'desc')
                ->get();

            $merged = collect($rajal)->keyBy('bulan');
            foreach ($ranap as $r) {
                if ($merged->has($r->bulan)) {
                    $merged[$r->bulan]->total_ranap = $r->total_ranap;
                } else {
                    $merged[$r->bulan] = (object) [
                        'bulan' => $r->bulan,
                        'total_rajal' => 0,
                        'total_ranap' => $r->total_ranap,
                    ];
                }
            }

            $list = $merged->sortByDesc('bulan')->values()->map(function ($r) {
                $bulan = $r->bulan;
                $nama_bulan = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                ];
                $parts = explode('-', $bulan);
                $label = ($nama_bulan[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
                return [
                    'bulan' => $label,
                    'rajal' => (float) ($r->total_rajal ?? 0),
                    'ranap' => (float) ($r->total_ranap ?? 0),
                    'total' => (float) ($r->total_rajal ?? 0) + (float) ($r->total_ranap ?? 0),
                ];
            });

            $total_all = [
                'rajal' => $list->sum('rajal'),
                'ranap' => $list->sum('ranap'),
                'total' => $list->sum('total'),
                'rata_rata' => $list->count() > 0 ? $list->avg('total') : 0,
            ];
        }

        return response()->json([
            'list' => $list,
            'total' => $total_all,
        ]);
    }
}
