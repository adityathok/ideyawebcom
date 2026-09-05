<?php

use App\Models\Post;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Posts')] class extends Component
{
    use \Livewire\WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterCategory = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'filterCategory']);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();
        Flux::toast(variant: 'success', text: 'Post dihapus.');
    }
}; ?>
<section class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Posts</flux:heading>
            <flux:subheading>Kelola artikel blog ({{ \App\Models\Post::count() }} total, {{ \App\Models\Post::published()->count() }} published).</flux:subheading>
        </div>
        <a href="{{ route('admin.post-form') }}" wire:navigate class="inline-flex items-center rounded-lg bg-[#111111] px-4 py-2 text-sm font-medium text-white hover:bg-black">+ Post Baru</a>
    </div>

    <!-- Filter — card putih DESIGN.md: hairline #d3cec6, rounded-xl 12px -->
    <div class="mt-6 rounded-xl border border-[#d3cec6] bg-white p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" label="Cari artikel" placeholder="Cari judul..." icon="magnifying-glass" />
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:w-[420px]">
                <flux:select wire:model.live="filterStatus" label="Status" placeholder="Semua Status">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                    <flux:select.option value="archived">Archived</flux:select.option>
                </flux:select>
                <flux:select wire:model.live="filterCategory" label="Kategori" placeholder="Semua Kategori">
                    <flux:select.option value="">Semua Kategori</flux:select.option>
                    @foreach (\App\Models\Category::orderBy('name')->get() as $cat)
                        <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
        @if ($search !== '' || $filterStatus !== '' || $filterCategory !== '')
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#ebe7e1] pt-4">
                <span class="text-xs font-medium text-[#626260]">Filter aktif:</span>
                @if ($search !== '')
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#f5f1ec] border border-[#d3cec6] px-3 py-1 text-xs text-[#111111]">“{{ \Illuminate\Support\Str::limit($search, 24) }}”</span>
                @endif
                @if ($filterStatus !== '')
                    <span class="inline-flex rounded-full bg-[#111111] px-3 py-1 text-xs font-medium text-white">{{ ucfirst($filterStatus) }}</span>
                @endif
                @if ($filterCategory !== '')
                    @php $fc = \App\Models\Category::find($filterCategory); @endphp
                    @if ($fc)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#d3cec6] bg-white px-3 py-1 text-xs font-medium text-[#111111]"><span class="size-2 rounded-full" style="background: {{ $fc->color ?? '#52525b' }}"></span>{{ $fc->name }}</span>
                    @endif
                @endif
                <flux:button size="sm" variant="ghost" wire:click="clearFilters">Reset</flux:button>
            </div>
        @endif
    </div>

    @php
        $posts = \App\Models\Post::with(['category'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($filterStatus, fn ($q) => $q->where('status', $filterStatus))
            ->when($filterCategory, fn ($q) => $q->where('category_id', $filterCategory))
            ->latest()
            ->paginate(10);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#d3cec6] bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#f5f1ec]">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-[#111111] w-[42%]">Judul</th>
                        <th class="px-4 py-3 font-semibold text-[#111111]">Kategori</th>
                        <th class="px-4 py-3 font-semibold text-[#111111]">Status</th>
                        <th class="px-4 py-3 font-semibold text-[#111111] text-center">Views</th>
                        <th class="px-4 py-3 font-semibold text-[#111111] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ebe7e1]">
                    @forelse ($posts as $post)
                        <tr class="bg-white hover:bg-[#f5f1ec]/60 transition">
                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    <div class="hidden sm:block size-12 shrink-0 overflow-hidden rounded-lg border border-[#ebe7e1] bg-[#f5f1ec]">
                                        @php $thumb = $post->imageUrl(); @endphp
                                        @if ($thumb)
                                            <img src="{{ $thumb }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-[#7b7b78] text-xs">—</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-[#111111] line-clamp-1">{{ $post->title }}</div>
                                        <div class="text-xs text-[#626260] truncate">{{ $post->slug }}</div>
                                        @if (filled($post->image_caption))
                                            <div class="text-xs italic text-[#626260] line-clamp-1">“{{ $post->image_caption }}”</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($post->category)
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium text-white" style="background: {{ $post->category->color ?? '#52525b' }}">{{ $post->category->name }}</span>
                                @else
                                    <span class="text-[#626260]">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $post->status->value === 'published' ? 'bg-green-100 text-green-700' : ($post->status->value === 'draft' ? 'bg-[#ebe7e1] text-[#626260]' : 'bg-amber-100 text-amber-700') }}">{{ $post->status->label() }}</span>
                            </td>
                            <td class="px-4 py-3 text-center text-[#111111]">{{ $post->view_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('blog.show', $post) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#d3cec6] bg-white px-2.5 py-1.5 text-xs font-medium text-[#111111] hover:bg-[#f5f1ec]">Lihat</a>
                                    <a href="{{ route('admin.post-form', ['id' => $post->id]) }}" wire:navigate class="inline-flex items-center rounded-lg bg-[#111111] px-2.5 py-1.5 text-xs font-medium text-white hover:bg-black">Edit</a>
                                    <flux:button size="sm" variant="danger" wire:click="delete({{ $post->id }})" wire:confirm="Hapus post ini?">Hapus</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm text-[#626260]">Tidak ada post ditemukan.</p>
                                @if ($search !== '' || $filterStatus !== '' || $filterCategory !== '')
                                    <flux:button size="sm" variant="ghost" wire:click="clearFilters" class="mt-2">Reset filter</flux:button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#ebe7e1] bg-white p-4">
            {{ $posts->links() }}
        </div>
    </div>
</section>
