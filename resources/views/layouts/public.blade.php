<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-[#f5f1ec] antialiased">
    @php $profile = $profile ?? \App\Models\Setting::profile(); @endphp
    <header class="sticky top-0 z-40 w-full border-b border-[#d3cec6] bg-[#f5f1ec]/90 backdrop-blur">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <x-app-logo-icon class="size-7 text-[#111111]" />
                <span class="text-[15px] font-semibold tracking-tight text-[#111111]">{{ ($profile['company_name'] ?? '') ?: config('app.name', 'Ideya Webcom') }}</span>
            </a>
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-[#111111] text-white' : 'text-[#626260] hover:bg-[#ebe7e1] hover:text-[#111111]' }}">Beranda</a>
                <a href="{{ route('home') }}#layanan" class="rounded-lg px-3 py-2 text-sm font-medium text-[#626260] hover:bg-[#ebe7e1] hover:text-[#111111]">Layanan</a>
                <a href="{{ route('home') }}#tentang" class="rounded-lg px-3 py-2 text-sm font-medium text-[#626260] hover:bg-[#ebe7e1] hover:text-[#111111]">Tentang</a>
                <a href="{{ route('home') }}#proses" class="rounded-lg px-3 py-2 text-sm font-medium text-[#626260] hover:bg-[#ebe7e1] hover:text-[#111111]">Proses</a>
                <a href="#kontak" class="rounded-lg px-3 py-2 text-sm font-medium text-[#626260] hover:bg-[#ebe7e1] hover:text-[#111111]">Kontak</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="#kontak" class="hidden rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-black sm:inline-flex">Konsultasi Gratis</a>
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex rounded-lg border border-[#d3cec6] bg-white px-3 py-2 text-sm font-medium text-[#111111] hover:bg-[#ebe7e1]">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="rounded-full border border-[#111111] bg-white px-4 py-2 text-sm font-medium text-[#111111] hover:bg-[#ebe7e1] sm:inline-flex">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main>{{ $slot ?? '' }} @yield('content')</main>

    <footer id="kontak" class="border-t border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <x-app-logo-icon class="size-7 text-[#111111]" />
                        <span class="font-semibold tracking-tight text-[#111111]">{{ ($profile['company_name'] ?? '') ?: 'Ideya Webcom' }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-[#626260]">{{ $profile['tagline'] ?? 'Digital Agency & IT Solution — membangun produk digital yang cepat, aman, dan mudah diskalakan.' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-[#111111]">Layanan</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#626260]">
                        <li><a href="#layanan" class="hover:text-[#111111]">Website & Aplikasi</a></li>
                        <li><a href="#layanan" class="hover:text-[#111111]">Branding & UI/UX</a></li>
                        <li><a href="#layanan" class="hover:text-[#111111]">Growth & SEO</a></li>
                        <li><a href="#layanan" class="hover:text-[#111111]">API & Integrasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-[#111111]">Kontak</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#626260]">
                        @if(!empty($profile['email']))<li><a href="mailto:{{ $profile['email'] }}" class="hover:text-[#111111] hover:underline">{{ $profile['email'] }}</a></li>@endif
                        @if(!empty($profile['phone']))<li>{{ $profile['phone'] }}</li>@endif
                        @if(!empty($profile['address']))<li class="leading-6 text-[#7b7b78]">{{ $profile['address'] }}</li>@endif
                    </ul>
                </div>
            </div>
            <div class="mt-10 border-t border-[#d3cec6] pt-6 text-center text-sm text-[#7b7b78]">
                &copy; {{ date('Y') }} {{ ($profile['company_name'] ?? '') ?: config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>
    @fluxScripts
</body>
</html>
