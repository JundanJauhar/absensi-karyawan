<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan semua seeder dalam urutan yang benar.
     *
     * URUTAN PENTING karena ada relasi FK antar tabel:
     * 1. StrukturSeeder  → harus lebih dulu (karyawan butuh struktur_id)
     * 2. KaryawanSeeder  → butuh struktur_id yang sudah ada
     */
    public function run(): void
    {
        $this->call([
            StrukturSeeder::class,   // ① Isi tabel struktur DULU (parent)
            KaryawanSeeder::class,   // ② Baru isi karyawan (butuh struktur_id)
        ]);

        // Akun Admin HR (Full Access)
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'        => 'admin',
                'password'    => bcrypt('admin'),
                'role'        => 'admin',
                'karyawan_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name'        => 'Administrator HR',
                'password'    => bcrypt('admin'),
                'role'        => 'admin',
                'karyawan_id' => null,
            ]
        );

        // Akun Karyawan 1 (Ahmad Fauzi - TI Backend Developer)
        User::updateOrCreate(
            ['email' => 'ahmad@company.com'],
            [
                'name'        => 'Ahmad Fauzi',
                'password'    => bcrypt('password'),
                'role'        => 'karyawan',
                'karyawan_id' => 1,
            ]
        );

        // Akun Karyawan 2 (Siti Rahayu - HR Specialist)
        User::updateOrCreate(
            ['email' => 'siti@company.com'],
            [
                'name'        => 'Siti Rahayu',
                'password'    => bcrypt('password'),
                'role'        => 'karyawan',
                'karyawan_id' => 2,
            ]
        );

        // Akun Karyawan Jundan jauhar (Software Engineer)
        $karyawanJundan = \App\Models\Karyawan::where('nama', 'Jundan jauhar')->first();
        User::updateOrCreate(
            ['email' => 'jundan87@gmail.com'],
            [
                'name'        => 'Jundan jauhar',
                'password'    => bcrypt('password'),
                'role'        => 'karyawan',
                'karyawan_id' => $karyawanJundan?->id ?? 7,
            ]
        );

        $this->call([
            AbsensiSeeder::class,    // ③ Data contoh absensi
        ]);
    }
}
