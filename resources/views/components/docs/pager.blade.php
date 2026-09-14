@props([
    'previous' => null,
    'next' => null,
    'product',
    'version',
])

@if ($previous || $next)
    <nav aria-label="Navigasi halaman dokumentasi" class="grid gap-3 border-t border-[#e3eaff] pt-6 sm:grid-cols-2">
        @if ($previous)
            <a href="{{ route('docs.page', [$product, $version, $previous]) }}" class="group rounded-[16px] border border-[#e3eaff] bg-white p-4 transition hover:border-[#0a1589] hover:bg-[#fafbff]">
                <span class="block text-xs font-medium text-[#65646e]">← Sebelumnya</span>
                <span class="mt-1 block text-sm font-semibold text-[#100f12] transition group-hover:text-[#0a1589]">{{ $previous->title }}</span>
            </a>
        @endif

        @if ($next)
            <a href="{{ route('docs.page', [$product, $version, $next]) }}" class="group rounded-[16px] border border-[#e3eaff] bg-white p-4 transition hover:border-[#0a1589] hover:bg-[#fafbff] sm:col-start-2 sm:text-right">
                <span class="block text-xs font-medium text-[#65646e]">Berikutnya →</span>
                <span class="mt-1 block text-sm font-semibold text-[#100f12] transition group-hover:text-[#0a1589]">{{ $next->title }}</span>
            </a>
        @endif
    </nav>
@endif
