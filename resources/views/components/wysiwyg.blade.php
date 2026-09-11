@props([
    'label' => null,
    'description' => null,
    'placeholder' => 'Tulis di sini...',
])

@php
    $property = $attributes->wire('model')->value();
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'grid gap-2']) }}>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div
        wire:ignore
        x-data="wysiwyg({ model: @js($property), placeholder: @js($placeholder) })"
        class="wysiwyg"
    >
        <div x-ref="editor"></div>
    </div>

    @if ($description)
        <flux:description>{{ $description }}</flux:description>
    @endif

    <flux:error name="{{ $property }}" />
</div>
