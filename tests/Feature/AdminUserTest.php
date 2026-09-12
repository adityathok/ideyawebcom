<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('guests are redirected to the login page', function () {
    $this->post(route('logout'));

    $this->get(route('admin.users'))->assertRedirect(route('login'));
});

test('authenticated users can visit the users page', function () {
    $this->get(route('admin.users'))
        ->assertOk()
        ->assertSee('Kelola akun admin');
});

test('user list renders edit and delete actions', function () {
    User::factory()->create(['name' => 'Budi Santoso']);

    Livewire::test('pages::admin.users.index')
        ->assertSee('Budi Santoso')
        ->assertSee('Edit')
        ->assertSee('Hapus');
});

test('a user can be created from the modal', function () {
    Livewire::test('pages::admin.users.index')
        ->call('create')
        ->set('name', 'Siti Aminah')
        ->set('email', 'siti@example.com')
        ->set('password', 'rahasia123')
        ->set('password_confirmation', 'rahasia123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'user-form');

    $user = User::where('email', 'siti@example.com')->firstOrFail();

    expect($user->name)->toBe('Siti Aminah');
    expect(Hash::check('rahasia123', $user->password))->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
});

test('creating a user requires a matching password of at least 8 characters', function () {
    Livewire::test('pages::admin.users.index')
        ->call('create')
        ->set('name', 'Siti Aminah')
        ->set('email', 'siti@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'berbeda')
        ->call('save')
        ->assertHasErrors(['password']);

    expect(User::where('email', 'siti@example.com')->exists())->toBeFalse();
});

test('creating a user rejects a duplicate email', function () {
    User::factory()->create(['email' => 'siti@example.com']);

    Livewire::test('pages::admin.users.index')
        ->call('create')
        ->set('name', 'Siti Aminah')
        ->set('email', 'siti@example.com')
        ->set('password', 'rahasia123')
        ->set('password_confirmation', 'rahasia123')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('users can be searched by name or email', function () {
    User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);
    User::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@example.com']);

    Livewire::test('pages::admin.users.index')
        ->set('search', 'siti')
        ->assertSee('Siti Aminah')
        ->assertDontSee('Budi Santoso');
});

test('editing a user loads its values into the form', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);

    Livewire::test('pages::admin.users.index')
        ->call('edit', $user->id)
        ->assertSet('editingId', $user->id)
        ->assertSet('name', 'Budi Santoso')
        ->assertSet('email', 'budi@example.com')
        ->assertSet('password', '')
        ->assertDispatched('modal-show', name: 'user-form');
});

test('editing without a password keeps the existing password', function () {
    $user = User::factory()->create();
    $originalHash = $user->password;

    Livewire::test('pages::admin.users.index')
        ->call('edit', $user->id)
        ->set('name', 'Nama Baru')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Nama Baru');
    expect($user->password)->toBe($originalHash);
});

test('editing can change the password', function () {
    $user = User::factory()->create();

    Livewire::test('pages::admin.users.index')
        ->call('edit', $user->id)
        ->set('password', 'passwordbaru')
        ->set('password_confirmation', 'passwordbaru')
        ->call('save')
        ->assertHasNoErrors();

    expect(Hash::check('passwordbaru', $user->fresh()->password))->toBeTrue();
});

test('deleting a user requires confirmation through the modal', function () {
    $user = User::factory()->create(['name' => 'Budi Santoso']);

    $component = Livewire::test('pages::admin.users.index')
        ->call('confirmDelete', $user->id)
        ->assertSet('deletingId', $user->id)
        ->assertSet('deletingName', 'Budi Santoso')
        ->assertDispatched('modal-show', name: 'confirm-user-deletion');

    expect(User::find($user->id))->not->toBeNull();

    $component->call('delete')
        ->assertSet('deletingId', null)
        ->assertDispatched('modal-close', name: 'confirm-user-deletion');

    expect(User::find($user->id))->toBeNull();
});

test('a user cannot delete their own account', function () {
    $me = User::factory()->create();
    $this->actingAs($me);

    $component = Livewire::test('pages::admin.users.index')
        ->call('confirmDelete', $me->id);

    $component->assertNotDispatched('modal-show');

    $component->set('deletingId', $me->id)
        ->call('delete')
        ->assertDispatched('modal-close', name: 'confirm-user-deletion');

    expect(User::find($me->id))->not->toBeNull();
});

test('deleting a user with posts requires choosing a new author', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    Livewire::test('pages::admin.users.index')
        ->call('confirmDelete', $author->id)
        ->assertSet('deletingPostsCount', 1)
        ->assertSee('Pindahkan post ke')
        ->call('delete')
        ->assertHasErrors(['reassignTo']);

    expect(User::find($author->id))->not->toBeNull();
    expect($post->fresh()->user_id)->toBe($author->id);
});

test('deleting a user moves their posts to the chosen author', function () {
    $author = User::factory()->create();
    $newAuthor = User::factory()->create(['name' => 'Penulis Baru']);
    $post = Post::factory()->published()->create(['user_id' => $author->id]);

    Livewire::test('pages::admin.users.index')
        ->call('confirmDelete', $author->id)
        ->set('reassignTo', (string) $newAuthor->id)
        ->call('delete')
        ->assertHasNoErrors();

    expect(User::find($author->id))->toBeNull();
    expect($post->fresh()->user_id)->toBe($newAuthor->id);
    expect($post->fresh()->trashed())->toBeFalse();
});

test('deleting a user cannot reassign posts to the deleted user', function () {
    $author = User::factory()->create();
    Post::factory()->create(['user_id' => $author->id]);

    Livewire::test('pages::admin.users.index')
        ->call('confirmDelete', $author->id)
        ->set('reassignTo', (string) $author->id)
        ->call('delete')
        ->assertHasErrors(['reassignTo']);

    expect(User::find($author->id))->not->toBeNull();
});

test('deleting a user also moves their soft-deleted posts', function () {
    $author = User::factory()->create();
    $newAuthor = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $post->delete();

    Livewire::test('pages::admin.users.index')
        ->call('confirmDelete', $author->id)
        ->assertSet('deletingPostsCount', 1)
        ->set('reassignTo', (string) $newAuthor->id)
        ->call('delete')
        ->assertHasNoErrors();

    expect(User::find($author->id))->toBeNull();
    expect(Post::withTrashed()->find($post->id)->user_id)->toBe($newAuthor->id);
});

test('a user with posts cannot be deleted directly at the database level', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    expect(fn () => $author->delete())->toThrow(QueryException::class);

    expect(User::find($author->id))->not->toBeNull();
    expect($post->fresh())->not->toBeNull();
});
