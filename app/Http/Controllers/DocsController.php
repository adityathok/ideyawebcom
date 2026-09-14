<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DocStatus;
use App\Models\DocPage;
use App\Models\DocVersion;
use App\Models\Product;
use App\Services\MarkdownService;
use App\Services\MetaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DocsController extends Controller
{
    public function index(MetaService $meta): View
    {
        $products = Product::published()
            ->with(['versions' => fn ($q) => $q->ordered()])
            ->withCount(['pages as published_pages_count' => fn (Builder $q): Builder => $q->where('status', DocStatus::Published)])
            ->ordered()
            ->get();

        $seoMeta = $meta->forDocsIndex()->generate();

        return view('pages.docs.index', compact('products', 'seoMeta'));
    }

    /**
     * `/docs/{produk}` sengaja redirect (302, bukan 301) ke versi current,
     * karena versi current bisa berubah.
     */
    public function product(Product $product): RedirectResponse
    {
        abort_unless($product->is_published, 404);

        $version = $product->versions()->current()->ordered()->first()
            ?? $product->versions()->ordered()->first();

        abort_if($version === null, 404);

        return redirect()->route('docs.version', [$product, $version]);
    }

    public function version(Product $product, DocVersion $version, Request $request, MetaService $meta): View
    {
        abort_unless($product->is_published, 404);

        $search = $request->string('q')->trim()->toString();

        $versions = $product->versions()->ordered()->get();
        $navigation = $this->navigation($version);
        $results = $search === '' ? null : $this->search($version, $search);

        $seoMeta = $meta->forDocsVersion($product, $version)->generate();

        return view('pages.docs.version', compact('product', 'version', 'versions', 'navigation', 'search', 'results', 'seoMeta'));
    }

    public function page(Product $product, DocVersion $version, DocPage $page, MarkdownService $markdown, MetaService $meta): View
    {
        abort_unless($product->is_published, 404);
        abort_unless($page->status === DocStatus::Published, 404);

        // `increment()` biasa ikut menyentuh `updated_at`, padahal label "Terakhir
        // diperbarui" dan `dateModified` harus menunjuk waktu edit konten.
        Model::withoutTimestamps(fn () => $page->increment('view_count'));

        $versions = $product->versions()->ordered()->get();
        $navigation = $this->navigation($version);
        [$previous, $next] = $this->adjacent($this->flatten($navigation), $page);
        $content = $markdown->render((string) $page->body);

        $seoMeta = $meta->forDocPage($product, $version, $page)->generate();

        return view('pages.docs.show', compact('product', 'version', 'versions', 'page', 'navigation', 'previous', 'next', 'content', 'seoMeta'));
    }

    /**
     * Tree navigasi halaman terpublikasi pada satu versi.
     *
     * @return list<array{page: DocPage, children: list<mixed>}>
     */
    private function navigation(DocVersion $version): array
    {
        $pages = $version->pages()
            ->published()
            ->ordered()
            ->get(['id', 'product_id', 'version_id', 'parent_id', 'title', 'slug', 'sort_order']);

        return $this->tree($pages);
    }

    /**
     * @param  Collection<int, DocPage>  $pages
     * @return list<array{page: DocPage, children: list<mixed>}>
     */
    private function tree(Collection $pages, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($pages as $page) {
            if ($page->parent_id !== $parentId) {
                continue;
            }

            $tree[] = [
                'page' => $page,
                'children' => $this->tree($pages, (int) $page->id),
            ];
        }

        return $tree;
    }

    /**
     * Ratakan tree navigasi secara depth-first, supaya halaman sebelumnya/berikutnya
     * mengikuti urutan baca yang sama dengan yang dilihat pembaca di sidebar.
     *
     * @param  array<int, mixed>  $navigation
     * @return list<DocPage>
     */
    private function flatten(array $navigation): array
    {
        $flat = [];

        foreach ($navigation as $node) {
            if (! is_array($node)) {
                continue;
            }

            $page = $node['page'] ?? null;

            if (! $page instanceof DocPage) {
                continue;
            }

            $flat[] = $page;

            $children = $node['children'] ?? null;

            if (is_array($children)) {
                foreach ($this->flatten($children) as $child) {
                    $flat[] = $child;
                }
            }
        }

        return $flat;
    }

    /**
     * @param  list<DocPage>  $flat
     * @return array{0: DocPage|null, 1: DocPage|null}
     */
    private function adjacent(array $flat, DocPage $current): array
    {
        $index = null;

        foreach ($flat as $position => $page) {
            if ($page->is($current)) {
                $index = $position;

                break;
            }
        }

        if ($index === null) {
            return [null, null];
        }

        return [
            $flat[$index - 1] ?? null,
            $flat[$index + 1] ?? null,
        ];
    }

    /**
     * @return Collection<int, DocPage>
     */
    private function search(DocVersion $version, string $term): Collection
    {
        return $version->pages()
            ->published()
            ->search($term)
            ->ordered()
            ->limit(30)
            ->get();
    }
}
