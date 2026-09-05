<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Tema terang aktif permanen')">
        <div class="rounded-xl border border-[#d3cec6] bg-[#f5f1ec] p-4">
            <p class="text-sm font-medium text-[#111111]">Light mode</p>
            <p class="mt-1 text-sm text-[#626260]">Dark mode telah dinonaktifkan. Website menggunakan tema terang saja.</p>
        </div>
    </x-pages::settings.layout>
</section>
