<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApotekController extends Controller
{
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
                    'databarang.harga_beli',
                    'databarang.harga_jual',
                    'databarang.stok',
                    'databarang.stok_minimal',
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
