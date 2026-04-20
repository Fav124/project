@props(['name', 'label' => null])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    
    {{ $slot }}

    @error($name)
        <div style="font-size: 12px; color: var(--danger); margin-top: 6px; font-weight: 600;">
            <i class="fas fa-circle-exclamation"></i> {{ $message }}
        </div>
    @enderror
</div>
