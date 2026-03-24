<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

    Route::resource('dashboard/users', UserController::class)
        ->except(['show'])
        ->names('dashboard.users');
});
