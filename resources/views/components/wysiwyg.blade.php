@props([
    'label' => null,
    'description' => null,
    'placeholder' => 'Tulis di sini...',
    'uploadProperty' => null,
])

@php
    $property = $attributes->wire('model')->value();
    $mediaInputId = $uploadProperty ? 'wysiwyg-media-'.$property : null;
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'grid gap-2']) }}>
    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <div
        wire:ignore
        @if ($mediaInputId) @media-image-inserted.window="insertMediaImage($event.detail.url)" @endif
        x-data="wysiwyg({ model: @js($property), placeholder: @js($placeholder), mediaInputId: @js($mediaInputId) })"
        class="wysiwyg"
    >
        <div x-ref="editor"></div>
    </div>

    @if ($mediaInputId)
        {{-- Di luar `wire:ignore` supaya Livewire yang mengurus unggahannya; toolbar
             Quill hanya memicu klik lewat id ini. --}}
        <input
            id="{{ $mediaInputId }}"
            type="file"
            wire:model="{{ $uploadProperty }}"
            accept="image/*"
            class="hidden"
        />
        <div wire:loading wire:target="{{ $uploadProperty }}" class="text-xs text-[#65646e]">Mengunggah gambar...</div>
        <flux:error name="{{ $uploadProperty }}" />
    @endif

    @if ($description)
        <flux:description>{{ $description }}</flux:description>
    @endif

    <flux:error name="{{ $property }}" />
</div>
