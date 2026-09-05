<?php
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;

new #[Title('Pengaturan Website')] class extends Component {
    use WithFileUploads;

    public string $company_name = '';
    public string $tagline = '';
    public string $about = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $facebook = '';
    public string $instagram = '';
    public string $twitter = '';
    public string $linkedin = '';
    public string $logo = '';

    public string $seo_title = '';
    public string $seo_description = '';
    public string $seo_keywords = '';
    public string $seo_og_image = '';

    public $seoOgImageUpload = null;

    public function mount(): void
    {
        $s = Setting::profile();
        $this->company_name = $s['company_name'] ?? '';
        $this->tagline = $s['tagline'] ?? '';
        $this->about = $s['about'] ?? '';
        $this->email = $s['email'] ?? '';
        $this->phone = $s['phone'] ?? '';
        $this->address = $s['address'] ?? '';
        $this->facebook = $s['facebook'] ?? '';
        $this->instagram = $s['instagram'] ?? '';
        $this->twitter = $s['twitter'] ?? '';
        $this->linkedin = $s['linkedin'] ?? '';
        $this->logo = $s['logo'] ?? '';

        $seo = Setting::seo();
        $this->seo_title = $seo['seo_title'] ?? '';
        $this->seo_description = $seo['seo_description'] ?? '';
        $this->seo_keywords = $seo['seo_keywords'] ?? '';
        $this->seo_og_image = $seo['seo_og_image'] ?? '';
    }

    public function removeSeoOgImage(): void
    {
        if ($this->seo_og_image && ! str_starts_with($this->seo_og_image, 'http') && ! str_starts_with($this->seo_og_image, '/')) {
            Storage::disk('public')->delete($this->seo_og_image);
        }
        $this->seo_og_image = '';
        $this->seoOgImageUpload = null;
        Setting::set('seo_og_image', '', 'string', 'seo');
    }

    public function save(): void
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'about' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'logo' => 'nullable|string|max:500',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
            'seo_og_image' => 'nullable|string|max:500',
            'seoOgImageUpload' => 'nullable|image|max:4096',
        ]);

        $ogImagePath = $this->seo_og_image;
        if ($this->seoOgImageUpload) {
            if ($ogImagePath && ! str_starts_with($ogImagePath, 'http') && ! str_starts_with($ogImagePath, '/')) {
                Storage::disk('public')->delete($ogImagePath);
            }
            $ogImagePath = $this->seoOgImageUpload->store('seo', 'public');
        }

        foreach ([
            'company_name' => $this->company_name,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'linkedin' => $this->linkedin,
            'logo' => $this->logo,
        ] as $k => $v) {
            Setting::set($k, $v, 'string', 'profile');
        }
        foreach ([
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'seo_og_image' => $ogImagePath,
        ] as $k => $v) {
            Setting::set($k, (string) $v, 'string', 'seo');
        }
        $this->seo_og_image = (string) $ogImagePath;
        $this->seoOgImageUpload = null;
        Flux::toast(variant: 'success', text: 'Pengaturan disimpan.');
    }
}; ?>
<section class="w-full">
    <flux:heading size="xl">Pengaturan Website</flux:heading>
    <flux:subheading>Kelola profil perusahaan & pengaturan umum (disimpan di tabel settings).</flux:subheading>

    <form wire:submit="save" class="mt-6 space-y-4 max-w-2xl rounded-2xl border border-[#d3cec6] bg-white p-6">
        <flux:input wire:model="company_name" label="Nama Perusahaan" required />
        <flux:input wire:model="tagline" label="Tagline" />
        <flux:textarea wire:model="about" label="Tentang" rows="4" required />
        <div class="grid gap-4 sm:grid-cols-2">
            <flux:input wire:model="email" label="Email" type="email" required />
            <flux:input wire:model="phone" label="Telepon" />
        </div>
        <flux:textarea wire:model="address" label="Alamat" rows="2" />
        <flux:input wire:model="logo" label="Logo URL" placeholder="/logo.png" />
        <flux:heading size="sm" class="pt-2">Sosial Media</flux:heading>
        <flux:input wire:model="facebook" label="Facebook URL" placeholder="https://facebook.com/..." />
        <flux:input wire:model="instagram" label="Instagram URL" placeholder="https://instagram.com/..." />
        <flux:input wire:model="twitter" label="Twitter / X URL" placeholder="https://x.com/..." />
        <flux:input wire:model="linkedin" label="LinkedIn URL" placeholder="https://linkedin.com/company/..." />

        <flux:separator class="my-2" />
        <flux:heading size="sm">SEO Default (fallback site-wide)</flux:heading>
        <p class="text-sm leading-6 text-[#626260]">Dipakai <code class="rounded bg-[#f5f1ec] px-1 py-0.5 text-xs">MetaService</code> sebagai default jika halaman tidak set meta sendiri. Kosongkan untuk fallback ke Nama Perusahaan / Tentang. Bisa di-override per-controller via <code class="rounded bg-[#f5f1ec] px-1 py-0.5 text-xs">forHome()</code> / <code class="rounded bg-[#f5f1ec] px-1 py-0.5 text-xs">set([...])</code>.</p>
        <flux:input wire:model="seo_title" label="SEO Title" placeholder="Contoh: IdeyaWeb — Digital Agency & IT Solution" description="Judul default (title tag). Ideal ≤60 karakter." />
        <flux:textarea wire:model="seo_description" label="SEO Description" rows="3" placeholder="Deskripsi singkat untuk hasil pencarian & share..." description="Ideal 150–160 karakter." />
        <flux:input wire:model="seo_keywords" label="SEO Keywords" placeholder="laravel, agency, website, aplikasi" description="Pisahkan dengan koma." />
        <div class="space-y-3 rounded-xl border border-[#d3cec6] bg-[#f5f1ec] p-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-[#111111]">OG Image Default</label>
                <span class="text-xs text-[#626260]">JPG/PNG/WebP max 4MB — dipakai og:image & twitter:image</span>
            </div>
            @php
                $ogPreview = null;
                if ($seoOgImageUpload) {
                    $ogPreview = $seoOgImageUpload->temporaryUrl();
                } elseif (filled($seo_og_image)) {
                    $ogPreview = \Illuminate\Support\Facades\Storage::disk('public')->url($seo_og_image);
                    if (str_starts_with($seo_og_image, 'http://') || str_starts_with($seo_og_image, 'https://') || str_starts_with($seo_og_image, '/')) {
                        $ogPreview = $seo_og_image;
                    }
                }
            @endphp
            @if($ogPreview)
                <div class="overflow-hidden rounded-lg border border-[#d3cec6] bg-white p-2">
                    <img src="{{ $ogPreview }}" alt="OG preview" class="h-48 w-full rounded-md object-cover" />
                    <div class="mt-2 flex gap-2">
                        <flux:button size="sm" variant="danger" wire:click="removeSeoOgImage" type="button">Hapus Gambar</flux:button>
                    </div>
                </div>
            @endif
            <input type="file" wire:model="seoOgImageUpload" accept="image/*" class="block w-full rounded-lg border border-[#d3cec6] bg-white px-3 py-2 text-sm text-[#111111] file:mr-3 file:rounded-md file:border-0 file:bg-[#111111] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-black" />
            <div wire:loading wire:target="seoOgImageUpload" class="text-xs text-[#626260]">Mengunggah...</div>
            @error('seoOgImageUpload')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            <flux:input wire:model="seo_og_image" label="Atau URL / path" placeholder="https://... atau /images/og-default.jpg" description="Jika upload di atas dipakai, field ini akan terisi otomatis dengan path storage." />
        </div>

        <div class="pt-4 flex gap-2">
            <flux:button type="submit" variant="primary">Simpan Pengaturan</flux:button>
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center rounded-lg border px-4 py-2 text-sm hover:bg-[#f5f1ec]">Lihat Homepage</a>
        </div>
    </form>
</section>
