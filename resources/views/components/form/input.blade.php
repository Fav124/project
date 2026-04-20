@props(['name', 'value' => ''])

<input 
    type="{{ $attributes->get('type', 'text') }}"
    id="{{ $name }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->merge(['class' => 'form-input']) }}
>
