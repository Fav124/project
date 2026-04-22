@props([
    'title' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => trim('card ' . $class)]) }}>
    <div class="card-body">
        @if($title || isset($header))
            <div class="d-flex flex-row justify-content-between mb-4">
                @if(isset($header))
                    {{ $header }}
                @else
                    <h4 class="card-title">{{ $title }}</h4>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if(isset($footer))
            <div class="mt-4 pt-3 border-top border-secondary">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
