<x-layouts.public :title="($profile['company_name'] ?? 'Home')">
    <!-- Hero — canvas warm, cards lift onto white (DESIGN.md) -->
    <section class="relative overflow-hidden bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="text-sm font-semibold tracking-widest text-[#626260] uppercase">{{ $profile['tagline'] ?? 'Digital Agency & IT Solution' }}</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-[#111111] sm:text-5xl">
                        {{ ($profile['company_name'] ?? 'Ideya Webcom') }}<br />
                        <span class="text-[#626260]">Membangun Digital Masa Depan</span>
                    </h1>
                    <p class="mt-6 text-lg leading-7 text-[#626260]">
                        {{ !empty($profile['about']) ? \Illuminate\Support\Str::limit($profile['about'], 200) : 'Kami membantu bisnis bertumbuh melalui website, aplikasi, dan strategi digital yang tepat.' }}
                    </p>
                    <div class="mt-8 flex gap-3">
                        <a href="{{ route('blog.index') }}" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none tracking-normal text-white hover:bg-black">Lihat Blog</a>
                        <a href="#about" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-[15px] font-medium leading-none text-[#111111] hover:bg-[#ebe7e1]">Tentang Kami</a>
                    </div>
                    <div class="mt-8 flex gap-6 text-sm">
                        <span class="flex items-center gap-2"><span class="size-2 rounded-full bg-green-500"></span> {{ \App\Models\Post::published()->count() }} Artikel</span>
                        <span class="flex items-center gap-2"><span class="size-2 rounded-full bg-blue-500"></span> {{ \App\Models\Category::count() }} Kategori</span>
                        <span class="flex items-center gap-2"><span class="size-2 rounded-full bg-amber-500"></span> {{ \App\Models\Tag::count() }} Tags</span>
                    </div>
                </div>
                @if($featuredPost)
                    <a href="{{ route('blog.show', $featuredPost) }}" class="group relative overflow-hidden rounded-2xl border border-[#d3cec6] bg-white p-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#626260]">Featured</p>
                        @if($featuredPost->category)
                            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium text-white" style="background: {{ $featuredPost->category->color ?? '#52525b' }}">{{ $featuredPost->category->name }}</span>
                        @endif
                        <h2 class="mt-3 text-2xl font-bold leading-tight text-[#111111] group-hover:text-[#111111]">{{ $featuredPost->title }}</h2>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-[#626260]">{{ $featuredPost->excerpt }}</p>
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @foreach($featuredPost->tags as $tag)
                                <span class="rounded-full bg-[#ebe7e1] px-2.5 py-1 text-xs font-medium text-[#111111]">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        <p class="mt-6 text-sm font-medium text-[#111111]">Baca selengkapnya →</p>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- About — on canvas, feature cards lift onto white -->
    <section id="about" class="border-y border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-[#111111]">Tentang Kami</h2>
            <p class="mt-3 max-w-3xl text-[#626260] leading-7">{{ $profile['about'] ?? 'Ideya Webcom adalah digital agency yang fokus pada pengembangan website dan aplikasi.' }}</p>
            <div class="mt-8 grid gap-6 sm:grid-cols-3">
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Website & Aplikasi</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Laravel, Livewire, dan teknologi modern.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Branding & Desain</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">UI/UX yang fokus pada konversi.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Growth & Marketing</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">SEO, konten, dan performa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Posts -->
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-bold text-[#111111]">Artikel Terbaru</h2>
                <p class="mt-1 text-sm text-[#626260]">Update terbaru dari blog kami</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-medium text-[#111111] hover:underline">Lihat semua →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($latestPosts as $post)
                <x-blog.post-card :post="$post" />
            @endforeach
        </div>
    </section>

    <!-- Categories & Tags -->
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                <h3 class="font-semibold text-[#111111]">Kategori</h3>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat) }}" class="inline-flex items-center gap-2 rounded-full border border-[#d3cec6] bg-white px-3 py-1.5 text-sm font-medium text-[#111111] hover:bg-[#f5f1ec]">
                            <span class="size-2 rounded-full" style="background: {{ $cat->color ?? '#52525b' }}"></span>
                            {{ $cat->name }} <span class="text-[#626260]">({{ $cat->posts_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                <h3 class="font-semibold text-[#111111]">Tags Populer</h3>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <a href="{{ route('blog.tag', $tag) }}" class="rounded-full bg-[#ebe7e1] px-3 py-1.5 text-sm font-medium text-[#111111] hover:bg-[#d3cec6]">#{{ $tag->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Popular -->
    @if($popularPosts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <h3 class="font-semibold text-[#111111]">Paling Populer</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($popularPosts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="rounded-xl border border-[#d3cec6] bg-white p-4">
                        <p class="text-xs font-medium" style="color: {{ $post->category?->color ?? '#71717a' }}">{{ $post->category?->name }}</p>
                        <h4 class="mt-1 line-clamp-2 text-sm font-semibold text-[#111111]">{{ $post->title }}</h4>
                        <p class="mt-2 text-xs text-[#626260]">{{ $post->view_count }} views</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>
