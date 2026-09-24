<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Setting;
use App\Services\MetaService;
use App\Services\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

final class PageController extends Controller
{
    public function services(MetaService $meta): View
    {
        $profile = Setting::profile();
        $services = $this->serviceCatalog();

        $seoMeta = $meta->set([
            'title' => 'Layanan',
            'description' => 'Layanan pembuatan website, web app custom, WordPress, integrasi API, dan maintenance — dikerjakan rapi, cepat, aman, dan terukur.',
            'image' => $this->pageOgImage('layanan'),
            'type' => 'website',
            'url' => route('layanan'),
            'services' => $services,
            'breadcrumbs' => [['name' => 'Layanan']],
        ])->generate();

        return view('pages.layanan', compact('profile', 'seoMeta', 'services'));
    }

    public function contact(MetaService $meta): View
    {
        $profile = Setting::profile();
        $company = ($profile['company_name'] ?? '') ?: (string) config('app.name', 'IdeyaWeb');

        $seoMeta = $meta->set([
            'title' => 'Kontak Kami',
            'description' => 'Hubungi '.$company.' untuk konsultasi gratis seputar pembuatan website, web app custom, WordPress, dan maintenance.',
            'image' => $this->pageOgImage('kontak'),
            'type' => 'website',
            'url' => route('kontak'),
            'breadcrumbs' => [['name' => 'Kontak Kami']],
        ])->generate();

        return view('pages.kontak-kami', compact('profile', 'seoMeta'));
    }

    public function privacy(MetaService $meta): View
    {
        $profile = Setting::profile();

        $seoMeta = $meta->set([
            'title' => 'Kebijakan Privasi',
            'description' => 'Kebijakan privasi menjelaskan bagaimana kami mengumpulkan, menggunakan, melindungi, dan menghapus data pribadi Anda.',
            'image' => $this->pageOgImage('privacy'),
            'type' => 'website',
            'url' => route('privacy'),
            'breadcrumbs' => [['name' => 'Kebijakan Privasi']],
        ])->generate();

        return view('pages.privacy-policy', compact('profile', 'seoMeta'));
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Honeypot: bot umumnya mengisi field tersembunyi ini, jadi dianggap sukses tanpa mengirim.
        if ($request->filled('website')) {
            return redirect()->route('kontak')->with('contact_status', 'Terima kasih! Pesan Anda sudah kami terima.');
        }

        $recipient = (Setting::profile()['email'] ?? '') ?: (string) config('mail.from.address');
        $phone = $request->string('phone')->toString();

        Mail::to($recipient)->send(new ContactMessage(
            senderName: $request->string('name')->toString(),
            senderEmail: $request->string('email')->toString(),
            senderPhone: $phone !== '' ? $phone : null,
            subjectLine: $request->string('subject')->toString(),
            messageBody: $request->string('message')->toString(),
        ));

        return redirect()
            ->route('kontak')
            ->with('contact_status', 'Terima kasih! Pesan Anda sudah kami terima dan akan segera dibalas.');
    }

    /**
     * OG image khusus halaman: public/images/og-{slug}.{ext}.
     * Null kalau belum ada → MetaService memakai default dari pengaturan seo_og_image.
     */
    private function pageOgImage(string $slug): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            $relative = "images/og-{$slug}.{$extension}";

            if (is_file(public_path($relative))) {
                return asset($relative);
            }
        }

        return null;
    }

    /**
     * Daftar layanan halaman /layanan — sumbernya `ServiceCatalog` supaya view
     * dan schema.org Service tidak bisa berbeda isi, dan halaman wilayah bisa
     * memakai daftar yang sama.
     *
     * @return array<int, array{icon: string, title: string, short: string, desc: string, points: array<int, string>}>
     */
    private function serviceCatalog(): array
    {
        return ServiceCatalog::all();
    }
}
