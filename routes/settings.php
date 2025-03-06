<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\RolesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/roles', [RolesController::class, 'index'])->name('roles.index');
    Route::get('settings/roles/{role_id}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::patch('settings/roles/{role_id}', [RolesController::class, 'update'])->name('roles.update');
    Route::post('settings/roles', [RolesController::class, 'store'])->name('roles.store');
    Route::delete('settings/roles/{role_id}', [RolesController::class, 'destroy'])->name('roles.destroy');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance');
});
