<?php

use App\Mail\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

test('renders the layanan page', function () {
    $this->get(route('layanan'))
        ->assertOk()
        ->assertSee('Solusi website &amp; web app', false)
        ->assertSee('Web App &amp; App Custom', false)
        ->assertSee('WordPress Development', false);
});

test('renders the shared page hero with a rich heading on inner pages', function () {
    $this->get(route('layanan'))
        ->assertOk()
        ->assertSee('aria-label="Breadcrumb"', false)
        ->assertSee('<span class="text-gradient-dark">dari ide sampai scale</span>', false)
        ->assertSee('page-hero-sky.jpg')
        ->assertSee('Lihat Layanan');

    $this->get(route('privacy'))
        ->assertOk()
        ->assertSee('<span class="text-gradient-dark">Privasi</span>', false);
});

test('renders page specific seo meta through the layout', function () {
    $this->get(route('layanan'))
        ->assertOk()
        ->assertSee('Layanan pembuatan website, web app custom, WordPress', false);
});

test('renders the kontak page with a working contact form', function () {
    $this->get(route('kontak'))
        ->assertOk()
        ->assertSee('Kirim pesan')
        ->assertSee(route('kontak.send'), false)
        ->assertSee('name="website"', false);
});

test('renders the privacy policy page', function () {
    $this->get(route('privacy'))
        ->assertOk()
        ->assertSee('Kebijakan')
        ->assertSee('Pendahuluan')
        ->assertSee('Hubungi Kami');
});

test('validates the contact form submission', function () {
    Mail::fake();

    $this->post(route('kontak.send'), [])
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

    Mail::assertNothingSent();
});

test('sends the contact form submission by email', function () {
    Mail::fake();
    Setting::set('email', 'halo@example.com');

    $response = $this->post(route('kontak.send'), [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '08123456789',
        'subject' => 'Pembuatan website company profile',
        'message' => 'Halo, saya ingin membuat website company profile.',
    ]);

    $response->assertRedirect(route('kontak'));
    $response->assertSessionHas('contact_status');

    Mail::assertSent(ContactMessage::class, function (ContactMessage $mail): bool {
        return $mail->hasTo('halo@example.com')
            && $mail->senderEmail === 'budi@example.com'
            && $mail->senderPhone === '08123456789';
    });
});

test('discards contact submissions that fill the honeypot', function () {
    Mail::fake();

    $response = $this->post(route('kontak.send'), [
        'name' => 'Spammer',
        'email' => 'spam@example.com',
        'subject' => 'Promo',
        'message' => 'Beli produk kami.',
        'website' => 'https://spam.example',
    ]);

    $response->assertRedirect(route('kontak'));

    Mail::assertNothingSent();
});

test('renders the contact message email view', function () {
    $html = view('mail.contact-message', [
        'senderName' => 'Budi Santoso',
        'senderEmail' => 'budi@example.com',
        'senderPhone' => '08123456789',
        'subjectLine' => 'Pembuatan website',
        'messageBody' => 'Halo, saya ingin membuat website.',
    ])->render();

    expect($html)
        ->toContain('Budi Santoso')
        ->toContain('budi@example.com')
        ->toContain('Halo, saya ingin membuat website.');
});
