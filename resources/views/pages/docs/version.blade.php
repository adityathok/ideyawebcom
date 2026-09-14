<x-layouts.public :title="'Dokumentasi '.$product->name.' '.$version->label" :seo-meta="$seoMeta">
    <div class="border-b border-[#e3eaff] bg-sky-100">
        <div class="mx-auto max-w-7xl px-4 pb-10 pt-28 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-[#65646e]">
                <a href="{{ route('home') }}" class="transition hover:text-[#0a1589]">Beranda</a>
                <span aria-hidden="true" class="text-[#c7d6ff]">/</span>
                <a href="{{ route('docs.index') }}" class="transition hover:text-[#0a1589]">Dokumentasi</a>
                <span aria-hidden="true" class="text-[#c7d6ff]">/</span>
                <span class="text-[#100f12]">{{ $product->name }}</span>
            </nav>

            <h1 class="mt-5 text-[36px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[44px]">
                {{ $product->name }}
            </h1>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-[#0a1589] px-3 py-1.5 text-xs font-medium text-white">{{ $version->label }}</span>
                @if ($version->is_current)
                    <span class="inline-flex items-center rounded-full border border-[#e3eaff] bg-white px-3 py-1.5 text-xs font-medium text-[#0a1589]">Versi terkini</span>
                @endif
                @if ($version->released_at)
                    <span class="text-xs text-[#65646e]">Dirilis {{ $version->released_at->format('d F Y') }}</span>
                @endif
            </div>

            @if (filled($product->tagline))
                <p class="mt-4 max-w-2xl text-lg leading-7 text-[#65646e]">{{ $product->tagline }}</p>
            @endif
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-10 sm:px-6 lg:flex-row lg:px-8">
        <aside class="lg:w-72 lg:shrink-0">
            <div class="space-y-4 lg:sticky lg:top-20">
                <form method="GET" action="{{ route('docs.version', [$product, $version]) }}" role="search">
                    <label for="docs-search" class="sr-only">Cari di dokumentasi ini</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#aaa9ae]">
                            <flux:icon.magnifying-glass class="size-4" />
                        </span>
                        <input
                            id="docs-search"
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Cari di dokumentasi…"
                            class="w-full rounded-[16px] border border-[#e3eaff] bg-white py-3 pl-11 pr-4 text-sm text-[#100f12] transition placeholder:text-[#aaa9ae] focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]"
                        />
                    </div>
                </form>

                <x-docs.version-switcher :product="$product" :versions="$versions" :version="$version" />

                <nav aria-labelledby="docs-nav-heading" class="rounded-[16px] border border-[#e3eaff] bg-white p-4">
                    <h2 id="docs-nav-heading" class="px-3 text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Halaman</h2>

                    <div class="mt-2">
                        @if (! empty($navigation))
                            <x-docs.nav-tree :navigation="$navigation" :product="$product" :version="$version" />
                        @else
                            <p class="px-3 py-2 text-sm leading-6 text-[#65646e]">Belum ada halaman terpublikasi di versi ini.</p>
                        @endif
                    </div>
                </nav>

                <a href="{{ route('docs.index') }}" class="block px-1 text-sm font-medium text-[#65646e] transition hover:text-[#0a1589]">← Semua produk</a>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            @if ($search !== '')
                <div class="flex flex-wrap items-baseline justify-between gap-3">
                    <h2 class="text-[20px] font-semibold tracking-[-0.16px] text-[#100f12]">
                        {{ $results->count() }} hasil untuk “{{ $search }}”
                    </h2>
                    <a href="{{ route('docs.version', [$product, $version]) }}" class="text-sm font-medium text-[#65646e] transition hover:text-[#0a1589]">Hapus pencarian</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($results as $result)
                        <a href="{{ route('docs.page', [$product, $version, $result]) }}" class="block rounded-[16px] border border-[#e3eaff] bg-white p-5 transition hover:border-[#0a1589] hover:bg-[#fafbff]">
                            <h3 class="text-sm font-semibold text-[#100f12]">{{ $result->title }}</h3>
                            @if (filled($result->excerpt))
                                <p class="mt-1 line-clamp-2 text-sm leading-6 text-[#65646e]">{{ $result->excerpt }}</p>
                            @endif
                        </a>
                    @empty
                        <div class="rounded-[32px] border border-dashed border-[#c7d6ff] bg-[#fafbff] p-10 text-center">
                            <p class="text-sm font-semibold text-[#100f12]">Tidak ada halaman ditemukan</p>
                            <p class="mt-2 text-sm leading-6 text-[#65646e]">Coba kata kunci lain, atau jelajahi daftar halaman di samping.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <h2 class="text-[20px] font-semibold tracking-[-0.16px] text-[#100f12]">Daftar halaman</h2>

                @if (! empty($navigation))
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        @foreach ($navigation as $node)
                            @php
                                $section = $node['page'];
                                $children = $node['children'] ?? [];
                            @endphp

                            <div class="rounded-[16px] border border-[#e3eaff] bg-white p-5 transition hover:border-[#0a1589]">
                                <a href="{{ route('docs.page', [$product, $version, $section]) }}" class="text-sm font-semibold text-[#100f12] transition hover:text-[#0a1589]">{{ $section->title }}</a>

                                @if (! empty($children))
                                    <ul class="mt-3 space-y-1.5 border-t border-[#e3eaff] pt-3">
                                        @foreach ($children as $child)
                                            @php $childPage = $child['page']; @endphp
                                            <li>
                                                <a href="{{ route('docs.page', [$product, $version, $childPage]) }}" class="text-sm leading-6 text-[#65646e] transition hover:text-[#0a1589]">{{ $childPage->title }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 rounded-[32px] border border-dashed border-[#c7d6ff] bg-[#fafbff] p-10 text-center">
                        <p class="text-sm font-semibold text-[#100f12]">Dokumentasi versi ini belum tersedia</p>
                        <p class="mt-2 text-sm leading-6 text-[#65646e]">Halaman untuk {{ $product->name }} {{ $version->label }} sedang disiapkan.</p>
                    </div>
                @endif

                <div class="mt-8 rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6">
                    <h3 class="text-sm font-semibold tracking-[-0.16px] text-[#100f12]">Tidak menemukan yang Anda cari?</h3>
                    <p class="mt-1 text-sm leading-6 text-[#65646e]">Sampaikan pertanyaan Anda — kami akan membantu, dan jawabannya bisa jadi bahan dokumentasi berikutnya.</p>
                    <a href="{{ route('kontak') }}" class="mt-4 inline-flex items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-5 pb-3 pt-3.5 text-sm font-medium leading-none text-[#100f12] transition hover:bg-[#f3f6ff]">Hubungi kami</a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.public>
