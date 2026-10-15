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
        Schema::table('absensi', function (Blueprint $table) {
            // Status approval keterlambatan (Masuk)
            $table->string('status_approval_masuk')->default('Tepat Waktu')->after('jam_keluar');
            $table->text('alasan_terlambat')->nullable()->after('status_approval_masuk');
            $table->boolean('kena_denda')->default(false)->after('alasan_terlambat');
            $table->decimal('nominal_denda', 10, 2)->default(0)->after('kena_denda');

            // Status approval pulang awal (Pulang)
            $table->string('status_approval_pulang')->default('Normal')->after('nominal_denda');
            $table->text('alasan_pulang_awal')->nullable()->after('status_approval_pulang');

            // Catatan atau tanggapan dari Admin/HR
            $table->text('catatan_admin')->nullable()->after('alasan_pulang_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn([
                'status_approval_masuk',
                'alasan_terlambat',
                'kena_denda',
                'nominal_denda',
                'status_approval_pulang',
                'alasan_pulang_awal',
                'catatan_admin',
            ]);
        });
    }
};
