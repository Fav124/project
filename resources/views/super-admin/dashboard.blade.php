@extends('layouts.app')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Otoritas & Kendali Sistem')

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-primary ">
                            <span class="mdi mdi-account-group icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Total Pengguna</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-warning">
                            <span class="mdi mdi-account-clock icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Menunggu Approval</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-success">
                            <span class="mdi mdi-account-check icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Akun Aktif</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-danger">
                            <span class="mdi mdi-account-remove icon-item"></span>
                        </div>
                    </div>
                </div>
                <h6 class="text-muted font-weight-normal">Akun Ditolak</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <x-ui.card title="Volume Pendaftaran Baru">
            <div id="regChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <x-ui.card title="Proporsi Jabatan">
            <div id="roleChart" style="min-height: 350px;"></div>
        </x-ui.card>
    </div>
</div>

<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <x-ui.card title="Antrian Approval Akun">
            <x-slot name="header">
                <h4 class="card-title">Antrian Approval Akun</h4>
                <a href="{{ route('super-admin.users.index', ['status' => 'pending']) }}" class="btn btn-outline-primary btn-sm">Kelola Antrian</a>
            </x-slot>
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingUsers as $user)
                        <tr>
                            <td class="text-white">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <form action="{{ route('super-admin.users.approve', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-xs">Setujui</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada antrian tertunda</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>
    <div class="col-md-5 grid-margin stretch-card">
        <x-ui.card title="System Health">
            <div class="preview-list">
                <div class="preview-item border-bottom">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-info">
                            <i class="mdi mdi-language-php"></i>
                        </div>
                    </div>
                    <div class="preview-item-content">
                        <p class="preview-subject mb-1">PHP Version</p>
                        <p class="text-muted text-small mb-0">{{ PHP_VERSION }}</p>
                    </div>
                </div>
                <div class="preview-item border-bottom">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-primary">
                            <i class="mdi mdi-laravel"></i>
                        </div>
                    </div>
                    <div class="preview-item-content">
                        <p class="preview-subject mb-1">Laravel Framework</p>
                        <p class="text-muted text-small mb-0">{{ app()->version() }}</p>
                    </div>
                </div>
                <div class="preview-item">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-success">
                            <i class="mdi mdi-database"></i>
                        </div>
                    </div>
                    <div class="preview-item-content">
                        <p class="preview-subject mb-1">Database</p>
                        <p class="text-muted text-small mb-0">Connected</p>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Registration Trends Chart
    const regOptions = {
        series: [{
            name: 'Pendaftar Baru',
            data: @json($registrationTrends->pluck('count'))
        }],
        chart: { type: 'bar', height: 350, theme: 'dark', background: 'transparent', toolbar: { show: false } },
        colors: ['#00d25b'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
        xaxis: {
            categories: @json($registrationTrends->pluck('date')),
            labels: { style: { colors: '#6c7293' } }
        },
        yaxis: { labels: { style: { colors: '#6c7293' } } },
        grid: { borderColor: '#191c24' }
    };
    new ApexCharts(document.querySelector("#regChart"), regOptions).render();

    // User Composition Chart
    const roleOptions = {
        series: [{{ $stats['petugas'] }}, {{ $stats['admin'] }}],
        chart: { type: 'donut', height: 350, theme: 'dark', background: 'transparent' },
        labels: ['Petugas UKS', 'Administrator'],
        colors: ['#0090e7', '#ffab00'],
        legend: { position: 'bottom', labels: { colors: '#6c7293' } },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Aktif',
                            fontSize: '14px',
                            color: '#6c7293'
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#roleChart"), roleOptions).render();
</script>
@endpush
@endsection
