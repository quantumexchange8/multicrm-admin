<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\InternalTransferController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WithdrawalController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\IBController;
use App\Http\Controllers\NetworkController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->middleware('role:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * ==============================
     *        Member Listing
     * ==============================
     */
    Route::prefix('member')->group(function () {
        Route::get('/member_listing', [MemberController::class, 'member_listing'])->name('member.member_listing');
        Route::patch('/member_update', [MemberController::class, 'member_update'])->name('member.member_update');
        Route::post('/upgradeIb', [MemberController::class, 'upgradeIb'])->name('member.upgradeIb');

        Route::get('/member_tree', [NetworkController::class, 'member_tree'])->name('member.member_tree');

        Route::get('/rebate_allocation', [MemberController::class, 'rebate_allocation'])->name('member.rebate_allocation');
        Route::post('/rebate_allocation', [MemberController::class, 'updateRebateAllocation'])->name('member.updateRebate');
        Route::post('/rebate_structure', [MemberController::class, 'updateRebateStructure'])->name('member.updateRebateStructure');
        Route::post('/transfer_ib', [IBController::class, 'transfer_ib'])->name('member.transfer_ib');

        //Rebate Payout
        Route::get('/rebate_payout', [MemberController::class, 'rebate_payout'])->name('member.rebate_payout');
        Route::post('/getRebatePayoutDetails', [MemberController::class, 'getRebatePayoutDetails'])->name('member.getRebatePayoutDetails');
        Route::post('/approve_rebate_payout', [MemberController::class, 'approve_rebate_payout'])->name('member.approve_rebate_payout');

        Route::post('/getIBAccountTypeSymbolGroupRate', [MemberController::class, 'getIBAccountTypeSymbolGroupRate']);
        Route::post('/getNewIbRebateInfo', [MemberController::class, 'getNewIbRebateInfo']);
        Route::post('/getIbDownlineRebateInfo', [MemberController::class, 'getIbDownlineRebateInfo']);

        Route::post('/reset_member_password', [MemberController::class, 'reset_member_password'])->name('member.reset_member_password');
        Route::delete('/delete_member', [MemberController::class, 'delete_member'])->name('member.delete_member');
    });

    /**
     * ==============================
     *    Trading Account Listing
     * ==============================
     */
    Route::get('/trading_account_listing', [MemberController::class, 'trading_account_listing'])->name('member.trading_account_listing');

    /**
     * ==============================
     *         Transaction
     * ==============================
     */
    Route::prefix('transaction')->group(function () {
        Route::get('/deposit_report', [DepositController::class, 'deposit_report'])->name('transaction.deposit_report');
        Route::get('/withdrawal_report', [WithdrawalController::class, 'withdrawal_report'])->name('transaction.withdrawal_report');
        Route::post('/withdrawal_approval', [WithdrawalController::class, 'withdrawal_approval'])->name('transaction.withdrawal_approval');
        Route::get('/internal_transfer_report', [InternalTransferController::class, 'internal_transfer_report'])->name('transaction.internal_transfer_report');
        Route::get('/wallet_report', [WalletController::class, 'wallet_report'])->name('transaction.wallet_report');
        Route::post('/wallet_adjustment', [WalletController::class, 'wallet_adjustment'])->name('transaction.wallet_adjustment');
        Route::get('/getInternalTransferHistory', [InternalTransferController::class, 'getInternalTransferHistory'])->name('transaction.getInternalTransferHistory');
        Route::get('/getCashWalletTransactionHistory/{id}', [WalletController::class, 'getCashWalletTransactionHistory'])->name('transaction.getCashWalletTransactionHistory');
        Route::get('/getRebateWalletTransactionHistory/{id}', [WalletController::class, 'getRebateWalletTransactionHistory'])->name('transaction.getRebateWalletTransactionHistory');
    });

});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

require __DIR__ . '/auth.php';
