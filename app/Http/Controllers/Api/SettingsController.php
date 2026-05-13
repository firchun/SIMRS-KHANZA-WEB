<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function identitas()
    {
        $row = DB::table('setting')->first();
        if (!$row) {
            return response()->json([
                'nama_instansi' => '',
                'alamat_instansi' => '',
                'kabupaten' => '',
                'propinsi' => '',
                'kontak' => '',
                'email' => '',
                'kode_ppk' => '',
                'kode_ppkinhealth' => '',
                'kode_ppkkemenkes' => '',
            ]);
        }
        return response()->json([
            'nama_instansi' => $row->nama_instansi ?? '',
            'alamat_instansi' => $row->alamat_instansi ?? '',
            'kabupaten' => $row->kabupaten ?? '',
            'propinsi' => $row->propinsi ?? '',
            'kontak' => $row->kontak ?? '',
            'email' => $row->email ?? '',
            'kode_ppk' => $row->kode_ppk ?? '',
            'kode_ppkinhealth' => $row->kode_ppkinhealth ?? '',
            'kode_ppkkemenkes' => $row->kode_ppkkemenkes ?? '',
        ]);
    }

    public function updateIdentitas(Request $request)
    {
        $data = $request->validate([
            'nama_instansi' => 'nullable|string|max:60',
            'alamat_instansi' => 'nullable|string|max:150',
            'kabupaten' => 'nullable|string|max:30',
            'propinsi' => 'nullable|string|max:30',
            'kontak' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:50',
            'kode_ppk' => 'nullable|string|max:15',
            'kode_ppkinhealth' => 'nullable|string|max:15',
            'kode_ppkkemenkes' => 'nullable|string|max:15',
        ]);

        $row = DB::table('setting')->first();
        if ($row) {
            DB::table('setting')->where('nama_instansi', $row->nama_instansi)->update($data);
        } else {
            DB::table('setting')->insert(array_merge($data, [
                'aktifkan' => 'Yes',
                'logo' => '',
                'wallpaper' => '',
            ]));
        }

        return response()->json(['message' => 'Identitas berhasil disimpan']);
    }

    public function depoList()
    {
        $ralan = [];
        $ranap = [];

        try {
            $ralan = DB::table('set_depo_ralan')
                ->leftJoin('depo_obat', 'set_depo_ralan.kd_depo', '=', 'depo_obat.kd_depo')
                ->leftJoin('bangsal', 'set_depo_ralan.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->select(
                    'set_depo_ralan.kd_depo',
                    'depo_obat.nama_depo',
                    DB::raw("COALESCE(depo_obat.nama_depo, 'Depo #' + set_depo_ralan.kd_depo) as nama"),
                    'set_depo_ralan.kd_bangsal',
                    'bangsal.nm_bangsal',
                    DB::raw("'Ralan' as jenis")
                )
                ->get();
        } catch (\Exception $e) {
            $ralan = [];
        }

        try {
            $ranap = DB::table('set_depo_ranap')
                ->leftJoin('depo_obat', 'set_depo_ranap.kd_depo', '=', 'depo_obat.kd_depo')
                ->leftJoin('bangsal', 'set_depo_ranap.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->select(
                    'set_depo_ranap.kd_depo',
                    'depo_obat.nama_depo',
                    DB::raw("COALESCE(depo_obat.nama_depo, 'Depo #' + set_depo_ranap.kd_depo) as nama"),
                    'set_depo_ranap.kd_bangsal',
                    'bangsal.nm_bangsal',
                    DB::raw("'Ranap' as jenis")
                )
                ->get();
        } catch (\Exception $e) {
            $ranap = [];
        }

        $merged = collect($ralan)->merge($ranap);
        return response()->json($merged);
    }

    public function industriFarmasi()
    {
        try {
            $data = DB::table('industrifarmasi')->orderBy('nama_industri')->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
