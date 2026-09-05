<x-layouts.public :title="($profile['company_name'] ?? config('app.name', 'IdeyaWeb'))">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $heroSky = file_exists(public_path('images/hero-sky.jpg')) ? asset('images/hero-sky.jpg') : 'https://images.unsplash.com/photo-1570483358100-6d222cdea6ff?auto=format&fit=crop&w=2400&q=80';
    @endphp

    {{-- Hero Agency — foto langit + overlay lembut + aurora tipis + Motion (center: heading / subheading / description + 2 CTA) --}}
    <section data-aurora-hero class="relative isolate overflow-hidden bg-[#bae6fd]">
        {{-- Latar: foto langit biru (ganti src dengan /images/hero-sky.jpg bila punya asset sendiri) --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <img data-hero-sky src="{{ $heroSky }}" alt="" class="h-full w-full object-cover object-center" loading="eager" fetchpriority="high" />
            {{-- Wash agar teks ink #111111 tetap kontras di atas foto (DESIGN.md) --}}
            <div class="absolute inset-0 bg-gradient-to-b from-[#f0f9ff]/70 via-[#e0f2fe]/45 to-[#f5f1ec]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-white/20 via-transparent to-white/10"></div>
            {{-- Aurora tipis di atas foto — tetap di-animate via Motion --}}
            <div data-aurora-blob class="absolute left-[4%] top-[-10%] h-[520px] w-[680px] rounded-full opacity-35 blur-[80px] will-change-transform" style="background: radial-gradient(ellipse 70% 60% at 40% 35%, rgba(125,211,252,0.55) 0%, rgba(56,189,248,0.30) 36%, transparent 70%);"></div>
            <div data-aurora-blob class="absolute right-[2%] top-[6%] h-[480px] w-[600px] rounded-full opacity-25 blur-[90px] will-change-transform" style="background: radial-gradient(ellipse 75% 65% at 60% 40%, rgba(14,165,233,0.32) 0%, transparent 72%);"></div>
        </div>

        <div class="mx-auto flex min-h-[560px] max-w-3xl items-center justify-center px-4 py-24 text-center sm:min-h-[640px] sm:px-6 sm:py-32 lg:min-h-[760px] lg:py-40">
            <div class="w-full">
            <p data-hero-sub class="text-sm font-medium tracking-wide text-[#626260]">{{ $profile['tagline'] ?? 'Digital Agency & IT Solution' }}</p>
            <h1 data-hero-heading class="mx-auto mt-4 max-w-2xl text-4xl font-medium leading-[1.05] tracking-[-0.8px] text-[#111111] sm:text-5xl lg:text-[56px] lg:leading-[1.10] lg:tracking-[-1.4px]">
                {{ ($profile['company_name'] ?? 'IdeyaWeb') }}
            </h1>
            <p data-hero-sub class="mx-auto mt-3 max-w-2xl text-[20px] font-normal leading-7 tracking-[-0.2px] text-[#111111] sm:text-[22px]">
                Membangun produk digital yang cepat &amp; bermakna.
            </p>
            <p data-hero-desc class="mx-auto mt-6 max-w-2xl text-base leading-7 text-[#626260] sm:text-[18px] sm:leading-7">
                {{ !empty($profile['about']) ? \Illuminate\Support\Str::limit($profile['about'], 200) : 'Kami membantu bisnis bertumbuh lewat website, aplikasi, dan strategi digital yang tepat — dari ide, desain, hingga launch dan scale.' }}
            </p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <a data-hero-cta href="#kontak" class="rounded-lg bg-[#111111] px-[18px] py-[10px] text-[15px] font-medium leading-none text-white hover:bg-black">Konsultasi Gratis</a>
                <a data-hero-cta href="#layanan" class="rounded-lg border border-[#d3cec6] bg-white px-[18px] py-[10px] text-[15px] font-medium leading-none text-[#111111] hover:bg-[#ebe7e1]">Lihat Layanan</a>
            </div>
            </div>
        </div>
    </section>

    {{-- Layanan — feature-card grid 3-up (DESIGN.md feature-card) --}}
    <section id="layanan" class="border-y border-[#ebe7e1] bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-medium tracking-wide text-[#626260]">Layanan &amp; Jasa</p>
                <h2 class="mt-2 text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Satu tim untuk seluruh kebutuhan digital Anda</h2>
                <p class="mt-3 text-base leading-7 text-[#626260]">Dari website company profile hingga aplikasi kompleks — kami rancang, bangun, dan rawat produk Anda.</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">◈</div>
                    <h3 class="mt-4 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Website &amp; Aplikasi</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Laravel, Livewire, Next.js. Cepat, aman, dan mudah diskalakan. CMS ringan sesuai kebutuhan.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">◎</div>
                    <h3 class="mt-4 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Branding &amp; UI/UX</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Desain yang fokus pada konversi — design system, prototype, dan usability yang terukur.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">⬡</div>
                    <h3 class="mt-4 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">API &amp; Integrasi</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Payment gateway, ERP, dan layanan pihak ketiga — sinkronisasi data yang andal.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">↗</div>
                    <h3 class="mt-4 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">SEO &amp; Growth</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Audit teknis, konten, dan optimasi performa untuk akuisisi organik yang berkelanjutan.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">✦</div>
                    <h3 class="mt-4 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Maintenance &amp; Support</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Monitoring, backup, dan SLA. Produk tetap aman setelah launch.</p>
                </div>
                <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-[#f5f1ec] text-[#111111]">☰</div>
                    <h3 class="mt-4 text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Konsultasi Digital</h3>
                    <p class="mt-2 text-sm leading-6 text-[#626260]">Butuh peta jalan? Kami bantu audit dan susun roadmap prioritas yang realistis.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tentang / Profil — lift onto white cards --}}
    <section id="tentang" class="bg-[#f5f1ec]">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-start">
                <div>
                    <p class="text-sm font-medium tracking-wide text-[#626260]">Profil</p>
                    <h2 class="mt-2 text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Tentang {{ ($profile['company_name'] ?? '') ?: 'IdeyaWeb' }}</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-[#626260]">{{ $profile['about'] ?? 'IdeyaWeb adalah digital agency yang fokus pada pengembangan website dan aplikasi berkualitas — mengutamakan kecepatan, keamanan, dan pengalaman pengguna.' }}</p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-xl border border-[#d3cec6] bg-white p-5 text-center">
                            <p class="text-[28px] font-medium tracking-tight text-[#111111]">50+</p>
                            <p class="mt-1 text-sm text-[#626260]">Proyek selesai</p>
                        </div>
                        <div class="rounded-xl border border-[#d3cec6] bg-white p-5 text-center">
                            <p class="text-[28px] font-medium tracking-tight text-[#111111]">98%</p>
                            <p class="mt-1 text-sm text-[#626260]">Kepuasan klien</p>
                        </div>
                        <div class="rounded-xl border border-[#d3cec6] bg-white p-5 text-center">
                            <p class="text-[28px] font-medium tracking-tight text-[#111111]">5★</p>
                            <p class="mt-1 text-sm text-[#626260]">Rating layanan</p>
                        </div>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                        <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Fokus pada hasil</h3>
                        <p class="mt-2 text-sm leading-6 text-[#626260]">Setiap keputusan desain diukur terhadap tujuan bisnis Anda.</p>
                    </div>
                    <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                        <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Komunikasi terbuka</h3>
                        <p class="mt-2 text-sm leading-6 text-[#626260]">Demo rutin, laporan jelas, tanpa kejutan di akhir.</p>
                    </div>
                    <div class="rounded-xl border border-[#d3cec6] bg-white p-6">
                        <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Kualitas terjaga</h3>
                        <p class="mt-2 text-sm leading-6 text-[#626260]">Code review, testing, dan dokumentasi sejak hari pertama.</p>
                    </div>
                    <div class="rounded-xl border border-[#d3cec6] bg-[#ebe7e1] p-6">
                        <h3 class="text-[22px] font-medium leading-tight tracking-[-0.3px] text-[#111111]">Jangka panjang</h3>
                        <p class="mt-2 text-sm leading-6 text-[#626260]">Kami bangun fondasi yang mudah dirawat dan dikembangkan.</p>
                    </div>
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
                        <h2 class="text-[28px] font-medium leading-[1.2] tracking-[-0.5px] text-[#111111]">Siap wujudkan ide Anda?</h2>
                        <p class="mt-3 max-w-xl text-base leading-7 text-[#626260]">Ceritakan kebutuhan Anda — kami beri estimasi dan rekomendasi tanpa komitmen.</p>
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
