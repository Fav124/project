@props([
    'title' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => trim('glass-card ' . $class)]) }}>
    @if($title || isset($header))
        <div class="card-header">
            @if(isset($header))
                {{ $header }}
            @else
                <h2>{{ $title }}</h2>
            @endif
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="pagination-wrap" style="padding: 24px 32px; border-top: 1px solid var(--border); background: var(--bg-main);">
            {{ $footer }}
        </div>
    @endif
</div>
