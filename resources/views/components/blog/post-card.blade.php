@props(['post'])
@php $img = $post->imageUrl(); @endphp
<article class="group flex flex-col overflow-hidden rounded-2xl border border-[#d3cec6] bg-white transition">
    @if($img)
        <div class="aspect-[16/9] overflow-hidden bg-[#ebe7e1]">
            <img src="{{ $img }}" alt="{{ $post->image_caption ?? $post->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy" />
        </div>
    @else
        <div class="aspect-[16/9] bg-gradient-to-br from-[#ebe7e1] to-[#d3cec6] flex items-center justify-center">
            <span class="text-3xl">📝</span>
        </div>
    @endif
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-2 text-xs">
            @if($post->category)
                <a href="{{ route('blog.category', $post->category) }}" class="rounded-full px-2.5 py-1 font-medium text-white" style="background: {{ $post->category->color ?? '#52525b' }}">{{ $post->category->name }}</a>
            @endif
            <span class="text-[#626260]">{{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }}</span>
            <span class="text-[#626260]">·</span>
            <span class="text-[#626260]">{{ $post->view_count }} views</span>
        </div>
        <a href="{{ route('blog.show', $post) }}" class="mt-3">
            <h3 class="line-clamp-2 text-lg font-semibold leading-tight text-[#111111] group-hover:text-[#111111]">{{ $post->title }}</h3>
        </a>
        <p class="mt-2 line-clamp-2 text-sm leading-6 text-[#626260]">{{ $post->excerpt }}</p>
        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach($post->tags as $tag)
                <a href="{{ route('blog.tag', $tag) }}" class="rounded-full border border-[#d3cec6] bg-[#ebe7e1] px-2.5 py-1 text-xs font-medium text-[#111111] hover:bg-[#d3cec6]">#{{ $tag->name }}</a>
            @endforeach
        </div>
        <div class="mt-4 flex items-center gap-2 border-t border-[#ebe7e1] pt-4">
            <div class="size-7 rounded-full bg-[#111111] text-white flex items-center justify-center text-xs font-bold">{{ \Illuminate\Support\Str::substr($post->author->name, 0, 1) }}</div>
            <span class="text-xs font-medium text-[#111111]">{{ $post->author->name }}</span>
        </div>
    </div>
</article>
