<?php

use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ReceptionPasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\ReceptionProfileController;
use App\Http\Controllers\Settings\RolesController;
use App\Http\Controllers\Settings\RoomsController;
use App\Http\Controllers\Settings\RecordTrackingController;
use App\Http\Middleware\CheckManagerRole;
use App\Http\Middleware\CheckReceptionistRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::patch('/settings/appearance/update', [AppearanceController::class, 'update'])->name('settings.appearance.update');
Route::get('/settings/appearance/get', [AppearanceController::class, 'getSettings'])->name('settings.appearance.get');

// Management Settings Routes
Route::middleware(['auth', 'verified', CheckManagerRole::class])->group(function () {
    Route::redirect('management/settings', 'management/settings/profile');

    Route::get('management/settings/roles', [RolesController::class, 'index'])->name('management.settings.roles.index');
    Route::patch('management/settings/roles/{role_id}', [RolesController::class, 'update'])->name('management.settings.roles.update');
    Route::post('management/settings/roles', [RolesController::class, 'store'])->name('management.settings.roles.store');
    Route::delete('management/settings/roles/{role_id}', [RolesController::class, 'destroy'])->name('management.settings.roles.destroy');

    Route::get('management/settings/rooms', [RoomsController::class, 'index'])->name('management.settings.rooms.index');
    Route::patch('management/settings/rooms/{room_id}', [RoomsController::class, 'update'])->name('management.settings.rooms.update');
    Route::post('management/settings/rooms', [RoomsController::class, 'store'])->name('management.settings.rooms.store');
    Route::delete('management/settings/rooms/{room_id}', [RoomsController::class, 'destroy'])->name('management.settings.rooms.destroy');

    Route::get('management/settings/profile', [ProfileController::class, 'edit'])->name('management.settings.profile.edit');
    Route::patch('management/settings/profile/verify-password', [ProfileController::class, 'verifyPassword'])->name('management.settings.profile.verify-password');
    Route::patch('management/settings/profile', [ProfileController::class, 'update'])->name('management.settings.profile.update');
    Route::delete('management/settings/profile', [ProfileController::class, 'destroy'])->name('management.settings.profile.destroy');
    Route::put('management/settings/password', [PasswordController::class, 'update'])->name('management.settings.password.update');
    Route::get('management/settings/password', [PasswordController::class, 'edit'])->name('management.settings.password.edit');
    Route::get('management/settings/activities', [RecordTrackingController::class, 'index'])->name('management.settings.activities.index');

    Route::get('management/settings/appearance', [AppearanceController::class, 'renderManagementAppearancePage'])->name('settings.appearance');
});

// Reception Settings Routes
Route::middleware(['auth', 'verified', CheckReceptionistRole::class])->group(function () {
    Route::get('reception/settings/profile', [ReceptionProfileController::class, 'edit'])->name('reception.settings.profile.edit');
    Route::patch('reception/settings/profile/verify-password', [ReceptionProfileController::class, 'verifyPassword'])->name('reception.settings.profile.verify-password');
    Route::patch('reception/settings/profile', [ReceptionProfileController::class, 'update'])->name('reception.settings.profile.update');
    Route::delete('reception/settings/profile', [ReceptionProfileController::class, 'destroy'])->name('reception.settings.profile.destroy');
    Route::get('reception/settings/password', [ReceptionPasswordController::class, 'edit'])->name('reception.settings.password.edit');
    Route::put('reception/settings/password', [ReceptionPasswordController::class, 'update'])->name('reception.settings.password.update');

    Route::get('reception/settings/appearance', [AppearanceController::class, 'renderReceptionAppearancePage'])->name('reception.settings.appearance');
});
