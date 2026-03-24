<?php

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    Route::resource('dashboard/users', UserController::class)
        ->except(['show'])
        ->names('dashboard.users');

    Route::get('dashboard/notifications', [NotificationController::class, 'index'])->name('dashboard.notifications.index');
    Route::patch('dashboard/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('dashboard.notifications.read');
    Route::post('dashboard/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('dashboard.notifications.readAll');
});
