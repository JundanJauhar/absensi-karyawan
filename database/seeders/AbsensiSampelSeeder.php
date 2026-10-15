<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class AbsensiSampelSeeder extends Seeder
{
    public function run(): void
    {
        $dates = ['2026-09-05', '2026-09-06', '2026-09-07', '2026-09-08', '2026-09-09'];
        $employees = Karyawan::where('id', '!=', 3)->get(); // Kecualikan Budi agar Budi tetap bersih untuk testing hari ini

        foreach ($dates as $d) {
            foreach ($employees as $idx => $emp) {
                // Simulasi acak realistis
                $hash = crc32($d . $emp->id);
                if ($hash % 6 === 0) continue; // Cuti/Izin tidak masuk

                $isLate = ($hash % 4 === 0);
                $jamMasuk = $isLate ? '07:50' : '07:15';
                $jamKeluar = ($hash % 5 === 0) ? '16:00' : '17:00';
                $isPulangAwal = ($jamKeluar === '16:00');

                Absensi::firstOrCreate(
                    [
                        'karyawan_id' => $emp->id,
                        'tanggal'     => $d,
                    ],
                    [
                        'jam_masuk'              => $jamMasuk,
                        'jam_keluar'             => $jamKeluar,
                        'status_kehadiran'       => $isLate ? 'Terlambat' : 'Hadir',
                        'status_approval_masuk'  => $isLate ? ($hash % 2 === 0 ? 'Disetujui Bebas Denda' : 'Ditolak') : 'Tepat Waktu',
                        'status_approval_pulang' => $isPulangAwal ? 'Normal' : 'Normal',
                        'alasan_terlambat'       => $isLate ? 'Terjebak kemacetan di jalan utama' : null,
                        'kena_denda'             => ($isLate && ($hash % 2 !== 0)) ? true : false,
                        'nominal_denda'          => ($isLate && ($hash % 2 !== 0)) ? 50000 : 0,
                    ]
                );
            }
        }
    }
}
