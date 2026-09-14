<?php

use App\Services\MarkdownService;

test('renders markdown into html with a table of contents', function () {
    $result = (new MarkdownService)->render("## Judul\n\nTeks **tebal**.");

    expect($result['html'])
        ->toContain('<h2 id="judul">')
        ->toContain('<strong>tebal</strong>')
        ->and($result['toc'])->toBe([
            ['level' => 2, 'id' => 'judul', 'title' => 'Judul'],
        ]);
});

test('collects only the headings from h2 to h4', function () {
    $result = (new MarkdownService)->render("# Satu\n\n## Dua\n\n### Tiga\n\n#### Empat\n\n##### Lima");

    expect(array_column($result['toc'], 'level'))->toBe([2, 3, 4])
        ->and(array_column($result['toc'], 'id'))->toBe(['dua', 'tiga', 'empat']);
});

test('gives repeated headings unique ids', function () {
    $result = (new MarkdownService)->render("## Sama\n\n## Sama\n\n## Sama");

    expect(array_column($result['toc'], 'id'))->toBe(['sama', 'sama-1', 'sama-2'])
        ->and($result['html'])->toContain('id="sama-2"');
});

test('strips raw html coming from the markdown source', function () {
    $result = (new MarkdownService)->render("<script>alert('xss')</script>\n\nAman.");

    expect($result['html'])
        ->not->toContain('<script')
        ->toContain('Aman.');
});

test('drops unsafe links', function () {
    $result = (new MarkdownService)->render('[klik](javascript:alert(1))');

    expect($result['html'])->not->toContain('javascript:');
});

test('builds the toc entry from a heading that carries inline markup', function () {
    $result = (new MarkdownService)->render("## Pasang **Cepat** Sekarang\n\n### Tombol & Tombol");

    expect($result['toc'])->toBe([
        ['level' => 2, 'id' => 'pasang-cepat-sekarang', 'title' => 'Pasang Cepat Sekarang'],
        ['level' => 3, 'id' => 'tombol-tombol', 'title' => 'Tombol & Tombol'],
    ]);
});

test('falls back to a generic id when the heading cannot be slugged', function () {
    $result = (new MarkdownService)->render('## ???');

    expect($result['toc'])->toBe([
        ['level' => 2, 'id' => 'section', 'title' => '???'],
    ]);
});

test('renders a github flavoured table', function () {
    $result = (new MarkdownService)->render("| A | B |\n| - | - |\n| 1 | 2 |");

    expect($result['html'])->toContain('<table>')->toContain('<td>1</td>');
});

test('returns an empty table of contents when there are no headings', function () {
    $result = (new MarkdownService)->render('Hanya paragraf biasa.');

    expect($result['toc'])->toBe([])
        ->and($result['html'])->toContain('<p>Hanya paragraf biasa.</p>');
});
