@extends('layouts.app') {{-- sesuaikan dengan layout project kamu --}}

@section('content')
<style>
    .form-karyawan-wrapper {
        background: #f4f6f9;
        padding: 2rem 0;
    }

    .form-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        border: 1px solid #edf0f5;
    }

    .form-card-header {
        background: linear-gradient(135deg, #4e73df 0%, #375bce 100%);
        padding: 1.75rem 2rem;
        color: #fff;
    }

    .form-card-header h4 {
        margin: 0;
        font-weight: 600;
    }

    .form-card-header p {
        margin: 0.25rem 0 0;
        font-size: 0.875rem;
        opacity: 0.85;
    }

    .form-card-body {
        padding: 2rem;
    }

    .section-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #4e73df;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eef1fb;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-block {
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #33415c;
        margin-bottom: 0.35rem;
    }

    .form-label .text-danger {
        font-weight: 700;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid #dfe3ea;
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
    }

    .input-group-text {
        border-radius: 10px 0 0 10px;
        background: #eef1fb;
        border: 1px solid #dfe3ea;
        color: #4e73df;
    }

    .file-drop {
        border: 1.5px dashed #c3cbe0;
        border-radius: 10px;
        padding: 0.9rem;
        background: #fafbff;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .file-drop:hover {
        border-color: #4e73df;
        background: #f3f6ff;
    }

    .form-text {
        font-size: 0.78rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, #4e73df 0%, #375bce 100%);
        border: none;
        border-radius: 10px;
        padding: 0.65rem 2rem;
        font-weight: 600;
        color: #fff;
        transition: opacity 0.2s;
    }

    .btn-submit:hover {
        opacity: 0.92;
        color: #fff;
    }

    .btn-cancel {
        border-radius: 10px;
        padding: 0.65rem 1.75rem;
        font-weight: 600;
        border: 1px solid #dfe3ea;
        color: #33415c;
    }

    .form-footer {
        border-top: 1px solid #edf0f5;
        padding-top: 1.5rem;
        margin-top: 0.5rem;
    }
</style>

<div class="form-karyawan-wrapper">
    <div class="container">
        <div class="form-card mx-auto" style="max-width: 960px;">

            <div class="form-card-header">
                <h4><i class="bi bi-person-badge me-2"></i>Tambah Data Karyawan</h4>
                <p>Lengkapi seluruh data di bawah ini dengan benar dan sesuai dokumen resmi.</p>
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

                <form method="POST" action="{{ route('karyawan.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- =============== DATA PRIBADI =============== --}}
                    <div class="section-block">
                        <div class="section-title"><i class="bi bi-person-lines-fill"></i> Data Pribadi</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                       id="nama" name="nama" value="{{ old('nama') }}"
                                       placeholder="Masukkan nama lengkap" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                       id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror"
                                          id="alamat" name="alamat" rows="2"
                                          placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                        id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-Laki" {{ old('jenis_kelamin') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="agama" class="form-label">Agama <span class="text-danger">*</span></label>
                                <select class="form-select @error('agama') is-invalid @enderror"
                                        id="agama" name="agama" required>
                                    <option value="">Pilih Agama</option>
                                    @foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                                @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- =============== DATA PEKERJAAN =============== --}}
                    <div class="section-block">
                        <div class="section-title"><i class="bi bi-briefcase-fill"></i> Data Pekerjaan</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" list="daftarJabatan" class="form-control @error('jabatan') is-invalid @enderror"
                                       id="jabatan" name="jabatan" value="{{ old('jabatan') }}"
                                       placeholder="Contoh: Backend Developer / HR Specialist / Manager" required>
                                <datalist id="daftarJabatan">
                                    <option value="Manager">
                                    <option value="Supervisor">
                                    <option value="Staff">
                                    <option value="Senior Backend Developer">
                                    <option value="Frontend Developer">
                                    <option value="Network Engineer">
                                    <option value="HR Specialist">
                                    <option value="Finance Officer">
                                    <option value="Logistik Staff">
                                </datalist>
                                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="gaji" class="form-label">Gaji <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" min="0" class="form-control @error('gaji') is-invalid @enderror"
                                           id="gaji" name="gaji" value="{{ old('gaji') }}"
                                           placeholder="0" required>
                                    @error('gaji') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="struktur_id" class="form-label">Departemen &amp; Divisi <span class="text-danger">*</span></label>
                                <select class="form-select @error('struktur_id') is-invalid @enderror"
                                        id="struktur_id" name="struktur_id" required>
                                    <option value="">Pilih Departemen / Divisi</option>
                                    @foreach ($strukturs ?? [] as $struktur)
                                        <option value="{{ $struktur->id }}" {{ old('struktur_id') == $struktur->id ? 'selected' : '' }}>
                                            {{ $struktur->nama_department }} &mdash; {{ $struktur->nama_divisi }} {{ $struktur->nama_biro ? "({$struktur->nama_biro})" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('struktur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status Karyawan <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="riwayat_karir" class="form-label">Riwayat Karir</label>
                                <textarea class="form-control @error('riwayat_karir') is-invalid @enderror"
                                          id="riwayat_karir" name="riwayat_karir" rows="2"
                                          placeholder="Ringkasan pengalaman kerja / riwayat karir sebelumnya">{{ old('riwayat_karir') }}</textarea>
                                @error('riwayat_karir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- =============== STATUS KONTRAK & HUBUNGAN KERJA =============== --}}
                    <div class="section-block">
                        <div class="section-title"><i class="bi bi-file-earmark-ruled-fill"></i> Ikatan Kerja &amp; Masa Kontrak</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tipe_kontrak" class="form-label">Tipe Ikatan Kerja <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipe_kontrak') is-invalid @enderror"
                                        id="tipe_kontrak" name="tipe_kontrak" required onchange="toggleKontrakFields()">
                                    <option value="PKWTT" {{ old('tipe_kontrak', 'PKWTT') == 'PKWTT' ? 'selected' : '' }}>
                                        PKWTT (Karyawan Tetap)
                                    </option>
                                    <option value="PKWT" {{ old('tipe_kontrak') == 'PKWT' ? 'selected' : '' }}>
                                        PKWT (Karyawan Kontrak)
                                    </option>
                                    <option value="Outsourcing" {{ old('tipe_kontrak') == 'Outsourcing' ? 'selected' : '' }}>
                                        Outsourcing (Alih Daya / Vendor)
                                    </option>
                                </select>
                                @error('tipe_kontrak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" id="boxVendor" style="display: none;">
                                <label for="nama_vendor" class="form-label">Nama PT / Vendor Outsourcing <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_vendor') is-invalid @enderror"
                                       id="nama_vendor" name="nama_vendor" value="{{ old('nama_vendor') }}"
                                       placeholder="Contoh: PT Karya Mandiri Outsourcing">
                                @error('nama_vendor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" id="boxMulaiKontrak">
                                <label for="tanggal_mulai_kontrak" class="form-label">Tanggal Mulai Bekerja / Kontrak</label>
                                <input type="date" class="form-control @error('tanggal_mulai_kontrak') is-invalid @enderror"
                                       id="tanggal_mulai_kontrak" name="tanggal_mulai_kontrak" value="{{ old('tanggal_mulai_kontrak') }}">
                                @error('tanggal_mulai_kontrak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" id="boxSelesaiKontrak" style="display: none;">
                                <label for="tanggal_selesai_kontrak" class="form-label">Tanggal Habis Kontrak <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_selesai_kontrak') is-invalid @enderror"
                                       id="tanggal_selesai_kontrak" name="tanggal_selesai_kontrak" value="{{ old('tanggal_selesai_kontrak') }}">
                                <div class="form-text">Digunakan sistem untuk mendeteksi masa kedaluwarsa kontrak.</div>
                                @error('tanggal_selesai_kontrak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- =============== AKUN LOGIN SISTEM =============== --}}
                    <div class="section-block">
                        <div class="section-title"><i class="bi bi-shield-lock-fill"></i> Akun Login Sistem (Untuk Presensi Mandiri)</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email Login <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       placeholder="karyawan@company.com" required>
                                <div class="form-text">Digunakan karyawan untuk masuk ke portal presensi.</div>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" placeholder="Min. 6 karakter" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password', this)" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                           id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_confirmation', this)" tabindex="-1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- =============== DOKUMEN =============== --}}
                    <div class="section-block mb-0">
                        <div class="section-title"><i class="bi bi-folder2-open"></i> Dokumen Pendukung</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kontrak_kerja" class="form-label">Kontrak Kerja</label>
                                <div class="file-drop">
                                    <input type="file" class="form-control @error('kontrak_kerja') is-invalid @enderror"
                                           id="kontrak_kerja" name="kontrak_kerja" accept=".pdf,.doc,.docx">
                                    <div class="form-text">Format PDF/DOC, maks. 2MB.</div>
                                </div>
                                @error('kontrak_kerja') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="dokumen_legal" class="form-label">Dokumen Legal</label>
                                <div class="file-drop">
                                    <input type="file" class="form-control @error('dokumen_legal') is-invalid @enderror"
                                           id="dokumen_legal" name="dokumen_legal" accept=".pdf,.doc,.docx">
                                    <div class="form-text">KTP, ijazah, atau dokumen legal lain. Maks. 2MB.</div>
                                </div>
                                @error('dokumen_legal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('karyawan.index') }}" class="btn btn-cancel">Batal</a>
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleKontrakFields() {
        const tipe = document.getElementById('tipe_kontrak').value;
        const boxVendor = document.getElementById('boxVendor');
        const boxSelesai = document.getElementById('boxSelesaiKontrak');
        const inputVendor = document.getElementById('nama_vendor');
        const inputSelesai = document.getElementById('tanggal_selesai_kontrak');

        if (tipe === 'Outsourcing') {
            boxVendor.style.display = 'block';
            boxSelesai.style.display = 'block';
            inputVendor.setAttribute('required', 'required');
            inputSelesai.setAttribute('required', 'required');
        } else if (tipe === 'PKWT') {
            boxVendor.style.display = 'none';
            boxSelesai.style.display = 'block';
            inputVendor.removeAttribute('required');
            inputSelesai.setAttribute('required', 'required');
        } else {
            // PKWTT (Tetap)
            boxVendor.style.display = 'none';
            boxSelesai.style.display = 'none';
            inputVendor.removeAttribute('required');
            inputSelesai.removeAttribute('required');
        }
    }
    document.addEventListener('DOMContentLoaded', toggleKontrakFields);

    function togglePwd(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endpush