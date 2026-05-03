@extends('layouts.app')

@section('title', 'Hasil Pencarian')
@section('page_title', 'Hasil Pencarian Global')
@section('page_description', 'Hasil pencarian untuk kata kunci: "' . $query . '"')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Search</li>
@endsection

@section('content')
<div class="row">
    @forelse($results as $type => $items)
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-primary">{{ Str::headline($type) }} ({{ count($items) }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($items as $item)
                    <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $item['title'] }}</h6>
                                <p class="mb-0 text-sm text-muted">{{ $item['description'] }}</p>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-search fs-1 mb-3 d-block text-muted"></i>
        <h4>Tidak ada hasil ditemukan.</h4>
        <p class="text-muted">Coba kata kunci lain atau periksa ejaan Anda.</p>
    </div>
    @endforelse
</div>
@endsection
