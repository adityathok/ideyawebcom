<?php
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Tag;
use Flux\Flux;
use Illuminate\Support\Str;

new #[Title('Tags')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $slug = '';
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
        $this->reset(['name','slug','editingId']);
        Flux::modal('tag-form')->show();
    }

    public function edit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $tag->id;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        Flux::modal('tag-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tags,slug,'.($this->editingId ?? 'NULL'),
        ]);

        Tag::updateOrCreate(['id' => $this->editingId], [
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug($this->name),
        ]);

        $this->reset(['name','slug','editingId']);
        Flux::toast(variant: 'success', text: 'Tag disimpan.');
        Flux::modal('tag-form')->close();
    }

    public function confirmDelete(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->deletingId = $tag->id;
        $this->deletingName = $tag->name;
        Flux::modal('confirm-tag-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId !== null) {
            Tag::findOrFail($this->deletingId)->delete();
            Flux::toast(variant: 'success', text: 'Tag dihapus.');
        }

        $this->reset(['deletingId', 'deletingName']);
        Flux::modal('confirm-tag-deletion')->close();
    }
}; ?>
<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Tags</flux:heading>
            <flux:subheading>Kelola tags blog ({{ \App\Models\Tag::count() }} total).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="create">+ Tag</flux:button>
    </div>

    <div class="mt-6">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari tag..." icon="magnifying-glass" />
    </div>

    @php
        $tags = \App\Models\Tag::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount('posts')
            ->orderBy('name')
            ->paginate(10);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#d3cec6] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#f5f1ec]">
                <tr>
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Posts</th>
                    <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#ebe7e1]">
                @forelse ($tags as $tag)
                    <tr wire:key="tag-{{ $tag->id }}" class="bg-white hover:bg-[#f5f1ec]/60 transition">
                        <td class="px-4 py-3 font-medium">#{{ $tag->name }}</td>
                        <td class="px-4 py-3 text-[#626260]">{{ $tag->slug }}</td>
                        <td class="px-4 py-3">{{ $tag->posts_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1.5">
                                <flux:button size="sm" variant="ghost" wire:click="edit({{ $tag->id }})">Edit</flux:button>
                                <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $tag->id }})">Hapus</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-sm text-[#626260]">Tidak ada tag ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-[#ebe7e1] bg-white p-4">
            {{ $tags->links() }}
        </div>
    </div>

    <flux:modal name="tag-form" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Tag' : 'Tambah Tag' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Perbarui nama atau slug tag.' : 'Buat tag baru untuk mengelompokkan artikel.' }}</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Nama" required />
            <flux:input wire:model="slug" label="Slug" description="Otomatis dari nama, bisa diedit" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="confirm-tag-deletion" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus tag ini?</flux:heading>
                <flux:subheading>
                    Tag "{{ $deletingName }}" akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
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
