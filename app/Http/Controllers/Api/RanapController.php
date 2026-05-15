<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RanapController extends Controller
{
    public function list(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;
        $status = $request->status;

        $query = DB::table('kamar_inap')
            ->leftJoin('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where(function ($qq) use ($tgl1, $tgl2) {
                $qq->where('kamar_inap.stts_pulang', '-')
                    ->orWhereBetween('kamar_inap.tgl_masuk', [$tgl1, $tgl2]);
            });

        if ($status === 'belum') {
            $query->where('kamar_inap.stts_pulang', '-');
        } elseif ($status === 'sudah') {
            $query->where('kamar_inap.stts_pulang', '!=', '-')
                ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar');
        } elseif ($status === 'pindah') {
            $query->where('kamar_inap.stts_pulang', 'Pindah Kamar');
        }

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('kamar_inap.no_rawat', 'LIKE', "%$q%")
                    ->orWhere('bangsal.nm_bangsal', 'LIKE', "%$q%");
            });
        }

        $list = $query->select(
            'kamar_inap.no_rawat',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'kamar_inap.tgl_masuk',
            'kamar_inap.jam_masuk',
            'kamar_inap.tgl_keluar',
            'kamar_inap.jam_keluar',
            'kamar_inap.lama',
            'kamar_inap.trf_kamar',
            'kamar_inap.ttl_biaya',
            'kamar_inap.stts_pulang',
            'kamar_inap.diagnosa_awal',
            'kamar_inap.diagnosa_akhir',
            'bangsal.nm_bangsal',
            'kamar.kd_kamar',
            'kamar.kelas',
            'dokter.nm_dokter as nm_dokter',
            'reg_periksa.kd_poli'
        )
        ->orderBy('kamar_inap.tgl_masuk', 'desc')
        ->orderBy('kamar_inap.jam_masuk', 'desc')
        ->get()
        ->map(function ($r) {
            return [
                'no_rawat' => $r->no_rawat,
                'no_rkm_medis' => $r->no_rkm_medis ?? '-',
                'nm_pasien' => $r->nm_pasien ?? '-',
                'tgl_masuk' => $r->tgl_masuk,
                'jam_masuk' => $r->jam_masuk,
                'tgl_keluar' => $r->tgl_keluar,
                'jam_keluar' => $r->jam_keluar,
                'lama' => (int) ($r->lama ?? 0),
                'trf_kamar' => (float) ($r->trf_kamar ?? 0),
                'ttl_biaya' => (float) ($r->ttl_biaya ?? 0),
                'stts_pulang' => $r->stts_pulang ?? '-',
                'diagnosa_awal' => $r->diagnosa_awal ?? '',
                'diagnosa_akhir' => $r->diagnosa_akhir ?? '',
                'nm_bangsal' => $r->nm_bangsal ?? '-',
                'kd_kamar' => $r->kd_kamar ?? '-',
                'kelas' => $r->kelas ?? '-',
                'nm_dokter' => $r->nm_dokter ?? '-',
            ];
        });

        $counts = [
            'total' => $list->count(),
            'belum' => $list->where('stts_pulang', '-')->count(),
            'sudah' => $list->where('stts_pulang', '!=', '-')->where('stts_pulang', '!=', 'Pindah Kamar')->count(),
            'pindah' => $list->where('stts_pulang', 'Pindah Kamar')->count(),
        ];

        return response()->json([
            'list' => $list,
            'counts' => $counts,
        ]);
    }

    public function riwayatKamar(Request $request)
    {
        try {
            $no_rawat = $request->query('no_rawat', '');
            if (!$no_rawat) return response()->json(['list' => []]);

            $rows = DB::table('kamar_inap')
                ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->where('kamar_inap.no_rawat', $no_rawat)
                ->orderBy('kamar_inap.tgl_masuk', 'asc')
                ->orderBy('kamar_inap.jam_masuk', 'asc')
                ->select(
                    'kamar_inap.tgl_masuk',
                    'kamar_inap.jam_masuk',
                    'kamar_inap.tgl_keluar',
                    'kamar_inap.jam_keluar',
                    'kamar_inap.stts_pulang',
                    'kamar.kd_kamar',
                    'kamar.kelas',
                    'bangsal.nm_bangsal'
                )
                ->get()
                ->map(function ($r) {
                    return [
                        'nm_bangsal' => $r->nm_bangsal ?? '-',
                        'kd_kamar' => $r->kd_kamar ?? '-',
                        'kelas' => $r->kelas ?? '-',
                        'tgl_masuk' => $r->tgl_masuk,
                        'jam_masuk' => $r->jam_masuk,
                        'tgl_keluar' => $r->tgl_keluar,
                        'jam_keluar' => $r->jam_keluar,
                        'stts_pulang' => $r->stts_pulang ?? '-',
                    ];
                });

            $total = $rows->count();
            $result = $rows->values()->toArray();
            if ($total > 0) {
                $result[$total - 1]['keterangan'] = 'Kamar Saat Ini';
            }
            for ($i = 0; $i < $total - 1; $i++) {
                $result[$i]['keterangan'] = 'Pindah';
            }

            return response()->json(['list' => $result]);
        } catch (\Exception $e) {
            return response()->json(['list' => []]);
        }
    }

    public function kamarList()
    {
        $rows = DB::table('kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar.statusdata', '1')
            ->orderBy('bangsal.nm_bangsal')
            ->orderBy('kamar.kd_kamar')
            ->select(
                'kamar.kd_kamar',
                'bangsal.nm_bangsal',
                'kamar.kelas',
                'kamar.trf_kamar',
                'kamar.status'
            )
            ->get()
            ->map(function ($r) {
                return [
                    'kd_kamar' => $r->kd_kamar,
                    'nm_bangsal' => $r->nm_bangsal ?? '-',
                    'kelas' => $r->kelas ?? '-',
                    'trf_kamar' => (float) ($r->trf_kamar ?? 0),
                    'status' => $r->status ?? 'KOSONG',
                ];
            });
        return response()->json($rows);
    }

    public function admit(Request $request)
    {
        $data = $request->validate([
            'no_rawat' => 'required|string|max:17',
            'no_rkm_medis' => 'nullable|string|max:15',
            'kd_kamar' => 'required|string|max:15',
            'trf_kamar' => 'nullable|numeric',
            'kd_dokter' => 'nullable|string|max:20',
            'diagnosa_awal' => 'nullable|string|max:100',
            'tgl_masuk' => 'nullable|date',
            'jam_masuk' => 'nullable|string|max:8',
        ]);

        $exists = DB::table('kamar_inap')->where('no_rawat', $data['no_rawat'])->exists();
        if ($exists) {
            return response()->json(['message' => 'Pasien sudah terdaftar di kamar inap'], 409);
        }

        $tgl = $data['tgl_masuk'] ?? date('Y-m-d');
        $jam = $data['jam_masuk'] ?? date('H:i:s');

        DB::table('kamar_inap')->insert([
            'no_rawat' => $data['no_rawat'],
            'kd_kamar' => $data['kd_kamar'],
            'trf_kamar' => $data['trf_kamar'] ?? 0,
            'diagnosa_awal' => $data['diagnosa_awal'] ?? '',
            'tgl_masuk' => $tgl,
            'jam_masuk' => $jam,
            'stts_pulang' => '-',
            'lama' => 0,
            'ttl_biaya' => $data['trf_kamar'] ?? 0,
        ]);

        if (!empty($data['kd_dokter'])) {
            $dpjpExists = DB::table('dpjp_ranap')
                ->where('no_rawat', $data['no_rawat'])
                ->where('kd_dokter', $data['kd_dokter'])
                ->exists();
            if (!$dpjpExists) {
                DB::table('dpjp_ranap')->insert([
                    'no_rawat' => $data['no_rawat'],
                    'kd_dokter' => $data['kd_dokter'],
                ]);
            }
        }

        DB::table('reg_periksa')
            ->where('no_rawat', $data['no_rawat'])
            ->update(['status_lanjut' => 'Ranap', 'stts' => 'Dirawat']);

        DB::table('kamar')
            ->where('kd_kamar', $data['kd_kamar'])
            ->update(['status' => 'ISI']);

        return response()->json(['message' => 'Pasien berhasil masuk kamar inap'], 201);
    }

    public function pindahKamar(Request $request)
    {
        $data = $request->validate([
            'no_rawat' => 'required|string|max:17',
            'kd_kamar' => 'required|string|max:15',
            'trf_kamar' => 'nullable|numeric',
            'tgl_keluar' => 'nullable|date',
            'jam_keluar' => 'nullable|string|max:8',
        ]);

        $kamar = DB::table('kamar')->where('kd_kamar', $data['kd_kamar'])->first();
        if (!$kamar) return response()->json(['message' => 'Kamar tidak ditemukan'], 404);

        $old = DB::table('kamar_inap')
            ->where('no_rawat', $data['no_rawat'])
            ->where('stts_pulang', '-')
            ->first();
        if (!$old) return response()->json(['message' => 'Tidak ada kamar aktif untuk pasien ini'], 404);

        $tgl_keluar = $data['tgl_keluar'] ?? date('Y-m-d');
        $jam_keluar = $data['jam_keluar'] ?? date('H:i:s');
        $lama = \Carbon\Carbon::parse($old->tgl_masuk)->diffInDays($tgl_keluar) + 1;
        $ttl_biaya = $lama * ($old->trf_kamar ?? 0);

        DB::table('kamar_inap')
            ->where('no_rawat', $data['no_rawat'])
            ->where('tgl_masuk', $old->tgl_masuk)
            ->where('jam_masuk', $old->jam_masuk)
            ->update([
                'tgl_keluar' => $tgl_keluar,
                'jam_keluar' => $jam_keluar,
                'lama' => $lama,
                'ttl_biaya' => $ttl_biaya,
                'stts_pulang' => 'Pindah Kamar',
            ]);

        DB::table('kamar_inap')->insert([
            'no_rawat' => $data['no_rawat'],
            'kd_kamar' => $data['kd_kamar'],
            'trf_kamar' => $data['trf_kamar'] ?? $kamar->trf_kamar ?? 0,
            'tgl_masuk' => $tgl_keluar,
            'jam_masuk' => $jam_keluar,
            'stts_pulang' => '-',
            'lama' => 0,
            'ttl_biaya' => 0,
        ]);

        DB::table('kamar')->where('kd_kamar', $old->kd_kamar)->update(['status' => 'KOSONG']);
        DB::table('kamar')->where('kd_kamar', $data['kd_kamar'])->update(['status' => 'ISI']);

        DB::table('reg_periksa')
            ->where('no_rawat', $data['no_rawat'])
            ->update(['stts' => 'Dirawat', 'status_lanjut' => 'Ranap']);

        return response()->json(['message' => 'Kamar berhasil dipindahkan']);
    }

    public function pulangkan(Request $request)
    {
        $data = $request->validate([
            'no_rawat' => 'required|string|max:17',
            'tgl_keluar' => 'nullable|date',
            'jam_keluar' => 'nullable|string|max:8',
            'stts_pulang' => 'nullable|string|max:30',
        ]);

        $kamar_inap = DB::table('kamar_inap')
            ->where('no_rawat', $data['no_rawat'])
            ->where('stts_pulang', '-')
            ->first();
        if (!$kamar_inap) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $tgl = $data['tgl_keluar'] ?? date('Y-m-d');
        $jam = $data['jam_keluar'] ?? date('H:i:s');
        $stts = $data['stts_pulang'] ?? 'Sehat';
        $lama = \Carbon\Carbon::parse($kamar_inap->tgl_masuk)->diffInDays($tgl) + 1;
        $ttl_biaya = $lama * ($kamar_inap->trf_kamar ?? 0);

        DB::table('kamar_inap')
            ->where('no_rawat', $data['no_rawat'])
            ->where('tgl_masuk', $kamar_inap->tgl_masuk)
            ->where('jam_masuk', $kamar_inap->jam_masuk)
            ->update([
                'tgl_keluar' => $tgl,
                'jam_keluar' => $jam,
                'lama' => $lama,
                'ttl_biaya' => $ttl_biaya,
                'stts_pulang' => $stts,
            ]);

        DB::table('kamar')->where('kd_kamar', $kamar_inap->kd_kamar)->update(['status' => 'KOSONG']);

        DB::table('reg_periksa')
            ->where('no_rawat', $data['no_rawat'])
            ->update(['stts' => $stts]);

        return response()->json(['message' => 'Pasien berhasil dipulangkan']);
    }

    public function destroy($no_rawat)
    {
        $kamar_inap = DB::table('kamar_inap')
            ->where('no_rawat', $no_rawat)
            ->where('stts_pulang', '-')
            ->first();
        if (!$kamar_inap) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        DB::table('kamar')->where('kd_kamar', $kamar_inap->kd_kamar)->update(['status' => 'KOSONG']);
        DB::table('kamar_inap')
            ->where('no_rawat', $no_rawat)
            ->where('tgl_masuk', $kamar_inap->tgl_masuk)
            ->where('jam_masuk', $kamar_inap->jam_masuk)
            ->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function ubahWaktuMasuk(Request $request)
    {
        $data = $request->validate([
            'no_rawat' => 'required|string|max:17',
            'tgl_masuk' => 'required|date',
            'jam_masuk' => 'required|string|max:8',
        ]);

        $kamar_inap = DB::table('kamar_inap')->where('no_rawat', $data['no_rawat'])->first();
        if (!$kamar_inap) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        DB::table('kamar_inap')
            ->where('no_rawat', $data['no_rawat'])
            ->update(['tgl_masuk' => $data['tgl_masuk'], 'jam_masuk' => $data['jam_masuk']]);

        return response()->json(['message' => 'Waktu masuk berhasil diubah']);
    }

    public function ubahWaktuKeluar(Request $request)
    {
        $data = $request->validate([
            'no_rawat' => 'required|string|max:17',
            'tgl_keluar' => 'required|date',
            'jam_keluar' => 'required|string|max:8',
        ]);

        $kamar_inap = DB::table('kamar_inap')->where('no_rawat', $data['no_rawat'])->first();
        if (!$kamar_inap) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $lama = \Carbon\Carbon::parse($kamar_inap->tgl_masuk)->diffInDays($data['tgl_keluar']) + 1;
        $ttl_biaya = $lama * ($kamar_inap->trf_kamar ?? 0);

        DB::table('kamar_inap')
            ->where('no_rawat', $data['no_rawat'])
            ->update([
                'tgl_keluar' => $data['tgl_keluar'],
                'jam_keluar' => $data['jam_keluar'],
                'lama' => $lama,
                'ttl_biaya' => $ttl_biaya,
            ]);

        return response()->json(['message' => 'Waktu keluar berhasil diubah']);
    }

    public function dpjpList($no_rawat)
    {
        $list = DB::table('dpjp_ranap')
            ->where('no_rawat', $no_rawat)
            ->leftJoin('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
            ->select('dpjp_ranap.kd_dokter', 'dokter.nm_dokter')
            ->get();
        return response()->json($list);
    }

    public function dpjpDoctors()
    {
        $doctors = DB::table('dokter')
            ->select('kd_dokter', 'nm_dokter')
            ->orderBy('nm_dokter')
            ->get();
        return response()->json($doctors);
    }

    public function dpjpAdd(Request $request)
    {
        $data = $request->validate([
            'no_rawat' => 'required|string|max:17',
            'kd_dokter' => 'required|string|max:20',
        ]);

        $exists = DB::table('dpjp_ranap')
            ->where('no_rawat', $data['no_rawat'])
            ->where('kd_dokter', $data['kd_dokter'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'DPJP sudah terdaftar'], 409);
        }

        DB::table('dpjp_ranap')->insert([
            'no_rawat' => $data['no_rawat'],
            'kd_dokter' => $data['kd_dokter'],
        ]);

        return response()->json(['message' => 'DPJP berhasil ditambahkan'], 201);
    }

    public function dpjpDelete($no_rawat, $kd_dokter)
    {
        DB::table('dpjp_ranap')
            ->where('no_rawat', $no_rawat)
            ->where('kd_dokter', $kd_dokter)
            ->delete();

        return response()->json(['message' => 'DPJP berhasil dihapus']);
    }

    public function dashboard()
    {
        $today = date('Y-m-d');

        $total = DB::table('kamar_inap')->count();
        $rawat_inap = DB::table('kamar_inap')->where('stts_pulang', '-')->count();
        $hari_ini_masuk = DB::table('kamar_inap')->where('tgl_masuk', $today)->count();
        $hari_ini_keluar = DB::table('kamar_inap')->where('tgl_keluar', $today)->count();

        return response()->json([
            'total' => $total,
            'rawat_inap' => $rawat_inap,
            'hari_ini_masuk' => $hari_ini_masuk,
            'hari_ini_keluar' => $hari_ini_keluar,
        ]);
    }
}
