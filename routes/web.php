<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Menu management
    Route::get('/menu', [AdminController::class, 'menuIndex'])->name('menu.index');
    Route::get('/menu/create', [AdminController::class, 'menuCreate'])->name('menu.create');
    Route::post('/menu', [AdminController::class, 'menuStore'])->name('menu.store');
    Route::get('/menu/{menu}', [AdminController::class, 'menuShow'])->name('menu.show');
    Route::get('/menu/{menu}/edit', [AdminController::class, 'menuEdit'])->name('menu.edit');
    Route::put('/menu/{menu}', [AdminController::class, 'menuUpdate'])->name('menu.update');
    Route::delete('/menu/{menu}', [AdminController::class, 'menuDestroy'])->name('menu.destroy');
    
    // Order management
    Route::get('/order', [AdminController::class, 'orderIndex'])->name('order.index');
    Route::get('/order/{order}', [AdminController::class, 'orderShow'])->name('order.show');
    Route::put('/order/{order}', [AdminController::class, 'orderUpdate'])->name('order.update');
    Route::delete('/order/{order}', [AdminController::class, 'orderDestroy'])->name('order.destroy');
    
    // Message management
    Route::get('/message', [AdminController::class, 'messageIndex'])->name('message.index');
    Route::get('/message/{message}', [AdminController::class, 'messageShow'])->name('message.show');
    Route::post('/message/{message}/reply', [AdminController::class, 'messageReply'])->name('message.reply');

    // Rute Laporan
    Route::get('/reports', [AdminController::class, 'reportIndex'])->name('report.index');
    Route::get('/reports/export', [AdminController::class, 'reportExport'])->name('report.export');

    // RUTE BARU: CRUD Pembelian
    Route::resource('/purchases', PurchaseController::class)->names('purchases');

    //Expense
    Route::get('/expense', [AdminController::class, 'expenseIndex'])->name('expense.index');
    Route::get('/expense/create', [AdminController::class, 'expenseCreate'])->name('expense.create');
    Route::post('/expense', [AdminController::class, 'expenseStore'])->name('expense.store');
    Route::delete('/expense/{expense}', [AdminController::class, 'expenseDestroy'])->name('expense.destroy');

    // 1. Rute Jurnal Umum
    Route::get('/journal', [AdminController::class, 'journalIndex'])->name('journal.index');
    
    // 2. Rute Buku Besar
    Route::get('/ledger', [AdminController::class, 'ledgerIndex'])->name('ledger.index');

    // Chart of account
    Route::resource('/chart-of-accounts', ChartOfAccountController::class)->names('chart_of_accounts');
});

// Customer routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    
    // Menu browsing
    Route::get('/menu', [CustomerController::class, 'menuIndex'])->name('menu.index');
    Route::get('/menu/{menu}', [CustomerController::class, 'menuShow'])->name('menu.show');
    
    // Order routes
    Route::get('/order', [CustomerController::class, 'orderIndex'])->name('order.index'); // Halaman shopping cart
    Route::get('/order/create', [CustomerController::class, 'orderCreate'])->name('order.create'); // Halaman checkout
    Route::post('/order', [CustomerController::class, 'orderStore'])->name('order.store'); // Store order
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders'); // Riwayat pesanan
    Route::get('/order/{order}', [CustomerController::class, 'orderShow'])->name('order.show'); // Detail pesanan
    Route::post('/order/{order}/reorder', [CustomerController::class, 'orderReorder'])->name('order.reorder'); // Pesan lagi
     
    // Payment routes
    Route::get('/order/{order}/payment', [CustomerController::class, 'orderPayment'])->name('order.payment');
    Route::post('/order/{order}/payment/proof', [CustomerController::class, 'paymentProofUpload'])->name('payment.proof.upload');
     
    // Midtrans routes
    Route::get('/order/{order}/midtrans', [CustomerController::class, 'orderMidtrans'])->name('order.midtrans');
    Route::get('/midtrans/finish', [CustomerController::class, 'midtransFinish'])->name('midtrans.finish');
    Route::get('/midtrans/unfinish', [CustomerController::class, 'midtransUnfinish'])->name('midtrans.unfinish');
    Route::get('/midtrans/error', [CustomerController::class, 'midtransError'])->name('midtrans.error');
    
    // Message management
    Route::get('/message', [CustomerController::class, 'messageIndex'])->name('message.index');
    Route::get('/message/create', [CustomerController::class, 'messageCreate'])->name('message.create');
    Route::post('/message', [CustomerController::class, 'messageStore'])->name('message.store');
    Route::get('/message/{message}', [CustomerController::class, 'messageShow'])->name('message.show');
});

Route::post('/midtrans/callback', [CustomerController::class, 'midtransCallback'])->name('midtrans.callback');