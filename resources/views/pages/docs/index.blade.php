<x-layouts.public title="Dokumentasi" :seo-meta="$seoMeta">
    {{-- Header — blok biru (surface-2) di atas canvas putih, sama seperti Blog --}}
    <div class="border-b border-[#e3eaff] bg-sky-100">
        <div class="mx-auto max-w-5xl px-4 pb-12 pt-28 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-[#65646e]">
                <a href="{{ route('home') }}" class="transition hover:text-[#0a1589]">Beranda</a>
                <span aria-hidden="true" class="text-[#c7d6ff]">/</span>
                <span class="text-[#100f12]">Dokumentasi</span>
            </nav>

            <h1 class="mt-5 text-[40px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[48px] sm:tracking-[-1px]">Dokumentasi</h1>
            <p class="mt-4 max-w-2xl text-lg leading-7 text-[#65646e]">
                Panduan, referensi, dan catatan rilis tiap produk — dari pemasangan, konfigurasi, hingga pemecahan masalah.
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        @if ($products->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($products as $product)
                    <article class="flex flex-col rounded-[16px] border border-[#e3eaff] bg-white p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                        <div class="flex items-center gap-3">
                            @php $logo = $product->logoUrl(); @endphp
                            <span class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-[12px] border border-[#e3eaff] bg-[#fafbff]">
                                @if ($logo)
                                    <img src="{{ $logo }}" alt="" class="h-full w-full object-contain" loading="lazy" />
                                @else
                                    <span class="size-3.5 rounded-full" style="background: {{ $product->color ?: '#0a1589' }}"></span>
                                @endif
                            </span>
                            <h2 class="text-[20px] font-semibold tracking-[-0.16px] text-[#100f12]">{{ $product->name }}</h2>
                        </div>

                        @if (filled($product->tagline))
                            <p class="mt-3 flex-1 text-sm leading-6 text-[#65646e]">{{ $product->tagline }}</p>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-[#f3f6ff] px-3 py-1.5 text-xs font-medium text-[#0a1589]">
                                {{ $product->published_pages_count }} halaman
                            </span>
                            @if ($product->website_url)
                                <a href="{{ $product->website_url }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full border border-[#e3eaff] bg-white px-3 py-1.5 text-xs font-medium text-[#100f12] transition hover:bg-[#f3f6ff]">
                                    Situs produk
                                </a>
                            @endif
                        </div>

                        @if ($product->versions->isNotEmpty())
                            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#e3eaff] pt-4">
                                <span class="text-xs font-medium text-[#65646e]">Versi:</span>
                                @foreach ($product->versions as $version)
                                    <a href="{{ route('docs.version', [$product, $version]) }}" class="rounded-full border px-2.5 py-1 text-xs font-medium transition {{ $version->is_current ? 'border-[#0a1589] bg-[#0a1589] text-white' : 'border-[#e3eaff] bg-white text-[#100f12] hover:bg-[#f3f6ff]' }}">{{ $version->label }}</a>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('docs.product', $product) }}" class="mt-5 inline-flex w-fit items-center rounded-[20px] bg-[#0a1589] px-5 pb-3 pt-3.5 text-sm font-medium leading-none text-white transition hover:bg-[#06105a]">
                            Buka dokumentasi →
                        </a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-[32px] border border-dashed border-[#c7d6ff] bg-[#fafbff] p-10 text-center">
                <p class="text-sm font-semibold text-[#100f12]">Belum ada dokumentasi</p>
                <p class="mt-2 text-sm leading-6 text-[#65646e]">Dokumentasi produk sedang kami siapkan. Sementara itu, silakan hubungi kami bila ada yang perlu ditanyakan.</p>
                <a href="{{ route('kontak') }}" class="mt-5 inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Hubungi kami</a>
            </div>
        @endif
    </div>
</x-layouts.public>
