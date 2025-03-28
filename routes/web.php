<?php

use App\Http\Controllers\management\ManagementUsersController;
use App\Http\Controllers\restaurant\restaurant_management\MenuItermsController;
use App\Http\Controllers\accounting\AccountingPaymentsController;
use App\Http\Controllers\management\BookingsController as ManagementBookingsController;
use App\Http\Controllers\reception\BookingsController as ReceptionBookingsController;
use App\Http\Controllers\reception\ReceptionUsersController;
use App\Http\Controllers\reception\AvailableRoomsController;
use App\Http\Middleware\CheckRoles;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Management Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':manager'])->group(function () {
    Route::get('management/dashboard', function () {
        return Inertia::render('management/dashboard');
    })->name('management.dashboard');

    Route::get('management/bookings', [ManagementBookingsController::class, 'index'])->name('management.bookings');

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

// Restaurant Management Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':restaurant_manager'])->group(function () {
    Route::get('restaurant/management/dashboard', function () {
        return Inertia::render('restaurant/management/dashboard');
    })->name('restaurant.management.dashboard');

    Route::get('restaurant/management/our_menu', function () {
        return Inertia::render('restaurant/management/our_menu');
    })->name('restaurant.management.our_menu');

    Route::middleware('auth')->group(function () {
        Route::redirect('restaurant/management/menu_iterms', 'restaurant/management/menu_iterms/list');

        Route::get('/restaurant/management/menu_iterms/list', [MenuItermsController::class, 'index'])->name('restaurant.management.menu_iterms.menu_list');
        Route::post('/restaurant/management/menu_iterms', [MenuItermsController::class, 'store'])->name('restaurant.management.menu_iterms.add');
        Route::get('/restaurant/management/menu_iterms/add', [MenuItermsController::class, 'create'])->name('restaurant.management.menu_iterms.add');
        Route::get('/restaurant/management/menu_iterms/{user}', [MenuItermsController::class, 'show'])->name('restaurant.management.menu_iterms.show');
    });

    Route::get('restaurant/management/sales', function () {
        return Inertia::render('restaurant/management/sales');
    })->name('restaurant.management.sales');

    Route::get('restaurant/management/user_info', function () {
        return Inertia::render('restaurant/management/user_info');
    })->name('restaurant.management.user_info');
});

// HouseKeeping Management Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':housekeeping_manager'])->group(function () {
    Route::get('housekeeping/management/dashboard', function () {
        return Inertia::render('housekeeping/management/dashboard');
    })->name('housekeeping.management.dashboard');

    Route::get('housekeeping/management/housekeepers', function () {
        return Inertia::render('housekeeping/management/housekeepers');
    })->name('housekeeping.management.housekeepers');

    Route::get('housekeeping/management/duties', function () {
        return Inertia::render('housekeeping/management/duties');
    })->name('housekeeping.management.duties');

    Route::get('housekeeping/management/active_duties', function () {
        return Inertia::render('housekeeping/management/active_duties');
    })->name('housekeeping.management.active_duties');

    Route::get('housekeeping/management/user_info', function () {
        return Inertia::render('housekeeping/management/user_info');
    })->name('housekeeping.management.user_info');
});

// Accounting Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':accounting'])->group(function () {
    Route::get('accounting/dashboard', function () {
        return Inertia::render('accounting/dashboard');
    })->name('accounting.dashboard');

    Route::middleware('auth')->group(function () {
        Route::redirect('accounting/payments', 'users/list');

        Route::get('/accounting/payments/list', [AccountingPaymentsController::class, 'index'])->name('accounting.payments.payments_list');
        Route::post('/accounting/payments/pending', [AccountingPaymentsController::class, 'pending'])->name('accounting.payments.pending');
        Route::get('/accounting/payments/approve', [AccountingPaymentsController::class, 'approve'])->name('accounting.payments.approve');
        Route::get('/accounting/payment/{payment}', [AccountingPaymentsController::class, 'show'])->name('accounting.payments.show');
    });

    Route::get('accounting/general_ledger', function () {
        return Inertia::render('accounting/general_ledger');
    })->name('accounting.general_ledger');

    Route::get('accounting/accounts_payables', function () {
        return Inertia::render('accounting/accounts_payables');
    })->name('accounting.accounts_payables');

    Route::get('accounting/accounts_receivables', function () {
        return Inertia::render('accounting/accounts_receivables');
    })->name('accounting.accounts_receivables');

    Route::get('accounting/invoice_management', function () {
        return Inertia::render('accounting/invoice_management');
    })->name('accounting.invoice_management');

    Route::get('accounting/financial_reporting', function () {
        return Inertia::render('accounting/financial_reporting');
    })->name('accounting.financial_reporting');

    Route::get('accounting/user_info', function () {
        return Inertia::render('accounting/user_info');
    })->name('accounting.user_info');
});

// Reception Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':reception'])->group(function () {
    Route::get('reception/dashboard', function () {
        return Inertia::render('reception/dashboard');
    })->name('reception.dashboard');

    Route::get('reception/bookings', [ReceptionBookingsController::class, 'index'])->name('reception.bookings');
    Route::post('reception/book', [ReceptionBookingsController::class, 'store'])->name('reception.bookings.store');

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

        Route::get('reception/users/list', [ManagementUsersController::class, 'index'])->name('reception.users.users_list');
        Route::post('/users', [ManagementUsersController::class, 'store'])->name('reception.users.add_users');
        Route::get('reception/users/add', [ManagementUsersController::class, 'create'])->name('reception.users.add_users');
        Route::get('reception/users/activate', function () {
            return Inertia::render('reception/users/verify_users');
        })->name('reception.users.verify_users');
        Route::get('reception/users/{user}', [ManagementUsersController::class, 'show'])->name('reception.users.users.show');
        Route::get('reception/users/suggestions', [ReceptionUsersController::class, 'suggestions'])->name('reception.users.suggestions');
    });

    Route::get('reception/user_info', function () {
        return Inertia::render('reception/user_info');
    })->name('reception.user_info');

    Route::get('/reception/available-rooms', [AvailableRoomsController::class, 'filter']);
});

// Restaurant Till Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':restaurant_till'])->group(function () {
    Route::get('restaurant/dashboard', function () {
        return Inertia::render('restaurant/dashboard');
    })->name('restaurant.dashboard');

    Route::get('restaurant/pos', function () {
        return Inertia::render('restaurant/pos');
    })->name('restaurant.pos');

    Route::get('restaurant/orders', function () {
        return Inertia::render('restaurant/orders');
    })->name('restaurant.orders');

    Route::get('restaurant/user_info', function () {
        return Inertia::render('restaurant/user_info');
    })->name('restaurant.user_info');
});

// HouseKeeping Management Routes
Route::middleware(['auth', 'verified', CheckRoles::class . ':housekeeper'])->group(function () {
    Route::get('housekeeping/dashboard', function () {
        return Inertia::render('housekeeping/dashboard');
    })->name('housekeeping.dashboard');

    Route::get('housekeeping/duties', function () {
        return Inertia::render('housekeeping/duties');
    })->name('housekeeping.duties');

    Route::get('housekeeping/user_info', function () {
        return Inertia::render('housekeeping/user_info');
    })->name('housekeeping.user_info');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
