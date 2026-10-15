<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusKaryawan extends Model
{
    use HasFactory;

    protected $table = 'status_karyawan';

    protected $fillable = [
        'status',
    ];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'status');
    }
}
