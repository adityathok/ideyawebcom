@props(['post'])
@php
    $img = $post->imageUrl();
    $date = $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y');
@endphp
<article class="group flex flex-col overflow-hidden rounded-[16px] border border-[#e3eaff] bg-white transition hover:border-[#c7d6ff] hover:shadow-[0_12px_32px_-16px_rgba(10,21,137,0.25)]">
    <a href="{{ route('blog.show', $post) }}" class="block shrink-0">
        @if($img)
            <img src="{{ $img }}" alt="{{ $post->image_caption ?? $post->title }}" class="aspect-[16/9] w-full object-cover" loading="lazy" />
        @else
            <div class="flex aspect-[16/9] w-full items-center justify-center bg-[#f3f6ff] text-[#aaa9ae]">
                <svg class="size-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 9 8.25l4.5 4.5 4.5-4.5 3.75 3.75M2.25 15.75V6.75A2.25 2.25 0 0 1 4.5 4.5h15A2.25 2.25 0 0 1 21.75 6.75v9M2.25 15.75A2.25 2.25 0 0 0 4.5 18h15a2.25 2.25 0 0 0 2.25-2.25" /></svg>
            </div>
        @endif
    </a>

    <div class="flex min-w-0 flex-1 flex-col p-5">
        {{-- Meta: kategori --}}
        @if($post->category)
            <a href="{{ route('blog.category', $post->category) }}" class="inline-flex items-center gap-1.5 self-start rounded-full bg-[#f3f6ff] px-2.5 py-1 text-[11px] font-medium leading-none text-[#0a1589] transition hover:bg-[#e3eaff]">
                <span class="size-1.5 rounded-full" style="background: {{ $post->category->color ?? '#7d95ff' }}"></span>
                {{ $post->category->name }}
            </a>
        @endif

        <a href="{{ route('blog.show', $post) }}" class="mt-3 block">
            <h3 class="line-clamp-2 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12] transition group-hover:text-[#0a1589]">{{ $post->title }}</h3>
        </a>
        @if($post->excerpt)
            <p class="mt-2 line-clamp-2 text-sm leading-6 text-[#65646e]">{{ $post->excerpt }}</p>
        @endif

        {{-- Footer meta: date --}}
        <div class="mt-auto pt-4 text-xs leading-none text-[#787685]">
            <span>{{ $date }}</span>
        </div>
    </div>
</article>
