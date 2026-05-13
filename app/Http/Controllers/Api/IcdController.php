<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IcdController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->q;
        $type = $request->type ?? 'icd10';
        if (!$q || strlen($q) < 1) return response()->json([]);

        if ($type === 'icd10') {
            $data = DB::table('penyakit')
                ->where(function($w) use ($q) {
                    $w->where('kd_penyakit', 'like', "%{$q}%")
                      ->orWhere('nm_penyakit', 'like', "%{$q}%")
                      ->orWhere('ciri_ciri', 'like', "%{$q}%");
                })
                ->select('kd_penyakit', 'nm_penyakit', 'ciri_ciri', 'keterangan', 'status')
                ->orderBy('nm_penyakit')
                ->limit(50)
                ->get();
        } else {
            $data = DB::table('icd9')
                ->where(function($w) use ($q) {
                    $w->where('kode', 'like', "%{$q}%")
                      ->orWhere('deskripsi_panjang', 'like', "%{$q}%")
                      ->orWhere('deskripsi_pendek', 'like', "%{$q}%");
                })
                ->select('kode', 'deskripsi_panjang', 'deskripsi_pendek')
                ->orderBy('deskripsi_panjang')
                ->limit(50)
                ->get();
        }

        return response()->json($data);
    }
}
