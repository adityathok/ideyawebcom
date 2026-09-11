<?php
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Str;

new #[Title('Kategori')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $color = '#52525b';
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public string $deletingName = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function updatingName(string $value): void
    {
        if ($this->editingId === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->reset(['name','slug','description','editingId']);
        $this->color = '#52525b';
        Flux::modal('category-form')->show();
    }

    public function edit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $cat->id;
        $this->name = $cat->name;
        $this->slug = $cat->slug;
        $this->description = $cat->description ?? '';
        $this->color = $cat->color ?? '#52525b';
        Flux::modal('category-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,'.($this->editingId ?? 'NULL'),
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20',
        ]);

        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }

        Category::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug($this->name),
            'description' => $this->description ?: null,
            'color' => $this->color ?: null,
        ]);

        $this->reset(['name','slug','description','editingId']);
        $this->color = '#52525b';
        Flux::toast(variant: 'success', text: 'Kategori disimpan.');
        Flux::modal('category-form')->close();
    }

    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->deletingId = $category->id;
        $this->deletingName = $category->name;
        Flux::modal('confirm-category-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId !== null) {
            Category::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Kategori dihapus.');
        }

        $this->reset(['deletingId', 'deletingName']);
        Flux::modal('confirm-category-deletion')->close();
    }
}; ?>
<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Kategori</flux:heading>
            <flux:subheading>Kelola kategori blog ({{ \App\Models\Category::count() }} total).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="create">+ Kategori</flux:button>
    </div>

    <div class="mt-6">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kategori..." icon="magnifying-glass" />
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-[#d3cec6]">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#f5f1ec]">
                <tr>
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Warna</th>
                    <th class="px-4 py-3 font-semibold">Posts</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#ebe7e1]">
                @foreach(\App\Models\Category::when($search, fn($q) => $q->where('name','like',"%{$search}%"))->withCount('posts')->orderBy('name')->paginate(10) as $cat)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium flex items-center gap-2"><span class="size-3 rounded-full" style="background: {{ $cat->color ?? '#52525b' }}"></span> {{ $cat->name }}</td>
                        <td class="px-4 py-3 text-[#626260]">{{ $cat->slug }}</td>
                        <td class="px-4 py-3"><span class="rounded px-2 py-1 text-xs text-white" style="background: {{ $cat->color ?? '#52525b' }}">{{ $cat->color ?? '-' }}</span></td>
                        <td class="px-4 py-3">{{ $cat->posts_count }}</td>
                        <td class="px-4 py-3 flex gap-1">
                            <flux:button size="sm" variant="ghost" wire:click="edit({{ $cat->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $cat->id }})">Hapus</flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-white">
            {{ \App\Models\Category::when($search, fn($q) => $q->where('name','like',"%{$search}%"))->orderBy('name')->paginate(10)->links() }}
        </div>
    </div>

    <flux:modal name="category-form" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Perbarui detail kategori.' : 'Buat kategori baru untuk artikel.' }}</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Nama" required />
            <flux:input wire:model="slug" label="Slug" description="Otomatis dari nama, bisa diedit" />
            <flux:textarea wire:model="description" label="Deskripsi" rows="2" />
            <flux:input wire:model="color" label="Warna" type="color" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="confirm-category-deletion" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus kategori ini?</flux:heading>
                <flux:subheading>
                    Kategori "{{ $deletingName }}" akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
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
