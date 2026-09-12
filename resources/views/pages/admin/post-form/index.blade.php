<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
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

    public $imageUpload = null;

    public string $image_caption = '';

    public ?string $existingImage = null;

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
        if ($this->id) {
            $post = Post::find($this->id);
            if ($post?->image) {
                Storage::disk('public')->delete($post->image);
                $post->update(['image' => null, 'image_caption' => null]);
            }
        }
        $this->imageUpload = null;
        $this->existingImage = null;
        $this->image_caption = '';
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
            'imageUpload' => 'nullable|image|max:4096',
            'image_caption' => 'nullable|string|max:500',
        ]);

        $slug = $this->slug ?: Str::slug($this->title).'-'.Str::lower(Str::random(4));
        $excerpt = $this->excerpt ?: Str::limit(strip_tags($this->body), 160);

        $existing = $this->id ? Post::find($this->id) : null;
        $imagePath = $existing?->image;

        if ($this->imageUpload) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->imageUpload->store('posts', 'public');
        }

        $data = [
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body' => $this->body,
            'status' => $this->status,
            'category_id' => $this->category_id ?: null,
            'published_at' => $this->published_at ? \Carbon\Carbon::parse($this->published_at) : ($this->status === 'published' ? now() : null),
            'user_id' => auth()->id(),
            'image' => $imagePath,
            'image_caption' => $this->image_caption ?: null,
        ];

        $post = Post::updateOrCreate(['id' => $this->id], $data);
        $post->tags()->sync($this->resolveTagIds());

        $message = $this->isEdit ? 'Post diperbarui.' : 'Post dibuat.';

        $this->id = $post->id;
        $this->isEdit = true;
        $this->slug = $post->slug;
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
        $this->imageUpload = null;
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
        <x-wysiwyg wire:model="body" label="Body" placeholder="Tulis isi artikel..." description="Gunakan toolbar untuk memformat teks." />

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

        <!-- Gambar -->
        <div class="space-y-3 rounded-xl border border-[#e3eaff] bg-[#fafbff] p-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-[#100f12]">Gambar</label>
                <span class="text-xs text-[#65646e]">JPG/PNG/WebP max 4MB</span>
            </div>
            @if ($existingImage || $imageUpload)
                <div class="overflow-hidden rounded-lg border border-[#e3eaff] bg-white p-2">
                    @if ($imageUpload)
                        <img src="{{ $imageUpload->temporaryUrl() }}" alt="Preview" class="h-52 w-full rounded-md object-cover" />
                    @else
                        <img src="{{ $existingImage }}" alt="Gambar post" class="h-52 w-full rounded-md object-cover" />
                    @endif
                    <div class="mt-2 flex gap-2">
                        <flux:button size="sm" variant="danger" wire:click="removeImage" type="button">Hapus Gambar</flux:button>
                    </div>
                </div>
            @endif
            <input type="file" wire:model="imageUpload" accept="image/*" class="block w-full rounded-lg border border-[#e3eaff] bg-white px-3 py-2 text-sm text-[#100f12] file:mr-3 file:rounded-md file:border-0 file:bg-[#0a1589] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#06105a]" />
            <div wire:loading wire:target="imageUpload" class="text-xs text-[#65646e]">Mengunggah...</div>
            @error('imageUpload')
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
</section>
