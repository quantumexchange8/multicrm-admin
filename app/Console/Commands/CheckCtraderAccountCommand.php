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
            $this->updateIbAccounts();
        } catch (\Exception $e) {
            \Log::error('Cronjob Updating got CTrader Error: ' . $e->getMessage());
        }
    }

    private function updateCTraderInfo(): void
    {
        $users = User::query()
            ->whereIn('role', ['ib', 'member'])
            ->where('remark', 'vietnam plan')
            ->get();

        foreach ($users as $user) {
            (new CTraderService)->getUserInfo($user->tradingUsers);
        }
    }

    private function updateIbAccounts(): void
    {
        $tradingAccounts = TradingAccount::whereHas('ofUser', function ($query) {
            $query->where('role', 'ib');
        })->get();
        $ibAccounts = IbAccountType::all();

        foreach ($tradingAccounts as $tradingAccount) {
            $ibAccount = $ibAccounts
                ->where('user_id', $tradingAccount->user_id)
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

    private function createIbAccount($tradingAccount): void
    {
        $user = $tradingAccount->ofUser;
        $ib = new IbAccountType;
        $ib->user_id = $tradingAccount->user_id;
        $ib->account_type = $tradingAccount->account_type;

        if ($user->upline_id) {
            $ib_upline = IbAccountType::where('user_id', $user->upline_id)
                ->where('account_type', $tradingAccount->account_type)
                ->first();

            if ($ib_upline) {
                $ib->upline_id = $ib_upline->id;
                $hierarchyList = $ib_upline->hierarchyList . $ib_upline->id . "-";
            } else {
                // If $ib_upline doesn't exist, create a new IbAccountType for it
                $newIbUpline = new IbAccountType;
                $newIbUpline->user_id = $user->upline_id;
                $newIbUpline->account_type = $tradingAccount->account_type;
                $newIbUpline->save();

                $ib->upline_id = $newIbUpline->id;
                $hierarchyList = "-" . $newIbUpline->id . "-";

                for ($i = 1; $i <= 7; $i++) {
                    IbAccountTypeSymbolGroupRate::create([
                        'ib_account_type' => $newIbUpline->id,
                        'symbol_group' => $i,
                        'amount' => 0,
                    ]);
                }
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
