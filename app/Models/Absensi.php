<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    // Konstanta Aturan Jam Kerja Perusahaan
    const JAM_MASUK_MAKSIMAL = '07:30';
    const JAM_PULANG_MINIMAL = '13:00';
    const JAM_PULANG_NORMAL  = '16:30';
    const NOMINAL_DENDA_DEFAULT = 50000;

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran',
        'keterangan',
        'sumber_absensi',

        // Foto Absensi
        'foto_masuk',
        'foto_keluar',

        // Koordinat Geofencing GPS & Jarak Meter
        'lat_masuk',
        'long_masuk',
        'jarak_masuk',
        'lat_keluar',
        'long_keluar',
        'jarak_keluar',

        // Aturan Keterlambatan & Approval Masuk
        'status_approval_masuk', // 'Tepat Waktu', 'Menunggu Review', 'Diizinkan', 'Ditolak'
        'alasan_terlambat',
        'kena_denda',
        'nominal_denda',

        // Aturan Pulang & Approval Pulang
        'status_approval_pulang', // 'Normal', 'Menunggu Review', 'Diizinkan', 'Ditolak'
        'alasan_pulang_awal',

        // Feedback Admin
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'kena_denda'    => 'boolean',
        'nominal_denda' => 'decimal:2',
        'lat_masuk'     => 'float',
        'long_masuk'    => 'float',
        'jarak_masuk'   => 'integer',
        'lat_keluar'    => 'float',
        'long_keluar'   => 'float',
        'jarak_keluar'  => 'integer',
    ];

    public function getFotoMasukUrlAttribute(): ?string
    {
        if ($this->foto_masuk && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_masuk)) {
            return asset('storage/' . $this->foto_masuk);
        }
        return null;
    }

    public function getFotoKeluarUrlAttribute(): ?string
    {
        if ($this->foto_keluar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_keluar)) {
            return asset('storage/' . $this->foto_keluar);
        }
        return null;
    }

    /**
     * Setiap record absensi dimiliki oleh satu karyawan.
     * Relasi: absensi (N) → karyawan (1)
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Hitung durasi kerja (jam_masuk - jam_keluar) dalam format jam dan menit.
     * Mengembalikan string seperti "7 jam 30 menit" atau null jika belum keluar.
     */
    public function getDurasiKerjaAttribute(): ?string
    {
        if (!$this->jam_masuk || !$this->jam_keluar) {
            return null;
        }

        $masuk  = Carbon::createFromTimeString($this->jam_masuk);
        $keluar = Carbon::createFromTimeString($this->jam_keluar);

        if ($keluar->lt($masuk)) {
            return null; // data tidak valid
        }

        $totalMenit = $masuk->diffInMinutes($keluar);
        $jam        = intdiv($totalMenit, 60);
        $menit      = $totalMenit % 60;

        return "{$jam} jam {$menit} menit";
    }

    /**
     * Warna badge berdasarkan status kehadiran untuk tampilan view.
     */
    public function getBadgeColorAttribute(): string
    {
        return match($this->status_kehadiran) {
            'Hadir'     => 'success',
            'Terlambat' => 'warning',
            'Izin'      => 'info',
            'Sakit'     => 'primary',
            'Alpha'     => 'danger',
            'Lembur'    => 'secondary',
            default     => 'secondary',
        };
    }

    /**
     * Badge visual untuk status persetujuan keterlambatan
     */
    public function getStatusApprovalMasukBadgeAttribute(): string
    {
        return match($this->status_approval_masuk) {
            'Tepat Waktu'     => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Tepat Waktu</span>',
            'Menunggu Review' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="bi bi-clock-history me-1"></i>Menunggu Review</span>',
            'Diizinkan'       => '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="bi bi-check2-circle me-1"></i>Izin Diterima (Bebas Denda)</span>',
            'Ditolak'         => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-x-circle me-1"></i>Terlambat (Denda: Rp ' . number_format($this->nominal_denda, 0, ',', '.') . ')</span>',
            default           => '<span class="badge bg-secondary">' . ($this->status_approval_masuk ?? '-') . '</span>',
        };
    }

    /**
     * Badge visual untuk status persetujuan pulang awal
     */
    public function getStatusApprovalPulangBadgeAttribute(): string
    {
        return match($this->status_approval_pulang) {
            'Normal'          => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Normal (≥ 16:30)</span>',
            'Menunggu Review' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="bi bi-clock-history me-1"></i>Pulang Awal (Menunggu ACC)</span>',
            'Diizinkan'       => '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="bi bi-check2-circle me-1"></i>Pulang Awal Di-ACC</span>',
            'Ditolak'         => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="bi bi-x-circle me-1"></i>Pulang Awal Ditolak</span>',
            default           => '<span class="badge bg-secondary">' . ($this->status_approval_pulang ?? '-') . '</span>',
        };
    }
}
