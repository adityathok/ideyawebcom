<?php

use App\Enums\DocStatus;
use App\Models\DocFeedback;
use App\Models\DocPage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    /** Hanya ID halaman yang dikirim ke klien, bukan modelnya. */
    public int $pageId;

    public ?bool $helpful = null;

    public string $comment = '';

    public bool $submitted = false;

    public int $helpfulCount = 0;

    public int $totalCount = 0;

    public function mount(int $pageId): void
    {
        $this->pageId = $pageId;

        $page = $this->page();

        $this->refreshAggregate($page);

        $existing = $page->feedback()->where('visitor_hash', $this->visitorHash())->first();

        // Pengunjung yang kembali melihat pilihannya sendiri, bukan formulir kosong.
        if ($existing instanceof DocFeedback) {
            $this->helpful = $existing->helpful;
            $this->comment = (string) $existing->comment;
            $this->submitted = true;
        }
    }

    public function rate(bool $helpful): void
    {
        $this->helpful = $helpful;

        $this->persist();
    }

    public function submit(): void
    {
        $this->persist();
    }

    private function persist(): void
    {
        $this->validate([
            'helpful' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ], [
            'helpful.required' => 'Pilih dulu apakah halaman ini membantu.',
        ]);

        $page = $this->page();

        // `updateOrCreate` pada pasangan (halaman, pengunjung) supaya pengunjung
        // bisa mengganti pilihannya alih-alih menghasilkan baris baru.
        $page->feedback()->updateOrCreate(
            ['visitor_hash' => $this->visitorHash()],
            [
                'helpful' => $this->helpful,
                'comment' => trim($this->comment) !== '' ? trim($this->comment) : null,
                'user_id' => Auth::id(),
            ],
        );

        $this->submitted = true;

        $this->refreshAggregate($page);
    }

    private function page(): DocPage
    {
        $page = DocPage::findOrFail($this->pageId);

        abort_unless($page->status === DocStatus::Published, 404);

        return $page;
    }

    private function refreshAggregate(DocPage $page): void
    {
        $aggregate = $page->feedback()
            ->selectRaw('count(*) as total, sum(helpful) as helpful')
            ->first();

        $this->totalCount = (int) ($aggregate?->getAttribute('total') ?? 0);
        $this->helpfulCount = (int) ($aggregate?->getAttribute('helpful') ?? 0);
    }

    /**
     * Identitas pengunjung tanpa menyimpan IP: ID sesi digabung app key lalu di-hash.
     */
    private function visitorHash(): string
    {
        return hash('sha256', session()->getId().'|'.(string) config('app.key'));
    }
}; ?>
<div class="rounded-[16px] border border-[#e3eaff] bg-[#fafbff] p-6">
    @if (! $submitted)
        <h2 class="text-[15px] font-semibold tracking-[-0.16px] text-[#100f12]">Apakah halaman ini membantu?</h2>
        <p class="mt-1 text-sm leading-6 text-[#65646e]">Masukan Anda membantu kami memperbaiki dokumentasi ini.</p>

        <div class="mt-4 flex flex-wrap gap-3">
            <button
                type="button"
                wire:click="rate(true)"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-full border border-[#e3eaff] bg-white px-5 py-2.5 text-sm font-medium text-[#100f12] transition hover:border-[#0a1589] hover:bg-[#f3f6ff] disabled:opacity-60"
            >
                <flux:icon.hand-thumb-up class="size-4" />
                Ya, membantu
            </button>

            <button
                type="button"
                wire:click="rate(false)"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-full border border-[#e3eaff] bg-white px-5 py-2.5 text-sm font-medium text-[#100f12] transition hover:border-[#0a1589] hover:bg-[#f3f6ff] disabled:opacity-60"
            >
                <flux:icon.hand-thumb-down class="size-4" />
                Tidak
            </button>
        </div>

        @error('helpful')
            <p class="mt-3 text-xs text-red-600">{{ $message }}</p>
        @enderror
    @else
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-[#0a1589] text-white">
                <flux:icon.check class="size-4" />
            </span>
            <div>
                <h2 class="text-[15px] font-semibold tracking-[-0.16px] text-[#100f12]">Terima kasih atas masukan Anda.</h2>
                <p class="mt-1 text-sm leading-6 text-[#65646e]">Pilihan Anda bisa diubah kapan saja.</p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-3">
            <button
                type="button"
                wire:click="rate(true)"
                wire:loading.attr="disabled"
                aria-pressed="{{ $helpful === true ? 'true' : 'false' }}"
                class="inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition disabled:opacity-60 {{ $helpful === true ? 'border-[#0a1589] bg-[#0a1589] text-white' : 'border-[#e3eaff] bg-white text-[#100f12] hover:border-[#0a1589] hover:bg-[#f3f6ff]' }}"
            >
                <flux:icon.hand-thumb-up class="size-4" />
                Membantu
            </button>

            <button
                type="button"
                wire:click="rate(false)"
                wire:loading.attr="disabled"
                aria-pressed="{{ $helpful === false ? 'true' : 'false' }}"
                class="inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition disabled:opacity-60 {{ $helpful === false ? 'border-[#0a1589] bg-[#0a1589] text-white' : 'border-[#e3eaff] bg-white text-[#100f12] hover:border-[#0a1589] hover:bg-[#f3f6ff]' }}"
            >
                <flux:icon.hand-thumb-down class="size-4" />
                Tidak membantu
            </button>
        </div>

        <div class="mt-4">
            <label for="docs-feedback-comment" class="text-sm font-medium text-[#100f12]">Ada saran tambahan? <span class="font-normal text-[#65646e]">(opsional)</span></label>
            <textarea
                id="docs-feedback-comment"
                wire:model="comment"
                rows="3"
                maxlength="1000"
                placeholder="Bagian mana yang perlu diperbaiki?"
                class="mt-2 w-full rounded-[16px] border border-[#e3eaff] bg-white px-4 py-3 text-sm text-[#100f12] transition placeholder:text-[#aaa9ae] focus:border-[#0a1589] focus:outline-none focus:ring-1 focus:ring-[#0a1589]"
            ></textarea>

            @error('comment')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-3 flex justify-end">
                <button
                    type="button"
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-[20px] bg-[#0a1589] px-6 pb-3.5 pt-4 text-[15px] font-medium leading-none text-white transition hover:bg-[#06105a] disabled:opacity-60"
                >
                    Kirim komentar
                </button>
            </div>
        </div>
    @endif

    @if ($totalCount > 0)
        <p class="mt-5 border-t border-[#e3eaff] pt-4 text-sm text-[#65646e]">
            <span class="font-semibold text-[#100f12]">{{ $helpfulCount }}</span> dari
            <span class="font-semibold text-[#100f12]">{{ $totalCount }}</span> orang merasa halaman ini membantu.
        </p>
    @endif
</div>
