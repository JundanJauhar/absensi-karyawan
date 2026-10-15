<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusKaryawan = [
            ['status' => 'Tetap'],
            ['status' => 'Kontrak'],
            ['status' => 'Magang'],
            ['status' => 'Nonaktif'],
        ];

        foreach ($statusKaryawan as $status) {
            \App\Models\StatusKaryawan::create($status);
        }
    }
}
