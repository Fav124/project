@props([
    'message' => 'Belum ada data yang tersedia saat ini.',
    'colspan' => null,
])

@if($colspan)
    <tr>
        <td colspan="{{ $colspan }}">
            <div class="text-center" style="padding: 64px 24px;">
                <div style="font-size: 48px; color: var(--border); margin-bottom: 16px;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div style="font-weight: 600; color: var(--text-muted);">{{ $message }}</div>
            </div>
        </td>
    </tr>
@else
    <div class="text-center" style="padding: 64px 24px;">
        <div style="font-size: 48px; color: var(--border); margin-bottom: 16px;">
            <i class="fas fa-folder-open"></i>
        </div>
        <div style="font-weight: 600; color: var(--text-muted);">{{ $message }}</div>
    </div>
@endif
