@props([
    'columns' => '1fr 1fr',
    'gap' => '16px',
])

<div style="display:grid; grid-template-columns:{{ $columns }}; gap:{{ $gap }};">
    {{ $slot }}
</div>
