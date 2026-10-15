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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();

            // === Data Pribadi ===
            $table->string('nama');
            $table->string('alamat');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']);

            // === Data Pekerjaan ===
            $table->string('jabatan');
            $table->decimal('gaji', 15, 2);                          // Presisi lebih besar untuk gaji besar
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');

            // === Struktur Organisasi ===
            // FK ke tabel struktur — menentukan karyawan ini ada di Department > Divisi > Biro mana
            $table->foreignId('struktur_id')
                  ->nullable()
                  ->constrained('struktur')   // merujuk ke tabel 'struktur'
                  ->nullOnDelete();            // jika unit dihapus, struktur_id di-set NULL (bukan error)

            // === Dokumen (disimpan path file, bukan file langsung) ===
            $table->text('riwayat_karir')->nullable();
            $table->string('kontrak_kerja')->nullable();              // path file kontrak
            $table->string('dokumen_legal')->nullable();              // path file dokumen legal

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
