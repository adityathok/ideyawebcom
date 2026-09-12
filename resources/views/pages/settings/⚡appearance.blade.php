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
        <div class="rounded-xl border border-[#e3eaff] bg-[#f3f6ff] p-4">
            <p class="text-sm font-medium text-[#100f12]">Light mode</p>
            <p class="mt-1 text-sm text-[#65646e]">Dark mode telah dinonaktifkan. Website menggunakan tema terang saja.</p>
        </div>
    </x-pages::settings.layout>
</section>
