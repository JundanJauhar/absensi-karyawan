@extends('layouts.app')

@section('page-title', 'Portal Presensi Mandiri')

@section('breadcrumb')
    <li class="breadcrumb-item active">Portal Karyawan</li>
@endsection

@section('content')

@php
    $hasFaceId = !empty($karyawan->face_descriptor);
@endphp

{{-- BANNER: Belum Setup Face ID --}}
@if(!$hasFaceId)
<div class="alert alert-warning border-0 rounded-3 mb-3 d-flex align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-person-bounding-box fs-3 text-warning"></i>
        <div>
            <strong>Wajah Belum Terdaftar di Sistem</strong><br>
            <small class="text-muted">Anda perlu mendaftarkan Face ID terlebih dahulu sebelum dapat melakukan absensi. Proses hanya dilakukan sekali.</small>
        </div>
    </div>
    <a href="{{ route('portal.face-setup') }}" class="btn btn-warning btn-sm text-nowrap fw-bold">
        <i class="bi bi-camera me-1"></i>Setup Face ID
    </a>
</div>
@else
@endif

{{-- ALERT REGULASI JAM KERJA PERUSAHAAN --}}
<div class="alert bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-4 p-3 shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1 text-primary">Regulasi Jam Kerja Perusahaan:</h6>
            <div class="d-flex flex-wrap gap-4 text-secondary small">
                <div><i class="bi bi-door-open-fill text-success me-1"></i> <strong>Jam Masuk Normal:</strong> Maksimal <code>07:30</code> (Lewat 07:30 wajib alasan &amp; persetujuan Admin)</div>
                <div><i class="bi bi-door-closed-fill text-danger me-1"></i> <strong>Jam Pulang Normal:</strong> Pukul <code>16:30</code></div>
                <div><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> <strong>Pulang Awal:</strong> Minimal <code>13:00</code> (Wajib alasan &amp; butuh ACC Admin)</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- PROFIL KARYAWAN CARD --}}
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center pb-3 border-bottom">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm mb-3" style="width: 72px; height: 72px;">
                        {{ strtoupper(substr($karyawan->nama, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ $karyawan->nama }}</h5>
                    <div class="text-primary fw-semibold small mb-2">{{ $karyawan->jabatan }}</div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                        <i class="bi bi-person-check-fill me-1"></i>{{ $karyawan->status }}
                    </span>
                </div>

                <div class="pt-3 small">
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Departemen</div>
                        <div class="col-7 fw-semibold text-dark">{{ $karyawan->struktur->nama_department ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Divisi</div>
                        <div class="col-7 fw-semibold text-dark">{{ $karyawan->struktur->nama_divisi ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Biro</div>
                        <div class="col-7 fw-semibold text-dark">{{ $karyawan->struktur->nama_biro ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Ikatan Kerja</div>
                        <div class="col-7">{!! $karyawan->status_kontrak_badge !!}</div>
                    </div>
                    @if($karyawan->tipe_kontrak === 'Outsourcing' && $karyawan->nama_vendor)
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Vendor PT</div>
                        <div class="col-7 text-secondary fw-semibold">{{ $karyawan->nama_vendor }}</div>
                    </div>
                    @endif
                    @if($karyawan->tanggal_selesai_kontrak)
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Habis Kontrak</div>
                        <div class="col-7 text-secondary">
                            {{ $karyawan->tanggal_selesai_kontrak->format('d M Y') }}
                            @if($karyawan->sisa_hari_kontrak !== null)
                                <span class="badge bg-light text-dark border ms-1">{{ $karyawan->sisa_hari_kontrak }} hari lagi</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="row pt-2 border-top">
                        <div class="col-5 text-muted">Email Login</div>
                        <div class="col-7 text-secondary text-truncate">{{ $user->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU AKSI PRESENSI HARI INI --}}
    <div class="col-lg-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-fingerprint text-primary me-2"></i>Presensi Hari Ini
                    </h6>
                    <small class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
                </div>
                <div class="text-end">
                    <span id="liveClock" class="badge bg-dark fs-6 px-3 py-2 font-monospace shadow-sm">00:00:00</span>
                </div>
            </div>

            <div class="card-body p-4">
                @if(!$absensiHariIni)
                    {{-- ── KONDISI 1: BELUM ABSEN MASUK ── --}}
                    <div class="p-3 bg-light rounded-3 text-center mb-3 border">
                        <div class="text-muted small mb-1">Status Kehadiran:</div>
                        <div class="badge bg-secondary fs-6 px-3 py-2 mb-2">
                            <i class="bi bi-clock me-1"></i> Belum Melakukan Presensi Masuk
                        </div>
                        <p class="small text-muted mb-0">Silakan tentukan jam kedatangan dan lakukan verifikasi Clock In.</p>
                    </div>

                    <form action="{{ route('portal.clock-in') }}" method="POST" id="formClockIn">
                        @csrf
                        <input type="hidden" name="lat" id="clockInLat">
                        <input type="hidden" name="long" id="clockInLong">
                        <input type="hidden" name="foto_masuk_base64" id="clockInFotoBase64">

                        {{-- Panel Pilihan Waktu Masuk --}}
                        <div class="mb-3 p-3 bg-light border rounded-3">
                            <label class="form-label fw-bold text-secondary small mb-2">
                                <i class="bi bi-clock-history me-1"></i> Jam Presensi Masuk:
                            </label>
                            <div class="input-group mb-2">
                                <input type="time" name="simulasi_jam" id="inputJamMasuk"
                                       class="form-control form-control-lg fw-bold text-primary font-monospace"
                                       value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="setJamMasuk('07:15')">
                                    <i class="bi bi-check2 me-1"></i> 07:15 (Tepat Waktu)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="setJamMasuk('08:05')">
                                    <i class="bi bi-exclamation-triangle me-1"></i> 08:05 (Terlambat)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setJamMasukSekarang()">
                                    <i class="bi bi-arrow-repeat me-1"></i> Waktu Sekarang
                                </button>
                            </div>
                        </div>

                        {{-- Form Alasan Terlambat (Muncul jika > 07:30) --}}
                        <div id="boxAlasanTerlambat" class="mb-3 p-3 bg-warning bg-opacity-10 border border-warning rounded-3" style="display: none;">
                            <label class="form-label fw-bold text-danger small">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                Jam kedatangan melewati 07:30 (Terlambat). Wajib mengisi keterangan:
                            </label>
                            <textarea name="alasan_terlambat" id="alasan_terlambat" class="form-control" rows="2"
                                      placeholder="Contoh: Terjebak kemacetan di jalan raya / kendaraan mogok / urusan keluarga mendesak..."></textarea>
                            <div class="form-text text-muted small">
                                * Alasan keterlambatan akan diverifikasi oleh HR/Admin untuk menentukan status pembebasan denda.
                            </div>
                        </div>

                        @if($hasFaceId)
                        <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm"
                            onclick="bukaModalFaceId('clockin', 'formClockIn')">
                            <i class="bi bi-person-bounding-box me-2"></i> VERIFIKASI WAJAH & CLOCK IN
                        </button>
                        @else
                        <div class="alert alert-warning small mb-0 text-center">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Daftarkan Face ID terlebih dahulu.
                            <a href="{{ route('portal.face-setup') }}" class="fw-bold">Setup Sekarang</a>
                        </div>
                        @endif
                    </form>

                @elseif(!$absensiHariIni->jam_keluar)
                    {{-- ── KONDISI 2: SUDAH MASUK, BELUM PULANG ── --}}
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Jam Masuk Anda:</span>
                                <div class="fs-4 fw-bold text-success font-monospace">{{ $absensiHariIni->jam_masuk }}</div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted mb-1">Status Verifikasi Masuk:</div>
                                {!! $absensiHariIni->status_approval_masuk_badge !!}
                            </div>
                        </div>
                        @if($absensiHariIni->alasan_terlambat)
                            <div class="mt-2 pt-2 border-top border-success border-opacity-25 small">
                                <strong>Keterangan Terlambat:</strong> "{{ $absensiHariIni->alasan_terlambat }}"
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('portal.clock-out') }}" method="POST" id="formClockOut">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="lat" id="clockOutLat">
                        <input type="hidden" name="long" id="clockOutLong">
                        <input type="hidden" name="foto_keluar_base64" id="clockOutFotoBase64">

                        {{-- Panel Pilihan Waktu Pulang --}}
                        <div class="mb-3 p-3 bg-light border rounded-3">
                            <label class="form-label fw-bold text-secondary small mb-2">
                                <i class="bi bi-clock-history me-1"></i> Jam Presensi Pulang:
                            </label>
                            <div class="input-group mb-2">
                                <input type="time" name="simulasi_jam_keluar" id="inputJamKeluar"
                                       class="form-control form-control-lg fw-bold text-danger font-monospace"
                                       value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="setJamKeluar('12:15')">
                                    12:15 (&lt; 13:00 Ditolak)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="setJamKeluar('14:30')">
                                    14:30 (Pulang Awal)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="setJamKeluar('17:00')">
                                    17:00 (Pulang Normal)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setJamKeluarSekarang()">
                                    Waktu Sekarang
                                </button>
                            </div>
                        </div>

                        {{-- Form Alasan Pulang Awal (Muncul jika 13:00 s/d 16:29) --}}
                        <div id="boxAlasanPulangAwal" class="mb-3 p-3 bg-warning bg-opacity-10 border border-warning rounded-3" style="display: none;">
                            <label class="form-label fw-bold text-warning-emphasis small">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                Pulang sebelum pukul 16:30 (Pulang Awal). Wajib mengisi keterangan izin:
                            </label>
                            <textarea name="alasan_pulang_awal" id="alasan_pulang_awal" class="form-control" rows="2"
                                      placeholder="Contoh: Mengurus dokumen penting keluarga / keperluan darurat / izin dokter..."></textarea>
                            <div class="form-text text-muted small">
                                * Pulang lebih awal wajib diverifikasi dan disetujui (ACC) oleh HR/Admin.
                            </div>
                        </div>

                        @if($hasFaceId)
                        <button type="button" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow-sm"
                            onclick="bukaModalFaceId('clockout', 'formClockOut')">
                            <i class="bi bi-person-bounding-box me-2"></i> VERIFIKASI WAJAH & CLOCK OUT
                        </button>
                        @else
                        <div class="alert alert-warning small mb-0 text-center">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Daftarkan Face ID terlebih dahulu.
                            <a href="{{ route('portal.face-setup') }}" class="fw-bold">Setup Sekarang</a>
                        </div>
                        @endif
                    </form>

                @elseif($absensiHariIni->jam_keluar && $absensiHariIni->status_approval_pulang === 'Ditolak')
                    {{-- ── KONDISI 4: PULANG AWAL DITOLAK → BISA CLOCK OUT LAGI ── --}}
                    <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25 mb-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-x-octagon-fill text-danger mt-1"></i>
                            <div>
                                <div class="fw-bold text-danger small">Izin Pulang Awal DITOLAK oleh Admin</div>
                                <div class="small text-muted mt-1">
                                    Pengajuan pulang awal pukul <strong>{{ $absensiHariIni->jam_keluar }}</strong> dengan alasan
                                    <em>"{{ $absensiHariIni->alasan_pulang_awal }}"</em> tidak disetujui.<br>
                                    Anda wajib menyelesaikan jam kerja normal hingga pukul <strong>{{ $jamPulangNormal }}</strong>.
                                </div>
                                @if($absensiHariIni->catatan_admin)
                                <div class="mt-2 p-2 bg-white rounded border small">
                                    <strong>Catatan Admin:</strong> {{ $absensiHariIni->catatan_admin }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-2 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3 mb-3 small text-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Silakan lakukan Clock Out kembali pada pukul <strong>{{ $jamPulangNormal }}</strong> atau setelahnya.
                    </div>

                    <form action="{{ route('portal.clock-out') }}" method="POST" id="formClockOutDitolak">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="lat" id="clockOutDitolakLat">
                        <input type="hidden" name="long" id="clockOutDitolakLong">
                        <input type="hidden" name="foto_keluar_base64" id="clockOutDitolakFotoBase64">
                        <div class="mb-3 p-3 bg-light border rounded-3">
                            <label class="form-label fw-bold text-secondary small mb-2">
                                <i class="bi bi-clock-history me-1"></i> Jam Presensi Pulang Normal:
                            </label>
                            <div class="input-group mb-2">
                                <input type="time" name="simulasi_jam_keluar" id="inputJamKeluarDitolak"
                                       class="form-control form-control-lg fw-bold text-danger font-monospace"
                                       value="{{ \Carbon\Carbon::now()->format('H:i') }}" required min="{{ $jamPulangNormal }}">
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="document.getElementById('inputJamKeluarDitolak').value='16:30'">
                                    16:30 (Jam Pulang Normal)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setJamKeluarDitolakSekarang()">
                                    <i class="bi bi-arrow-repeat me-1"></i>Waktu Sekarang
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow-sm"
                            onclick="bukaModalFaceId('clockout-ditolak', 'formClockOutDitolak')">
                            <i class="bi bi-person-bounding-box me-2"></i>
                            VERIFIKASI WAJAH & CLOCK OUT SEKARANG
                        </button>
                    </form>

                @else
                    {{-- ── KONDISI 3: SUDAH SELESAI HARI INI ── --}}
                    <div class="p-4 bg-light rounded-3 text-center border">
                        <div class="text-success mb-2" style="font-size: 2.8rem;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Presensi Hari Ini Telah Lengkap</h5>
                        <p class="text-muted small mb-3">Seluruh presensi masuk dan pulang Anda hari ini sudah tersimpan di sistem.</p>

                        <div class="row g-2 justify-content-center text-start small mb-3">
                            <div class="col-sm-5 p-3 bg-white rounded-3 border shadow-sm">
                                <div class="text-muted small">Jam Masuk:</div>
                                <div class="fs-5 fw-bold text-dark font-monospace">{{ $absensiHariIni->jam_masuk }}</div>
                                <div class="mt-1">{!! $absensiHariIni->status_approval_masuk_badge !!}</div>
                            </div>
                            <div class="col-sm-5 p-3 bg-white rounded-3 border shadow-sm">
                                <div class="text-muted small">Jam Pulang:</div>
                                <div class="fs-5 fw-bold text-dark font-monospace">{{ $absensiHariIni->jam_keluar }}</div>
                                <div class="mt-1">{!! $absensiHariIni->status_approval_pulang_badge !!}</div>
                            </div>
                        </div>

                        <div class="text-muted small mb-4">
                            Total Jam Kerja: <strong class="text-dark">{{ $absensiHariIni->durasi_kerja ?? '-' }}</strong>
                        </div>

                        {{-- Tombol Reset untuk kemudahan Pengujian / Testing --}}
                        <div class="border-top pt-3">
                            <form action="{{ route('portal.reset-hari-ini') }}" method="POST" onsubmit="return confirm('Reset presensi hari ini untuk kebutuhan pengujian ulang?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Presensi Hari Ini (Khusus Mode Testing)
                                </button>
                            </form>
                            <div class="text-muted" style="font-size: 0.75rem;">Gunakan tombol ini jika ingin menguji Clock In &amp; Clock Out kembali dari awal.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODAL FACE ID — Digunakan untuk Clock In, Clock Out, & Ditolak --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalFaceId" tabindex="-1" aria-labelledby="modalFaceIdLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" id="faceModalHeader" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
                <h5 class="modal-title fw-bold" id="modalFaceIdLabel">
                    <i class="bi bi-person-bounding-box me-2"></i>Verifikasi Face ID
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopFaceCamera()"></button>
            </div>
            <div class="modal-body p-0">
                {{-- Info Bar --}}
                <div class="px-4 py-3 border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="small fw-semibold text-dark" id="faceModalSubtitle">Hadapkan wajah Anda ke kamera untuk verifikasi</div>
                            <div class="small mt-1" id="faceLocationInfo">
                                <i class="bi bi-geo-alt me-1 text-muted"></i>
                                <span class="text-muted" id="faceLocationText">Mendeteksi lokasi GPS...</span>
                            </div>
                        </div>
                        <span id="faceStatusBadge" class="badge bg-secondary">Memulai Kamera...</span>
                    </div>
                </div>

                {{-- Camera + Overlay --}}
                <div class="position-relative" style="background:#0a0a0a; min-height: 300px; overflow:hidden;">
                    <video id="faceVideo" autoplay muted playsinline
                           style="width:100%; max-height:360px; display:block; transform:scaleX(-1);"></video>
                    <canvas id="faceCanvas" style="position:absolute;top:0;left:0;width:100%;height:100%;transform:scaleX(-1);"></canvas>

                    {{-- Placeholder kamera belum aktif (tanpa class d-flex agar hide bekerja sempurna) --}}
                    <div id="faceCamPlaceholder" style="display:flex; flex-direction:column; align-items:center; justify-content:center; position:absolute;top:0;left:0;right:0;bottom:0;min-height:300px; color:#fff; z-index:2;">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <span class="small text-white-50" id="facePlaceholderText">Memuat AI dan membuka kamera...</span>
                    </div>

                    {{-- Scan animation overlay --}}
                    <div id="faceScanLine" style="display:none; position:absolute;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#10b981,transparent);animation:scan 2s linear infinite; z-index:3;"></div>

                    {{-- Result success overlay --}}
                    <div id="faceSuccessOverlay" style="display:none; position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(16,185,129,0.92); z-index:10; align-items:center;justify-content:center;flex-direction:column; text-align:center; color:#fff;">
                        <i class="bi bi-check-circle-fill mb-2" style="font-size:4rem;"></i>
                        <div class="fw-bold fs-5">Wajah Terverifikasi!</div>
                        <small class="text-white-75">Menyimpan presensi Anda...</small>
                    </div>
                </div>

                {{-- Attempt counter --}}
                <div class="px-4 py-3 text-center">
                    <div id="faceAttemptInfo" class="small text-muted"></div>
                    <div id="faceErrorMsg" class="alert alert-danger py-2 mt-2 small" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" onclick="stopFaceCamera()">
                    <i class="bi bi-x me-1"></i>Batal
                </button>
                <button type="button" id="btnFaceRetry" class="btn btn-outline-primary px-4" style="display:none;" onclick="retryFaceVerification()">
                    <i class="bi bi-arrow-repeat me-1"></i>Coba Lagi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scan {
    0% { top: 0; }
    100% { top: 100%; }
}
</style>


{{-- TABEL RIWAYAT ABSENSI PRIBADI --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-journal-check me-2 text-primary"></i>Riwayat Kehadiran Pribadi
        </h6>
        <span class="badge bg-secondary">{{ $riwayatAbsensi->total() }} Record</span>
    </div>

    {{-- BAR PENCARIAN & FILTER RIWAYAT --}}
    <div class="p-3 bg-light border-bottom">
        <form action="{{ route('portal.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-lg-3 col-sm-6">
                <label class="form-label small text-muted mb-1"><i class="bi bi-calendar-event me-1"></i>Cari Tanggal Tertentu:</label>
                <input type="date" name="cari_tanggal" class="form-control form-control-sm" value="{{ request('cari_tanggal') }}">
            </div>
            <div class="col-lg-4 col-sm-6">
                <label class="form-label small text-muted mb-1"><i class="bi bi-calendar-range me-1"></i>Rentang Tanggal:</label>
                <div class="input-group input-group-sm">
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}" placeholder="Dari">
                    <span class="input-group-text bg-white">s/d</span>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}" placeholder="Sampai">
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <label class="form-label small text-muted mb-1"><i class="bi bi-funnel me-1"></i>Status Kehadiran:</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>
            <div class="col-lg-2 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                @if(request()->hasAny(['cari_tanggal', 'dari_tanggal', 'sampai_tanggal', 'status']))
                    <a href="{{ route('portal.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 4%">No</th>
                        <th>Tanggal</th>
                        <th>Foto Masuk</th>
                        <th>Jam Masuk &amp; Approval</th>
                        <th>Foto Pulang</th>
                        <th>Jam Keluar &amp; Approval</th>
                        <th>Jarak GPS</th>
                        <th>Durasi</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($riwayatAbsensi as $r)
                        <tr>
                            <td>{{ $loop->iteration + ($riwayatAbsensi->currentPage() - 1) * $riwayatAbsensi->perPage() }}</td>
                            <td class="fw-semibold">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                            
                            {{-- Foto Masuk --}}
                            <td>
                                @if($r->foto_masuk_url)
                                    <img src="{{ $r->foto_masuk_url }}" alt="Foto Masuk" 
                                         class="rounded-circle border shadow-sm" 
                                         style="width: 36px; height: 36px; object-fit: cover; cursor: pointer;"
                                         onclick="previewFoto('{{ $r->foto_masuk_url }}', 'Foto Masuk — {{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }} ({{ $r->jam_masuk }})')"
                                         title="Klik untuk perbesar">
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">{{ $r->jam_masuk ?? '-' }}</div>
                                {!! $r->status_approval_masuk_badge !!}
                                @if($r->alasan_terlambat)
                                    <div class="text-muted small mt-1"><em>"{{ $r->alasan_terlambat }}"</em></div>
                                @endif
                            </td>

                            {{-- Foto Pulang --}}
                            <td>
                                @if($r->foto_keluar_url)
                                    <img src="{{ $r->foto_keluar_url }}" alt="Foto Pulang" 
                                         class="rounded-circle border shadow-sm" 
                                         style="width: 36px; height: 36px; object-fit: cover; cursor: pointer;"
                                         onclick="previewFoto('{{ $r->foto_keluar_url }}', 'Foto Pulang — {{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }} ({{ $r->jam_keluar }})')"
                                         title="Klik untuk perbesar">
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold">{{ $r->jam_keluar ?? '-' }}</div>
                                {!! $r->status_approval_pulang_badge !!}
                                @if($r->alasan_pulang_awal)
                                    <div class="text-muted small mt-1"><em>"{{ $r->alasan_pulang_awal }}"</em></div>
                                @endif
                            </td>

                            {{-- Jarak GPS --}}
                            <td>
                                @if($r->jarak_masuk !== null)
                                    <div class="badge bg-light text-dark border mb-1">
                                        <i class="bi bi-geo-alt text-primary me-1"></i>In: {{ $r->jarak_masuk }}m
                                    </div>
                                @endif
                                @if($r->jarak_keluar !== null)
                                    <div class="badge bg-light text-dark border d-block">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>Out: {{ $r->jarak_keluar }}m
                                    </div>
                                @endif
                                @if($r->jarak_masuk === null && $r->jarak_keluar === null)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="small">{{ $r->durasi_kerja ?? '-' }}</td>
                            <td>
                                @if($r->kena_denda && $r->nominal_denda > 0)
                                    <span class="badge bg-danger">Rp {{ number_format($r->nominal_denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Rp 0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Tidak ada data riwayat presensi yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($riwayatAbsensi->hasPages())
        <div class="card-footer bg-white">
            {{ $riwayatAbsensi->links() }}
        </div>
    @endif
</div>

{{-- MODAL PREVIEW FOTO ABSENSI --}}
<div class="modal fade" id="modalPreviewFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title small fw-bold" id="modalPreviewTitle">Bukti Foto Absensi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-black">
                <img id="modalPreviewImg" src="" alt="Foto Absensi" style="width:100%; max-height:450px; object-fit:contain;">
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <span class="small text-muted" id="modalPreviewCaption"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- face-api.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
// ════════════════════════════════════════════════
// LIVE CLOCK
// ════════════════════════════════════════════════
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) el.textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

function getNowHHMM() {
    const now = new Date();
    return `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
}

// ════════════════════════════════════════════════
// CLOCK IN — Form Validation
// ════════════════════════════════════════════════
const inputJamMasuk = document.getElementById('inputJamMasuk');
const boxAlasanTerlambat = document.getElementById('boxAlasanTerlambat');
const txtAlasanTerlambat = document.getElementById('alasan_terlambat');

function checkLateRule() {
    if (!inputJamMasuk) return;
    if (inputJamMasuk.value > '07:30') {
        boxAlasanTerlambat.style.display = 'block';
        txtAlasanTerlambat.setAttribute('required', 'required');
    } else {
        boxAlasanTerlambat.style.display = 'none';
        txtAlasanTerlambat.removeAttribute('required');
    }
}
inputJamMasuk?.addEventListener('change', checkLateRule);
inputJamMasuk?.addEventListener('input', checkLateRule);
checkLateRule();

function setJamMasuk(timeStr) {
    if (inputJamMasuk) { inputJamMasuk.value = timeStr; checkLateRule(); }
}
function setJamMasukSekarang() { setJamMasuk(getNowHHMM()); }

// ════════════════════════════════════════════════
// CLOCK OUT — Form Validation
// ════════════════════════════════════════════════
const inputJamKeluar = document.getElementById('inputJamKeluar');
const boxAlasanPulangAwal = document.getElementById('boxAlasanPulangAwal');
const txtAlasanPulangAwal = document.getElementById('alasan_pulang_awal');

function checkEarlyLeaveRule() {
    if (!inputJamKeluar) return;
    const val = inputJamKeluar.value;
    if (val >= '13:00' && val < '16:30') {
        boxAlasanPulangAwal.style.display = 'block';
        txtAlasanPulangAwal.setAttribute('required', 'required');
    } else {
        boxAlasanPulangAwal.style.display = 'none';
        txtAlasanPulangAwal.removeAttribute('required');
    }
}
inputJamKeluar?.addEventListener('change', checkEarlyLeaveRule);
inputJamKeluar?.addEventListener('input', checkEarlyLeaveRule);
checkEarlyLeaveRule();

function setJamKeluar(timeStr) {
    if (inputJamKeluar) { inputJamKeluar.value = timeStr; checkEarlyLeaveRule(); }
}
function setJamKeluarSekarang() { setJamKeluar(getNowHHMM()); }
function setJamKeluarDitolakSekarang() {
    const el = document.getElementById('inputJamKeluarDitolak');
    if (el) el.value = getNowHHMM();
}

// ════════════════════════════════════════════════
// PREVIEW FOTO ABSENSI MODAL
// ════════════════════════════════════════════════
function previewFoto(url, caption) {
    document.getElementById('modalPreviewImg').src = url;
    document.getElementById('modalPreviewCaption').textContent = caption || '';
    const modal = new bootstrap.Modal(document.getElementById('modalPreviewFoto'));
    modal.show();
}

// ════════════════════════════════════════════════
// GEOFENCING & GPS COORDINATES
// ════════════════════════════════════════════════
const officeLat = {{ $geofence['lat'] }};
const officeLong = {{ $geofence['long'] }};
const officeRadius = {{ $geofence['radius'] }};
const geofencingAktif = {{ $geofence['aktif'] ? 'true' : 'false' }};
let userLat = null;
let userLong = null;
let userDistance = null;

function hitungJarakHaversine(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const toRad = x => x * Math.PI / 180;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return Math.round(R * c);
}

function deteksiLokasiGPS() {
    const locText = document.getElementById('faceLocationText');
    if (!navigator.geolocation) {
        locText.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Perangkat tidak mendukung GPS</span>';
        return;
    }

    locText.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm me-1"></span>Mendeteksi posisi GPS...</span>';

    navigator.geolocation.getCurrentPosition(
        function (position) {
            userLat = position.coords.latitude;
            userLong = position.coords.longitude;
            userDistance = hitungJarakHaversine(officeLat, officeLong, userLat, userLong);

            if (userDistance <= officeRadius) {
                locText.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Area Kantor (Jarak: ${userDistance}m / Maks ${officeRadius}m)</span>`;
            } else {
                if (geofencingAktif) {
                    locText.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Di Luar Radius Kantor! (${userDistance}m / Maks ${officeRadius}m)</span>`;
                    const errMsg = document.getElementById('faceErrorMsg');
                    errMsg.style.display = 'block';
                    errMsg.innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Anda berada di luar area kantor!</strong> Jarak terdeteksi: <strong>${userDistance} meter</strong> (Radius toleransi: <strong>${officeRadius} meter</strong>). Absensi tidak dapat dilanjutkan.`;
                } else {
                    locText.innerHTML = `<span class="text-info fw-bold"><i class="bi bi-geo-alt me-1"></i>Jarak: ${userDistance}m (Mode WFH/Bebas)</span>`;
                }
            }
        },
        function (error) {
            if (geofencingAktif) {
                locText.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-octagon me-1"></i>Izin lokasi GPS ditolak! Aktifkan GPS pada browser.</span>';
            } else {
                locText.innerHTML = '<span class="text-muted"><i class="bi bi-geo me-1"></i>GPS dilewati (Mode Bebas)</span>';
            }
        },
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
}

// ════════════════════════════════════════════════
// FACE ID ENGINE
// ════════════════════════════════════════════════
const MODEL_URL = '{{ asset("models") }}';
const STORED_DESCRIPTOR = @json($karyawan->face_descriptor);
let faceModelsLoaded = false;
let faceStream = null;
let faceDetectionInterval = null;
let currentActionType = null;   // 'clockin', 'clockout', 'clockout-ditolak'
let currentFormId = null;
let attemptCount = 0;
const MAX_ATTEMPTS = 5;

// Preload face-api models in background
async function preloadFaceModels() {
    if (faceModelsLoaded || !STORED_DESCRIPTOR) return;
    try {
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);
        faceModelsLoaded = true;
    } catch(e) { console.warn('Face model preload failed:', e); }
}
document.addEventListener('DOMContentLoaded', () => { preloadFaceModels(); });

// Validate Clock In/Out form before opening Face ID modal
function validateBeforeFaceId(actionType) {
    if (actionType === 'clockin') {
        const jam = inputJamMasuk?.value;
        if (!jam) { alert('Silakan pilih jam masuk terlebih dahulu!'); return false; }
        const alasan = txtAlasanTerlambat ? txtAlasanTerlambat.value.trim() : '';
        if (jam > '07:30' && alasan.length < 5) {
            alert('Jam kedatangan Anda melewati 07:30 (Terlambat). Wajib mengisi alasan keterlambatan minimal 5 karakter!');
            txtAlasanTerlambat.focus();
            return false;
        }
    } else if (actionType === 'clockout') {
        const jam = inputJamKeluar?.value;
        if (!jam) { alert('Silakan pilih jam pulang terlebih dahulu!'); return false; }
        if (jam < '13:00') {
            alert('Pukul ' + jam + ': Belum diperbolehkan pulang! Jam pulang paling awal adalah pukul 13:00 dengan ACC Admin.');
            return false;
        }
        const alasan = txtAlasanPulangAwal ? txtAlasanPulangAwal.value.trim() : '';
        if (jam < '16:30' && alasan.length < 5) {
            alert('Pulang sebelum pukul 16:30 (Pulang Awal) wajib mengisi alasan izin minimal 5 karakter!');
            txtAlasanPulangAwal.focus();
            return false;
        }
    }
    return true;
}

async function bukaModalFaceId(actionType, formId) {
    if (!validateBeforeFaceId(actionType)) return;

    currentActionType = actionType;
    currentFormId = formId;
    attemptCount = 0;

    // Set header color based on action
    const header = document.getElementById('faceModalHeader');
    const title = document.getElementById('modalFaceIdLabel');
    if (actionType === 'clockin') {
        header.style.background = 'linear-gradient(135deg, #1e40af, #3b82f6)';
        title.innerHTML = '<i class="bi bi-person-bounding-box me-2"></i>Verifikasi Face ID — Clock In';
    } else {
        header.style.background = 'linear-gradient(135deg, #991b1b, #ef4444)';
        title.innerHTML = '<i class="bi bi-person-bounding-box me-2"></i>Verifikasi Face ID — Clock Out';
    }

    // Reset UI
    document.getElementById('faceAttemptInfo').textContent = '';
    document.getElementById('faceErrorMsg').style.display = 'none';
    document.getElementById('btnFaceRetry').style.display = 'none';
    document.getElementById('faceSuccessOverlay').style.display = 'none';
    document.getElementById('faceScanLine').style.display = 'none';
    document.getElementById('faceCamPlaceholder').style.display = 'flex';
    document.getElementById('facePlaceholderText').textContent = 'Memuat AI dan membuka kamera...';
    document.getElementById('faceStatusBadge').className = 'badge bg-secondary';
    document.getElementById('faceStatusBadge').textContent = 'Memulai Kamera...';

    const modal = new bootstrap.Modal(document.getElementById('modalFaceId'));
    modal.show();

    // Deteksi lokasi GPS secara bersamaan
    deteksiLokasiGPS();

    // Load models if needed then open camera
    if (!faceModelsLoaded) {
        document.getElementById('facePlaceholderText').textContent = 'Memuat model AI... (pertama kali ±6MB)';
        try {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);
            faceModelsLoaded = true;
        } catch(e) {
            document.getElementById('facePlaceholderText').textContent = 'Gagal memuat AI: ' + e.message;
            return;
        }
    }
    await bukaKameraFace();
}

async function bukaKameraFace() {
    try {
        faceStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
        });
        const video = document.getElementById('faceVideo');
        video.srcObject = faceStream;
        document.getElementById('faceCamPlaceholder').style.display = 'none';
        document.getElementById('faceScanLine').style.display = 'block';
        document.getElementById('faceStatusBadge').className = 'badge bg-warning text-dark';
        document.getElementById('faceStatusBadge').textContent = 'Mendeteksi Wajah...';

        video.onloadedmetadata = () => {
            mulaiVerifikasiWajah();
        };
    } catch(e) {
        document.getElementById('faceCamPlaceholder').style.display = 'flex';
        document.getElementById('facePlaceholderText').textContent = 'Gagal akses kamera: ' + e.message;
    }
}

async function mulaiVerifikasiWajah() {
    const video = document.getElementById('faceVideo');
    const canvas = document.getElementById('faceCanvas');
    const storedArr = JSON.parse(STORED_DESCRIPTOR);
    const storedDescriptor = new Float32Array(storedArr);

    document.getElementById('faceAttemptInfo').textContent = `Percobaan verifikasi: 0 / ${MAX_ATTEMPTS}`;

    faceDetectionInterval = setInterval(async () => {
        if (video.readyState !== 4) return;

        const dims = faceapi.matchDimensions(canvas, video, true);
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
            .withFaceLandmarks(true)
            .withFaceDescriptor();

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!detection) {
            document.getElementById('faceStatusBadge').className = 'badge bg-secondary';
            document.getElementById('faceStatusBadge').textContent = 'Mengarahkan Wajah...';
            return;
        }

        // Gambar kotak deteksi
        const resized = faceapi.resizeResults(detection, dims);
        const box = resized.detection.box;
        ctx.strokeStyle = '#f59e0b'; 
        ctx.lineWidth = 3;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
        faceapi.draw.drawFaceLandmarks(canvas, resized);

        // Hitung jarak kemiripan
        const distance = faceapi.euclideanDistance(detection.descriptor, storedDescriptor);
        attemptCount++;

        document.getElementById('faceAttemptInfo').textContent = `Percobaan: ${attemptCount} / ${MAX_ATTEMPTS} | Jarak: ${distance.toFixed(3)} (Maks: 0.55)`;
        document.getElementById('faceStatusBadge').className = 'badge bg-warning text-dark';
        document.getElementById('faceStatusBadge').textContent = `Menganalisis... (${distance.toFixed(2)})`;

        if (distance <= 0.55) {
            // Cek batasan geofencing
            if (geofencingAktif && userDistance !== null && userDistance > officeRadius) {
                clearInterval(faceDetectionInterval);
                stopFaceCamera();
                document.getElementById('faceStatusBadge').className = 'badge bg-danger';
                document.getElementById('faceStatusBadge').textContent = '❌ Di Luar Area Kantor';
                const errMsg = document.getElementById('faceErrorMsg');
                errMsg.style.display = 'block';
                errMsg.innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Wajah Cocok, TAPI Anda di luar area kantor!</strong> Jarak Anda: <strong>${userDistance}m</strong> (Maksimal: ${officeRadius}m). Absensi diblokir.`;
                document.getElementById('btnFaceRetry').style.display = 'inline-block';
                return;
            }

            // ✅ WAJAH COCOK & DALAM RADIUS
            clearInterval(faceDetectionInterval);
            ctx.strokeStyle = '#10b981';
            ctx.strokeRect(box.x, box.y, box.width, box.height);
            document.getElementById('faceStatusBadge').className = 'badge bg-success';
            document.getElementById('faceStatusBadge').textContent = '✅ Wajah Terverifikasi!';
            document.getElementById('faceSuccessOverlay').style.display = 'flex';
            document.getElementById('faceScanLine').style.display = 'none';

            // Ambil snapshot foto wajah dari video frame
            const snapCanvas = document.createElement('canvas');
            snapCanvas.width = video.videoWidth || 640;
            snapCanvas.height = video.videoHeight || 480;
            const snapCtx = snapCanvas.getContext('2d');
            snapCtx.drawImage(video, 0, 0, snapCanvas.width, snapCanvas.height);
            const photoBase64 = snapCanvas.toDataURL('image/jpeg', 0.85);

            // Suntikkan data GPS dan Foto ke formulir yang aktif
            const targetForm = document.getElementById(currentFormId);
            if (targetForm) {
                const inLat = targetForm.querySelector('input[name="lat"]');
                const inLong = targetForm.querySelector('input[name="long"]');
                if (inLat && userLat) inLat.value = userLat;
                if (inLong && userLong) inLong.value = userLong;

                if (currentActionType === 'clockin') {
                    const inFoto = targetForm.querySelector('input[name="foto_masuk_base64"]');
                    if (inFoto) inFoto.value = photoBase64;
                } else {
                    const inFoto = targetForm.querySelector('input[name="foto_keluar_base64"]');
                    if (inFoto) inFoto.value = photoBase64;
                }
            }

            stopFaceCamera();
            setTimeout(() => {
                const bsModal = bootstrap.Modal.getInstance(document.getElementById('modalFaceId'));
                if (bsModal) bsModal.hide();
                if (targetForm) targetForm.submit();
            }, 800);

        } else if (attemptCount >= MAX_ATTEMPTS) {
            // ❌ GAGAL — Habis percobaan
            clearInterval(faceDetectionInterval);
            stopFaceCamera();
            document.getElementById('faceStatusBadge').className = 'badge bg-danger';
            document.getElementById('faceStatusBadge').textContent = '❌ Verifikasi Gagal';
            const errMsg = document.getElementById('faceErrorMsg');
            errMsg.style.display = 'block';
            errMsg.innerHTML = '<i class="bi bi-x-circle me-1"></i><strong>Wajah tidak dapat diverifikasi.</strong> Pastikan pencahayaan cukup terang dan wajah menghadap lurus ke kamera.';
            document.getElementById('btnFaceRetry').style.display = 'inline-block';
        }
    }, 350);
}

function stopFaceCamera() {
    clearInterval(faceDetectionInterval);
    if (faceStream) {
        faceStream.getTracks().forEach(t => t.stop());
        faceStream = null;
    }
    const video = document.getElementById('faceVideo');
    if (video) video.srcObject = null;
}

async function retryFaceVerification() {
    attemptCount = 0;
    document.getElementById('faceErrorMsg').style.display = 'none';
    document.getElementById('btnFaceRetry').style.display = 'none';
    document.getElementById('faceStatusBadge').className = 'badge bg-secondary';
    document.getElementById('faceStatusBadge').textContent = 'Membuka Kamera Ulang...';
    document.getElementById('faceCamPlaceholder').style.display = 'flex';
    document.getElementById('facePlaceholderText').textContent = 'Membuka kamera...';
    deteksiLokasiGPS();
    await bukaKameraFace();
}
</script>
@endpush

