@props(['title' => null, 'seoMeta' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-white antialiased">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        // Halaman dengan hero gelap → nav harus terang selama belum di-scroll.
        $darkHero = request()->routeIs('home', 'layanan', 'kontak', 'privacy');
    @endphp
    <!-- Top Nav — transparan di atas, blur saat scroll, height 56px -->
    <header
        x-data="{ scrolled: false, darkHero: @js($darkHero) }"
        x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => { scrolled = window.scrollY > 8; }, { passive: true })"
        :data-nav-light="(! scrolled && darkHero) ? 'true' : null"
        @if($darkHero) data-nav-light="true" @endif
        class="fixed top-0 z-40 w-full transition-all duration-300">
        <div
            :class="scrolled ? 'mt-1 rounded-xl border-[#e3eaff] bg-white/85 shadow-[0_1px_12px_rgba(43,75,255,0.10)] backdrop-blur-md' : 'border-transparent bg-transparent'"
            class="mx-auto transition-all duration-300 flex h-14 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="nav-fg flex items-center gap-2.5">
                <x-app-logo-icon class="size-7 text-[#100f12]" />
                <span class="text-[15px] font-semibold tracking-tight text-[#100f12]">{{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}</span>
            </a>
            <nav class="nav-fg hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) data-active @endif class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-[#f3f6ff] text-[#0a1589]' : 'text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]' }}">Beranda</a>
                <a href="{{ route('layanan') }}" @if(request()->routeIs('layanan')) data-active @endif class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('layanan') ? 'bg-[#f3f6ff] text-[#0a1589]' : 'text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]' }}">Layanan</a>
                <a href="{{ route('home') }}#tentang" class="rounded-lg px-3 py-2 text-sm font-medium text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]">Tentang</a>
                <a href="{{ route('home') }}#proses" class="rounded-lg px-3 py-2 text-sm font-medium text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]">Proses</a>
                <a href="{{ route('kontak') }}" @if(request()->routeIs('kontak')) data-active @endif class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('kontak') ? 'bg-[#f3f6ff] text-[#0a1589]' : 'text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]' }}">Kontak</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('kontak') }}" class="nav-cta hidden rounded-lg bg-[#0a1589] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-[#06105a] sm:inline-flex">Konsultasi Gratis</a>
            </div>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <!-- Footer — biru tua #06105a, teks inverse (DESIGN.md: footer) -->
    <footer id="kontak" class="relative overflow-hidden bg-slate-900 text-white">
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-64 bg-[radial-gradient(60%_120%_at_50%_130%,rgba(43,75,255,0.55),transparent)]"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <x-app-logo-icon class="size-7 text-white" />
                        <span class="font-semibold tracking-tight text-white">{{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-[#c7d6ff]">{{ ($profile['tagline'] ?? '') ?: 'Developer Website & Web App — spesialis web app & berpengalaman di WordPress.' }}</p>
                    @if(!empty($profile['about']))
                        <p class="mt-3 text-sm leading-6 text-[#c7d6ff]">{{ \Illuminate\Support\Str::limit($profile['about'], 160) }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-white">Layanan</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-50">
                        <li><a href="{{ route('layanan') }}" class="hover:text-white">Web App Custom</a></li>
                        <li><a href="{{ route('layanan') }}" class="hover:text-white">Website Company Profile</a></li>
                        <li><a href="{{ route('layanan') }}" class="hover:text-white">WordPress Development</a></li>
                        <li><a href="{{ route('layanan') }}" class="hover:text-white">Optimasi WordPress</a></li>
                        <li><a href="{{ route('layanan') }}" class="hover:text-white">Maintenance Website</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-white">Kontak</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#c7d6ff]">
                        @if(!empty($profile['email']))<li><a href="mailto:{{ $profile['email'] }}" class="hover:text-white hover:underline">{{ $profile['email'] }}</a></li>@endif
                        @if(!empty($profile['phone']))<li><a href="tel:{{ preg_replace('/\s+/', '', $profile['phone']) }}" class="hover:text-white">{{ $profile['phone'] }}</a></li>@endif
                        @if(!empty($profile['address']))<li class="leading-6 text-[#c7d6ff]">{{ $profile['address'] }}</li>@endif
                        @if(empty($profile['email']) && empty($profile['phone']) && empty($profile['address']))
                            <li class="text-[#c7d6ff]">Hubungi kami untuk konsultasi proyek Anda.</li>
                        @endif
                        <li><a href="{{ route('kontak') }}" class="hover:text-white hover:underline">Formulir Kontak</a></li>
                    </ul>
                    @php $social = array_filter(['facebook' => $profile['facebook'] ?? null, 'instagram' => $profile['instagram'] ?? null, 'twitter' => $profile['twitter'] ?? null, 'linkedin' => $profile['linkedin'] ?? null]); @endphp
                    @if(!empty($social))
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach($social as $key => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sm font-medium text-[#c7d6ff] hover:text-white hover:underline">{{ ucfirst($key) }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-sm text-[#c7d6ff] sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}. All rights reserved.</p>
                <div class="flex items-center gap-5">
                    <a href="{{ route('kontak') }}" class="transition hover:text-white">Kontak</a>
                    <a href="{{ route('privacy') }}" class="transition hover:text-white">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>
    @fluxScripts
</body>
</html>
