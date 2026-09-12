@props([
    'expandable' => false,
    'expanded' => true,
    'heading' => null,
])

<?php if ($expandable && $heading): ?>

<ui-disclosure
    {{ $attributes->class('group/disclosure') }}
    @if ($expanded === true) open @endif
    data-flux-navlist-group
>
    <button
        type="button"
        class="group/disclosure-button mb-[2px] flex h-10 w-full items-center rounded-lg text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12] lg:h-8"
    >
        <div class="ps-3 pe-4">
            <flux:icon.chevron-down class="hidden size-3! group-data-open/disclosure-button:block" />
            <flux:icon.chevron-right class="block size-3! group-data-open/disclosure-button:hidden" />
        </div>

        <span class="text-sm font-medium leading-none">{{ $heading }}</span>
    </button>

    <div class="relative hidden space-y-[2px] ps-7 data-open:block" @if ($expanded === true) data-open @endif>
        <div class="absolute inset-y-[3px] start-0 ms-4 w-px bg-[#e3eaff]"></div>

        {{ $slot }}
    </div>
</ui-disclosure>

<?php elseif ($heading): ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    <div class="px-1 py-2">
        <div class="text-xs font-medium leading-none tracking-widest uppercase text-[#65646e]">{{ $heading }}</div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>

<?php else: ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    {{ $slot }}
</div>

<?php endif; ?>
