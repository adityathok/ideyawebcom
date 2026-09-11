<x-layouts.public :seo-meta="$seoMeta">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $company = ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb');

        $phoneRaw = (string) ($profile['phone'] ?? '');
        $waNumber = preg_replace('/[^0-9]/', '', $phoneRaw) ?? '';
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62'.substr($waNumber, 1);
        }

        $social = array_filter([
            'facebook' => $profile['facebook'] ?? null,
            'instagram' => $profile['instagram'] ?? null,
            'twitter' => $profile['twitter'] ?? null,
            'linkedin' => $profile['linkedin'] ?? null,
        ]);

        $inputClass = 'w-full rounded-[12px] border border-[#d3cec6] bg-white px-4 py-3 text-sm text-[#100f12] placeholder:text-[#9c9fa5] focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]';
        $labelClass = 'mb-1.5 block text-sm font-medium text-[#100f12]';
    @endphp

    {{-- Hero --}}
    <section class="relative isolate overflow-hidden bg-[#0a1589]">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-[radial-gradient(80%_70%_at_50%_0%,rgba(43,75,255,0.35),transparent_70%)]"></div>
            <div class="absolute inset-x-0 bottom-0 h-32 bg-[linear-gradient(180deg,transparent_0%,rgba(255,255,255,0.45)_55%,#ffffff_100%)]"></div>
        </div>
        <div class="mx-auto max-w-4xl px-4 pb-28 pt-32 text-center sm:px-6 sm:pb-32 sm:pt-40 lg:px-8">
            <nav aria-label="Breadcrumb" class="flex items-center justify-center gap-2 text-sm text-white/70">
                <a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-white">Kontak Kami</span>
            </nav>
            <span class="mt-6 inline-flex items-center gap-2 rounded-[16px] bg-white/10 px-4 py-2 text-sm font-medium tracking-[-0.16px] text-white ring-1 ring-inset ring-white/25">
                <span aria-hidden="true" class="size-1.5 rounded-full bg-[#7d95ff]"></span>
                Konsultasi Gratis
            </span>
            <h1 class="mx-auto mt-6 max-w-3xl text-[36px] font-medium leading-[1.08] tracking-[-1.1px] text-white sm:text-[48px] sm:tracking-[-1.4px] lg:text-[56px] lg:leading-[1.05]">
                Mari bicara tentang <span class="text-gradient-dark">proyek Anda</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-[17px] leading-8 tracking-[-0.16px] text-white/80 sm:text-[18px]">
                Ceritakan kebutuhan website, web app, atau WordPress Anda. Kami balas dengan rekomendasi dan estimasi tanpa komitmen.
            </p>
        </div>
    </section>

    {{-- Form + info kontak --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="grid gap-8 lg:grid-cols-12">
                {{-- Formulir --}}
                <div class="lg:col-span-7">
                    <div class="rounded-[24px] border border-[#e3eaff] bg-[#fafbff] p-6 sm:p-8">
                        <h2 class="text-[26px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12]">Kirim pesan</h2>
                        <p class="mt-2 text-sm leading-6 text-[#65646e]">Isi formulir di bawah ini, kami akan menghubungi Anda kembali dalam 1&times;24 jam kerja.</p>

                        @if (session('contact_status'))
                            <div role="status" class="mt-6 flex items-start gap-3 rounded-[12px] border border-[#bbf7d0] bg-[#f0fdf4] p-4 text-sm leading-6 text-[#166534]">
                                <span aria-hidden="true">✓</span>
                                <span>{{ session('contact_status') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div role="alert" class="mt-6 rounded-[12px] border border-[#fecaca] bg-[#fef2f2] p-4 text-sm leading-6 text-[#b91c1c]">
                                <p class="font-medium">Mohon periksa kembali data Anda:</p>
                                <ul class="mt-2 list-inside list-disc space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('kontak.send') }}" class="mt-6 grid gap-5">
                            @csrf

                            {{-- Honeypot (disembunyikan dari pengguna) --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="website">Website</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off" />
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="{{ $labelClass }}">Nama <span class="text-red-600">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Nama Anda" class="{{ $inputClass }}" />
                                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="email" class="{{ $labelClass }}">Email <span class="text-red-600">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" class="{{ $inputClass }}" />
                                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="phone" class="{{ $labelClass }}">No. Telepon / WhatsApp</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder="08xxxxxxxxxx" class="{{ $inputClass }}" />
                                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="subject" class="{{ $labelClass }}">Subjek <span class="text-red-600">*</span></label>
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="Contoh: Pembuatan website company profile" class="{{ $inputClass }}" />
                                    @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label for="message" class="{{ $labelClass }}">Pesan <span class="text-red-600">*</span></label>
                                <textarea id="message" name="message" rows="5" required placeholder="Ceritakan kebutuhan, target waktu, dan anggaran Anda…" class="{{ $inputClass }}">{{ old('message') }}</textarea>
                                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-[14px] bg-[#0a1589] px-8 py-4 text-[16px] font-medium leading-none text-white transition hover:bg-[#06105a] sm:w-auto">Kirim Pesan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info kontak --}}
                <aside class="space-y-4 lg:col-span-5">
                    <div class="rounded-[20px] border border-[#e3eaff] bg-white p-6">
                        <h2 class="text-[18px] font-semibold tracking-[-0.16px] text-[#100f12]">Informasi kontak</h2>
                        <ul class="mt-5 space-y-5 text-sm">
                            @if(!empty($profile['email']))
                                <li class="flex items-start gap-3">
                                    <span aria-hidden="true" class="flex size-10 shrink-0 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                                    </span>
                                    <span>
                                        <span class="block text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Email</span>
                                        <a href="mailto:{{ $profile['email'] }}" class="mt-0.5 block font-medium text-[#100f12] hover:text-[#0a1589] hover:underline">{{ $profile['email'] }}</a>
                                    </span>
                                </li>
                            @endif

                            @if(!empty($phoneRaw))
                                <li class="flex items-start gap-3">
                                    <span aria-hidden="true" class="flex size-10 shrink-0 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    </span>
                                    <span>
                                        <span class="block text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Telepon / WhatsApp</span>
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phoneRaw) }}" class="mt-0.5 block font-medium text-[#100f12] hover:text-[#0a1589] hover:underline">{{ $phoneRaw }}</a>
                                        @if($waNumber !== '')
                                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-[#25d366] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#1ebe5a]">
                                                <span aria-hidden="true">✆</span> Chat WhatsApp
                                            </a>
                                        @endif
                                    </span>
                                </li>
                            @endif

                            @if(!empty($profile['address']))
                                <li class="flex items-start gap-3">
                                    <span aria-hidden="true" class="flex size-10 shrink-0 items-center justify-center rounded-[12px] bg-[#f3f6ff] text-[#0a1589]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5" focusable="false"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    </span>
                                    <span>
                                        <span class="block text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Alamat</span>
                                        <span class="mt-0.5 block leading-6 text-[#100f12]">{{ $profile['address'] }}</span>
                                    </span>
                                </li>
                            @endif
                        </ul>

                        @if(!empty($social))
                            <div class="mt-6 border-t border-[#e3eaff] pt-5">
                                <span class="block text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Media sosial</span>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($social as $key => $url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full border border-[#e3eaff] bg-white px-4 py-2 text-sm font-medium text-[#100f12] transition hover:border-[#0a1589] hover:text-[#0a1589]">{{ ucfirst($key) }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-[20px] border border-[#e3eaff] bg-[#f3f6ff] p-6">
                        <h2 class="text-[18px] font-semibold tracking-[-0.16px] text-[#100f12]">Jam operasional</h2>
                        <ul class="mt-4 space-y-2 text-sm leading-6 text-[#65646e]">
                            <li class="flex items-center justify-between gap-4"><span>Senin – Jumat</span><span class="font-medium text-[#100f12]">09.00 – 18.00 WIB</span></li>
                            <li class="flex items-center justify-between gap-4"><span>Sabtu</span><span class="font-medium text-[#100f12]">09.00 – 13.00 WIB</span></li>
                            <li class="flex items-center justify-between gap-4"><span>Minggu &amp; hari libur</span><span class="font-medium text-[#100f12]">Tutup</span></li>
                        </ul>
                        <p class="mt-4 text-xs leading-6 text-[#65646e]">Pesan yang masuk di luar jam kerja akan dibalas pada hari kerja berikutnya.</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-layouts.public>
