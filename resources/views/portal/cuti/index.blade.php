@extends('layouts.app')

@section('page-title', 'Pengajuan Cuti & Izin Sakit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.index') }}">Portal Karyawan</a></li>
    <li class="breadcrumb-item active">Pengajuan Cuti &amp; Izin</li>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- RINGKASAN STATISTIK --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="bi bi-calendar2-range-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Pengajuan ({{ date('Y') }})</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $stats['total_pengajuan'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Disetujui</div>
                    <h5 class="fw-bold mb-0 text-success">{{ $stats['disetujui'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small">Menunggu Review</div>
                    <h5 class="fw-bold mb-0 text-warning-emphasis">{{ $stats['menunggu'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Ditolak</div>
                    <h5 class="fw-bold mb-0 text-danger">{{ $stats['ditolak'] }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- FORM PENGAJUAN --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Formulir Permohonan Cuti &amp; Izin
                </h6>
                <small class="text-muted">Isi detail pengajuan permohonan ketidakhadiran kerja</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('portal.cuti.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Jenis Permohonan:</label>
                        <select name="tipe" id="selectTipe" class="form-select @error('tipe') is-invalid @enderror" required onchange="cekTipeSakit()">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Sakit" {{ old('tipe') == 'Sakit' ? 'selected' : '' }}>🏥 Sakit (Izin Sakit / Surat Dokter)</option>
                            <option value="Cuti Tahunan" {{ old('tipe') == 'Cuti Tahunan' ? 'selected' : '' }}>🏖️ Cuti Tahunan</option>
                            <option value="Izin" {{ old('tipe') == 'Izin' ? 'selected' : '' }}>📝 Izin Keperluan Mendesak</option>
                            <option value="Cuti Melahirkan" {{ old('tipe') == 'Cuti Melahirkan' ? 'selected' : '' }}>👶 Cuti Melahirkan</option>
                            <option value="Cuti Khusus" {{ old('tipe') == 'Cuti Khusus' ? 'selected' : '' }}>💍 Cuti Khusus (Menikah, Duka, dll)</option>
                        </select>
                        @error('tipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small">Tanggal Mulai:</label>
                            <input type="date" name="tanggal_mulai" id="tglMulai" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                   value="{{ old('tanggal_mulai', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required onchange="hitungHari()">
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small">Tanggal Selesai:</label>
                            <input type="date" name="tanggal_selesai" id="tglSelesai" class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                   value="{{ old('tanggal_selesai', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required onchange="hitungHari()">
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="p-2 mb-3 bg-light rounded-3 border text-center small">
                        Perkiraan Durasi: <strong class="text-primary" id="tampilHari">1 Hari Kerja</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Keterangan / Alasan:</label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" 
                                  placeholder="Tuliskan keterangan keperluan cuti/sakit secara jelas..." required>{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small d-flex justify-content-between">
                            <span>Lampiran Berkas (Surat Dokter / Bukti):</span>
                            <span id="badgeWajibLampiran" class="badge bg-danger" style="display: none;">Wajib untuk Sakit</span>
                        </label>
                        <input type="file" name="lampiran" id="inputLampiran" class="form-control @error('lampiran') is-invalid @enderror" 
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text text-muted small">
                            Format yang didukung: JPG, PNG, atau PDF (Maks. 5 MB).
                        </div>
                        @error('lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> Kirim Pengajuan Cuti / Izin
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIWAYAT PENGAJUAN --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pengajuan Saya
                    </h6>
                    <small class="text-muted">Status peninjauan permohonan oleh HRD</small>
                </div>
                <span class="badge bg-secondary">{{ $pengajuan->total() }} Pengajuan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Jenis</th>
                                <th>Periode</th>
                                <th>Hari</th>
                                <th>Bukti</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($pengajuan as $p)
                            <tr>
                                <td>
                                    {!! $p->tipe_badge !!}
                                    <div class="text-muted small mt-1 text-truncate" style="max-width: 140px;" title="{{ $p->alasan }}">
                                        {{ $p->alasan }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->tanggal_mulai->format('d M Y') }}</div>
                                    @if($p->tanggal_mulai != $p->tanggal_selesai)
                                        <div class="text-muted" style="font-size:0.75rem;">s.d. {{ $p->tanggal_selesai->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $p->jumlah_hari }} hr</span></td>
                                <td>
                                    @if($p->lampiran_url)
                                        <a href="{{ $p->lampiran_url }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.75rem;">
                                            <i class="bi bi-paperclip"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $p->status_badge !!}
                                    @if($p->catatan_admin)
                                        <div class="text-muted small mt-1" style="font-size:0.7rem;">
                                            <em>"{{ $p->catatan_admin }}"</em>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada riwayat pengajuan cuti atau izin sakit.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pengajuan->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function hitungHari() {
        const mulai = document.getElementById('tglMulai').value;
        const selesai = document.getElementById('tglSelesai').value;
        if (mulai && selesai) {
            const d1 = new Date(mulai);
            const d2 = new Date(selesai);
            if (d2 >= d1) {
                const diffTime = Math.abs(d2 - d1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('tampilHari').textContent = diffDays + ' Hari Kalender';
            } else {
                document.getElementById('tampilHari').textContent = 'Tanggal tidak valid';
            }
        }
    }

    function cekTipeSakit() {
        const tipe = document.getElementById('selectTipe').value;
        const badge = document.getElementById('badgeWajibLampiran');
        if (tipe === 'Sakit') {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
</script>
@endpush
