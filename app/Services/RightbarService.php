<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Str;

class RightbarService
{
    protected function getPaymentQuery($category, $type, $status)
    {
        return Payment::query()
            ->where('category', $category)
            ->where('type', $type)
            ->where('status', $status)
            ->whereDate('created_at', now())
            ->sum('amount');
    }
    public function getTotalApprovedDeposit()
    {
        return $this->getPaymentQuery('payment', 'Deposit', 'Successful');
    }

    public function getTotalPendingDeposit()
    {
        return $this->getPaymentQuery('payment', 'Deposit', 'Submitted');
    }
    public function getTotalApprovedWithdrawal()
    {
        return $this->getPaymentQuery('payment', 'Withdrawal', 'Successful');
    }
    public function getTotalPendingWithdrawal()
    {
        return $this->getPaymentQuery('payment', 'Withdrawal', 'Submitted');
    }
}
