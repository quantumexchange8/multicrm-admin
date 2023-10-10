<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditRequest;
use App\Http\Requests\FundRequest;
use App\Http\Requests\PaymentAccountRequest;
use App\Models\FundAdjustment;
use App\Models\PaymentAccount;
use App\Models\SettingCountry;
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

        return redirect()->back()->with('toast', trans('public.Successfully Updated Balance'));
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
                \Log::error($e->getMessage());
            }
            return redirect()->back()->withErrors('Something Went Wrong, ' . $e->getMessage());
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

        return redirect()->back()->with('toast', trans('public.Successfully Updated Credit'));
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

    public function payment_account_listing()
    {
        return Inertia::render('Finance/PaymentAccountListing', [
            'countries' => SettingCountry::all('id', 'name_en'),
        ]);
    }

    public function getPaymentAccount(Request $request): \Illuminate\Http\JsonResponse
    {
        $paymentAccounts = PaymentAccount::query()
            ->with(['user', 'media'])
            ->when($request->filled('type'), function ($query) use ($request) {
                $type = $request->input('type');
                $query->where(function ($innerQuery) use ($type) {
                    $innerQuery->where('payment_platform', $type);
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                        ->orWhere('account_no', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return response()->json($paymentAccounts);
    }

    public function update_payment_account(PaymentAccountRequest $request)
    {
        $paymentAccount = PaymentAccount::find($request->payment_account_id);

        $paymentAccount->update([
            'payment_platform_name' => $request->payment_platform_name,
            'bank_branch_address' => $request->bank_branch_address,
            'payment_account_name' => $request->payment_account_name,
            'account_no' => $request->account_no,
            'bank_swift_code' => $request->bank_swift_code,
            'bank_code_type' => $request->bank_code_type,
            'bank_code' => $request->bank_code,
            'country' => $request->country,
            'currency' => $request->currency,
        ]);

        $successMessage = $paymentAccount->payment_platform === 'bank'
            ? trans('public.The bank account details has been edited successfully!')
            : trans('public.The cryptocurrency wallet details has been edited successfully!');

        return redirect()->back()->with('toast', $successMessage);
    }

    public function delete_payment_account(Request $request)
    {
        $paymentAccount = PaymentAccount::find($request->id);

        $paymentAccount->delete();

        return redirect()->back()->with('toast', trans('public.The payment account has been deleted successfully'));
    }
}
