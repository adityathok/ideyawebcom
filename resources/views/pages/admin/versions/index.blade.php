<?php
use App\Models\DocVersion;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Versi Dokumentasi')] class extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public string $filterProduct = '';

    public ?int $product_id = null;

    public string $label = '';

    public string $slug = '';

    public bool $is_current = false;

    public int $sort_order = 0;

    public ?string $released_at = null;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public string $deletingLabel = '';

    public int $deletingPages = 0;

    public bool $deletingBlocked = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProduct(): void
    {
        $this->resetPage();
    }

    public function updatedLabel(string $value): void
    {
        if ($this->editingId === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('version-form')->show();
    }

    public function edit(int $id): void
    {
        $version = DocVersion::findOrFail($id);

        $this->editingId = $version->id;
        $this->product_id = $version->product_id;
        $this->label = $version->label;
        $this->slug = $version->slug;
        $this->is_current = $version->is_current;
        $this->sort_order = $version->sort_order;
        $this->released_at = $version->released_at?->format('Y-m-d');

        Flux::modal('version-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'label' => 'required|string|max:100',
            'slug' => ['required', 'string', 'max:255', Rule::unique('doc_versions', 'slug')->where('product_id', $this->product_id)->ignore($this->editingId)],
            'is_current' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'released_at' => 'nullable|date',
        ]);

        $message = $this->editingId ? 'Versi diperbarui.' : 'Versi dibuat.';

        $version = DocVersion::updateOrCreate(['id' => $this->editingId], [
            'product_id' => $this->product_id,
            'label' => $this->label,
            'slug' => $this->slug ?: Str::slug($this->label),
            'is_current' => $this->is_current,
            'sort_order' => $this->sort_order,
            'released_at' => filled($this->released_at) ? Carbon::parse($this->released_at) : null,
        ]);

        // Hanya satu versi per produk yang boleh menjadi versi terkini.
        if ($this->is_current) {
            DocVersion::where('product_id', $version->product_id)
                ->whereKeyNot($version->id)
                ->update(['is_current' => false]);
        }

        $this->resetForm();
        Flux::toast(variant: 'success', text: $message);
        Flux::modal('version-form')->close();
    }

    public function confirmDelete(int $id): void
    {
        $version = DocVersion::withCount('pages')->findOrFail($id);

        $this->deletingId = $version->id;
        $this->deletingLabel = $version->label;
        $this->deletingPages = $version->pages_count;
        $this->deletingBlocked = $version->product->versions()->count() <= 1;

        Flux::modal('confirm-version-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingBlocked) {
            $this->reset(['deletingId', 'deletingLabel', 'deletingPages', 'deletingBlocked']);
            Flux::modal('confirm-version-deletion')->close();

            return;
        }

        if ($this->deletingId !== null) {
            DocVersion::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Versi dihapus.');
        }

        $this->reset(['deletingId', 'deletingLabel', 'deletingPages', 'deletingBlocked']);
        Flux::modal('confirm-version-deletion')->close();
    }

    private function resetForm(): void
    {
        $this->reset(['label', 'slug', 'is_current', 'sort_order', 'released_at', 'editingId']);
        $this->product_id = is_numeric($this->filterProduct) ? (int) $this->filterProduct : null;
    }
}; ?>
<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Versi Dokumentasi</flux:heading>
            <flux:subheading>Kelola versi dokumentasi tiap produk ({{ \App\Models\DocVersion::count() }} total).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="create">+ Versi</flux:button>
    </div>

    <div class="mt-6 rounded-xl border border-[#e3eaff] bg-white p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" label="Cari versi" placeholder="Cari versi..." icon="magnifying-glass" />
            </div>
            <div class="lg:w-[280px]">
                <flux:select wire:model.live="filterProduct" label="Produk" placeholder="Semua Produk">
                    <flux:select.option value="">Semua Produk</flux:select.option>
                    @foreach (\App\Models\Product::orderBy('name')->get() as $item)
                        <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    @php
        $versions = \App\Models\DocVersion::query()
            ->with('product')
            ->withCount('pages')
            ->when($search, fn ($q) => $q->where('label', 'like', "%{$search}%"))
            ->when($filterProduct !== '', fn ($q) => $q->where('product_id', $filterProduct))
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(15);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#e3eaff] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#fafbff]">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Produk</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Label</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Status</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Halaman</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Rilis</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Urutan</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3eaff]">
                    @forelse ($versions as $version)
                        <tr wire:key="version-{{ $version->id }}" class="bg-white hover:bg-[#f3f6ff]/60 transition">
                            <td class="px-4 py-3 text-[#100f12]">{{ $version->product?->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-[#100f12]">{{ $version->label }}</div>
                                <div class="text-xs text-[#65646e]">{{ $version->slug }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $version->is_current ? 'bg-[#0a1589] text-white' : 'bg-[#f3f6ff] text-[#65646e]' }}">{{ $version->is_current ? 'Current' : 'Arsip' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-[#100f12]">{{ $version->pages_count }}</td>
                            <td class="px-4 py-3 text-[#65646e]">{{ $version->released_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-[#100f12]">{{ $version->sort_order }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <flux:button size="sm" variant="ghost" wire:click="edit({{ $version->id }})">Edit</flux:button>
                                    <a href="{{ route('admin.docs', ['filterVersion' => $version->id]) }}" wire:navigate class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-2.5 py-1.5 text-xs font-medium text-[#100f12] hover:bg-[#f3f6ff]">Halaman</a>
                                    <a href="{{ route('docs.version', [$version->product, $version]) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-2.5 py-1.5 text-xs font-medium text-[#100f12] hover:bg-[#f3f6ff]">Lihat</a>
                                    <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $version->id }})">Hapus</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-[#65646e]">Tidak ada versi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e3eaff] bg-white p-4">
            {{ $versions->links() }}
        </div>
    </div>

    <flux:modal name="version-form" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Versi' : 'Tambah Versi' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Perbarui detail versi dokumentasi.' : 'Buat versi baru untuk sebuah produk.' }}</flux:subheading>
            </div>

            <flux:select wire:model="product_id" label="Produk" required>
                <flux:select.option value="">Pilih produk</flux:select.option>
                @foreach (\App\Models\Product::orderBy('name')->get() as $item)
                    <flux:select.option value="{{ $item->id }}">{{ $item->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="label" label="Label" placeholder="v2.0" description="Tampil di URL dan pemilih versi" required />
            <flux:input wire:model="slug" label="Slug" description="Otomatis dari label" />
            <flux:switch wire:model="is_current" label="Jadikan versi terkini" description="Hanya satu versi per produk yang bisa menjadi versi terkini." />
            <flux:input wire:model="released_at" label="Tanggal Rilis" type="date" />
            <flux:input wire:model="sort_order" label="Urutan" type="number" min="0" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="confirm-version-deletion" class="max-w-md">
        <div class="space-y-6">
            @if ($deletingBlocked)
                <div>
                    <flux:heading size="lg">Versi ini tidak bisa dihapus</flux:heading>
                    <flux:subheading>
                        "{{ $deletingLabel }}" adalah satu-satunya versi produk ini. Setiap produk wajib memiliki minimal satu versi, hapus produknya bila memang tidak dibutuhkan.
                    </flux:subheading>
                </div>

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            @else
                <div>
                    <flux:heading size="lg">Hapus versi ini?</flux:heading>
                    <flux:subheading>
                        Versi "{{ $deletingLabel }}" beserta {{ $deletingPages }} halaman akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                    </flux:subheading>
                </div>

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" wire:click="delete">Hapus</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
</section>
