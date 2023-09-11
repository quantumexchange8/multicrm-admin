<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalApprovalRequest;
use App\Models\GatewayExchangeRate;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\TradingUser;
use App\Models\User;
use App\Services\ChangeTraderBalanceType;
use App\Services\CTraderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        $payment = Payment::find($request->id);
        $paymentAccount = PaymentAccount::query()->where('account_no', $payment->account_no)->first();
        $currency = $payment->currency;

        $status = $request->status == "approve" ? "Processing" : "Rejected";
        $payment->status = $status;
        $payment->description = $request->comment;
        $payment->approval_date = Carbon::today();
        $payment->save();

        if ($payment->status == "Processing") {
            $currencyConfig = config('payout_setting');
            $payment_charges = null;
            $real_amount = $payment->amount;
            $exchange_rate =  GatewayExchangeRate::whereRelation('ofGateway', 'name', '=', 'ompay')
                ->where('base_currency', $currencyConfig[$currency]['currency'])
                ->where('target_currency', 'USD')
                ->where('status', 'Active')
                ->first();
            if ($exchange_rate) {

                switch ($exchange_rate->withdrawal_charge_type) {
                    case 'percentage': {
                        $payment_charges = $exchange_rate->withdrawal_charge_amount . '%';

                        $real_amount = number_format(($payment->amount * $exchange_rate->withdrawal_rate) * ((100 + $exchange_rate->withdrawal_charge_amount) / 100), 2, '.', '');
                        break;
                    }
                    case 'amount': {
                        $payment_charges = $currencyConfig[$currency]['currency'] . ' ' . $exchange_rate->withdrawal_charge_amount;
                        $real_amount = number_format(($payment->amount * $exchange_rate->withdrawal_rate) + $exchange_rate->withdrawal_charge_amount, 2, '.', '');
                        break;
                    }
                }
            }
            $payment->update([
                'real_amount' => $real_amount,
                'payment_charges' => $payment_charges,
            ]);
            $userRef = $payment->payment_id;
            $token = md5($currencyConfig[$currency]['agentCode'] . $userRef . $currencyConfig[$currency]['secretKey']);
            $callbackUrl = url('payout/callback');

            $postData = [
                'AgentCode' => $currencyConfig[$currency]['agentCode'],
                'UserRef' => $userRef,
                'Token' => $token,
                'TransactionId' => $payment->payment_id,
                'FullName' => $paymentAccount->payment_account_name,
                'AccountNo' => $payment->account_no,
                'BankCode' => $payment->account_type,
                'WithdrawType' => 2,
                'Amount' => $real_amount,
                'Remark' => $payment->description,
                'CallbackURL' => $callbackUrl,
                'Currency' => $currency,
            ];

            $response = \Http::post($currencyConfig[$currency]['domain'], $postData);
            \Log::debug($response->body());
            return redirect()->back()->with('toast', 'Successfully Updated Withdrawal Status');
        } else {
            $user = User::find($payment->user_id);
            $user->cash_wallet += $payment->amount;
            $user->save();
        }
        return redirect()->back()->with('toast', 'Successfully Rejected Withdrawal Request');
    }

    public function updateWithdrawalStatus(Request $request)
    {
        $data = $request->all();

        $result = [
            "Token" => $data['Token'],
            "TransactionId" => $data['TransactionId'],
            "StatusDesc" => $data["StatusDesc"],
            "StatusId" => $data["StatusId"],
            "FullName" => $data["FullName"],
            "AccountNo" => $data['AccountNo'],
            "Amount" => $data["Amount"],
        ];

        $paymentCurrency = Payment::query()->where('payment_id', $result['TransactionId'])->where('account_no', $result['AccountNo'])->first();
        $currency = $paymentCurrency->currency;
        $currencyConfig = config('payout_setting');
        $token = md5($currencyConfig[$currency]['agentCode'] . $data['TransactionId'] . $currencyConfig[$currency]['secretKey']);

        if ($result["Token"] == $token) {
            $payment = Payment::query()->where('payment_id', Str::upper($result['TransactionId']))->where('account_no', $result['AccountNo'])->first();
            if ($payment->status == "Processing") {
                if ($result['StatusId'] == 2) {
                    $payment->update([
                        'status' => 'Successful',
                    ]);
                } elseif ($result['StatusId'] == 3) {
                    $payment->update([
                        'status' => 'Rejected'
                    ]);

                    $user = User::find($payment->user_id);
                    $user->cash_wallet += $payment->amount;
                    $user->save();
                }
            }
        }
    }
}
