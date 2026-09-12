<?php
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\User;
use Flux\Flux;

new #[Title('Users')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public string $deletingName = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->reset(['name','email','password','password_confirmation','editingId']);
        $this->resetValidation();
        Flux::modal('user-form')->show();
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->resetValidation();
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';

        Flux::modal('user-form')->show();
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.($this->editingId ?? 'NULL'),
        ];

        if ($this->editingId === null || filled($this->password)) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules);

        $isEdit = $this->editingId !== null;

        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (filled($this->password)) {
            $attributes['password'] = $this->password;
        }

        $user = User::updateOrCreate(['id' => $this->editingId], $attributes);

        if (! $isEdit) {
            // Akun yang dibuat admin langsung terverifikasi agar bisa dipakai login.
            $user->email_verified_at = now();
            $user->save();
        }

        $this->reset(['name','email','password','password_confirmation','editingId']);
        Flux::toast(variant: 'success', text: $isEdit ? 'User diperbarui.' : 'User ditambahkan.');
        Flux::modal('user-form')->close();
    }

    public function confirmDelete(int $id): void
    {
        if ($id === auth()->id()) {
            Flux::toast(variant: 'danger', text: 'Tidak bisa menghapus akun yang sedang login.');
            return;
        }

        $user = User::findOrFail($id);
        $this->deletingId = $user->id;
        $this->deletingName = $user->name;
        Flux::modal('confirm-user-deletion')->show();
    }

    public function delete(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        if ($this->deletingId === auth()->id()) {
            $this->reset(['deletingId', 'deletingName']);
            Flux::modal('confirm-user-deletion')->close();
            Flux::toast(variant: 'danger', text: 'Tidak bisa menghapus akun yang sedang login.');
            return;
        }

        User::findOrFail($this->deletingId)->delete();

        $this->reset(['deletingId', 'deletingName']);
        Flux::toast(variant: 'success', text: 'User dihapus.');
        Flux::modal('confirm-user-deletion')->close();
    }
}; ?>
<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Users</flux:heading>
            <flux:subheading>Kelola akun admin ({{ \App\Models\User::count() }} total).</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="create">+ User</flux:button>
    </div>

    <div class="mt-6">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." icon="magnifying-glass" />
    </div>

    @php
        $users = \App\Models\User::query()
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate(10);
    @endphp

    <div class="mt-6 overflow-hidden rounded-xl border border-[#e3eaff] bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#fafbff]">
                <tr>
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Email</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#e3eaff]">
                @forelse ($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="bg-white hover:bg-[#f3f6ff]/60 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <flux:avatar size="sm" :name="$user->name" :initials="$user->initials()" />
                                <span class="font-medium">{{ $user->name }}</span>
                                @if ($user->id === auth()->id())
                                    <span class="rounded-full bg-[#f3f6ff] px-2 py-0.5 text-xs font-medium text-[#0a1589]">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-[#65646e]">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if ($user->email_verified_at)
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">Terverifikasi</span>
                            @else
                                <span class="rounded-full bg-[#f3f6ff] px-2 py-0.5 text-xs text-[#65646e]">Belum verifikasi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1.5">
                                <flux:button size="sm" variant="ghost" wire:click="edit({{ $user->id }})">Edit</flux:button>
                                @if ($user->id !== auth()->id())
                                    <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $user->id }})">Hapus</flux:button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-sm text-[#65646e]">Tidak ada user ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-[#e3eaff] bg-white p-4">
            {{ $users->links() }}
        </div>
    </div>

    <flux:modal name="user-form" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit User' : 'Tambah User' }}</flux:heading>
                <flux:subheading>{{ $editingId ? 'Perbarui data akun. Kosongkan password bila tidak diubah.' : 'Buat akun baru untuk mengakses panel admin.' }}</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Nama" required />
            <flux:input wire:model="email" label="Email" type="email" required />
            <flux:input wire:model="password" label="Password" type="password" viewable :description="$editingId ? 'Kosongkan bila tidak ingin mengubah password' : 'Minimal 8 karakter'" />
            <flux:input wire:model="password_confirmation" label="Konfirmasi Password" type="password" viewable />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Simpan' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="confirm-user-deletion" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus user ini?</flux:heading>
                <flux:subheading>
                    Akun "{{ $deletingName }}" akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                </flux:subheading>
            </div>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="delete">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
