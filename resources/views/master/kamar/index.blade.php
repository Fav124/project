@extends('layouts.app')

@section('title', 'Data Kamar / Asrama')
@section('page_title', 'Master Data Kamar')
@section('page_description', 'Kelola daftar kamar dan asrama santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kamar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Kamar Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('kamar.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Kamar / Gedung</label>
                        <input type="text" name="nama_kamar" class="form-control" placeholder="Misal: Al-Fatih 01" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Kamar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kamar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kamars as $km)
                            <tr>
                                <td class="fw-bold">{{ $km->nama_kamar }}</td>
                                <td>
                                    <form action="{{ route('kamar.destroy', $km->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kamar ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
