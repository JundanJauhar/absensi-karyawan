<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Jabatan::create([
            'nama_jabatan' => 'Kepala Bagian',
            'deskripsi' => 'Kepala Bagian bertanggung jawab atas pengelolaan dan koordinasi seluruh kegiatan di dalam bagian tersebut.',
        ]);

        \App\Models\Jabatan::create([
            'nama_jabatan' => 'Wakil Kepala Bagian',
            'deskripsi' => 'Wakil Kepala Bagian membantu Kepala Bagian dalam mengelola dan mengkoordinasikan kegiatan di dalam bagian tersebut.',
        ]);

        \App\Models\Jabatan::create([
            'nama_jabatan' => 'Anggota',
            'deskripsi' => 'Anggota bertanggung jawab atas pelaksanaan tugas-tugas yang diberikan oleh Kepala Bagian atau Wakil Kepala Bagian.',
        ]);
    }
}
