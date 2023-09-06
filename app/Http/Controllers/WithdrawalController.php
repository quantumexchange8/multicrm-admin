<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalApprovalRequest;
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

        $status = $request->status == "approve" ? "Processing" : "Rejected";
        $payment->status = $status;
        $payment->description = $request->comment;
        $payment->approval_date = Carbon::today();
        $payment->save();

        if ($payment->status == "Processing") {
            if ($payment->channel == 'bank') {
                if ($paymentAccount->currency == 'MYR') {
                    $url = 'https://payout.doitwallet.asia/api/wallet/Withdraw';
                    $agentCode = '93DD4A81-EDC2-48E9-BED4-AE6D208DCA47';
                    $userRef = $payment->payment_id;
                    $apiKey = '46B157AB13184B229A29E99A04508032';
                    $callbackUrl = url('withdrawal/updateWithdrawalStatus');
                    $token = md5($agentCode . $userRef . $apiKey);
                    // Data for the POST request
                    $postData = [
                        'AgentCode' => $agentCode,
                        'UserRef' => $userRef,
                        'Token' => $token,
                        'TransactionId' => $payment->payment_id,
                        'FullName' => $paymentAccount->payment_account_name,
                        'AccountNo' => $payment->account_no,
                        'BankCode' => $payment->account_type,
                        'WithdrawType' => 2,
                        'Amount' => $payment->amount,
                        'Remark' => $payment->description,
                        'CallbackURL' => $callbackUrl,
                        'Currency' => 'MYR',
                    ];

                    $response = \Http::post($url, $postData);

                    \Log::debug($response->body());
                } else {
                    $payment->update([
                        'status' => 'Successful'
                    ]);
                }
                return redirect()->back()->with('toast', 'Successfully Updated Withdrawal Status');
            } elseif ($payment->channel == 'crypto') {
                $payment->update([
                    'status' => 'Successful'
                ]);

                return redirect()->back()->with('toast', 'Successfully Approved Withdrawal Request');
            }
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
        \Log::debug($data);
        $agentCode = '93DD4A81-EDC2-48E9-BED4-AE6D208DCA47';
        $apiKey = '46B157AB13184B229A29E99A04508032';
        $token = md5($agentCode . $data['TransactionId'] . $apiKey);

        $result = [
            "Token" => $data['Token'],
            "TransactionId" => $data['TransactionId'],
            "StatusDesc" => $data["StatusDesc"],
            "StatusId" => $data["StatusId"],
            "FullName" => $data["FullName"],
            "AccountNo" => $data['AccountNo'],
            "Amount" => $data["Amount"],
        ];

        \Log::debug($result);

        if ($result["Token"] == $token) {
            $payment = Payment::query()->where('payment_id', Str::upper($result['TransactionId']))->where('account_no', $result['AccountNo'])->first();
            if ($payment->status == "Processing") {
                if ($result['StatusId'] == 2) {
                    $payment->update([
                        'status' => 'Successful',
                        'real_amount' => $data["Amount"]
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
