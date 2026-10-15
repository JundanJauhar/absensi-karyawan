<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'karyawan_id',
        'tipe',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'lampiran',
        'status',
        'catatan_admin',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'approved_at'     => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getLampiranUrlAttribute()
    {
        if ($this->lampiran && Storage::disk('public')->exists($this->lampiran)) {
            return asset('storage/' . $this->lampiran);
        }
        return null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Disetujui'       => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>',
            'Ditolak'         => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>',
            default           => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Review</span>',
        };
    }

    public function getTipeBadgeAttribute(): string
    {
        return match ($this->tipe) {
            'Sakit'           => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-heart-pulse-fill me-1"></i>Sakit</span>',
            'Cuti Tahunan'    => '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><i class="bi bi-calendar2-week me-1"></i>Cuti Tahunan</span>',
            'Cuti Melahirkan' => '<span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25"><i class="bi bi-person-hearts me-1"></i>Cuti Melahirkan</span>',
            default           => '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="bi bi-file-earmark-text me-1"></i>Izin</span>',
        };
    }
}
