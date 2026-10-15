<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\StrukturController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PortalKaryawanController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\PengaturanLokasiController;

// ── RUTE ROOT (REDIRECT CERDAS) ──
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('karyawan.index')
            : redirect()->route('portal.index');
    }
    return redirect()->route('login');
});

// ── RUTE OTENTIKASI (GUEST) ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── KIOSK ABSENSI OTOMATIS ──
// Halaman ini dibuka langsung pada perangkat kamera/TV tanpa login karyawan.
Route::get('/kiosk', [PortalKaryawanController::class, 'kiosk'])->name('kiosk.index');
Route::post('/kiosk/attendance', [PortalKaryawanController::class, 'kioskAttendance'])->name('kiosk.attendance');
Route::get('/kiosk/recent', [PortalKaryawanController::class, 'kioskRecent'])->name('kiosk.recent');

// ── RUTE TERAUTENTIKASI (AUTH) ──
Route::middleware('auth')->group(function () {

    // ── PORTAL MANDIRI KARYAWAN (ESS) ──
    Route::prefix('portal-karyawan')->name('portal.')->group(function () {
        Route::get('/', [PortalKaryawanController::class, 'index'])->name('index');
        Route::post('/clock-in', [PortalKaryawanController::class, 'clockIn'])->name('clock-in');
        Route::patch('/clock-out', [PortalKaryawanController::class, 'clockOut'])->name('clock-out');
        Route::delete('/reset-hari-ini', [PortalKaryawanController::class, 'resetHariIni'])->name('reset-hari-ini');

        // Face Recognition Routes
        Route::get('/setup-wajah', [PortalKaryawanController::class, 'setupWajah'])->name('face-setup');
        Route::post('/simpan-descriptor', [PortalKaryawanController::class, 'simpanDescriptor'])->name('save-descriptor');

        // Pengajuan Cuti & Izin Sakit Karyawan
        Route::get('/cuti', [PengajuanIzinController::class, 'portalIndex'])->name('cuti.index');
        Route::post('/cuti', [PengajuanIzinController::class, 'portalStore'])->name('cuti.store');
    });

    // ── RUTE ADMINISTRATOR HR (ROLE: ADMIN) ──
    Route::middleware('role:admin')->group(function () {
        Route::get('/karyawan/grafik-analisis', function () {
            return view('karyawan.grafik-analisis');
        })->name('karyawan.grafik');

        Route::resource('karyawan', KaryawanController::class);
        Route::resource('struktur', StrukturController::class);
        Route::resource('jabatan', JabatanController::class);

        // Approval Keterlambatan & Pulang Awal
        Route::patch('absensi/{id}/approve-terlambat', [AbsensiController::class, 'approveKeterlambatan'])->name('absensi.approve-terlambat');
        Route::patch('absensi/{id}/approve-pulang', [AbsensiController::class, 'approvePulangAwal'])->name('absensi.approve-pulang');
        Route::patch('absensi/{id}/clock-out', [AbsensiController::class, 'clockOut'])->name('absensi.clock-out');
        Route::resource('absensi', AbsensiController::class);

        // Manajemen & Approval Cuti / Izin Sakit
        Route::get('cuti-izin', [PengajuanIzinController::class, 'adminIndex'])->name('cuti.index');
        Route::patch('cuti-izin/{id}/approve', [PengajuanIzinController::class, 'approve'])->name('cuti.approve');
        Route::patch('cuti-izin/{id}/reject', [PengajuanIzinController::class, 'reject'])->name('cuti.reject');

        // Pengaturan Lokasi Kantor & Geofencing GPS
        Route::get('pengaturan-lokasi', [PengaturanLokasiController::class, 'index'])->name('pengaturan.lokasi');
        Route::post('pengaturan-lokasi', [PengaturanLokasiController::class, 'update'])->name('pengaturan.lokasi.update');
    });

});
