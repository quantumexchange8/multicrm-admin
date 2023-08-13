<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalApprovalRequest;
use App\Models\Payment;
use App\Models\TradingUser;
use App\Models\User;
use App\Services\ChangeTraderBalanceType;
use App\Services\CTraderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    public function withdrawal_report(Request $request)
    {
        return Inertia::render('Transaction/WithdrawalReport');
    }

    public function getPendingTransaction(Request $request)
    {
        $search = $request->search;
        $requestDate = $request->date;
        $type = $request->type;

        $withdrawals = Payment::query()->with('ofUser')
            ->where('category', 'payment')
            ->where('type', 'Withdrawal');

        if (auth()->user()->remark == "vietnam plan") {
            $withdrawals =  $withdrawals->whereRelation('ofUser', 'remark', "vietnam plan");
        }

        if ($type) {
            $withdrawals =  $withdrawals->where('channel', $type);
        }

        if ($requestDate) {
            $start_date = Carbon::createFromFormat('Y-m-d', $requestDate[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $requestDate[1])->endOfDay();
            $withdrawals->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($search) {
            $withdrawals->whereRelation('ofUser', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $histories = clone $withdrawals;
        $withdrawals =  $withdrawals
            ->where('status', 'Submitted')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $histories =  $histories
            ->whereNot('status', 'Submitted')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'withdrawals' => $withdrawals,
            'histories' => $histories
        ]);
    }

    public function withdrawal_approval(WithdrawalApprovalRequest $request)
    {
        $conn = (new CTraderService)->connectionStatus();
            if ($conn['code'] != 0) {
                if ($conn['code'] == 10) {
                    return response()->json(['success' => false, 'message' => 'No connection with cTrader Server']);
                }
                return response()->json(['success' => false, 'message' => $conn['message']]);
            }

            $payment = Payment::query()->where('id', $request->id)->first();

            if ($payment->status == "Submitted") {
                $status = $request->status == "approve" ? "Successful" : "Rejected";
                $payment->status = $status;
                $payment->description = $request->comment;
                $payment->approval_date = Carbon::today();
                $payment->save();

                if ($status == "Successful") {

                    if ($payment->category == "payment") {
                        if ($payment->type == "Deposit") {
                            try {

                                $trade = (new CTraderService)->createTrade($payment->to, $payment->amount, $payment->comment, ChangeTraderBalanceType::DEPOSIT);
                                $payment->ticket = $trade->getTicket();
                                $payment->save();

                                $user = User::find($payment->user_id);
                                $user->total_deposit += $payment->amount;
                                $user->save();
                            } catch (\Throwable $e) {
                                if ($e->getMessage() == "Not found") {
                                    TradingUser::firstWhere('meta_login', $payment->to)->update(['acc_status' => 'Inactive']);
                                } else {
                                    Log::error($e->getMessage());
                                }
                                return response()->json(['success' => false, 'message' => $e->getMessage()]);
                            }
                            /*  $follow = MmFollower::where('status', 'Approve')->where('end_date', NULL)->where('meta_login', $payment->to)->first();
                            if ($follow) {
                                $mm = MmProfile::find($follow->mm_id);
                             $trade = (new CTraderService)->createTrade($mm->meta_login, $payment->amount, $payment->to . ' Deposit', ChangeTraderBalanceType::DEPOSIT);
                        } */
                        } else if ($payment->type == "Withdrawal") {
                        } else return response()->json(['success' => false, 'message' => "Invalid payment type"]);
                    } else if ($payment->category == "internal transfer") {

                        if ($payment->type == "WalletToMeta") {
                            try {
                                $trade = (new CTraderService)->createTrade($payment->to, $payment->amount, $payment->comment, ChangeTraderBalanceType::DEPOSIT);
                            } catch (\Throwable $e) {
                                if ($e->getMessage() == "Not found") {
                                    TradingUser::firstWhere('meta_login', $payment->to)->update(['acc_status' => 'Inactive']);
                                } else {
                                    Log::error($e->getMessage());
                                }
                                return response()->json(['success' => false, 'message' => $e->getMessage()]);
                            }
                            $user = User::find($payment->user_id);
                            $user->cash_wallet -= $payment->amount;
                            $user->save();
                            $payment->ticket = $trade->getTicket();
                            $payment->save();
                        } else if ($payment->type == "MetaToWallet") {
                            try {
                                $trade = (new CTraderService)->createTrade($payment->from, $payment->amount, $payment->comment, ChangeTraderBalanceType::WITHDRAW);
                            } catch (\Throwable $e) {
                                if ($e->getMessage() == "Not found") {
                                    TradingUser::firstWhere('meta_login', $payment->from)->update(['acc_status' => 'Inactive']);
                                } else {
                                    Log::error($e->getMessage());
                                }
                                return response()->json(['success' => false, 'message' => $e->getMessage()]);
                            }
                            $user = User::find($payment->user_id);
                            $user->cash_wallet += $payment->amount;
                            $user->save();
                            $payment->ticket = $trade->getTicket();
                            $payment->save();
                        } else if ($payment->type == "MetaToMeta") {
                            try {
                                $trade_1 = (new CTraderService)->createTrade($payment->from, $payment->amount, $payment->comment, ChangeTraderBalanceType::WITHDRAW);
                            } catch (\Throwable $e) {
                                if ($e->getMessage() == "Not found") {
                                    TradingUser::firstWhere('meta_login', $payment->from)->update(['acc_status' => 'Inactive']);
                                } else {
                                    Log::error($e->getMessage());
                                }
                                return response()->json(['success' => false, 'message' => $e->getMessage()]);
                            }
                            try {
                                $trade_2 = (new CTraderService)->createTrade($payment->to, $payment->amount, $payment->comment, ChangeTraderBalanceType::DEPOSIT);
                            } catch (\Throwable $e) {
                                if ($e->getMessage() == "Not found") {
                                    TradingUser::firstWhere('meta_login', $payment->to)->update(['acc_status' => 'Inactive']);
                                } else {
                                    Log::error($e->getMessage());
                                }
                                return response()->json(['success' => false, 'message' => $e->getMessage()]);
                            }
                            $payment->ticket = $trade_1->getTicket() . ', ' . $trade_2->getTicket();
                            $payment->save();
                        }
                    } else return response()->json(['success' => false, 'message' => "Invalid payment category"]);;
                    return redirect()->back()->with('toast', 'Successfully Updated');
                } else {
                    if ($payment->category == "payment") {
                        if ($payment->type == "Withdrawal") {
                            $user = User::find($payment->user_id);
                            $user->cash_wallet += $payment->amount;
                            $user->save();
                        }
                    }
                }
                return redirect()->back()->with('toast', 'Successfully Updated');
            }
            return response()->json(['success' => false, 'message' => "Invalid status"], 422);
    }
}
