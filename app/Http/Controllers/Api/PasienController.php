<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $query = Pasien::query();
        if ($s = $request->search) $query->where(function($q) use ($s) { $q->where('nama', 'like', "%{$s}%")->orWhere('no_rm', 'like', "%{$s}%")->orWhere('nik', 'like', "%{$s}%"); });
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'nullable|string|max:30|unique:pasien,nik',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kelurahan' => 'nullable|string|max:50',
            'kecamatan' => 'nullable|string|max:50',
            'kota' => 'nullable|string|max:50',
            'provinsi' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
        ]);
        $data['no_rm'] = Pasien::generateNoRM();
        $pasien = Pasien::create($data);
        return response()->json($pasien, 201);
    }

    public function show(Pasien $pasien) { return response()->json($pasien); }

    public function update(Request $request, Pasien $pasien)
    {
        $data = $request->validate([
            'nama' => 'string|max:100',
            'nik' => 'nullable|string|max:30|unique:pasien,nik,' . $pasien->id,
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'in:L,P',
            'alamat' => 'nullable|string|max:255',
        ]);
        $pasien->update($data);
        return response()->json($pasien);
    }

    public function dokterList()
    {
        $dokters = DB::table('dokter')
            ->where('status', '1')
            ->select('kd_dokter', 'nm_dokter')
            ->orderBy('nm_dokter')
            ->get();
        return response()->json($dokters);
    }

    public function search(Request $request)
    {
        $s = $request->q;
        $query = DB::table('pasien');
        if ($s) {
            $query->where(function($q) use ($s) {
                $q->where('nm_pasien', 'like', "%{$s}%")
                  ->orWhere('no_rkm_medis', 'like', "%{$s}%")
                  ->orWhere('no_ktp', 'like', "%{$s}%");
            });
        }
        $patients = $query->orderBy('no_rkm_medis', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->no_rkm_medis,
                    'no_rkm_medis' => $p->no_rkm_medis,
                    'no_rm' => $p->no_rkm_medis,
                    'nm_pasien' => $p->nm_pasien,
                    'nama' => $p->nm_pasien,
                    'jk' => $p->jk,
                    'no_ktp' => $p->no_ktp,
                    'nik' => $p->no_ktp,
                    'tmp_lahir' => $p->tmp_lahir,
                    'tgl_lahir' => $p->tgl_lahir,
                    'alamat' => $p->alamat,
                    'gol_darah' => $p->gol_darah,
                    'pekerjaan' => $p->pekerjaan,
                    'agama' => $p->agama,
                    'no_tlp' => $p->no_tlp,
                    'pnd' => $p->pnd,
                    'stts_nikah' => $p->stts_nikah,
                    'keluarga' => $p->keluarga,
                    'namakeluarga' => $p->namakeluarga,
                    'kd_pj' => $p->kd_pj,
                    'no_peserta' => $p->no_peserta,
                    'umur' => $p->umur,
                ];
            });
        return response()->json($patients);
    }

    public function storePasien(Request $request)
    {
        $data = $request->validate([
            'nm_pasien' => 'required|string|max:40',
            'no_ktp' => 'nullable|string|max:20',
            'jk' => 'required|in:L,P',
            'tmp_lahir' => 'nullable|string|max:15',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:200',
            'gol_darah' => 'nullable|in:A,B,O,AB,-',
            'pekerjaan' => 'nullable|string|max:60',
            'agama' => 'nullable|string|max:12',
            'no_tlp' => 'nullable|string|max:40',
            'pnd' => 'nullable|string|max:20',
            'stts_nikah' => 'nullable|string|max:20',
            'keluarga' => 'nullable|string|max:20',
            'namakeluarga' => 'nullable|string|max:50',
            'kd_pj' => 'nullable|string|max:3',
            'no_peserta' => 'nullable|string|max:25',
        ]);

        $max = DB::table('pasien')
            ->where('no_rkm_medis', 'regexp', '^[0-9]+$')
            ->select(DB::raw('MAX(CAST(no_rkm_medis AS UNSIGNED)) as max_id'))
            ->value('max_id');
        $no_rkm_medis = str_pad(($max ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        $data['no_rkm_medis'] = $no_rkm_medis;
        $data['tgl_daftar'] = now()->toDateString();
        $data['umur'] = $data['tgl_lahir']
            ? Carbon::parse($data['tgl_lahir'])->age . ' Thn'
            : '0 Thn';

        DB::table('pasien')->insert($data);

        $pasien = DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->first();
        return response()->json($pasien, 201);
    }

    public function updatePasien(Request $request, $no_rkm_medis)
    {
        $pasien = DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->first();
        if (!$pasien) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validate([
            'nm_pasien' => 'sometimes|string|max:40',
            'no_ktp' => 'nullable|string|max:20',
            'jk' => 'sometimes|in:L,P',
            'tmp_lahir' => 'nullable|string|max:15',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:200',
            'gol_darah' => 'nullable|in:A,B,O,AB,-',
            'pekerjaan' => 'nullable|string|max:60',
            'agama' => 'nullable|string|max:12',
            'no_tlp' => 'nullable|string|max:40',
            'pnd' => 'nullable|string|max:20',
            'stts_nikah' => 'nullable|string|max:20',
            'keluarga' => 'nullable|string|max:20',
            'namakeluarga' => 'nullable|string|max:50',
            'kd_pj' => 'nullable|string|max:3',
            'no_peserta' => 'nullable|string|max:25',
        ]);

        if (isset($data['tgl_lahir'])) {
            $data['umur'] = Carbon::parse($data['tgl_lahir'])->age . ' Thn';
        }

        DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->update($data);
        $pasien = DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->first();
        return response()->json($pasien);
    }

    public function destroyPasien($no_rkm_medis)
    {
        $pasien = DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->first();
        if (!$pasien) return response()->json(['message' => 'Not found'], 404);
        DB::table('pasien')->where('no_rkm_medis', $no_rkm_medis)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function searchRegPeriksa(Request $request)
    {
        $q = $request->q;
        if (!$q || strlen($q) < 2) return response()->json([]);

        $results = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where(function($w) use ($q) {
                $w->where('pasien.nm_pasien', 'like', "%{$q}%")
                  ->orWhere('pasien.no_rkm_medis', 'like', "%{$q}%")
                  ->orWhere('pasien.no_ktp', 'like', "%{$q}%");
            })
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_dokter',
                'reg_periksa.stts',
                'pasien.nm_pasien',
                'pasien.no_ktp',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
            )
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->limit(15)
            ->get()
            ->map(function($r) {
                $isIgd = $r->kd_poli === 'IGDK' || $r->kd_poli === 'IGD' || ($r->nm_poli && str_contains($r->nm_poli, 'IGD'));
                $isActive = $r->stts !== 'Sudah';
                $location = $isActive
                    ? ($isIgd ? 'IGD' : ($r->nm_poli ?? 'Poli'))
                    : ($r->nm_poli ?? 'Poli') . ' (selesai)';

                $umur = '-';
                if ($r->tgl_lahir) {
                    $umur = Carbon::parse($r->tgl_lahir)->age . ' thn';
                }

                return [
                    'id' => $r->no_rawat,
                    'no_rm' => $r->no_rkm_medis,
                    'nama' => $r->nm_pasien,
                    'nik' => $r->no_ktp,
                    'location' => $location,
                    'module_key' => 'pasien-detail',
                    'data' => [
                        'no_rawat' => $r->no_rawat,
                        'pasien' => [
                            'no_rawat' => $r->no_rawat,
                            'no_rkm_medis' => $r->no_rkm_medis,
                            'nm_pasien' => $r->nm_pasien,
                            'jk' => $r->jk,
                            'tgl_lahir' => $r->tgl_lahir,
                            'alamat' => $r->alamat,
                            'umur' => $umur,
                            'nm_poli' => $r->nm_poli,
                            'nm_dokter' => $r->nm_dokter,
                            'jam_reg' => $r->jam_reg,
                            'tgl_registrasi' => $r->tgl_registrasi,
                            'stts' => $r->stts,
                            'kd_poli' => $r->kd_poli,
                        ],
                    ],
                    'tgl_registrasi' => $r->tgl_registrasi,
                    'jenis_registrasi' => $isIgd ? 'IGD' : ($r->nm_poli ?? 'Poli'),
                    'status_registrasi' => $r->stts,
                ];
            });

        return response()->json($results);
    }
}
