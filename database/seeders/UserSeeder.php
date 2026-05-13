<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Utama', 'email' => 'admin@simrs.khanza', 'password' => Hash::make('password'), 'role' => 'admin', 'nik' => '0000000000000001', 'is_active' => true],
            ['name' => 'Dr. Andi Pratama', 'email' => 'dokter@simrs.khanza', 'password' => Hash::make('password'), 'role' => 'dokter', 'nik' => '0000000000000002', 'spesialisasi' => 'Umum', 'no_str' => 'STR-001', 'is_active' => true],
            ['name' => 'Dr. Siti Rahmawati', 'email' => 'dokter2@simrs.khanza', 'password' => Hash::make('password'), 'role' => 'dokter', 'nik' => '0000000000000003', 'spesialisasi' => 'Anak', 'no_str' => 'STR-002', 'is_active' => true],
            ['name' => 'Suster Dewi', 'email' => 'perawat@simrs.khanza', 'password' => Hash::make('password'), 'role' => 'perawat', 'nik' => '0000000000000004', 'is_active' => true],
            ['name' => 'Kasir Budi', 'email' => 'kasir@simrs.khanza', 'password' => Hash::make('password'), 'role' => 'kasir', 'nik' => '0000000000000005', 'is_active' => true],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
