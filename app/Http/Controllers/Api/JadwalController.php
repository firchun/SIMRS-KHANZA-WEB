<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    public function praktek()
    {
        $rows = DB::table('jadwal')
            ->leftJoin('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('poliklinik', 'jadwal.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'jadwal.kd_dokter',
                'jadwal.hari_kerja',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'jadwal.kd_poli',
                'jadwal.kuota',
                'dokter.nm_dokter',
                'poliklinik.nm_poli'
            )
            ->orderBy('jadwal.hari_kerja')
            ->orderBy('jadwal.jam_mulai')
            ->get()
            ->map(function ($r) {
                $hariMap = [
                    'SENIN' => 'Senin', 'SELASA' => 'Selasa', 'RABU' => 'Rabu',
                    'KAMIS' => 'Kamis', 'JUMAT' => 'Jumat', 'SABTU' => 'Sabtu', 'AKHAD' => 'Ahad',
                ];
                return [
                    'kd_dokter' => $r->kd_dokter,
                    'nm_dokter' => $r->nm_dokter ?? '-',
                    'nm_poli' => $r->nm_poli ?: $r->kd_poli,
                    'hari_kerja' => $hariMap[$r->hari_kerja] ?? $r->hari_kerja,
                    'jam_mulai' => substr($r->jam_mulai, 0, 5),
                    'jam_selesai' => substr($r->jam_selesai, 0, 5),
                    'kuota' => $r->kuota,
                ];
            });

        return response()->json($rows);
    }

    public function poliList()
    {
        $rows = DB::table('poliklinik')
            ->orderBy('kd_poli')
            ->get()
            ->map(function ($r) {
                return [
                    'kd_poli' => $r->kd_poli,
                    'nm_poli' => $r->nm_poli,
                    'registrasi' => $r->registrasi,
                    'registrasilama' => $r->registrasilama,
                    'status' => $r->status,
                    'status_label' => $r->status === '1' ? 'Aktif' : 'Tidak Aktif',
                ];
            });
        return response()->json($rows);
    }

    public function poliStore(Request $request)
    {
        $data = $request->validate([
            'kd_poli' => 'required|string|max:5|unique:poliklinik,kd_poli',
            'nm_poli' => 'required|string|max:50',
            'registrasi' => 'nullable|numeric',
            'registrasilama' => 'nullable|numeric',
            'status' => 'required|in:0,1',
        ]);
        DB::table('poliklinik')->insert($data);
        $poli = DB::table('poliklinik')->where('kd_poli', $data['kd_poli'])->first();
        return response()->json($poli, 201);
    }

    public function poliUpdate(Request $request, $kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        if (!$poli) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validate([
            'nm_poli' => 'required|string|max:50',
            'registrasi' => 'nullable|numeric',
            'registrasilama' => 'nullable|numeric',
            'status' => 'required|in:0,1',
        ]);
        DB::table('poliklinik')->where('kd_poli', $kd_poli)->update($data);
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return response()->json($poli);
    }

    public function poliDestroy($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        if (!$poli) return response()->json(['message' => 'Not found'], 404);
        DB::table('poliklinik')->where('kd_poli', $kd_poli)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function penjabList()
    {
        $rows = DB::table('penjab')
            ->orderBy('png_jawab')
            ->get()
            ->map(function ($r) {
                return [
                    'kd_pj' => $r->kd_pj,
                    'png_jawab' => $r->png_jawab,
                    'nama_perusahaan' => $r->nama_perusahaan,
                    'alamat' => $r->alamat_asuransi,
                    'no_telp' => $r->no_telp,
                    'attn' => $r->attn,
                    'status' => $r->status,
                    'status_label' => $r->status === '1' ? 'Aktif' : 'Tidak Aktif',
                ];
            });
        return response()->json($rows);
    }

    public function penjabStore(Request $request)
    {
        $data = $request->validate([
            'kd_pj' => 'required|string|max:3|unique:penjab,kd_pj',
            'png_jawab' => 'required|string|max:30',
            'nama_perusahaan' => 'nullable|string|max:60',
            'alamat' => 'nullable|string|max:130',
            'no_telp' => 'nullable|string|max:40',
            'attn' => 'nullable|string|max:60',
            'status' => 'required|in:0,1',
        ]);
        $data['alamat_asuransi'] = $data['alamat'] ?? '';
        unset($data['alamat']);

        DB::table('penjab')->insert($data);
        $row = DB::table('penjab')->where('kd_pj', $data['kd_pj'])->first();
        return response()->json($row, 201);
    }

    public function penjabUpdate(Request $request, $kd_pj)
    {
        $row = DB::table('penjab')->where('kd_pj', $kd_pj)->first();
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validate([
            'png_jawab' => 'required|string|max:30',
            'nama_perusahaan' => 'nullable|string|max:60',
            'alamat' => 'nullable|string|max:130',
            'no_telp' => 'nullable|string|max:40',
            'attn' => 'nullable|string|max:60',
            'status' => 'required|in:0,1',
        ]);
        $data['alamat_asuransi'] = $data['alamat'] ?? '';
        unset($data['alamat']);

        DB::table('penjab')->where('kd_pj', $kd_pj)->update($data);
        $row = DB::table('penjab')->where('kd_pj', $kd_pj)->first();
        return response()->json($row);
    }

    public function penjabDestroy($kd_pj)
    {
        $row = DB::table('penjab')->where('kd_pj', $kd_pj)->first();
        if (!$row) return response()->json(['message' => 'Not found'], 404);
        DB::table('penjab')->where('kd_pj', $kd_pj)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
