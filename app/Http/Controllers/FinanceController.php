<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditRequest;
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
                $tradingUsers = TradingUser::where('acc_status', 'Active')->where('remarks', 'vietnam plan')->whereNot('module', 'pamm')->get();
                (new CTraderService)->getUserInfo($tradingUsers);
            } catch (\Exception $e) {
                \Log::error('CTrader Error');
            }
        }
        $tradingAccounts = TradingAccount::query()
            ->with(['ofUser.upline', 'accountType', 'tradingUser'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('ofUser', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                        ->orWhere('meta_login', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('user_id')
            ->paginate(10);

        return response()->json($tradingAccounts);
    }

    public function balance_adjustment(FundRequest $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return redirect()->back()->withErrors('No Connection with CTrader');
            }
            return redirect()->back()->withErrors('Something Went Wrong');
        }
        $changeType = ($request->type === 'deposit') ? ChangeTraderBalanceType::DEPOSIT : ChangeTraderBalanceType::WITHDRAW;

        try {
            $trade = (new CTraderService)->createTrade($request->account_no, $request->amount, $request->comment, $changeType);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return redirect()->back()->withErrors('Something Went Wrong!');
        }

        FundAdjustment::create([
            'user_id' => $request->user_id,
            'to' => $request->account_no,
            'type' => $request->type,
            'amount' => $request->amount,
            'comment' => $request->comment,
            'status' => 'completed',
            'handle_by' => Auth::id(),
            'ticket' => $trade->getTicket()
        ]);

        return redirect()->back()->with('toast', 'Successfully Updated Balance');
    }

    public function credit_adjustment(CreditRequest $request)
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return redirect()->back()->withErrors('No Connection with CTrader');
            }
            return redirect()->back()->withErrors('Something Went Wrong');
        }
        $changeType = ($request->type === 'credit_in') ? ChangeTraderBalanceType::DEPOSIT_NONWITHDRAWABLE_BONUS : ChangeTraderBalanceType::WITHDRAW_NONWITHDRAWABLE_BONUS;

        try {
            $trade = (new CTraderService)->createTrade($request->account_no, $request->amount, $request->internal_description, $changeType);
        } catch (\Throwable $e) {
            if ($e->getMessage() == "Not found") {
                TradingUser::firstWhere('meta_login', $request->account_no)->update(['acc_status' => 'Inactive']);
            } else {
                Log::error($e->getMessage());
            }
            return redirect()->back()->withErrors('Something Went Wrong!');
        }

        $comment = ($request->type === 'credit_in') ? 'Credit In' : 'Credit Out';
        $status = ($request->allotted_time === 0) ? 'completed' : 'running';

        FundAdjustment::create([
            'user_id' => $request->user_id,
            'to' => $request->account_no,
            'type' => $request->type,
            'amount' => $request->amount,
            'comment' => $comment,
            'internal_description' => $request->internal_description,
            'client_description' => $request->client_description,
            'allotted_time' => $request->allotted_time,
            'start_date' => Carbon::parse($request->start_date),
            'expiry_date' => Carbon::parse($request->end_date),
            'status' => $status,
            'handle_by' => Auth::id(),
            'ticket' => $trade->getTicket()
        ]);

        return redirect()->back()->with('toast', 'Successfully Updated Credit');
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

    public function getCreditHistory(Request $request, $meta_login)
    {
        $credit_histories = FundAdjustment::query()
            ->where('to', $meta_login)
            ->whereIn('type', ['credit_in', 'credit_out'])
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

        return response()->json($credit_histories);
    }
}
