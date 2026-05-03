@extends('layouts.app')

@section('title', 'Data Jurusan')
@section('page_title', 'Master Data Jurusan')
@section('page_description', 'Kelola daftar jurusan / konsentrasi keahlian santri.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Jurusan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Jurusan Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('jurusan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control" placeholder="Misal: IPA, IPS, RPL" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Jurusan</button>
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
                                <th>Nama Jurusan</th>
                                <th>Jumlah Santri</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurusans as $j)
                            <tr>
                                <td class="fw-bold">{{ $j->nama_jurusan }}</td>
                                <td>{{ $j->santris_count }} Santri</td>
                                <td>
                                    <form action="{{ route('jurusan.destroy', $j->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jurusan ini?')">
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
