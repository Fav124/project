@extends('layouts.app')

@section('title', 'Detail Kelas')
@section('page_title', 'Data Santri: ' . $kela->nama_kelas)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Kelas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Ringkasan Kelas</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Santri</span>
                    <span class="badge bg-primary">{{ $kela->santris->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total Jurusan</span>
                    <span class="badge bg-info">{{ $groupedSantri->count() }}</span>
                </div>
                <hr>
                <a href="{{ route('kelas.index') }}" class="btn btn-light w-100">Kembali</a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Santri per Jurusan</h4>
            </div>
            <div class="card-body">
                @forelse($groupedSantri as $jurusanName => $santris)
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-3 text-primary">
                        <i class="bi bi-mortarboard me-2"></i> {{ $jurusanName }} 
                        <small class="text-muted">({{ $santris->count() }} Santri)</small>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>NIS</th>
                                    <th>Nama Lengkap</th>
                                    <th>JK</th>
                                    <th>Status</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($santris as $index => $santri)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $santri->nis }}</code></td>
                                    <td class="fw-bold">{{ $santri->nama_lengkap }}</td>
                                    <td>{{ $santri->jenis_kelamin }}</td>
                                    <td>
                                        <span class="badge bg-{{ $santri->status_santri === 'aktif' ? 'success' : 'secondary' }} small">
                                            {{ strtoupper($santri->status_santri) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('santri.show', $santri->id) }}" class="btn btn-sm btn-outline-info p-1 px-2" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Tidak ada santri di kelas ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
