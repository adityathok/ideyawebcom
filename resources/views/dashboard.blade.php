<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('admin.posts') }}" wire:navigate class="relative flex flex-col justify-center gap-2 overflow-hidden rounded-xl border border-[#d3cec6] bg-white p-6">
                <div class="flex items-center gap-2 text-sm font-medium text-[#626260]"><span class="size-2 rounded-full bg-green-500"></span> Posts</div>
                <div class="text-2xl font-bold text-[#111111]">{{ \App\Models\Post::count() }}</div>
                <div class="text-xs text-[#626260]">{{ \App\Models\Post::published()->count() }} published</div>
            </a>
            <a href="{{ route('admin.categories') }}" wire:navigate class="relative flex flex-col justify-center gap-2 overflow-hidden rounded-xl border border-[#d3cec6] bg-white p-6">
                <div class="flex items-center gap-2 text-sm font-medium text-[#626260]"><span class="size-2 rounded-full bg-blue-500"></span> Categories</div>
                <div class="text-2xl font-bold text-[#111111]">{{ \App\Models\Category::count() }}</div>
                <div class="text-xs text-[#626260]">Kategori blog</div>
            </a>
            <a href="{{ route('admin.tags') }}" wire:navigate class="relative flex flex-col justify-center gap-2 overflow-hidden rounded-xl border border-[#d3cec6] bg-white p-6">
                <div class="flex items-center gap-2 text-sm font-medium text-[#626260]"><span class="size-2 rounded-full bg-amber-500"></span> Tags</div>
                <div class="text-2xl font-bold text-[#111111]">{{ \App\Models\Tag::count() }}</div>
                <div class="text-xs text-[#626260]">Tags blog</div>
            </a>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                <h3 class="font-semibold text-[#111111]">Artikel Terbaru</h3>
                <ul class="mt-4 space-y-2">
                    @foreach(\App\Models\Post::latest()->take(5)->get() as $post)
                        <li class="flex items-center justify-between text-sm">
                            <a href="{{ route('blog.show', $post) }}" target="_blank" class="font-medium text-[#111111] hover:underline">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</a>
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $post->status->value==='published' ? 'bg-green-100 text-green-700' : 'bg-[#ebe7e1] text-[#626260]' }}">{{ $post->status->label() }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.posts') }}" wire:navigate class="mt-4 inline-flex text-sm font-medium text-[#111111] underline">Kelola Posts &rarr;</a>
            </div>
            <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                <h3 class="font-semibold text-[#111111]">Profil Website</h3>
                @php $settings = \App\Models\Setting::profile(); @endphp
                <p class="mt-2 text-sm font-medium text-[#111111]">{{ $settings['company_name'] ?? '-' }}</p>
                <p class="text-sm text-[#626260]">{{ $settings['tagline'] ?? '' }}</p>
                <p class="mt-2 text-sm text-[#626260]">{{ \Illuminate\Support\Str::limit($settings['about'] ?? '', 120) }}</p>
                <a href="{{ route('admin.settings') }}" wire:navigate class="mt-4 inline-flex text-sm font-medium text-[#111111] underline">Edit Pengaturan &rarr;</a>
            </div>
        </div>
    </div>
</x-layouts::app>
