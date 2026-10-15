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
            // Foto Absensi (Wajah/Selfie)
            $table->string('foto_masuk')->nullable()->after('jam_masuk');
            $table->string('foto_keluar')->nullable()->after('jam_keluar');

            // Koordinat Geofencing GPS & Jarak ke Kantor
            $table->decimal('lat_masuk', 10, 7)->nullable()->after('foto_masuk');
            $table->decimal('long_masuk', 10, 7)->nullable()->after('lat_masuk');
            $table->integer('jarak_masuk')->nullable()->after('long_masuk')->comment('Jarak ke kantor dalam meter saat Clock In');

            $table->decimal('lat_keluar', 10, 7)->nullable()->after('foto_keluar');
            $table->decimal('long_keluar', 10, 7)->nullable()->after('lat_keluar');
            $table->integer('jarak_keluar')->nullable()->after('long_keluar')->comment('Jarak ke kantor dalam meter saat Clock Out');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn([
                'foto_masuk', 'foto_keluar',
                'lat_masuk', 'long_masuk', 'jarak_masuk',
                'lat_keluar', 'long_keluar', 'jarak_keluar',
            ]);
        });
    }
};
