<?php

use App\Enums\DocStatus;
use App\Models\DocPage;
use App\Models\DocVersion;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Halaman Dokumentasi')] class extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public string $filterProduct = '';

    #[Url]
    public string $filterVersion = '';

    public string $filterStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterVersion(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProduct(): void
    {
        $this->resetPage();

        // Versi yang sudah tidak berada di produk terpilih harus dilepas.
        if ($this->filterVersion !== '' && ! DocVersion::whereKey($this->filterVersion)->where('product_id', $this->filterProduct)->exists()) {
            $this->filterVersion = '';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterProduct', 'filterVersion', 'filterStatus']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        DocPage::findOrFail($id)->delete();

        Flux::toast(variant: 'success', text: 'Halaman dokumentasi dihapus.');
    }

    public function moveUp(int $id): void
    {
        $this->move($id, -1);
    }

    public function moveDown(int $id): void
    {
        $this->move($id, 1);
    }

    private function move(int $id, int $offset): void
    {
        $page = DocPage::findOrFail($id);

        // Urutan hanya berarti di antara saudara sekandung: satu versi dan satu induk.
        $siblings = DocPage::query()
            ->where('version_id', $page->version_id)
            ->where('parent_id', $page->parent_id)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->values();

        $index = $siblings->search(fn (DocPage $item): bool => $item->id === $page->id);

        if ($index === false) {
            return;
        }

        $target = $index + $offset;

        if ($target < 0 || $target >= $siblings->count()) {
            return;
        }

        $items = $siblings->all();
        [$items[$index], $items[$target]] = [$items[$target], $items[$index]];

        // `withoutTimestamps` supaya `updated_at` (dipakai untuk "Terakhir diperbarui"
        // dan dateModified di SEO) tidak ikut berubah hanya karena urutan diubah.
        DocPage::withoutTimestamps(function () use ($items): void {
            foreach ($items as $position => $item) {
                $item->update(['sort_order' => $position]);
            }
        });
    }
}; ?>
<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Halaman Dokumentasi</flux:heading>
            <flux:subheading>Kelola halaman dokumentasi tiap produk ({{ \App\Models\DocPage::count() }} total, {{ \App\Models\DocPage::published()->count() }} published).</flux:subheading>
        </div>
        <a href="{{ route('admin.doc-form') }}" wire:navigate class="inline-flex items-center rounded-lg bg-[#0a1589] px-4 py-2 text-sm font-medium text-white hover:bg-[#06105a]">+ Halaman</a>
    </div>

    <!-- Filter — card putih DESIGN.md: hairline #e3eaff, rounded-xl 12px -->
    <div class="mt-6 rounded-xl border border-[#e3eaff] bg-white p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" label="Cari halaman" placeholder="Cari judul..." icon="magnifying-glass" />
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:w-[660px]">
                <flux:select wire:model.live="filterProduct" label="Produk" placeholder="Semua Produk">
                    <flux:select.option value="">Semua Produk</flux:select.option>
                    @foreach (\App\Models\Product::orderBy('name')->get() as $product)
                        <flux:select.option value="{{ $product->id }}">{{ $product->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="filterVersion" label="Versi" placeholder="Semua Versi">
                    <flux:select.option value="">Semua Versi</flux:select.option>
                    @foreach (\App\Models\DocVersion::when($filterProduct !== '', fn ($q) => $q->where('product_id', $filterProduct))->with('product')->orderBy('product_id')->orderBy('sort_order')->get() as $version)
                        <flux:select.option value="{{ $version->id }}">{{ $version->product->name }} — {{ $version->label }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="filterStatus" label="Status" placeholder="Semua Status">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                    <flux:select.option value="archived">Archived</flux:select.option>
                </flux:select>
            </div>
        </div>
        @if ($search !== '' || $filterProduct !== '' || $filterVersion !== '' || $filterStatus !== '')
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#e3eaff] pt-4">
                <span class="text-xs font-medium text-[#65646e]">Filter aktif:</span>
                @if ($search !== '')
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#f3f6ff] border border-[#e3eaff] px-3 py-1 text-xs text-[#100f12]">“{{ \Illuminate\Support\Str::limit($search, 24) }}”</span>
                @endif
                @if ($filterProduct !== '')
                    @php $activeProduct = \App\Models\Product::find($filterProduct); @endphp
                    @if ($activeProduct)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#e3eaff] bg-white px-3 py-1 text-xs font-medium text-[#100f12]">{{ $activeProduct->name }}</span>
                    @endif
                @endif
                @if ($filterVersion !== '')
                    @php $activeVersion = \App\Models\DocVersion::with('product')->find($filterVersion); @endphp
                    @if ($activeVersion)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#e3eaff] bg-white px-3 py-1 text-xs font-medium text-[#100f12]">{{ $activeVersion->product->name }} — {{ $activeVersion->label }}</span>
                    @endif
                @endif
                @if ($filterStatus !== '')
                    <span class="inline-flex rounded-full bg-[#0a1589] px-3 py-1 text-xs font-medium text-white">{{ ucfirst($filterStatus) }}</span>
                @endif
                <flux:button size="sm" variant="ghost" wire:click="clearFilters">Reset</flux:button>
            </div>
        @endif
    </div>

    @php
        $pages = \App\Models\DocPage::query()
            ->with(['version.product'])
            ->when($search, fn ($q) => $q->where(fn ($i) => $i->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")))
            ->when($filterProduct !== '', fn ($q) => $q->where('product_id', $filterProduct))
            ->when($filterVersion !== '', fn ($q) => $q->where('version_id', $filterVersion))
            ->when($filterStatus !== '', fn ($q) => $q->where('status', $filterStatus))
            ->orderBy('product_id')
            ->orderBy('version_id')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#e3eaff] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#fafbff]">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-[#100f12] w-[34%]">Judul</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Produk / Versi</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Status</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Urutan</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Views</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3eaff]">
                    @forelse ($pages as $page)
                        <tr wire:key="doc-page-{{ $page->id }}" class="bg-white hover:bg-[#f3f6ff]/60 transition">
                            <td class="px-4 py-3">
                                <div class="min-w-0">
                                    <div class="font-medium text-[#100f12] line-clamp-1">{{ $page->title }}</div>
                                    <div class="text-xs text-[#65646e] truncate">{{ $page->slug }}</div>
                                    @if (filled($page->excerpt))
                                        <div class="text-xs text-[#65646e] truncate">{{ $page->excerpt }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-[#100f12]">{{ $page->version->product->name }}</div>
                                <div class="text-xs text-[#65646e]">{{ $page->version->label }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $page->status->value === 'published' ? 'bg-green-100 text-green-700' : ($page->status->value === 'draft' ? 'bg-[#f3f6ff] text-[#65646e]' : 'bg-amber-100 text-amber-700') }}">{{ $page->status->label() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <span class="text-[#100f12]">{{ $page->sort_order }}</span>
                                    <flux:button size="sm" variant="ghost" icon="chevron-up" aria-label="Naikkan" wire:click="moveUp({{ $page->id }})" />
                                    <flux:button size="sm" variant="ghost" icon="chevron-down" aria-label="Turunkan" wire:click="moveDown({{ $page->id }})" />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-[#100f12]">{{ $page->view_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    @if ($page->status === \App\Enums\DocStatus::Published)
                                        <a href="{{ route('docs.page', [$page->version->product, $page->version, $page]) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-2.5 py-1.5 text-xs font-medium text-[#100f12] hover:bg-[#f3f6ff]">Lihat</a>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-[#65646e]">Draft</span>
                                    @endif
                                    <a href="{{ route('admin.doc-form', ['id' => $page->id]) }}" wire:navigate class="inline-flex items-center rounded-lg bg-[#0a1589] px-2.5 py-1.5 text-xs font-medium text-white hover:bg-[#06105a]">Edit</a>
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $page->id }})" wire:confirm="Hapus halaman dokumentasi ini?">Hapus</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <p class="text-sm text-[#65646e]">Tidak ada halaman dokumentasi ditemukan.</p>
                                @if ($search !== '' || $filterProduct !== '' || $filterVersion !== '' || $filterStatus !== '')
                                    <flux:button size="sm" variant="ghost" wire:click="clearFilters" class="mt-2">Reset filter</flux:button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e3eaff] bg-white p-4">
            {{ $pages->links() }}
        </div>
    </div>
</section>
