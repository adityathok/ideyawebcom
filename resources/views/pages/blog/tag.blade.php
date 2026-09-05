<x-layouts.public :title="'#' . $tag->name">
    <div class="border-b border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-[40px] font-medium leading-[1.15] tracking-[-0.8px] text-[#111111]">#{{ $tag->name }}</h1>
            <p class="mt-2 text-sm text-[#626260]">{{ $posts->total() }} artikel dengan tag ini</p>
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
                <p class="text-sm font-medium text-[#111111]">Belum ada artikel dengan tag ini</p>
                <a href="{{ route('blog.index') }}" class="mt-3 inline-flex text-sm font-medium text-[#111111] underline">Jelajahi semua artikel</a>
            </div>
        @endif
    </div>
</x-layouts.public>
