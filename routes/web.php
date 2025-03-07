<?php

use App\Http\Controllers\management\ManagementUsersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('bookings', function () {
        return Inertia::render('bookings');
    })->name('bookings');

    Route::get('accounting', function () {
        return Inertia::render('accounting');
    })->name('accounting');

    Route::get('restaurant', function () {
        return Inertia::render('restaurant');
    })->name('restaurant');

    Route::get('statistics', function () {
        return Inertia::render('statistics');
    })->name('statistics');

    Route::get('/users', [ManagementUsersController::class, 'index'])->name('management.users_list');
    Route::get('/users/list', [ManagementUsersController::class, 'index'])->name('management.users_list');
    Route::post('/users', [ManagementUsersController::class, 'store'])->name('management.add_users');
    Route::get('/users/add', [ManagementUsersController::class, 'create'])->name('management.add_users');
    Route::get('/users/activate', function () {
        return Inertia::render('management/verify_users');
    })->name('management.verify_users');

    Route::get('/users/{user}', [ManagementUsersController::class, 'show'])->name('management.users.show');

    Route::get('user_info', function () {
        return Inertia::render('user_info');
    })->name('user_info');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
