@extends('layouts.app')

@section('page-title', 'Persetujuan Cuti & Izin Karyawan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Approval Cuti &amp; Izin</li>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- KARTU STATISTIK --}}
<div class="row g-3 mb-4">
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
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="bi bi-files"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Pengajuan</div>
                    <h5 class="fw-bold mb-0 text-primary">{{ $stats['total'] }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DAFTAR PERMOHONAN CUTI / IZIN --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-file-earmark-medical text-primary me-2"></i>Daftar Pengajuan Cuti &amp; Izin Sakit Karyawan
                </h6>
                <small class="text-muted">Tinjau surat dokter dan lakukan persetujuan atau penolakan</small>
            </div>
            <form action="{{ route('cuti.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="Menunggu Review" {{ request('status') == 'Menunggu Review' ? 'selected' : '' }}>⏳ Menunggu Review</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
                <select name="tipe" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Jenis --</option>
                    <option value="Sakit" {{ request('tipe') == 'Sakit' ? 'selected' : '' }}>🏥 Sakit</option>
                    <option value="Cuti Tahunan" {{ request('tipe') == 'Cuti Tahunan' ? 'selected' : '' }}>🏖️ Cuti Tahunan</option>
                    <option value="Izin" {{ request('tipe') == 'Izin' ? 'selected' : '' }}>📝 Izin</option>
                </select>
                @if(request()->hasAny(['status', 'tipe', 'karyawan_id']))
                    <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Karyawan</th>
                        <th>Jenis Permohonan</th>
                        <th>Periode Tanggal</th>
                        <th>Durasi</th>
                        <th>Alasan / Keterangan</th>
                        <th>Surat / Lampiran</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($pengajuan as $p)
                    <tr>
                        <td>{{ $loop->iteration + ($pengajuan->currentPage() - 1) * $pengajuan->perPage() }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->karyawan->nama }}</div>
                            <small class="text-muted">{{ $p->karyawan->jabatan }} &bull; {{ $p->karyawan->struktur->nama_department ?? '-' }}</small>
                        </td>
                        <td>{!! $p->tipe_badge !!}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $p->tanggal_mulai->format('d M Y') }}</div>
                            @if($p->tanggal_mulai != $p->tanggal_selesai)
                                <small class="text-muted">s.d. {{ $p->tanggal_selesai->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $p->jumlah_hari }} hari</span></td>
                        <td>
                            <div style="max-width: 200px;" class="text-truncate" title="{{ $p->alasan }}">
                                {{ $p->alasan }}
                            </div>
                        </td>
                        <td>
                            @if($p->lampiran_url)
                                <a href="{{ $p->lampiran_url }}" target="_blank" class="btn btn-outline-primary btn-sm py-1 px-2">
                                    <i class="bi bi-paperclip me-1"></i> Buka Berkas
                                </a>
                            @else
                                <span class="text-muted">Tidak ada lampiran</span>
                            @endif
                        </td>
                        <td>
                            {!! $p->status_badge !!}
                            @if($p->catatan_admin)
                                <div class="text-muted small mt-1" style="font-size:0.75rem;">
                                    <strong>Catatan:</strong> <em>{{ $p->catatan_admin }}</em>
                                </div>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($p->status === 'Menunggu Review')
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $p->id }}">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject{{ $p->id }}">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </div>

                                {{-- MODAL SETUJUI --}}
                                <div class="modal fade text-start" id="modalApprove{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('cuti.approve', $p->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header bg-success text-white">
                                                    <h6 class="modal-title fw-bold"><i class="bi bi-check-circle me-1"></i> Setujui Permohonan {{ $p->tipe }}</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Anda akan menyetujui pengajuan cuti/izin untuk:</p>
                                                    <div class="p-3 bg-light rounded-3 border mb-3">
                                                        <div><strong>{{ $p->karyawan->nama }}</strong> ({{ $p->karyawan->jabatan }})</div>
                                                        <div class="text-muted small">Periode: {{ $p->tanggal_mulai->format('d M Y') }} - {{ $p->tanggal_selesai->format('d M Y') }} ({{ $p->jumlah_hari }} hari)</div>
                                                        <div class="text-muted small mt-1">Alasan: <em>"{{ $p->alasan }}"</em></div>
                                                    </div>
                                                    <div class="alert alert-info py-2 small mb-3">
                                                        <i class="bi bi-info-circle me-1"></i> Sistem akan <strong>secara otomatis membuat catatan absensi</strong> dengan status <strong>{{ $p->tipe === 'Sakit' ? 'Sakit' : 'Izin' }}</strong> pada tanggal-tanggal tersebut.
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label fw-semibold small">Catatan Persetujuan (Opsional):</label>
                                                        <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Contoh: Disetujui, jaga kesehatan / selamat berlibur..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success btn-sm fw-bold">
                                                        <i class="bi bi-check-lg me-1"></i> Ya, Setujui Pengajuan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL TOLAK --}}
                                <div class="modal fade text-start" id="modalReject{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('cuti.reject', $p->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header bg-danger text-white">
                                                    <h6 class="modal-title fw-bold"><i class="bi bi-x-circle me-1"></i> Tolak Permohonan {{ $p->tipe }}</h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Anda akan menolak pengajuan izin untuk <strong>{{ $p->karyawan->nama }}</strong>.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-danger small">Alasan Penolakan (Wajib Diisi):</label>
                                                        <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Tuliskan alasan penolakan secara jelas untuk karyawan..." required minlength="5"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger btn-sm fw-bold">
                                                        <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">Selesai Ditinjau</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            Belum ada permohonan cuti atau izin sakit karyawan.
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

@endsection
