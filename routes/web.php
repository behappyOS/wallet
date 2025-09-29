<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('dashboard');

    Route::get('/deposit', [WalletController::class, 'showDeposit'])->name('wallet.deposit');
    Route::post('/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit.submit');

    Route::get('/transfer', [WalletController::class, 'showTransfer'])->name('wallet.transfer');
    Route::post('/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer.submit');

    Route::patch('/transactions/{transaction}/reverse', [WalletController::class, 'reverse'])
        ->name('wallet.reverse');
});
