<x-layouts.public :title="$baris->labelWilayah()" :seo-meta="$seoMeta">
    @php
        $foto = $baris->gambarUtamaUrl();
        $ikon = $baris->gambarIconUrl();
        $urlKota = route('lp.kota', ['kota' => $kotaSlug]);
    @endphp

    {{-- Hero gelap; latar memakai foto wilayah kalau admin sudah mengunggahnya --}}
    <x-page-hero
        :breadcrumb="$baris->nama_kecamatan"
        :trail="[
            ['name' => 'Wilayah Layanan', 'url' => route('lp.index')],
            ['name' => $namaKota, 'url' => $urlKota],
        ]"
        :badge="'Wilayah '.$baris->labelWilayah()"
        :image="$foto"
    >
        <x-slot:title>Jasa website di <span class="text-gradient-dark">{{ $baris->nama_kecamatan }}</span></x-slot:title>
        <x-slot:subtitle>Melayani pembuatan website, web app custom, WordPress, dan maintenance untuk bisnis dan instansi di {{ $baris->labelWilayah() }}.</x-slot:subtitle>
        <x-slot:actions>
            @if ($whatsapp)
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center rounded-[20px] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#0a1589] transition hover:bg-[#e3eaff] sm:w-auto sm:text-[18px]">Chat WhatsApp</a>
            @else
                <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#0a1589] transition hover:bg-[#e3eaff] sm:w-auto sm:text-[18px]">Konsultasi Gratis</a>
            @endif
            <a href="{{ $urlKota }}" class="inline-flex w-full items-center justify-center rounded-[20px] border border-white/30 bg-white/10 px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-white/20 sm:w-auto sm:text-[18px]">Kecamatan lain di {{ $namaKota }}</a>
        </x-slot:actions>
    </x-page-hero>

    {{-- Isi: deskripsi wilayah + layanan + sidebar kontak --}}
    <section class="bg-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="lg:col-span-2">
                @if ($ikon)
                    <span class="mb-6 inline-flex size-14 items-center justify-center overflow-hidden rounded-[16px] border border-[#e3eaff] bg-[#fafbff]">
                        <img src="{{ $ikon }}" alt="" class="h-full w-full object-contain p-1.5" loading="lazy" />
                    </span>
                @endif

                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">{{ $baris->labelWilayah() }}</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Website untuk bisnis di {{ $baris->nama_kecamatan }}</h2>

                @if (filled($baris->deskripsi))
                    <div class="mt-6 space-y-4 text-[17px] leading-8 tracking-[-0.16px] text-[#65646e]">
                        @foreach (preg_split('/\R{2,}/', trim($baris->deskripsi)) as $paragraf)
                            <p>{{ $paragraf }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="mt-6 text-[17px] leading-8 tracking-[-0.16px] text-[#65646e]">
                        Kami membantu bisnis, UMKM, dan instansi di {{ $baris->labelWilayah() }} tampil profesional secara online — mulai dari company profile, toko online, sampai web app custom yang menyesuaikan alur kerja Anda.
                    </p>
                @endif

                @if ($foto)
                    <figure class="mt-10 overflow-hidden rounded-[16px] border border-[#e3eaff] bg-[#f3f6ff]">
                        <img src="{{ $foto }}" alt="Wilayah {{ $baris->labelWilayah() }}" class="h-full w-full object-cover" loading="lazy" />
                    </figure>
                @endif

                {{-- Layanan yang sama untuk semua wilayah, sumbernya ServiceCatalog --}}
                <div class="mt-12">
                    <h3 class="text-[24px] font-medium leading-tight tracking-[-0.5px] text-[#100f12]">Yang bisa kami kerjakan</h3>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach ($services as $service)
                            <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-5">
                                <p class="text-[16px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $service['title'] }}</p>
                                <p class="mt-2 text-sm leading-6 text-[#65646e]">{{ $service['short'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar: kontak + kecamatan lain di kota yang sama --}}
            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-[16px] border border-[#e3eaff] bg-[#f3f6ff] p-6">
                    <h3 class="text-[18px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Butuh website di {{ $baris->nama_kecamatan }}?</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Ceritakan kebutuhan Anda. Kami balas dengan rekomendasi dan estimasi biaya tanpa komitmen.</p>
                    <div class="mt-5 flex flex-col gap-2.5">
                        @if ($whatsapp)
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Chat WhatsApp</a>
                        @endif
                        <a href="{{ route('kontak') }}" class="inline-flex items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff]">Formulir Kontak</a>
                    </div>
                </div>

                @if ($kecamatanLain->isNotEmpty())
                    <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6">
                        <h3 class="text-[18px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Kecamatan lain di {{ $namaKota }}</h3>
                        <ul class="mt-4 space-y-1.5">
                            @foreach ($kecamatanLain as $lain)
                                <li>
                                    <a href="{{ route('lp.kecamatan', ['kota' => $kotaSlug, 'kecamatan' => $lain->slugKecamatan()]) }}"
                                       class="flex items-center justify-between gap-3 rounded-[12px] px-3 py-2.5 text-sm font-medium text-[#100f12] transition hover:bg-[#f3f6ff] hover:text-[#0a1589]">
                                        <span>{{ $lain->nama_kecamatan }}</span>
                                        <span aria-hidden="true" class="text-[#9aa0b4]">→</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('lp.index') }}" class="mt-4 inline-flex text-sm font-medium text-[#0a1589] transition hover:underline">Lihat semua wilayah</a>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    {{-- CTA penutup --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="relative isolate overflow-hidden rounded-[32px] border border-[#e3eaff] bg-[#f3f6ff] px-6 py-16 text-center sm:px-12 lg:py-20">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(70%_120%_at_100%_0%,#e3eaff,transparent)]"></div>
                <h2 class="mx-auto max-w-2xl font-serif text-[30px] font-normal leading-[1.2] tracking-[-0.5px] text-[#100f12] sm:text-[40px]">Mulai proyek Anda di <span class="text-gradient">{{ $baris->labelWilayah() }}</span></h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-7 text-[#65646e]">Kami mengerjakan proyek secara remote maupun tatap muka. Konsultasi awal gratis.</p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-[#0a1589] px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto sm:text-[18px]">Hubungi Kami</a>
                    <a href="{{ route('layanan') }}" class="inline-flex w-full items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff] sm:w-auto sm:text-[18px]">Pelajari layanan</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
