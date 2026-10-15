@extends('layouts.app') {{-- sesuaikan dengan layout project kamu --}}

@section('content')
<style>
    .form-karyawan-wrapper { background: #f4f6f9; padding: 2rem 0; }
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        border: 1px solid #edf0f5;
    }
    .form-card-header {
        background: linear-gradient(135deg, #4e73df 0%, #375bce 100%);
        padding: 1.75rem 2rem;
        color: #fff;
    }
    .form-card-header h4 { margin: 0; font-weight: 600; }
    .form-card-header p { margin: 0.25rem 0 0; font-size: 0.875rem; opacity: 0.85; }
    .form-card-body { padding: 2rem; }
    .form-label { font-weight: 600; font-size: 0.85rem; color: #33415c; margin-bottom: 0.35rem; }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #dfe3ea;
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.15);
    }
    .btn-submit {
        background: linear-gradient(135deg, #4e73df 0%, #375bce 100%);
        border: none;
        border-radius: 10px;
        padding: 0.65rem 2rem;
        font-weight: 600;
        color: #fff;
    }
    .btn-submit:hover { opacity: 0.92; color: #fff; }
    .btn-cancel {
        border-radius: 10px;
        padding: 0.65rem 1.75rem;
        font-weight: 600;
        border: 1px solid #dfe3ea;
        color: #33415c;
    }
    .form-footer { border-top: 1px solid #edf0f5; padding-top: 1.5rem; margin-top: 1.5rem; }
</style>

<div class="form-karyawan-wrapper">
    <div class="container">
        <div class="form-card mx-auto" style="max-width: 640px;">

            <div class="form-card-header">
                <h4><i class="bi bi-briefcase-fill me-2"></i>Tambah Struktur</h4>
                <p>Isi nama struktur baru beserta keterangannya.</p>
            </div>

            <div class="form-card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('struktur.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="nama_struktur" class="form-label">Nama Struktur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_struktur') is-invalid @enderror"
                                   id="nama_struktur" name="nama_struktur" value="{{ old('nama_struktur') }}"
                                   placeholder="Contoh: IT, Produksi" required>
                            @error('nama_struktur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi" name="deskripsi" rows="3"
                                      placeholder="Deskripsi singkat tugas/wewenang jabatan (opsional)">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 form-footer d-flex justify-content-end gap-2">
                            <a href="{{ route('struktur.index') }}" class="btn btn-cancel">Batal</a>
                            <button type="submit" class="btn btn-submit">Simpan</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection