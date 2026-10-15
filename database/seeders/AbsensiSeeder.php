<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->toDateString();

        DB::table('absensi')->insertOrIgnore([
            [
                'karyawan_id'      => 1, // Ahmad Fauzi
                'tanggal'          => $today,
                'jam_masuk'        => '08:00',
                'jam_keluar'       => '17:00',
                'status_kehadiran' => 'Hadir',
                'keterangan'       => 'Hadir tepat waktu',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'karyawan_id'      => 2, // Siti Rahayu
                'tanggal'          => $today,
                'jam_masuk'        => '08:45',
                'jam_keluar'       => null, // Belum clock out
                'status_kehadiran' => 'Terlambat',
                'keterangan'       => 'Macet di jalan',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'karyawan_id'      => 3, // Budi Santoso
                'tanggal'          => $today,
                'jam_masuk'        => '07:55',
                'jam_keluar'       => null, // Belum clock out
                'status_kehadiran' => 'Hadir',
                'keterangan'       => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'karyawan_id'      => 4, // Dewi Lestari
                'tanggal'          => $today,
                'jam_masuk'        => '08:10',
                'jam_keluar'       => '17:15',
                'status_kehadiran' => 'Hadir',
                'keterangan'       => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}