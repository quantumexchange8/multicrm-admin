<?php

namespace App\Services\Data;

use App\Models\AccountType;
use App\Models\TradingAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateTradingAccount
{
    public function execute($meta_login, $data): TradingAccount
    {
        return $this->updateTradingAccount($meta_login, $data);
    }

    public function updateTradingAccount($meta_login, $data): ? TradingAccount
    {
        $tradingAccount = TradingAccount::query()->where('meta_login', $meta_login)->first();

        if (!empty($tradingAccount)) {
            $tradingUser = $tradingAccount->tradingUser;

            if (!empty($tradingUser)) {
                $accountType = AccountType::where('name', $tradingUser->meta_group)->first();

                if (!empty($accountType)) {
                    $tradingAccount->currency_digits = $data['moneyDigits'];
                    $tradingAccount->balance = $data['balance'] / 100;
                    $tradingAccount->credit = $data['nonWithdrawableBonus'] / 100;
                    $tradingAccount->bonus = $data['bonus'] / 100;
                    $tradingAccount->margin_leverage = $data['leverageInCents'] / 100;
                    $tradingAccount->equity = $data['equity'] / 100;
                    $tradingAccount->account_type = $accountType->id;

                    DB::transaction(function () use ($tradingAccount) {
                        $tradingAccount->save();
                    });

                    return $tradingAccount;
                } else {
                    \Log::debug('No account type');
                }
            } else {
                \Log::debug('No trading user');
            }
        }

        // Handle the case where $tradingAccount is not found
        // Log an error, throw an exception, or handle it as appropriate for your application.
        return null;
    }
}
