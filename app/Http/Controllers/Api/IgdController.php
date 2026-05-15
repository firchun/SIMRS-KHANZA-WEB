<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Registrasi, IgdTriage, IgdTindakan, IgdDiagnosis};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IgdController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tgl1' => 'nullable|date',
            'tgl2' => 'nullable|date',
            'q' => 'nullable|string|max:100',
        ]);

        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;

        $query = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.kd_poli', 'IGDK')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('reg_periksa.no_reg', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.no_rawat', 'LIKE', "%$q%")
                    ->orWhere('dokter.nm_dokter', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.kd_dokter', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.p_jawab', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.almt_pj', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.hubunganpj', 'LIKE', "%$q%")
                    ->orWhere('penjab.png_jawab', 'LIKE', "%$q%")
                    ->orWhere('poliklinik.nm_poli', 'LIKE', "%$q%")
                    ->orWhere('reg_periksa.stts_daftar', 'LIKE', "%$q%");
            });
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
            'reg_periksa.p_jawab',
            'reg_periksa.almt_pj',
            'reg_periksa.hubunganpj',
            'reg_periksa.biaya_reg',
            'reg_periksa.stts_daftar',
            'penjab.png_jawab as jenis_bayar',
            'reg_periksa.stts as stts_rawat',
            'reg_periksa.kd_pj',
            'reg_periksa.status_bayar'
        )
        ->orderBy('reg_periksa.no_rawat')
        ->get();

        $dirawat = DB::table('kamar_inap')
            ->whereIn('no_rawat', $list->pluck('no_rawat'))
            ->where('stts_pulang', '-')
            ->pluck('no_rawat')
            ->unique();

        $list = $list->map(fn($item) => tap($item, fn($i) => $i->dirawat = $dirawat->contains($i->no_rawat)));

        $total_hari_ini = DB::table('reg_periksa')
            ->where('kd_poli', 'IGDK')
            ->where('tgl_registrasi', date('Y-m-d'))
            ->count();

        $counts = DB::table('reg_periksa')
            ->where('kd_poli', 'IGDK')
            ->where('tgl_registrasi', date('Y-m-d'))
            ->selectRaw("SUM(CASE WHEN stts='Belum' THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN stts='Sudah' THEN 1 ELSE 0 END) as diperiksa")
            ->selectRaw("SUM(CASE WHEN stts IN ('Dirujuk','Dirawat','Meninggal','Pulang Paksa') THEN 1 ELSE 0 END) as selesai")
            ->first();

        return response()->json([
            'list' => $list,
            'total' => $list->count(),
            'total_hari_ini' => $total_hari_ini,
            'counts' => $counts,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'no_rkm_medis' => 'required|string|max:15',
            'kd_dokter' => 'required|string|max:20',
            'kd_pj' => 'required|string|max:3',
            'p_jawab' => 'nullable|string|max:30',
            'almt_pj' => 'nullable|string|max:200',
            'hubunganpj' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string|max:255',
            'tgl_registrasi' => 'nullable|date',
            'jam_reg' => 'nullable|string|max:8',
        ]);

        $pasien = DB::table('pasien')->where('no_rkm_medis', $request->no_rkm_medis)->first();
        if (!$pasien) {
            throw ValidationException::withMessages(['no_rkm_medis' => ['Pasien tidak ditemukan']]);
        }

        $cekIGD = DB::table('reg_periksa')
            ->where('no_rkm_medis', $request->no_rkm_medis)
            ->where('kd_poli', 'IGDK')
            ->count();

        $stts_daftar = $cekIGD > 0 ? 'Lama' : 'Baru';

        $fee = DB::table('poliklinik')
            ->where('kd_poli', 'IGDK')
            ->selectRaw("IF('$stts_daftar'='Baru', registrasi, registrasilama) as biaya")
            ->first();
        $biaya_reg = $fee->biaya ?? 25000;

        $tgl = $request->tgl_registrasi ?? date('Y-m-d');
        $jam = $request->jam_reg ?? date('H:i:s');
        $lastRawat = DB::table('reg_periksa')
            ->where('tgl_registrasi', $tgl)
            ->selectRaw("IFNULL(MAX(CONVERT(RIGHT(no_rawat,6),SIGNED)),0) + 1 as next")
            ->first();
        $seq = str_pad($lastRawat->next ?? 1, 6, '0', STR_PAD_LEFT);
        $no_rawat = "$tgl/$seq";

        $lastReg = DB::table('reg_periksa')
            ->where('kd_poli', 'IGDK')
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
            'kd_poli' => 'IGDK',
            'p_jawab' => $request->p_jawab ?? $pasien->namakeluarga ?? '-',
            'almt_pj' => $request->almt_pj ?? $pasien->alamatpj ?? '-',
            'hubunganpj' => $request->hubunganpj ?? $pasien->keluarga ?? '-',
            'biaya_reg' => $biaya_reg,
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
            'message' => 'Registrasi IGD berhasil',
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

    public function triage(Request $request, Registrasi $registrasi)
    {
        $data = $request->validate([
            'triase' => 'required|string|max:20',
            'tekanan_darah' => 'nullable|string|max:20',
            'nadi' => 'nullable|string|max:20',
            'suhu' => 'nullable|string|max:10',
            'pernapasan' => 'nullable|string|max:20',
            'spo2' => 'nullable|string|max:10',
            'kesadaran' => 'nullable|string|max:30',
            'nyeri' => 'nullable|string|max:10',
            'anamnesis' => 'nullable|string',
        ]);
        $data['registrasi_id'] = $registrasi->id;
        $data['dokter_id'] = $request->user()->id;
        $data['tgl_triase'] = now();
        $triage = IgdTriage::create($data);
        $registrasi->update(['status' => 'diperiksa']);
        return response()->json($triage, 201);
    }

    public function tindakan(Request $request, Registrasi $registrasi)
    {
        $data = $request->validate([
            'tindakan' => 'required|string|max:200',
            'jumlah' => 'integer|min:1',
            'tarif' => 'numeric|min:0',
        ]);
        $data['registrasi_id'] = $registrasi->id;
        $data['user_id'] = $request->user()->id;
        $data['tgl_tindakan'] = now();
        return response()->json(IgdTindakan::create($data), 201);
    }

    public function diagnosis(Request $request, Registrasi $registrasi)
    {
        $data = $request->validate([
            'kode_icd' => 'nullable|string|max:20',
            'diagnosis' => 'required|string|max:255',
            'jenis' => 'required|in:utama,sekunder,komplikasi',
        ]);
        $data['registrasi_id'] = $registrasi->id;
        $data['user_id'] = $request->user()->id;
        return response()->json(IgdDiagnosis::create($data), 201);
    }

    public function dashboard()
    {
        $today = date('Y-m-d');
        $list = DB::table('reg_periksa')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.kd_poli', 'IGDK')
            ->where('reg_periksa.tgl_registrasi', $today)
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_reg',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'dokter.nm_dokter',
                'reg_periksa.stts',
            )
            ->orderBy('reg_periksa.jam_reg')
            ->get();

        $counts = DB::table('reg_periksa')
            ->where('kd_poli', 'IGDK')
            ->where('tgl_registrasi', $today)
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN stts='Belum' THEN 1 ELSE 0 END) as menunggu")
            ->selectRaw("SUM(CASE WHEN stts='Sudah' THEN 1 ELSE 0 END) as diperiksa")
            ->selectRaw("SUM(CASE WHEN stts IN ('Dirujuk','Dirawat','Meninggal','Pulang Paksa') THEN 1 ELSE 0 END) as selesai")
            ->first();

        return response()->json([
            'total_hari_ini' => (int) ($counts->total ?? 0),
            'menunggu' => (int) ($counts->menunggu ?? 0),
            'diperiksa' => (int) ($counts->diperiksa ?? 0),
            'selesai' => (int) ($counts->selesai ?? 0),
            'list' => $list,
        ]);
    }
}
