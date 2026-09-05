<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[#f5f1ec] antialiased">
    @php $profile = $profile ?? \App\Models\Setting::profile(); @endphp
    <!-- Navbar -->
    <header class="sticky top-0 z-40 w-full border-b border-[#d3cec6] bg-[#f5f1ec]/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                <x-app-logo-icon class="size-8" />
                <span class="text-lg font-bold tracking-tight text-[#111111]">{{ ($profile['company_name'] ?? '') ?: config('app.name') }}</span>
            </a>
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-[#111111] text-white' : 'text-[#626260] hover:bg-[#ebe7e1]' }}">Home</a>
                <a href="{{ route('blog.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'bg-[#111111] text-white' : 'text-[#626260] hover:bg-[#ebe7e1]' }}">Blog</a>
                <a href="{{ route('home') }}#about" class="rounded-lg px-3 py-2 text-sm font-medium text-[#626260] hover:bg-[#ebe7e1]">Tentang</a>
                <a href="{{ route('home') }}#kontak" class="rounded-lg px-3 py-2 text-sm font-medium text-[#626260] hover:bg-[#ebe7e1]">Kontak</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('blog.index') }}" class="hidden rounded-full border border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium text-[#111111] hover:bg-[#f5f1ec] sm:inline-flex">Jelajahi Blog</a>
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex rounded-full bg-[#111111] px-4 py-2 text-sm font-medium text-white hover:bg-black">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="inline-flex rounded-full bg-[#111111] px-4 py-2 text-sm font-medium text-white hover:bg-black">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <!-- Footer -->
    <footer id="kontak" class="border-t border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2">
                        <x-app-logo-icon class="size-7" />
                        <span class="font-bold text-[#111111]">{{ ($profile['company_name'] ?? '') ?: 'Ideya Webcom' }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-[#626260]">{{ $profile['tagline'] ?? 'Digital Agency & IT Solution' }}</p>
                    <p class="mt-3 text-sm text-[#626260]">{{ !empty($profile['about']) ? \Illuminate\Support\Str::limit($profile['about'], 160) : '' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[#111111]">Kategori</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach(\App\Models\Category::orderBy('name')->take(6)->get() as $cat)
                            <li><a href="{{ route('blog.category', $cat) }}" class="text-sm text-[#626260] hover:text-[#111111]">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[#111111]">Kontak</h3>
                    <ul class="mt-3 space-y-2 text-sm text-[#626260]">
                        @if(!empty($profile['email']))<li>{{ $profile['email'] }}</li>@endif
                        @if(!empty($profile['phone']))<li>{{ $profile['phone'] }}</li>@endif
                        @if(!empty($profile['address']))<li>{{ $profile['address'] }}</li>@endif
                    </ul>
                    @php $social = array_filter(['facebook' => $profile['facebook'] ?? null, 'instagram' => $profile['instagram'] ?? null, 'twitter' => $profile['twitter'] ?? null, 'linkedin' => $profile['linkedin'] ?? null]); @endphp
                    @if(!empty($social))
                        <div class="mt-4 flex gap-3">
                            @foreach($social as $key => $url)
                                <a href="{{ $url }}" target="_blank" class="text-sm font-medium text-[#626260] hover:text-[#111111]">{{ ucfirst($key) }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-8 border-t border-[#d3cec6] pt-8 text-center text-sm text-[#626260]">
                &copy; {{ date('Y') }} {{ ($profile['company_name'] ?? '') ?: config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>
    @fluxScripts
</body>
</html>
