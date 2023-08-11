<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletAdjustmentRequest;
use App\Models\Payment;
use App\Models\User;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function wallet_report(Request $request)
    {
        $search = $request->search;
        $role = $request->role;

        $users = User::query()->whereIn('role', ['member', 'ib'])->with('userIb');

        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $users->where('role', $role);
        }

        $users = $users->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Transaction/WalletReport', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function wallet_adjustment(WalletAdjustmentRequest $request)
    {
        $amount = floatval($request->amount);

        $user = User::find($request->id);
        $balance = $user->cash_wallet + $amount;

        if ($balance < 0 || $amount == 0) {
            throw ValidationException::withMessages(['amount' => 'Insufficient balance']);
        } else {
            $user->cash_wallet += $amount;
            $user->save();
            $payment_id = RunningNumberService::getID('transaction');
            Payment::create([
                'handleBy' => Auth::id(),
                'user_id' => $user->id,
                'payment_id' => $payment_id,
                'category' => 'wallet_adjustment',
                'type' => 'WalletAdjustment',
                'amount' => $amount,
                'status' => 'Successful',
                'description' => $request->comment,
            ]);
            return redirect()->back()->with('toast', 'Successfully Adjusted Cash Wallet');
        }
    }

    public function getCashWalletTransactionHistory(Request $request, $user_id)
    {
        $transactions = Payment::whereHas('ofUser', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->whereNot('type', '=', 'AccountToAccount')
            ->whereNot('status', '=', 'Submitted')
            ->whereNot('category', '=', 'rebate_payout')
            ->when($request->filled('type'), function ($query) use ($request) {
                $type = $request->input('type');
                $query->where(function ($innerQuery) use ($type) {
                    $innerQuery->where('type', $type);
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $query->whereDate('created_at', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($transactions);
    }

    public function getRebateWalletTransactionHistory(Request $request, $user_id)
    {
        $transactions = Payment::whereHas('ofUser', function ($query) use ($user_id) {
                $query->where('id', $user_id);
            })
            ->whereNot('status', '=', 'Submitted')
            ->where('category', '=', 'rebate_payout')
            ->when($request->filled('type'), function ($query) use ($request) {
                $type = $request->input('type');
                $query->where(function ($innerQuery) use ($type) {
                    $innerQuery->where('type', $type);
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $query->whereDate('created_at', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($transactions);
    }
}
