@props(['title' => null, 'seoMeta' => null])

@php
    $profile = $profile ?? \App\Models\Setting::profile();
    // Halaman dengan hero gelap → nav harus terang selama belum di-scroll.
    $darkHero = request()->routeIs('home', 'layanan', 'kontak', 'privacy', 'lp.*');

    // Menu publik dipakai dua kali: nav desktop di header dan panel offcanvas di mobile.
    $navMenu = [
        ['label' => 'Beranda', 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Layanan', 'url' => route('layanan'), 'active' => request()->routeIs('layanan')],
        ['label' => 'Tentang', 'url' => route('home').'#tentang', 'active' => false],
        ['label' => 'Proses', 'url' => route('home').'#proses', 'active' => false],
        ['label' => 'Blog', 'url' => route('blog.index'), 'active' => request()->routeIs('blog.*')],
        ['label' => 'Wilayah', 'url' => route('lp.index'), 'active' => request()->routeIs('lp.*')],
        ['label' => 'Dokumentasi', 'url' => route('docs.index'), 'active' => request()->routeIs('docs.*')],
        ['label' => 'Kontak', 'url' => route('kontak'), 'active' => request()->routeIs('kontak')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body
    class="min-h-screen bg-white antialiased"
    x-data="{ scrolled: false, darkHero: @js($darkHero), menuOpen: false }"
    :class="{ 'overflow-hidden': menuOpen }"
    @keydown.escape.window="menuOpen = false">
    <!-- Top Nav — transparan di atas, blur saat scroll, height 56px -->
    <header
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
                @foreach($navMenu as $item)
                    <a href="{{ $item['url'] }}" @if($item['active']) data-active aria-current="page" @endif class="rounded-lg px-3 py-2 text-sm font-medium {{ $item['active'] ? 'bg-[#f3f6ff] text-[#0a1589]' : 'text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('kontak') }}" class="nav-cta hidden rounded-lg bg-[#0a1589] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-[#06105a] sm:inline-flex">Konsultasi Gratis</a>

                {{-- Toggle menu mobile — nav desktop disembunyikan di bawah md --}}
                <button type="button"
                        x-ref="menuToggle"
                        @click="menuOpen = true"
                        :aria-expanded="menuOpen ? 'true' : 'false'"
                        aria-controls="menu-mobile"
                        aria-label="Buka menu"
                        class="nav-fg nav-ghost inline-flex size-10 items-center justify-center rounded-xl border border-[#e3eaff] text-[#100f12] transition hover:bg-[#f3f6ff] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0a1589] md:hidden">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Menu mobile: offcanvas dari kanan. State `menuOpen` tinggal di <body> (x-data),
         jadi tombol di header dan panel ini berbagi scope yang sama.
         z-[60] supaya latarnya ikut menutup tombol WhatsApp melayang (z-50). --}}
    <div class="md:hidden" @resize.window="if (window.innerWidth >= 768) menuOpen = false">
        <div x-cloak x-show="menuOpen"
             x-transition.opacity.duration.200ms
             @click="menuOpen = false; $nextTick(() => $refs.menuToggle?.focus())"
             aria-hidden="true"
             class="fixed inset-0 z-[60] bg-[#100f12]/45 backdrop-blur-sm"></div>

        <aside x-cloak x-show="menuOpen"
               id="menu-mobile"
               role="dialog"
               aria-modal="true"
               aria-label="Menu navigasi"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="translate-x-full"
               x-effect="menuOpen ? $nextTick(() => $refs.menuClose?.focus()) : null"
               class="fixed inset-y-0 right-0 z-[60] flex w-[88%] max-w-sm flex-col bg-white shadow-2xl">
            <div class="flex h-14 shrink-0 items-center justify-between gap-4 border-b border-[#e3eaff] px-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <x-app-logo-icon class="size-7 text-[#100f12]" />
                    <span class="text-[15px] font-semibold tracking-tight text-[#100f12]">{{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}</span>
                </a>
                <button type="button"
                        x-ref="menuClose"
                        @click="menuOpen = false; $nextTick(() => $refs.menuToggle?.focus())"
                        aria-label="Tutup menu"
                        class="inline-flex size-10 items-center justify-center rounded-xl border border-[#e3eaff] text-[#100f12] transition hover:bg-[#f3f6ff] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0a1589]">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Menu utama">
                <ul class="space-y-1">
                    @foreach($navMenu as $item)
                        <li>
                            <a href="{{ $item['url'] }}" @click="menuOpen = false"
                               @if($item['active']) aria-current="page" @endif
                               class="flex items-center justify-between rounded-xl px-3.5 py-3 text-[15px] font-medium transition {{ $item['active'] ? 'bg-[#f3f6ff] text-[#0a1589]' : 'text-[#100f12] hover:bg-[#f3f6ff]' }}">
                                <span>{{ $item['label'] }}</span>
                                <svg class="size-4 text-[#9aa0b4]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 6l6 6-6 6" />
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="shrink-0 border-t border-[#e3eaff] p-4">
                <a href="{{ route('kontak') }}" @click="menuOpen = false"
                   class="flex w-full items-center justify-center rounded-xl bg-[#0a1589] px-[18px] py-3 text-[15px] font-medium text-white transition hover:bg-[#06105a]">Konsultasi Gratis</a>
            </div>
        </aside>
    </div>

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
                    <ul class="mt-3 space-y-2 text-sm text-white">
                        <li><a href="{{ route('layanan') }}" class="text-white transition hover:underline">Web App Custom</a></li>
                        <li><a href="{{ route('layanan') }}" class="text-white transition hover:underline">Website Company Profile</a></li>
                        <li><a href="{{ route('layanan') }}" class="text-white transition hover:underline">WordPress Development</a></li>
                        <li><a href="{{ route('layanan') }}" class="text-white transition hover:underline">Optimasi WordPress</a></li>
                        <li><a href="{{ route('layanan') }}" class="text-white transition hover:underline">Maintenance Website</a></li>
                        <li><a href="{{ route('lp.index') }}" class="text-white transition hover:underline">Wilayah Layanan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-white">Kontak</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#c7d6ff]">
                        @if(!empty($profile['email']))<li><a href="mailto:{{ $profile['email'] }}" class="text-white transition hover:underline">{{ $profile['email'] }}</a></li>@endif
                        @if(!empty($profile['phone']))<li><a href="tel:{{ preg_replace('/\s+/', '', $profile['phone']) }}" class="text-white transition hover:underline">{{ $profile['phone'] }}</a></li>@endif
                        @if(!empty($profile['address']))<li class="leading-6 text-[#c7d6ff]">{{ $profile['address'] }}</li>@endif
                        @if(empty($profile['email']) && empty($profile['phone']) && empty($profile['address']))
                            <li class="text-[#c7d6ff]">Hubungi kami untuk konsultasi proyek Anda.</li>
                        @endif
                        <li><a href="{{ route('kontak') }}" class="text-white transition hover:underline">Formulir Kontak</a></li>
                    </ul>
                    @php $social = array_filter(['facebook' => $profile['facebook'] ?? null, 'instagram' => $profile['instagram'] ?? null, 'twitter' => $profile['twitter'] ?? null, 'linkedin' => $profile['linkedin'] ?? null]); @endphp
                    @if(!empty($social))
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach($social as $key => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sm font-medium text-white transition hover:underline">{{ ucfirst($key) }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-sm text-[#c7d6ff] sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}. All rights reserved.</p>
                <div class="flex items-center gap-5">
                    <a href="{{ route('kontak') }}" class="text-white transition hover:underline">Kontak</a>
                    <a href="{{ route('docs.index') }}" class="text-white transition hover:underline">Dokumentasi</a>
                    <a href="{{ route('privacy') }}" class="text-white transition hover:underline">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
        {{-- Tombol WhatsApp melayang di pojok kanan bawah; nomor diambil dari pengaturan profil --}}
        <x-whatsapp-float :profile="$profile" />
    </footer>
    @fluxScripts
</body>
</html>
