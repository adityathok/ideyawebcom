<x-layouts.public :title="'#' . $tag->name">
    {{-- Header — blue tint block (surface-2) di atas canvas putih --}}
    <div class="border-b border-[#e3eaff] bg-[#f3f6ff]">
        <div class="mx-auto max-w-5xl px-4 pb-10 pt-28 sm:px-6 lg:px-8">
            <span class="inline-flex items-center gap-2 rounded-[16px] border border-[#e3eaff] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">
                <span aria-hidden="true" class="size-1.5 rounded-full bg-[#2b4bff]"></span>
                Tag
            </span>
            <h1 class="mt-5 text-[40px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[48px] sm:tracking-[-1px]">#{{ $tag->name }}</h1>
            <p class="mt-3 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">{{ $posts->total() }} artikel dengan tag ini</p>
            <a href="{{ route('blog.index') }}" class="mt-5 inline-flex text-sm font-medium text-[#0a1589] transition hover:text-[#06105a]">← Semua artikel</a>
        </div>
    </div>

    {{-- Feed — white canvas --}}
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        @if($posts->count())
            <div class="divide-y divide-[#e3eaff] border-y border-[#e3eaff]">
                @foreach($posts as $post)<x-blog.post-card :post="$post" />@endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            <div class="rounded-[32px] border border-dashed border-[#c7d6ff] bg-[#fafbff] p-10 text-center">
                <p class="text-sm font-semibold text-[#100f12]">Belum ada artikel dengan tag ini</p>
                <a href="{{ route('blog.index') }}" class="mt-5 inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Jelajahi semua artikel</a>
            </div>
        @endif
    </div>
</x-layouts.public>
