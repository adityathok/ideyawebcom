<x-layouts.public :title="'Jasa Website '.$namaKota" :seo-meta="$seoMeta">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $gambarHero = $kecamatan->first()?->gambarUtamaUrl();
        $jumlahKecamatan = $kecamatan->count();
    @endphp

    {{-- Hero gelap; latar memakai foto wilayah kalau admin sudah mengunggahnya --}}
    <x-page-hero
        :breadcrumb="$namaKota"
        :badge="'Wilayah '.$namaKota"
        :image="$gambarHero"
    >
        <x-slot:title>Jasa website &amp; web app di <span class="text-gradient-dark">{{ $namaKota }}</span></x-slot:title>
        <x-slot:subtitle>Kami mengerjakan website, web app custom, WordPress, dan maintenance untuk bisnis dan instansi di {{ $namaKota }} — termasuk {{ $jumlahKecamatan }} kecamatan di bawah ini.</x-slot:subtitle>
        <x-slot:actions>
            <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#0a1589] transition hover:bg-[#e3eaff] sm:w-auto sm:text-[18px]">Konsultasi Gratis</a>
            <a href="#kecamatan" class="inline-flex w-full items-center justify-center rounded-[20px] border border-white/30 bg-white/10 px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-white/20 sm:w-auto sm:text-[18px]">Lihat Kecamatan</a>
        </x-slot:actions>
    </x-page-hero>

    {{-- Layanan: berlaku untuk seluruh kota --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Layanan</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Yang bisa kami kerjakan di {{ $namaKota }}</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">Semua layanan dikerjakan remote maupun tatap muka, jadi jarak bukan penghalang.</p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($services as $service)
                    <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6">
                        <p class="text-[18px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $service['title'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-[#65646e]">{{ $service['short'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Kecamatan: konten, bukan link. Tiap kartu punya CTA-nya sendiri. --}}
    <section id="kecamatan" class="bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Kecamatan</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Wilayah yang kami tangani di {{ $namaKota }}</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">{{ $jumlahKecamatan }} kecamatan — dari yang sudah kami kerjakan maupun yang siap kami layani.</p>
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($kecamatan as $item)
                    @php
                        $ikon = $item->gambarIconUrl();
                        $foto = $item->gambarUtamaUrl();
                        $waKecamatan = $whatsapp[$item->id] ?? null;
                    @endphp
                    <article class="flex h-full flex-col overflow-hidden rounded-[16px] border border-[#e3eaff] bg-[#fafbff] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                        @if ($foto)
                            <span class="block aspect-[16/9] overflow-hidden border-b border-[#e3eaff] bg-[#f3f6ff]">
                                <img src="{{ $foto }}" alt="Wilayah {{ $item->labelWilayah() }}" class="h-full w-full object-cover" loading="lazy" />
                            </span>
                        @endif
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center gap-3">
                                <span class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-[12px] border border-[#e3eaff] bg-white">
                                    @if ($ikon)
                                        <img src="{{ $ikon }}" alt="" class="h-full w-full object-contain" loading="lazy" />
                                    @else
                                        <span aria-hidden="true" class="text-lg text-[#0a1589]">◈</span>
                                    @endif
                                </span>
                                <div>
                                    <h3 class="text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $item->nama_kecamatan }}</h3>
                                    <p class="text-xs font-medium uppercase tracking-[0.5px] text-[#aaa9ae]">{{ $namaKota }}</p>
                                </div>
                            </div>

                            @if (filled($item->deskripsi))
                                <p class="mt-3 flex-1 text-sm leading-6 text-[#65646e]">{{ \Illuminate\Support\Str::limit($item->deskripsi, 160) }}</p>
                            @else
                                <p class="mt-3 flex-1 text-sm leading-6 text-[#65646e]">Kami siap mengerjakan website maupun web app untuk bisnis dan instansi di {{ $item->nama_kecamatan }}.</p>
                            @endif

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                @if ($waKecamatan)
                                    <a href="{{ $waKecamatan }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center rounded-[14px] bg-[#0a1589] px-4 py-2.5 text-sm font-medium leading-none text-white transition hover:bg-[#06105a]">
                                        Chat WhatsApp
                                    </a>
                                @endif
                                <a href="{{ route('kontak') }}"
                                   class="inline-flex items-center rounded-[14px] border border-[#e3eaff] bg-white px-4 py-2.5 text-sm font-medium leading-none text-[#100f12] transition hover:bg-[#f3f6ff]">
                                    Konsultasi {{ $item->nama_kecamatan }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA penutup --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="relative isolate overflow-hidden rounded-[32px] border border-[#e3eaff] bg-[#f3f6ff] px-6 py-16 text-center sm:px-12 lg:py-20">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(70%_120%_at_100%_0%,#e3eaff,transparent)]"></div>
                <h2 class="mx-auto max-w-2xl font-serif text-[30px] font-normal leading-[1.2] tracking-[-0.5px] text-[#100f12] sm:text-[40px]">Butuh website untuk bisnis di <span class="text-gradient">{{ $namaKota }}?</span></h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-7 text-[#65646e]">Ceritakan kebutuhan Anda — kami balas dengan rekomendasi dan estimasi tanpa komitmen.</p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-[#0a1589] px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto sm:text-[18px]">Hubungi Kami</a>
                    <a href="{{ route('layanan') }}" class="inline-flex w-full items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff] sm:w-auto sm:text-[18px]">Pelajari layanan</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
