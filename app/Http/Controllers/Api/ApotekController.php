<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApotekController extends Controller
{
    public function dashboard()
    {
        $industriCount = $this->safeCount('industrifarmasi');
        $jenisCount = $this->safeCount('jenis');
        $kategoriCount = $this->safeCount('kategori_barang');
        $golonganCount = $this->safeCount('golongan_barang');
        $kodesatuanCount = $this->safeCount('kodesatuan');
        $barangCount = $this->safeCount('databarang');

        $barangHabis = 0;
        $barangKritis = 0;
        $obatHabis = [];
        $stokUnit = [];
        $stokMasukKeluar = ['hari_ini' => ['masuk' => 0, 'keluar' => 0], 'bulan_ini' => ['masuk' => 0, 'keluar' => 0], 'total' => ['masuk' => 0, 'keluar' => 0]];

        try {
            $barangHabis = (int) DB::table(DB::raw('(SELECT d.kode_brng, COALESCE(SUM(g.stok), 0) as total_stok FROM databarang d LEFT JOIN gudangbarang g ON d.kode_brng = g.kode_brng GROUP BY d.kode_brng) sub'))
                ->where('total_stok', '<=', 0)->count();
        } catch (\Exception $e) {}

        try {
            $barangKritis = (int) DB::table(DB::raw('(SELECT d.kode_brng, d.stokminimal, COALESCE(SUM(g.stok), 0) as total_stok FROM databarang d LEFT JOIN gudangbarang g ON d.kode_brng = g.kode_brng GROUP BY d.kode_brng, d.stokminimal) sub'))
                ->where('total_stok', '>', 0)->whereColumn('total_stok', '<=', 'stokminimal')->count();
        } catch (\Exception $e) {}

        try {
            $obatHabis = DB::table(DB::raw('(SELECT d.kode_brng, d.nama_brng, COALESCE(s.satuan, d.kode_sat) as satuan, COALESCE(SUM(g.stok), 0) as total_stok FROM databarang d LEFT JOIN gudangbarang g ON d.kode_brng = g.kode_brng LEFT JOIN kodesatuan s ON d.kode_sat = s.kode_sat GROUP BY d.kode_brng, d.nama_brng, s.satuan, d.kode_sat) sub'))
                ->where('total_stok', '<=', 0)
                ->limit(20)
                ->get()
                ->map(fn($i) => ['kode' => $i->kode_brng, 'nama' => $i->nama_brng, 'stok' => 0, 'satuan' => $i->satuan ?? '-'])
                ->toArray();
        } catch (\Exception $e) {}

        try {
            $bangsal = DB::table('bangsal')->pluck('nm_bangsal', 'kd_bangsal');
        } catch (\Exception $e) {
            $bangsal = collect();
        }

        try {
            $stokUnit = DB::table('gudangbarang')
                ->join('databarang', 'gudangbarang.kode_brng', '=', 'databarang.kode_brng')
                ->select('gudangbarang.kd_bangsal', 'gudangbarang.kode_brng', 'databarang.nama_brng', DB::raw('SUM(gudangbarang.stok) as stok'))
                ->groupBy('gudangbarang.kd_bangsal', 'gudangbarang.kode_brng', 'databarang.nama_brng')
                ->having('stok', '>', 0)
                ->orderBy('gudangbarang.kd_bangsal')
                ->orderByDesc('stok')
                ->get()
                ->groupBy('kd_bangsal')
                ->map(function ($items, $kd) use ($bangsal) {
                    $top = $items->take(5);
                    return [
                        'unit' => $bangsal[$kd] ?? $kd,
                        'items' => $top->values()->map(fn($i) => [
                            'kode' => $i->kode_brng,
                            'nama' => $i->nama_brng,
                            'stok' => (int) $i->stok,
                        ])->toArray(),
                    ];
                })->values()->toArray();
        } catch (\Exception $e) {}

        try {
            $today = date('Y-m-d');
            $firstOfMonth = date('Y-m-01');
            $stokMasukKeluar = [
                'hari_ini' => [
                    'masuk' => (int) DB::table('mutasibarang')->whereDate('tanggal', $today)->where('jml', '>', 0)->sum('jml'),
                    'keluar' => (int) DB::table('mutasibarang')->whereDate('tanggal', $today)->where('jml', '<', 0)->sum(DB::raw('ABS(jml)')),
                ],
                'bulan_ini' => [
                    'masuk' => (int) DB::table('mutasibarang')->where('tanggal', '>=', $firstOfMonth)->where('jml', '>', 0)->sum('jml'),
                    'keluar' => (int) DB::table('mutasibarang')->where('tanggal', '>=', $firstOfMonth)->where('jml', '<', 0)->sum(DB::raw('ABS(jml)')),
                ],
            ];
        } catch (\Exception $e) {}

        return response()->json([
            'counts' => [
                'industri' => $industriCount,
                'jenis' => $jenisCount,
                'kategori' => $kategoriCount,
                'golongan' => $golonganCount,
                'kodesatuan' => $kodesatuanCount,
                'databarang' => $barangCount,
                'barang_habis' => $barangHabis,
                'barang_kritis' => $barangKritis,
            ],
            'obat_habis' => $obatHabis,
            'stok_unit' => $stokUnit,
            'stok_masuk_keluar' => $stokMasukKeluar,
        ]);
    }

    private function safeCount($table)
    {
        try { return DB::table($table)->count(); } catch (\Exception $e) { return 0; }
    }

    public function industri()
    {
        try {
            return response()->json(DB::table('industrifarmasi')->orderBy('nama_industri')->get());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function jenis()
    {
        try {
            return response()->json(DB::table('jenis')->orderBy('nama')->get());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function kategori()
    {
        try {
            return response()->json(DB::table('kategori_barang')->orderBy('nama')->get());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function golongan()
    {
        try {
            return response()->json(DB::table('golongan_barang')->orderBy('nama')->get());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function kodesatuan()
    {
        try {
            return response()->json(DB::table('kodesatuan')->orderBy('satuan')->get());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function databarang(Request $request)
    {
        $q = $request->q;
        $perPage = min((int) ($request->perPage ?? 50), 200);
        $page = (int) ($request->page ?? 1);

        try {
            $query = DB::table('databarang')
                ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
                ->leftJoin('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
                ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
                ->leftJoin('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
                ->leftJoin(DB::raw('(SELECT kode_brng, SUM(stok) as total_stok FROM gudangbarang GROUP BY kode_brng) g'), 'databarang.kode_brng', '=', 'g.kode_brng')
                ->select(
                    'databarang.kode_brng',
                    'databarang.nama_brng',
                    'databarang.kode_sat',
                    'kodesatuan.satuan',
                    'databarang.kdjns',
                    'jenis.nama as jenis_nama',
                    'databarang.kode_kategori',
                    'kategori_barang.nama as kategori_nama',
                    'databarang.kode_golongan',
                    'golongan_barang.nama as golongan_nama',
                    'databarang.h_beli as harga_beli',
                    'databarang.ralan as harga_jual',
                    DB::raw('COALESCE(g.total_stok, 0) as stok'),
                    'databarang.stokminimal as stok_minimal',
                    'databarang.status',
                );

            if ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('databarang.nama_brng', 'like', "%{$q}%")
                      ->orWhere('databarang.kode_brng', 'like', "%{$q}%");
                });
            }

            $total = (clone $query)->count();
            $data = $query->orderBy('databarang.nama_brng')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            return response()->json([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'lastPage' => (int) ceil($total / $perPage),
            ]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'total' => 0, 'page' => 1, 'perPage' => $perPage, 'lastPage' => 0]);
        }
    }
}
