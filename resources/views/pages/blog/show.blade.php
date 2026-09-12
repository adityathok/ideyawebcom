<x-layouts.public :title="$post->title" :seo-meta="$seoMeta">
    <article class="mx-auto max-w-3xl px-4 pb-16 pt-28 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <a href="{{ route('blog.index') }}" class="text-[#65646e] transition hover:text-[#0a1589]">← Kembali ke Blog</a>
        </div>

        @if($post->category)
            <a href="{{ route('blog.category', $post->category) }}" class="mt-8 inline-flex items-center gap-2 rounded-full border border-[#e3eaff] bg-[#f3f6ff] px-3 py-1.5 text-xs font-medium text-[#0a1589] transition hover:bg-[#e3eaff]">
                <span class="size-1.5 rounded-full" style="background: {{ $post->category->color ?? '#7d95ff' }}"></span>
                {{ $post->category->name }}
            </a>
        @endif

        <h1 class="mt-4 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">{{ $post->title }}</h1>

        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-[#65646e]">
            <span class="flex items-center gap-2">
                <span class="flex size-7 items-center justify-center rounded-full bg-[#0a1589] text-xs font-semibold text-white">{{ \Illuminate\Support\Str::substr($post->author->name, 0, 1) }}</span>
                {{ $post->author->name }}
            </span>
            <span aria-hidden="true" class="text-[#c7d6ff]">·</span>
            <span>{{ $post->published_at?->format('d F Y') ?? $post->created_at->format('d F Y') }}</span>
            <span aria-hidden="true" class="text-[#c7d6ff]">·</span>
            <span>{{ $post->view_count }} views</span>
        </div>

        @php $heroImg = $post->imageUrl(); @endphp
        @if($heroImg)
            <figure class="mt-8">
                <img src="{{ $heroImg }}" alt="{{ $post->image_caption ?? $post->title }}" class="w-full rounded-[16px] border border-[#e3eaff] object-cover" />
                @if(filled($post->image_caption))
                    <figcaption class="mt-2 text-center text-sm leading-6 text-[#65646e]">{{ $post->image_caption }}</figcaption>
                @endif
            </figure>
        @endif

        @if($post->excerpt)
            <p class="mt-8 border-l-4 border-[#0a1589] pl-4 text-lg leading-7 text-[#65646e]">{{ $post->excerpt }}</p>
        @endif

        @php $bodyIsHtml = strip_tags($post->body) !== $post->body; @endphp
        <div class="prose mt-8 max-w-none">
            {!! $bodyIsHtml ? $post->body : nl2br(e($post->body)) !!}
        </div>

        @if($post->tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2 border-t border-[#e3eaff] pt-6">
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag) }}" class="rounded-full bg-[#f3f6ff] px-3 py-1.5 text-sm font-medium text-[#0a1589] transition hover:bg-[#e3eaff]">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        <!-- Share / Nav -->
        <div class="mt-8 border-t border-[#e3eaff] pt-6">
            <x-blog.share-buttons :post="$post" />

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-[#65646e] transition hover:text-[#0a1589]">← Semua artikel</a>
                @if($post->category)
                    <a href="{{ route('blog.category', $post->category) }}" class="rounded-full border border-[#e3eaff] px-4 py-2 text-sm font-medium text-[#100f12] transition hover:bg-[#f3f6ff]">Kategori: {{ $post->category->name }}</a>
                @endif
            </div>
        </div>

        @if($related->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-[20px] font-semibold tracking-[-0.16px] text-[#100f12]">Artikel Terkait</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel) }}" class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-5 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                            @if($rel->category)<p class="text-xs font-medium text-[#0a1589]">{{ $rel->category->name }}</p>@endif
                            <h3 class="mt-1 line-clamp-2 text-sm font-semibold text-[#100f12]">{{ $rel->title }}</h3>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-[#65646e]">{{ $rel->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</x-layouts.public>
