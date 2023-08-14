<?php

namespace App\Http\Controllers;

use App\Http\Requests\FundRequest;
use App\Models\FundAdjustment;
use App\Models\TradingAccount;
use App\Models\TradingUser;
use App\Services\ChangeTraderBalanceType;
use App\Services\CTraderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function credit_amount_adjustment()
    {
        return Inertia::render('Finance/CreditAmountAdjustment', [
            'status' => session('session')
        ]);
    }

    public function getTradingAccounts(Request $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] == 0) {
            try {
                $tradingUsers = TradingUser::where('acc_status', 'Active')->where('remarks', 'vietnam plan')->get();
                (new CTraderService)->getUserInfo($tradingUsers);
            } catch (\Exception $e) {
                \Log::error('CTrader Error');
            }
        }
        $tradingAccounts = TradingAccount::query()
            ->with(['ofUser.upline', 'accountType', 'tradingUser'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->whereHas('ofUser', function ($user_query) use ($search) {
                    $user_query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('user_id')
            ->paginate(10);

        return response()->json($tradingAccounts);
    }

    public function balance_adjustment(FundRequest $request)
    {
        $trade = null;

        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return redirect()->back()->withErrors('No Connection with CTrader');
            }
            return redirect()->back()->withErrors('Something Went Wrong');
        }
        try {
            if ($request->type === 'deposit') {
                $trade = (new CTraderService)->createTrade($request->account_no, $request->amount, $request->comment, ChangeTraderBalanceType::DEPOSIT);
            } elseif ($request->type === 'withdrawal') {
                $trade = (new CTraderService)->createTrade($request->account_no, $request->amount, $request->comment, ChangeTraderBalanceType::WITHDRAW);
            }
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return redirect()->back()->withErrors('Something Went Wrong!');
        }

        if ($trade) {
            FundAdjustment::create([
                'user_id' => $request->user_id,
                'to' => $request->account_no,
                'type' => $request->type,
                'amount' => $request->amount,
                'comment' => $request->comment,
                'ticket' => $trade->getTicket()
            ]);
        } else {
            return redirect()->back()->withErrors('Something Went Wrong..');
        }

        return redirect()->back()->with('toast', 'Successfully Updated Balance');
    }

    public function getBalanceHistory(Request $request, $meta_login)
    {
        $balance_histories = FundAdjustment::query()
            ->where('to', $meta_login)
            ->whereIn('type', ['deposit', 'withdrawal'])
            ->when($request->filled('type'), function ($query) use ($request) {
                $type = $request->input('type');
                $query->where(function ($innerQuery) use ($type) {
                    $innerQuery->where('type', $type);
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $dateRange = explode(' ~ ', $date);
                $start_date = Carbon::createFromFormat('Y-m-d', $dateRange[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $dateRange[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->latest()
            ->paginate(5);

        return response()->json($balance_histories);
    }
}
