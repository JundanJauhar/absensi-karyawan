@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Jabatan</h5>
            <a href="{{ route('jabatan.create') }}" class="btn btn-primary btn-sm">+ Tambah Jabatan</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatans as $jabatan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jabatan->jabatan }}</td>
                            <td>{{ $jabatan->deskripsi }}</td>
                            <td class="text-center">
                                <a href="{{ route('jabatan.edit', $jabatan->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('jabatan.destroy', $jabatan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jabatan ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data jabatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection