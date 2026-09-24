<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Katalog layanan publik — satu sumber untuk halaman /layanan dan landing page
 * wilayah.
 *
 * Halaman /layanan merender semuanya; halaman wilayah hanya butuh judul dan
 * ringkasannya supaya tidak ada dua daftar layanan yang bisa berbeda isi.
 * `points` ikut dibawa karena dipakai schema.org Service di halaman /layanan.
 */
final class ServiceCatalog
{
    /**
     * @return array<int, array{icon: string, title: string, short: string, desc: string, points: array<int, string>}>
     */
    public static function all(): array
    {
        return [
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
    }

    /**
     * Versi ringkas untuk kartu di halaman wilayah: hanya yang dibutuhkan kartu.
     *
     * @return array<int, array{title: string, short: string}>
     */
    public static function summary(): array
    {
        return array_map(
            fn (array $service): array => ['title' => $service['title'], 'short' => $service['short']],
            self::all(),
        );
    }
}
