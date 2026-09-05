<x-layouts.public :title="'#' . $tag->name">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-[#111111]">#{{ $tag->name }}</h1>
        <p class="mt-1 text-sm text-[#626260]">{{ $posts->total() }} artikel dengan tag ini</p>
        <a href="{{ route('blog.index') }}" class="mt-4 inline-flex text-sm text-[#626260] hover:text-[#111111]">← Semua artikel</a>

        @if($posts->count())
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)<x-blog.post-card :post="$post" />@endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            <p class="mt-12 text-center text-[#626260]">Belum ada artikel dengan tag ini.</p>
        @endif
    </div>
</x-layouts.public>
