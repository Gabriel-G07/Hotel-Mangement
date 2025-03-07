<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\RolesController;
use App\Http\Controllers\Settings\RecordTrackingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/roles', [RolesController::class, 'index'])->name('management.settings.roles.index');
    Route::get('settings/roles/{role_id}/edit', [RolesController::class, 'edit'])->name('management.settings.roles.edit');
    Route::patch('settings/roles/{role_id}', [RolesController::class, 'update'])->name('management.settings.roles.update');
    Route::post('settings/roles', [RolesController::class, 'store'])->name('management.settings.roles.store');
    Route::delete('settings/roles/{role_id}', [RolesController::class, 'destroy'])->name('management.settings.roles.destroy');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('management.settings.profile.edit');
    Route::patch('settings/profile/verify-password', [ProfileController::class, 'verifyPassword'])->name('management.settings.profile.verify-password');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('management.settings.profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('management.settings.profile.destroy');
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('management.settings.password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('management.settings.password.update');

    Route::get('settings/activities', [RecordTrackingController::class, 'index'])->name('management.settings.activities.index');

    Route::get('settings/appearance', function () {return Inertia::render('management/settings/appearance');})->name('management.settings.appearance');
});
