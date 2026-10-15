<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    /**
     * Mengisi tabel karyawan dengan data contoh.
     *
     * PENTING: StrukturSeeder harus dijalankan LEBIH DULU sebelum seeder ini,
     * karena struktur_id merujuk ke ID yang sudah ada di tabel struktur.
     * Urutan sudah diatur di DatabaseSeeder.
     */
    public function run(): void
    {
        // Ambil ID struktur yang sudah di-seed untuk referensi FK
        // Struktur ID 1 = TI > Divisi Pengembangan > Biro Backend
        // Struktur ID 4 = SDM > Divisi Rekrutmen > Biro Rekrutmen
        // Struktur ID 7 = Keuangan > Divisi Akuntansi > Biro Pembukuan

        $data = [
            [
                'nama'          => 'Ahmad Fauzi',
                'alamat'        => 'Jl. Mawar No. 10, Jakarta Selatan',
                'tanggal_lahir' => '1990-05-15',
                'jenis_kelamin' => 'Laki-Laki',
                'agama'         => 'Islam',
                'jabatan'       => 'Senior Backend Developer',
                'gaji'          => 12000000,
                'status'        => 'Aktif',
                'struktur_id'   => 1,  // TI > Divisi Pengembangan > Biro Backend
                'riwayat_karir' => 'Bekerja di PT XYZ sebagai developer selama 4 tahun sebelumnya.',
                'kontrak_kerja' => null,
                'dokumen_legal' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama'          => 'Siti Rahayu',
                'alamat'        => 'Jl. Melati No. 25, Bandung',
                'tanggal_lahir' => '1993-08-20',
                'jenis_kelamin' => 'Perempuan',
                'agama'         => 'Islam',
                'jabatan'       => 'HR Specialist',
                'gaji'          => 8500000,
                'status'        => 'Aktif',
                'struktur_id'   => 4,  // SDM > Divisi Rekrutmen > Biro Rekrutmen
                'riwayat_karir' => 'Pengalaman 3 tahun di bidang rekrutmen dan HR generalist.',
                'kontrak_kerja' => null,
                'dokumen_legal' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama'          => 'Budi Santoso',
                'alamat'        => 'Jl. Kenanga No. 5, Surabaya',
                'tanggal_lahir' => '1988-02-10',
                'jenis_kelamin' => 'Laki-Laki',
                'agama'         => 'Kristen',
                'jabatan'       => 'Finance Manager',
                'gaji'          => 15000000,
                'status'        => 'Aktif',
                'struktur_id'   => 7,  // Keuangan > Divisi Akuntansi > Biro Pembukuan
                'riwayat_karir' => 'Mantan auditor di KAP ternama selama 6 tahun.',
                'kontrak_kerja' => null,
                'dokumen_legal' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama'          => 'Dewi Lestari',
                'alamat'        => 'Jl. Anggrek No. 18, Yogyakarta',
                'tanggal_lahir' => '1995-11-03',
                'jenis_kelamin' => 'Perempuan',
                'agama'         => 'Hindu',
                'jabatan'       => 'Frontend Developer',
                'gaji'          => 9000000,
                'status'        => 'Aktif',
                'struktur_id'   => 2,  // TI > Divisi Pengembangan > Biro Frontend
                'riwayat_karir' => null,
                'kontrak_kerja' => null,
                'dokumen_legal' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama'          => 'Rizky Pratama',
                'alamat'        => 'Jl. Dahlia No. 7, Semarang',
                'tanggal_lahir' => '1991-07-22',
                'jenis_kelamin' => 'Laki-Laki',
                'agama'         => 'Islam',
                'jabatan'       => 'Network Engineer',
                'gaji'          => 11000000,
                'status'        => 'Tidak Aktif',
                'struktur_id'   => 3,  // TI > Divisi Infrastruktur > Biro Jaringan & Server
                'riwayat_karir' => 'Pernah mengelola infrastruktur jaringan untuk 3 perusahaan.',
                'kontrak_kerja' => null,
                'dokumen_legal' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama'          => 'Jundan jauhar',
                'alamat'        => 'Jl. Raya No. 1, Jakarta',
                'tanggal_lahir' => '1998-01-01',
                'jenis_kelamin' => 'Laki-Laki',
                'agama'         => 'Islam',
                'jabatan'       => 'Software Engineer',
                'gaji'          => 10000000,
                'status'        => 'Aktif',
                'struktur_id'   => 1,  // TI > Divisi Pengembangan > Biro Backend
                'riwayat_karir' => 'Staff IT & Software Engineer',
                'kontrak_kerja' => null,
                'dokumen_legal' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        // Gunakan DB::table()->insert() agar lebih efisien untuk batch data
        DB::table('karyawan')->insert($data);
    }
}
