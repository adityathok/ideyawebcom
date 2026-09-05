<?php
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Setting;
use Flux\Flux;

new #[Title('Pengaturan Website')] class extends Component {
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
        ]);
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
        <div class="pt-4 flex gap-2">
            <flux:button type="submit" variant="primary">Simpan Pengaturan</flux:button>
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center rounded-lg border px-4 py-2 text-sm hover:bg-[#f5f1ec]">Lihat Homepage</a>
        </div>
    </form>
</section>
