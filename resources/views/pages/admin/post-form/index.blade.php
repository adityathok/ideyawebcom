<?php

use App\Actions\Media\StoreMediaAction;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Form Post')] class extends Component
{
    use WithFileUploads;

    #[Url]
    public ?int $id = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $body = '';

    public string $status = 'draft';

    public ?int $category_id = null;

    public string $tags = '';

    public ?string $published_at = null;

    public $coverUpload = null;

    public ?int $coverMediaId = null;

    public string $image_caption = '';

    public ?string $existingImage = null;

    public $inlineImageUpload = null;

    public string $pickerTarget = 'cover';

    public string $mediaSearch = '';

    public bool $isEdit = false;

    public function mount(): void
    {
        if ($this->id) {
            $post = Post::with('tags')->findOrFail($this->id);
            $this->isEdit = true;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->excerpt = $post->excerpt ?? '';
            $this->body = $post->body;
            $this->status = $post->status->value;
            $this->category_id = $post->category_id;
            $this->tags = $post->tags->pluck('name')->implode(', ');
            $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
            $this->image_caption = $post->image_caption ?? '';
            $this->coverMediaId = $post->coverMedia()?->id;
            $this->existingImage = $post->imageUrl();
        }
    }

    public function updatingTitle(string $value): void
    {
        if (! $this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function removeImage(): void
    {
        $this->coverUpload = null;
        $this->coverMediaId = null;
        $this->existingImage = null;
        $this->image_caption = '';

        if (! $this->id) {
            return;
        }

        $post = Post::find($this->id);

        if ($post === null) {
            return;
        }

        // Kolom warisan ikut dikosongkan: selama masih berisi, `imageUrl()` akan
        // mengembalikan gambar yang baru saja dihapus pengguna.
        $post->setCoverMedia(null);
        $post->update(['image' => null, 'cover_image' => null, 'image_caption' => null]);
    }

    /**
     * Buka pemilih media dari perpustakaan.
     *
     * `cover` mengganti gambar utama; `inline` menyisipkan gambar ke dalam body.
     */
    public function openMediaPicker(string $target = 'cover'): void
    {
        $this->pickerTarget = $target === 'inline' ? 'inline' : 'cover';
        $this->mediaSearch = '';

        Flux::modal('media-picker')->show();
    }

    public function selectMedia(int $id): void
    {
        $media = Media::findOrFail($id);

        if ($this->pickerTarget === 'inline') {
            $this->dispatch('media-image-inserted', url: $media->url());
        } else {
            $this->coverMediaId = $media->id;
            $this->coverUpload = null;
            $this->existingImage = $media->url();
        }

        Flux::modal('media-picker')->close();
    }

    public function updatedInlineImageUpload(): void
    {
        if (! $this->inlineImageUpload instanceof UploadedFile) {
            return;
        }

        $this->validate([
            'inlineImageUpload' => StoreMediaAction::validationRules(true),
        ]);

        $media = app(StoreMediaAction::class)->handle($this->inlineImageUpload, auth()->id());

        $this->inlineImageUpload = null;

        $this->dispatch('media-image-inserted', url: $media->url());
    }

    /**
     * Parse the comma-separated tag input into tag IDs, creating missing tags.
     *
     * @return int[]
     */
    private function resolveTagIds(): array
    {
        $names = collect(explode(',', $this->tags))
            ->map(fn (string $name): string => trim($name))
            ->filter()
            ->unique(fn (string $name): string => Str::lower($name))
            ->values();

        return $names->map(function (string $name): int {
            $slug = Str::slug($name) ?: Str::lower(Str::random(8));

            return Tag::firstOrCreate(['slug' => $slug], ['name' => $name])->id;
        })->all();
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,'.($this->id ?? 'NULL'),
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|string|max:1000',
            'published_at' => 'nullable|date',
            'coverUpload' => StoreMediaAction::validationRules(),
            'image_caption' => 'nullable|string|max:500',
        ]);

        $slug = $this->slug ?: Str::slug($this->title).'-'.Str::lower(Str::random(4));
        $excerpt = $this->excerpt ?: Str::limit(strip_tags($this->body), 160);

        $data = [
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body' => $this->body,
            'status' => $this->status,
            'category_id' => $this->category_id ?: null,
            'published_at' => $this->published_at ? \Carbon\Carbon::parse($this->published_at) : ($this->status === 'published' ? now() : null),
            'user_id' => auth()->id(),
            // Kolom `image` warisan sengaja tidak ditulis lagi: gambar utama post
            // sekarang lewat pivot `mediables` supaya ikut terkelola di perpustakaan.
            'image_caption' => $this->image_caption ?: null,
        ];

        $post = Post::updateOrCreate(['id' => $this->id], $data);
        $post->tags()->sync($this->resolveTagIds());

        if ($this->coverUpload instanceof UploadedFile) {
            $this->coverMediaId = app(StoreMediaAction::class)->handle($this->coverUpload, auth()->id())->id;
        }

        $post->setCoverMedia($this->coverMediaId ? Media::find($this->coverMediaId) : null);

        $message = $this->isEdit ? 'Post diperbarui.' : 'Post dibuat.';

        $this->id = $post->id;
        $this->isEdit = true;
        $this->slug = $post->slug;
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
        $this->coverUpload = null;
        $this->existingImage = $post->imageUrl();

        Flux::toast(variant: 'success', text: $message);
    }
}; ?>
<section class="w-full max-w-3xl mx-auto">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.posts') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-[#65646e] hover:text-[#100f12]">
            <span>←</span> Kembali ke Posts
        </a>
    </div>

    <div class="mt-4">
        <flux:heading size="xl">{{ $isEdit ? 'Edit Post' : 'Tulis Post Baru' }}</flux:heading>
        <flux:subheading>{{ $isEdit ? 'Perbarui artikel #'.$id.' dan simpan perubahan.' : 'Buat artikel baru, lengkapi gambar dan caption bila perlu.' }}</flux:subheading>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-[#e3eaff] bg-white p-6" enctype="multipart/form-data">
        <flux:input wire:model="title" label="Judul" required placeholder="Judul artikel..." />
        <flux:input wire:model="slug" label="Slug" description="Kosongkan untuk auto-generate" placeholder="judul-artikel" />
        <flux:textarea wire:model="excerpt" label="Excerpt" rows="2" description="Ringkasan, kosongkan untuk auto dari body" placeholder="Ringkasan singkat..." />
        <x-wysiwyg wire:model="body" label="Body" placeholder="Tulis isi artikel..." description="Gunakan toolbar untuk memformat teks; tombol gambar mengunggah ke perpustakaan media." upload-property="inlineImageUpload" />

        <div class="flex justify-end">
            <flux:button type="button" size="sm" variant="ghost" wire:click="openMediaPicker('inline')">Sisipkan gambar dari galeri</flux:button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <flux:select wire:model="category_id" label="Kategori" placeholder="Pilih kategori">
                @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model="status" label="Status">
                <flux:select.option value="draft">Draft</flux:select.option>
                <flux:select.option value="published">Published</flux:select.option>
                <flux:select.option value="archived">Archived</flux:select.option>
            </flux:select>
        </div>

        <flux:input wire:model="published_at" label="Published At" type="datetime-local" />

        <!-- Gambar utama — diambil dari perpustakaan media, bukan lagi file lepas di post -->
        <div class="space-y-3 rounded-xl border border-[#e3eaff] bg-[#fafbff] p-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-[#100f12]">Gambar Utama</label>
                <span class="text-xs text-[#65646e]">JPG/PNG/WebP/GIF/AVIF maks {{ (int) config('media.max_size_kb') / 1024 }}MB</span>
            </div>

            @php
                $coverPreview = $coverUpload instanceof \Illuminate\Http\UploadedFile ? $coverUpload->temporaryUrl() : $existingImage;
            @endphp

            @if ($coverPreview)
                <div class="overflow-hidden rounded-lg border border-[#e3eaff] bg-white p-2">
                    <img src="{{ $coverPreview }}" alt="Pratinjau gambar utama" class="h-52 w-full rounded-md object-cover" />
                </div>
            @else
                <p class="text-xs text-[#65646e]">Belum ada gambar utama. Pilih dari galeri atau unggah file baru.</p>
            @endif

            <div class="flex flex-wrap gap-2">
                <flux:button size="sm" variant="ghost" type="button" wire:click="openMediaPicker('cover')">Pilih dari galeri</flux:button>
                @if ($coverPreview)
                    <flux:button size="sm" variant="danger" type="button" wire:click="removeImage">Hapus Gambar</flux:button>
                @endif
            </div>

            <input type="file" wire:model="coverUpload" accept="image/*" class="block w-full rounded-lg border border-[#e3eaff] bg-white px-3 py-2 text-sm text-[#100f12] file:mr-3 file:rounded-md file:border-0 file:bg-[#0a1589] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#06105a]" />
            <div wire:loading wire:target="coverUpload" class="text-xs text-[#65646e]">Mengunggah...</div>
            @error('coverUpload')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
            <flux:input wire:model="image_caption" label="Caption Gambar" placeholder="Keterangan gambar (opsional)" description="Maks 500 karakter" />
        </div>

        <flux:textarea wire:model="tags" label="Tags" rows="2" description="Pisahkan dengan koma, contoh: laravel, livewire, php" placeholder="laravel, livewire, php" />

        <div class="flex justify-end gap-2 border-t border-[#e3eaff] pt-4">
            <a href="{{ route('admin.posts') }}" wire:navigate class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-4 py-2 text-sm font-medium text-[#100f12] hover:bg-[#f3f6ff]">Batal</a>
            <flux:button type="submit" variant="primary">{{ $isEdit ? 'Update' : 'Simpan' }}</flux:button>
        </div>
    </form>

    <flux:modal name="media-picker" class="max-w-3xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $pickerTarget === 'inline' ? 'Sisipkan gambar di body' : 'Pilih gambar utama' }}</flux:heading>
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
                        <button type="button" wire:key="picker-{{ $item->id }}" wire:click="selectMedia({{ $item->id }})" class="overflow-hidden rounded-lg border border-[#e3eaff] bg-white text-left transition hover:border-[#0a1589]">
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
</section>
