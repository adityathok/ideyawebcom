<x-layouts.public :title="$post->title">
    <article class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <a href="{{ route('blog.index') }}" class="text-[#626260] hover:text-[#111111]">← Kembali ke Blog</a>
        </div>

        @if($post->category)
            <a href="{{ route('blog.category', $post->category) }}" class="mt-6 inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: {{ $post->category->color ?? '#52525b' }}">{{ $post->category->name }}</a>
        @endif

        <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-[#111111] sm:text-4xl">{{ $post->title }}</h1>

        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-[#626260]">
            <span class="flex items-center gap-2">
                <span class="flex size-7 items-center justify-center rounded-full bg-[#111111] text-xs font-bold text-white">{{ \Illuminate\Support\Str::substr($post->author->name, 0, 1) }}</span>
                {{ $post->author->name }}
            </span>
            <span>·</span>
            <span>{{ $post->published_at?->format('d F Y') ?? $post->created_at->format('d F Y') }}</span>
            <span>·</span>
            <span>{{ $post->view_count }} views</span>
        </div>

        @php $heroImg = $post->imageUrl(); @endphp
        @if($heroImg)
            <figure class="mt-8">
                <img src="{{ $heroImg }}" alt="{{ $post->image_caption ?? $post->title }}" class="w-full rounded-2xl border border-[#d3cec6] object-cover" />
                @if(filled($post->image_caption))
                    <figcaption class="mt-2 text-center text-sm leading-6 text-[#626260]">{{ $post->image_caption }}</figcaption>
                @endif
            </figure>
        @endif

        @if($post->excerpt)
            <p class="mt-8 text-lg leading-7 text-[#626260] border-l-4 border-[#111111] pl-4">{{ $post->excerpt }}</p>
        @endif

        <div class="prose prose-neutral mt-8 max-w-none prose-headings:font-semibold prose-a:text-[#111111] prose-a:underline-offset-2">
            {!! nl2br(e($post->body)) !!}
        </div>

        @if($post->tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2 border-t border-[#d3cec6] pt-6">
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag) }}" class="rounded-full bg-[#ebe7e1] px-3 py-1.5 text-sm font-medium text-[#111111] hover:bg-[#d3cec6]">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        <!-- Share / Nav -->
        <div class="mt-8 flex items-center justify-between border-t border-[#d3cec6] pt-6">
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-[#626260] hover:text-[#111111]">← Semua artikel</a>
            <div class="flex gap-2">
                @if($post->category)
                    <a href="{{ route('blog.category', $post->category) }}" class="rounded-full border border-[#d3cec6] px-4 py-2 text-sm font-medium hover:bg-[#f5f1ec]">Kategori: {{ $post->category->name }}</a>
                @endif
            </div>
        </div>

        @if($related->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-lg font-semibold text-[#111111]">Artikel Terkait</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel) }}" class="rounded-xl border border-[#d3cec6] bg-white p-4">
                            @if($rel->category)<p class="text-xs font-medium" style="color: {{ $rel->category->color ?? '#71717a' }}">{{ $rel->category->name }}</p>@endif
                            <h3 class="mt-1 line-clamp-2 text-sm font-semibold text-[#111111]">{{ $rel->title }}</h3>
                            <p class="mt-2 line-clamp-2 text-xs text-[#626260]">{{ $rel->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</x-layouts.public>
