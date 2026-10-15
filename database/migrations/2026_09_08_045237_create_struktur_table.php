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
        Schema::create('struktur', function (Blueprint $table) {
            $table->id();

            // Hierarki organisasi: Department > Divisi > Biro
            // Contoh: "Teknologi Informasi" > "Divisi Backend" > "Biro API Development"
            $table->string('nama_department');            // Level 1: Departemen (wajib)
            $table->string('nama_divisi');                // Level 2: Divisi (wajib)
            $table->string('nama_biro')->nullable();      // Level 3: Biro (opsional, unit terkecil)

            $table->text('deskripsi')->nullable();        // Keterangan tambahan tentang unit ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struktur');
    }
};
