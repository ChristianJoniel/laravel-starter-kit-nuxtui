<?php

use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\Notification;

it('redirects guests from notification endpoints', function () {
    $this->getJson(route('dashboard.notifications.index'))
        ->assertUnauthorized();

    $this->patchJson(route('dashboard.notifications.read', 'fake-id'))
        ->assertUnauthorized();

    $this->postJson(route('dashboard.notifications.readAll'))
        ->assertUnauthorized();
});

it('returns notifications for authenticated users', function () {
    $user = User::factory()->create();
    $createdUser = User::factory()->create();
    $user->notify(new UserCreatedNotification($createdUser));

    $this->actingAs($user)
        ->getJson(route('dashboard.notifications.index'))
        ->assertSuccessful()
        ->assertJsonStructure(['notifications', 'unreadCount'])
        ->assertJsonCount(1, 'notifications')
        ->assertJsonPath('unreadCount', 1);
});

it('marks a single notification as read', function () {
    $user = User::factory()->create();
    $createdUser = User::factory()->create();
    $user->notify(new UserCreatedNotification($createdUser));

    $notification = $user->notifications()->first();

    $this->actingAs($user)
        ->patchJson(route('dashboard.notifications.read', $notification->id))
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('marks all notifications as read', function () {
    $user = User::factory()->create();
    $users = User::factory()->count(3)->create();

    foreach ($users as $createdUser) {
        $user->notify(new UserCreatedNotification($createdUser));
    }

    expect($user->unreadNotifications()->count())->toBe(3);

    $this->actingAs($user)
        ->postJson(route('dashboard.notifications.readAll'))
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    expect($user->unreadNotifications()->count())->toBe(0);
});

it('dispatches UserCreatedNotification on database and broadcast channels when a user is created', function () {
    Notification::fake();

    $actor = User::factory()->create();

    $this->actingAs($actor)
        ->post(route('dashboard.users.store'), [
            'name' => 'New Person',
            'email' => 'new-person@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('dashboard.users.index'));

    Notification::assertSentTo(
        $actor,
        UserCreatedNotification::class,
        function (UserCreatedNotification $notification, array $channels) {
            return in_array('database', $channels) && in_array('broadcast', $channels);
        }
    );
});
