@props(['type' => 'info', 'message' => '', 'dismissible' => true])

<div class="alert alert-{{ $type }} {{ $dismissible ? 'alert-dismissible' : '' }} show fade" role="alert">
    @if($type === 'success')
        <i class="bi bi-check-circle me-2"></i>
    @elseif($type === 'danger')
        <i class="bi bi-exclamation-triangle me-2"></i>
    @elseif($type === 'warning')
        <i class="bi bi-exclamation-circle me-2"></i>
    @else
        <i class="bi bi-info-circle me-2"></i>
    @endif

    {{ $message ?: $slot }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
