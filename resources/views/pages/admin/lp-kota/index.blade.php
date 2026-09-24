<?php

use App\Actions\Media\StoreMediaAction;
use App\Models\LpKota;
use App\Models\Media;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Title('LP Kota')] class extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $nama_kota = '';

    public string $nama_kecamatan = '';

    public string $deskripsi = '';

    public ?int $gambarUtamaMediaId = null;

    public ?int $gambarIconMediaId = null;

    public ?string $gambarUtamaPreview = null;

    public ?string $gambarIconPreview = null;

    /** @var mixed */
    public $gambarUtamaUpload = null;

    /** @var mixed */
    public $gambarIconUpload = null;

    public string $pickerTarget = 'utama';

    public string $mediaSearch = '';

    public ?int $deletingId = null;

    public string $deletingLabel = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        Flux::modal('lp-kota-form')->show();
    }

    public function edit(int $id): void
    {
        $lp = LpKota::findOrFail($id);

        $this->editingId = $lp->id;
        $this->nama_kota = $lp->nama_kota;
        $this->nama_kecamatan = $lp->nama_kecamatan;
        $this->deskripsi = $lp->deskripsi ?? '';
        $this->gambarUtamaMediaId = $lp->gambarUtamaMedia()->first()?->id;
        $this->gambarIconMediaId = $lp->gambarIconMedia()->first()?->id;
        $this->gambarUtamaPreview = $lp->gambarUtamaUrl();
        $this->gambarIconPreview = $lp->gambarIconUrl();

        Flux::modal('lp-kota-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'nama_kota' => 'required|string|max:255',
            'nama_kecamatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'gambarUtamaUpload' => StoreMediaAction::validationRules(),
            'gambarIconUpload' => StoreMediaAction::validationRules(),
        ], attributes: [
            'nama_kota' => 'nama kota',
            'nama_kecamatan' => 'nama kecamatan',
        ]);

        // Pasangan kota + kecamatan yang sama akan menabrak unique index, jadi
        // dicegah di sini dengan pesan yang bisa dibaca, bukan error database.
        $this->validate([
            'nama_kecamatan' => Rule::unique('lp_kota', 'nama_kecamatan')
                ->where(fn ($q) => $q->where('nama_kota', $this->nama_kota))
                ->ignore($this->editingId),
        ], messages: [
            'nama_kecamatan.unique' => 'Kecamatan ini sudah terdaftar untuk kota tersebut.',
        ]);

        $lp = LpKota::updateOrCreate(['id' => $this->editingId], [
            'nama_kota' => $this->nama_kota,
            'nama_kecamatan' => $this->nama_kecamatan,
            'deskripsi' => $this->deskripsi !== '' ? $this->deskripsi : null,
        ]);

        $this->applyUploads($lp);

        $message = $this->editingId ? 'Data LP kota diperbarui.' : 'Data LP kota dibuat.';

        $this->editingId = $lp->id;
        $this->gambarUtamaUpload = null;
        $this->gambarIconUpload = null;
        $this->gambarUtamaPreview = $lp->gambarUtamaUrl();
        $this->gambarIconPreview = $lp->gambarIconUrl();

        Flux::toast(variant: 'success', text: $message);
        Flux::modal('lp-kota-form')->close();
    }

    public function openMediaPicker(string $target = 'utama'): void
    {
        $this->pickerTarget = $target === 'icon' ? 'icon' : 'utama';
        $this->mediaSearch = '';

        Flux::modal('lp-kota-media-picker')->show();
    }

    public function selectMedia(int $id): void
    {
        $media = Media::findOrFail($id);

        if ($this->pickerTarget === 'icon') {
            $this->gambarIconMediaId = $media->id;
            $this->gambarIconUpload = null;
            $this->gambarIconPreview = $media->url();
        } else {
            $this->gambarUtamaMediaId = $media->id;
            $this->gambarUtamaUpload = null;
            $this->gambarUtamaPreview = $media->url();
        }

        Flux::modal('lp-kota-media-picker')->close();
    }

    public function removeGambar(string $target): void
    {
        if ($target === 'icon') {
            $this->gambarIconMediaId = null;
            $this->gambarIconUpload = null;
            $this->gambarIconPreview = null;
        } else {
            $this->gambarUtamaMediaId = null;
            $this->gambarUtamaUpload = null;
            $this->gambarUtamaPreview = null;
        }

        if ($this->editingId === null) {
            return;
        }

        $lp = LpKota::find($this->editingId);

        if ($lp === null) {
            return;
        }

        // Kolom warisan ikut dikosongkan supaya `gambarUtamaUrl()` tidak
        // mengembalikan gambar yang baru saja dihapus.
        if ($target === 'icon') {
            $lp->setGambarIcon(null);
            $lp->update(['gambar_icon' => null]);
        } else {
            $lp->setGambarUtama(null);
            $lp->update(['gambar_utama' => null]);
        }
    }

    public function confirmDelete(int $id): void
    {
        $lp = LpKota::findOrFail($id);

        $this->deletingId = $lp->id;
        $this->deletingLabel = $lp->labelWilayah();

        Flux::modal('confirm-lp-kota-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        // File media tidak ikut dihapus: perpustakaan media yang mengurusnya,
        // dan gambar bisa dipakai record lain.
        LpKota::findOrFail($this->deletingId)->delete();

        $this->reset(['deletingId', 'deletingLabel']);

        Flux::toast(variant: 'success', text: 'Data LP kota dihapus.');
        Flux::modal('confirm-lp-kota-deletion')->close();
    }

    /**
     * Unggahan langsung dari form ikut masuk perpustakaan media, jadi tidak ada
     * gambar yang "tersembunyi" di luar perpustakaan. Kolom `gambar_utama` /
     * `gambar_icon` sengaja tidak ditulis (sama seperti `image` di Post): kolom
     * itu hanya dibaca sebagai cadangan data lama.
     */
    private function applyUploads(LpKota $lp): void
    {
        $action = app(StoreMediaAction::class);

        if ($this->gambarUtamaUpload instanceof UploadedFile) {
            $lp->setGambarUtama($action->handle($this->gambarUtamaUpload, auth()->id()));
        } elseif ($this->gambarUtamaMediaId !== null) {
            $lp->setGambarUtama(Media::find($this->gambarUtamaMediaId));
        }

        if ($this->gambarIconUpload instanceof UploadedFile) {
            $lp->setGambarIcon($action->handle($this->gambarIconUpload, auth()->id()));
        } elseif ($this->gambarIconMediaId !== null) {
            $lp->setGambarIcon(Media::find($this->gambarIconMediaId));
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'nama_kota', 'nama_kecamatan', 'deskripsi',
            'gambarUtamaMediaId', 'gambarIconMediaId',
            'gambarUtamaPreview', 'gambarIconPreview',
            'gambarUtamaUpload', 'gambarIconUpload',
        ]);
    }
}; ?>
<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">LP Kota</flux:heading>
            <flux:subheading>Landing page per wilayah — {{ \App\Models\LpKota::count() }} data ({{ \App\Models\LpKota::distinct()->count('nama_kota') }} kota).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="create">+ Data LP</flux:button>
    </div>

    <!-- Filter — card putih DESIGN.md: hairline #e3eaff, rounded-xl 12px -->
    <div class="mt-6 rounded-xl border border-[#e3eaff] bg-white p-4">
        <flux:input wire:model.live.debounce.300ms="search" label="Cari wilayah" placeholder="Cari kota, kecamatan, atau deskripsi..." icon="magnifying-glass" />
        @if ($search !== '')
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#e3eaff] pt-4">
                <span class="text-xs font-medium text-[#65646e]">Filter aktif:</span>
                <span class="inline-flex items-center gap-1 rounded-full border border-[#e3eaff] bg-[#f3f6ff] px-3 py-1 text-xs text-[#100f12]">“{{ \Illuminate\Support\Str::limit($search, 24) }}”</span>
                <flux:button size="sm" variant="ghost" wire:click="$set('search', '')">Reset</flux:button>
            </div>
        @endif
    </div>

    @php
        $daftar = \App\Models\LpKota::query()
            ->with(['media'])
            ->search($search !== '' ? $search : null)
            ->orderBy('nama_kota')
            ->orderBy('nama_kecamatan')
            ->paginate(10);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#e3eaff] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#fafbff]">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-[#100f12] w-[38%]">Wilayah</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12]">Deskripsi</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-center">Gambar</th>
                        <th class="px-4 py-3 font-semibold text-[#100f12] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3eaff]">
                    @forelse ($daftar as $item)
                        <tr wire:key="lp-kota-{{ $item->id }}" class="bg-white hover:bg-[#f3f6ff]/60 transition">
                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    <div class="hidden sm:block size-12 shrink-0 overflow-hidden rounded-lg border border-[#e3eaff] bg-[#fafbff]">
                                        @php $thumb = $item->gambarUtamaUrl(); @endphp
                                        @if ($thumb)
                                            <img src="{{ $thumb }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs text-[#787685]">—</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 font-medium text-[#100f12]">
                                            @php $icon = $item->gambarIconUrl(); @endphp
                                            @if ($icon)
                                                <img src="{{ $icon }}" alt="" class="size-5 shrink-0 rounded object-cover" loading="lazy" />
                                            @endif
                                            {{ $item->nama_kota }}
                                        </div>
                                        <div class="text-xs text-[#65646e]">{{ $item->nama_kecamatan }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-[#65646e]">
                                {{ $item->deskripsi ? \Illuminate\Support\Str::limit($item->deskripsi, 90) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-[#65646e]">
                                {{ $item->gambarUtamaUrl() ? 'Utama' : '—' }}{{ $item->gambarIconUrl() ? ' + Ikon' : '' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <flux:button size="sm" variant="ghost" wire:click="edit({{ $item->id }})">Edit</flux:button>
                                    <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $item->id }})">Hapus</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <p class="text-sm text-[#65646e]">
                                    {{ $search !== '' ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data LP kota.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e3eaff] bg-white p-4">
            {{ $daftar->links() }}
        </div>
    </div>

    <flux:modal name="lp-kota-form" class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit Data LP Kota' : 'Tambah Data LP Kota' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Perbarui data wilayah beserta gambarnya.' : 'Satu baris mewakili satu kecamatan pada satu kota.' }}</flux:subheading>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="nama_kota" label="Nama Kota" required placeholder="Contoh: Sukoharjo" />
                <flux:input wire:model="nama_kecamatan" label="Nama Kecamatan" required placeholder="Contoh: Weru" />
            </div>

            <flux:textarea wire:model="deskripsi" label="Deskripsi" rows="4" placeholder="Deskripsi wilayah untuk landing page..." />

            <!-- Gambar utama -->
            <div class="space-y-3 rounded-xl border border-[#e3eaff] bg-[#fafbff] p-4">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-[#100f12]">Gambar Utama</label>
                    <span class="text-xs text-[#65646e]">Maks {{ (int) config('media.max_size_kb') / 1024 }}MB</span>
                </div>

                @php
                    $utamaPreview = $gambarUtamaUpload instanceof \Illuminate\Http\UploadedFile
                        ? $gambarUtamaUpload->temporaryUrl()
                        : $gambarUtamaPreview;
                @endphp

                @if ($utamaPreview)
                    <div class="overflow-hidden rounded-lg border border-[#e3eaff] bg-white p-2">
                        <img src="{{ $utamaPreview }}" alt="Pratinjau gambar utama" class="h-44 w-full rounded-md object-cover" />
                    </div>
                @else
                    <p class="text-xs text-[#65646e]">Belum ada gambar utama. Pilih dari perpustakaan atau unggah file baru.</p>
                @endif

                <div class="flex flex-wrap gap-2">
                    <flux:button size="sm" variant="ghost" type="button" wire:click="openMediaPicker('utama')">Pilih dari galeri</flux:button>
                    @if ($utamaPreview)
                        <flux:button size="sm" variant="danger" type="button" wire:click="removeGambar('utama')">Hapus Gambar</flux:button>
                    @endif
                </div>

                <input type="file" wire:model="gambarUtamaUpload" accept="image/*" class="block w-full rounded-lg border border-[#e3eaff] bg-white px-3 py-2 text-sm text-[#100f12] file:mr-3 file:rounded-md file:border-0 file:bg-[#0a1589] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#06105a]" />
                <div wire:loading wire:target="gambarUtamaUpload" class="text-xs text-[#65646e]">Mengunggah...</div>
                <flux:error name="gambarUtamaUpload" />
            </div>

            <!-- Gambar ikon -->
            <div class="space-y-3 rounded-xl border border-[#e3eaff] bg-[#fafbff] p-4">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-[#100f12]">Gambar Ikon</label>
                    <span class="text-xs text-[#65646e]">Ukuran kecil, mis. 128×128</span>
                </div>

                @php
                    $iconPreview = $gambarIconUpload instanceof \Illuminate\Http\UploadedFile
                        ? $gambarIconUpload->temporaryUrl()
                        : $gambarIconPreview;
                @endphp

                @if ($iconPreview)
                    <div class="flex items-center gap-3">
                        <div class="size-16 overflow-hidden rounded-lg border border-[#e3eaff] bg-white p-1">
                            <img src="{{ $iconPreview }}" alt="Pratinjau ikon" class="h-full w-full rounded object-contain" />
                        </div>
                        <span class="text-xs text-[#65646e]">Ikon wilayah.</span>
                    </div>
                @else
                    <p class="text-xs text-[#65646e]">Belum ada ikon. Pilih dari perpustakaan atau unggah file baru.</p>
                @endif

                <div class="flex flex-wrap gap-2">
                    <flux:button size="sm" variant="ghost" type="button" wire:click="openMediaPicker('icon')">Pilih dari galeri</flux:button>
                    @if ($iconPreview)
                        <flux:button size="sm" variant="danger" type="button" wire:click="removeGambar('icon')">Hapus Ikon</flux:button>
                    @endif
                </div>

                <input type="file" wire:model="gambarIconUpload" accept="image/*" class="block w-full rounded-lg border border-[#e3eaff] bg-white px-3 py-2 text-sm text-[#100f12] file:mr-3 file:rounded-md file:border-0 file:bg-[#0a1589] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#06105a]" />
                <div wire:loading wire:target="gambarIconUpload" class="text-xs text-[#65646e]">Mengunggah...</div>
                <flux:error name="gambarIconUpload" />
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="lp-kota-media-picker" class="max-w-3xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $pickerTarget === 'icon' ? 'Pilih gambar ikon' : 'Pilih gambar utama' }}</flux:heading>
                <flux:subheading>Gambar diambil dari perpustakaan media. Belum ada yang cocok? Unggah dulu di halaman Media.</flux:subheading>
            </div>

            <flux:input wire:model.live.debounce.300ms="mediaSearch" placeholder="Cari nama file..." icon="magnifying-glass" />

            @php
                $pickerMedia = \App\Models\Media::query()
                    ->images()
                    ->search($mediaSearch !== '' ? $mediaSearch : null)
                    ->latest('id')
                    ->take(24)
                    ->get();
            @endphp

            @if ($pickerMedia->isEmpty())
                <p class="py-8 text-center text-sm text-[#65646e]">Tidak ada media yang cocok.</p>
            @else
                <div class="grid max-h-[60vh] grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3">
                    @foreach ($pickerMedia as $item)
                        <button type="button" wire:key="lp-picker-{{ $item->id }}" wire:click="selectMedia({{ $item->id }})" class="overflow-hidden rounded-lg border border-[#e3eaff] bg-white text-left transition hover:border-[#0a1589]">
                            <span class="block aspect-[4/3] bg-[#fafbff]">
                                <img src="{{ $item->url() }}" alt="{{ $item->alt_text ?? '' }}" class="h-full w-full object-cover" loading="lazy" />
                            </span>
                            <span class="block truncate px-2 py-1.5 text-xs text-[#100f12]" title="{{ $item->original_name }}">{{ $item->original_name }}</span>
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Tutup</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-lp-kota-deletion" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus data ini?</flux:heading>
                <flux:subheading>
                    Data LP untuk wilayah “{{ $deletingLabel }}” akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
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
