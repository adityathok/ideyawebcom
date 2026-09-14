<?php
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Produk Dokumentasi')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $name = '';

    public string $slug = '';

    public string $tagline = '';

    public string $description = '';

    public string $logo = '';

    public string $color = '#0a1589';

    public string $website_url = '';

    public int $sort_order = 0;

    public bool $is_published = true;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public string $deletingName = '';

    public int $deletingVersions = 0;

    public int $deletingPages = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatingName(string $value): void
    {
        if ($this->editingId === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('product-form')->show();
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->tagline = $product->tagline ?? '';
        $this->description = $product->description ?? '';
        $this->logo = $product->logo ?? '';
        $this->color = $product->color ?? '#0a1589';
        $this->website_url = $product->website_url ?? '';
        $this->sort_order = $product->sort_order;
        $this->is_published = $product->is_published;

        Flux::modal('product-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,'.($this->editingId ?? 'NULL'),
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'logo' => 'nullable|string|max:2048',
            'color' => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_published' => 'boolean',
        ]);

        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }

        $message = $this->editingId ? 'Produk diperbarui.' : 'Produk dibuat.';

        Product::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug($this->name),
            'tagline' => $this->tagline ?: null,
            'description' => $this->description ?: null,
            'logo' => $this->logo ?: null,
            'color' => $this->color ?: null,
            'website_url' => $this->website_url ?: null,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
        ]);

        $this->resetForm();
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('product-form')->close();
    }

    public function confirmDelete(int $id): void
    {
        $product = Product::withCount(['versions', 'pages'])->findOrFail($id);

        $this->deletingId = $product->id;
        $this->deletingName = $product->name;
        $this->deletingVersions = $product->versions_count;
        $this->deletingPages = $product->pages_count;

        Flux::modal('confirm-product-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId !== null) {
            Product::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Produk dihapus.');
        }

        $this->reset(['deletingId', 'deletingName', 'deletingVersions', 'deletingPages']);
        Flux::modal('confirm-product-deletion')->close();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'slug', 'tagline', 'description', 'logo', 'color', 'website_url', 'sort_order', 'is_published', 'editingId']);
    }
}; ?>
<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Produk Dokumentasi</flux:heading>
            <flux:subheading>Kelola produk beserta dokumentasinya ({{ \App\Models\Product::count() }} total).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="create">+ Produk</flux:button>
    </div>

    <div class="mt-6">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari produk..." icon="magnifying-glass" />
    </div>

    @php
        $products = \App\Models\Product::query()
            ->withCount(['versions', 'pages'])
            ->when($search, fn ($q) => $q->where(fn ($i) => $i->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")))
            ->ordered()
            ->paginate(10);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#e3eaff] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#fafbff]">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Nama</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Slug</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Versi</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Halaman</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Status</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3eaff]">
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="bg-white hover:bg-[#f3f6ff]/60 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @php $logo = $product->logoUrl(); @endphp
                                    @if ($logo)
                                        <div class="hidden sm:block size-10 shrink-0 overflow-hidden rounded-lg border border-[#e3eaff] bg-[#fafbff]">
                                            <img src="{{ $logo }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 font-medium text-[#100f12]">
                                        <span class="size-3 shrink-0 rounded-full" style="background: {{ $product->color ?? '#0a1589' }}"></span>
                                        {{ $product->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-[#65646e]">{{ $product->slug }}</td>
                            <td class="px-4 py-3 text-center text-[#100f12]">{{ $product->versions_count }}</td>
                            <td class="px-4 py-3 text-center text-[#100f12]">{{ $product->pages_count }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $product->is_published ? 'bg-green-100 text-green-700' : 'bg-[#f3f6ff] text-[#65646e]' }}">{{ $product->is_published ? 'Published' : 'Draft' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <flux:button size="sm" variant="ghost" wire:click="edit({{ $product->id }})">Edit</flux:button>
                                    <a href="{{ route('admin.versions', ['filterProduct' => $product->id]) }}" wire:navigate class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-2.5 py-1.5 text-xs font-medium text-[#100f12] hover:bg-[#f3f6ff]">Versi</a>
                                    @if ($product->is_published)
                                        <a href="{{ route('docs.product', $product->slug) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-2.5 py-1.5 text-xs font-medium text-[#100f12] hover:bg-[#f3f6ff]">Lihat</a>
                                    @else
                                        <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-[#e3eaff] bg-[#fafbff] px-2.5 py-1.5 text-xs font-medium text-[#65646e]">Lihat</span>
                                    @endif
                                    <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $product->id }})">Hapus</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-[#65646e]">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e3eaff] bg-white p-4">
            {{ $products->links() }}
        </div>
    </div>

    <flux:modal name="product-form" class="max-w-xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Produk' : 'Tambah Produk' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Perbarui detail produk dokumentasi.' : 'Buat produk baru untuk dokumentasi.' }}</flux:subheading>
            </div>

            @if ($editingId === null)
                <flux:callout color="blue" icon="information-circle" text="Versi v1 akan dibuat otomatis saat produk disimpan." />
            @endif

            <flux:input wire:model="name" label="Nama" required />
            <flux:input wire:model="slug" label="Slug" description="Otomatis dari nama, bisa diedit" />
            <flux:input wire:model="tagline" label="Tagline" />
            <flux:textarea wire:model="description" label="Deskripsi" rows="3" />
            <flux:input wire:model="logo" label="Logo" description="URL atau path file di disk publik (opsional)" />
            <flux:input wire:model="color" label="Warna" type="color" />
            <flux:input wire:model="website_url" label="Website (URL)" />
            <flux:input wire:model="sort_order" label="Urutan" type="number" min="0" />
            <flux:switch wire:model="is_published" label="Publikasikan" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="confirm-product-deletion" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus produk ini?</flux:heading>
                <flux:subheading>
                    Produk "{{ $deletingName }}" beserta {{ $deletingVersions }} versi dan {{ $deletingPages }} halaman akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                </flux:subheading>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="delete">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
