<nav class="navbar navbar-expand navbar-light ">
    <div class="container-fluid">
        <a href="#" class="burger-btn d-block">
            <i class="bi bi-justify fs-3"></i>
        </a>

        <form action="{{ route('search') }}" method="GET" class="ms-3 d-none d-md-block flex-grow-1" style="max-width: 400px;">
            <div class="input-group">
                <span class="input-group-text border-0 bg-transparent"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-0 bg-light-secondary rounded-pill" placeholder="Cari santri, obat, atau data lainnya..." value="{{ request('q') }}">
            </div>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @if(Auth::check())
                <li class="nav-item dropdown me-3">
                    @php
                        $unreadCount = auth()->user()->unreadNotifications->count();
                        $totalNotif = $unreadCount + ($pendingApprovalsCount ?? 0);
                    @endphp
                    <a class="nav-link active dropdown-toggle" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class='bi bi-bell bi-sub fs-4 text-gray-600'></i>
                        @if($totalNotif > 0)
                            <span class="badge badge-notification bg-danger">{{ $totalNotif }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="dropdownMenuButton" style="min-width: 350px;">
                        <li>
                            <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                Notifikasi
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-xs text-primary text-decoration-none">Tandai Baca Semua</button>
                                    </form>
                                @endif
                            </h6>
                        </li>
                        
                        {{-- Approvals (Priority) --}}
                        @if($pendingApprovalsCount > 0)
                            <li>
                                <a href="{{ route('approvals.index') }}" class="dropdown-item bg-light-primary">
                                    <div class="d-flex align-items-center py-2">
                                        <div class="notification-icon bg-primary text-white rounded-circle p-2 me-3">
                                            <i class="bi bi-person-check-fill"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Persetujuan Pending</h6>
                                            <p class="mb-0 text-sm">Ada {{ $pendingApprovalsCount }} permintaan butuh tinjauan.</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif

                        {{-- System Notifications --}}
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <li>
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <div class="d-flex align-items-center py-2">
                                            <div class="notification-icon bg-{{ $notification->data['type'] == 'kadaluarsa' ? 'danger' : 'warning' }} text-white rounded-circle p-2 me-3">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-wrap" style="max-width: 250px;">{{ $notification->data['title'] }}</h6>
                                                <p class="mb-0 text-sm text-muted">{{ \Carbon\Carbon::parse($notification->data['created_at'])->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            </li>
                        @empty
                            @if($pendingApprovalsCount == 0)
                                <li><a class="dropdown-item text-center py-4 text-muted">Belum ada notifikasi baru</a></li>
                            @endif
                        @endforelse
                        
                        @if($unreadCount > 0 || $pendingApprovalsCount > 0)
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center text-sm text-primary" href="{{ route('notifications.index') }}">Lihat Semua Notifikasi</a></li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>

            @if(Auth::check())
            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-menu d-flex">
                        <div class="user-name text-end me-3">
                            <h6 class="mb-0 text-gray-600">{{ Auth::user()->name }}</h6>
                            <p class="mb-0 text-sm text-gray-600">{{ Str::headline(Auth::user()->role->value) }}</p>
                        </div>
                        <div class="user-img d-flex align-items-center">
                            <div class="avatar avatar-md">
                                <img src="{{ asset('assets/images/faces/1.jpg') }}">
                            </div>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li>
                        <h6 class="dropdown-header">Halo, {{ Auth::user()->name }}!</h6>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="icon-mid bi bi-person me-2"></i> Profil Saya</a></li>
                    @if(Auth::user()->role->value === 'super_admin')
                    <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="icon-mid bi bi-gear me-2"></i> Pengaturan</a></li>
                    @endif
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i
                                class="icon-mid bi bi-box-arrow-left me-2"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary ms-auto">Login Petugas</a>
            @endif
        </div>
    </div>
</nav>
