<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Setting;
use App\Services\MetaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

final class PageController extends Controller
{
    public function services(MetaService $meta): View
    {
        $profile = Setting::profile();

        $seoMeta = $meta->set([
            'title' => 'Layanan',
            'description' => 'Layanan pembuatan website, web app custom, WordPress, integrasi API, dan maintenance — dikerjakan rapi, cepat, aman, dan terukur.',
            'type' => 'website',
            'url' => route('layanan'),
        ])->generate();

        return view('pages.layanan', compact('profile', 'seoMeta'));
    }

    public function contact(MetaService $meta): View
    {
        $profile = Setting::profile();
        $company = ($profile['company_name'] ?? '') ?: (string) config('app.name', 'IdeyaWeb');

        $seoMeta = $meta->set([
            'title' => 'Kontak Kami',
            'description' => 'Hubungi '.$company.' untuk konsultasi gratis seputar pembuatan website, web app custom, WordPress, dan maintenance.',
            'type' => 'website',
            'url' => route('kontak'),
        ])->generate();

        return view('pages.kontak-kami', compact('profile', 'seoMeta'));
    }

    public function privacy(MetaService $meta): View
    {
        $profile = Setting::profile();

        $seoMeta = $meta->set([
            'title' => 'Kebijakan Privasi',
            'description' => 'Kebijakan privasi menjelaskan bagaimana kami mengumpulkan, menggunakan, melindungi, dan menghapus data pribadi Anda.',
            'type' => 'website',
            'url' => route('privacy'),
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
}
