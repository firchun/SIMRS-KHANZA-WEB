<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaboratoriumController extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-d');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $q = $request->q;
        $status = $request->status;

        $query = DB::table('permintaan_lab')
            ->leftJoin('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter', 'permintaan_lab.dokter_perujuk', '=', 'dokter.kd_dokter')
            ->whereBetween('permintaan_lab.tgl_permintaan', [$tgl1, $tgl2]);

        if ($status === 'selesai') {
            $query->where('permintaan_lab.status', 'selesai');
        } elseif ($status === 'belum') {
            $query->where('permintaan_lab.status', '!=', 'selesai');
        }

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('pasien.nm_pasien', 'LIKE', "%$q%")
                    ->orWhere('pasien.no_rkm_medis', 'LIKE', "%$q%")
                    ->orWhere('permintaan_lab.no_rawat', 'LIKE', "%$q%");
            });
        }

        $list = $query->select(
            'permintaan_lab.noorder',
            'permintaan_lab.no_rawat',
            'permintaan_lab.tgl_permintaan',
            'permintaan_lab.jam_permintaan',
            'permintaan_lab.status',
            'permintaan_lab.tgl_sampel',
            'permintaan_lab.jam_sampel',
            'reg_periksa.no_rkm_medis',
            'pasien.nm_pasien',
            'dokter.nm_dokter'
        )
            ->orderBy('permintaan_lab.tgl_permintaan', 'desc')
            ->orderBy('permintaan_lab.jam_permintaan', 'desc')
            ->get()
            ->map(function ($r) {
                return [
                    'noorder' => $r->noorder,
                    'no_rawat' => $r->no_rawat,
                    'no_rkm_medis' => $r->no_rkm_medis ?? '-',
                    'nm_pasien' => $r->nm_pasien ?? '-',
                    'tgl_permintaan' => $r->tgl_permintaan,
                    'jam_permintaan' => $r->jam_permintaan,
                    'status' => $r->status ?? '-',
                    'diterima' => $r->tgl_sampel ? 'Diterima' : 'Belum',
                    'tgl_diterima' => $r->tgl_sampel,
                    'jam_diterima' => $r->jam_sampel,
                    'nm_dokter' => $r->nm_dokter ?? '-',
                ];
            });

        $counts = [
            'total' => $list->count(),
            'belum' => $list->where('status', '!=', 'selesai')->count(),
            'selesai' => $list->where('status', 'selesai')->count(),
        ];

        return response()->json(['list' => $list, 'counts' => $counts]);
    }

    public function detail($noorder)
    {
        $items = DB::table('permintaan_detail_permintaan_lab')
            ->leftJoin('jns_perawatan_lab', 'permintaan_detail_permintaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->leftJoin('template_laboratorium', 'permintaan_detail_permintaan_lab.id_template', '=', 'template_laboratorium.id_template')
            ->where('permintaan_detail_permintaan_lab.noorder', $noorder)
            ->select(
                'permintaan_detail_permintaan_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'jns_perawatan_lab.total_byr as tarif',
                'jns_perawatan_lab.kategori',
                'permintaan_detail_permintaan_lab.id_template',
                'template_laboratorium.pemeriksaan as nm_template',
                'template_laboratorium.satuan',
                'template_laboratorium.nilai_rujukan_ld',
                'template_laboratorium.nilai_rujukan_la',
                'template_laboratorium.nilai_rujukan_pd',
                'template_laboratorium.nilai_rujukan_pa',
                'permintaan_detail_permintaan_lab.nilai_rujukan as hasil'
            )
            ->get();

        return response()->json(['items' => $items]);
    }

    public function sample(Request $request)
    {
        $data = $request->validate([
            'noorder' => 'required|string|max:20',
            'tgl_sampel' => 'nullable|date',
            'jam_sampel' => 'nullable|string|max:8',
        ]);

        $updated = DB::table('permintaan_lab')
            ->where('noorder', $data['noorder'])
            ->update([
                'tgl_sampel' => $data['tgl_sampel'] ?? date('Y-m-d'),
                'jam_sampel' => $data['jam_sampel'] ?? date('H:i:s'),
            ]);

        if (!$updated) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        return response()->json(['message' => 'Sample berhasil disimpan']);
    }

    public function kirimLis(Request $request)
    {
        $noorder = $request->input('noorder');
        if (!$noorder) return response()->json(['message' => 'No order diperlukan'], 400);
        // TODO: integrasi actual LIS
        return response()->json(['message' => 'Data berhasil dikirim ke LIS']);
    }

    public function tarikLis(Request $request)
    {
        $noorder = $request->input('noorder');
        if (!$noorder) return response()->json(['message' => 'No order diperlukan'], 400);
        // TODO: integrasi actual LIS
        return response()->json(['message' => 'Data berhasil ditarik dari LIS']);
    }

    public function updateHasil(Request $request)
    {
        $data = $request->validate([
            'noorder' => 'required|string|max:15',
            'kd_jenis_prw' => 'required|string|max:15',
            'id_template' => 'required|integer',
            'nilai_rujukan' => 'nullable|string|max:200',
        ]);

        $updated = DB::table('permintaan_detail_permintaan_lab')
            ->where('noorder', $data['noorder'])
            ->where('kd_jenis_prw', $data['kd_jenis_prw'])
            ->where('id_template', $data['id_template'])
            ->update(['nilai_rujukan' => $data['nilai_rujukan']]);

        if (!$updated) return response()->json(['message' => 'Item tidak ditemukan'], 404);
        return response()->json(['message' => 'Hasil berhasil disimpan']);
    }

    public function dataHasil($noorder)
    {
        $order = DB::table('permintaan_lab')->where('noorder', $noorder)->first();
        if (!$order) return response()->json(['message' => 'Permintaan tidak ditemukan'], 404);

        $items = DB::table('permintaan_detail_permintaan_lab')
            ->leftJoin('template_laboratorium', 'permintaan_detail_permintaan_lab.id_template', '=', 'template_laboratorium.id_template')
            ->where('permintaan_detail_permintaan_lab.noorder', $noorder)
            ->select(
                'permintaan_detail_permintaan_lab.kd_jenis_prw',
                'permintaan_detail_permintaan_lab.id_template',
                'template_laboratorium.Pemeriksaan as pemeriksaan',
                'template_laboratorium.satuan',
                'template_laboratorium.nilai_rujukan_ld',
                'template_laboratorium.nilai_rujukan_la',
                'template_laboratorium.nilai_rujukan_pd',
                'template_laboratorium.nilai_rujukan_pa',
                'template_laboratorium.biaya_item',
                'template_laboratorium.bagian_rs as template_bagian_rs',
                'template_laboratorium.bhp as template_bhp',
                'template_laboratorium.bagian_perujuk',
                'template_laboratorium.bagian_dokter',
                'template_laboratorium.bagian_laborat',
                'template_laboratorium.kso as template_kso',
                'template_laboratorium.menejemen as template_menejemen'
            )
            ->get();

        $perawatanList = DB::table('jns_perawatan_lab')
            ->whereIn('kd_jenis_prw', $items->pluck('kd_jenis_prw')->unique())
            ->select('kd_jenis_prw', 'nm_perawatan', 'bagian_rs', 'bhp', 'tarif_perujuk',
                'tarif_tindakan_dokter', 'tarif_tindakan_petugas', 'kso', 'menejemen', 'total_byr', 'kategori')
            ->get()
            ->keyBy('kd_jenis_prw');

        $grouped = [];
        foreach ($items as $item) {
            $prw = $perawatanList->get($item->kd_jenis_prw);
            $group = $item->kd_jenis_prw;
            if (!isset($grouped[$group])) {
                $grouped[$group] = [
                    'kd_jenis_prw' => $item->kd_jenis_prw,
                    'nm_perawatan' => $prw->nm_perawatan ?? '-',
                    'tarif' => $prw->total_byr ?? 0,
                    'kategori' => $prw->kategori ?? 'PK',
                    'templates' => [],
                ];
            }
            $grouped[$group]['templates'][] = [
                'id_template' => $item->id_template,
                'pemeriksaan' => $item->pemeriksaan ?? '',
                'satuan' => $item->satuan ?? '',
                'nilai_rujukan' => trim(
                    ($item->nilai_rujukan_ld ? "L:{$item->nilai_rujukan_ld}" : '') .
                    ($item->nilai_rujukan_ld && $item->nilai_rujukan_pd ? ' | ' : '') .
                    ($item->nilai_rujukan_pd ? "P:{$item->nilai_rujukan_pd}" : '')
                ),
                'biaya_item' => $item->biaya_item ?? 0,
            ];
        }

        // Load existing hasil from detail_periksa_lab
        $existing = DB::table('detail_periksa_lab')
            ->where('no_rawat', $order->no_rawat)
            ->whereIn('kd_jenis_prw', $items->pluck('kd_jenis_prw')->unique())
            ->select('kd_jenis_prw', 'id_template', 'nilai', 'keterangan', 'tgl_periksa', 'jam')
            ->get()
            ->keyBy(fn($r) => $r->kd_jenis_prw . '_' . $r->id_template);

        $hasExisting = $existing->isNotEmpty();

        return response()->json([
            'no_rawat' => $order->no_rawat,
            'no_rkm_medis' => DB::table('reg_periksa')->where('no_rawat', $order->no_rawat)->value('no_rkm_medis'),
            'dokter_perujuk' => $order->dokter_perujuk,
            'status' => $order->status,
            'groups' => array_values($grouped),
            'existing' => $existing->map(fn($r) => [
                'kd_jenis_prw' => $r->kd_jenis_prw,
                'id_template' => $r->id_template,
                'nilai' => $r->nilai,
                'keterangan' => $r->keterangan,
            ])->values(),
            'has_existing' => $hasExisting,
        ]);
    }

    public function simpanHasil(Request $request)
    {
        $data = $request->validate([
            'noorder' => 'required|string|max:15',
            'kategori' => 'required|string|in:PK,PA,MB',
            'items' => 'required|array|min:1',
            'items.*.kd_jenis_prw' => 'required|string|max:15',
            'items.*.id_template' => 'required|integer',
            'items.*.nilai' => 'nullable|string|max:200',
            'items.*.keterangan' => 'nullable|string|max:200',
        ]);

        $order = DB::table('permintaan_lab')->where('noorder', $data['noorder'])->first();
        if (!$order) return response()->json(['message' => 'Permintaan tidak ditemukan'], 404);

        $no_rawat = $order->no_rawat;
        $tgl = date('Y-m-d');
        $jam = date('H:i:s');
        $nip = auth()->user()->nik ?? auth()->user()->id_user ?? '000000';
        $kd_dokter = $order->dokter_perujuk;

        $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
        $status = $reg ? ucfirst(strtolower($reg->status_lanjut ?? 'ralan')) : 'Ralan';

        $grouped = collect($data['items'])->groupBy('kd_jenis_prw');

        DB::beginTransaction();
        try {
            foreach ($grouped as $kd_jenis_prw => $items) {
                $prw = DB::table('jns_perawatan_lab')->where('kd_jenis_prw', $kd_jenis_prw)->first();
                if (!$prw) continue;

                DB::table('periksa_lab')->updateOrInsert(
                    [
                        'no_rawat' => $no_rawat,
                        'kd_jenis_prw' => $kd_jenis_prw,
                        'tgl_periksa' => $tgl,
                        'jam' => $jam,
                    ],
                    [
                        'nip' => $nip,
                        'dokter_perujuk' => $order->dokter_perujuk,
                        'bagian_rs' => $prw->bagian_rs ?? 0,
                        'bhp' => $prw->bhp ?? 0,
                        'tarif_perujuk' => $prw->tarif_perujuk ?? 0,
                        'tarif_tindakan_dokter' => $prw->tarif_tindakan_dokter ?? 0,
                        'tarif_tindakan_petugas' => $prw->tarif_tindakan_petugas ?? 0,
                        'kso' => $prw->kso ?? 0,
                        'menejemen' => $prw->menejemen ?? 0,
                        'biaya' => $prw->total_byr ?? 0,
                        'kd_dokter' => $kd_dokter,
                        'status' => $status,
                        'kategori' => $data['kategori'],
                    ]
                );

                foreach ($items as $item) {
                    $tmpl = DB::table('template_laboratorium')
                        ->where('id_template', $item['id_template'])
                        ->where('kd_jenis_prw', $item['kd_jenis_prw'])
                        ->first();

                    $nilai_rujukan = '';
                    if ($tmpl) {
                        $parts = array_filter([
                            $tmpl->nilai_rujukan_ld ? "L:{$tmpl->nilai_rujukan_ld}" : null,
                            $tmpl->nilai_rujukan_la ? "A:{$tmpl->nilai_rujukan_la}" : null,
                            $tmpl->nilai_rujukan_pd ? "P:{$tmpl->nilai_rujukan_pd}" : null,
                            $tmpl->nilai_rujukan_pa ? "A:{$tmpl->nilai_rujukan_pa}" : null,
                        ]);
                        $nilai_rujukan = implode(' | ', $parts);
                    }

                    DB::table('detail_periksa_lab')->updateOrInsert(
                        [
                            'no_rawat' => $no_rawat,
                            'kd_jenis_prw' => $item['kd_jenis_prw'],
                            'tgl_periksa' => $tgl,
                            'jam' => $jam,
                            'id_template' => $item['id_template'],
                        ],
                        [
                            'nilai' => $item['nilai'] ?? '',
                            'nilai_rujukan' => $nilai_rujukan,
                            'keterangan' => $item['keterangan'] ?? '',
                            'bagian_rs' => $tmpl->bagian_rs ?? 0,
                            'bhp' => $tmpl->bhp ?? 0,
                            'bagian_perujuk' => $tmpl->bagian_perujuk ?? 0,
                            'bagian_dokter' => $tmpl->bagian_dokter ?? 0,
                            'bagian_laborat' => $tmpl->bagian_laborat ?? 0,
                            'kso' => $tmpl->kso ?? 0,
                            'menejemen' => $tmpl->menejemen ?? 0,
                            'biaya_item' => $tmpl->biaya_item ?? 0,
                        ]
                    );
                }
            }

            DB::table('permintaan_lab')
                ->where('noorder', $data['noorder'])
                ->update([
                    'tgl_hasil' => $tgl,
                    'jam_hasil' => $jam,
                ]);

            DB::commit();
            return response()->json(['message' => 'Hasil berhasil disimpan', 'tgl_periksa' => $tgl, 'jam' => $jam]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function templates()
    {
        $rows = DB::table('template_laboratorium')
            ->orderBy('Pemeriksaan')
            ->get()
            ->map(function ($r) {
                return [
                    'kd_jenis_prw' => $r->kd_jenis_prw,
                    'id_template' => $r->id_template,
                    'pemeriksaan' => $r->pemeriksaan ?? '',
                    'nilai_rujukan' => $r->nilai_rujukan ?? '',
                    'satuan' => $r->satuan ?? '',
                ];
            });
        return response()->json($rows);
    }

    public function perawatan($kategori)
    {
        $rows = DB::table('jns_perawatan_lab')
            ->where('kategori', $kategori)
            ->where('status', '1')
            ->orderBy('nm_perawatan')
            ->select('kd_jenis_prw as kode', 'nm_perawatan as nama', 'total_byr as tarif')
            ->get();
        return response()->json($rows);
    }
}
