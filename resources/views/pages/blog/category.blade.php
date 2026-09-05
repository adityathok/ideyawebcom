<x-layouts.public :title="$category->name">
    <div class="border-b border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="size-3 rounded-full" style="background: {{ $category->color ?? '#52525b' }}"></span>
                <h1 class="text-[40px] font-medium leading-[1.15] tracking-[-0.8px] text-[#111111]">{{ $category->name }}</h1>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-[#626260] ring-1 ring-[#d3cec6]">{{ $posts->total() }} artikel</span>
            </div>
            @if($category->description)<p class="mt-3 max-w-2xl text-[18px] leading-7 text-[#626260]">{{ $category->description }}</p>@endif
            <a href="{{ route('blog.index') }}" class="mt-4 inline-flex text-sm font-medium text-[#626260] hover:text-[#111111]">← Semua artikel</a>
        </div>
    </div>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @if($posts->count())
            <div class="divide-y divide-[#ebe7e1] border-y border-[#ebe7e1]">
                @foreach($posts as $post)<x-blog.post-card :post="$post" />@endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            <div class="rounded-xl border border-dashed border-[#d3cec6] bg-white p-10 text-center">
                <p class="text-sm font-medium text-[#111111]">Belum ada artikel di kategori ini</p>
                <a href="{{ route('blog.index') }}" class="mt-3 inline-flex text-sm font-medium text-[#111111] underline">Jelajahi semua artikel</a>
            </div>
        @endif
    </div>
</x-layouts.public>
