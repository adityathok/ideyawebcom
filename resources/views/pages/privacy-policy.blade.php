<x-layouts.public :seo-meta="$seoMeta">
    @php
        $profile = $profile ?? \App\Models\Setting::profile();
        $company = ($profile['company_name'] ?? '') ?: config('app.name', 'IdeyaWeb');

        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $now = now();
        $lastUpdated = $now->day.' '.$months[(int) $now->month].' '.$now->year;

        $sections = [
            [
                'id' => 'pendahuluan',
                'title' => '1. Pendahuluan',
                'paragraphs' => [
                    'Kebijakan Privasi ini menjelaskan bagaimana '.$company.' ("kami") mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda ketika Anda mengakses situs web dan menggunakan layanan kami.',
                    'Dengan mengakses atau menggunakan layanan kami, Anda menyetujui praktik yang dijelaskan dalam kebijakan ini.',
                ],
            ],
            [
                'id' => 'informasi-yang-dikumpulkan',
                'title' => '2. Informasi yang Kami Kumpulkan',
                'paragraphs' => ['Kami dapat mengumpulkan beberapa jenis informasi berikut:'],
                'items' => [
                    'Informasi yang Anda berikan secara langsung — seperti nama, alamat email, nomor telepon, dan isi pesan saat mengisi formulir kontak.',
                    'Data teknis — seperti alamat IP, jenis perangkat, browser, halaman yang dikunjungi, dan waktu akses.',
                    'Cookies dan teknologi serupa yang membantu kami mengingat preferensi serta mengukur penggunaan situs.',
                ],
            ],
            [
                'id' => 'penggunaan-informasi',
                'title' => '3. Cara Kami Menggunakan Informasi',
                'paragraphs' => ['Informasi yang kami kumpulkan digunakan untuk:'],
                'items' => [
                    'Menanggapi pertanyaan, permintaan penawaran, dan komunikasi Anda.',
                    'Menyediakan, mengoperasikan, dan memelihara layanan kami.',
                    'Meningkatkan kualitas situs, konten, dan pengalaman pengguna.',
                    'Mengirim informasi terkait layanan, pembaruan, atau penawaran — yang dapat Anda hentikan kapan saja.',
                    'Memenuhi kewajiban hukum yang berlaku.',
                ],
            ],
            [
                'id' => 'cookies',
                'title' => '4. Cookies & Teknologi Serupa',
                'paragraphs' => [
                    'Situs kami menggunakan cookies untuk memahami bagaimana pengunjung menggunakan situs, mengingat preferensi, dan meningkatkan layanan. Cookies adalah file kecil yang disimpan pada perangkat Anda.',
                    'Anda dapat mengatur browser untuk menolak cookies. Namun, beberapa bagian situs mungkin tidak berfungsi optimal jika cookies dinonaktifkan.',
                ],
            ],
            [
                'id' => 'berbagi-informasi',
                'title' => '5. Berbagi Informasi dengan Pihak Ketiga',
                'paragraphs' => ['Kami tidak menjual atau menyewakan data pribadi Anda. Kami hanya membagikan informasi dalam situasi berikut:'],
                'items' => [
                    'Kepada penyedia layanan yang membantu operasional kami, seperti hosting, penyedia email, dan alat analitik.',
                    'Apabila diwajibkan oleh hukum, peraturan, atau proses hukum yang berlaku.',
                    'Dengan persetujuan Anda yang jelas.',
                ],
            ],
            [
                'id' => 'keamanan-data',
                'title' => '6. Penyimpanan & Keamanan Data',
                'paragraphs' => [
                    'Kami menyimpan data pribadi hanya selama diperlukan untuk memenuhi tujuan yang dijelaskan dalam kebijakan ini atau untuk memenuhi kewajiban hukum.',
                    'Kami menerapkan langkah keamanan teknis dan organisasi yang wajar untuk melindungi data Anda. Meskipun demikian, tidak ada metode transmisi data melalui internet yang sepenuhnya aman.',
                ],
            ],
            [
                'id' => 'hak-anda',
                'title' => '7. Hak Anda',
                'paragraphs' => ['Sehubungan dengan data pribadi Anda, Anda berhak untuk:'],
                'items' => [
                    'Mengakses dan memperoleh salinan data pribadi yang kami simpan.',
                    'Meminta perbaikan atas data yang tidak akurat atau tidak lengkap.',
                    'Meminta penghapusan data pribadi Anda.',
                    'Membatasi atau menolak pemrosesan data tertentu.',
                    'Menarik persetujuan yang sebelumnya Anda berikan.',
                ],
            ],
            [
                'id' => 'tautan-pihak-ketiga',
                'title' => '8. Tautan ke Situs Lain',
                'paragraphs' => [
                    'Situs kami dapat memuat tautan ke situs pihak ketiga. Kami tidak bertanggung jawab atas praktik privasi atau konten situs tersebut. Kami menyarankan Anda membaca kebijakan privasi masing-masing situs yang Anda kunjungi.',
                ],
            ],
            [
                'id' => 'perubahan-kebijakan',
                'title' => '9. Perubahan Kebijakan',
                'paragraphs' => [
                    'Kami dapat memperbarui kebijakan ini dari waktu ke waktu. Setiap perubahan akan dipublikasikan di halaman ini beserta tanggal pembaruannya. Kami menyarankan Anda meninjau halaman ini secara berkala.',
                ],
            ],
        ];
    @endphp

    {{-- Hero --}}
    <x-page-hero breadcrumb="Kebijakan Privasi">
        <x-slot:title>Kebijakan <span class="text-gradient-dark">Privasi</span></x-slot:title>
        <x-slot:subtitle>Terakhir diperbarui: {{ $lastUpdated }}</x-slot:subtitle>
    </x-page-hero>

    {{-- Isi --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="grid gap-10 lg:grid-cols-12">
                {{-- Konten --}}
                <div class="lg:col-span-8">
                    <div class="space-y-10">
                        @foreach($sections as $section)
                            <section id="{{ $section['id'] }}" class="scroll-mt-24">
                                <h2 class="text-[22px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12] sm:text-[24px]">{{ $section['title'] }}</h2>
                                @foreach($section['paragraphs'] as $paragraph)
                                    <p class="mt-3 text-[16px] leading-7 text-[#65646e]">{{ $paragraph }}</p>
                                @endforeach
                                @if(!empty($section['items']))
                                    <ul class="mt-4 space-y-2.5">
                                        @foreach($section['items'] as $item)
                                            <li class="flex items-start gap-3 text-[16px] leading-7 text-[#65646e]">
                                                <span aria-hidden="true" class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[#0a1589]"></span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </section>
                        @endforeach

                        <section id="hubungi-kami" class="scroll-mt-24">
                            <h2 class="text-[22px] font-semibold leading-tight tracking-[-0.16px] text-[#100f12] sm:text-[24px]">10. Hubungi Kami</h2>
                            <p class="mt-3 text-[16px] leading-7 text-[#65646e]">
                                Jika Anda memiliki pertanyaan tentang kebijakan privasi ini atau ingin menggunakan hak Anda terkait data pribadi, silakan hubungi kami melalui
                                <a href="{{ route('kontak') }}" class="font-medium text-[#0a1589] underline underline-offset-2 hover:text-[#06105a]">halaman kontak</a>
                                @if(!empty($profile['email']))
                                    atau email ke <a href="mailto:{{ $profile['email'] }}" class="font-medium text-[#0a1589] underline underline-offset-2 hover:text-[#06105a]">{{ $profile['email'] }}</a>
                                @endif
                                .
                            </p>
                        </section>
                    </div>
                </div>

                {{-- Daftar isi --}}
                <aside class="hidden lg:col-span-4 lg:block">
                    <nav aria-label="Daftar isi" class="sticky top-24 rounded-[20px] border border-[#e3eaff] bg-[#fafbff] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.5px] text-[#aaa9ae]">Daftar isi</p>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            @foreach($sections as $section)
                                <li><a href="#{{ $section['id'] }}" class="text-[#65646e] transition hover:text-[#0a1589]">{{ $section['title'] }}</a></li>
                            @endforeach
                            <li><a href="#hubungi-kami" class="text-[#65646e] transition hover:text-[#0a1589]">10. Hubungi Kami</a></li>
                        </ul>
                    </nav>
                </aside>
            </div>
        </div>
    </section>
</x-layouts.public>
