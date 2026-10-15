<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Struktur extends Model
{
    use HasFactory;

    protected $table = 'struktur';

    protected $fillable = [
        'nama_department',   // Level 1: Departemen
        'nama_divisi',       // Level 2: Divisi
        'nama_biro',         // Level 3: Biro (opsional)
        'deskripsi',
    ];

    /**
     * Satu unit struktur bisa memiliki banyak karyawan.
     * Relasi: struktur (1) → karyawan (N)
     */
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'struktur_id');
    }
}
