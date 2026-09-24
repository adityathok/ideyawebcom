<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LpKota;
use App\Models\Setting;
use App\Services\MetaService;
use App\Services\ServiceCatalog;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Halaman publik landing page per wilayah: daftar kota, halaman satu kota, dan
 * halaman satu kecamatan.
 *
 * URL memakai slug turunan dari nama kota/kecamatan, sedangkan binding route
 * memakai `{lpKota}` (id) supaya rute admin yang sudah ada tidak berubah. Nama
 * yang tidak menghasilkan slug — misalnya "Kec. 12" — sengaja menghasilkan 404,
 * bukan mencocokkan baris sembarangan.
 */
final class LpKotaController extends Controller
{
    public function index(MetaService $meta): View
    {
        $wilayah = LpKota::query()
            ->with('media')
            ->orderBy('nama_kota')
            ->orderBy('nama_kecamatan')
            ->get();

        $kota = $wilayah
            ->groupBy('nama_kota')
            ->map(fn ($baris, string $nama): array => [
                'nama' => $nama,
                'slug' => Str::slug($nama),
                'kecamatan' => $baris->pluck('nama_kecamatan')->unique()->values()->all(),
                'gambar' => $baris->first()->gambarUtamaUrl(),
            ])
            ->sortKeys()
            ->values();

        $seoMeta = $meta->set([
            'title' => 'Wilayah Layanan',
            'description' => 'Kami melayani jasa pembuatan website, web app custom, dan WordPress untuk '
                .$kota->count().' kota/kabupaten. Lihat daftar wilayah dan kecamatan yang kami jangkau.',
            'type' => 'website',
            'url' => route('lp.index'),
            'breadcrumbs' => [['name' => 'Wilayah Layanan']],
        ])->generate();

        return view('pages.lp-kota.index', compact('kota', 'seoMeta'));
    }

    public function kota(string $kota, MetaService $meta): View
    {
        // Kota dicari dari baris pertama, bukan dari daftar distinct, supaya
        // slug kota yang tidak punya baris apa pun tetap berakhir 404.
        $wilayah = $this->wilayahKota($kota);

        if ($wilayah->isEmpty()) {
            throw new NotFoundHttpException;
        }

        $namaKota = (string) $wilayah->first()->nama_kota;
        $kotaSlug = $wilayah->first()->slugKota();
        $kecamatan = $wilayah->sortBy('nama_kecamatan')->values();

        $seoMeta = $meta->forLpKota($namaKota, $wilayah, [
            'url' => route('lp.kota', ['kota' => $kota]),
            'breadcrumbs' => [
                ['name' => 'Wilayah Layanan', 'url' => route('lp.index')],
                ['name' => $namaKota],
            ],
        ])->generate();

        return view('pages.lp-kota.kota', compact('namaKota', 'kotaSlug', 'kecamatan', 'seoMeta'));
    }

    public function kecamatan(string $kota, string $kecamatan, MetaService $meta): View
    {
        $wilayah = $this->wilayahKota($kota);

        if ($wilayah->isEmpty()) {
            throw new NotFoundHttpException;
        }

        $namaKota = (string) $wilayah->first()->nama_kota;
        $kotaSlug = $wilayah->first()->slugKota();
        $baris = $wilayah->first(fn (LpKota $item): bool => $item->slugKecamatan() === $kecamatan);

        if ($baris === null) {
            throw new NotFoundHttpException;
        }

        $kecamatanLain = $wilayah
            ->reject(fn (LpKota $item): bool => $item->id === $baris->id)
            ->sortBy('nama_kecamatan')
            ->values();

        $judul = $baris->nama_kecamatan.', '.$namaKota;
        $profil = Setting::profile();
        $whatsapp = $this->whatsappUrl($profil['phone'] ?? null, 'Halo, saya butuh jasa website untuk wilayah '.$baris->labelWilayah().'.');

        $services = ServiceCatalog::summary();

        $seoMeta = $meta->forLpKota($judul, collect([$baris]), [
            'url' => route('lp.kecamatan', ['kota' => $kota, 'kecamatan' => $kecamatan]),
            'services' => $services,
            'breadcrumbs' => [
                ['name' => 'Wilayah Layanan', 'url' => route('lp.index')],
                ['name' => $namaKota, 'url' => route('lp.kota', ['kota' => $kota])],
                ['name' => $baris->nama_kecamatan],
            ],
        ])->generate();

        return view('pages.lp-kota.kecamatan', compact('baris', 'namaKota', 'kotaSlug', 'kecamatanLain', 'whatsapp', 'seoMeta', 'services'));
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
