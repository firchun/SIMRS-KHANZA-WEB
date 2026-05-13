<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $polis = [
            ['kode' => 'UMUM', 'nama' => 'Poli Umum', 'biaya' => 50000, 'created_at' => $now],
            ['kode' => 'GIGI', 'nama' => 'Poli Gigi', 'biaya' => 75000, 'created_at' => $now],
            ['kode' => 'ANAK', 'nama' => 'Poli Anak', 'biaya' => 60000, 'created_at' => $now],
            ['kode' => 'KANDUNGAN', 'nama' => 'Poli Kandungan', 'biaya' => 100000, 'created_at' => $now],
            ['kode' => 'BEDAH', 'nama' => 'Poli Bedah', 'biaya' => 125000, 'created_at' => $now],
            ['kode' => 'SARAF', 'nama' => 'Poli Saraf', 'biaya' => 150000, 'created_at' => $now],
            ['kode' => 'JANTUNG', 'nama' => 'Poli Jantung', 'biaya' => 200000, 'created_at' => $now],
            ['kode' => 'MATA', 'nama' => 'Poli Mata', 'biaya' => 80000, 'created_at' => $now],
            ['kode' => 'THT', 'nama' => 'Poli THT', 'biaya' => 85000, 'created_at' => $now],
            ['kode' => 'KULIT', 'nama' => 'Poli Kulit', 'biaya' => 90000, 'created_at' => $now],
        ];

        $bangsalData = [
            ['nama' => 'Flamboyan', 'kelas' => '1', 'kapasitas' => 10],
            ['nama' => 'Flamboyan', 'kelas' => '2', 'kapasitas' => 15],
            ['nama' => 'Anggrek', 'kelas' => '3', 'kapasitas' => 20],
            ['nama' => 'Melati', 'kelas' => 'VIP', 'kapasitas' => 5],
            ['nama' => 'Mawar', 'kelas' => 'VVIP', 'kapasitas' => 3],
            ['nama' => 'Dahlia', 'kelas' => '1', 'kapasitas' => 8],
            ['nama' => 'Kamboja', 'kelas' => '2', 'kapasitas' => 12],
        ];

        $tindakanData = [
            ['kode' => 'TD-001', 'nama' => 'Pemeriksaan Dokter Umum', 'biaya' => 50000, 'kategori' => 'ralan'],
            ['kode' => 'TD-002', 'nama' => 'Pemeriksaan Dokter Spesialis', 'biaya' => 100000, 'kategori' => 'ralan'],
            ['kode' => 'TD-003', 'nama' => 'EKG', 'biaya' => 75000, 'kategori' => 'ralan'],
            ['kode' => 'TD-004', 'nama' => 'USG', 'biaya' => 150000, 'kategori' => 'ralan'],
            ['kode' => 'TD-005', 'nama' => 'Pasang Infus', 'biaya' => 35000, 'kategori' => 'igd'],
            ['kode' => 'TD-006', 'nama' => 'Jahit Luka', 'biaya' => 100000, 'kategori' => 'igd'],
            ['kode' => 'TD-007', 'nama' => 'Resusitasi', 'biaya' => 200000, 'kategori' => 'igd'],
            ['kode' => 'TD-008', 'nama' => 'Observasi Rawat Inap/hari', 'biaya' => 150000, 'kategori' => 'ranap'],
            ['kode' => 'TD-009', 'nama' => 'Visite Dokter', 'biaya' => 75000, 'kategori' => 'ranap'],
            ['kode' => 'TD-010', 'nama' => 'Perawatan Luka', 'biaya' => 50000, 'kategori' => 'ranap'],
        ];

        foreach ($polis as $poli) {
            DB::table('master_poli')->insertOrIgnore($poli);
        }

        foreach ($bangsalData as $b) {
            for ($i = 1; $i <= $b['kapasitas']; $i++) {
                DB::table('master_kamar')->insertOrIgnore([
                    'bangsal' => $b['nama'],
                    'kelas' => $b['kelas'],
                    'no_kamar' => $b['nama'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status' => 'tersedia',
                    'created_at' => $now,
                ]);
            }
        }

        foreach ($tindakanData as $t) {
            DB::table('master_tindakan')->insertOrIgnore($t + ['created_at' => $now]);
        }
    }
}
