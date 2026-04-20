<div class="filter-bar" style="background: var(--bg-main); padding: 16px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px;">
    <form {{ $attributes->merge(['method' => 'GET', 'class' => 'flex items-center gap-3 flex-wrap w-full']) }}>
        {{ $slot }}
    </form>
</div>
