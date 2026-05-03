@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page_title', 'Master Data Kelas')
@section('page_description', 'Kelola daftar kelas untuk pengelompokan santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Kelas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Kelas Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('kelas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="Misal: 10A, 11B" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Kelas</button>
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
                                <th>Nama Kelas</th>
                                <th>Jumlah Santri</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $k)
                            <tr>
                                <td class="fw-bold">{{ $k->nama_kelas }}</td>
                                <td>{{ $k->santris_count }} Santri</td>
                                <td>
                                    <a href="{{ route('kelas.show', $k->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
