<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kolom struktur_id sudah didefinisikan langsung di migration create_karyawan_table.
        // Migration ini sengaja dikosongkan agar tidak konflik.
        // Jalankan: php artisan migrate:fresh untuk memulai dari awal.
    }

    public function down(): void
    {
        // Tidak ada yang perlu di-rollback di sini.
    }
};
