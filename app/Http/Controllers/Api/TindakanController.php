<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TindakanController extends Controller
{
    public function jnsPerawatan(Request $request)
    {
        $q = $request->q;
        $query = DB::table('jns_perawatan')
            ->select('kd_jenis_prw', 'nm_perawatan', 'total_byrdr', 'total_byrpr', 'total_byrdrpr', 'material', 'bhp', 'tarif_tindakandr', 'tarif_tindakanpr', 'kso', 'menejemen', 'kd_pj', 'kd_poli', 'status');
        if ($q) {
            $query->where(function($w) use ($q) {
                $w->where('nm_perawatan', 'like', "%{$q}%")
                  ->orWhere('kd_jenis_prw', 'like', "%{$q}%");
            });
        }
        $data = $query->orderBy('nm_perawatan')->limit(50)->get();
        return response()->json($data);
    }

    public function petugasList()
    {
        $data = DB::table('petugas')
            ->select('nip', 'nama')
            ->where('status', '1')
            ->orderBy('nama')
            ->get();
        if ($data->isEmpty()) {
            $data = DB::table('pegawai')
                ->select('nik as nip', 'nama')
                ->where('stts_aktif', 'AKTIF')
                ->orderBy('nama')
                ->limit(50)
                ->get();
        }
        return response()->json($data);
    }

    public function getPenangananIgd($no_rawat)
    {
        $data = DB::table('rawat_jl_drpr')
            ->leftJoin('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_jl_drpr.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
            ->leftJoin('pegawai', 'rawat_jl_drpr.nip', '=', 'pegawai.nik')
            ->where('rawat_jl_drpr.no_rawat', $no_rawat)
            ->select(
                'rawat_jl_drpr.no_rawat',
                'rawat_jl_drpr.kd_jenis_prw',
                'rawat_jl_drpr.kd_dokter',
                'rawat_jl_drpr.nip',
                'rawat_jl_drpr.tgl_perawatan',
                'rawat_jl_drpr.jam_rawat',
                'rawat_jl_drpr.biaya_rawat',
                'rawat_jl_drpr.tarif_tindakandr',
                'rawat_jl_drpr.tarif_tindakanpr',
                'rawat_jl_drpr.material',
                'rawat_jl_drpr.bhp',
                'rawat_jl_drpr.kso',
                'rawat_jl_drpr.menejemen',
                'rawat_jl_drpr.stts_bayar',
                'jns_perawatan.nm_perawatan',
                'dokter.nm_dokter',
                DB::raw('COALESCE(petugas.nama, pegawai.nama) as nm_petugas')
            )
            ->orderBy('rawat_jl_drpr.tgl_perawatan', 'desc')
            ->orderBy('rawat_jl_drpr.jam_rawat', 'desc')
            ->get();
        return response()->json($data);
    }

    public function savePenangananIgd(Request $request, $no_rawat)
    {
        $request->validate([
            'kd_jenis_prw' => 'required|string|max:15',
            'kd_dokter' => 'required|string|max:20',
            'nip' => 'required|string|max:20',
            'tgl_perawatan' => 'required|date',
            'jam_rawat' => 'required|string|max:8',
            'material' => 'nullable|numeric',
            'bhp' => 'nullable|numeric',
            'tarif_tindakandr' => 'nullable|numeric',
            'tarif_tindakanpr' => 'nullable|numeric',
            'kso' => 'nullable|numeric',
            'menejemen' => 'nullable|numeric',
            'biaya_rawat' => 'nullable|numeric',
        ]);

        $exists = DB::table('rawat_jl_drpr')
            ->where('no_rawat', $no_rawat)
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->where('kd_dokter', $request->kd_dokter)
            ->where('nip', $request->nip)
            ->where('tgl_perawatan', $request->tgl_perawatan)
            ->where('jam_rawat', $request->jam_rawat)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Data sudah ada'], 409);
        }

        $tarif = DB::table('jns_perawatan')
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->first();

        DB::table('rawat_jl_drpr')->insert([
            'no_rawat' => $no_rawat,
            'kd_jenis_prw' => $request->kd_jenis_prw,
            'kd_dokter' => $request->kd_dokter,
            'nip' => $request->nip,
            'tgl_perawatan' => $request->tgl_perawatan,
            'jam_rawat' => $request->jam_rawat,
            'material' => $request->material ?? $tarif->material ?? 0,
            'bhp' => $request->bhp ?? $tarif->bhp ?? 0,
            'tarif_tindakandr' => $request->tarif_tindakandr ?? $tarif->tarif_tindakandr ?? 0,
            'tarif_tindakanpr' => $request->tarif_tindakanpr ?? $tarif->tarif_tindakanpr ?? 0,
            'kso' => $request->kso ?? $tarif->kso ?? 0,
            'menejemen' => $request->menejemen ?? $tarif->menejemen ?? 0,
            'biaya_rawat' => $request->biaya_rawat ?? $tarif->total_byrdrpr ?? $tarif->total_byrdr ?? 0,
            'stts_bayar' => 'Belum',
        ]);

        return response()->json(['message' => 'Penanganan berhasil disimpan']);
    }

    public function deletePenangananIgd(Request $request, $no_rawat)
    {
        $request->validate([
            'kd_jenis_prw' => 'required|string|max:15',
            'kd_dokter' => 'required|string|max:20',
            'nip' => 'required|string|max:20',
            'tgl_perawatan' => 'required|date',
            'jam_rawat' => 'required|string|max:8',
        ]);

        DB::table('rawat_jl_drpr')
            ->where('no_rawat', $no_rawat)
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->where('kd_dokter', $request->kd_dokter)
            ->where('nip', $request->nip)
            ->where('tgl_perawatan', $request->tgl_perawatan)
            ->where('jam_rawat', $request->jam_rawat)
            ->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    // === Ranap (rawat_inap_drpr) ===

    public function getPenangananRanap($no_rawat)
    {
        $data = DB::table('rawat_inap_drpr')
            ->leftJoin('jns_perawatan', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_inap_drpr.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
            ->leftJoin('pegawai', 'rawat_inap_drpr.nip', '=', 'pegawai.nik')
            ->where('rawat_inap_drpr.no_rawat', $no_rawat)
            ->select(
                'rawat_inap_drpr.no_rawat',
                'rawat_inap_drpr.kd_jenis_prw',
                'rawat_inap_drpr.kd_dokter',
                'rawat_inap_drpr.nip',
                'rawat_inap_drpr.tgl_perawatan',
                'rawat_inap_drpr.jam_rawat',
                'rawat_inap_drpr.biaya_rawat',
                'rawat_inap_drpr.tarif_tindakandr',
                'rawat_inap_drpr.tarif_tindakanpr',
                'rawat_inap_drpr.material',
                'rawat_inap_drpr.bhp',
                'rawat_inap_drpr.kso',
                'rawat_inap_drpr.menejemen',
                DB::raw("'Belum' as stts_bayar"),
                'jns_perawatan.nm_perawatan',
                'dokter.nm_dokter',
                DB::raw('COALESCE(petugas.nama, pegawai.nama) as nm_petugas')
            )
            ->orderBy('rawat_inap_drpr.tgl_perawatan', 'desc')
            ->orderBy('rawat_inap_drpr.jam_rawat', 'desc')
            ->get();
        return response()->json($data);
    }

    public function savePenangananRanap(Request $request, $no_rawat)
    {
        $request->validate([
            'kd_jenis_prw' => 'required|string|max:15',
            'kd_dokter' => 'required|string|max:20',
            'nip' => 'required|string|max:20',
            'tgl_perawatan' => 'required|date',
            'jam_rawat' => 'required|string|max:8',
            'material' => 'nullable|numeric',
            'bhp' => 'nullable|numeric',
            'tarif_tindakandr' => 'nullable|numeric',
            'tarif_tindakanpr' => 'nullable|numeric',
            'kso' => 'nullable|numeric',
            'menejemen' => 'nullable|numeric',
            'biaya_rawat' => 'nullable|numeric',
        ]);

        $exists = DB::table('rawat_inap_drpr')
            ->where('no_rawat', $no_rawat)
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->where('kd_dokter', $request->kd_dokter)
            ->where('nip', $request->nip)
            ->where('tgl_perawatan', $request->tgl_perawatan)
            ->where('jam_rawat', $request->jam_rawat)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Data sudah ada'], 409);
        }

        $tarif = DB::table('jns_perawatan')
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->first();

        DB::table('rawat_inap_drpr')->insert([
            'no_rawat' => $no_rawat,
            'kd_jenis_prw' => $request->kd_jenis_prw,
            'kd_dokter' => $request->kd_dokter,
            'nip' => $request->nip,
            'tgl_perawatan' => $request->tgl_perawatan,
            'jam_rawat' => $request->jam_rawat,
            'material' => $request->material ?? $tarif->material ?? 0,
            'bhp' => $request->bhp ?? $tarif->bhp ?? 0,
            'tarif_tindakandr' => $request->tarif_tindakandr ?? $tarif->tarif_tindakandr ?? 0,
            'tarif_tindakanpr' => $request->tarif_tindakanpr ?? $tarif->tarif_tindakanpr ?? 0,
            'kso' => $request->kso ?? $tarif->kso ?? 0,
            'menejemen' => $request->menejemen ?? $tarif->menejemen ?? 0,
            'biaya_rawat' => $request->biaya_rawat ?? $tarif->total_byrdrpr ?? $tarif->total_byrdr ?? 0,
        ]);

        return response()->json(['message' => 'Penanganan berhasil disimpan']);
    }

    public function deletePenangananRanap(Request $request, $no_rawat)
    {
        $request->validate([
            'kd_jenis_prw' => 'required|string|max:15',
            'kd_dokter' => 'required|string|max:20',
            'nip' => 'required|string|max:20',
            'tgl_perawatan' => 'required|date',
            'jam_rawat' => 'required|string|max:8',
        ]);

        DB::table('rawat_inap_drpr')
            ->where('no_rawat', $no_rawat)
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->where('kd_dokter', $request->kd_dokter)
            ->where('nip', $request->nip)
            ->where('tgl_perawatan', $request->tgl_perawatan)
            ->where('jam_rawat', $request->jam_rawat)
            ->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    // SOAP in rawat_inap_drpr
    public function getSoapListRanap($no_rawat)
    {
        $data = DB::table('rawat_inap_drpr')
            ->where('no_rawat', $no_rawat)
            ->where(function($q) {
                $q->whereNotNull('keluhan')->orWhere('keluhan', '<>', '');
            })
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
        return response()->json($data);
    }

    public function saveSoapRanap(Request $request, $no_rawat)
    {
        $request->validate([
            'keluhan' => 'nullable|string',
            'pemeriksaan' => 'nullable|string',
            'penilaian' => 'nullable|string',
            'instruksi' => 'nullable|string',
            'tensi' => 'nullable|string|max:12',
            'suhu_tubuh' => 'nullable|string|max:5',
            'nadi' => 'nullable|string|max:5',
            'respirasi' => 'nullable|string|max:5',
            'spo2' => 'nullable|string|max:5',
            'gcs' => 'nullable|string|max:10',
            'kesadaran' => 'nullable|string|max:30',
            'tinggi' => 'nullable|string|max:5',
            'berat' => 'nullable|string|max:5',
            'lingkar_perut' => 'nullable|string|max:5',
            'alergi' => 'nullable|string|max:80',
        ]);

        DB::table('rawat_inap_drpr')->insert([
            'no_rawat' => $no_rawat,
            'tgl_perawatan' => $request->tgl_perawatan ?? date('Y-m-d'),
            'jam_rawat' => $request->jam_rawat ?? date('H:i:s'),
            'keluhan' => $request->keluhan ?? '',
            'pemeriksaan' => $request->pemeriksaan ?? '',
            'penilaian' => $request->penilaian ?? '',
            'instruksi' => $request->instruksi ?? '',
            'evaluasi' => $request->evaluasi ?? '',
            'tensi' => $request->tensi ?? '',
            'suhu_tubuh' => $request->suhu_tubuh ?? '',
            'nadi' => $request->nadi ?? '',
            'respirasi' => $request->respirasi ?? '',
            'spo2' => $request->spo2 ?? '',
            'gcs' => $request->gcs ?? '',
            'kesadaran' => $request->kesadaran ?? '',
            'tinggi' => $request->tinggi ?? '',
            'berat' => $request->berat ?? '',
            'lingkar_perut' => $request->lingkar_perut ?? '',
            'alergi' => $request->alergi ?? '',
            'nip' => $request->nip ?? '',
        ]);

        return response()->json(['message' => 'SOAP berhasil disimpan']);
    }

    public function riwayatSoapRanap($no_rkm_medis)
    {
        $data = DB::table('rawat_inap_drpr')
            ->join('reg_periksa', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
            ->where(function($q) {
                $q->whereNotNull('rawat_inap_drpr.keluhan')->orWhere('rawat_inap_drpr.keluhan', '<>', '');
            })
            ->select(
                'rawat_inap_drpr.no_rawat',
                'rawat_inap_drpr.tgl_perawatan',
                'rawat_inap_drpr.jam_rawat',
                'rawat_inap_drpr.keluhan',
                'rawat_inap_drpr.pemeriksaan as pemeriksaan_objektif',
                'rawat_inap_drpr.penilaian',
                'rawat_inap_drpr.instruksi',
                'rawat_inap_drpr.evaluasi',
                'rawat_inap_drpr.suhu_tubuh',
                'rawat_inap_drpr.tensi',
                'rawat_inap_drpr.nadi',
                'rawat_inap_drpr.respirasi',
                'rawat_inap_drpr.spo2',
                'rawat_inap_drpr.gcs',
                'rawat_inap_drpr.kesadaran',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
            )
            ->orderBy('rawat_inap_drpr.tgl_perawatan', 'desc')
            ->orderBy('rawat_inap_drpr.jam_rawat', 'desc')
            ->limit(20)
            ->get();
        return response()->json($data);
    }

    public function soapGrafikRanap($no_rkm_medis)
    {
        $data = DB::table('rawat_inap_drpr')
            ->join('reg_periksa', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
            ->where(function($q) {
                $q->whereNotNull('rawat_inap_drpr.keluhan')->orWhere('rawat_inap_drpr.keluhan', '<>', '');
            })
            ->select(
                'rawat_inap_drpr.tgl_perawatan',
                'rawat_inap_drpr.jam_rawat',
                'rawat_inap_drpr.tensi',
                'rawat_inap_drpr.suhu_tubuh',
                'rawat_inap_drpr.nadi',
                'rawat_inap_drpr.respirasi',
                'rawat_inap_drpr.spo2',
                'rawat_inap_drpr.gcs',
                'rawat_inap_drpr.kesadaran',
                'rawat_inap_drpr.tinggi',
                'rawat_inap_drpr.berat',
                'rawat_inap_drpr.lingkar_perut',
            )
            ->orderBy('rawat_inap_drpr.tgl_perawatan')
            ->orderBy('rawat_inap_drpr.jam_rawat')
            ->get();
        return response()->json($data);
    }

    public function riwayatKunjungan($no_rkm_medis)
    {
        $data = DB::table('reg_periksa')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_reg',
                'reg_periksa.kd_dokter',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'reg_periksa.stts as stts_rawat',
                'reg_periksa.biaya_reg',
                'reg_periksa.kd_pj',
            )
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->limit(20)
            ->get();
        return response()->json($data);
    }

    public function riwayatSoap($no_rkm_medis)
    {
        $data = DB::table('pemeriksaan_ralan')
            ->join('reg_periksa', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
            ->select(
                'pemeriksaan_ralan.no_rawat',
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan as pemeriksaan_objektif',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'pemeriksaan_ralan.evaluasi',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
            )
            ->orderBy('pemeriksaan_ralan.tgl_perawatan', 'desc')
            ->orderBy('pemeriksaan_ralan.jam_rawat', 'desc')
            ->limit(20)
            ->get();
        return response()->json($data);
    }

    public function getSoapList($no_rawat)
    {
        $data = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
        return response()->json($data);
    }

    public function saveSoap(Request $request, $no_rawat)
    {
        $request->validate([
            'keluhan' => 'nullable|string',
            'pemeriksaan' => 'nullable|string',
            'penilaian' => 'nullable|string',
            'instruksi' => 'nullable|string',
            'tensi' => 'nullable|string|max:12',
            'suhu_tubuh' => 'nullable|string|max:5',
            'nadi' => 'nullable|string|max:5',
            'respirasi' => 'nullable|string|max:5',
            'spo2' => 'nullable|string|max:5',
            'gcs' => 'nullable|string|max:10',
            'kesadaran' => 'nullable|string|max:30',
            'tinggi' => 'nullable|string|max:5',
            'berat' => 'nullable|string|max:5',
            'lingkar_perut' => 'nullable|string|max:5',
            'alergi' => 'nullable|string|max:80',
        ]);

        DB::table('pemeriksaan_ralan')->insert([
            'no_rawat' => $no_rawat,
            'tgl_perawatan' => $request->tgl_perawatan ?? date('Y-m-d'),
            'jam_rawat' => $request->jam_rawat ?? date('H:i:s'),
            'keluhan' => $request->keluhan ?? '',
            'pemeriksaan' => $request->pemeriksaan ?? '',
            'penilaian' => $request->penilaian ?? '',
            'instruksi' => $request->instruksi ?? '',
            'evaluasi' => $request->evaluasi ?? '',
            'tensi' => $request->tensi ?? '',
            'suhu_tubuh' => $request->suhu_tubuh ?? '',
            'nadi' => $request->nadi ?? '',
            'respirasi' => $request->respirasi ?? '',
            'spo2' => $request->spo2 ?? '',
            'gcs' => $request->gcs ?? '',
            'kesadaran' => $request->kesadaran ?? '',
            'tinggi' => $request->tinggi ?? '',
            'berat' => $request->berat ?? '',
            'lingkar_perut' => $request->lingkar_perut ?? '',
            'alergi' => $request->alergi ?? '',
            'nip' => $request->nip ?? '',
        ]);

        return response()->json(['message' => 'SOAP berhasil disimpan']);
    }

    public function soapGrafik($no_rkm_medis)
    {
        $data = DB::table('pemeriksaan_ralan')
            ->join('reg_periksa', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
            ->select(
                'pemeriksaan_ralan.tgl_perawatan',
                'pemeriksaan_ralan.jam_rawat',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.lingkar_perut',
            )
            ->orderBy('pemeriksaan_ralan.tgl_perawatan')
            ->orderBy('pemeriksaan_ralan.jam_rawat')
            ->get();
        return response()->json($data);
    }

    public function dataList(Request $request, $jenis)
    {
        $q = $request->q;
        $limit = min((int) ($request->limit ?? 200), 500);

        $buildQuery = function($table, $columns, $searchCols, $where = null) use ($q, $limit) {
            $query = DB::table($table)->select($columns);
            if ($where) $query->where($where);
            if ($q) {
                $query->where(function($w) use ($q, $searchCols) {
                    foreach ($searchCols as $col) {
                        $w->orWhere($col, 'like', "%{$q}%");
                    }
                });
            }
            $total = (clone $query)->count();
            $data = $query->orderBy($searchCols[0])->limit($limit)->get();
            return compact('data', 'total');
        };

        $mapStatus = function($result) {
            return [
                'total' => $result['total'],
                'data' => collect($result['data'])->map(function($i) {
                    $i = (array) $i;
                    $i['status_label'] = ($i['status'] ?? '0') === '1' ? 'Aktif' : 'Tidak Aktif';
                    return $i;
                }),
            ];
        };

        switch ($jenis) {
            case 'jalan':
                $result = $buildQuery('jns_perawatan',
                    ['kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byrdrpr as tarif', 'kd_pj as kategori', 'status'],
                    ['nm_perawatan', 'kd_jenis_prw']
                );
                break;
            case 'inap':
                $result = $buildQuery('jns_perawatan_inap',
                    ['kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byrdrpr as tarif', DB::raw("CONCAT(COALESCE(kelas,''),' / ',COALESCE(kd_bangsal,'')) as kategori"), 'status'],
                    ['nm_perawatan', 'kd_jenis_prw']
                );
                break;
            case 'operasi':
                $result = $buildQuery('paket_operasi',
                    ['kode_paket as kode', 'nm_perawatan as nama', DB::raw("COALESCE(bagian_rs,0) + COALESCE(sewa_ok,0) + COALESCE(alat,0) + COALESCE(akomodasi,0) as tarif"), 'kategori', 'status'],
                    ['nm_perawatan', 'kode_paket']
                );
                break;
            case 'lab-pk':
                $result = $buildQuery('jns_perawatan_lab',
                    ['kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byr as tarif', 'kategori', 'status'],
                    ['nm_perawatan', 'kd_jenis_prw'],
                    ['kategori' => 'PK']
                );
                break;
            case 'lab-pa':
                $result = $buildQuery('jns_perawatan_lab',
                    ['kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byr as tarif', 'kategori', 'status'],
                    ['nm_perawatan', 'kd_jenis_prw'],
                    ['kategori' => 'PA']
                );
                break;
            case 'lab-mb':
                $result = $buildQuery('jns_perawatan_lab',
                    ['kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byr as tarif', 'kategori', 'status'],
                    ['nm_perawatan', 'kd_jenis_prw'],
                    ['kategori' => 'MB']
                );
                break;
            case 'radiologi':
                $result = $buildQuery('jns_perawatan_radiologi',
                    ['kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byr as tarif', DB::raw("'-' as kategori"), 'status'],
                    ['nm_perawatan', 'kd_jenis_prw']
                );
                break;
            default:
                return response()->json([]);
        }
        return response()->json($mapStatus($result));
    }
}
