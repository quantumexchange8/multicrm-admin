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
                $this->updateIbAccountType();
                $this->checkIbDownlineAccountType();
            }
        }
    }

    protected function createIbAccount($tradingAccount): void
    {
        $existingAccountType = IbAccountType::where('user_id', $tradingAccount->user_id)
            ->where('account_type', $tradingAccount->account_type)
            ->first();

        if (!$existingAccountType) {
            $user = $tradingAccount->ofUser;
            $ib = new IbAccountType;
            $ib->user_id = $tradingAccount->user_id;
            $ib->account_type = $tradingAccount->account_type;
            if ($user->upline_id) {
                $ib_upline = IbAccountType::where('user_id', $user->upline_id)->where('account_type', $tradingAccount->account_type)->first();

                if ($ib_upline) {
                    $ib->upline_id = $ib_upline->id;

                    $hierarchyList = null;
                    if (!$ib_upline->hierarchyList) {
                        $hierarchyList = "-" . $ib_upline->id . "-";
                    } else {
                        $hierarchyList = $ib_upline->hierarchyList . $ib_upline->id . "-";
                    }
                    $ib->hierarchyList = $hierarchyList;
                } else {
                    // Create a new IbAccountType for the ib upline
                    $newIbUpline = new IbAccountType;
                    $newIbUpline->user_id = $user->upline_id;
                    $newIbUpline->account_type = $tradingAccount->account_type;

                    $newIbUpline->upline_id = null;
                    $newIbUpline->hierarchyList = null;

                    $newIbUpline->save();

                    $ib->upline_id = null;
                    $ib->hierarchyList = null;

                    // Create IbAccountTypeSymbolGroupRate records for the new IbAccountType
                    for ($i = 1; $i <= 7; $i++) {
                        IbAccountTypeSymbolGroupRate::create([
                            'ib_account_type' => $newIbUpline->id,
                            'symbol_group' => $i,
                            'amount' => 0,
                        ]);
                    }
                }
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

    protected function updateIbAccountType(): void
    {
        $ib_accounts = IbAccountType::all();

        foreach ($ib_accounts as $ib) {
            $user = User::find($ib->user_id);

            if ($user->upline_id) {
                $ib_upline = IbAccountType::where('user_id', $user->upline_id)->where('account_type', $ib->account_type)->first();

                if ($ib_upline) {

                    if (!$ib_upline->hierarchyList) {
                        $hierarchyList = "-" . $ib_upline->id . "-";
                    } else {
                        $hierarchyList = $ib_upline->hierarchyList . $ib_upline->id . "-";
                    }

                    $ib->update([
                        'upline_id' => $ib_upline->id,
                        'hierarchyList' => $hierarchyList,
                    ]);
                }
            }
        }
    }

    protected function checkIbDownlineAccountType(): void
    {
        $ibs = IbAccountType::all();

        foreach ($ibs as $ib) {
            $user = User::find($ib->user_id);
            $user_downline_ids = $user->getIbChildrenIds();

            foreach ($user_downline_ids as $user_downline_id) {
                // Check if there's an entry with the same user_id and a different account_type
                $existingIbAccount = IbAccountType::where('user_id', $user_downline_id)
                    ->where('account_type', $ib->account_type)
                    ->first();

                if (!$existingIbAccount) {
                    // Create a new row with the correct account_type
                    $newIb = IbAccountType::create([
                        'user_id' => $user_downline_id,
                        'account_type' => $ib->account_type,
                    ]);

                    for ($i = 1; $i <= 7; $i++) {
                        IbAccountTypeSymbolGroupRate::create([
                            'ib_account_type' => $newIb->id,
                            'symbol_group' => $i,
                            'amount' => 0,
                        ]);
                    }
                }
            }
        }
    }
}
