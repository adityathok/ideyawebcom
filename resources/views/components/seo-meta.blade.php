@props(['meta' => []])
@php
    $m = $meta ?? [];
    $titleFull = $m['title_full'] ?? $m['title'] ?? config('app.name', 'IdeyaWeb');
    $title = $m['title'] ?? $titleFull;
    $description = $m['description'] ?? null;
    $keywords = $m['keywords'] ?? null;
    $canonical = $m['canonical'] ?? $m['url'] ?? null;
    $robots = $m['robots'] ?? 'index, follow';
    $url = $m['url'] ?? $canonical;
    $siteName = $m['site_name'] ?? config('app.name');
    $locale = $m['locale'] ?? str_replace('_', '-', app()->getLocale());
    $type = $m['type'] ?? 'website';
    $image = $m['image'] ?? null;
    $imageAlt = $m['image_alt'] ?? $title;
    $twitterCard = $m['twitter_card'] ?? ($image ? 'summary_large_image' : 'summary');
    $publishedTime = $m['published_time'] ?? null;
    $author = $m['author'] ?? null;
    $section = $m['section'] ?? null;
    $tags = $m['tags'] ?? null;
    $jsonLd = $m['json_ld'] ?? null;
@endphp
<title>{{ $titleFull }}</title>
@if($description)<meta name="description" content="{{ $description }}" />@endif
@if($keywords)<meta name="keywords" content="{{ $keywords }}" />@endif
<meta name="robots" content="{{ $robots }}" />
@if($canonical)<link rel="canonical" href="{{ $canonical }}" />@endif

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:locale" content="{{ $locale }}" />
<meta property="og:type" content="{{ $type }}" />
<meta property="og:title" content="{{ $title }}" />
@if($description)<meta property="og:description" content="{{ $description }}" />@endif
@if($url)<meta property="og:url" content="{{ $url }}" />@endif
@if($image)<meta property="og:image" content="{{ $image }}" />
<meta property="og:image:alt" content="{{ $imageAlt }}" />@endif
@if($publishedTime)<meta property="article:published_time" content="{{ $publishedTime }}" />@endif
@if($author)<meta property="article:author" content="{{ $author }}" />@endif
@if($section)<meta property="article:section" content="{{ $section }}" />@endif
@if(is_array($tags))@foreach($tags as $t)<meta property="article:tag" content="{{ $t }}" />@endforeach@endif

{{-- Twitter --}}
<meta name="twitter:card" content="{{ $twitterCard }}" />
<meta name="twitter:title" content="{{ $title }}" />
@if($description)<meta name="twitter:description" content="{{ $description }}" />@endif
@if($image)<meta name="twitter:image" content="{{ $image }}" />
<meta name="twitter:image:alt" content="{{ $imageAlt }}" />@endif

@if($jsonLd)
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
