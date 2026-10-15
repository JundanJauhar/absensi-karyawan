<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrukturSeeder extends Seeder
{
    /**
     * Mengisi tabel struktur dengan data hierarki organisasi perusahaan.
     *
     * Hierarki: Department (Level 1) > Divisi (Level 2) > Biro (Level 3)
     *
     * Setiap row = satu unit/posisi spesifik di organisasi.
     * Karyawan nanti akan memilih salah satu row ini via struktur_id.
     */
    public function run(): void
    {
        DB::table('struktur')->insert([

            // ── Departemen Teknologi Informasi ──────────────────────────────
            [
                'nama_department' => 'Teknologi Informasi',
                'nama_divisi'     => 'Divisi Pengembangan',
                'nama_biro'       => 'Biro Backend',
                'deskripsi'       => 'Pengembangan sistem dan API backend perusahaan.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_department' => 'Teknologi Informasi',
                'nama_divisi'     => 'Divisi Pengembangan',
                'nama_biro'       => 'Biro Frontend',
                'deskripsi'       => 'Pengembangan antarmuka pengguna dan aplikasi web.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_department' => 'Teknologi Informasi',
                'nama_divisi'     => 'Divisi Infrastruktur',
                'nama_biro'       => 'Biro Jaringan & Server',
                'deskripsi'       => 'Pengelolaan server, jaringan, dan keamanan IT.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            // ── Departemen Sumber Daya Manusia ───────────────────────────────
            [
                'nama_department' => 'Sumber Daya Manusia',
                'nama_divisi'     => 'Divisi Rekrutmen & Pengembangan',
                'nama_biro'       => 'Biro Rekrutmen',
                'deskripsi'       => 'Proses seleksi dan penerimaan karyawan baru.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_department' => 'Sumber Daya Manusia',
                'nama_divisi'     => 'Divisi Rekrutmen & Pengembangan',
                'nama_biro'       => 'Biro Pelatihan & Pengembangan',
                'deskripsi'       => 'Program pelatihan dan pengembangan kompetensi karyawan.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_department' => 'Sumber Daya Manusia',
                'nama_divisi'     => 'Divisi Administrasi SDM',
                'nama_biro'       => 'Biro Penggajian & Benefit',
                'deskripsi'       => 'Pengelolaan penggajian, tunjangan, dan benefit karyawan.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            // ── Departemen Keuangan ──────────────────────────────────────────
            [
                'nama_department' => 'Keuangan',
                'nama_divisi'     => 'Divisi Akuntansi',
                'nama_biro'       => 'Biro Pembukuan',
                'deskripsi'       => 'Pencatatan dan pelaporan keuangan perusahaan.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_department' => 'Keuangan',
                'nama_divisi'     => 'Divisi Keuangan Operasional',
                'nama_biro'       => 'Biro Anggaran & Kontrol',
                'deskripsi'       => 'Penyusunan anggaran dan kontrol pengeluaran operasional.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            // ── Departemen Operasional ───────────────────────────────────────
            [
                'nama_department' => 'Operasional',
                'nama_divisi'     => 'Divisi Logistik',
                'nama_biro'       => 'Biro Pengadaan',
                'deskripsi'       => 'Pengadaan barang dan jasa kebutuhan perusahaan.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nama_department' => 'Operasional',
                'nama_divisi'     => 'Divisi Logistik',
                'nama_biro'       => 'Biro Distribusi',
                'deskripsi'       => 'Distribusi dan pengiriman produk/layanan.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

        ]);
    }
}
