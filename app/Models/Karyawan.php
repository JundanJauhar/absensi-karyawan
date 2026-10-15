<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        // Data Pribadi
        'nama',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',

        // Data Pekerjaan
        'jabatan',           // disimpan sebagai string langsung (nama jabatan)
        'gaji',
        // Status & Tipe Ikatan Kerja
        'status',                   // 'Aktif' / 'Tidak Aktif'
        'tipe_kontrak',             // 'PKWTT', 'PKWT', 'Outsourcing'
        'nama_vendor',              // Nama PT jika outsourcing
        'tanggal_mulai_kontrak',
        'tanggal_selesai_kontrak',

        // Relasi Organisasi
        'struktur_id',       // FK integer → tabel struktur

        // Dokumen
        'riwayat_karir',
        'kontrak_kerja',
        'dokumen_legal',

        // Face Recognition
        'face_descriptor',       // JSON array 128-float dari face-api.js
        'face_registered_at',    // Waktu registrasi wajah
    ];

    protected $casts = [
        'tanggal_lahir'           => 'date',
        'tanggal_mulai_kontrak'   => 'date',
        'tanggal_selesai_kontrak' => 'date',
        'face_registered_at'      => 'datetime',
    ];

    /**
     * Hitung sisa hari kontrak kerja (untuk PKWT dan Outsourcing)
     */
    public function getSisaHariKontrakAttribute(): ?int
    {
        if ($this->tipe_kontrak === 'PKWTT' || !$this->tanggal_selesai_kontrak) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->tanggal_selesai_kontrak->startOfDay(), false);
    }

    /**
     * Tampilan badge status kontrak lengkap dengan sisa masa berlaku
     */
    public function getStatusKontrakBadgeAttribute(): string
    {
        if ($this->tipe_kontrak === 'PKWTT') {
            return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-shield-check me-1"></i>PKWTT (Tetap)</span>';
        }

        $sisa = $this->sisa_hari_kontrak;
        $vendorInfo = $this->nama_vendor ? "<div class='text-muted small mt-1'>PT: {$this->nama_vendor}</div>" : "";
        $expInfo = $this->tanggal_selesai_kontrak ? $this->tanggal_selesai_kontrak->format('d M Y') : '-';

        if ($this->tipe_kontrak === 'PKWT') {
            $badgeColor = 'primary';
            $title = 'PKWT (Kontrak)';
        } else {
            $badgeColor = 'warning';
            $title = 'Outsourcing';
        }

        if ($sisa !== null && $sisa < 0) {
            $masaLabel = '<span class="badge bg-danger ms-1">Habis Kontrak</span>';
        } elseif ($sisa !== null && $sisa <= 30) {
            $masaLabel = '<span class="badge bg-danger ms-1">Sisa ' . $sisa . ' hari</span>';
        } elseif ($sisa !== null) {
            $masaLabel = '<span class="badge bg-info ms-1">Sisa ' . $sisa . ' hari</span>';
        } else {
            $masaLabel = '';
        }

        return "<div>
                    <span class='badge bg-{$badgeColor} bg-opacity-10 text-{$badgeColor} border border-{$badgeColor} border-opacity-25'>{$title}</span>
                    {$masaLabel}
                    <div class='text-muted small mt-1' style='font-size:0.75rem'>Hingga: {$expInfo}</div>
                    {$vendorInfo}
                </div>";
    }

    /**
     * Setiap karyawan berada di satu unit struktur organisasi (Department > Divisi > Biro).
     * Relasi: karyawan (N) → struktur (1)
     */
    public function struktur()
    {
        return $this->belongsTo(Struktur::class, 'struktur_id');
    }

    /**
     * Akun user untuk login sistem
     */
    public function user()
    {
        return $this->hasOne(User::class, 'karyawan_id');
    }

    /**
     * Riwayat absensi karyawan
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }

    /**
     * Pengajuan cuti & izin sakit karyawan
     */
    public function pengajuanIzin()
    {
        return $this->hasMany(PengajuanIzin::class, 'karyawan_id');
    }
}
