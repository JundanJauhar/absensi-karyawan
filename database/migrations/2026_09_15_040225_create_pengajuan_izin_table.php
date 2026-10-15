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
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->string('tipe'); // 'Cuti Tahunan', 'Sakit', 'Izin', 'Cuti Khusus', 'Cuti Melahirkan'
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari')->default(1);
            $table->text('alasan');
            $table->string('lampiran')->nullable()->comment('Path file surat dokter atau bukti pendukung');
            $table->string('status')->default('Menunggu Review'); // 'Menunggu Review', 'Disetujui', 'Ditolak'
            $table->text('catatan_admin')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
