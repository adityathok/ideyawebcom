<x-layouts.public :seo-meta="$seoMeta">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $company = ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb');

        $services = [
            [
                'icon' => '◈',
                'title' => 'Web App & App Custom',
                'short' => 'Dashboard, sistem & aplikasi bisnis',
                'desc' => 'Spesialis membangun web app dan app custom — dashboard, sistem internal, dan aplikasi bisnis yang cepat, aman, dan mudah diskalakan. Dirancang sesuai alur kerja Anda, lengkap dengan testing dan dokumentasi sejak hari pertama.',
                'points' => ['Dashboard & sistem internal', 'Auth, roles & permission', 'API & integrasi siap pakai', 'Testing & dokumentasi'],
            ],
            [
                'icon' => '◎',
                'title' => 'Website Company Profile',
                'short' => 'Ringan & SEO-friendly',
                'desc' => 'Website bisnis yang ringan, cepat, dan mudah dikelola — fokus pada kecepatan, SEO teknis, dan konversi. Cocok untuk company profile, landing page, dan katalog.',
                'points' => ['Desain responsif & cepat', 'SEO teknis & meta rapi', 'Mudah dikelola / CMS ringan', 'Siap dihubungkan ke WhatsApp & formulir'],
            ],
            [
                'icon' => '⬡',
                'title' => 'WordPress Development',
                'short' => 'Theme, plugin & WooCommerce',
                'desc' => 'Berpengalaman mengerjakan WordPress — custom theme, custom plugin, Elementor/Gutenberg, hingga WooCommerce. Rapi, aman, dan tidak berat.',
                'points' => ['Custom theme & child theme', 'Custom plugin & Gutenberg block', 'Elementor / Gutenberg yang rapi', 'WooCommerce & payment gateway'],
            ],
            [
                'icon' => '↗',
                'title' => 'Optimasi & Percepatan WordPress',
                'short' => 'Core Web Vitals lebih hijau',
                'desc' => 'Audit menyeluruh untuk WordPress yang lambat atau bermasalah — performa, keamanan, dan SEO teknis. Hasil terukur, sebelum vs sesudah.',
                'points' => ['Audit kecepatan & Core Web Vitals', 'Hardening keamanan & cleanup', 'Optimasi database & caching', 'SEO teknis & struktur data'],
            ],
            [
                'icon' => '✦',
                'title' => 'API & Integrasi Web',
                'short' => 'Payment & pihak ketiga',
                'desc' => 'REST API, payment gateway, dan integrasi layanan pihak ketiga untuk website maupun web app — sinkronisasi data yang andal.',
                'points' => ['REST API & webhook', 'Payment gateway (Midtrans, Xendit, dll)', 'Integrasi ERP / CRM / spreadsheet', 'Dokumentasi API yang jelas'],
            ],
            [
                'icon' => '☰',
                'title' => 'Maintenance Website',
                'short' => 'Update, backup & support',
                'desc' => 'Update, backup, monitoring, dan support rutin khusus website & web app — termasuk WordPress. Website tetap aman tanpa Anda repot.',
                'points' => ['Update rutin & backup', 'Monitoring uptime & error', 'Perbaikan bug prioritas', 'Laporan berkala yang jelas'],
            ],
        ];

        $process = [
            ['step' => '01', 'title' => 'Diskusi & Discovery', 'desc' => 'Gali tujuan, audiens, dan batasan. Output: scope & estimasi jelas.'],
            ['step' => '02', 'title' => 'Desain & Prototipe', 'desc' => 'Wireframe → UI → prototipe interaktif untuk validasi cepat.'],
            ['step' => '03', 'title' => 'Develop & QA', 'desc' => 'Build iteratif, code review, dan testing sebelum rilis.'],
            ['step' => '04', 'title' => 'Launch & Scale', 'desc' => 'Deploy, monitoring, dan iterasi berbasis data pengguna.'],
        ];
    @endphp

    {{-- Hero — hero gelap agar nav terang (lihat layouts.public: darkHero) --}}
    <section class="relative isolate overflow-hidden bg-[#0a1589]">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-[radial-gradient(80%_70%_at_50%_0%,rgba(43,75,255,0.35),transparent_70%)]"></div>
            <div class="absolute inset-x-0 bottom-0 h-32 bg-[linear-gradient(180deg,transparent_0%,rgba(255,255,255,0.45)_55%,#ffffff_100%)]"></div>
        </div>
        <div class="mx-auto max-w-4xl px-4 pb-28 pt-32 text-center sm:px-6 sm:pb-32 sm:pt-40 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex items-center justify-center gap-2 text-sm text-white/70">
                <a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-white">Layanan</span>
            </nav>
            <span class="mt-6 inline-flex items-center gap-2 rounded-[16px] bg-white/10 px-4 py-2 text-sm font-medium tracking-[-0.16px] text-white ring-1 ring-inset ring-white/25">
                <span aria-hidden="true" class="size-1.5 rounded-full bg-[#7d95ff]"></span>
                Layanan {{ $company }}
            </span>
            <h1 class="mx-auto mt-6 max-w-3xl text-[36px] font-medium leading-[1.08] tracking-[-1.1px] text-white sm:text-[48px] sm:tracking-[-1.4px] lg:text-[56px] lg:leading-[1.05]">
                Solusi website &amp; web app <span class="text-gradient-dark">dari ide sampai scale</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-[17px] leading-8 tracking-[-0.16px] text-white/80 sm:text-[18px]">
                Kami membangun website, aplikasi, dan integrasi yang cepat, aman, dan mudah dikembangkan — dari company profile hingga web app custom dan WordPress.
            </p>
            <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#0a1589] transition hover:bg-[#e3eaff] sm:w-auto sm:text-[18px]">Konsultasi Gratis</a>
                <a href="#daftar-layanan" class="inline-flex w-full items-center justify-center rounded-[20px] border border-white/30 bg-white/10 px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-white/20 sm:w-auto sm:text-[18px]">Lihat Layanan</a>
            </div>
        </div>
    </section>

    {{-- Daftar layanan --}}
    <section id="daftar-layanan" class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Layanan</p>
                <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Apa yang kami kerjakan</h2>
                <p class="mt-4 text-[18px] leading-7 tracking-[-0.16px] text-[#65646e]">Kami berpengalaman bertahun-tahun membangun, merawat, dan memelihara website profesional — termasuk web app dashboard, SaaS, dan aplikasi internal untuk berbagai kebutuhan.</p>
            </div>

            <div class="mt-12 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($services as $i => $service)
                    <article class="flex h-full flex-col rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_2px_5px_#0a158933]">
                        <div class="flex items-center gap-4">
                            <span aria-hidden="true" class="flex size-12 shrink-0 items-center justify-center rounded-[14px] bg-[#f3f6ff] text-xl text-[#0a1589]">{{ $service['icon'] }}</span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Layanan 0{{ $i + 1 }}</p>
                                <h3 class="mt-1 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $service['title'] }}</h3>
                            </div>
                        </div>
                        <p class="mt-2 text-sm font-medium text-[#0a1589]">{{ $service['short'] }}</p>
                        <p class="mt-3 text-sm leading-6 text-[#65646e]">{{ $service['desc'] }}</p>
                        <ul class="mt-5 grid gap-2">
                            @foreach($service['points'] as $point)
                                <li class="flex items-start gap-2 text-sm leading-6 text-[#100f12]">
                                    <span aria-hidden="true" class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-[#f3f6ff] text-xs text-[#0a1589]">✓</span>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-6 pt-1">
                            <a href="{{ route('kontak') }}" class="inline-flex items-center justify-center rounded-[14px] bg-[#0a1589] px-5 py-3 text-sm font-medium leading-none text-white transition hover:bg-[#06105a]">Konsultasikan kebutuhan ini</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Proses --}}
    <section id="proses" class="bg-[#f3f6ff]">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">Cara kerja</p>
                    <h2 class="mt-3 text-[32px] font-medium leading-[1.15] tracking-[-0.8px] text-[#100f12] sm:text-[40px]">Proses yang sederhana &amp; terukur</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-[#65646e]">Transparan dari discovery hingga launch — Anda tahu apa yang dikerjakan dan kapan selesai.</p>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($process as $step)
                    <div class="rounded-[16px] border border-[#e3eaff] bg-white p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#0a1589]">{{ $step['step'] }}</p>
                        <h3 class="mt-3 text-[20px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-[#65646e]">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="relative isolate overflow-hidden rounded-[32px] border border-[#e3eaff] bg-[#f3f6ff] px-6 py-16 text-center sm:px-12 lg:py-20">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(70%_120%_at_100%_0%,#e3eaff,transparent)]"></div>
                <h2 class="mx-auto max-w-2xl font-serif text-[30px] font-normal leading-[1.2] tracking-[-0.5px] text-[#100f12] sm:text-[40px]">Belum yakin layanan mana yang <span class="text-gradient">Anda butuhkan?</span></h2>
                <p class="mx-auto mt-4 max-w-xl text-base leading-7 text-[#65646e]">Ceritakan kebutuhan Anda, kami bantu petakan solusi dan estimasinya tanpa komitmen.</p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('kontak') }}" class="inline-flex w-full items-center justify-center rounded-[20px] bg-[#0a1589] px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto sm:text-[18px]">Hubungi Kami</a>
                    <a href="{{ route('blog.index') }}" class="inline-flex w-full items-center justify-center rounded-[20px] border border-[#e3eaff] bg-white px-8 pb-4 pt-[18px] text-[16px] font-medium leading-none text-[#100f12] transition hover:bg-[#fafbff] sm:w-auto sm:text-[18px]">Baca artikel</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
