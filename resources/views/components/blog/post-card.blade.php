@props(['post'])
@php
    $img = $post->imageUrl();
    $date = $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y');
    $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($post->author->name ?? 'A', 0, 1));
@endphp
<article class="group flex gap-4 py-6 sm:gap-6">
    <div class="min-w-0 flex-1">
        {{-- Medium meta row: avatar · author · in category --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex size-6 items-center justify-center rounded-full bg-[#111111] text-[11px] font-semibold leading-none text-white">{{ $initial }}</span>
            <span class="text-[13px] font-medium leading-none text-[#111111]">{{ $post->author->name }}</span>
            @if($post->category)
                <span class="text-[#ebe7e1]">·</span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="size-1.5 rounded-full" style="background: {{ $post->category->color ?? '#52525b' }}"></span>
                    <a href="{{ route('blog.category', $post->category) }}" class="text-xs font-medium text-[#626260] hover:text-[#111111] hover:underline">{{ $post->category->name }}</a>
                </span>
            @endif
            <span class="hidden text-[#ebe7e1] sm:inline">·</span>
            <span class="hidden text-xs text-[#7b7b78] sm:inline">{{ $date }}</span>
        </div>

        <a href="{{ route('blog.show', $post) }}" class="mt-2 block">
            <h3 class="line-clamp-2 text-[20px] font-semibold leading-tight tracking-tight text-[#111111] group-hover:text-[#111111] sm:text-[22px] sm:font-medium sm:tracking-[-0.3px]">{{ $post->title }}</h3>
        </a>
        @if($post->excerpt)
            <p class="mt-1 line-clamp-2 hidden text-sm leading-6 text-[#626260] sm:block">{{ $post->excerpt }}</p>
        @endif

        {{-- Footer meta: date · views · tags --}}
        <div class="mt-3 flex items-center gap-2 text-xs leading-none text-[#7b7b78]">
            <span class="sm:hidden">{{ $date }}</span>
            <span class="hidden sm:inline">{{ $post->view_count }} views</span>
            <span class="sm:hidden">· {{ $post->view_count }} views</span>
            @if($post->tags->isNotEmpty())
                <span class="hidden items-center gap-1.5 sm:inline-flex">
                    <span class="text-[#ebe7e1]">·</span>
                    @foreach($post->tags->take(2) as $tag)
                        <a href="{{ route('blog.tag', $tag) }}" class="rounded-full bg-[#ebe7e1] px-2 py-1 text-[11px] font-medium text-[#626260] hover:bg-[#d3cec6] hover:text-[#111111]">#{{ $tag->name }}</a>
                    @endforeach
                </span>
            @endif
            <span class="ml-auto inline-flex items-center gap-1.5 text-[#9c9fa5]">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 11.186 0Z" /></svg>
            </span>
        </div>
    </div>

    <a href="{{ route('blog.show', $post) }}" class="shrink-0">
        @if($img)
            <img src="{{ $img }}" alt="{{ $post->image_caption ?? $post->title }}" class="h-20 w-20 rounded-md border border-[#ebe7e1] object-cover sm:h-28 sm:w-28" loading="lazy" />
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-md border border-[#ebe7e1] bg-[#ebe7e1] text-[#9c9fa5] sm:h-28 sm:w-28">
                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 9 8.25l4.5 4.5 4.5-4.5 3.75 3.75M2.25 15.75V6.75A2.25 2.25 0 0 1 4.5 4.5h15A2.25 2.25 0 0 1 21.75 6.75v9M2.25 15.75A2.25 2.25 0 0 0 4.5 18h15a2.25 2.25 0 0 0 2.25-2.25" /></svg>
            </div>
        @endif
    </a>
</article>
