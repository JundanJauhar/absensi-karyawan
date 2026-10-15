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
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });

        // Seed nilai default lokasi kantor dari Google Earth user
        $defaults = [
            ['key' => 'kantor_nama',         'value' => 'Kantor Enterprise HR', 'deskripsi' => 'Nama lokasi kantor'],
            ['key' => 'kantor_lat',          'value' => '-7.711583',            'deskripsi' => 'Latitude koordinat pusat kantor'],
            ['key' => 'kantor_long',         'value' => '110.395792',           'deskripsi' => 'Longitude koordinat pusat kantor'],
            ['key' => 'kantor_radius_meter', 'value' => '40',                   'deskripsi' => 'Radius toleransi absensi dalam meter'],
            ['key' => 'geofencing_aktif',    'value' => '1',                    'deskripsi' => 'Status aktif geofencing (1 = Aktif, 0 = Nonaktif)'],
        ];

        foreach ($defaults as $d) {
            \Illuminate\Support\Facades\DB::table('pengaturan')->insert(array_merge($d, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
