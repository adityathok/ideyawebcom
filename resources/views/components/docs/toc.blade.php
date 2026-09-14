@props(['toc' => []])

@if (count($toc) > 1)
    <nav aria-labelledby="docs-toc-heading" class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-5">
        <h2 id="docs-toc-heading" class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Di halaman ini</h2>

        <ul class="mt-3 space-y-1.5 text-sm">
            @foreach ($toc as $item)
                <li @class([
                    'pl-3' => $item['level'] === 3,
                    'pl-6' => $item['level'] >= 4,
                ])>
                    <a href="#{{ $item['id'] }}" class="block leading-6 text-[#65646e] transition hover:text-[#0a1589]">{{ $item['title'] }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
@endif
