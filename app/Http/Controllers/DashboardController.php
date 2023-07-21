<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDeposit = Payment::where('type','=', 'Deposit')->where('status', '=', 'Successful')->sum('amount');
        $totalWithdrawal = Payment::where('type','=', 'Withdrawal')->where('status', '=', 'Successful')->sum('amount');

        return Inertia::render('Dashboard', [
            'totalDeposit' => $totalDeposit,
            'totalWithdrawal' => $totalWithdrawal,
        ]);
    }
}
