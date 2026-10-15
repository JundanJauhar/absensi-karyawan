<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Daftar semua absensi dengan filter tanggal, karyawan, dan status.
     */
    public function index(Request $request)
    {
        $query = Absensi::with('karyawan')
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('jam_masuk', 'desc');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            // Default: tampilkan absensi hari ini
            $query->whereDate('tanggal', today());
        }

        // Filter berdasarkan karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter berdasarkan status kehadiran
        if ($request->filled('status')) {
            $query->where('status_kehadiran', $request->status);
        }

        $absensi  = $query->paginate(15)->withQueryString();
        $karyawan = Karyawan::orderBy('nama')->get(); // untuk dropdown filter

        // Ringkasan statistik hari yang dipilih
        $tanggalFilter = $request->tanggal ?? today()->toDateString();
        $statistik = [
            'hadir'     => Absensi::whereDate('tanggal', $tanggalFilter)->where('status_kehadiran', 'Hadir')->count(),
            'terlambat' => Absensi::whereDate('tanggal', $tanggalFilter)->where('status_kehadiran', 'Terlambat')->count(),
            'izin'      => Absensi::whereDate('tanggal', $tanggalFilter)->where('status_kehadiran', 'Izin')->count(),
            'sakit'     => Absensi::whereDate('tanggal', $tanggalFilter)->where('status_kehadiran', 'Sakit')->count(),
            'alpha'     => Absensi::whereDate('tanggal', $tanggalFilter)->where('status_kehadiran', 'Alpha')->count(),
            'total'     => Karyawan::where('status', 'Aktif')->count(),
        ];

        return view('absensi.index', compact('absensi', 'karyawan', 'statistik', 'tanggalFilter'));
    }

    /**
     * Form input absensi masuk karyawan (Clock In).
     */
    public function create()
    {
        // Hanya tampilkan karyawan Aktif yang belum absen hari ini
        $sudahAbsen = Absensi::whereDate('tanggal', today())->pluck('karyawan_id');
        $karyawan   = Karyawan::where('status', 'Aktif')
                              ->whereNotIn('id', $sudahAbsen)
                              ->with('struktur')
                              ->orderBy('nama')
                              ->get();

        $jamSekarang = Carbon::now()->format('H:i');

        return view('absensi.create', compact('karyawan', 'jamSekarang'));
    }

    /**
     * Simpan record absensi baru (Clock In).
     * Satu karyawan hanya bisa 1x absen per hari (dijaga oleh unique constraint di DB).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'      => 'required|exists:karyawan,id',
            'tanggal'          => 'required|date|before_or_equal:today',
            'jam_masuk'        => 'required|date_format:H:i',
            'jam_keluar'       => 'nullable|date_format:H:i|after:jam_masuk',
            'status_kehadiran' => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpha,Lembur',
            'keterangan'       => 'nullable|string|max:500',
        ]);

        // Cek apakah karyawan sudah absen di tanggal tersebut
        $sudahAbsen = Absensi::where('karyawan_id', $validated['karyawan_id'])
                             ->whereDate('tanggal', $validated['tanggal'])
                             ->exists();

        if ($sudahAbsen) {
            return back()
                ->withInput()
                ->withErrors(['karyawan_id' => 'Karyawan ini sudah memiliki data absensi untuk tanggal tersebut.']);
        }

        Absensi::create($validated);

        return redirect()->route('absensi.index')
                         ->with('success', 'Absensi berhasil dicatat.');
    }

    /**
     * Detail absensi satu record.
     */
    public function show(string $id)
    {
        $absensi = Absensi::with('karyawan.struktur')->findOrFail($id);
        return view('absensi.show', compact('absensi'));
    }

    /**
     * Form edit absensi (misal: koreksi jam keluar atau status).
     */
    public function edit(string $id)
    {
        $absensi = Absensi::with('karyawan')->findOrFail($id);
        return view('absensi.edit', compact('absensi'));
    }

    /**
     * Update data absensi (koreksi).
     */
    public function update(Request $request, string $id)
    {
        $absensi = Absensi::findOrFail($id);

        $validated = $request->validate([
            'jam_masuk'        => 'required|date_format:H:i',
            'jam_keluar'       => 'nullable|date_format:H:i|after:jam_masuk',
            'status_kehadiran' => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpha,Lembur',
            'keterangan'       => 'nullable|string|max:500',
        ]);

        $absensi->update($validated);

        return redirect()->route('absensi.index')
                         ->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Hapus record absensi.
     */
    public function destroy(string $id)
    {
        Absensi::findOrFail($id)->delete();

        return redirect()->route('absensi.index')
                         ->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Clock Out — update jam_keluar karyawan yang sudah Clock In hari ini.
     * Dipanggil via POST /absensi/{id}/clock-out
     */
    public function clockOut(Request $request, string $id)
    {
        $absensi = Absensi::findOrFail($id);

        $request->validate([
            'jam_keluar' => 'required|date_format:H:i|after:' . $absensi->jam_masuk,
        ]);

        $absensi->update(['jam_keluar' => $request->jam_keluar]);

        return redirect()->route('absensi.index')
                         ->with('success', "Absen pulang {$absensi->karyawan->nama} berhasil dicatat.");
    }

    /**
     * Verifikasi Keterlambatan oleh Admin:
     * - Aksi 'izinkan': Diterima izinnya, status = 'Diizinkan', Denda = 0
     * - Aksi 'tolak': Ditolak izinnya, status = 'Ditolak', Denda = nominal
     */
    public function approveKeterlambatan(Request $request, string $id)
    {
        $absensi = Absensi::with('karyawan')->findOrFail($id);

        $request->validate([
            'aksi'          => 'required|in:izinkan,tolak',
            'nominal_denda' => 'nullable|numeric|min:0',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        if ($request->aksi === 'izinkan') {
            $absensi->update([
                'status_approval_masuk' => 'Diizinkan',
                'kena_denda'            => false,
                'nominal_denda'         => 0,
                'catatan_admin'         => $request->catatan_admin,
            ]);
            $pesan = "Keterlambatan {$absensi->karyawan->nama} DISETUJUI / DIIZINKAN (Bebas Denda).";
        } else {
            $denda = $request->filled('nominal_denda') ? $request->nominal_denda : Absensi::NOMINAL_DENDA_DEFAULT;
            $absensi->update([
                'status_approval_masuk' => 'Ditolak',
                'kena_denda'            => true,
                'nominal_denda'         => $denda,
                'catatan_admin'         => $request->catatan_admin,
            ]);
            $pesan = "Keterlambatan {$absensi->karyawan->nama} DITOLAK. Karyawan dikenakan denda Rp " . number_format($denda, 0, ',', '.') . ".";
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * Verifikasi Pulang Awal oleh Admin:
     * - Aksi 'izinkan': Pulang awal di-ACC
     * - Aksi 'tolak': Pulang awal ditolak
     */
    public function approvePulangAwal(Request $request, string $id)
    {
        $absensi = Absensi::with('karyawan')->findOrFail($id);

        $request->validate([
            'aksi'          => 'required|in:izinkan,tolak',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $status = $request->aksi === 'izinkan' ? 'Diizinkan' : 'Ditolak';

        $absensi->update([
            'status_approval_pulang' => $status,
            'catatan_admin'          => $request->catatan_admin,
        ]);

        $statusText = $status === 'Diizinkan' ? 'DI-ACC' : 'DITOLAK';
        return redirect()->back()->with('success', "Pengajuan pulang awal {$absensi->karyawan->nama} {$statusText}.");
    }
}
