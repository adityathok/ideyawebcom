@props([
    'product',
    'versions',
    'version',
])

@if ($versions->count() > 1)
    <div class="rounded-[16px] border border-[#e3eaff] bg-white p-4">
        <label for="docs-version" class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Versi</label>

        {{-- Mengarah ke indeks versi, bukan halaman yang sama: slug halaman belum
             tentu ada di versi tujuan. --}}
        <select
            id="docs-version"
            onchange="window.location.href = this.value"
            class="mt-2 w-full rounded-[16px] border border-[#e3eaff] bg-white px-3 py-2.5 text-sm text-[#100f12] transition focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]"
        >
            @foreach ($versions as $item)
                <option value="{{ route('docs.version', [$product, $item]) }}" @selected($item->is($version))>
                    {{ $item->label }}@if ($item->is_current) — terkini @endif
                </option>
            @endforeach
        </select>
    </div>
@endif
