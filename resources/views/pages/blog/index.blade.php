<x-layouts.public title="Blog">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-2">
            <h1 class="text-3xl font-bold tracking-tight text-[#111111]">Blog</h1>
            <p class="text-[#626260]">Artikel, tutorial, dan insight terbaru</p>
        </div>

        <form method="GET" action="{{ route('blog.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..." class="w-full rounded-lg border border-[#d3cec6] bg-white px-4 py-2.5 text-sm text-[#111111] placeholder:text-[#7b7b78] focus:border-[#111111] focus:outline-none focus:ring-1 focus:ring-[#111111]" />
            <select name="category" class="rounded-lg border border-[#d3cec6] bg-white px-4 py-2.5 text-sm text-[#111111]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category')===$cat->slug)>{{ $cat->name }} ({{ $cat->posts_count ?? '' }})</option>
                @endforeach
            </select>
            <select name="tag" class="rounded-lg border border-[#d3cec6] bg-white px-4 py-2.5 text-sm text-[#111111]">
                <option value="">Semua Tag</option>
                @foreach($tags as $t)
                    <option value="{{ $t->slug }}" @selected(request('tag')===$t->slug)>#{{ $t->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-sm font-medium text-white hover:bg-black">Cari</button>
            @if(request('q') || request('category') || request('tag'))
                <a href="{{ route('blog.index') }}" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-sm font-medium text-[#111111] hover:bg-[#f5f1ec]">Reset</a>
            @endif
        </form>

        @if($posts->count())
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <x-blog.post-card :post="$post" />
                @endforeach
            </div>
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="mt-12 rounded-2xl border border-dashed border-[#d3cec6] bg-white p-12 text-center">
                <p class="text-[#626260]">Tidak ada artikel ditemukan.</p>
                <a href="{{ route('blog.index') }}" class="mt-4 inline-flex text-sm font-medium text-[#111111] underline">Lihat semua artikel</a>
            </div>
        @endif
    </div>
</x-layouts.public>
