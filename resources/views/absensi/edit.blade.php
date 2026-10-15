@extends('layouts.app')

@section('page-title', 'Edit Absensi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold">Edit Absensi</h6>
                <small class="text-muted">{{ $absensi->karyawan->nama }} &mdash; {{ $absensi->tanggal->format('d M Y') }}</small>
            </div>
            <div class="card-body">
                <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Kehadiran</label>
                        <select name="status_kehadiran" class="form-select @error('status_kehadiran') is-invalid @enderror">
                            @foreach(['Hadir','Terlambat','Izin','Sakit','Alpha','Lembur'] as $s)
                                <option value="{{ $s }}" {{ $absensi->status_kehadiran == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('status_kehadiran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Masuk</label>
                            <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $absensi->jam_masuk) }}"
                                   class="form-control @error('jam_masuk') is-invalid @enderror" required>
                            @error('jam_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Keluar</label>
                            <input type="time" name="jam_keluar" value="{{ old('jam_keluar', $absensi->jam_keluar) }}"
                                   class="form-control @error('jam_keluar') is-invalid @enderror">
                            @error('jam_keluar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $absensi->keterangan) }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">Simpan Perubahan</button>
                        <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection