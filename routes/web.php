<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletTransactionController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:withdraw wallet')->group(function () {
        Route::get('/dashboard/wallet', [DashboardController::class, 'wallet'])->name('dashboard.wallet');
        Route::get('/dashboard/wallet/withdraw', [DashboardController::class, 'withdraw_wallet'])->name('dashboard.wallet.withdraw');
        Route::post('/dashboard/wallet/withdraw/store', [DashboardController::class, 'withdraw_wallet_store'])->name('dashboard.wallet.withdraw.store');
    });

    Route::middleware('can:topup wallet')->group(function () {
        Route::get('/dashboard/wallet/topup', [DashboardController::class, 'topup_wallet'])->name('dashboard.wallet.topup');
        Route::post('/dashboard/wallet/topup/store', [DashboardController::class, 'topup_wallet_store'])->name('dashboard.wallet.topup.store');
    });

    Route::middleware('can:apply job')->group(function () {
        Route::get('/apply/{project:slug}', [FrontController::class, 'apply_job'])->name('front.apply_job');
        Route::post('/apply/{project:slung}/submit', [FrontController::class, 'applay_job_store'])->name('front.apply_job.store');
        Route::get('/dashboard/proposals', [DashboardController::class, 'proposals'])->name('dashboard.proposals');
        Route::get('/dashboard/proposal_details/{project}/{projectApplicant}', [DashboardController::class, 'proposal_details'])->name('dashboard.proposal_details');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('can:manage wallets')->group(function () {
            Route::get('/wallet/topups', [WalletTransactionController::class, 'wallet_topups'])->name('topups');
            Route::get('/wallet/withdrawals', [WalletTransactionController::class, 'wallet_withdrawals'])->name('withdrawals');
            Route::resource('wallet_transactions', WalletTransactionController::class);
        });
    });

});

require __DIR__.'/auth.php';
