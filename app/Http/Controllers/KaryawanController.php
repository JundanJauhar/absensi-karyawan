<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Struktur;
use App\Models\User;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Menampilkan daftar semua karyawan (tabel + pagination).
     * Hanya butuh $karyawan — tidak perlu $jabatans/$statusKaryawans di sini.
     */
    public function index()
    {
        $karyawan = Karyawan::with('struktur')->latest()->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    /**
     * Form tambah karyawan.
     * $strukturs dipakai untuk dropdown pilih unit organisasi.
     */
    public function create()
    {
        $strukturs = Struktur::orderBy('nama_department')->orderBy('nama_divisi')->get();
        return view('karyawan.create', compact('strukturs'));
    }

    /**
     * Simpan karyawan baru ke database + otomatis buatkan akun user login.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'alamat'        => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'agama'         => 'required|in:Islam,Kristen,Katolik,Hindu,Budha,Buddha,Konghucu',
            'jabatan'       => 'required|string|max:255',
            'gaji'          => 'required|numeric|min:0',
            'status'                  => 'required|in:Aktif,Tidak Aktif',
            'tipe_kontrak'            => 'required|in:PKWTT,PKWT,Outsourcing',
            'nama_vendor'             => 'nullable|string|max:255',
            'tanggal_mulai_kontrak'   => 'nullable|date',
            'tanggal_selesai_kontrak' => 'nullable|date|after_or_equal:tanggal_mulai_kontrak',
            'struktur_id'             => 'required|exists:struktur,id',
            'riwayat_karir'           => 'nullable|string',
            'kontrak_kerja'           => 'nullable|file|max:2048',
            'dokumen_legal'           => 'nullable|file|max:2048',

            // Akun Login Sistem
            'email'                   => 'required|email|unique:users,email',
            'password'                => 'required|string|min:6|confirmed',
            'password_confirmation'   => 'required|string|min:6',
        ]);

        if ($validated['agama'] === 'Buddha') {
            $validated['agama'] = 'Budha';
        }

        if ($request->hasFile('kontrak_kerja')) {
            $validated['kontrak_kerja'] = $request->file('kontrak_kerja')->store('kontrak', 'public');
        }
        if ($request->hasFile('dokumen_legal')) {
            $validated['dokumen_legal'] = $request->file('dokumen_legal')->store('dokumen', 'public');
        }

        $karyawan = Karyawan::create([
            'nama'                    => $validated['nama'],
            'alamat'                  => $validated['alamat'],
            'tanggal_lahir'           => $validated['tanggal_lahir'],
            'jenis_kelamin'           => $validated['jenis_kelamin'],
            'agama'                   => $validated['agama'],
            'jabatan'                 => $validated['jabatan'],
            'gaji'                    => $validated['gaji'],
            'status'                  => $validated['status'],
            'tipe_kontrak'            => $validated['tipe_kontrak'],
            'nama_vendor'             => $validated['tipe_kontrak'] === 'Outsourcing' ? $validated['nama_vendor'] : null,
            'tanggal_mulai_kontrak'   => $validated['tanggal_mulai_kontrak'] ?? null,
            'tanggal_selesai_kontrak' => $validated['tipe_kontrak'] !== 'PKWTT' ? ($validated['tanggal_selesai_kontrak'] ?? null) : null,
            'struktur_id'             => $validated['struktur_id'],
            'riwayat_karir'           => $validated['riwayat_karir'] ?? null,
            'kontrak_kerja'           => $validated['kontrak_kerja'] ?? null,
            'dokumen_legal'           => $validated['dokumen_legal'] ?? null,
        ]);

        // Otomatis buatkan akun user login untuk karyawan ini
        User::create([
            'name'        => $karyawan->nama,
            'email'       => $validated['email'],
            'password'    => bcrypt($validated['password']),
            'role'        => 'karyawan',
            'karyawan_id' => $karyawan->id,
        ]);

        return redirect()->route('karyawan.index')
                         ->with('success', "Karyawan {$karyawan->nama} berhasil ditambahkan beserta akun login ({$validated['email']}).");
    }

    /**
     * Detail satu karyawan — load relasi struktur untuk tampilkan Department > Divisi > Biro.
     */
    public function show(string $id)
    {
        $karyawan = Karyawan::with('struktur')->findOrFail($id);
        return view('karyawan.show', compact('karyawan'));
    }

    /**
     * Form edit karyawan.
     */
    public function edit(string $id)
    {
        $karyawan  = Karyawan::with('user')->findOrFail($id);
        $strukturs = Struktur::orderBy('nama_department')->orderBy('nama_divisi')->get();
        return view('karyawan.edit', compact('karyawan', 'strukturs'));
    }

    /**
     * Update data karyawan beserta akun login (email & password opsional).
     */
    public function update(Request $request, string $id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $userId = $karyawan->user?->id ?? 'NULL';

        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'alamat'        => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'agama'         => 'required|in:Islam,Kristen,Katolik,Hindu,Budha,Konghucu',
            'jabatan'       => 'required|string|max:255',
            'gaji'          => 'required|numeric|min:0',
            'status'        => 'required|in:Aktif,Tidak Aktif',
            'struktur_id'   => 'required|exists:struktur,id',
            'riwayat_karir' => 'nullable|string',
            'kontrak_kerja' => 'nullable|string|max:255',
            'dokumen_legal' => 'nullable|string|max:255',

            // Akun Login
            'email'                 => "required|email|unique:users,email,{$userId}",
            'password'              => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|string|min:6',
        ]);

        $karyawan->update([
            'nama'          => $validated['nama'],
            'alamat'        => $validated['alamat'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'agama'         => $validated['agama'],
            'jabatan'       => $validated['jabatan'],
            'gaji'          => $validated['gaji'],
            'status'        => $validated['status'],
            'struktur_id'   => $validated['struktur_id'],
            'riwayat_karir' => $validated['riwayat_karir'] ?? null,
        ]);

        // Update atau buat akun login karyawan
        $userData = ['name' => $karyawan->nama, 'email' => $validated['email']];
        if (!empty($validated['password'])) {
            $userData['password'] = bcrypt($validated['password']);
        }

        if ($karyawan->user) {
            $karyawan->user->update($userData);
        } else {
            User::create(array_merge($userData, [
                'password'    => bcrypt($validated['password'] ?? 'password'),
                'role'        => 'karyawan',
                'karyawan_id' => $karyawan->id,
            ]));
        }

        return redirect()->route('karyawan.index')
                         ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Hapus karyawan. Data absensi akan ikut terhapus otomatis (cascadeOnDelete di migration).
     */
    public function destroy(string $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')
                         ->with('success', 'Karyawan berhasil dihapus.');
    }
}
