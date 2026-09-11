@props([
    'breadcrumb' => null,
    'badge' => null,
])

{{--
    Hero halaman dalam — foto langit biru gelap dengan scrim, agar nav terang (lihat layouts.public: darkHero).
    Slot: `title` (wajib), `subtitle` (opsional), `actions` (opsional).
--}}
<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden bg-[#0a1589]']) }}>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <img src="{{ asset('images/page-hero-sky.jpg') }}" alt="" class="absolute inset-0 size-full object-cover object-top brightness-90" loading="lazy" decoding="async" />
        {{-- Scrim biru tua tipis: jaga kontras teks putih tanpa membuat langit terlalu gelap --}}
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(6,16,90,0.58)_0%,rgba(6,16,90,0.52)_40%,rgba(6,16,90,0.48)_72%,rgba(6,16,90,0.38)_86%)]"></div>
        {{-- Glow biru untuk kedalaman --}}
        <div class="absolute inset-0 bg-[radial-gradient(80%_70%_at_50%_0%,rgba(43,75,255,0.28),transparent_70%)]"></div>
        {{-- Gradasi ke putih di dasar: transisi mulus ke section putih di bawah --}}
        <div class="absolute inset-x-0 bottom-0 h-32 bg-[linear-gradient(180deg,transparent_0%,rgba(255,255,255,0.45)_55%,#ffffff_100%)]"></div>
    </div>
    <div class="mx-auto max-w-4xl px-4 pb-28 pt-32 text-center sm:px-6 sm:pb-32 sm:pt-40 lg:px-8">
        <nav aria-label="Breadcrumb" class="flex items-center justify-center gap-2 text-sm text-white/70">
            <a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a>
            <span aria-hidden="true">/</span>
            <span class="text-white">{{ $breadcrumb }}</span>
        </nav>

        @if ($badge)
            <span class="mt-6 inline-flex items-center gap-2 rounded-[16px] bg-white/10 px-4 py-2 text-sm font-medium tracking-[-0.16px] text-white ring-1 ring-inset ring-white/25">
                <span aria-hidden="true" class="size-1.5 rounded-full bg-[#7d95ff]"></span>
                {{ $badge }}
            </span>
        @endif

        @isset($title)
            <h1 class="mx-auto mt-6 max-w-3xl text-[36px] font-medium leading-[1.08] tracking-[-1.1px] text-white sm:text-[48px] sm:tracking-[-1.4px] lg:text-[56px] lg:leading-[1.05]">
                {{ $title }}
            </h1>
        @endisset

        @isset($subtitle)
            <p class="mx-auto mt-6 max-w-2xl text-[17px] leading-8 tracking-[-0.16px] text-white/80 sm:text-[18px]">
                {{ $subtitle }}
            </p>
        @endisset

        @isset($actions)
            <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
