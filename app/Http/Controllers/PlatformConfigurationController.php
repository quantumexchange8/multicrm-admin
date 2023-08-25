<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlatformConfigurationController extends Controller
{
    public function ctrader(Request $request)
    {
        return Inertia::render('PlatformConfiguration/Ctrader');
    }

    public function getCTraderAccounts(): \Illuminate\Http\JsonResponse
    {
        if (auth()->user()->remark == "vietnam plan") {
            $accountTypes = AccountType::where('id', 1)->paginate(10);
        } else {
            $accountTypes = AccountType::with('metaGroup')->paginate(10);
        }

        return response()->json($accountTypes);
    }
}
