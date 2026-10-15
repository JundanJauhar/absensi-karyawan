@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Karyawan</h5>
            <a href="{{ route('karyawan.create') }}" class="btn btn-primary btn-sm">+ Tambah Karyawan</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th>Nama</th>
                        <th><a href="{{ route('jabatan.index') }}">Jabatan</a></th>
                        <th>Alamat</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Agama</th>
                        <th>Gaji</th>
                        <th>Riwayat Karir</th>
                        <th><a href="{{ route('struktur.index') }}">Struktur</a></th>
                        <th>Ikatan Kerja &amp; Kontrak</th>
                        <th>Status</th>
                        <th>Dokumen</th>
                        <th style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($karyawan->currentPage() - 1) * $karyawan->perPage() }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->nama }}</div>
                                <div class="text-muted small">{{ $item->jenis_kelamin }} | {{ $item->agama }}</div>
                            </td>
                            <td>{{ $item->jabatan }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->jenis_kelamin }}</td>
                            <td>{{ $item->agama }}</td>
                            <td>{{ number_format($item->gaji, 0, ',', '.') }}</td>
                            <td>{{ $item->riwayat_karir ?? '-' }}</td>
                            <td>
                                @if($item->struktur)
                                    <div style="font-size:0.8rem; line-height:1.6">
                                        <div><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $item->struktur->nama_department }}</span></div>
                                        <div class="text-muted mt-1">↳ {{ $item->struktur->nama_divisi }}</div>
                                        @if($item->struktur->nama_biro)
                                        <div class="text-muted" style="padding-left:12px">↳ {{ $item->struktur->nama_biro }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {!! $item->status_kontrak_badge !!}
                            </td>
                            <td>
                                <span class="badge {{ $item->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                @if($item->kontrak_kerja)
                                    <div><a href="{{ asset('storage/' . $item->kontrak_kerja) }}" target="_blank" class="small"><i class="bi bi-file-earmark-pdf me-1"></i>Kontrak</a></div>
                                @endif
                                @if($item->dokumen_legal)
                                    <div><a href="{{ asset('storage/' . $item->dokumen_legal) }}" target="_blank" class="small text-secondary"><i class="bi bi-file-earmark me-1"></i>Legal</a></div>
                                @endif
                                @if(!$item->kontrak_kerja && !$item->dokumen_legal)
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('karyawan.show', $item->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('karyawan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('karyawan.destroy', $item->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $karyawan->links() }}
        </div>
    </div>

</div>
@endsection