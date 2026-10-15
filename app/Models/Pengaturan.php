<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'deskripsi',
    ];

    /**
     * Ambil nilai pengaturan berdasarkan key dengan default value
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set atau update nilai pengaturan
     */
    public static function set($key, $value, $deskripsi = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'deskripsi' => $deskripsi ?? static::where('key', $key)->value('deskripsi')
            ]
        );
    }
}
