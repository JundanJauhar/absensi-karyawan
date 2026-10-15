<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GrafanaDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tambah Data Karyawan Dummy Beragam
        $karyawanData = [
            [
                'nama' => 'Rian Pratama',
                'alamat' => 'Jl. Merdeka No. 12, Jakarta',
                'tanggal_lahir' => '1995-04-12',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Islam',
                'jabatan' => 'Senior Backend Engineer',
                'gaji' => 14000000,
                'status' => 'Aktif',
                'struktur_id' => 1,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2023-01-10',
                'tanggal_selesai_kontrak' => null,
            ],
            [
                'nama' => 'Dewi Lestari',
                'alamat' => 'Jl. Sudirman No. 45, Jakarta',
                'tanggal_lahir' => '1998-08-23',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'Frontend UI/UX Specialist',
                'gaji' => 11000000,
                'status' => 'Aktif',
                'struktur_id' => 2,
                'tipe_kontrak' => 'PKWT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2025-09-20',
                'tanggal_selesai_kontrak' => '2026-09-20', // Habis dlm 9 hari!
            ],
            [
                'nama' => 'Hendra Gunawan',
                'alamat' => 'Jl. Thamrin No. 8, Tangerang',
                'tanggal_lahir' => '1992-11-05',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Kristen',
                'jabatan' => 'DevOps & Cloud Engineer',
                'gaji' => 15500000,
                'status' => 'Aktif',
                'struktur_id' => 3,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2022-03-01',
                'tanggal_selesai_kontrak' => null,
            ],
            [
                'nama' => 'Maya Indah Puspita',
                'alamat' => 'Jl. Kebon Jeruk No. 19, Jakarta',
                'tanggal_lahir' => '1997-02-14',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'Talent Acquisition Specialist',
                'gaji' => 9500000,
                'status' => 'Aktif',
                'struktur_id' => 4,
                'tipe_kontrak' => 'PKWT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2025-10-01',
                'tanggal_selesai_kontrak' => '2026-10-01', // Habis dlm 20 hari
            ],
            [
                'nama' => 'Bayu Anggoro',
                'alamat' => 'Jl. Gatot Subroto No. 88, Bekasi',
                'tanggal_lahir' => '1994-06-30',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Islam',
                'jabatan' => 'Training & Development Lead',
                'gaji' => 10500000,
                'status' => 'Aktif',
                'struktur_id' => 5,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2023-06-15',
                'tanggal_selesai_kontrak' => null,
            ],
            [
                'nama' => 'Nadia Safira',
                'alamat' => 'Jl. Tebet Raya No. 3, Jakarta',
                'tanggal_lahir' => '1999-01-19',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'Payroll & Compensation Officer',
                'gaji' => 8500000,
                'status' => 'Aktif',
                'struktur_id' => 6,
                'tipe_kontrak' => 'PKWT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2025-09-18',
                'tanggal_selesai_kontrak' => '2026-09-18', // Habis dlm 7 hari!
            ],
            [
                'nama' => 'Dimas Setiawan',
                'alamat' => 'Jl. Kemang Timur No. 25, Jakarta',
                'tanggal_lahir' => '1993-09-10',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Katolik',
                'jabatan' => 'Senior Financial Accountant',
                'gaji' => 13000000,
                'status' => 'Aktif',
                'struktur_id' => 7,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2021-08-01',
                'tanggal_selesai_kontrak' => null,
            ],
            [
                'nama' => 'Anisa Rahmawati',
                'alamat' => 'Jl. Rasuna Said No. 7, Jakarta',
                'tanggal_lahir' => '1996-05-17',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'Budgeting & Controller',
                'gaji' => 11500000,
                'status' => 'Aktif',
                'struktur_id' => 8,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2022-11-15',
                'tanggal_selesai_kontrak' => null,
            ],
            [
                'nama' => 'Fajar Nugraha',
                'alamat' => 'Jl. Kalimalang No. 102, Bekasi',
                'tanggal_lahir' => '1995-12-03',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Islam',
                'jabatan' => 'Procurement Specialist',
                'gaji' => 9000000,
                'status' => 'Aktif',
                'struktur_id' => 9,
                'tipe_kontrak' => 'Outsourcing',
                'nama_vendor' => 'PT Sumber Daya Prima',
                'tanggal_mulai_kontrak' => '2026-01-01',
                'tanggal_selesai_kontrak' => '2026-12-31',
            ],
            [
                'nama' => 'Rizky Ramadhan',
                'alamat' => 'Jl. Daan Mogot No. 64, Tangerang',
                'tanggal_lahir' => '1998-03-28',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Islam',
                'jabatan' => 'Warehouse & Distribution Supervisor',
                'gaji' => 8800000,
                'status' => 'Aktif',
                'struktur_id' => 10,
                'tipe_kontrak' => 'PKWT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2026-03-15',
                'tanggal_selesai_kontrak' => '2027-03-15',
            ],
            [
                'nama' => 'Gita Gutawa Putri',
                'alamat' => 'Jl. Fatmawati No. 40, Jakarta',
                'tanggal_lahir' => '1997-07-22',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'IT QA Automation Tester',
                'gaji' => 10000000,
                'status' => 'Aktif',
                'struktur_id' => 1,
                'tipe_kontrak' => 'Outsourcing',
                'nama_vendor' => 'PT Solusi Mitra Mandiri',
                'tanggal_mulai_kontrak' => '2026-02-01',
                'tanggal_selesai_kontrak' => '2026-11-30',
            ],
            [
                'nama' => 'Kevin Sanjaya',
                'alamat' => 'Jl. Senayan No. 11, Jakarta',
                'tanggal_lahir' => '1996-08-02',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Kristen',
                'jabatan' => 'Frontend React Developer',
                'gaji' => 12000000,
                'status' => 'Aktif',
                'struktur_id' => 2,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2023-04-01',
                'tanggal_selesai_kontrak' => null,
            ],
            [
                'nama' => 'Tiara Andini',
                'alamat' => 'Jl. Cilandak No. 18, Jakarta',
                'tanggal_lahir' => '2000-09-15',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'Network & Security Admin',
                'gaji' => 9500000,
                'status' => 'Aktif',
                'struktur_id' => 3,
                'tipe_kontrak' => 'PKWT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2025-09-25',
                'tanggal_selesai_kontrak' => '2026-09-25', // Habis dlm 14 hari!
            ],
            [
                'nama' => 'Eko Prasetyo',
                'alamat' => 'Jl. Warung Buncit No. 71, Jakarta',
                'tanggal_lahir' => '1991-04-18',
                'jenis_kelamin' => 'Laki-Laki',
                'agama' => 'Islam',
                'jabatan' => 'Logistics Fleet Coordinator',
                'gaji' => 8000000,
                'status' => 'Aktif',
                'struktur_id' => 10,
                'tipe_kontrak' => 'Outsourcing',
                'nama_vendor' => 'PT Garda Utama Perkasa',
                'tanggal_mulai_kontrak' => '2026-01-15',
                'tanggal_selesai_kontrak' => '2026-10-15',
            ],
            [
                'nama' => 'Tari Wulandari',
                'alamat' => 'Jl. Radio Dalam No. 33, Jakarta',
                'tanggal_lahir' => '1996-10-10',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'jabatan' => 'Tax Compliance Officer',
                'gaji' => 10500000,
                'status' => 'Aktif',
                'struktur_id' => 7,
                'tipe_kontrak' => 'PKWTT',
                'nama_vendor' => null,
                'tanggal_mulai_kontrak' => '2022-09-01',
                'tanggal_selesai_kontrak' => null,
            ]
        ];

        foreach ($karyawanData as $k) {
            Karyawan::firstOrCreate(['nama' => $k['nama']], $k);
        }

        // 2. Buat Riwayat Absensi Selama 2 Minggu Terakhir (31 Ags 2026 - 11 Sep 2026 HARI INI)
        $semuaKaryawan = Karyawan::all();
        $tanggalList = [
            '2026-08-31', '2026-09-01', '2026-09-02', '2026-09-03', '2026-09-04',
            '2026-09-07', '2026-09-08', '2026-09-09', '2026-09-10', '2026-09-11' // HARI INI
        ];

        $alasanTerlambatList = [
            'Macet parah akibat kecelakaan di tol lingkar luar',
            'Kereta commuter line mengalami kendala teknis sinyal',
            'Hujan deras dan genangan banjir di jalur arteri',
            'Kendaraan mogok dan harus mencari bengkel terdekat',
            'Antrian perbaikan jalan tol Cikampek lebih dari 5 km'
        ];

        $insertedCount = 0;

        foreach ($tanggalList as $tgl) {
            $isToday = ($tgl === '2026-09-11');

            foreach ($semuaKaryawan as $emp) {
                // Khusus hari ini, biarkan Budi Santoso (id: 3) tetap belum absen agar user bisa coba absen mandiri
                if ($isToday && $emp->id == 3) {
                    continue;
                }

                // 90% hadir setiap harinya
                $seed = crc32($tgl . $emp->id);
                if (!$isToday && ($seed % 10 == 0)) {
                    continue; // Simulasi cuti / absen
                }

                // Peluang terlambat sekitar 25%
                $isLate = ($seed % 4 == 0);
                $jamMasuk = $isLate 
                    ? sprintf('07:%02d', 35 + ($seed % 25)) // 07:35 s.d 07:59
                    : sprintf('07:%02d', 5 + ($seed % 24));  // 07:05 s.d 07:29

                // Jam keluar: jika hari ini dan waktu masih pagi, jam keluar bisa null atau sore
                $jamKeluar = $isToday ? null : sprintf('17:%02d', ($seed % 30));

                $approvalStatus = 'Tepat Waktu';
                $kenaDenda = false;
                $nominalDenda = 0;
                $alasan = null;

                if ($isLate) {
                    $alasan = $alasanTerlambatList[$seed % count($alasanTerlambatList)];
                    $pilihanApproval = $seed % 3;
                    if ($pilihanApproval == 0) {
                        $approvalStatus = 'Disetujui Bebas Denda';
                        $kenaDenda = false;
                        $nominalDenda = 0;
                    } elseif ($pilihanApproval == 1) {
                        $approvalStatus = 'Ditolak';
                        $kenaDenda = true;
                        $nominalDenda = 50000;
                    } else {
                        $approvalStatus = 'Menunggu Review';
                        $kenaDenda = false;
                        $nominalDenda = 50000;
                    }
                }

                // Bersihkan record lama pada tanggal ini jika ada
                DB::table('absensi')
                    ->where('karyawan_id', $emp->id)
                    ->where(function ($q) use ($tgl) {
                        $q->where('tanggal', $tgl)
                          ->orWhere('tanggal', 'like', "$tgl%");
                    })
                    ->delete();

                DB::table('absensi')->insert([
                    'karyawan_id' => $emp->id,
                    'tanggal' => $tgl,
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                    'status_kehadiran' => $isLate ? 'Terlambat' : 'Hadir',
                    'status_approval_masuk' => $approvalStatus,
                    'status_approval_pulang' => 'Normal',
                    'alasan_terlambat' => $alasan,
                    'kena_denda' => $kenaDenda ? 1 : 0,
                    'nominal_denda' => $nominalDenda,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $insertedCount++;
            }
        }

        echo "Berhasil membuat data dummy! Total Karyawan: " . Karyawan::count() . ", Total Absensi: " . Absensi::count() . PHP_EOL;
    }
}
