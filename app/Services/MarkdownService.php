<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

final class MarkdownService
{
    /**
     * Render Markdown dari admin menjadi HTML siap tampil, sekaligus daftar isi
     * h2–h4 (setiap heading diberi `id` unik agar bisa di-anchor).
     *
     * @return array{html: string, toc: list<array{level: int, id: string, title: string}>}
     */
    public function render(string $markdown): array
    {
        $html = Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        /** @var list<array{level: int, id: string, title: string}> $toc */
        $toc = [];

        /** @var array<string, true> $usedIds */
        $usedIds = [];

        $processed = preg_replace_callback(
            '#<h([2-4])([^>]*)>(.*?)</h\1>#s',
            function (array $matches) use (&$toc, &$usedIds): string {
                $level = (int) $matches[1];
                // Heading bisa datang dengan id sendiri dari Markdown mentah; dibuang
                // supaya id kita tidak dobel di dalam satu dokumen.
                $attributes = preg_replace('/\s+id="[^"]*"/i', '', $matches[2]) ?? '';
                $inner = $matches[3];
                $title = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $id = $this->uniqueId($title, $usedIds);

                $toc[] = ['level' => $level, 'id' => $id, 'title' => $title];

                return '<h'.$level.$attributes.' id="'.$id.'">'.$inner.'</h'.$level.'>';
            },
            $html,
        );

        if ($processed === null) {
            return ['html' => $html, 'toc' => []];
        }

        return ['html' => $processed, 'toc' => $toc];
    }

    /**
     * Slug unik per dokumen: `judul`, `judul-1`, `judul-2`, dan seterusnya.
     *
     * @param  array<string, true>  $usedIds
     */
    private function uniqueId(string $title, array &$usedIds): string
    {
        $base = Str::slug($title) ?: 'section';

        if (! isset($usedIds[$base])) {
            $usedIds[$base] = true;

            return $base;
        }

        $suffix = 1;

        while (isset($usedIds[$base.'-'.$suffix])) {
            $suffix++;
        }

        $id = $base.'-'.$suffix;
        $usedIds[$id] = true;

        return $id;
    }
}
