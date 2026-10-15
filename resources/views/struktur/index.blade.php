@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Struktur</h5>
            <a href="{{ route('struktur.create') }}" class="btn btn-primary btn-sm">+ Tambah Struktur</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($strukturs as $struktur)
                        <tr>
                            <td>{{ $struktur->nama_struktur }}</td>
                            <td>{{ $struktur->deskripsi }}</td>
                            <td class="text-center">
                                <a href="{{ route('struktur.edit', $struktur->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('struktur.destroy', $struktur->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus struktur ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data struktur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection