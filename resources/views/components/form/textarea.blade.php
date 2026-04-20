@props(['name', 'value' => ''])

<textarea 
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'form-input', 'rows' => 3]) }}
>{{ old($name, $value) }}</textarea>
