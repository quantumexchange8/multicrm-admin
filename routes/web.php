<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InternalTransferController;
use App\Http\Controllers\PlatformConfigurationController;
use App\Http\Controllers\SettingController;
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
Route::post('payout/callback', [WithdrawalController::class, 'updateWithdrawalStatus']);

Route::middleware('auth')->middleware('role:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * ==============================
     *        Announcement
     * ==============================
     */
    Route::get('/announcement', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::get('/announcement/getAnnouncements', [AnnouncementController::class, 'getAnnouncements'])->name('announcement.getAnnouncements');
    Route::post('/create_announcement', [AnnouncementController::class, 'create_announcement'])->name('announcement.create_announcement');
    Route::post('/edit_announcement', [AnnouncementController::class, 'edit_announcement'])->name('announcement.edit_announcement');
    Route::delete('/delete_announcement', [AnnouncementController::class, 'delete_announcement'])->name('announcement.delete_announcement');

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
        Route::get('/getIbListing', [MemberController::class, 'getIbListing'])->name('member.getIbListing');
        Route::post('/rebate_allocation', [MemberController::class, 'updateRebateAllocation'])->name('member.updateRebate');
        Route::post('/rebate_structure', [MemberController::class, 'updateRebateStructure'])->name('member.updateRebateStructure');
        Route::post('/transfer_ib', [IBController::class, 'transfer_ib'])->name('member.transfer_ib');
        Route::get('/impersonate/{user}', [MemberController::class, 'impersonate'])->name('member.impersonate');

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
     *            Finance
     * ==============================
     */
    Route::prefix('finance')->group(function () {
        Route::get('/credit_amount_adjustment', [FinanceController::class, 'credit_amount_adjustment'])->name('finance.credit_amount_adjustment');
        Route::get('/getTradingAccounts', [FinanceController::class, 'getTradingAccounts'])->name('finance.getTradingAccounts');
        Route::post('/balance_adjustment', [FinanceController::class, 'balance_adjustment'])->name('finance.balance_adjustment');
        Route::post('/credit_adjustment', [FinanceController::class, 'credit_adjustment'])->name('finance.credit_adjustment');
        Route::get('/getBalanceHistory/{id}', [FinanceController::class, 'getBalanceHistory'])->name('finance.getBalanceHistory');
        Route::get('/getCreditHistory/{id}', [FinanceController::class, 'getCreditHistory'])->name('finance.getCreditHistory');

        Route::get('/payment_account_listing', [FinanceController::class, 'payment_account_listing'])->name('finance.payment_account_listing');
        Route::get('/getPaymentAccount', [FinanceController::class, 'getPaymentAccount'])->name('finance.getPaymentAccount');
        Route::patch('/update_payment_account', [FinanceController::class, 'update_payment_account'])->name('finance.update_payment_account');
        Route::delete('/delete_payment_account', [FinanceController::class, 'delete_payment_account'])->name('finance.delete_payment_account');

    });

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
        Route::get('/getDepositReport', [DepositController::class, 'getDepositReport'])->name('transaction.getDepositReport');
        Route::get('/getPendingTransaction', [WithdrawalController::class, 'getPendingTransaction'])->name('transaction.getPendingTransaction');
        Route::get('/getInternalTransferHistory', [InternalTransferController::class, 'getInternalTransferHistory'])->name('transaction.getInternalTransferHistory');
        Route::get('/getCashWalletTransactionHistory/{id}', [WalletController::class, 'getCashWalletTransactionHistory'])->name('transaction.getCashWalletTransactionHistory');
        Route::get('/getRebateWalletTransactionHistory/{id}', [WalletController::class, 'getRebateWalletTransactionHistory'])->name('transaction.getRebateWalletTransactionHistory');
    });

    /**
     * ==============================
     *    Platform Configuration
     * ==============================
     */
    Route::prefix('platform_configuration')->group(function () {
        Route::get('/ctrader', [PlatformConfigurationController::class, 'ctrader'])->name('platform_configuration.ctrader');
        Route::get('/getCTraderAccounts', [PlatformConfigurationController::class, 'getCTraderAccounts'])->name('platform_configuration.getCTraderAccounts');
        Route::post('/addAccountType', [PlatformConfigurationController::class, 'addAccountType'])->name('platform_configuration.addAccountType');
        Route::patch('/updateAccountType', [PlatformConfigurationController::class, 'updateAccountType'])->name('platform_configuration.updateAccountType');
        Route::delete('/updateAccountType', [PlatformConfigurationController::class, 'deleteAccountType'])->name('platform_configuration.deleteAccountType');
    });

    /**
     * ==============================
     *            Setting
     * ==============================
     */
    Route::prefix('setting')->group(function () {
//        Route::get('/trading_account_setting', [SettingController::class, 'trading_account_setting'])->name('setting.trading_account_setting');
//        Route::get('/refreshGroup', [SettingController::class, 'refreshGroup'])->name('setting.refreshGroup');
//        Route::get('/getTradingAccountSettings', [SettingController::class, 'getTradingAccountSettings'])->name('setting.getTradingAccountSettings');

        Route::get('/highlights_setting', [SettingController::class, 'highlights_setting'])->name('setting.highlights_setting');
        Route::post('/highlight/update_highlights', [SettingController::class, 'update_highlights'])->name('setting.update_highlights');
        Route::post('/highlight/upload-highlight-image', [SettingController::class, 'upload_highlight_image']);
        Route::post('/highlight/highlight-image-revert', [SettingController::class, 'revert_highlight_image']);
    });


});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

require __DIR__ . '/auth.php';
