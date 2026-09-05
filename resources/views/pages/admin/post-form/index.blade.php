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

    /** @var int[] */
    public array $tag_ids = [];

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
            $this->tag_ids = $post->tags->pluck('id')->toArray();
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

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,'.($this->id ?? 'NULL'),
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'array',
            'tag_ids.*' => 'exists:tags,id',
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
        $post->tags()->sync($this->tag_ids);

        Flux::toast(variant: 'success', text: $this->isEdit ? 'Post diperbarui.' : 'Post dibuat.');

        $this->redirect(route('admin.posts'), navigate: true);
    }
}; ?>
<section class="w-full max-w-3xl mx-auto">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.posts') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-[#626260] hover:text-[#111111]">
            <span>←</span> Kembali ke Posts
        </a>
    </div>

    <div class="mt-4">
        <flux:heading size="xl">{{ $isEdit ? 'Edit Post' : 'Tulis Post Baru' }}</flux:heading>
        <flux:subheading>{{ $isEdit ? 'Perbarui artikel #'.$id.' dan simpan perubahan.' : 'Buat artikel baru, lengkapi gambar dan caption bila perlu.' }}</flux:subheading>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-[#d3cec6] bg-white p-6" enctype="multipart/form-data">
        <flux:input wire:model="title" label="Judul" required placeholder="Judul artikel..." />
        <flux:input wire:model="slug" label="Slug" description="Kosongkan untuk auto-generate" placeholder="judul-artikel" />
        <flux:textarea wire:model="excerpt" label="Excerpt" rows="2" description="Ringkasan, kosongkan untuk auto dari body" placeholder="Ringkasan singkat..." />
        <flux:textarea wire:model="body" label="Body" rows="8" required placeholder="Isi artikel..." />

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
        <div class="space-y-3 rounded-xl border border-[#d3cec6] bg-[#f5f1ec] p-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-[#111111]">Gambar</label>
                <span class="text-xs text-[#626260]">JPG/PNG/WebP max 4MB</span>
            </div>
            @if ($existingImage || $imageUpload)
                <div class="overflow-hidden rounded-lg border border-[#d3cec6] bg-white p-2">
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
            <input type="file" wire:model="imageUpload" accept="image/*" class="block w-full rounded-lg border border-[#d3cec6] bg-white px-3 py-2 text-sm text-[#111111] file:mr-3 file:rounded-md file:border-0 file:bg-[#111111] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-black" />
            <div wire:loading wire:target="imageUpload" class="text-xs text-[#626260]">Mengunggah...</div>
            @error('imageUpload')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
            <flux:input wire:model="image_caption" label="Caption Gambar" placeholder="Keterangan gambar (opsional)" description="Maks 500 karakter" />
        </div>

        <div>
            <label class="text-sm font-medium text-[#111111]">Tags</label>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach (\App\Models\Tag::orderBy('name')->get() as $tag)
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm {{ in_array($tag->id, $tag_ids) ? 'bg-[#111111] text-white border-[#111111]' : 'border-[#d3cec6] bg-white text-[#111111] hover:bg-[#f5f1ec]' }}">
                        <input type="checkbox" value="{{ $tag->id }}" wire:model.live="tag_ids" class="sr-only" />
                        #{{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-[#ebe7e1] pt-4">
            <a href="{{ route('admin.posts') }}" wire:navigate class="inline-flex items-center rounded-lg border border-[#d3cec6] bg-white px-4 py-2 text-sm font-medium text-[#111111] hover:bg-[#f5f1ec]">Batal</a>
            <flux:button type="submit" variant="primary">{{ $isEdit ? 'Update' : 'Simpan' }}</flux:button>
        </div>
    </form>
</section>
