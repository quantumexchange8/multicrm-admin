<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\CTraderService;
use App\Services\MetaTrader5\Group\FetchGroupByPos;
use App\Services\MetaTrader5\Group\FetchTotalGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function trading_account_setting()
    {
        return Inertia::render('Setting/TradingAccountSetting');
    }

    public function refreshGroup()
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server'], 422);
            }
            return response()->json(['success' => false, 'message' => $conn['message']], 422);
        }
        $total_group = (new FetchTotalGroup)->execute();
        $groups = collect();
        for ($i = 0; $i < $total_group; $i++) {
            $group =  collect((new FetchGroupByPos)->execute($i));
            Group::firstOrCreate(['meta_group_name' => $group['Group']]);
        }

        return response()->json(['success' => true, 'message' => 'Successfully refresh group']);
    }

    public function getTradingAccountSettings(Request $request)
    {
        $groups = Group::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('display', 'like', '%' . $search . '%')
                        ->orWhere('value', 'like', '%' . $search . '%')
                        ->orWhere('meta_group_name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10);

        return response()->json([
            'groups' => $groups,
        ]);
    }
}
