<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="theme-color" content="#0a1589">

@php
    // Meta SEO: jika controller sudah share $seoMeta, pakai itu; fallback via MetaService
    $seoMeta = $seoMeta ?? app(\App\Services\MetaService::class)->set(
        filled($title ?? null) ? ['title' => $title] : []
    )->generate();
@endphp
<x-seo-meta :meta="$seoMeta" />

<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="16x16 32x32 48x48">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
<link rel="manifest" href="{{ route('manifest') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])
