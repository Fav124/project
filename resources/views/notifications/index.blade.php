@extends('layouts.app')

@section('title', 'Notifikasi Sistem')
@section('page_title', 'Semua Notifikasi')
@section('page_description', 'Tinjau semua peringatan stok, kesehatan, dan aktivitas sistem.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Riwayat Notifikasi</h4>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Tandai Semua Dibaca</button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item list-group-item-action py-3 {{ $notification->read_at ? 'opacity-75' : 'bg-light-primary' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-{{ $notification->data['type'] == 'kadaluarsa' ? 'danger' : 'warning' }} text-white rounded-circle p-3 me-4">
                                        <i class="bi bi-bell-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold">{{ $notification->data['title'] }}</h5>
                                        <p class="mb-1 text-secondary">
                                            @if(isset($notification->data['obats']))
                                                @foreach(array_slice($notification->data['obats'], 0, 3) as $o)
                                                    <span class="badge bg-light-secondary text-dark me-1">{{ $o['nama_obat'] }}</span>
                                                @endforeach
                                                @if(count($notification->data['obats']) > 3)
                                                    <small>...dan {{ count($notification->data['obats']) - 3 }} lainnya</small>
                                                @endif
                                            @endif
                                        </p>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $notification->created_at->translatedFormat('d F Y, H:i') }} ({{ $notification->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Tandai Baca</button>
                                    </form>
                                @else
                                    <span class="badge bg-light-success text-success"><i class="bi bi-check-all"></i> Dibaca</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted">Tidak ada riwayat notifikasi.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
