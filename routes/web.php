<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('bookings', function () {
        return Inertia::render('bookings');
    })->name('bookings');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('accounting', function () {
        return Inertia::render('accounting');
    })->name('accounting');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('restaurant', function () {
        return Inertia::render('restaurant');
    })->name('restaurant');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('statistics', function () {
        return Inertia::render('statistics');
    })->name('statistics');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('users', function () {
        return Inertia::render('users');
    })->name('users');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('user_info', function () {
        return Inertia::render('user_info');
    })->name('user_info');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
