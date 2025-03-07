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
        return Inertia::render('management/dashboard');
    })->name('management.dashboard');

    Route::get('bookings', function () {
        return Inertia::render('management/bookings');
    })->name('management.bookings');

    Route::get('accounting', function () {
        return Inertia::render('management/accounting');
    })->name('management.accounting');

    Route::get('restaurant', function () {
        return Inertia::render('management/restaurant');
    })->name('management.restaurant');

    Route::get('statistics', function () {
        return Inertia::render('management/statistics');
    })->name('management.statistics');

    Route::middleware('auth')->group(function () {
        Route::redirect('users', 'users/list');

        Route::get('/users/list', [ManagementUsersController::class, 'index'])->name('management.users.users_list');
        Route::post('/users', [ManagementUsersController::class, 'store'])->name('management.users.add_users');
        Route::get('/users/add', [ManagementUsersController::class, 'create'])->name('management.users.add_users');
        Route::get('/users/activate', function () {
            return Inertia::render('management/users/verify_users');
        })->name('management.users.verify_users');
        Route::get('/users/{user}', [ManagementUsersController::class, 'show'])->name('management.users.users.show');
    });

    Route::get('user_info', function () {
        return Inertia::render('management/user_info');
    })->name('management.user_info');
});

// Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('reception/dashboard', function () {
        return Inertia::render('reception/dashboard');
    })->name('reception.dashboard');

    Route::get('reception/bookings', function () {
        return Inertia::render('reception/bookings');
    })->name('reception.bookings');

    Route::get('reception/accounting', function () {
        return Inertia::render('reception/accounting');
    })->name('reception.accounting');

    Route::get('reception/restaurant', function () {
        return Inertia::render('reception/restaurant');
    })->name('reception.restaurant');

    Route::get('reception/statistics', function () {
        return Inertia::render('reception/statistics');
    })->name('reception.statistics');

    Route::middleware('auth')->group(function () {
        Route::redirect('users', 'users/list');

        Route::get('reception//users/list', [ManagementUsersController::class, 'index'])->name('reception.users.users_list');
        Route::post('/users', [ManagementUsersController::class, 'store'])->name('reception.users.add_users');
        Route::get('reception//users/add', [ManagementUsersController::class, 'create'])->name('reception.users.add_users');
        Route::get('reception//users/activate', function () {
            return Inertia::render('reception/users/verify_users');
        })->name('reception.users.verify_users');
        Route::get('reception//users/{user}', [ManagementUsersController::class, 'show'])->name('reception.users.users.show');
    });

    Route::get('reception/user_info', function () {
        return Inertia::render('reception/user_info');
    })->name('reception.user_info');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
