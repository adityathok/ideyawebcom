<?php

use App\Actions\Media\DeleteMediaAction;
use App\Actions\Media\StoreMediaAction;
use App\Models\Media;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('Media')] class extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    /** @var array<int, mixed> */
    public array $uploads = [];

    public ?int $editingId = null;

    public string $alt_text = '';

    public string $caption = '';

    public ?int $deletingId = null;

    public string $deletingName = '';

    public int $deletingUsage = 0;

    public bool $deleteBlocked = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedUploads(): void
    {
        $files = array_values(array_filter(
            $this->uploads,
            fn (mixed $file): bool => $file instanceof UploadedFile,
        ));

        // Saat file masih dalam perjalanan, properti sempat berisi string nama file
        // sementara. Ditunggu sampai semuanya berupa UploadedFile.
        if ($files === [] || count($files) !== count($this->uploads)) {
            return;
        }

        $this->validate([
            'uploads.*' => StoreMediaAction::validationRules(true),
        ]);

        $action = app(StoreMediaAction::class);

        foreach ($files as $file) {
            $action->handle($file, auth()->id());
        }

        $this->uploads = [];
        $this->resetPage();

        Flux::toast(variant: 'success', text: count($files).' media berhasil diunggah.');
    }

    public function edit(int $id): void
    {
        $media = Media::findOrFail($id);

        $this->editingId = $media->id;
        $this->alt_text = $media->alt_text ?? '';
        $this->caption = $media->caption ?? '';

        Flux::modal('media-detail')->show();
    }

    public function saveDetails(): void
    {
        if ($this->editingId === null) {
            return;
        }

        $this->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
        ]);

        Media::findOrFail($this->editingId)->update([
            'alt_text' => $this->alt_text !== '' ? $this->alt_text : null,
            'caption' => $this->caption !== '' ? $this->caption : null,
        ]);

        $this->reset(['editingId', 'alt_text', 'caption']);

        Flux::toast(variant: 'success', text: 'Detail media diperbarui.');
        Flux::modal('media-detail')->close();
    }

    public function confirmDelete(int $id): void
    {
        $media = Media::withCount('attachments')->findOrFail($id);

        $this->deletingId = $media->id;
        $this->deletingName = $media->original_name;
        $this->deletingUsage = (int) $media->attachments_count;
        $this->deleteBlocked = $this->deletingUsage > 0;

        Flux::modal('confirm-media-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        $deleted = app(DeleteMediaAction::class)->handle(Media::findOrFail($this->deletingId));

        if (! $deleted) {
            $this->deleteBlocked = true;

            Flux::toast(variant: 'warning', text: 'Media masih dipakai, jadi belum bisa dihapus.');

            return;
        }

        $this->reset(['deletingId', 'deletingName', 'deletingUsage', 'deleteBlocked']);

        Flux::toast(variant: 'success', text: 'Media dihapus.');
        Flux::modal('confirm-media-deletion')->close();
    }

    public function clearFilters(): void
    {
        $this->reset('search');
        $this->resetPage();
    }
}; ?>
<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Perpustakaan Media</flux:heading>
            <flux:subheading>Semua gambar yang dipakai post dan halaman dokumentasi ({{ \App\Models\Media::count() }} media).</flux:subheading>
        </div>
    </div>

    <!-- Filter — card putih DESIGN.md: hairline #e3eaff, rounded-xl 12px -->
    <div class="mt-6 rounded-xl border border-[#e3eaff] bg-white p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" label="Cari media" placeholder="Cari nama file, alt, atau caption..." icon="magnifying-glass" />
            </div>
            <div class="lg:w-[320px]">
                <label for="media-uploads" class="text-sm font-medium text-[#100f12]">Unggah media</label>
                <input id="media-uploads" type="file" wire:model="uploads" multiple accept="image/*" class="mt-1 block w-full rounded-lg border border-[#e3eaff] bg-white px-3 py-2 text-sm text-[#100f12] file:mr-3 file:rounded-md file:border-0 file:bg-[#0a1589] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#06105a]" />
                <p class="mt-1.5 text-xs text-[#65646e]">
                    JPG, PNG, WebP, GIF, atau AVIF — maks {{ (int) config('media.max_size_kb') / 1024 }}MB per file.
                </p>
                <div wire:loading wire:target="uploads" class="mt-1.5 text-xs text-[#0a1589]">Mengunggah...</div>
                @error('uploads.*')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        @if ($search !== '')
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#e3eaff] pt-4">
                <span class="text-xs font-medium text-[#65646e]">Filter aktif:</span>
                <span class="inline-flex items-center gap-1 rounded-full bg-[#f3f6ff] border border-[#e3eaff] px-3 py-1 text-xs text-[#100f12]">“{{ \Illuminate\Support\Str::limit($search, 24) }}”</span>
                <flux:button size="sm" variant="ghost" wire:click="clearFilters">Reset</flux:button>
            </div>
        @endif
    </div>

    @php
        $media = \App\Models\Media::query()
            ->withCount('attachments')
            ->images()
            ->search($search !== '' ? $search : null)
            ->latest('id')
            ->paginate(18);
    @endphp

    @if ($media->total() === 0)
        <div class="mt-6 rounded-xl border border-[#e3eaff] bg-white px-4 py-12 text-center">
            <p class="text-sm text-[#65646e]">
                {{ $search !== '' ? 'Tidak ada media yang cocok dengan pencarian.' : 'Belum ada media. Unggah gambar pertama Anda di atas.' }}
            </p>
            @if ($search !== '')
                <flux:button size="sm" variant="ghost" wire:click="clearFilters" class="mt-2">Reset filter</flux:button>
            @endif
        </div>
    @else
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($media as $item)
                <div wire:key="media-{{ $item->id }}" class="flex flex-col overflow-hidden rounded-xl border border-[#e3eaff] bg-white">
                    <div class="aspect-[4/3] bg-[#fafbff]">
                        <img src="{{ $item->url() }}" alt="{{ $item->alt_text ?? '' }}" class="h-full w-full object-cover" loading="lazy" />
                    </div>
                    <div class="flex flex-1 flex-col gap-2 p-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium text-[#100f12]" title="{{ $item->original_name }}">{{ $item->original_name }}</div>
                            <div class="text-xs text-[#65646e]">
                                {{ $item->humanSize() }}
                                @if ($item->width && $item->height)
                                    · {{ $item->width }}×{{ $item->height }}
                                @endif
                            </div>
                        </div>
                        <div>
                            @if ($item->attachments_count > 0)
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Dipakai {{ $item->attachments_count }}×</span>
                            @else
                                <span class="inline-flex rounded-full bg-[#f3f6ff] px-2.5 py-1 text-xs font-medium text-[#65646e]">Belum dipakai</span>
                            @endif
                        </div>
                        <div class="mt-auto flex justify-end gap-1.5">
                            <flux:button size="sm" variant="ghost" wire:click="edit({{ $item->id }})">Detail</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $item->id }})">Hapus</flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $media->links() }}
        </div>
    @endif

    <flux:modal name="media-detail" class="max-w-lg">
        <form wire:submit="saveDetails" class="space-y-6">
            <div>
                <flux:heading size="lg">Detail media</flux:heading>
                <flux:subheading>Alt text dipakai sebagai teks alternatif gambar; caption tampil di bawah gambar bila tema mendukungnya.</flux:subheading>
            </div>

            <flux:input wire:model="alt_text" label="Alt text" placeholder="Deskripsi singkat gambar" />
            <flux:input wire:model="caption" label="Caption" placeholder="Keterangan gambar (opsional)" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="confirm-media-deletion" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus media ini?</flux:heading>
                <flux:subheading>
                    @if ($deleteBlocked)
                        “{{ $deletingName }}” masih dipakai di {{ $deletingUsage }} tempat, jadi belum bisa dihapus.
                    @else
                        “{{ $deletingName }}” belum dipakai di mana pun. Media akan dihapus dari perpustakaan.
                    @endif
                </flux:subheading>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="delete" :disabled="$deleteBlocked">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
