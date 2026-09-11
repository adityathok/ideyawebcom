<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-white text-[#100f12] antialiased">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        // Hero halaman home berlatar gelap → nav harus terang selama belum di-scroll.
        $darkHero = request()->routeIs('home');
    @endphp
    <header
        x-data="{ scrolled: false, darkHero: @js($darkHero) }"
        x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => { scrolled = window.scrollY > 8; }, { passive: true })"
        :data-nav-light="(! scrolled && darkHero) ? 'true' : null"
        class="fixed top-0 z-40 w-full transition-all duration-300">
        <div
            :class="scrolled ? 'border-[#e3eaff] bg-white/85 shadow-[0_1px_12px_rgba(43,75,255,0.10)] backdrop-blur-md' : 'border-transparent bg-transparent'"
            class="border-b">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="nav-fg flex items-center gap-2.5">
                    <x-app-logo-icon class="size-7 text-[#100f12]" />
                    <span class="font-display text-[16px] font-medium tracking-[-0.16px] text-[#100f12]">{{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}</span>
                </a>
                <nav class="nav-fg hidden items-center gap-1 md:flex">
                    <a href="{{ route('home') }}" @if(request()->routeIs('home')) data-active @endif class="rounded-[12px] px-3.5 py-2 text-sm font-medium tracking-[-0.16px] transition {{ request()->routeIs('home') ? 'bg-[#f3f6ff] text-[#0a1589]' : 'text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]' }}">Beranda</a>
                    <a href="{{ route('home') }}#layanan" class="rounded-[12px] px-3.5 py-2 text-sm font-medium tracking-[-0.16px] text-[#65646e] transition hover:bg-[#f3f6ff] hover:text-[#100f12]">Layanan</a>
                    <a href="{{ route('home') }}#keunggulan" class="rounded-[12px] px-3.5 py-2 text-sm font-medium tracking-[-0.16px] text-[#65646e] transition hover:bg-[#f3f6ff] hover:text-[#100f12]">Keunggulan</a>
                    <a href="{{ route('home') }}#proses" class="rounded-[12px] px-3.5 py-2 text-sm font-medium tracking-[-0.16px] text-[#65646e] transition hover:bg-[#f3f6ff] hover:text-[#100f12]">Proses</a>
                    <a href="#kontak" class="rounded-[12px] px-3.5 py-2 text-sm font-medium tracking-[-0.16px] text-[#65646e] transition hover:bg-[#f3f6ff] hover:text-[#100f12]">Kontak</a>
                </nav>
                <div class="flex items-center gap-2">
                    <a href="#kontak" class="nav-cta hidden items-center justify-center rounded-[14px] bg-[#0a1589] px-5 py-2.5 text-sm font-medium leading-none text-white transition hover:bg-[#06105a] sm:inline-flex">Konsultasi Gratis</a>
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="nav-ghost inline-flex items-center justify-center rounded-[14px] border border-[#e3eaff] bg-white px-4 py-2.5 text-sm font-medium leading-none text-[#100f12] transition hover:bg-[#f3f6ff]">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="nav-ghost inline-flex items-center justify-center rounded-[14px] border border-[#e3eaff] bg-white px-4 py-2.5 text-sm font-medium leading-none text-[#100f12] transition hover:bg-[#f3f6ff]">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>{{ $slot ?? '' }} @yield('content')</main>

    <footer id="kontak" class="relative overflow-hidden bg-slate-900 text-white">
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-64 bg-[radial-gradient(60%_120%_at_50%_130%,rgba(43,75,255,0.55),transparent)]"></div>
        <div class="relative mx-auto max-w-7xl px-6 pb-10 pt-16 sm:px-8 sm:pt-20 lg:px-12 lg:pt-24">
            <div class="grid gap-12 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <x-app-logo-icon class="size-7 text-white" />
                        <span class="font-display text-[16px] font-medium tracking-[-0.16px] text-white">{{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}</span>
                    </div>
                    <p class="mt-4 max-w-xs text-sm leading-6 tracking-[-0.16px] text-white/75">{{ ($profile['tagline'] ?? '') ?: 'Developer Website & Web App — spesialis web app & berpengalaman di WordPress.' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-[-0.16px] text-white">Layanan</h3>
                    <ul class="mt-4 space-y-3 text-sm tracking-[-0.16px] text-white/75">
                        <li><a href="#layanan" class="transition hover:text-white">Web App Custom</a></li>
                        <li><a href="#layanan" class="transition hover:text-white">Website Company Profile</a></li>
                        <li><a href="#layanan" class="transition hover:text-white">WordPress Development</a></li>
                        <li><a href="#layanan" class="transition hover:text-white">Optimasi WordPress</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-[-0.16px] text-white">Kontak</h3>
                    <ul class="mt-4 space-y-3 text-sm tracking-[-0.16px] text-white/75">
                        @if(!empty($profile['email']))<li><a href="mailto:{{ $profile['email'] }}" class="transition hover:text-white hover:underline">{{ $profile['email'] }}</a></li>@endif
                        @if(!empty($profile['phone']))<li>{{ $profile['phone'] }}</li>@endif
                        @if(!empty($profile['address']))<li class="leading-6">{{ $profile['address'] }}</li>@endif
                    </ul>
                </div>
            </div>
            <div class="mt-12 flex flex-col items-center gap-4 border-t border-white/15 pt-8 text-sm tracking-[-0.16px] text-white/70 sm:flex-row sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ ($profile['company_name'] ?? '') ?: config('app.name') }}. All rights reserved.</p>
                <a href="#kontak" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-2.5 text-sm font-medium leading-none text-[#06105a] transition hover:bg-[#f3f6ff]">Konsultasi Gratis</a>
            </div>
        </div>
    </footer>
    @fluxScripts
</body>
</html>
