<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    // Meta SEO: jika controller sudah share $seoMeta, pakai itu; fallback via MetaService
    $seoMeta = $seoMeta ?? app(\App\Services\MetaService::class)->set(
        filled($title ?? null) ? ['title' => $title] : []
    )->generate();
@endphp
<x-seo-meta :meta="$seoMeta" />

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@vite(['resources/css/app.css', 'resources/js/app.js'])
