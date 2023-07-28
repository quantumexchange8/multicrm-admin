<?php

use App\Http\Controllers\DashboardController;
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

Route::middleware('auth')->group(function () {
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
        Route::get('/rebate_allocation', [MemberController::class, 'getrebateallocation'])->name('rebate_allocation');
        Route::post('/rebate_allocation', [MemberController::class, 'updateRebateAllocation'])->name('updateRebate.update');

        Route::post('/getIBAccountTypeSymbolGroupRate', [MemberController::class, 'getIBAccountTypeSymbolGroupRate']);
        Route::post('/upgradeIb', [MemberController::class, 'upgradeIb'])->name('member.upgradeIb');

        Route::post('/reset_member_password', [MemberController::class, 'reset_member_password'])->name('member.reset_member_password');
        Route::delete('/delete_member', [MemberController::class, 'delete_member'])->name('member.delete_member');
    });

    /**
     * ==============================
     *          IB Listing
     * ==============================
     */
    Route::prefix('ib')->group(function () {
        Route::get('/ib_listing', [IBController::class, 'ib_listing'])->name('ib.ib_listing');
    });

    /**
     * ==============================
     *    Trading Account Listing
     * ==============================
     */
    Route::get('/trading_account_listing', [MemberController::class, 'trading_account_listing'])->name('member.trading_account_listing');

    /**
     * ==============================
     *          Network Tree
     * ==============================
     */
    Route::get('/network_tree', [NetworkController::class, 'network_tree'])->name('member.network_tree');

});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

require __DIR__ . '/auth.php';
