<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LpKota;
use App\Models\Setting;
use App\Services\MetaService;
use App\Services\ServiceCatalog;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Halaman publik landing page per kota: `/lp-layanan-kota/{kota}`.
 *
 * Hanya ada satu level halaman — kecamatan tidak punya URL sendiri, melainkan
 * tampil sebagai konten di halaman kotanya. Jadi tidak ada halaman daftar
 * wilayah; halaman ini dituju langsung (iklan/SEO) dan didaftarkan di sitemap.
 *
 * URL memakai slug turunan dari nama kota. Nama yang tidak menghasilkan slug —
 * misalnya "Kota 12" — sengaja berakhir 404, bukan mencocokkan baris sembarangan.
 */
final class LpKotaController extends Controller
{
    public function kota(string $kota, MetaService $meta): View
    {
        // Kota dicari lewat daftar barisnya, bukan dari daftar distinct, supaya
        // slug yang tidak punya baris apa pun tetap berakhir 404.
        $wilayah = $this->wilayahKota($kota);

        if ($wilayah->isEmpty()) {
            throw new NotFoundHttpException;
        }

        $namaKota = (string) $wilayah->first()->nama_kota;
        $kecamatan = $wilayah->sortBy('nama_kecamatan')->values();

        $profil = Setting::profile();
        $services = ServiceCatalog::summary();

        // Satu tombol WhatsApp per kecamatan, supaya pesan yang masuk sudah
        // menyebut wilayah yang dibaca pengunjung.
        $whatsapp = [];
        foreach ($kecamatan as $item) {
            $whatsapp[$item->id] = $this->whatsappUrl(
                $profil['phone'] ?? null,
                'Halo, saya butuh jasa website untuk wilayah '.$item->labelWilayah().'.',
            );
        }

        $seoMeta = $meta->forLpKota($namaKota, $wilayah, [
            'url' => route('lp.kota', ['kota' => $kota]),
            'services' => $services,
            'breadcrumbs' => [['name' => $namaKota]],
        ])->generate();

        return view('pages.lp-kota.kota', compact('namaKota', 'kecamatan', 'whatsapp', 'services', 'seoMeta'));
    }

    /**
     * Semua baris wilayah satu kota, lengkap dengan medianya (butuh eager load
     * supaya daftar kecamatan tidak menembak satu query per baris).
     *
     * @return Collection<int, LpKota>
     */
    private function wilayahKota(string $slug): Collection
    {
        $wilayah = LpKota::query()
            ->with('media')
            ->where('nama_kota', $slug)
            ->orderBy('nama_kecamatan')
            ->get();

        if ($wilayah->isNotEmpty()) {
            return $wilayah;
        }

        // SQL tidak bisa membandingkan hasil Str::slug(), jadi slug dicocokkan
        // di sini; nama kota di situs ini hanya puluhan, bukan ribuan.
        return LpKota::query()
            ->with('media')
            ->orderBy('nama_kecamatan')
            ->get()
            ->filter(fn (LpKota $item): bool => $item->slugKota() === $slug)
            ->values();
    }

    /**
     * Nomor WhatsApp dalam format wa.me (62...), atau null kalau nomor belum diisi.
     * Aturannya sama dengan komponen `whatsapp-float` supaya tidak ada dua format.
     */
    private function whatsappUrl(?string $nomor, string $pesan): ?string
    {
        $digit = preg_replace('/\D/', '', (string) $nomor) ?? '';

        if ($digit === '') {
            return null;
        }

        if (str_starts_with($digit, '0')) {
            $digit = '62'.substr($digit, 1);
        } elseif (! str_starts_with($digit, '62')) {
            $digit = '62'.$digit;
        }

        return 'https://wa.me/'.$digit.'?text='.rawurlencode($pesan);
    }
}
