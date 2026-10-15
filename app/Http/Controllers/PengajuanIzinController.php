<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;

class PengajuanIzinController extends Controller
{
    /**
     * ── KARYAWAN: Tampilkan daftar pengajuan cuti & izin mandiri ──
     */
    public function portalIndex(Request $request)
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->route('portal.index')->with('error', 'Data karyawan tidak terhubung.');
        }

        $query = PengajuanIzin::where('karyawan_id', $karyawan->id)
                              ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->paginate(10)->withQueryString();

        // Ringkasan kuota / statistik pengajuan tahun ini
        $tahunIni = Carbon::now()->year;
        $stats = [
            'total_pengajuan' => PengajuanIzin::where('karyawan_id', $karyawan->id)->whereYear('created_at', $tahunIni)->count(),
            'disetujui'       => PengajuanIzin::where('karyawan_id', $karyawan->id)->whereYear('created_at', $tahunIni)->where('status', 'Disetujui')->count(),
            'menunggu'        => PengajuanIzin::where('karyawan_id', $karyawan->id)->where('status', 'Menunggu Review')->count(),
            'ditolak'         => PengajuanIzin::where('karyawan_id', $karyawan->id)->whereYear('created_at', $tahunIni)->where('status', 'Ditolak')->count(),
        ];

        return view('portal.cuti.index', compact('user', 'karyawan', 'pengajuan', 'stats'));
    }

    /**
     * ── KARYAWAN: Simpan pengajuan cuti / izin baru ──
     */
    public function portalStore(Request $request)
    {
        $karyawan = auth()->user()->karyawan;

        if (!$karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $request->validate([
            'tipe'            => 'required|in:Cuti Tahunan,Sakit,Izin,Cuti Khusus,Cuti Melahirkan',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|min:5|max:1000',
            'lampiran'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'tipe.required'            => 'Pilih jenis pengajuan (Cuti / Sakit / Izin).',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh tanggal di masa lalu.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'alasan.required'          => 'Alasan atau keterangan izin wajib diisi.',
            'alasan.min'               => 'Alasan minimal 5 karakter.',
            'lampiran.mimes'           => 'Format lampiran harus berupa JPG, PNG, atau PDF.',
            'lampiran.max'             => 'Ukuran file lampiran maksimal 5 MB.',
        ]);

        // Hitung jumlah hari kerja (inklusif)
        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $period = CarbonPeriod::create($mulai, $selesai);
        
        $jumlahHari = 0;
        foreach ($period as $date) {
            if (!$date->isWeekend()) {
                $jumlahHari++;
            }
        }

        if ($jumlahHari < 1) {
            $jumlahHari = 1; // Minimal 1 hari jika dalam weekend
        }

        // Upload lampiran jika ada
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_cuti', 'public');
        }

        PengajuanIzin::create([
            'karyawan_id'     => $karyawan->id,
            'tipe'            => $request->tipe,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari'     => $jumlahHari,
            'alasan'          => $request->alasan,
            'lampiran'        => $lampiranPath,
            'status'          => 'Menunggu Review',
        ]);

        return back()->with('success', "Pengajuan {$request->tipe} ({$jumlahHari} hari) berhasil dikirim! Menunggu peninjauan dari HR/Admin.");
    }

    /**
     * ── ADMIN: Tampilkan seluruh pengajuan cuti & izin karyawan ──
     */
    public function adminIndex(Request $request)
    {
        $query = PengajuanIzin::with(['karyawan.struktur', 'approver'])
                              ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $pengajuan = $query->paginate(15)->withQueryString();
        $karyawanList = Karyawan::where('status', 'Aktif')->orderBy('nama')->get();

        $stats = [
            'menunggu'  => PengajuanIzin::where('status', 'Menunggu Review')->count(),
            'disetujui' => PengajuanIzin::where('status', 'Disetujui')->count(),
            'ditolak'   => PengajuanIzin::where('status', 'Ditolak')->count(),
            'total'     => PengajuanIzin::count(),
        ];

        return view('admin.cuti.index', compact('pengajuan', 'karyawanList', 'stats'));
    }

    /**
     * ── ADMIN: Setujui pengajuan cuti / sakit & otomatis buat absensi ──
     */
    public function approve(Request $request, $id)
    {
        $pengajuan = PengajuanIzin::findOrFail($id);

        $pengajuan->update([
            'status'        => 'Disetujui',
            'catatan_admin' => $request->catatan_admin,
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
        ]);

        // Sinkronisasi otomatis ke tabel absensi untuk rentang tanggal pengajuan
        $mulai   = Carbon::parse($pengajuan->tanggal_mulai);
        $selesai = Carbon::parse($pengajuan->tanggal_selesai);
        $period  = CarbonPeriod::create($mulai, $selesai);

        $statusKehadiran = $pengajuan->tipe === 'Sakit' ? 'Sakit' : 'Izin';

        foreach ($period as $date) {
            if ($date->isWeekend()) {
                continue; // Lewati weekend
            }

            Absensi::updateOrCreate(
                [
                    'karyawan_id' => $pengajuan->karyawan_id,
                    'tanggal'     => $date->toDateString(),
                ],
                [
                    'jam_masuk'             => '08:00',
                    'jam_keluar'            => '17:00',
                    'status_kehadiran'      => $statusKehadiran,
                    'keterangan'            => "Pengajuan {$pengajuan->tipe}: {$pengajuan->alasan}",
                    'status_approval_masuk' => 'Diizinkan',
                    'status_approval_pulang'=> 'Diizinkan',
                    'kena_denda'            => false,
                    'nominal_denda'         => 0,
                    'catatan_admin'         => "Disetujui via Pengajuan {$pengajuan->tipe} #" . $pengajuan->id,
                ]
            );
        }

        return back()->with('success', "Pengajuan {$pengajuan->tipe} untuk {$pengajuan->karyawan->nama} berhasil DISETUJUI. Catatan absensi telah disinkronkan otomatis!");
    }

    /**
     * ── ADMIN: Tolak pengajuan cuti / sakit ──
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|min:5|max:500',
        ], [
            'catatan_admin.required' => 'Wajib memberikan alasan atau catatan penolakan.',
            'catatan_admin.min'      => 'Catatan penolakan minimal 5 karakter.',
        ]);

        $pengajuan = PengajuanIzin::findOrFail($id);

        $pengajuan->update([
            'status'        => 'Ditolak',
            'catatan_admin' => $request->catatan_admin,
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
        ]);

        return back()->with('warning', "Pengajuan {$pengajuan->tipe} untuk {$pengajuan->karyawan->nama} telah DITOLAK.");
    }
}
