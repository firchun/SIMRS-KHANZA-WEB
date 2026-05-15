<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BpjsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BpjsController extends Controller
{
    public function dashboard(Request $request)
    {
        $tgl1 = $request->get('tgl1', date('Y-m-01'));
        $tgl2 = $request->get('tgl2', date('Y-m-d'));

        $pingStart = microtime(true);
        $pingStatus = 'Terhubung';
        $pingLatency = '-';
        try {
            $sock = @fsockopen('apijkn.bpjs-kesehatan.go.id', 443, $errno, $errstr, 5);
            if ($sock) {
                $pingLatency = round((microtime(true) - $pingStart) * 1000) . 'ms';
                fclose($sock);
            } else {
                $pingStatus = 'Tidak Terhubung';
                $pingLatency = $errstr;
            }
        } catch (\Exception $e) {
            $pingStatus = 'Error';
            $pingLatency = $e->getMessage();
        }

        $antrian = DB::table('reg_periksa')
            ->where('kd_poli', '!=', 'IGDK')
            ->whereBetween('tgl_registrasi', [$tgl1, $tgl2])
            ->count();

        $waktuTunggu = DB::table('reg_periksa')
            ->where('kd_poli', '!=', 'IGDK')
            ->whereBetween('tgl_registrasi', [$tgl1, $tgl2])
            ->whereNotNull('jam_reg')
            ->selectRaw('COALESCE(ROUND(AVG(TIMESTAMPDIFF(MINUTE, CONCAT(tgl_registrasi, " ", jam_reg), NOW()))), 0) as rata_rata')
            ->value('rata_rata');

        $totalBed = DB::table('kamar')->where('statusdata', '1')->count();
        $bedTerisi = DB::table('kamar')->where('statusdata', '1')->where('status', 'ISI')->count();
        $mutasiKamar = DB::table('kamar_inap')->whereBetween('tgl_masuk', [$tgl1, $tgl2])->count();

        $sepRajal = DB::table('bridging_sep')->where('jnspelayanan', '1')->count();
        $sepRanap = DB::table('bridging_sep')->where('jnspelayanan', '2')->count();
        $sepIgd = DB::table('bridging_sep')->whereNull('jnspelayanan')->orWhere('jnspelayanan', '')->count();
        $totalSep = DB::table('bridging_sep')->count();

        $layanan = [
            'Vclaim' => $this->pingService('Vclaim', 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest'),
            'Antrian BPJS' => $this->pingService('Antrian BPJS', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs'),
            'Aplicare' => $this->pingService('Aplicare', 'https://apijkn.bpjs-kesehatan.go.id/aplicares'),
            'Icare' => $this->pingService('Icare', 'https://apijkn.bpjs-kesehatan.go.id/icare'),
            'Pcare' => $this->pingService('Pcare', 'https://apijkn.bpjs-kesehatan.go.id/pcare'),
        ];

        $data = [
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'antrian_dilayani' => $antrian,
            'waktu_tunggu' => $waktuTunggu,
            'sep_ranap' => $sepRanap,
            'sep_rajal' => $sepRajal,
            'status_api_mjkn' => $layanan['Vclaim'],
            'koneksi_bpjs' => ['status' => $pingStatus, 'latency' => $pingLatency],
            'total_bed' => $totalBed,
            'bed_terisi' => $bedTerisi,
            'mutasi_kamar' => $mutasiKamar,
            'ringkasan_sep' => [
                ['label' => 'Rawat Jalan', 'count' => $sepRajal],
                ['label' => 'Rawat Inap', 'count' => $sepRanap],
                ['label' => 'IGD', 'count' => $sepIgd],
                ['label' => 'Total', 'count' => $totalSep],
            ],
            'status_layanan' => array_values($layanan),
        ];
        return response()->json($data);
    }

    private function pingService($name, $url)
    {
        $start = microtime(true);
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_NOBODY => true,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode > 0) {
                $latency = round((microtime(true) - $start) * 1000) . 'ms';
                return ['name' => $name, 'status' => 'Online', 'latency' => $latency];
            }
        } catch (\Exception $e) {
        }
        $latency = round((microtime(true) - $start) * 1000) . 'ms';
        return ['name' => $name, 'status' => 'Offline', 'latency' => $latency];
    }

    public function cariPeserta(Request $request)
    {
        $request->validate(['nomor' => 'required|string', 'tanggal' => 'nullable|date']);

        $nomor = $request->nomor;
        $tanggal = $request->tanggal ?? date('Y-m-d');

        if (strlen($nomor) === 16 && is_numeric($nomor)) {
            $bpjs = new BpjsService('vclaim');
            return $bpjs->get('Peserta/nik/' . $nomor . '/tglSEP/' . $tanggal);
        } else {
            $bpjs = new BpjsService('vclaim');
            return $bpjs->get('Peserta/nokartu/' . $nomor . '/tglSEP/' . $tanggal);
        }
    }

    public function cariSep($nomor)
    {
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('SEP/' . $nomor);
    }

    public function insertSep(Request $request)
    {
        $bpjs = new BpjsService('vclaim');
        return $bpjs->post('SEP/2.0/insert', $request->all());
    }

    public function referensiDiagnosa(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('referensi/diagnosa/' . $keyword);
    }

    public function referensiPoli(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('referensi/poli/' . $keyword);
    }

    public function referensiFaskes(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $jenis = $request->get('jenis', '2');
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('referensi/faskes/' . $keyword . '/' . $jenis);
    }

    public function referensiDpjp(Request $request)
    {
        $jenis = $request->get('jenis', '2');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $kode = $request->get('kode', '');
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('referensi/dokter/pelayanan/' . $jenis . '/tglPelayanan/' . $tanggal . '/Spesialis/' . $kode);
    }

    public function antreanRefPoli()
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('ref/poli');
    }

    public function antreanRefDokter()
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('ref/dokter');
    }

    public function antreanRefJadwal(Request $request)
    {
        $poli = $request->get('poli', '');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('jadwaldokter/kodepoli/' . $poli . '/tanggal/' . $tanggal);
    }

    public function antreanTambah(Request $request)
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->post('antrean/add', $request->all());
    }

    public function antreanDashboard(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $waktu = $request->get('waktu', '');
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('dashboard/waktutunggu/tanggal/' . $tanggal . '/waktu/' . $waktu);
    }

    public function antreanPerTanggal($tanggal)
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('antrean/pendaftaran/tanggal/' . $tanggal);
    }

    public function kamarApplicare(Request $request)
    {
        $start = $request->get('start', '0');
        $limit = $request->get('limit', '10');
        $bpjs = new BpjsService('aplicares');
        return $bpjs->get('rest/bed/read/' . config('bpjs.ppk_code', env('JKN_PPK_CODE', '')) . '/' . $start . '/' . $limit);
    }

    public function kamarApplicareRef()
    {
        $bpjs = new BpjsService('aplicares');
        return $bpjs->get('rest/ref/kelas');
    }

    public function kamarApplicareUpdate(Request $request)
    {
        $bpjs = new BpjsService('aplicares');
        return $bpjs->post('rest/bed/update/' . config('bpjs.ppk_code', env('JKN_PPK_CODE', '')), $request->all());
    }

    public function suratKontrolInsert(Request $request)
    {
        $bpjs = new BpjsService('vclaim');
        return $bpjs->post('RencanaKontrol/insert', $request->all());
    }

    public function suratKontrolCari($nomor)
    {
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('RencanaKontrol/noSuratKontrol/' . $nomor);
    }

    public function suratKontrolByNoka(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $nomor = $request->get('nomor', '');
        $filter = $request->get('filter', '');
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('RencanaKontrol/ListRencanaKontrol/Bulan/' . $bulan . '/Tahun/' . $tahun . '/Nokartu/' . $nomor . '/filter/' . $filter);
    }

    public function prbInsert(Request $request)
    {
        $bpjs = new BpjsService('vclaim');
        return $bpjs->post('PRB/insert', $request->all());
    }

    public function prbCari(Request $request)
    {
        $nomorSrb = $request->get('nomor_srb', '');
        $nomorSep = $request->get('nomor_sep', '');
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('prb/' . $nomorSrb . '/nosep/' . $nomorSep);
    }

    public function prbRekap(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $bpjs = new BpjsService('vclaim');
        return $bpjs->get('prbpotensi/tahun/' . $tahun . '/bulan/' . $bulan);
    }

    public function jadwalHfisPoli()
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('ref/poli');
    }

    public function jadwalHfisDokter()
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->get('ref/dokter');
    }

    public function jadwalHfisUpdate(Request $request)
    {
        $bpjs = new BpjsService('antrean');
        return $bpjs->post('jadwaldokter/updatejadwaldokter', $request->all());
    }

    public function icareFkrtl(Request $request)
    {
        $param = $request->get('param', '');
        $kodedokter = $request->get('kodedokter', '');
        $bpjs = new BpjsService('icare');
        return $bpjs->post('api/rs/validate', [
            'param' => $param,
            'kodedokter' => $kodedokter,
        ]);
    }

    public function icareFktp(Request $request)
    {
        $param = $request->get('param', '');
        $bpjs = new BpjsService('icare');
        return $bpjs->post('api/pcare/validate', ['param' => $param]);
    }
}
