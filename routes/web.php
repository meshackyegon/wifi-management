<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MobileMoneyController;
use App\Http\Controllers\VoucherController;

Route::get('/', function () {
  return redirect()->route('voucher.buy');
})->name('home');

// Public voucher purchase routes
Route::get('/buy-voucher', [MobileMoneyController::class, 'showPaymentForm'])->name('voucher.buy');
Route::get('/buy-voucher/{plan}', [MobileMoneyController::class, 'showPaymentForm'])->name('voucher.buy.plan');

// Mobile Money Payment Routes
Route::prefix('mobile-money')->name('mobile-money.')->group(function () {
    Route::post('/initiate', [MobileMoneyController::class, 'initiatePayment'])->name('initiate');
    Route::post('/check-status', [MobileMoneyController::class, 'checkPaymentStatus'])->name('check-status');
    Route::post('/callback/{provider}', [MobileMoneyController::class, 'handleCallback'])->name('callback');
    Route::post('/cash-payment', [MobileMoneyController::class, 'processCashPayment'])->name('cash-payment');
});

// M-Pesa Callback Routes (for NGrok)
Route::prefix('mpesa')->name('mpesa.')->group(function () {
    Route::post('/stk-callback', [App\Http\Controllers\MpesaCallbackController::class, 'stkCallback'])->name('stk-callback');
    Route::post('/validation', [App\Http\Controllers\MpesaCallbackController::class, 'validation'])->name('validation');
    Route::post('/confirmation', [App\Http\Controllers\MpesaCallbackController::class, 'confirmation'])->name('confirmation');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    
    // Mobile Money Management
    Route::prefix('mobile-money')->name('mobile-money.')->group(function () {
        Route::get('/history', [MobileMoneyController::class, 'paymentHistory'])->name('history');
        Route::post('/retry/{payment}', [MobileMoneyController::class, 'retryPayment'])->name('retry');
        Route::get('/stats', [MobileMoneyController::class, 'getPaymentStats'])->name('stats');
        Route::get('/export', [MobileMoneyController::class, 'exportPayments'])->name('export');
        
        // Cash Payment Management (Admin only)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/cash-payments', [MobileMoneyController::class, 'cashPayments'])->name('cash-payments');
            Route::post('/cash-payments/{payment}/approve', [MobileMoneyController::class, 'approveCashPayment'])->name('approve-cash');
            Route::post('/cash-payments/{payment}/reject', [MobileMoneyController::class, 'rejectCashPayment'])->name('reject-cash');
        });
    });
    
    // Voucher Management
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::get('/create', [VoucherController::class, 'create'])->name('create');
        Route::post('/generate', [VoucherController::class, 'generate'])->name('generate');
        Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show');
        Route::post('/print', [VoucherController::class, 'print'])->name('print');
        Route::post('/mark-printed', [VoucherController::class, 'markPrinted'])->name('mark-printed');
        Route::post('/bulk-generate', [VoucherController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::get('/export/{format}', [VoucherController::class, 'export'])->name('export');
        Route::get('/stats', [VoucherController::class, 'getStats'])->name('stats');
        Route::post('/{voucher}/send-sms', [VoucherController::class, 'sendSms'])->name('send-sms');
    });
    
    // Voucher Plans (Admin only)
    Route::middleware(['role:admin'])->prefix('voucher-plans')->name('voucher-plans.')->group(function () {
        Volt::route('/', 'voucher-plans.index')->name('index');
        Volt::route('/create', 'voucher-plans.create')->name('create');
        Volt::route('/{plan}/edit', 'voucher-plans.edit')->name('edit');
    });
    
    // Router Management (Admin only)
    Route::middleware(['role:admin'])->prefix('routers')->name('routers.')->group(function () {
        Volt::route('/', 'routers.index')->name('index');
        Volt::route('/create', 'routers.create')->name('create');
        Volt::route('/{router}/edit', 'routers.edit')->name('edit');
        Volt::route('/{router}/test', 'routers.test')->name('test');
    });
    
    // User Management (Admin only)
    Route::middleware(['role:admin'])->prefix('users')->name('users.')->group(function () {
        Volt::route('/', 'users.index')->name('index');
        Volt::route('/create', 'users.create')->name('create');
        Volt::route('/{user}/edit', 'users.edit')->name('edit');
    });
    
    // SMS Management
    Route::prefix('sms')->name('sms.')->group(function () {
        Volt::route('/', 'sms.index')->name('index');
        Volt::route('/send', 'sms.send')->name('send');
        Volt::route('/logs', 'sms.logs')->name('logs');
    });
    
    // Reports (Admin and Agent)
    Route::middleware(['role:admin|agent'])->prefix('reports')->name('reports.')->group(function () {
        Volt::route('/', 'reports.index')->name('index');
        Volt::route('/revenue', 'reports.revenue')->name('revenue');
        Volt::route('/vouchers', 'reports.vouchers')->name('vouchers');
        Volt::route('/commissions', 'reports.commissions')->name('commissions');
    });
    
    // Settings
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    
    // Admin Settings
    Route::middleware(['role:admin'])->group(function () {
        Volt::route('settings/system', 'settings.system')->name('settings.system');
        Volt::route('settings/mobile-money', 'settings.mobile-money')->name('settings.mobile-money');
        Volt::route('settings/sms', 'settings.sms')->name('settings.sms');
    });
});

require __DIR__ . '/auth.php';
