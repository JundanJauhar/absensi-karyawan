<?php
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortalKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = $user->karyawan()->with('struktur')->first();

        if (!$karyawan) {
            return view('portal.no_karyawan');
        }

        $today = today()->toDateString();
        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)->first();

        $query = Absensi::where('karyawan_id', $karyawan->id)
            ->orderByDesc('tanggal')->orderByDesc('jam_masuk');
        if ($request->filled('cari_tanggal')) {
            $query->whereDate('tanggal', $request->cari_tanggal);
        }
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status_kehadiran', $request->status);
        }

        $riwayatAbsensi = $query->paginate(10)->withQueryString();
        $bulan = now()->month;
        $tahun = now()->year;
        $stats = [
            'total_hadir' => Absensi::where('karyawan_id', $karyawan->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->whereIn('status_kehadiran', ['Hadir', 'Terlambat'])->count(),
            'total_terlambat' => Absensi::where('karyawan_id', $karyawan->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status_kehadiran', 'Terlambat')->count(),
            'total_denda' => Absensi::where('karyawan_id', $karyawan->id)->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('kena_denda', true)->sum('nominal_denda'),
        ];

        return view('portal.index', [
            'user' => $user,
            'karyawan' => $karyawan,
            'absensiHariIni' => $absensiHariIni,
            'riwayatAbsensi' => $riwayatAbsensi,
            'stats' => $stats,
            'hasFaceId' => !empty($karyawan->face_descriptor),
            'jamPulangNormal' => Absensi::JAM_PULANG_NORMAL,
            'geofence' => [
                'lat' => (float) Pengaturan::get('kantor_lat', '-7.711583'),
                'long' => (float) Pengaturan::get('kantor_long', '110.395792'),
                'radius' => (int) Pengaturan::get('kantor_radius_meter', '40'),
                'aktif' => (bool) Pengaturan::get('geofencing_aktif', '1'),
            ],
        ]);
    }

    public function clockIn(Request $request)
    {
        $karyawan = auth()->user()->karyawan;
        abort_unless($karyawan, 403);
        $request->validate([
            'simulasi_jam' => 'nullable|date_format:H:i',
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',
            'alasan_terlambat' => 'nullable|string|max:500',
            'foto_masuk_base64' => 'nullable|string',
        ]);

        $absensi = Absensi::firstOrNew(['karyawan_id' => $karyawan->id, 'tanggal' => today()]);
        if ($absensi->exists && $absensi->jam_masuk) {
            return back()->withErrors(['clock_in' => 'Anda sudah melakukan absensi masuk hari ini.']);
        }

        $time = $request->input('simulasi_jam', now()->format('H:i'));
        $late = $time > Absensi::JAM_MASUK_MAKSIMAL;
        $distance = $this->distanceFromOffice($request);
        if ($distance !== null && $distance > $this->officeRadius()) {
            return back()->withErrors(['location' => 'Anda berada di luar radius absensi kantor.']);
        }

        $absensi->fill([
            'jam_masuk' => $time,
            'status_kehadiran' => $late ? 'Terlambat' : 'Hadir',
            'sumber_absensi' => 'manual',
            'status_approval_masuk' => $late ? 'Menunggu Review' : 'Tepat Waktu',
            'alasan_terlambat' => $late ? $request->alasan_terlambat : null,
            'kena_denda' => $late,
            'nominal_denda' => $late ? Absensi::NOMINAL_DENDA_DEFAULT : 0,
            'lat_masuk' => $request->lat,
            'long_masuk' => $request->long,
            'jarak_masuk' => $distance,
            'foto_masuk' => $this->storeBase64Photo($request->foto_masuk_base64, 'absensi/masuk'),
        ]);
        $absensi->save();

        return back()->with('success', 'Clock In berhasil dicatat.');
    }

    public function clockOut(Request $request)
    {
        $karyawan = auth()->user()->karyawan;
        abort_unless($karyawan, 403);
        $absensi = Absensi::where('karyawan_id', $karyawan->id)->whereDate('tanggal', today())->firstOrFail();
        $request->validate([
            'simulasi_jam_keluar' => 'required|date_format:H:i',
            'lat' => 'nullable|numeric|between:-90,90',
            'long' => 'nullable|numeric|between:-180,180',
            'alasan_pulang_awal' => 'nullable|string|max:500',
            'foto_keluar_base64' => 'nullable|string',
        ]);
        if ($absensi->jam_keluar) {
            return back()->withErrors(['clock_out' => 'Anda sudah melakukan absensi pulang hari ini.']);
        }
        if ($request->simulasi_jam_keluar < $absensi->jam_masuk) {
            return back()->withErrors(['simulasi_jam_keluar' => 'Jam pulang tidak boleh sebelum jam masuk.']);
        }
        $distance = $this->distanceFromOffice($request);
        if ($distance !== null && $distance > $this->officeRadius()) {
            return back()->withErrors(['location' => 'Anda berada di luar radius absensi kantor.']);
        }
        $early = $request->simulasi_jam_keluar < Absensi::JAM_PULANG_NORMAL;
        $absensi->update([
            'jam_keluar' => $request->simulasi_jam_keluar,
            'sumber_absensi' => 'manual',
            'status_approval_pulang' => $early ? 'Menunggu Review' : 'Normal',
            'alasan_pulang_awal' => $early ? $request->alasan_pulang_awal : null,
            'lat_keluar' => $request->lat,
            'long_keluar' => $request->long,
            'jarak_keluar' => $distance,
            'foto_keluar' => $this->storeBase64Photo($request->foto_keluar_base64, 'absensi/pulang'),
        ]);
        return back()->with('success', 'Clock Out berhasil dicatat.');
    }

    public function resetHariIni()
    {
        $karyawan = auth()->user()->karyawan;
        if ($karyawan) {
            Absensi::where('karyawan_id', $karyawan->id)->whereDate('tanggal', today())->delete();
        }
        return back()->with('success', 'Presensi hari ini berhasil direset.');
    }

    public function setupWajah()
    {
        $karyawan = auth()->user()->karyawan()->with('struktur')->firstOrFail();
        return view('portal.face-setup', compact('karyawan'));
    }

    public function simpanDescriptor(Request $request): JsonResponse
    {
        $request->validate(['face_descriptor' => 'required|json']);
        $descriptor = json_decode($request->face_descriptor, true);
        if (!is_array($descriptor) || count($descriptor) !== 128 || count(array_filter($descriptor, 'is_numeric')) !== 128) {
            return response()->json(['success' => false, 'message' => 'Descriptor wajah tidak valid.'], 422);
        }
        $karyawan = auth()->user()->karyawan;
        abort_unless($karyawan, 403);
        $karyawan->update(['face_descriptor' => json_encode(array_map('floatval', $descriptor)), 'face_registered_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Face ID berhasil didaftarkan.']);
    }

    public function kiosk()
    {
        $manualAttendanceIds = Absensi::whereDate('tanggal', today())
            ->where(function ($query) {
                $query->whereNull('sumber_absensi')
                    ->orWhere('sumber_absensi', '!=', 'kiosk');
            })
            ->pluck('karyawan_id');

        $employees = Karyawan::where('status', 'Aktif')
            ->whereNotNull('face_descriptor')
            ->whereNotIn('id', $manualAttendanceIds)
            ->with('struktur')
            ->get();

        $employeeProfiles = $employees->map(function (Karyawan $employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->nama,
                'division' => $employee->struktur?->nama_divisi ?? '-',
                'descriptor' => json_decode($employee->face_descriptor, true),
            ];
        })->values();

        return view('kiosk.index', [
            'employeeProfiles' => $employeeProfiles,
        ]);
    }

    public function kioskAttendance(Request $request): JsonResponse
    {
        $data = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'photo_base64' => 'nullable|string',
        ]);
        $employee = Karyawan::where('status', 'Aktif')->findOrFail($data['karyawan_id']);
        $attendance = Absensi::firstOrNew(['karyawan_id' => $employee->id, 'tanggal' => today()]);
        $event = null;
        if ($attendance->exists && $attendance->sumber_absensi !== 'kiosk') {
            return response()->json(['success' => false, 'ignored' => true]);
        }
        if (!$attendance->jam_masuk) {
            $time = now()->format('H:i:s');
            if ($time > Absensi::JAM_MASUK_MAKSIMAL . ':59') {
                return response()->json([
                    'success' => false,
                    'manual' => true,
                    'message' => 'Waktu masuk otomatis sudah lewat 07:30. Silakan lakukan absensi manual melalui akun karyawan.',
                ]);
            }
            $attendance->fill([
                'jam_masuk' => $time,
                'status_kehadiran' => 'Hadir',
                'status_approval_masuk' => 'Tepat Waktu',
                'sumber_absensi' => 'kiosk',
                'kena_denda' => false,
                'nominal_denda' => 0,
                'foto_masuk' => $this->storeBase64Photo($data['photo_base64'] ?? null, 'absensi/masuk'),
            ]);
            $event = 'Masuk';
        } elseif (!$attendance->jam_keluar) {
            $time = now()->format('H:i:s');
            if ($time < Absensi::JAM_PULANG_NORMAL . ':00') {
                return response()->json([
                    'success' => false,
                    'manual' => true,
                    'message' => 'Waktu pulang otomatis belum dimulai. Pulang lebih awal harus diajukan melalui akun karyawan.',
                ]);
            }
            $attendance->jam_keluar = $time;
            $attendance->status_approval_pulang = 'Normal';
            $attendance->sumber_absensi = 'kiosk';
            $attendance->foto_keluar = $this->storeBase64Photo($data['photo_base64'] ?? null, 'absensi/pulang');
            $event = 'Pulang';
        } else {
            return response()->json(['success' => false, 'duplicate' => true, 'message' => 'Absensi hari ini sudah lengkap.']);
        }
        $attendance->save();
        return response()->json(['success' => true, 'event' => $event, 'employee' => $employee->only('id', 'nama'), 'divisi' => $employee->struktur->nama_divisi ?? '-', 'time' => $event === 'Masuk' ? $attendance->jam_masuk : $attendance->jam_keluar]);
    }

    public function kioskRecent(): JsonResponse
    {
        $rows = Absensi::with('karyawan.struktur')->whereDate('tanggal', today())->where(function ($q) {
            $q->whereNotNull('jam_masuk')->orWhereNotNull('jam_keluar');
        })->orderByDesc('updated_at')->limit(10)->get();
        return response()->json($rows->map(fn ($row) => ['name' => $row->karyawan->nama, 'division' => $row->karyawan->struktur->nama_divisi ?? '-', 'in' => $row->jam_masuk, 'out' => $row->jam_keluar]));
    }

    private function officeRadius(): int { return (int) Pengaturan::get('kantor_radius_meter', '40'); }

    private function distanceFromOffice(Request $request): ?int
    {
        if (!Pengaturan::get('geofencing_aktif', '1') || !$request->filled(['lat', 'long'])) return null;
        $lat1 = deg2rad((float) Pengaturan::get('kantor_lat', '-7.711583'));
        $lat2 = deg2rad((float) $request->lat);
        $dLat = $lat2 - $lat1;
        $dLon = deg2rad((float) $request->long - (float) Pengaturan::get('kantor_long', '110.395792'));
        $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        return (int) round(6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    private function storeBase64Photo(?string $value, string $directory): ?string
    {
        if (!$value || !preg_match('/^data:image\/(jpeg|png);base64,/', $value, $match)) return null;
        $binary = base64_decode(substr($value, strpos($value, ',') + 1), true);
        if ($binary === false || strlen($binary) > 5 * 1024 * 1024) return null;
        $path = $directory . '/' . uniqid('', true) . '.' . ($match[1] === 'jpeg' ? 'jpg' : 'png');
        Storage::disk('public')->put($path, $binary);
        return $path;
    }
}