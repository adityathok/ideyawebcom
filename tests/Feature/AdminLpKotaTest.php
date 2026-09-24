<?php

use App\Models\LpKota;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');

    $this->actingAs(User::factory()->create());
});

test('guests are redirected to the login page', function () {
    auth()->logout();

    $this->get(route('admin.lp-kota'))->assertRedirect(route('login'));
});

test('authenticated users can visit the lp kota page', function () {
    $this->get(route('admin.lp-kota'))
        ->assertOk()
        ->assertSee('LP Kota');
});

test('the list shows the city and its district', function () {
    LpKota::factory()->wilayah('Magetan', 'Plaosan')->create();

    // Nama kota sengaja tidak memakai contoh di placeholder form, supaya yang
    // benar-benar diuji adalah baris tabel, bukan teks placeholder.
    Livewire::test('pages::admin.lp-kota.index')
        ->assertSee('Magetan')
        ->assertSee('Plaosan');
});

test('creating a record stores city, district and description', function () {
    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->set('deskripsi', 'Wilayah industri kecil.')
        ->call('save')
        ->assertHasNoErrors();

    $lp = LpKota::sole();

    expect($lp->nama_kota)->toBe('Sukoharjo')
        ->and($lp->nama_kecamatan)->toBe('Weru')
        ->and($lp->deskripsi)->toBe('Wilayah industri kecil.');
});

test('city and district are required', function () {
    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->call('save')
        ->assertHasErrors(['nama_kota' => 'required', 'nama_kecamatan' => 'required']);

    expect(LpKota::count())->toBe(0);
});

test('the same district cannot be saved twice for one city', function () {
    LpKota::factory()->wilayah('Sukoharjo', 'Weru')->create();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->call('save')
        ->assertHasErrors(['nama_kecamatan' => 'unique']);

    expect(LpKota::count())->toBe(1);
});

test('the same district name is allowed in another city', function () {
    LpKota::factory()->wilayah('Sukoharjo', 'Weru')->create();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Karanganyar')
        ->set('nama_kecamatan', 'Weru')
        ->call('save')
        ->assertHasNoErrors();

    expect(LpKota::count())->toBe(2);
});

test('editing loads the existing values', function () {
    $lp = LpKota::factory()->wilayah('Sukoharjo', 'Weru')->create(['deskripsi' => 'Deskripsi awal']);

    Livewire::test('pages::admin.lp-kota.index')
        ->call('edit', $lp->id)
        ->assertSet('nama_kota', 'Sukoharjo')
        ->assertSet('nama_kecamatan', 'Weru')
        ->assertSet('deskripsi', 'Deskripsi awal');
});

test('an uploaded main image lands in the media library and is linked', function () {
    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->set('gambarUtamaUpload', uploadPng('utama.png', 8, 5))
        ->call('save')
        ->assertHasNoErrors();

    $lp = LpKota::sole();
    $media = Media::sole();

    expect($lp->gambarUtamaUrl())->toContain($media->path)
        ->and($lp->gambarUtamaMedia()->pluck('media.id')->all())->toBe([$media->id]);

    Storage::disk('public')->assertExists($media->path);
});

test('uploading an icon leaves the main image collection untouched', function () {
    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->set('gambarUtamaUpload', uploadPng('utama.png', 8, 5))
        ->call('save')
        ->assertHasNoErrors();

    $lp = LpKota::sole();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('edit', $lp->id)
        ->set('gambarIconUpload', uploadPng('ikon.png', 3, 3))
        ->call('save')
        ->assertHasNoErrors();

    $fresh = $lp->fresh();

    expect($fresh->gambarUtamaUrl())->not->toBeNull()
        ->and($fresh->gambarIconUrl())->not->toBeNull()
        ->and(Media::count())->toBe(2);
});

test('uploading the same file twice reuses one media record', function () {
    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->set('gambarUtamaUpload', uploadPng('sama.png', 4, 4))
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Klaten')
        ->set('nama_kecamatan', 'Prambanan')
        ->set('gambarUtamaUpload', uploadPng('sama-lagi.png', 4, 4))
        ->call('save')
        ->assertHasNoErrors();

    // Isi file identik → satu file di disk, dipakai dua record.
    expect(Media::count())->toBe(1)
        ->and(LpKota::count())->toBe(2);
});

test('an image can be picked from the media library', function () {
    $media = Media::factory()->withFile()->create(['original_name' => 'peta.png']);

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->call('openMediaPicker', 'utama')
        ->assertSet('pickerTarget', 'utama')
        ->call('selectMedia', $media->id)
        ->assertSet('gambarUtamaMediaId', $media->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(LpKota::sole()->gambarUtamaUrl())->toBe($media->url());
});

test('the icon picker fills the icon, not the main image', function () {
    $media = Media::factory()->withFile()->create();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->call('openMediaPicker', 'icon')
        ->assertSet('pickerTarget', 'icon')
        ->call('selectMedia', $media->id)
        ->assertSet('gambarIconMediaId', $media->id)
        ->assertSet('gambarUtamaMediaId', null);
});

test('removing an image detaches it without deleting the file', function () {
    $media = Media::factory()->withFile()->create();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->call('selectMedia', $media->id)
        ->call('save')
        ->assertHasNoErrors();

    $lp = LpKota::sole();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('edit', $lp->id)
        ->call('removeGambar', 'utama')
        ->assertSet('gambarUtamaMediaId', null)
        ->assertSet('gambarUtamaPreview', null);

    $fresh = $lp->fresh();

    expect($fresh->gambarUtamaUrl())->toBeNull()
        ->and($fresh->gambar_utama)->toBeNull();

    Storage::disk('public')->assertExists($media->path);
});

test('deleting a record keeps the media file in the library', function () {
    $media = Media::factory()->withFile()->create();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('create')
        ->set('nama_kota', 'Sukoharjo')
        ->set('nama_kecamatan', 'Weru')
        ->call('selectMedia', $media->id)
        ->call('save')
        ->assertHasNoErrors();

    $lp = LpKota::sole();

    Livewire::test('pages::admin.lp-kota.index')
        ->call('confirmDelete', $lp->id)
        ->assertSet('deletingLabel', 'Weru, Sukoharjo')
        ->call('delete');

    expect(LpKota::count())->toBe(0);

    Storage::disk('public')->assertExists($media->path);
});

test('search filters by city, district or description', function () {
    // Nama sengaja tidak bertabrakan dengan contoh di placeholder form
    // ("Sukoharjo"/"Weru"), supaya yang diuji benar-benar isi tabel.
    LpKota::factory()->wilayah('Magetan', 'Plaosan')->create();
    LpKota::factory()->wilayah('Ngawi', 'Sine')->create(['deskripsi' => 'Kawasan bengawan.']);

    Livewire::test('pages::admin.lp-kota.index')
        ->set('search', 'Magetan')
        ->assertSee('Magetan')
        ->assertDontSee('Ngawi');

    Livewire::test('pages::admin.lp-kota.index')
        ->set('search', 'Sine')
        ->assertSee('Ngawi')
        ->assertDontSee('Magetan');

    Livewire::test('pages::admin.lp-kota.index')
        ->set('search', 'Kawasan bengawan')
        ->assertSee('Ngawi')
        ->assertDontSee('Magetan');

    Livewire::test('pages::admin.lp-kota.index')
        ->set('search', 'tidak-ada-hasil')
        ->assertSee('Tidak ada data yang cocok');
});
