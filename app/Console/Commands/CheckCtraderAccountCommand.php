<?php

namespace App\Console\Commands;

use App\Models\IbAccountType;
use App\Models\IbAccountTypeSymbolGroupRate;
use App\Models\TradingAccount;
use App\Models\TradingUser;
use App\Models\User;
use App\Services\CTraderService;
use Illuminate\Console\Command;

class CheckCtraderAccountCommand extends Command
{
    protected $signature = 'check:ctrader-account';

    protected $description = 'Check CTrader Account on latest data';

    public function handle(): void
    {
        $conn = (new CTraderService)->connectionStatus();

        if ($conn['code'] != 0) {
            return; // No need to proceed if the connection is not established
        }

        try {
            $this->updateCTraderInfo();
        } catch (\Exception $e) {
            \Log::error('Cronjob Updating got CTrader Error: ' . $e->getMessage());
        }

        $this->updateIbAccounts();
    }

    protected function updateCTraderInfo(): void
    {
        $users = User::query()
            ->whereIn('role', ['ib', 'member'])
            ->where('remark', 'vietnam plan')
            ->get();

        foreach ($users as $user) {
            (new CTraderService)->getUserInfo($user->tradingUsers);
        }
    }

    protected function updateIbAccounts(): void
    {
        $tradingAccounts = TradingAccount::whereHas('ofUser', function ($query) {
            $query->where('role', 'ib');
        })->get();
        $ibAccounts = IbAccountType::all();

        foreach ($tradingAccounts as $tradingAccount) {
            $ibAccount = $ibAccounts
                ->where('user_id', $tradingAccount->user_id)
                ->where('account_type', $tradingAccount->account_type)
                ->first();

            if ($ibAccount) {
                if ($ibAccount->account_type !== $tradingAccount->account_type) {
                    $ibAccount->update([
                        'account_type' => $tradingAccount->account_type,
                    ]);
                }
            } else {
                $this->createIbAccount($tradingAccount);
            }
        }
    }

    protected function createIbAccount($tradingAccount): void
    {
        $user = $tradingAccount->ofUser;
        $ib = new IbAccountType;
        $ib->user_id = $tradingAccount->user_id;
        $ib->account_type = $tradingAccount->account_type;

        if ($user->upline_id) {
            $ib_upline = IbAccountType::where('user_id', $user->upline_id)->first();
            $ib->upline_id = $ib_upline->id;

            $hierarchyList = null;
            if (!$ib_upline->hierarchyList) {
                $hierarchyList = "-" . $ib_upline->id . "-";
            } else {
                $hierarchyList = $ib_upline->hierarchyList . $ib_upline->id . "-";
            }
            $ib->hierarchyList = $hierarchyList;
        }
        $ib->save();

        for ($i = 1; $i <= 7; $i++) {
            IbAccountTypeSymbolGroupRate::create([
                'ib_account_type' => $ib->id,
                'symbol_group' => $i,
                'amount' => 0,
            ]);
        }
    }
}
