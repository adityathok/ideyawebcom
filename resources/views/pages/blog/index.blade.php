<x-layouts.public title="Blog">
    {{-- Medium-like header: title + subtitle on canvas --}}
    <div class="border-b border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-[40px] font-medium leading-[1.15] tracking-[-0.8px] text-[#111111]">Blog</h1>
            <p class="mt-2 max-w-2xl text-[18px] font-normal leading-7 tracking-[-0.1px] text-[#626260]">Tulisan tentang membangun produk digital — dari ide, desain, hingga scale. Kurasi editorial, bukan template.</p>

            {{-- Search + filters — text-input (DESIGN.md): surface-1, rounded md 8px, hairline --}}
            <form method="GET" action="{{ route('blog.index') }}" class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#9c9fa5]">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel, topik, atau penulis…" class="w-full rounded-lg border border-[#d3cec6] bg-white py-[10px] pl-9 pr-4 text-sm text-[#111111] placeholder:text-[#9c9fa5] focus:border-[#111111] focus:outline-none focus:ring-1 focus:ring-[#111111]" />
                </div>
                <select name="category" class="rounded-lg border border-[#d3cec6] bg-white px-3 py-[10px] text-sm text-[#111111] focus:border-[#111111] focus:outline-none focus:ring-1 focus:ring-[#111111]">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" @selected(request('category')===$cat->slug)>{{ $cat->name }}@if(!empty($cat->posts_count)) ({{ $cat->posts_count }})@endif</option>
                    @endforeach
                </select>
                <select name="tag" class="rounded-lg border border-[#d3cec6] bg-white px-3 py-[10px] text-sm text-[#111111] focus:border-[#111111] focus:outline-none focus:ring-1 focus:ring-[#111111]">
                    <option value="">Semua Tag</option>
                    @foreach($tags as $t)
                        <option value="{{ $t->slug }}" @selected(request('tag')===$t->slug)>#{{ $t->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-black">Cari</button>
                @if(request('q') || request('category') || request('tag'))
                    <a href="{{ route('blog.index') }}" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-[15px] font-medium leading-none text-[#111111] hover:bg-[#ebe7e1]">Reset</a>
                @endif
            </form>

            {{-- Active filter chips — pill (DESIGN.md rounded.pill) --}}
            @if(request('q') || request('category') || request('tag'))
                <div class="mt-4 flex flex-wrap gap-2">
                    @if(request('q'))
                        <span class="inline-flex items-center gap-2 rounded-full border border-[#d3cec6] bg-white px-3 py-1.5 text-xs font-medium text-[#111111]">q: “{{ request('q') }}”</span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center gap-2 rounded-full border border-[#d3cec6] bg-white px-3 py-1.5 text-xs font-medium text-[#111111]">kategori: {{ request('category') }}</span>
                    @endif
                    @if(request('tag'))
                        <span class="inline-flex items-center gap-2 rounded-full border border-[#d3cec6] bg-white px-3 py-1.5 text-xs font-medium text-[#111111]">#{{ request('tag') }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Content: feed left + sidebar right — Medium layout --}}
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1fr_300px]">
            {{-- Feed --}}
            <div>
                @if($posts->count())
                    <div class="divide-y divide-[#ebe7e1] border-y border-[#ebe7e1]">
                        @foreach($posts as $post)
                            <x-blog.post-card :post="$post" />
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-[#d3cec6] bg-white p-10 text-center">
                        <p class="text-sm font-medium text-[#111111]">Tidak ada artikel ditemukan</p>
                        <p class="mt-2 text-sm leading-6 text-[#626260]">Coba kata kunci lain atau reset filter di atas.</p>
                        <a href="{{ route('blog.index') }}" class="mt-4 inline-flex rounded-lg bg-[#111111] px-4 py-2 text-sm font-medium text-white hover:bg-black">Lihat semua artikel</a>
                    </div>
                @endif
            </div>

            {{-- Sidebar: sticky, cream cards — discovery like Medium right rail --}}
            <aside class="hidden lg:block">
                <div class="sticky top-[72px] space-y-6">
                    {{-- Kategori --}}
                    <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                        <h2 class="text-sm font-semibold tracking-tight text-[#111111]">Kategori</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($categories as $cat)
                                <a href="{{ route('blog.category', $cat) }}" class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium {{ request('category')===$cat->slug ? 'border-[#111111] bg-[#111111] text-white' : 'border-[#d3cec6] bg-white text-[#111111] hover:bg-[#f5f1ec]' }}">
                                    <span class="size-2 rounded-full" style="background: {{ $cat->color ?? '#52525b' }}"></span>
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                        <h2 class="text-sm font-semibold tracking-tight text-[#111111]">Topik populer</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($tags->take(14) as $t)
                                <a href="{{ route('blog.tag', $t) }}" class="rounded-full bg-[#ebe7e1] px-3 py-1.5 text-xs font-medium text-[#111111] hover:bg-[#d3cec6] {{ request('tag')===$t->slug ? 'ring-1 ring-[#111111]' : '' }}">#{{ $t->name }}</a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Newsletter / CTA — surface-2 --}}
                    <div class="rounded-xl border border-[#ebe7e1] bg-[#ebe7e1] p-6">
                        <h3 class="text-[18px] font-medium leading-tight tracking-[-0.1px] text-[#111111]">Dapatkan tulisan terbaru</h3>
                        <p class="mt-2 text-sm leading-6 text-[#626260]">Kurasi mingguan langsung ke inbox — tanpa spam.</p>
                        <a href="{{ route('blog.index') }}" class="mt-4 inline-flex rounded-lg bg-[#111111] px-4 py-2 text-sm font-medium text-white hover:bg-black">Jelajahi arsip →</a>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Mobile categories/tags — below feed --}}
        <div class="mt-8 grid gap-4 lg:hidden">
            <div class="rounded-xl border border-[#d3cec6] bg-white p-5">
                <h3 class="text-sm font-semibold text-[#111111]">Kategori</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat) }}" class="inline-flex items-center gap-1.5 rounded-full border border-[#d3cec6] bg-white px-3 py-1.5 text-xs font-medium text-[#111111] hover:bg-[#f5f1ec]">
                            <span class="size-2 rounded-full" style="background: {{ $cat->color ?? '#52525b' }}"></span>{{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="rounded-xl border border-[#d3cec6] bg-white p-5">
                <h3 class="text-sm font-semibold text-[#111111]">Topik</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($tags->take(12) as $t)
                        <a href="{{ route('blog.tag', $t) }}" class="rounded-full bg-[#ebe7e1] px-3 py-1.5 text-xs font-medium text-[#111111]">#{{ $t->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
