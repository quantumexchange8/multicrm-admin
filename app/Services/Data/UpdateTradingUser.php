<?php

namespace App\Services\Data;

use App\Models\AccountType;
use App\Models\TradingUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateTradingUser
{
    public function execute($meta_login, $data): TradingUser
    {
        return $this->updateTradingUser($meta_login, $data);
    }

    public function updateTradingUser($meta_login, $data): TradingUser
    {
        $tradingUser = TradingUser::query()->where('meta_login', $meta_login)->first();

        if (!empty($tradingUser)) {
            $accountType = AccountType::where('name', $data['groupName'])->first();

            if (!empty($accountType)) {
                $tradingUser->meta_group = $data['groupName'];
                $tradingUser->leverage = $data['leverageInCents'] / 100;
                $tradingUser->registration = $data['registrationTimestamp'];
                $tradingUser->last_access = $data['lastUpdateTimestamp'];
                $tradingUser->balance = $data['balance'] / 100;
                $tradingUser->credit = $data['nonWithdrawableBonus'] / 100;
                $tradingUser->bonus = $data['bonus'] / 100;
                $tradingUser->account_type = $accountType->id;

                DB::transaction(function () use ($tradingUser) {
                    $tradingUser->save();
                });
            } else {
                \Log::debug('Account type not found');
            }
        }

        return $tradingUser;
    }
}
