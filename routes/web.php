<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RewardController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Customers
Route::resource('customers', CustomerController::class);
Route::post('customers/{customer}/loyalty', [CustomerController::class, 'manageLoyalty'])->name('customers.loyalty');

// Orders
Route::resource('orders', OrderController::class);
Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::post('orders/{order}/payment', [OrderController::class, 'addPayment'])->name('orders.add-payment');
Route::get('track', [OrderController::class, 'track'])->name('orders.track');

// Services
Route::resource('services', ServiceController::class);

// Rewards
Route::resource('rewards', RewardController::class)->except(['show']);
Route::post('rewards/{reward}/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');

// Financial Management
Route::prefix('financial')->name('financial.')->group(function () {
    Route::get('/', [FinancialController::class, 'index'])->name('index');
    Route::get('/transactions', [FinancialController::class, 'transactions'])->name('transactions');
    Route::get('/expenses', [FinancialController::class, 'expenses'])->name('expenses');
    Route::get('/expenses/create', [FinancialController::class, 'createExpense'])->name('create-expense');
    Route::post('/expenses', [FinancialController::class, 'storeExpense'])->name('store-expense');
    Route::get('/report', [FinancialController::class, 'report'])->name('report');
});

// Inventory Management
Route::resource('inventory', InventoryController::class);
Route::post('inventory/{inventory}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');
