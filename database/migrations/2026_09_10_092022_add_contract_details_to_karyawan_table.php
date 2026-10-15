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
        Schema::table('karyawan', function (Blueprint $table) {
            // Tipe Ikatan Kerja: PKWTT (Tetap), PKWT (Kontrak), Outsourcing
            $table->enum('tipe_kontrak', ['PKWTT', 'PKWT', 'Outsourcing'])->default('PKWTT')->after('status');

            // Nama PT Penyedia jika karyawan Outsourcing
            $table->string('nama_vendor')->nullable()->after('tipe_kontrak');

            // Masa Kontrak (untuk PKWT dan Outsourcing)
            $table->date('tanggal_mulai_kontrak')->nullable()->after('nama_vendor');
            $table->date('tanggal_selesai_kontrak')->nullable()->after('tanggal_mulai_kontrak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_kontrak',
                'nama_vendor',
                'tanggal_mulai_kontrak',
                'tanggal_selesai_kontrak',
            ]);
        });
    }
};
