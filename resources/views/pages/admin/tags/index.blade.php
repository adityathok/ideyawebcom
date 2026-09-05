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
    public bool $showForm = false;

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
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $tag->id;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        $this->showForm = true;
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

        $this->reset(['name','slug','editingId','showForm']);
        Flux::toast(variant: 'success', text: 'Tag disimpan.');
    }

    public function delete(int $id): void
    {
        Tag::findOrFail($id)->delete();
        Flux::toast(variant: 'success', text: 'Tag dihapus.');
    }

    public function cancel(): void { $this->reset(['name','slug','editingId','showForm']); }
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

    @if($showForm)
        <div class="mt-6 rounded-2xl border border-[#d3cec6] bg-white p-6">
            <flux:heading>{{ $editingId ? 'Edit Tag' : 'Tambah Tag' }}</flux:heading>
            <form wire:submit="save" class="mt-4 space-y-4">
                <flux:input wire:model="name" label="Nama" required />
                <flux:input wire:model="slug" label="Slug" />
                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
                    <flux:button type="button" variant="ghost" wire:click="cancel">Batal</flux:button>
                </div>
            </form>
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-xl border border-[#d3cec6]">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#f5f1ec]">
                <tr>
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Posts</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#ebe7e1]">
                @foreach(\App\Models\Tag::when($search, fn($q) => $q->where('name','like',"%{$search}%"))->withCount('posts')->orderBy('name')->paginate(10) as $tag)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">#{{ $tag->name }}</td>
                        <td class="px-4 py-3 text-[#626260]">{{ $tag->slug }}</td>
                        <td class="px-4 py-3">{{ $tag->posts_count }}</td>
                        <td class="px-4 py-3 flex gap-1">
                            <flux:button size="sm" variant="ghost" wire:click="edit({{ $tag->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="delete({{ $tag->id }})" wire:confirm="Hapus tag ini?">Hapus</flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-white">
            {{ \App\Models\Tag::when($search, fn($q) => $q->where('name','like',"%{$search}%"))->orderBy('name')->paginate(10)->links() }}
        </div>
    </div>
</section>
