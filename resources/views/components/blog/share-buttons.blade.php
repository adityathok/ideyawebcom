@props(['post'])
@php
    $url = route('blog.show', $post);
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($post->title);

    $networks = [
        [
            'label' => 'Facebook',
            'href' => 'https://www.facebook.com/sharer/sharer.php?u='.$encodedUrl,
            'path' => 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z',
        ],
        [
            'label' => 'X',
            'href' => 'https://twitter.com/intent/tweet?url='.$encodedUrl.'&text='.$encodedTitle,
            'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
        ],
        [
            'label' => 'LinkedIn',
            'href' => 'https://www.linkedin.com/sharing/share-offsite/?url='.$encodedUrl,
            'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
        ],
        [
            'label' => 'WhatsApp',
            'href' => 'https://wa.me/?text='.$encodedTitle.'%20'.$encodedUrl,
            'path' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z',
        ],
    ];
@endphp
<div class="flex flex-wrap items-center gap-3">
    <span class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Bagikan</span>
    <div class="flex items-center gap-2">
        @foreach($networks as $network)
            <a href="{{ $network['href'] }}" target="_blank" rel="noopener noreferrer" aria-label="Bagikan ke {{ $network['label'] }}" title="Bagikan ke {{ $network['label'] }}" class="inline-flex size-9 items-center justify-center rounded-full border border-[#e3eaff] bg-white text-[#65646e] transition hover:border-[#0a1589] hover:bg-[#f3f6ff] hover:text-[#0a1589]">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{{ $network['path'] }}" /></svg>
            </a>
        @endforeach

        {{-- Salin tautan — umpan balik visual via Alpine --}}
        <div class="contents" x-data="{ copied: false }">
            <button
                type="button"
                @click="navigator.clipboard.writeText(@js($url)); copied = true; setTimeout(() => copied = false, 2000)"
                :aria-label="copied ? 'Tautan tersalin' : 'Salin tautan artikel'"
                :title="copied ? 'Tautan tersalin' : 'Salin tautan artikel'"
                :class="copied ? 'border-[#0a1589] bg-[#0a1589] text-white' : 'border-[#e3eaff] bg-white text-[#65646e] hover:border-[#0a1589] hover:bg-[#f3f6ff] hover:text-[#0a1589]'"
                class="inline-flex size-9 items-center justify-center rounded-full border transition">
                <svg x-show="! copied" class="size-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                <svg x-show="copied" x-cloak class="size-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </button>
        </div>
    </div>
</div>
