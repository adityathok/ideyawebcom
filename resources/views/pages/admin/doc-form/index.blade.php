<?php

use App\Models\DocPage;
use App\Models\DocVersion;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Form Dokumen')] class extends Component
{
    #[Url]
    public ?int $id = null;

    public bool $isEdit = false;

    public ?int $product_id = null;

    public ?int $version_id = null;

    public ?int $parent_id = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $body = '';

    public string $status = 'draft';

    public int $sort_order = 0;

    public ?string $published_at = null;

    public bool $showPreview = false;

    public function mount(): void
    {
        if (! $this->id) {
            return;
        }

        $page = DocPage::with('version')->findOrFail($this->id);

        $this->isEdit = true;
        $this->product_id = $page->product_id;
        $this->version_id = $page->version_id;
        $this->parent_id = $page->parent_id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->excerpt = $page->excerpt ?? '';
        $this->body = $page->body;
        $this->status = $page->status->value;
        $this->sort_order = $page->sort_order;
        $this->published_at = $page->published_at?->format('Y-m-d\TH:i');
    }

    public function updatedProductId(): void
    {
        $this->version_id = null;
        $this->parent_id = null;
    }

    public function updatedVersionId(): void
    {
        $this->parent_id = null;
    }

    public function updatingTitle(string $value): void
    {
        if (! $this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    public function save(): void
    {
        // Properti bertipe di-`unset` Livewire saat `wire:model` dikosongkan, jadi
        // baca lewat `??` supaya tidak Error saat pengguna mengosongkan pilihannya.
        $versionId = $this->version_id ?? null;
        $parentId = $this->parent_id ?? null;
        $publishedAt = $this->published_at ?? null;

        $this->validate([
            'version_id' => ['required', 'integer', Rule::exists('doc_versions', 'id')],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('doc_pages', 'id')->where('version_id', $versionId),
                function (string $attribute, mixed $value, Closure $fail) use ($versionId): void {
                    $parent = DocPage::whereKey($value)->first();

                    if ($parent === null || (int) $parent->version_id !== (int) $versionId) {
                        $fail('Induk halaman harus berada di versi yang sama.');

                        return;
                    }

                    if ((int) $value === $this->id) {
                        $fail('Halaman tidak bisa menjadi induk dirinya sendiri.');

                        return;
                    }

                    if ($this->isDescendantOf((int) $value, (int) $this->id)) {
                        $fail('Halaman tidak bisa dipindah ke bawah turunannya sendiri.');
                    }
                },
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('doc_pages', 'slug')->where('version_id', $versionId)->ignore($this->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $this->slug ?: Str::slug($this->title);

        if ($slug === '') {
            $slug = Str::lower(Str::random(8));
        }

        $excerpt = $this->excerpt ?: Str::limit(strip_tags($this->body), 160);

        // `product_id` sengaja tidak dikirim: hook `saving()` model menurunkannya dari versi.
        $data = [
            'version_id' => $versionId,
            'parent_id' => $parentId ?: null,
            'title' => $this->title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body' => $this->body,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'published_at' => $publishedAt ? Carbon::parse($publishedAt) : ($this->status === 'published' ? now() : null),
        ];

        $page = DocPage::updateOrCreate(['id' => $this->id], $data);

        $message = $this->isEdit ? 'Halaman dokumentasi diperbarui.' : 'Halaman dokumentasi dibuat.';

        $this->id = $page->id;
        $this->isEdit = true;
        $this->slug = $page->slug;
        $this->published_at = $page->published_at?->format('Y-m-d\TH:i');

        Flux::toast(variant: 'success', text: $message);
    }

    private function isDescendantOf(int $candidateId, int $pageId): bool
    {
        $visited = [];

        $currentId = $candidateId;

        while (true) {
            // Data melingkar (induk menunjuk ke turunannya sendiri) berhenti di sini
            // alih-alih berputar tanpa henti.
            if (isset($visited[$currentId])) {
                return false;
            }

            $visited[$currentId] = true;

            if ($currentId === $pageId) {
                return true;
            }

            $parentId = DocPage::whereKey($currentId)->value('parent_id');

            if ($parentId === null) {
                return false;
            }

            $currentId = (int) $parentId;
        }
    }
}; ?>
<section class="w-full max-w-3xl mx-auto">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.docs') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-[#65646e] hover:text-[#100f12]">
            <span>←</span> Kembali ke Halaman Dokumentasi
        </a>
    </div>

    <div class="mt-4">
        <flux:heading size="xl">{{ $isEdit ? 'Edit Halaman' : 'Tulis Halaman Baru' }}</flux:heading>
        <flux:subheading>{{ $isEdit ? 'Perbarui halaman dokumentasi #'.$id.' dan simpan perubahan.' : 'Buat halaman dokumentasi baru untuk sebuah versi produk.' }}</flux:subheading>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 rounded-2xl border border-[#e3eaff] bg-white p-6">
        <flux:select wire:model.live="product_id" label="Produk" required description="Hanya untuk memilih versi di bawah — yang disimpan di halaman adalah versinya.">
            <flux:select.option value="">Pilih produk</flux:select.option>
            @foreach (\App\Models\Product::orderBy('name')->get() as $product)
                <flux:select.option value="{{ $product->id }}">{{ $product->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="version_id" label="Versi" required :disabled="$product_id === null" :description="$product_id === null ? 'Pilih produk terlebih dahulu untuk melihat versinya.' : null">
            <flux:select.option value="">Pilih versi</flux:select.option>
            @foreach (\App\Models\DocVersion::when($product_id, fn ($q) => $q->where('product_id', $product_id))->with('product')->orderBy('product_id')->orderBy('sort_order')->get() as $version)
                <flux:select.option value="{{ $version->id }}">{{ $product_id === null ? $version->product->name.' — '.$version->label : $version->label }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model="parent_id" label="Induk Halaman" :disabled="$version_id === null" description="Hanya halaman di versi yang sama yang bisa menjadi induk.">
            <flux:select.option value="">— Tanpa induk (halaman utama) —</flux:select.option>
            @foreach (\App\Models\DocPage::where('version_id', $version_id)->whereKeyNot($id)->ordered()->get() as $option)
                <flux:select.option value="{{ $option->id }}">{{ $option->title }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="title" label="Judul" required placeholder="Judul halaman..." />
        <flux:input wire:model="slug" label="Slug" description="Kosongkan untuk otomatis dari judul — harus unik di dalam satu versi." placeholder="judul-halaman" />
        <flux:textarea wire:model="excerpt" label="Ringkasan" rows="2" description="Kosongkan untuk otomatis dari konten." placeholder="Ringkasan singkat halaman..." />

        <flux:textarea wire:model.live.debounce.500ms="body" label="Konten (Markdown)" rows="18" class="font-mono text-sm" placeholder="# Judul bagian&#10;&#10;Tulis isi halaman di sini..." description="Gunakan Markdown: ## untuk sub-judul, **tebal**, - untuk daftar, ``` untuk blok kode, > untuk kutipan." />

        <div class="flex items-center justify-between">
            <span class="text-xs text-[#65646e]">Pratinjau dirender dari Markdown di atas.</span>
            <flux:button type="button" variant="ghost" size="sm" wire:click="togglePreview">{{ $showPreview ? 'Sunting' : 'Pratinjau' }}</flux:button>
        </div>

        @if ($showPreview)
            @php $preview = app(\App\Services\MarkdownService::class)->render($body); @endphp
            <div class="rounded-xl border border-[#e3eaff] bg-[#fafbff] p-4">
                @if (trim($body) === '')
                    <p class="text-sm text-[#65646e]">Belum ada konten untuk dipratinjau.</p>
                @else
                    <div class="prose max-w-none">{!! $preview['html'] !!}</div>
                @endif
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <flux:select wire:model="status" label="Status">
                <flux:select.option value="draft">Draft</flux:select.option>
                <flux:select.option value="published">Published</flux:select.option>
                <flux:select.option value="archived">Archived</flux:select.option>
            </flux:select>
            <flux:input type="number" wire:model="sort_order" label="Urutan" min="0" />
            <flux:input type="datetime-local" wire:model="published_at" label="Diterbitkan Pada" />
        </div>

        <div class="flex justify-end gap-2 border-t border-[#e3eaff] pt-4">
            <a href="{{ route('admin.docs') }}" wire:navigate class="inline-flex items-center rounded-lg border border-[#e3eaff] bg-white px-4 py-2 text-sm font-medium text-[#100f12] hover:bg-[#f3f6ff]">Batal</a>
            <flux:button type="submit" variant="primary">{{ $isEdit ? 'Update' : 'Simpan' }}</flux:button>
        </div>
    </form>
</section>
