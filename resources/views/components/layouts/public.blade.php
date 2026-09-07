<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-[#f5f1ec] antialiased">
    @php $profile = $profile ?? \App\Models\Setting::profile(); @endphp
    <!-- Top Nav — putih, height 56px -->
    <header class="fixed top-0 z-40 w-full border-b border-[#ebe7e1] bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <x-app-logo-icon class="size-7 text-[#111111]" />
                <span class="text-[15px] font-semibold tracking-tight text-[#111111]">{{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}</span>
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
            </div>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <!-- Footer — gelap #111111, teks inverse -->
    <footer id="kontak" class="border-t border-white/10 bg-[#111111] text-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2.5">
                        <x-app-logo-icon class="size-7 text-white" />
                        <span class="font-semibold tracking-tight text-white">{{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-[#9c9fa5]">{{ ($profile['tagline'] ?? '') ?: 'Developer Website & Web App — spesialis web app & berpengalaman di WordPress.' }}</p>
                    @if(!empty($profile['about']))
                        <p class="mt-3 text-sm leading-6 text-[#9c9fa5]">{{ \Illuminate\Support\Str::limit($profile['about'], 160) }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-white">Layanan</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#9c9fa5]">
                        <li><a href="#layanan" class="hover:text-white">Web App Custom</a></li>
                        <li><a href="#layanan" class="hover:text-white">Website Company Profile</a></li>
                        <li><a href="#layanan" class="hover:text-white">WordPress Development</a></li>
                        <li><a href="#layanan" class="hover:text-white">Optimasi WordPress</a></li>
                        <li><a href="#layanan" class="hover:text-white">Maintenance Website</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-tight text-white">Kontak</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#9c9fa5]">
                        @if(!empty($profile['email']))<li><a href="mailto:{{ $profile['email'] }}" class="hover:text-white hover:underline">{{ $profile['email'] }}</a></li>@endif
                        @if(!empty($profile['phone']))<li><a href="tel:{{ preg_replace('/\s+/', '', $profile['phone']) }}" class="hover:text-white">{{ $profile['phone'] }}</a></li>@endif
                        @if(!empty($profile['address']))<li class="leading-6 text-[#9c9fa5]">{{ $profile['address'] }}</li>@endif
                        @if(empty($profile['email']) && empty($profile['phone']) && empty($profile['address']))
                            <li class="text-[#9c9fa5]">Hubungi kami untuk konsultasi proyek Anda.</li>
                        @endif
                    </ul>
                    @php $social = array_filter(['facebook' => $profile['facebook'] ?? null, 'instagram' => $profile['instagram'] ?? null, 'twitter' => $profile['twitter'] ?? null, 'linkedin' => $profile['linkedin'] ?? null]); @endphp
                    @if(!empty($social))
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach($social as $key => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="text-sm font-medium text-[#9c9fa5] hover:text-white hover:underline">{{ ucfirst($key) }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-sm text-[#9c9fa5] sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb') }}. All rights reserved.</p>
                <p class="text-xs tracking-wide">Dibuat dengan ♥ di IdeyaWeb</p>
            </div>
        </div>
    </footer>
    @fluxScripts
</body>
</html>
