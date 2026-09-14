<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Disk Penyimpanan
    |--------------------------------------------------------------------------
    |
    | Disk tempat semua file media disimpan. Dimensi gambar dibaca langsung dari
    | disk (butuh driver lokal), jadi ganti nilai ini ke disk remote hanya kalau
    | pembacaan dimensi juga disesuaikan.
    |
    */

    'disk' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Direktori Induk
    |--------------------------------------------------------------------------
    |
    | File disimpan per bulan (`media/2026/09/...`) supaya satu folder tidak
    | menampung ribuan file sekaligus.
    |
    */

    'directory' => 'media',

    /*
    |--------------------------------------------------------------------------
    | Batas Ukuran File
    |--------------------------------------------------------------------------
    |
    | Dalam kilobyte, sama seperti aturan validasi `max:` di Laravel. PHP tetap
    | membatasi lebih dulu lewat `upload_max_filesize` dan `post_max_size`.
    |
    */

    'max_size_kb' => (int) env('MEDIA_MAX_SIZE_KB', 4096),

    /*
    |--------------------------------------------------------------------------
    | Tipe File yang Diizinkan
    |--------------------------------------------------------------------------
    |
    | Peta MIME → ekstensi. Validasi memakai aturan `mimetypes:` sehingga isi
    | file dibaca ulang, bukan hanya ekstensinya. SVG sengaja tidak didaftarkan:
    | berkas SVG bisa memuat script dan dilayani dari origin yang sama.
    |
    */

    'allowed_mimes' => [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/avif' => 'avif',
    ],

];
