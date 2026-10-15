@extends('layouts.app')

@section('page-title', 'Catat Absensi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
    <li class="breadcrumb-item active">Catat Absensi</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">Formulir Pencatatan Absensi</h6>
                <small class="text-muted">Hari ini: {{ \Carbon\Carbon::now()->format('l, d F Y') }}</small>
            </div>
            <div class="card-body">
                <form action="{{ route('absensi.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Karyawan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_id" class="form-select @error('karyawan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama }}
                                    @if($k->struktur)
                                        ({{ $k->struktur->nama_department }} / {{ $k->struktur->nama_divisi }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($karyawan->isEmpty())
                            <div class="form-text text-warning">
                                Semua karyawan aktif sudah memiliki absensi hari ini.
                            </div>
                        @endif
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal"
                               value="{{ old('tanggal', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               class="form-control @error('tanggal') is-invalid @enderror" required>
                        <div class="form-text">Tidak bisa memilih tanggal di masa depan.</div>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Kehadiran --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Kehadiran <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @php
                                $statuses = [
                                    ['value'=>'Hadir',     'icon'=>'bi-check-circle-fill', 'color'=>'success', 'label'=>'Hadir'],
                                    ['value'=>'Terlambat', 'icon'=>'bi-clock-fill',        'color'=>'warning', 'label'=>'Terlambat'],
                                    ['value'=>'Izin',      'icon'=>'bi-file-earmark-text', 'color'=>'info',    'label'=>'Izin'],
                                    ['value'=>'Sakit',     'icon'=>'bi-bandaid-fill',       'color'=>'primary', 'label'=>'Sakit'],
                                    ['value'=>'Alpha',     'icon'=>'bi-x-circle-fill',      'color'=>'danger',  'label'=>'Alpha'],
                                    ['value'=>'Lembur',    'icon'=>'bi-moon-fill',          'color'=>'secondary','label'=>'Lembur'],
                                ];
                            @endphp
                            @foreach($statuses as $s)
                                <div class="col-4 col-md-2">
                                    <input type="radio" class="btn-check" name="status_kehadiran"
                                           id="status_{{ $s['value'] }}" value="{{ $s['value'] }}"
                                           {{ old('status_kehadiran', 'Hadir') == $s['value'] ? 'checked' : '' }}>
                                    <label class="btn btn-outline-{{ $s['color'] }} w-100 d-flex flex-column align-items-center py-2"
                                           for="status_{{ $s['value'] }}" style="font-size:.8rem">
                                        <i class="bi {{ $s['icon'] }} mb-1" style="font-size:1.2rem"></i>
                                        {{ $s['label'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('status_kehadiran')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jam Masuk & Keluar --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Masuk <span class="text-danger">*</span></label>
                            <input type="time" name="jam_masuk"
                                   value="{{ old('jam_masuk', $jamSekarang) }}"
                                   class="form-control @error('jam_masuk') is-invalid @enderror" required>
                            @error('jam_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Keluar <span class="text-muted">(opsional)</span></label>
                            <input type="time" name="jam_keluar"
                                   value="{{ old('jam_keluar') }}"
                                   class="form-control @error('jam_keluar') is-invalid @enderror">
                            <div class="form-text">Bisa diisi nanti saat karyawan pulang.</div>
                            @error('jam_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" rows="2" maxlength="500"
                                  class="form-control @error('keterangan') is-invalid @enderror"
                                  placeholder="Alasan izin/sakit, catatan terlambat, dll...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Absensi
                        </button>
                        <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection