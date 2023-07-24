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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * ==============================
     *          Member Listing
     * ==============================
     */
    Route::prefix('member')->group(function () {
        Route::get('/member_listing', [MemberController::class, 'MemberListing'])->name('member_listing');
    });
     
    /**
     * ==============================
     *          IB Listing
     * ==============================
     */
    Route::prefix('ib')->group(function () {
        Route::get('/ib_listing', [IBController::class, 'IBListing'])->name('ib_listing');
    });

    /**
     * ==============================
     *          Trading Account Listing
     * ==============================
     */
    Route::get('/trading_account_listing', [MemberController::class, 'TradingAccountListing'])->name('Trading_Account_Listing');

    /**
     * ==============================
     *         Network Tree
     * ==============================
     */
    Route::get('/network_tree', [NetworkController::class, 'Network'])->name('Network_Tree');

});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

require __DIR__ . '/auth.php';
