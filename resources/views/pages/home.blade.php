<x-layouts.public :title="($profile['company_name'] ?? config('app.name', 'IdeyaWeb'))">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();

        // Rootly gradient emphasis lands on the final word of the tagline.
        $tagline = trim(($profile['tagline'] ?? '') ?: 'Developer Website & Web App');
        $words = preg_split('/\s+/', $tagline) ?: [$tagline];
        $tailWord = count($words) > 1 ? (string) array_pop($words) : '';
        $heroHead = implode(' ', $words) ?: $tagline;
    @endphp

    {{-- Hero — bluish sky canvas, gradient accent on the tagline, deep blue primary CTA (DESIGN.md: hero) --}}
    <section data-hero-anim class="relative isolate overflow-hidden bg-[linear-gradient(180deg,#b9cdff_0%,#dae4ff_36%,#f1f5ff_68%,#ffffff_100%)]">
        {{-- Langit kebiruan: glow lembut di puncak + bauran biru di sudut atas --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(60%_55%_at_50%_0%,#ffffff8c,transparent_72%),radial-gradient(45%_50%_at_88%_10%,#7d95ff33,transparent_70%),radial-gradient(42%_45%_at_10%_4%,#ffffff66,transparent_70%)]"></div>

        <div class="mx-auto max-w-4xl px-4 pb-16 pt-32 text-center sm:px-6 sm:pt-40 lg:pb-20 lg:pt-44">
            <span data-hero-sub class="inline-flex items-center gap-2 rounded-[16px] bg-white/85 px-4 py-2 text-sm font-medium tracking-[-0.16px] text-[#0a1589] ring-1 ring-inset ring-[#c7d6ff]">
                <span aria-hidden="true" class="size-1.5 rounded-full bg-[#2b4bff]"></span>
                {{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}
            </span>
            <h1 data-hero-heading class="mx-auto mt-6 max-w-3xl text-[38px] font-medium leading-[1.05] tracking-[-1.2px] text-[#100f12] sm:text-[52px] sm:tracking-[-1.4px] lg:text-[60px] lg:leading-[1.03]">
                {{ $heroHead }}@if($tailWord) <span class="text-gradient">{{ $tailWord }}</span>@endif
            </h1>
            <p data-hero-desc class="mx-auto mt-6 max-w-2xl text-[17px] leading-8 tracking-[-0.16px] text-[#65646e] sm:text-[18px]">
                {{ !empty($profile['about']) ? \Illuminate\Support\Str::limit($profile['about'], 200) : 'Kami membangun website & app custom, dan berpengalaman membangun, mengoptimasi, dan merawat Aplikasi dan Web WordPress, dari company profile hingga WooCommerce.' }}
            </p>
            <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a data-hero-cta href="#kontak" class="inline-flex w-full items-center justify-center rounded-[20px] bg-[#0a1589] px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto sm:text-[18px]">Konsultasi Gratis</a>
                <a data-hero-cta href="#layanan" class="inline-flex w-full items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#100f12] transition hover:bg-[#f3f6ff] sm:w-auto sm:text-[18px]">Lihat Layanan</a>
            </div>
        </div>
    </section>

    {{-- Keunggulan — blue lift cards with blue icon tiles (DESIGN.md feature-card) --}}
    <section id="keunggulan" class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Keunggulan</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Mengapa Memilih {{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}?</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">Lebih dari sekadar membangun website, kami memastikan hasilnya tampil memukau di semua perangkat, cepat diakses, aman, dan siap mendampingi bisnis Anda jangka panjang.</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                    <span aria-hidden="true" class="flex size-11 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false">
                            <rect x="2" y="3" width="20" height="14" rx="2"></rect>
                            <path d="M8 21h8"></path>
                            <path d="M12 17v4"></path>
                        </svg>
                    </span>
                    <h3 class="mt-5 text-[20px] font-semibold leading-[1.25] tracking-[-0.16px] text-[#100f12]">Desain Modern &amp; Responsif</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Tampilan elegan dan sempurna di semua perangkat — HP, tablet, hingga laptop.</p>
                </div>
                <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                    <span aria-hidden="true" class="flex size-11 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </span>
                    <h3 class="mt-5 text-[20px] font-semibold leading-[1.25] tracking-[-0.16px] text-[#100f12]">Performa Cepat &amp; Aksesibel</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Dibangun dengan struktur kode yang bersih agar website cepat diakses dan ramah SEO (Search Engine Optimization).</p>
                </div>
                <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                    <span aria-hidden="true" class="flex size-11 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </span>
                    <h3 class="mt-5 text-[20px] font-semibold leading-[1.25] tracking-[-0.16px] text-[#100f12]">Skalabel &amp; Aman</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Siap berkembang mengikuti kebutuhan bisnis Anda, didukung sistem keamanan yang andal.</p>
                </div>
                <div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                    <span aria-hidden="true" class="flex size-11 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <polyline points="1 20 1 14 7 14"></polyline>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                        </svg>
                    </span>
                    <h3 class="mt-5 text-[20px] font-semibold leading-[1.25] tracking-[-0.16px] text-[#100f12]">Dukungan &amp; Pemeliharaan</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Layanan support dan perawatan berkala setelah website selesai diluncurkan.</p>
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

    {{-- Layanan — blue tint block, blue pill tabs, framed panel (DESIGN.md: pricing-tab + content-image-frame) --}}
    <section id="layanan" class="bg-[#f3f6ff]">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Layanan</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Apa yang kami kerjakan</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">Kami berpengalaman bertahun-tahun dalam membangun, merawat dan memelihara website profesional. Kami juga berpengalaman mengerjakan web app dashboard, saas, aplikasi internal untuk berbagai kebutuhan.</p>
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
                            :class="active === {{ $i }} ? 'border-[#0a1589] bg-[#0a1589] shadow-[0_2px_5px_#0a158933]' : 'border-[#e3eaff] bg-white/70 hover:border-[#c7d6ff] hover:bg-white'"
                            class="flex w-64 shrink-0 items-center gap-4 rounded-[16px] border p-4 text-left transition lg:w-full"
                        >
                            <span aria-hidden="true" :class="active === {{ $i }} ? 'bg-white/20 text-white' : 'bg-[#f3f6ff] text-[#0a1589]'" class="flex size-10 shrink-0 items-center justify-center rounded-[12px] transition">{{ $service['icon'] }}</span>
                            <span class="min-w-0">
                                <span class="flex items-baseline gap-2">
                                    <span aria-hidden="true" :class="active === {{ $i }} ? 'text-white/70' : 'text-[#aaa9ae]'" class="text-xs font-semibold tabular-nums transition">0{{ $i + 1 }}</span>
                                    <span :class="active === {{ $i }} ? 'text-white' : 'text-[#100f12]'" class="truncate text-[15px] font-medium transition">{{ $service['title'] }}</span>
                                </span>
                                <span :class="active === {{ $i }} ? 'text-white/80' : 'text-[#65646e]'" class="mt-0.5 block truncate text-sm transition">{{ $service['short'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- Kolom kanan: deskripsi layanan bergantian — tiap layanan punya background image sendiri, tinggi disamakan dengan kolom daftar --}}
                <div class="flex flex-col lg:col-span-7">
                    <div class="relative flex-1 overflow-hidden rounded-[32px] border border-[#e3eaff] bg-white">
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
                                    <span aria-hidden="true" class="flex size-12 items-center justify-center rounded-[14px] bg-[#f3f6ff] text-xl text-[#0a1589]">{{ $service['icon'] }}</span>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Layanan 0{{ $i + 1 }} / 0{{ count($services) }}</p>
                                        <h3 class="mt-1 text-[26px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $service['title'] }}</h3>
                                    </div>
                                </div>
                                <p class="mt-4 text-base leading-7 text-[#65646e]">{{ $service['desc'] }}</p>
                                <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                                    @foreach($service['points'] as $point)
                                        <li class="flex items-start gap-2 text-sm leading-6 text-[#100f12]">
                                            <span aria-hidden="true" class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-[#f3f6ff] text-xs text-[#0a1589]">✓</span>
                                            <span>{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="#kontak" class="inline-flex items-center justify-center rounded-[14px] bg-[#0a1589] px-6 py-3 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a]">Konsultasikan kebutuhan ini</a>
                                    <a href="#proses" class="inline-flex items-center justify-center rounded-[14px] border border-[#e3eaff] bg-white px-6 py-3 text-[15px] font-medium leading-none text-[#100f12] transition hover:bg-[#f3f6ff]">Lihat proses kerja</a>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Kontrol carousel --}}
                    <div class="mt-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="prev()" aria-label="Layanan sebelumnya" class="inline-flex size-10 items-center justify-center rounded-full border border-[#e3eaff] bg-white text-[#100f12] transition hover:bg-[#f3f6ff]">←</button>
                            <button type="button" @click="next()" aria-label="Layanan berikutnya" class="inline-flex size-10 items-center justify-center rounded-full border border-[#e3eaff] bg-white text-[#100f12] transition hover:bg-[#f3f6ff]">→</button>
                        </div>

                        <p class="text-sm tabular-nums text-[#65646e]" aria-live="polite"><span x-text="active + 1"></span> / {{ count($services) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Proses — blue cards; highlight dikelola lewat .is-active (lihat app.css + app.js) --}}
    <section id="proses" class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Cara kerja</p>
                    <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Proses yang sederhana &amp; terukur</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-[#65646e]">Transparan dari discovery hingga launch — Anda tahu apa yang dikerjakan dan kapan selesai.</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div data-proses-step class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-8 transition-colors duration-500">
                    <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">01</p>
                    <h3 class="mt-3 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Diskusi &amp; Discovery</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Gali tujuan, audiens, dan batasan. Output: scope &amp; estimasi jelas.</p>
                </div>
                <div data-proses-step class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-8 transition-colors duration-500">
                    <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">02</p>
                    <h3 class="mt-3 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Desain &amp; Prototipe</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Wireframe → UI → prototipe interaktif untuk validasi cepat.</p>
                </div>
                <div data-proses-step class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-8 transition-colors duration-500">
                    <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">03</p>
                    <h3 class="mt-3 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Develop &amp; QA</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Build iteratif, code review, dan testing sebelum rilis.</p>
                </div>
                <div data-proses-step class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-8 transition-colors duration-500">
                    <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">04</p>
                    <h3 class="mt-3 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Launch &amp; Scale</h3>
                    <p class="mt-2 text-sm leading-6 text-[#65646e]">Deploy, monitoring, dan iterasi berbasis data pengguna.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA banner — blue panel, serif accent + blue CTA (DESIGN.md: cta-banner) --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="relative isolate overflow-hidden rounded-[32px] border border-[#e3eaff] bg-[#f3f6ff] px-6 py-16 text-center sm:px-12 lg:py-20">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(70%_120%_at_100%_0%,#e3eaff,transparent)]"></div>
                <h2 class="mx-auto max-w-2xl font-serif text-[30px] font-normal leading-[1.2] tracking-[-0.5px] text-[#100f12] sm:text-[40px]">Butuh website atau <span class="text-gradient">app baru?</span></h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-7 text-[#65646e]">Ceritakan kebutuhan website, web app, atau WordPress Anda. Kami beri estimasi dan rekomendasi tanpa komitmen.</p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="#kontak" class="inline-flex w-full items-center justify-center rounded-[20px] bg-[#0a1589] px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto sm:text-[18px]">Hubungi Kami</a>
                    <a href="#layanan" class="inline-flex w-full items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff] sm:w-auto sm:text-[18px]">Pelajari layanan</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
