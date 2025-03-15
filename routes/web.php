<?php

use App\Http\Controllers\management\ManagementUsersController;
use App\Http\Middleware\CheckReceptionistRole;
use App\Http\Middleware\CheckManagerRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Management Routes
Route::middleware(['auth', 'verified', CheckManagerRole::class])->group(function () {
    Route::get('management/dashboard', function () {
        return Inertia::render('management/dashboard');
    })->name('management.dashboard');

    Route::get('management/bookings', function () {
        return Inertia::render('management/bookings');
    })->name('management.bookings');

    Route::get('management/accounting', function () {
        return Inertia::render('management/accounting');
    })->name('management.accounting');

    Route::get('management/restaurant', function () {
        return Inertia::render('management/restaurant');
    })->name('management.restaurant');

    Route::get('management/statistics', function () {
        return Inertia::render('management/statistics');
    })->name('management.statistics');

    Route::middleware('auth')->group(function () {
        Route::redirect('management/users', 'users/list');

        Route::get('/management/users/list', [ManagementUsersController::class, 'index'])->name('management.users.users_list');
        Route::post('/management/users', [ManagementUsersController::class, 'store'])->name('management.users.add_users');
        Route::get('/management/users/add', [ManagementUsersController::class, 'create'])->name('management.users.add_users');
        Route::get('/management/users/activate', [ManagementUsersController::class, 'unverified'])->name('management.users.verify_users');
        Route::post('/management/users/verify/{user}', [ManagementUsersController::class, 'verify'])->name('management.users.verify');
        Route::get('/management/users/{user}', [ManagementUsersController::class, 'show'])->name('management.users.users.show');
    });

    Route::get('management/user_info', function () {
        return Inertia::render('management/user_info');
    })->name('management.user_info');
});

// Reception Routes
Route::middleware(['auth', 'verified', CheckReceptionistRole::class])->group(function () {
    Route::get('reception/dashboard', function () {
        return Inertia::render('reception/dashboard');
    })->name('reception.dashboard');

    Route::get('reception/bookings', function () {
        return Inertia::render('reception/bookings');
    })->name('reception.bookings');

    Route::get('reception/payments', function () {
        return Inertia::render('reception/payments');
    })->name('reception.payments');

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
        Route::get('reception/users/add', [ManagementUsersController::class, 'create'])->name('reception.users.add_users');
        Route::get('reception/users/activate', function () {
            return Inertia::render('reception/users/verify_users');
        })->name('reception.users.verify_users');
        Route::get('reception/users/{user}', [ManagementUsersController::class, 'show'])->name('reception.users.users.show');
    });

    Route::get('reception/user_info', function () {
        return Inertia::render('reception/user_info');
    })->name('reception.user_info');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
