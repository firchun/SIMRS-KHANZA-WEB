<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistrasiController extends Controller
{
    public function todayList()
    {
        $today = now()->toDateString();
        $rows = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereDate('reg_periksa.tgl_registrasi', $today)
            ->where('reg_periksa.kd_poli', '!=', 'IGDK')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_reg',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.stts',
                'reg_periksa.status_bayar',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.jk',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
            )
            ->orderBy('reg_periksa.jam_reg')
            ->get()
            ->map(function ($r) {
                $statusMap = [
                    'Belum' => 'Menunggu',
                    'Sudah' => 'Selesai',
                    'Batal' => 'Batal',
                    'Berkas Diterima' => 'Diproses',
                    'Dirujuk' => 'Dirujuk',
                    'Meninggal' => 'Meninggal',
                    'Dirawat' => 'Dirawat',
                    'Pulang Paksa' => 'Pulang Paksa',
                ];
                return [
                    'no_rawat' => $r->no_rawat,
                    'no_reg' => $r->no_reg,
                    'no_rm' => $r->no_rkm_medis,
                    'nm_pasien' => $r->nm_pasien,
                    'jk' => $r->jk,
                    'nm_poli' => $r->nm_poli ?: $r->kd_poli,
                    'nm_dokter' => $r->nm_dokter ?: '-',
                    'jam_reg' => substr($r->jam_reg, 0, 5),
                    'status' => $statusMap[$r->stts] ?? $r->stts,
                    'stts' => $r->stts,
                    'status_bayar' => $r->status_bayar,
                ];
            });
        return response()->json($rows);
    }

    public function dokterByPoli()
    {
        $rows = DB::table('jadwal')
            ->join('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
            ->select('jadwal.kd_dokter', 'dokter.nm_dokter', 'jadwal.kd_poli')
            ->distinct()
            ->orderBy('dokter.nm_dokter')
            ->get()
            ->map(function ($r) {
                return [
                    'kd_dokter' => $r->kd_dokter,
                    'nm_dokter' => $r->nm_dokter,
                    'kd_poli' => $r->kd_poli,
                ];
            });
        return response()->json($rows);
    }

    public function petugasList()
    {
        $rows = DB::table('petugas')
            ->where('status', '1')
            ->orderBy('nama')
            ->get()
            ->map(function ($r) {
                return [
                    'nip' => $r->nip,
                    'nama' => $r->nama,
                    'jk' => $r->jk,
                ];
            });
        return response()->json($rows);
    }

    public function storeRegPeriksa(Request $request)
    {
        $data = $request->validate([
            'no_rkm_medis' => 'required|string|max:15',
            'kd_poli' => 'required|string|max:5',
            'kd_dokter' => 'required|string|max:20',
            'kd_pj' => 'nullable|string|max:3',
            'p_jawab' => 'nullable|string|max:100',
            'almt_pj' => 'nullable|string|max:200',
            'hubunganpj' => 'nullable|string|max:20',
            'biaya_reg' => 'nullable|numeric',
        ]);

        $max = DB::table('reg_periksa')
            ->where('no_reg', 'regexp', '^[0-9]+$')
            ->select(DB::raw('MAX(CAST(no_reg AS UNSIGNED)) as max_id'))
            ->value('max_id');
        $no_reg = str_pad(($max ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        $now = now();
        $no_rawat = $now->format('Y-m-d') . '/' . str_pad(($max ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        $insert = [
            'no_reg' => $no_reg,
            'no_rawat' => $no_rawat,
            'tgl_registrasi' => $now->toDateString(),
            'jam_reg' => $now->format('H:i:s'),
            'kd_dokter' => $data['kd_dokter'],
            'no_rkm_medis' => $data['no_rkm_medis'],
            'kd_poli' => $data['kd_poli'],
            'p_jawab' => $data['p_jawab'] ?? '-',
            'almt_pj' => $data['almt_pj'] ?? '-',
            'hubunganpj' => $data['hubunganpj'] ?? '-',
            'biaya_reg' => $data['biaya_reg'] ?? 0,
            'stts' => 'Belum',
            'stts_daftar' => 'Baru',
            'status_lanjut' => 'Ralan',
            'kd_pj' => $data['kd_pj'] ?? '-',
            'umurdaftar' => 0,
            'sttsumur' => 'Th',
            'status_bayar' => 'Belum Bayar',
            'status_poli' => 'Baru',
        ];

        DB::table('reg_periksa')->insert($insert);
        $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
        return response()->json($reg, 201);
    }
}
