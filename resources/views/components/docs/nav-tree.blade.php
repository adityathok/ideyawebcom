@props([
    'navigation' => [],
    'product',
    'version',
    'current' => null,
    'depth' => 0,
])

@php
    // Cek apakah subtree ini memuat halaman yang sedang dibuka, supaya section
    // induknya tetap tersorot saat pembaca berada di salah satu halaman anak.
    $containsCurrent = function (array $nodes) use (&$containsCurrent, $current): bool {
        foreach ($nodes as $node) {
            $page = $node['page'] ?? null;

            if ($page instanceof \App\Models\DocPage && $page->is($current)) {
                return true;
            }

            if (! empty($node['children']) && $containsCurrent($node['children'])) {
                return true;
            }
        }

        return false;
    };
@endphp

<ul @class([
    'space-y-0.5',
    'mt-0.5 ml-3 border-l border-[#e3eaff] pl-3' => $depth > 0,
])>
    @foreach ($navigation as $node)
        @php
            $page = $node['page'] ?? null;
            $children = $node['children'] ?? [];
        @endphp

        @if ($page instanceof \App\Models\DocPage)
            @php
                $isCurrent = $page->is($current);
                $hasCurrentDescendant = ! $isCurrent && $containsCurrent($children);
            @endphp

            <li>
                <a
                    href="{{ route('docs.page', [$product, $version, $page]) }}"
                    @if ($isCurrent) aria-current="page" @endif
                    @class([
                        'block rounded-lg px-3 py-2 text-sm leading-6 transition',
                        'bg-[#0a1589] font-medium text-white' => $isCurrent,
                        'font-medium text-[#0a1589] hover:bg-[#f3f6ff]' => $hasCurrentDescendant,
                        'text-[#65646e] hover:bg-[#f3f6ff] hover:text-[#100f12]' => ! $isCurrent && ! $hasCurrentDescendant,
                    ])
                >{{ $page->title }}</a>

                @if (! empty($children))
                    <x-docs.nav-tree
                        :navigation="$children"
                        :product="$product"
                        :version="$version"
                        :current="$current"
                        :depth="$depth + 1"
                    />
                @endif
            </li>
        @endif
    @endforeach
</ul>
