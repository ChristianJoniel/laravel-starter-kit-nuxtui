<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

it('redirects guests from the users index', function () {
    $this->get(route('dashboard.users.index'))
        ->assertRedirect(route('login'));
});

it('shows the users index for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard.users.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Users/Index')
            ->has('users.data'));
});

it('creates a user', function () {
    $actor = User::factory()->create();

    $this->actingAs($actor)
        ->post(route('dashboard.users.store'), [
            'name' => 'New Person',
            'email' => 'new-person@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('dashboard.users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'new-person@example.com',
        'name' => 'New Person',
    ]);
});

it('updates a user', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->actingAs($actor)
        ->put(route('dashboard.users.update', $target), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ])
        ->assertRedirect(route('dashboard.users.index'));

    expect($target->fresh())
        ->name->toBe('Updated Name')
        ->email->toBe('updated@example.com');
});

it('prevents deleting your own account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('dashboard.users.destroy', $user))
        ->assertRedirect(route('dashboard.users.index'));

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

it('deletes another user', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->delete(route('dashboard.users.destroy', $target))
        ->assertRedirect(route('dashboard.users.index'));

    $this->assertDatabaseMissing('users', ['id' => $target->id]);
});
