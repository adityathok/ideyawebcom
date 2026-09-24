<x-layouts.public title="Wilayah Layanan" :seo-meta="$seoMeta">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $company = ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb');
        $jumlahKecamatan = $kota->sum(fn (array $item): int => count($item['kecamatan']));
    @endphp

    {{-- Hero gelap agar nav terang (lihat layouts.public: darkHero) --}}
    <x-page-hero breadcrumb="Wilayah Layanan" :badge="'Layanan '.$company">
        <x-slot:title>Jasa website untuk <span class="text-gradient-dark">kota &amp; kecamatan Anda</span></x-slot:title>
        <x-slot:subtitle>Kami melayani pembuatan website, web app custom, dan WordPress di {{ $kota->count() }} kota/kabupaten dan {{ $jumlahKecamatan }} kecamatan. Pilih wilayah Anda untuk melihat detailnya.</x-slot:subtitle>
        <x-slot:actions>
            <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#0a1589] transition hover:bg-[#e3eaff] sm:w-auto sm:text-[18px]">Konsultasi Gratis</a>
            <a href="{{ route('layanan') }}" class="inline-flex w-full items-center justify-center rounded-[20px] border border-white/30 bg-white/10 px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-white/20 sm:w-auto sm:text-[18px]">Lihat Layanan</a>
        </x-slot:actions>
    </x-page-hero>

    {{-- Daftar kota --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Wilayah</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Kota &amp; kabupaten yang kami layani</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">Klik salah satu wilayah untuk melihat kecamatan yang sudah kami tangani beserta gambarnya.</p>
            </div>

            @if ($kota->isNotEmpty())
                <div class="mt-12 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($kota as $item)
                        <a href="{{ route('lp.kota', ['kota' => $item['slug']]) }}"
                           class="group flex h-full flex-col overflow-hidden rounded-[16px] border border-[#e3eaff] bg-[#fafbff] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                            <span class="block aspect-[16/9] overflow-hidden border-b border-[#e3eaff] bg-[#f3f6ff]">
                                @if ($item['gambar'])
                                    <img src="{{ $item['gambar'] }}" alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" />
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-3xl text-[#c7d6ff]" aria-hidden="true">◎</span>
                                @endif
                            </span>
                            <span class="flex flex-1 flex-col p-6">
                                <span class="text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $item['nama'] }}</span>
                                <span class="mt-2 text-sm leading-6 text-[#65646e]">
                                    {{ count($item['kecamatan']) }} kecamatan: {{ \Illuminate\Support\Str::limit(implode(', ', $item['kecamatan']), 90) }}
                                </span>
                                <span class="mt-4 inline-flex w-fit items-center rounded-[14px] bg-[#0a1589] px-4 py-2.5 text-sm font-medium leading-none text-white transition group-hover:bg-[#06105a]">
                                    Lihat wilayah →
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-12 rounded-[32px] border border-dashed border-[#c7d6ff] bg-[#fafbff] p-10 text-center">
                    <p class="text-sm font-semibold text-[#100f12]">Belum ada wilayah terdaftar</p>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Daftar wilayah sedang kami lengkapi. Sementara itu, silakan hubungi kami untuk wilayah Anda.</p>
                    <a href="{{ route('kontak') }}" class="mt-5 inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Hubungi kami</a>
                </div>
            @endif
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="relative isolate overflow-hidden rounded-[32px] border border-[#e3eaff] bg-[#f3f6ff] px-6 py-16 text-center sm:px-12 lg:py-20">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(70%_120%_at_100%_0%,#e3eaff,transparent)]"></div>
                <h2 class="mx-auto max-w-2xl font-serif text-[30px] font-normal leading-[1.2] tracking-[-0.5px] text-[#100f12] sm:text-[40px]">Wilayah Anda belum <span class="text-gradient">ada di daftar?</span></h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-7 text-[#65646e]">Kami mengerjakan proyek secara remote ke seluruh Indonesia. Ceritakan kebutuhan Anda, kami bantu petakan solusinya.</p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-[#0a1589] px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto sm:text-[18px]">Hubungi Kami</a>
                    <a href="{{ route('layanan') }}" class="inline-flex w-full items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff] sm:w-auto sm:text-[18px]">Pelajari layanan</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
