<?php

use App\Http\Controllers\Settings\AppearanceController;

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ReceptionPasswordController;
use App\Http\Controllers\Settings\ManagementPasswordController;
use App\Http\Controllers\Settings\RestaurantManagementPasswordController;
use App\Http\Controllers\Settings\HouseKeepingManagementPasswordController;
use App\Http\Controllers\Settings\AccountingPasswordController;
use App\Http\Controllers\Settings\RestaurantTillPasswordController;
use App\Http\Controllers\Settings\HouseKeepingPasswordController;

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\AccountingProfileController;
use App\Http\Controllers\Settings\ManagementProfileController;
use App\Http\Controllers\Settings\RestaurantManagementProfileController;
use App\Http\Controllers\Settings\HouseKeepingManagementProfileController;
use App\Http\Controllers\Settings\ReceptionProfileController;
use App\Http\Controllers\Settings\RestaurantTillProfileController;
use App\Http\Controllers\Settings\HouseKeepingProfileController;

use App\Http\Controllers\Settings\RoomTypesController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\DutiesController;
use App\Http\Controllers\Settings\RoomsController;
use App\Http\Controllers\Settings\RecordTrackingController;
use App\Http\Middleware\CheckRoles;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::patch('/settings/appearance/update', [AppearanceController::class, 'update'])->name('settings.appearance.update');
Route::get('/settings/appearance/get', [AppearanceController::class, 'getSettings'])->name('settings.appearance.get');

// Management Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':manager'])->group(function () {
    Route::redirect('management/settings', 'management/settings/profile');

    Route::get('management/settings/rooms', [RoomsController::class, 'index'])->name('management.settings.rooms.index');
    Route::patch('management/settings/rooms/{room_id}', [RoomsController::class, 'update'])->name('management.settings.rooms.update');
    Route::post('management/settings/rooms', [RoomsController::class, 'store'])->name('management.settings.rooms.store');
    Route::delete('management/settings/rooms/{room_id}', [RoomsController::class, 'destroy'])->name('management.settings.rooms.destroy');

    Route::get('management/settings/currencies', [CurrencyController::class, 'index'])->name('management.settings.currencies.index');
    Route::patch('management/settings/currencies/{currency_id}', [CurrencyController::class, 'update'])->name('management.settings.currencies.update');
    Route::post('management/settings/currencies', [CurrencyController::class, 'store'])->name('management.settings.currencies.store');
    Route::delete('management/settings/currencies/{currency_id}', [CurrencyController::class, 'destroy'])->name('management.settings.currencies.destroy');

    Route::get('management/settings/room_types', [RoomTypesController::class, 'index'])->name('management.settings.room_types.index');
    Route::patch('management/settings/room_types/{room_type_id}', [RoomTypesController::class, 'update'])->name('management.settings.room_types.update');
    Route::post('management/settings/room_types', [RoomTypesController::class, 'store'])->name('management.settings.room_types.store');
    Route::delete('management/settings/room_types/{room_type_id}', [RoomTypesController::class, 'destroy'])->name('management.settings.room_types.destroy');

    Route::get('management/settings/profile', [ManagementProfileController::class, 'edit'])->name('management.settings.profile.edit');
    Route::patch('management/settings/profile/verify-password', [ManagementProfileController::class, 'verifyPassword'])->name('management.settings.profile.verify-password');
    Route::patch('management/settings/profile', [ManagementProfileController::class, 'update'])->name('management.settings.profile.update');
    Route::delete('management/settings/profile', [ManagementProfileController::class, 'destroy'])->name('management.settings.profile.destroy');
    Route::put('management/settings/password', [ManagementPasswordController::class, 'update'])->name('management.settings.password.update');
    Route::get('management/settings/password', [ManagementPasswordController::class, 'edit'])->name('management.settings.password.edit');
    Route::get('management/settings/activities', [RecordTrackingController::class, 'index'])->name('management.settings.activities.index');

    Route::get('management/settings/appearance', [AppearanceController::class, 'renderManagementAppearancePage'])->name('management.settings.appearance');
});

// House Keeping Management Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':housekeeping_manager'])->group(function () {
    Route::redirect('housekeeping/management/settings', 'housekeeping/management/settings/profile');

    Route::get('housekeeping/management/settings/duties', [DutiesController::class, 'index'])->name('housekeeping.management.settings.duties.index');
    Route::patch('housekeeping/management/settings/duties/{role_id}', [DutiesController::class, 'update'])->name('housekeeping.management.settings.duties.update');
    Route::post('housekeeping/management/settings/duties', [DutiesController::class, 'store'])->name('housekeeping.management.settings.duties.store');
    Route::delete('housekeeping/management/settings/duties/{role_id}', [DutiesController::class, 'destroy'])->name('housekeeping.management.settings.duties.destroy');

    Route::get('housekeeping/management/settings/profile', [HouseKeepingManagementProfileController::class, 'edit'])->name('housekeeping.management.settings.profile.edit');
    Route::patch('housekeeping/management/settings/profile/verify-password', [HouseKeepingManagementProfileController::class, 'verifyPassword'])->name('housekeeping.management.settings.profile.verify-password');
    Route::patch('housekeeping/management/settings/profile', [HouseKeepingManagementProfileController::class, 'update'])->name('housekeeping.management.settings.profile.update');
    Route::delete('housekeeping/management/settings/profile', [HouseKeepingManagementProfileController::class, 'destroy'])->name('housekeeping.management.settings.profile.destroy');
    Route::put('housekeeping/management/settings/password', [HouseKeepingManagementPasswordController::class, 'update'])->name('housekeeping.management.settings.password.update');
    Route::get('housekeeping/management/settings/password', [HouseKeepingManagementPasswordController::class, 'edit'])->name('housekeeping.management.settings.password.edit');

    Route::get('housekeeping/management/settings/appearance', [AppearanceController::class, 'renderHouseKeepingManagementAppearancePage'])->name('housekeeping.management.settings.appearance');
});

// Restaurant Management Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':restaurant_manager'])->group(function () {
    Route::redirect('restaurant/management/settings', 'restaurant/management/settings/profile');

    Route::get('restaurant/management/settings/profile', [RestaurantManagementProfileController::class, 'edit'])->name('restaurant.management.settings.profile.edit');
    Route::patch('restaurant/management/settings/profile/verify-password', [RestaurantManagementProfileController::class, 'verifyPassword'])->name('restaurant.management.settings.profile.verify-password');
    Route::patch('restaurant/management/settings/profile', [RestaurantManagementProfileController::class, 'update'])->name('restaurant.management.settings.profile.update');
    Route::delete('restaurant/management/settings/profile', [RestaurantManagementProfileController::class, 'destroy'])->name('restaurant.management.settings.profile.destroy');
    Route::put('restaurant/management/settings/password', [RestaurantManagementPasswordController::class, 'update'])->name('restaurant.management.settings.password.update');
    Route::get('restaurant/management/settings/password', [RestaurantManagementPasswordController::class, 'edit'])->name('restaurant.management.settings.password.edit');

    Route::get('restaurant/management/settings/appearance', [AppearanceController::class, 'renderRestaurantManagementAppearancePage'])->name('restaurant.management.settings.appearance');
});

// Accounting Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':accounting'])->group(function () {
    Route::redirect('accounting/settings', 'accounting/settings/profile');

    Route::get('accounting/settings/profile', [AccountingProfileController::class, 'edit'])->name('accounting.settings.profile.edit');
    Route::patch('accounting/settings/profile/verify-password', [AccountingProfileController::class, 'verifyPassword'])->name('accounting.settings.profile.verify-password');
    Route::patch('accounting/settings/profile', [AccountingProfileController::class, 'update'])->name('accounting.settings.profile.update');
    Route::delete('accounting/settings/profile', [AccountingProfileController::class, 'destroy'])->name('accounting.settings.profile.destroy');
    Route::put('accounting/settings/password', [AccountingPasswordController::class, 'update'])->name('accounting.settings.password.update');
    Route::get('accounting/settings/password', [AccountingPasswordController::class, 'edit'])->name('accounting.settings.password.edit');

    Route::get('accounting/settings/appearance', [AppearanceController::class, 'renderRestaurantManagementAppearancePage'])->name('accounting.settings.appearance');
});

// Reception Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':reception'])->group(function () {
    Route::get('reception/settings/profile', [ReceptionProfileController::class, 'edit'])->name('reception.settings.profile.edit');
    Route::patch('reception/settings/profile/verify-password', [ReceptionProfileController::class, 'verifyPassword'])->name('reception.settings.profile.verify-password');
    Route::patch('reception/settings/profile', [ReceptionProfileController::class, 'update'])->name('reception.settings.profile.update');
    Route::delete('reception/settings/profile', [ReceptionProfileController::class, 'destroy'])->name('reception.settings.profile.destroy');
    Route::get('reception/settings/password', [ReceptionPasswordController::class, 'edit'])->name('reception.settings.password.edit');
    Route::put('reception/settings/password', [ReceptionPasswordController::class, 'update'])->name('reception.settings.password.update');

    Route::get('reception/settings/appearance', [AppearanceController::class, 'renderReceptionAppearancePage'])->name('reception.settings.appearance');
});

// Restaurant Till Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':restaurant_till'])->group(function () {
    Route::get('restaurant/settings/profile', [RestaurantTillProfileController::class, 'edit'])->name('restaurant.settings.profile.edit');
    Route::patch('restaurant/settings/profile/verify-password', [RestaurantTillProfileController::class, 'verifyPassword'])->name('restaurant.settings.profile.verify-password');
    Route::patch('restaurant/settings/profile', [RestaurantTillProfileController::class, 'update'])->name('restaurant.settings.profile.update');
    Route::delete('restaurant/settings/profile', [RestaurantTillProfileController::class, 'destroy'])->name('restaurant.settings.profile.destroy');
    Route::get('restaurant/settings/password', [RestaurantTillPasswordController::class, 'edit'])->name('restaurant.settings.password.edit');
    Route::put('restaurant/settings/password', [RestaurantTillPasswordController::class, 'update'])->name('restaurant.settings.password.update');

    Route::get('restaurant/settings/appearance', [AppearanceController::class, 'renderReceptionAppearancePage'])->name('restaurant.settings.appearance');
});

// House Keeping Settings Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':housekeeper'])->group(function () {
    Route::get('housekeeping/settings/profile', [HouseKeepingProfileController::class, 'edit'])->name('housekeeping.settings.profile.edit');
    Route::patch('housekeeping/settings/profile/verify-password', [HouseKeepingProfileController::class, 'verifyPassword'])->name('housekeeping.settings.profile.verify-password');
    Route::patch('housekeeping/settings/profile', [HouseKeepingProfileController::class, 'update'])->name('housekeeping.settings.profile.update');
    Route::delete('housekeeping/settings/profile', [HouseKeepingProfileController::class, 'destroy'])->name('housekeeping.settings.profile.destroy');
    Route::get('housekeeping/settings/password', [HouseKeepingPasswordController::class, 'edit'])->name('housekeeping.settings.password.edit');
    Route::put('housekeeping/settings/password', [HouseKeepingPasswordController::class, 'update'])->name('housekeeping.settings.password.update');

    Route::get('housekeeping/settings/appearance', [AppearanceController::class, 'renderReceptionAppearancePage'])->name('housekeeping.settings.appearance');
});
