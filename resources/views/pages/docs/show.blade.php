<x-layouts.public :title="$page->title" :seo-meta="$seoMeta">
    <div class="border-b border-[#e3eaff] bg-sky-100">
        <div class="mx-auto max-w-7xl px-4 pb-8 pt-28 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-[#65646e]">
                <a href="{{ route('home') }}" class="transition hover:text-[#0a1589]">Beranda</a>
                <span aria-hidden="true" class="text-[#c7d6ff]">/</span>
                <a href="{{ route('docs.index') }}" class="transition hover:text-[#0a1589]">Dokumentasi</a>
                <span aria-hidden="true" class="text-[#c7d6ff]">/</span>
                <a href="{{ route('docs.version', [$product, $version]) }}" class="transition hover:text-[#0a1589]">{{ $product->name }}</a>
                <span aria-hidden="true" class="text-[#c7d6ff]">/</span>
                <span class="text-[#100f12]">{{ $version->label }}</span>
            </nav>

            <h1 class="mt-5 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">{{ $page->title }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-[#65646e]">
                <span class="inline-flex items-center rounded-full bg-white/70 px-3 py-1.5 text-xs font-medium text-[#0a1589]">{{ $product->name }} {{ $version->label }}</span>
                @if ($page->updated_at)
                    <span>Terakhir diperbarui {{ $page->updated_at->format('d F Y') }}</span>
                @endif
                <span aria-hidden="true" class="text-[#c7d6ff]">·</span>
                <span>{{ $page->view_count }} kali dibaca</span>
            </div>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-10 sm:px-6 lg:flex-row lg:px-8">
        <aside class="lg:w-72 lg:shrink-0">
            <div class="space-y-4 lg:sticky lg:top-20">
                <nav aria-labelledby="docs-nav-heading" class="rounded-[16px] border border-[#e3eaff] bg-white p-4">
                    <h2 id="docs-nav-heading" class="px-3 text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Halaman</h2>

                    <div class="mt-2">
                        @if (! empty($navigation))
                            <x-docs.nav-tree :navigation="$navigation" :product="$product" :version="$version" :current="$page" />
                        @else
                            <p class="px-3 py-2 text-sm leading-6 text-[#65646e]">Belum ada halaman lain di versi ini.</p>
                        @endif
                    </div>
                </nav>

                <x-docs.version-switcher :product="$product" :versions="$versions" :version="$version" />

                <a href="{{ route('docs.version', [$product, $version]) }}" class="block px-1 text-sm font-medium text-[#65646e] transition hover:text-[#0a1589]">← Indeks {{ $product->name }} {{ $version->label }}</a>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col gap-10 xl:flex-row">
            <article class="min-w-0 max-w-3xl flex-1">
                @if (filled($page->excerpt))
                    <p class="border-l-4 border-[#0a1589] pl-4 text-lg leading-7 text-[#65646e]">{{ $page->excerpt }}</p>
                @endif

                <div class="prose mt-8 max-w-none">
                    {!! $content['html'] !!}
                </div>

                <div class="mt-10">
                    <x-docs.pager :previous="$previous" :next="$next" :product="$product" :version="$version" />
                </div>

                <div class="mt-10">
                    <livewire:docs.feedback :page-id="$page->id" />
                </div>
            </article>

            <aside class="hidden xl:block xl:w-64 xl:shrink-0">
                <div class="xl:sticky xl:top-20">
                    <x-docs.toc :toc="$content['toc']" />
                </div>
            </aside>
        </div>
    </div>
</x-layouts.public>
