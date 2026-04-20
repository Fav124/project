@props(['name'])

<select 
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'form-input']) }}
>
    {{ $slot }}
</select>
