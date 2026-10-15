<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            // Menyimpan face descriptor sebagai JSON array (128 float values dari face-api.js)
            $table->text('face_descriptor')->nullable()->after('dokumen_legal')
                  ->comment('Array 128 float - face recognition descriptor dari face-api.js');
            $table->timestamp('face_registered_at')->nullable()->after('face_descriptor')
                  ->comment('Waktu pendaftaran wajah pertama kali');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn(['face_descriptor', 'face_registered_at']);
        });
    }
};
