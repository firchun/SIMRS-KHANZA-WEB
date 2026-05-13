<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RalanController extends Controller
{
    public function list(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;
        $poli = $request->poli;
        $dokter = $request->dokter;
        $status = $request->status;

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.kd_poli', '!=', 'IGDK')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('reg_periksa.no_reg', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.no_rawat', 'LIKE', "%$q%")
                    ->orWhere('dokter.nm_dokter', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.p_jawab', 'LIKE', "%$q%")
                    ->orWhere('penjab.png_jawab', 'LIKE', "%$q%")
                    ->orWhere('poliklinik.nm_poli', 'LIKE', "%$q%");
            });
        }

        if ($poli) {
            $query->where('reg_periksa.kd_poli', $poli);
        }

        if ($dokter) {
            $query->where('reg_periksa.kd_dokter', $dokter);
        }

        if ($status) {
            $query->where('reg_periksa.stts', $status);
        }

        $list = $query->select(
            'reg_periksa.no_reg',
            'reg_periksa.no_rawat',
            'reg_periksa.tgl_registrasi',
            'reg_periksa.jam_reg',
            'reg_periksa.kd_dokter',
            'dokter.nm_dokter as nama_dokter',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'pasien.jk',
            DB::raw("CONCAT(reg_periksa.umurdaftar, ' ', reg_periksa.sttsumur) as umur"),
            'poliklinik.nm_poli',
            'poliklinik.kd_poli as kd_poli',
            'reg_periksa.p_jawab',
            'reg_periksa.almt_pj',
            'reg_periksa.hubunganpj',
            'reg_periksa.biaya_reg',
            'reg_periksa.stts',
            'reg_periksa.stts_daftar',
            'penjab.png_jawab as jenis_bayar',
            'reg_periksa.kd_pj',
            'reg_periksa.status_bayar',
            'reg_periksa.status_lanjut'
        )
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->orderBy('reg_periksa.jam_reg', 'desc')
        ->get();

        $counts = [
            'menunggu' => $list->where('stts', 'Belum')->count(),
            'diperiksa' => $list->where('stts', 'Sudah')->count(),
            'selesai' => $list->whereIn('stts', ['Sudah', 'Selesai'])->count(),
        ];

        return response()->json([
            'list' => $list,
            'total' => $list->count(),
            'counts' => $counts,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'no_rkm_medis' => 'required|string|max:15',
            'kd_poli' => 'required|string|max:5',
            'kd_dokter' => 'required|string|max:20',
            'kd_pj' => 'required|string|max:3',
            'p_jawab' => 'nullable|string|max:30',
            'almt_pj' => 'nullable|string|max:200',
            'hubunganpj' => 'nullable|string|max:20',
            'tgl_registrasi' => 'nullable|date',
            'jam_reg' => 'nullable|string|max:8',
        ]);

        $pasien = DB::table('pasien')->where('no_rkm_medis', $request->no_rkm_medis)->first();
        if (!$pasien) {
            throw ValidationException::withMessages(['no_rkm_medis' => ['Pasien tidak ditemukan']]);
        }

        $poli = DB::table('poliklinik')->where('kd_poli', $request->kd_poli)->first();
        if (!$poli) {
            throw ValidationException::withMessages(['kd_poli' => ['Poli tidak ditemukan']]);
        }

        $cek = DB::table('reg_periksa')
            ->where('no_rkm_medis', $request->no_rkm_medis)
            ->where('kd_poli', $request->kd_poli)
            ->where('tgl_registrasi', $request->tgl_registrasi ?? date('Y-m-d'))
            ->count();

        $stts_daftar = $cek > 0 ? 'Lama' : 'Baru';

        $biaya_reg = $stts_daftar === 'Baru' ? $poli->registrasi : $poli->registrasilama;

        $tgl = $request->tgl_registrasi ?? date('Y-m-d');
        $jam = $request->jam_reg ?? date('H:i:s');

        $lastRawat = DB::table('reg_periksa')
            ->where('tgl_registrasi', $tgl)
            ->selectRaw("IFNULL(MAX(CONVERT(RIGHT(no_rawat,6),SIGNED)),0) + 1 as next")
            ->first();
        $seq = str_pad($lastRawat->next ?? 1, 6, '0', STR_PAD_LEFT);
        $no_rawat = "$tgl/$seq";

        $lastReg = DB::table('reg_periksa')
            ->where('kd_poli', $request->kd_poli)
            ->where('tgl_registrasi', $tgl)
            ->where('kd_dokter', $request->kd_dokter)
            ->selectRaw("IFNULL(MAX(CONVERT(no_reg,SIGNED)),0) + 1 as next")
            ->first();
        $no_reg = str_pad($lastReg->next ?? 1, 4, '0', STR_PAD_LEFT);

        $umur = DB::selectOne("SELECT TIMESTAMPDIFF(YEAR, ?, CURDATE()) as tahun, TIMESTAMPDIFF(MONTH, ?, CURDATE()) - TIMESTAMPDIFF(YEAR, ?, CURDATE())*12 as bulan", [$pasien->tgl_lahir, $pasien->tgl_lahir, $pasien->tgl_lahir]);

        DB::table('reg_periksa')->insert([
            'no_reg' => $no_reg,
            'no_rawat' => $no_rawat,
            'tgl_registrasi' => $tgl,
            'jam_reg' => $jam,
            'kd_dokter' => $request->kd_dokter,
            'no_rkm_medis' => $request->no_rkm_medis,
            'kd_poli' => $request->kd_poli,
            'p_jawab' => $request->p_jawab ?? $pasien->namakeluarga ?? '-',
            'almt_pj' => $request->almt_pj ?? $pasien->alamatpj ?? '-',
            'hubunganpj' => $request->hubunganpj ?? $pasien->keluarga ?? '-',
            'biaya_reg' => $biaya_reg ?? 0,
            'stts' => 'Belum',
            'stts_daftar' => $stts_daftar,
            'status_lanjut' => 'Ralan',
            'kd_pj' => $request->kd_pj,
            'umurdaftar' => $umur->tahun ?? 0,
            'sttsumur' => 'Th',
            'status_bayar' => 'Belum Bayar',
            'status_poli' => $stts_daftar,
        ]);

        return response()->json([
            'message' => 'Registrasi Rawat Jalan berhasil',
            'no_rawat' => $no_rawat,
            'no_reg' => $no_reg,
        ], 201);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required|string',
            'stts' => 'required|string|in:Belum,Sudah,Batal,Dirujuk,Dirawat,Meninggal,Pulang Paksa',
        ]);

        $reg = DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->first();
        if (!$reg) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $kamar_inap = DB::table('kamar_inap')->where('no_rawat', $request->no_rawat)->where('stts_pulang', '-')->count();
        if ($kamar_inap > 0) {
            return response()->json(['message' => 'Pasien sudah masuk Kamar Inap. Gunakan billing Ranap.'], 422);
        }

        $upd = ['stts' => $request->stts];
        if ($request->stts === 'Batal') {
            $upd['biaya_reg'] = 0;
        }

        DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->update($upd);

        return response()->json(['message' => 'Status berhasil diupdate', 'stts' => $request->stts]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required|string',
            'kd_poli' => 'required|string|max:5',
            'kd_dokter' => 'required|string|max:20',
            'kd_pj' => 'required|string|max:3',
            'p_jawab' => 'nullable|string|max:30',
            'almt_pj' => 'nullable|string|max:200',
            'hubunganpj' => 'nullable|string|max:20',
            'tgl_registrasi' => 'nullable|date',
            'jam_reg' => 'nullable|string|max:8',
        ]);

        $reg = DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->first();
        if (!$reg) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $upd = [
            'kd_poli' => $request->kd_poli,
            'kd_dokter' => $request->kd_dokter,
            'kd_pj' => $request->kd_pj,
            'p_jawab' => $request->p_jawab ?? $reg->p_jawab,
            'almt_pj' => $request->almt_pj ?? $reg->almt_pj,
            'hubunganpj' => $request->hubunganpj ?? $reg->hubunganpj,
        ];
        if ($request->has('tgl_registrasi')) $upd['tgl_registrasi'] = $request->tgl_registrasi;
        if ($request->has('jam_reg')) $upd['jam_reg'] = $request->jam_reg;

        DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->update($upd);

        return response()->json(['message' => 'Data berhasil diupdate']);
    }

    public function destroy(Request $request)
    {
        $request->validate(['no_rawat' => 'required|string']);
        DB::table('reg_periksa')->where('no_rawat', $request->no_rawat)->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function poliList()
    {
        $rows = DB::table('poliklinik')
            ->where('kd_poli', '!=', 'IGDK')
            ->orderBy('nm_poli')
            ->get()
            ->map(function ($r) {
                return [
                    'kd_poli' => $r->kd_poli,
                    'nm_poli' => $r->nm_poli,
                ];
            });
        return response()->json($rows);
    }

    public function dokterList()
    {
        $rows = DB::table('dokter')
            ->where('status', '1')
            ->orderBy('nm_dokter')
            ->get(['kd_dokter', 'nm_dokter']);
        return response()->json($rows);
    }

    public function dashboard()
    {
        $today = date('Y-m-d');

        $total_hari_ini = DB::table('reg_periksa')
            ->where('kd_poli', '!=', 'IGDK')
            ->where('tgl_registrasi', $today)
            ->count();

        $counts = DB::table('reg_periksa')
            ->where('kd_poli', '!=', 'IGDK')
            ->where('tgl_registrasi', $today)
            ->selectRaw("SUM(CASE WHEN stts='Belum' THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN stts='Sudah' THEN 1 ELSE 0 END) as diperiksa")
            ->selectRaw("SUM(CASE WHEN stts IN ('Dirujuk','Dirawat','Meninggal','Pulang Paksa') THEN 1 ELSE 0 END) as selesai")
            ->first();

        return response()->json([
            'total_hari_ini' => $total_hari_ini,
            'menunggu' => $counts->menunggu ?? 0,
            'diperiksa' => $counts->diperiksa ?? 0,
            'selesai' => $counts->selesai ?? 0,
        ]);
    }

    public function queue(Request $request)
    {
        return $this->list($request);
    }

    public function startExamination(Request $request, $no_rawat)
    {
        $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
        if (!$reg) {
            return response()->json(['message' => 'Registrasi tidak ditemukan'], 404);
        }

        DB::table('reg_periksa')->where('no_rawat', $no_rawat)->update(['stts' => 'Sudah']);

        return response()->json(['message' => 'Pemeriksaan dimulai', 'no_rawat' => $no_rawat]);
    }

    public function updateExamination(Request $request, $no_rawat)
    {
        return response()->json(['message' => 'Endpoint belum diimplementasikan']);
    }

    public function addResep(Request $request, $no_rawat)
    {
        return response()->json(['message' => 'Endpoint belum diimplementasikan']);
    }
}
