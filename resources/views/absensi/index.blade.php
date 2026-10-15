@extends('layouts.app')

@section('page-title', 'Manajemen & Verifikasi Absensi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Absensi &amp; Verifikasi</li>
@endsection

@section('content')

{{-- ══ STATISTIK CARDS ══ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:700;color:#10b981">{{ $statistik['hadir'] }}</div>
                <div class="text-muted" style="font-size:.78rem">Tepat Waktu</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:700;color:#f59e0b">{{ $statistik['terlambat'] }}</div>
                <div class="text-muted" style="font-size:.78rem">Terlambat (&gt; 07:30)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:700;color:#0ea5e9">{{ $statistik['izin'] }}</div>
                <div class="text-muted" style="font-size:.78rem">Izin</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:700;color:#6366f1">{{ $statistik['sakit'] }}</div>
                <div class="text-muted" style="font-size:.78rem">Sakit</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:700;color:#ef4444">{{ $statistik['alpha'] }}</div>
                <div class="text-muted" style="font-size:.78rem">Alpha</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:700;color:#64748b">{{ $statistik['total'] }}</div>
                <div class="text-muted" style="font-size:.78rem">Karyawan Aktif</div>
            </div>
        </div>
    </div>
</div>

{{-- ══ FILTER ══ --}}
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('absensi.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggalFilter }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Karyawan</label>
                <select name="karyawan_id" class="form-select form-select-sm">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($karyawan as $k)
                        <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status Kehadiran</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    @foreach(['Hadir','Terlambat','Izin','Sakit','Alpha','Lembur'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('absensi.create') }}" class="btn btn-success btn-sm w-100">
                    <i class="bi bi-plus-lg me-1"></i>Catat Manual
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ══ TABEL ABSENSI DENGAN VERIFIKASI ADMIN ══ --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold text-dark">
                <i class="bi bi-calendar-check me-2 text-primary"></i>Daftar Presensi &amp; Verifikasi Approval
            </h6>
            <small class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('d F Y') }}</small>
        </div>
        <span class="badge bg-secondary">{{ $absensi->total() }} record</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:3%">No</th>
                        <th>Karyawan</th>
                        <th>Departemen &amp; Divisi</th>
                        <th>Masuk (&le;07:30)</th>
                        <th>Status Masuk &amp; Verifikasi</th>
                        <th>Pulang (&ge;16:30)</th>
                        <th>Status Pulang &amp; Verifikasi</th>
                        <th>Denda</th>
                        <th style="width:16%" class="text-center">Aksi &amp; Approval</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($absensi->currentPage() - 1) * $absensi->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size:.875rem">{{ $item->karyawan->nama ?? '-' }}</div>
                                <div class="text-primary small">{{ $item->karyawan->jabatan ?? '' }}</div>
                            </td>
                            <td style="font-size:.8rem">
                                @if($item->karyawan?->struktur)
                                    <div><strong>{{ $item->karyawan->struktur->nama_department }}</strong></div>
                                    <div class="text-muted">↳ {{ $item->karyawan->struktur->nama_divisi }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- JAM MASUK & FOTO --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->foto_masuk_url)
                                        <img src="{{ $item->foto_masuk_url }}" alt="Foto Masuk" 
                                             class="rounded-circle border shadow-sm" 
                                             style="width: 32px; height: 32px; object-fit: cover; cursor: pointer;"
                                             onclick="previewFoto('{{ $item->foto_masuk_url }}', 'Foto Masuk: {{ $item->karyawan->nama ?? '' }} ({{ $item->jam_masuk }})')"
                                             title="Klik untuk perbesar">
                                    @endif
                                    <div>
                                        <span class="fw-semibold">{{ $item->jam_masuk ?? '-' }}</span>
                                        @if($item->jarak_masuk !== null)
                                            <div>
                                                <span class="badge bg-light text-dark border" style="font-size:0.68rem;">
                                                    <i class="bi bi-geo-alt text-primary"></i> {{ $item->jarak_masuk }}m
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- STATUS MASUK & APPROVAL --}}
                            <td>
                                {!! $item->status_approval_masuk_badge !!}
                                @if($item->alasan_terlambat)
                                    <div class="p-1 mt-1 bg-light border rounded small text-dark" style="font-size: 0.75rem;">
                                        <strong>Alasan:</strong> {{ $item->alasan_terlambat }}
                                    </div>
                                @endif
                            </td>

                            {{-- JAM KELUAR & FOTO --}}
                            <td>
                                @if($item->jam_keluar)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($item->foto_keluar_url)
                                            <img src="{{ $item->foto_keluar_url }}" alt="Foto Pulang" 
                                                 class="rounded-circle border shadow-sm" 
                                                 style="width: 32px; height: 32px; object-fit: cover; cursor: pointer;"
                                                 onclick="previewFoto('{{ $item->foto_keluar_url }}', 'Foto Pulang: {{ $item->karyawan->nama ?? '' }} ({{ $item->jam_keluar }})')"
                                                 title="Klik untuk perbesar">
                                        @endif
                                        <div>
                                            <span class="fw-semibold">{{ $item->jam_keluar }}</span>
                                            <div class="text-muted small">({{ $item->durasi_kerja ?? '-' }})</div>
                                            @if($item->jarak_keluar !== null)
                                                <span class="badge bg-light text-dark border" style="font-size:0.68rem;">
                                                    <i class="bi bi-geo-alt text-danger"></i> {{ $item->jarak_keluar }}m
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Belum Pulang</span>
                                @endif
                            </td>

                            {{-- STATUS PULANG & APPROVAL --}}
                            <td>
                                {!! $item->status_approval_pulang_badge !!}
                                @if($item->alasan_pulang_awal)
                                    <div class="p-1 mt-1 bg-light border rounded small text-dark" style="font-size: 0.75rem;">
                                        <strong>Alasan Pulang Awal:</strong> {{ $item->alasan_pulang_awal }}
                                    </div>
                                @endif
                            </td>

                            {{-- DENDA --}}
                            <td>
                                @if($item->kena_denda && $item->nominal_denda > 0)
                                    <span class="badge bg-danger">Rp {{ number_format($item->nominal_denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted small">Rp 0</span>
                                @endif
                            </td>

                            {{-- AKSI & APPROVAL ADMIN --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    {{-- AKSI APPROVAL KETERLAMBATAN --}}
                                    @if($item->status_approval_masuk === 'Menunggu Review')
                                        <div class="btn-group btn-group-sm w-100">
                                            {{-- FORM ACC IZIN --}}
                                            <form action="{{ route('absensi.approve-terlambat', $item->id) }}" method="POST" class="d-inline flex-fill">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="aksi" value="izinkan">
                                                <button type="submit" class="btn btn-sm btn-success w-100 py-1"
                                                        title="Terima izin (Bebas Denda)">
                                                    <i class="bi bi-check-lg"></i> ACC Izin
                                                </button>
                                            </form>
                                            {{-- FORM TOLAK (KENA DENDA) --}}
                                            <form action="{{ route('absensi.approve-terlambat', $item->id) }}" method="POST" class="d-inline flex-fill">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="aksi" value="tolak">
                                                <input type="hidden" name="nominal_denda" value="50000">
                                                <button type="submit" class="btn btn-sm btn-danger w-100 py-1"
                                                        onclick="return confirm('Tolak izin keterlambatan dan kenakan denda Rp 50.000?')"
                                                        title="Tolak izin (Kena Denda)">
                                                    <i class="bi bi-x-lg"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- AKSI APPROVAL PULANG AWAL --}}
                                    @if($item->status_approval_pulang === 'Menunggu Review')
                                        <div class="btn-group btn-group-sm w-100">
                                            <form action="{{ route('absensi.approve-pulang', $item->id) }}" method="POST" class="d-inline flex-fill">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="aksi" value="izinkan">
                                                <button type="submit" class="btn btn-sm btn-info text-white w-100 py-1" title="ACC Pulang Awal">
                                                    ACC Pulang
                                                </button>
                                            </form>
                                            <form action="{{ route('absensi.approve-pulang', $item->id) }}" method="POST" class="d-inline flex-fill">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="aksi" value="tolak">
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1"
                                                        onclick="return confirm('Tolak kepulangan awal?')" title="Tolak Pulang Awal">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- EDIT & HAPUS BIASA --}}
                                    <div class="d-flex gap-1 justify-content-center mt-1">
                                        <a href="{{ route('absensi.edit', $item->id) }}" class="btn btn-outline-warning btn-sm py-0 px-2" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('absensi.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2"
                                                    onclick="return confirm('Yakin hapus data presensi ini?')" title="Hapus Data">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div style="font-size:2rem">📭</div>
                                Belum ada data absensi untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($absensi->hasPages())
        <div class="card-footer bg-white">{{ $absensi->links() }}</div>
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
<script>
function previewFoto(url, caption) {
    document.getElementById('modalPreviewImg').src = url;
    document.getElementById('modalPreviewCaption').textContent = caption || '';
    const modal = new bootstrap.Modal(document.getElementById('modalPreviewFoto'));
    modal.show();
}
</script>
@endpush