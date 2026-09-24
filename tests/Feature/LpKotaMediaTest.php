<?php

use App\Models\LpKota;
use App\Models\Media;

test('keeps both images when the same media is used for hero and icon', function () {
    $media = Media::factory()->withFile()->create();
    $lp = LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $lp->setGambarUtama($media);
    $lp->setGambarIcon($media);
    $lp->refresh();

    // Satu media boleh dipakai di dua koleksi; memilihnya sebagai ikon tidak
    // boleh menggeser baris `gambar_utama`.
    expect($lp->gambarUtamaUrl())->not->toBeNull()
        ->and($lp->gambarIconUrl())->not->toBeNull()
        ->and($lp->gambarUtamaUrl())->toBe($lp->gambarIconUrl());
});

test('is idempotent when the same media is attached to one collection twice', function () {
    $media = Media::factory()->withFile()->create();
    $lp = LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $lp->setGambarUtama($media);
    $lp->setGambarUtama($media);

    expect($lp->media()->wherePivot('collection', LpKota::COLLECTION_GAMBAR_UTAMA)->count())->toBe(1);
});

test('replacing the hero image leaves the icon untouched', function () {
    $hero = Media::factory()->withFile()->create();
    $ikon = Media::factory()->withFile()->create();
    $pengganti = Media::factory()->withFile()->create();
    $lp = LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $lp->setGambarUtama($hero);
    $lp->setGambarIcon($ikon);
    $lp->setGambarUtama($pengganti);
    $lp->refresh();

    expect($lp->gambarUtamaUrl())->toBe($pengganti->url())
        ->and($lp->gambarIconUrl())->toBe($ikon->url());
});
