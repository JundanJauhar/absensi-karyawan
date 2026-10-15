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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            // FK ke tabel karyawan — setiap record absensi milik 1 karyawan
            // cascadeOnDelete: jika karyawan dihapus, semua absensinya ikut terhapus otomatis
            $table->foreignId('karyawan_id')
                  ->constrained('karyawan')
                  ->cascadeOnDelete();

            $table->date('tanggal');                      // tanggal absensi (mis: 2026-09-10)
            $table->time('jam_masuk')->nullable();         // jam scan masuk (nullable: jika tidak hadir)
            $table->time('jam_keluar')->nullable();        // jam scan keluar (nullable)

            // Status kehadiran hari itu
            $table->enum('status_kehadiran', [
                'Hadir',       // Datang normal
                'Terlambat',   // Datang tapi melebihi jam masuk
                'Izin',        // Izin dengan surat/persetujuan
                'Sakit',       // Sakit dengan keterangan dokter
                'Alpha',       // Tidak hadir tanpa keterangan
                'Lembur',      // Hadir di luar jam kerja normal
            ])->default('Hadir');

            $table->text('keterangan')->nullable();        // catatan tambahan (alasan izin, dll)
            $table->timestamps();

            // Index gabungan: satu karyawan hanya bisa punya 1 record per tanggal
            $table->unique(['karyawan_id', 'tanggal'], 'absensi_karyawan_tanggal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
