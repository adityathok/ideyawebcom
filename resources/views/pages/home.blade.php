<x-layouts.public :title="($profile['company_name'] ?? config('app.name', 'IdeyaWeb'))">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $heroSky = file_exists(public_path('images/hero-sky.jpg')) ? asset('images/hero-sky.jpg') : 'https://images.unsplash.com/photo-1570483358100-6d222cdea6ff?auto=format&fit=crop&w=2400&q=80';
    @endphp

    {{-- Hero Agency — foto langit + overlay lembut + ikon bertebangan (tanpa aurora), center: heading / subheading / description + 2 CTA --}}
    <section data-hero-anim class="relative isolate overflow-hidden bg-[#bae6fd]">
        {{-- Latar: foto langit biru (ganti src dengan /images/hero-sky.jpg bila punya asset sendiri) --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <img data-hero-sky src="{{ $heroSky }}" alt="" class="absolute -top-8 left-0 h-[calc(100%+4rem)] w-full object-cover object-center" loading="eager" fetchpriority="high" />
            {{-- Wash agar teks ink #111111 tetap kontras di atas foto (DESIGN.md) --}}
            <div class="absolute inset-0 bg-gradient-to-b from-[#f0f9ff]/70 via-[#e0f2fe]/45 to-[#f5f1ec]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-white/20 via-transparent to-white/10"></div>
            {{-- Ikon bertebangan — dekoratif, di-animate via Motion --}}
            <span data-hero-icon class="absolute left-[6%] top-[16%] flex size-12 items-center justify-center rounded-xl bg-white/60 text-xl text-[#111111] opacity-80 shadow-sm will-change-transform">◈</span>
            <span data-hero-icon class="absolute bottom-[20%] left-[12%] flex size-10 items-center justify-center rounded-full bg-white/60 text-lg text-[#111111] opacity-70 shadow-sm will-change-transform">◎</span>
            <span data-hero-icon class="absolute right-[8%] top-[18%] flex size-12 items-center justify-center rounded-xl bg-white/60 text-xl text-[#111111] opacity-80 shadow-sm will-change-transform">⬡</span>
            <span data-hero-icon class="absolute bottom-[22%] right-[12%] flex size-10 items-center justify-center rounded-full bg-white/60 text-lg text-[#111111] opacity-70 shadow-sm will-change-transform">↗</span>
            <span data-hero-icon class="absolute left-[30%] top-[10%] flex size-8 items-center justify-center rounded-lg bg-white/60 text-base text-[#111111] opacity-60 shadow-sm will-change-transform">✦</span>
            <span data-hero-icon class="absolute bottom-[14%] right-[30%] flex size-8 items-center justify-center rounded-lg bg-white/60 text-base text-[#111111] opacity-60 shadow-sm will-change-transform">☰</span>
        </div>

        <div class="mx-auto flex min-h-[560px] max-w-3xl items-center justify-center px-4 py-24 text-center sm:min-h-[640px] sm:px-6 sm:py-32 lg:min-h-[760px] lg:py-40">
            <div class="w-full">
            <p data-hero-sub class="text-sm font-medium tracking-wide text-[#626260]">{{ ($profile['company_name'] ?? 'IdeyaWeb') }}</p>
            <h1 data-hero-heading class="mx-auto mt-4 max-w-2xl text-4xl font-bold leading-[1.05] tracking-[-0.8px] text-[#111111] sm:text-5xl lg:text-[56px] lg:leading-[1.10] lg:tracking-[-1.4px]">
                {{ ($profile['tagline'] ?? '') ?: 'Developer Website & Web App' }}
            </h1>
            <p data-hero-desc class="mx-auto mt-6 max-w-2xl text-base leading-7 text-[#626260] sm:text-[18px] sm:leading-7">
                {{ !empty($profile['about']) ? \Illuminate\Support\Str::limit($profile['about'], 200) : 'Kami membangun website & app custom, dan berpengalaman membangun, mengoptimasi, dan merawat Aplikasi dan Web WordPress, dari company profile hingga WooCommerce.' }}
            </p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <a data-hero-cta href="#kontak" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-black">Konsultasi Gratis</a>
                <a data-hero-cta href="#layanan" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-[15px] font-medium leading-none text-[#111111] hover:bg-[#ebe7e1]">Lihat Layanan</a>
            </div>
            </div>
        </div>
    </section>

    @php
        $services = [
            [
                'icon' => '◈',
                'title' => 'Web App & App Custom',
                'short' => 'Dashboard, sistem & aplikasi bisnis',
                'desc' => 'Spesialis membangun web app dan app custom — dashboard, sistem internal, dan aplikasi bisnis yang cepat, aman, dan mudah diskalakan. Dirancang sesuai alur kerja Anda, lengkap dengan testing dan dokumentasi sejak hari pertama.',
                'points' => ['Dashboard & sistem internal', 'Auth, roles & permission', 'API & integrasi siap pakai', 'Testing & dokumentasi'],
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=80',
            ],
            [
                'icon' => '◎',
                'title' => 'Website Company Profile',
                'short' => 'Ringan & SEO-friendly',
                'desc' => 'Website bisnis yang ringan, cepat, dan mudah dikelola — fokus pada kecepatan, SEO teknis, dan konversi. Cocok untuk company profile, landing page, dan katalog.',
                'points' => ['Desain responsif & cepat', 'SEO teknis & meta rapi', 'Mudah dikelola / CMS ringan', 'Siap dihubungkan ke WhatsApp & formulir'],
                'image' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?auto=format&fit=crop&w=1600&q=80',
            ],
            [
                'icon' => '⬡',
                'title' => 'WordPress Development',
                'short' => 'Theme, plugin & WooCommerce',
                'desc' => 'Berpengalaman mengerjakan WordPress — custom theme, custom plugin, Elementor/Gutenberg, hingga WooCommerce. Rapi, aman, dan tidak berat.',
                'points' => ['Custom theme & child theme', 'Custom plugin & Gutenberg block', 'Elementor / Gutenberg yang rapi', 'WooCommerce & payment gateway'],
                'image' => 'https://images.unsplash.com/photo-1547658719-da2b51169166?auto=format&fit=crop&w=1600&q=80',
            ],
            [
                'icon' => '↗',
                'title' => 'Optimasi & Percepatan WordPress',
                'short' => 'Core Web Vitals lebih hijau',
                'desc' => 'Audit menyeluruh untuk WordPress yang lambat atau bermasalah — performa, keamanan, dan SEO teknis. Hasil terukur, sebelum vs sesudah.',
                'points' => ['Audit kecepatan & Core Web Vitals', 'Hardening keamanan & cleanup', 'Optimasi database & caching', 'SEO teknis & struktur data'],
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1600&q=80',
            ],
            [
                'icon' => '✦',
                'title' => 'API & Integrasi Web',
                'short' => 'Payment & pihak ketiga',
                'desc' => 'REST API, payment gateway, dan integrasi layanan pihak ketiga untuk website maupun web app — sinkronisasi data yang andal.',
                'points' => ['REST API & webhook', 'Payment gateway (Midtrans, Xendit, dll)', 'Integrasi ERP / CRM / spreadsheet', 'Dokumentasi API yang jelas'],
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1600&q=80',
            ],
            [
                'icon' => '☰',
                'title' => 'Maintenance Website',
                'short' => 'Update, backup & support',
                'desc' => 'Update, backup, monitoring, dan support rutin khusus website & web app — termasuk WordPress. Website tetap aman tanpa Anda repot.',
                'points' => ['Update rutin & backup', 'Monitoring uptime & error', 'Perbaikan bug prioritas', 'Laporan berkala yang jelas'],
                'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1600&q=80',
            ],
        ];
    @endphp

    {{-- Layanan — carousel tab dua kolom: kiri daftar judul, kanan deskripsi bergantian (DESIGN.md feature-card) --}}
    <section id="layanan" class="border-y border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-medium tracking-wide text-[#626260]">Layanan</p>
                <h2 class="mt-2 text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Developer website &amp; web app saja</h2>
                <p class="mt-3 text-base leading-7 text-[#626260]">Pilih layanan di kiri — detailnya tampil di kanan dan berganti otomatis. Klik judul untuk melompat ke layanan tertentu.</p>
            </div>
            <div
                data-service-tabs
                x-data="{ active: 0, total: {{ count($services) }}, timer: null, start() { if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; this.stop(); this.timer = setInterval(() => { this.active = (this.active + 1) % this.total; }, 6000); }, stop() { if (this.timer) clearInterval(this.timer); this.timer = null; }, next() { this.active = (this.active + 1) % this.total; this.stop(); this.start(); }, prev() { this.active = (this.active - 1 + this.total) % this.total; this.stop(); this.start(); }, go(i) { this.active = i; this.stop(); this.start(); }, pause() { this.stop(); }, resume() { this.start(); } }"
                x-init="start()"
                @mouseenter="pause()"
                @mouseleave="resume()"
                @focusin="pause()"
                @focusout="resume()"
                @visibilitychange.window="document.hidden ? pause() : resume()"
                class="mt-10 grid gap-6 lg:grid-cols-12 lg:items-stretch"
            >
                {{-- Kolom kiri: daftar judul layanan --}}
                <div
                    role="tablist"
                    aria-label="Daftar layanan"
                    aria-orientation="vertical"
                    @keydown.arrow-down.prevent="next()"
                    @keydown.arrow-up.prevent="prev()"
                    @keydown.arrow-right.prevent="next()"
                    @keydown.arrow-left.prevent="prev()"
                    @keydown.home.prevent="go(0)"
                    @keydown.end.prevent="go(total - 1)"
                    class="flex gap-3 overflow-x-auto pb-2 lg:col-span-5 lg:flex-col lg:overflow-visible lg:pb-0"
                >
                    @foreach($services as $i => $service)
                        <button
                            type="button"
                            role="tab"
                            id="layanan-tab-{{ $i }}"
                            aria-controls="layanan-panel-{{ $i }}"
                            :aria-selected="active === {{ $i }} ? 'true' : 'false'"
                            :tabindex="active === {{ $i }} ? '0' : '-1'"
                            @click="go({{ $i }})"
                            :class="active === {{ $i }} ? 'border-[#111111] bg-white shadow-sm' : 'border-[#d3cec6] bg-transparent hover:border-[#9c9fa5] hover:bg-white/60'"
                            class="flex w-64 shrink-0 items-center gap-4 rounded-xl border p-4 text-left transition lg:w-full"
                        >
                            <span aria-hidden="true" class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">{{ $service['icon'] }}</span>
                            <span class="min-w-0">
                                <span class="flex items-baseline gap-2">
                                    <span aria-hidden="true" class="text-xs font-medium tabular-nums text-[#9c9fa5]">0{{ $i + 1 }}</span>
                                    <span class="truncate text-[15px] font-medium text-[#111111]">{{ $service['title'] }}</span>
                                </span>
                                <span class="mt-0.5 block truncate text-sm text-[#626260]">{{ $service['short'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- Kolom kanan: deskripsi layanan bergantian — tiap layanan punya background image sendiri, tinggi disamakan dengan kolom daftar --}}
                <div class="flex flex-col lg:col-span-7">
                    <div class="relative flex-1 overflow-hidden rounded-xl border border-[#d3cec6] bg-white">
                        @foreach($services as $i => $service)
                            <div
                                role="tabpanel"
                                id="layanan-panel-{{ $i }}"
                                aria-labelledby="layanan-tab-{{ $i }}"
                                tabindex="0"
                                x-show="active === {{ $i }}"
                                x-cloak
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-3"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="relative isolate flex h-full flex-col overflow-hidden"
                            >
                                {{-- Gambar di atas: area terlihat diperbesar, bawahnya tertutup gradasi pudar --}}
                                <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 sm:h-[28rem]">
                                    <img src="{{ $service['image'] }}" alt="" loading="lazy" class="h-full w-full object-cover" />
                                </div>
                                <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10" style="background: linear-gradient(to bottom, transparent 0, rgba(255,255,255,0.55) 180px, #ffffff 320px, #ffffff 100%);"></div>
                                {{-- Spacer: area gambar yang dibiarkan terlihat --}}
                                <div aria-hidden="true" class="h-52 shrink-0 sm:h-72"></div>
                                <div class="h-full p-6 pt-2 sm:p-8 sm:pt-3">
                                <div class="flex items-center gap-4">
                                    <span aria-hidden="true" class="flex size-12 items-center justify-center rounded-xl bg-[#f5f1ec] text-xl text-[#111111]">{{ $service['icon'] }}</span>
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-widest text-[#9c9fa5]">Layanan 0{{ $i + 1 }} / 0{{ count($services) }}</p>
                                        <h3 class="mt-1 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">{{ $service['title'] }}</h3>
                                    </div>
                                </div>
                                <p class="mt-4 text-base leading-7 text-[#626260]">{{ $service['desc'] }}</p>
                                <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                                    @foreach($service['points'] as $point)
                                        <li class="flex items-start gap-2 text-sm leading-6 text-[#111111]">
                                            <span aria-hidden="true" class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-[#f5f1ec] text-xs">✓</span>
                                            <span>{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="#kontak" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-black">Konsultasikan kebutuhan ini</a>
                                    <a href="#proses" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-[15px] font-medium leading-none text-[#111111] hover:bg-[#ebe7e1]">Lihat proses kerja</a>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Kontrol carousel --}}
                    <div class="mt-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="prev()" aria-label="Layanan sebelumnya" class="inline-flex size-10 items-center justify-center rounded-full border border-[#d3cec6] bg-white text-[#111111] hover:bg-[#ebe7e1]">←</button>
                            <button type="button" @click="next()" aria-label="Layanan berikutnya" class="inline-flex size-10 items-center justify-center rounded-full border border-[#d3cec6] bg-white text-[#111111] hover:bg-[#ebe7e1]">→</button>
                        </div>

                        <p class="text-sm tabular-nums text-[#626260]" aria-live="polite"><span x-text="active + 1"></span> / {{ count($services) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Tentang / Profil — DESIGN.md: surface-1 section (96px), eyebrow sentence-case, headline 28/500; nilai di bawah sebagai strip flat tanpa rounded tanpa gap, hanya dibatasi border --}}
    <section id="tentang" class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-medium text-[#626260]">Profil</p>
                <h2 class="mt-2 text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Tentang {{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.1px] text-[#626260]">{{ $profile['about'] ?? 'IdeyaWeb adalah developer website, web app, dan app — spesialis membangun web app & app custom, dan berpengalaman mengerjakan WordPress, dari pembuatan hingga optimasi dan perawatan.' }}</p>
                <div class="mt-10 grid gap-8 pt-8 sm:grid-cols-3 text-center">
                    <div>
                        <p class="text-[40px] xl:text-[70px] font-medium tabular-nums leading-[1.15] tracking-[-0.8px] text-[#111111]">50+</p>
                        <p class="mt-1 text-sm text-[#626260]">Proyek selesai</p>
                    </div>
                    <div>
                        <p class="text-[40px] xl:text-[70px] font-medium tabular-nums leading-[1.15] tracking-[-0.8px] text-[#111111]">98%</p>
                        <p class="mt-1 text-sm text-[#626260]">Kepuasan klien</p>
                    </div>
                    <div>
                        <p class="text-[40px] xl:text-[70px] font-medium tabular-nums leading-[1.15] tracking-[-0.8px] text-[#111111]">5★</p>
                        <p class="mt-1 text-sm text-[#626260]">Rating layanan</p>
                    </div>
                </div>
            </div>
            {{-- Strip nilai: flat, tanpa rounded, tanpa gap — hanya dibatasi border hairline-soft --}}
            <div class="mt-12 grid border-y border-[#d3cec6] sm:grid-cols-2 lg:grid-cols-4">
                <div class="border-b border-[#ebe7e1] px-6 py-6 sm:border-r lg:border-b-0">
                    <h3 class="text-[22px] font-medium leading-[1.25] tracking-[-0.3px] text-[#111111]">Spesialis web app & app</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Fokus ke web app & app custom yang cepat, aman, dan mudah diskalakan.</p>
                </div>
                <div class="border-b border-[#ebe7e1] px-6 py-6 lg:border-r lg:border-b-0">
                    <h3 class="text-[22px] font-medium leading-[1.25] tracking-[-0.3px] text-[#111111]">Berpengalaman WordPress</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Custom theme &amp; plugin, WooCommerce, migrasi, dan hardening keamanan.</p>
                </div>
                <div class="border-b border-[#ebe7e1] px-6 py-6 sm:border-b-0 sm:border-r">
                    <h3 class="text-[22px] font-medium leading-[1.25] tracking-[-0.3px] text-[#111111]">Kualitas terjaga</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Code review, testing, dan dokumentasi sejak hari pertama.</p>
                </div>
                <div class="px-6 py-6">
                    <h3 class="text-[22px] font-medium leading-[1.25] tracking-[-0.3px] text-[#111111]">Support jangka panjang</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Maintenance rutin untuk website, web app, dan WordPress Anda.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Proses — numbered cards --}}
    <section id="proses" class="border-y border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium tracking-wide text-[#626260]">Cara kerja</p>
                    <h2 class="mt-2 text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Proses yang sederhana &amp; terukur</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-[#626260]">Transparan dari discovery hingga launch — Anda tahu apa yang dikerjakan dan kapan selesai.</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-widest text-[#9c9fa5]">01</p>
                    <h3 class="mt-2 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Diskusi &amp; Discovery</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Gali tujuan, audiens, dan batasan. Output: scope &amp; estimasi jelas.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-widest text-[#9c9fa5]">02</p>
                    <h3 class="mt-2 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Desain &amp; Prototipe</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Wireframe → UI → prototipe interaktif untuk validasi cepat.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-widest text-[#9c9fa5]">03</p>
                    <h3 class="mt-2 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Develop &amp; QA</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Build iteratif, code review, dan testing sebelum rilis.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-widest text-[#9c9fa5]">04</p>
                    <h3 class="mt-2 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Launch &amp; Scale</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Deploy, monitoring, dan iterasi berbasis data pengguna.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA banner — surface-1, rounded lg, padding 48px (DESIGN.md cta-banner) --}}
    <section class="bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-[#d3cec6] bg-white p-8 sm:p-12">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Butuh website atau web app baru?</h2>
                        <p class="mt-3 max-w-xl text-base leading-7 text-[#626260]">Ceritakan kebutuhan website, web app, atau WordPress Anda — kami beri estimasi dan rekomendasi tanpa komitmen.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="#kontak" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-black">Hubungi Kami</a>
                        <a href="#layanan" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-[15px] font-medium leading-none text-[#111111] hover:bg-[#ebe7e1]">Pelajari layanan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
