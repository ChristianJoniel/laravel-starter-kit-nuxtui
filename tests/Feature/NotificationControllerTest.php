<?php

use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('index returns latest notifications and unread count', function () {
    $createdUser = User::factory()->create();
    $this->user->notify(new UserCreatedNotification($createdUser));

    $response = $this->getJson(route('dashboard.notifications.index'));

    $response->assertOk()
        ->assertJsonStructure(['notifications', 'unreadCount'])
        ->assertJsonCount(1, 'notifications')
        ->assertJsonPath('unreadCount', 1);
});

test('index returns empty when no notifications', function () {
    $response = $this->getJson(route('dashboard.notifications.index'));

    $response->assertOk()
        ->assertJsonPath('unreadCount', 0)
        ->assertJsonCount(0, 'notifications');
});

test('markAsRead marks a single notification as read', function () {
    $createdUser = User::factory()->create();
    $this->user->notify(new UserCreatedNotification($createdUser));

    $notification = $this->user->notifications()->first();

    $response = $this->patchJson(route('dashboard.notifications.read', $notification->id));

    $response->assertOk()
        ->assertJsonPath('success', true);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('markAllAsRead marks all unread notifications as read', function () {
    $users = User::factory()->count(3)->create();
    foreach ($users as $createdUser) {
        $this->user->notify(new UserCreatedNotification($createdUser));
    }

    expect($this->user->unreadNotifications()->count())->toBe(3);

    $response = $this->postJson(route('dashboard.notifications.readAll'));

    $response->assertOk()
        ->assertJsonPath('success', true);

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

test('unauthenticated user cannot access notifications', function () {
    auth()->logout();

    $this->getJson(route('dashboard.notifications.index'))->assertUnauthorized();
    $this->postJson(route('dashboard.notifications.readAll'))->assertUnauthorized();
});
