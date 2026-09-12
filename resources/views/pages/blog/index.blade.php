<x-layouts.public title="Blog">
    {{-- Header — blue tint block (surface-2) di atas canvas putih --}}
    <div class="border-b border-[#e3eaff] bg-[#f3f6ff]">
        <div class="mx-auto max-w-5xl px-4 pb-12 pt-28 sm:px-6 lg:px-8">
            <span class="inline-flex items-center gap-2 rounded-[16px] border border-[#e3eaff] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">
                <span aria-hidden="true" class="size-1.5 rounded-full bg-[#2b4bff]"></span>
                Blog &amp; Insight
            </span>
            <h1 class="mt-5 text-[40px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[48px] sm:tracking-[-1px]">Blog</h1>
            <p class="mt-3 max-w-2xl text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">Tulisan tentang membangun produk digital — dari ide, desain, hingga scale. Kurasi editorial, bukan template.</p>

            {{-- Search + filters — text-input (DESIGN.md): canvas, radius lg 16px, focus ke primary --}}
            <form method="GET" action="{{ route('blog.index') }}" class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#aaa9ae]">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel, topik, atau penulis…" class="w-full rounded-[16px] border border-[#e3eaff] bg-white py-3.5 pl-11 pr-4 text-sm text-[#100f12] transition placeholder:text-[#aaa9ae] focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]" />
                </div>
                <select name="category" class="rounded-[16px] border border-[#e3eaff] bg-white px-4 py-3.5 text-sm text-[#100f12] transition focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected(request('category')===$cat->slug)>{{ $cat->name }}@if(!empty($cat->posts_count)) ({{ $cat->posts_count }})@endif</option>
                    @endforeach
                </select>
                <select name="tag" class="rounded-[16px] border border-[#e3eaff] bg-white px-4 py-3.5 text-sm text-[#100f12] transition focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]">
                    <option value="">Semua Tag</option>
                    @foreach($tags as $t)
                        <option value="{{ $t->slug }}" @selected(request('tag')===$t->slug)>#{{ $t->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Cari</button>
                @if(request('q') || request('category') || request('tag'))
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff]">Reset</a>
                @endif
            </form>

            {{-- Active filter chips — pill (DESIGN.md rounded.pill) --}}
            @if(request('q') || request('category') || request('tag'))
                <div class="mt-4 flex flex-wrap gap-2">
                    @if(request('q'))
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#e3eaff] px-3 py-1.5 text-xs font-medium text-[#0a1589]">q: “{{ request('q') }}”</span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#e3eaff] px-3 py-1.5 text-xs font-medium text-[#0a1589]">kategori: {{ request('category') }}</span>
                    @endif
                    @if(request('tag'))
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#e3eaff] px-3 py-1.5 text-xs font-medium text-[#0a1589]">#{{ request('tag') }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Content: white canvas, feed kiri + sidebar kanan --}}
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1fr_300px]">
            {{-- Feed --}}
            <div>
                @if($posts->count())
                    <div class="divide-y divide-[#e3eaff] border-y border-[#e3eaff]">
                        @foreach($posts as $post)
                            <x-blog.post-card :post="$post" />
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="rounded-[32px] border border-dashed border-[#c7d6ff] bg-[#fafbff] p-10 text-center">
                        <p class="text-sm font-semibold text-[#100f12]">Tidak ada artikel ditemukan</p>
                        <p class="mt-2 text-sm leading-6 text-[#65646e]">Coba kata kunci lain atau reset filter di atas.</p>
                        <a href="{{ route('blog.index') }}" class="mt-5 inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Lihat semua artikel</a>
                    </div>
                @endif
            </div>

            {{-- Sidebar: feature-card (DESIGN.md) — surface-1, hairline, radius 16px, sticky --}}
            <aside class="hidden lg:block">
                <div class="sticky top-[72px] space-y-6">
                    {{-- Kategori --}}
                    <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Kategori</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($categories as $cat)
                                <a href="{{ route('blog.category', $cat) }}" class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition {{ request('category')===$cat->slug ? 'border-[#0a1589] bg-[#0a1589] text-white' : 'border-[#e3eaff] bg-white text-[#100f12] hover:bg-[#f3f6ff]' }}">
                                    <span class="size-2 rounded-full" style="background: {{ $cat->color ?? '#7d95ff' }}"></span>
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Topik populer</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($tags->take(14) as $t)
                                <a href="{{ route('blog.tag', $t) }}" class="rounded-full bg-[#f3f6ff] px-3 py-1.5 text-xs font-medium text-[#0a1589] transition hover:bg-[#e3eaff] {{ request('tag')===$t->slug ? 'ring-1 ring-[#0a1589]' : '' }}">#{{ $t->name }}</a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Newsletter / CTA — cta-banner (DESIGN.md): surface-2 + glow --}}
                    <div class="relative isolate overflow-hidden rounded-[16px] border border-[#e3eaff] bg-[#f3f6ff] p-6">
                        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(80%_120%_at_100%_0%,#e3eaff,transparent)]"></div>
                        <h3 class="text-[18px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Dapatkan tulisan terbaru</h3>
                        <p class="mt-2 text-sm leading-6 text-[#65646e]">Kurasi mingguan langsung ke inbox — tanpa spam.</p>
                        <a href="{{ route('blog.index') }}" class="mt-5 inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-5 pb-3 pt-3.5 text-sm font-medium leading-none text-white transition hover:bg-[#06105a]">Jelajahi arsip →</a>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Mobile categories/tags — below feed --}}
        <div class="mt-8 grid gap-4 lg:hidden">
            <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-5">
                <h3 class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Kategori</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat) }}" class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition {{ request('category')===$cat->slug ? 'border-[#0a1589] bg-[#0a1589] text-white' : 'border-[#e3eaff] bg-white text-[#100f12] hover:bg-[#f3f6ff]' }}">
                            <span class="size-2 rounded-full" style="background: {{ $cat->color ?? '#7d95ff' }}"></span>{{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-5">
                <h3 class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Topik</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($tags->take(12) as $t)
                        <a href="{{ route('blog.tag', $t) }}" class="rounded-full bg-[#f3f6ff] px-3 py-1.5 text-xs font-medium text-[#0a1589] transition hover:bg-[#e3eaff]">#{{ $t->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
