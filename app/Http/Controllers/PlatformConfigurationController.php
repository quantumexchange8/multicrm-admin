<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountTypeRequest;
use App\Models\AccountType;
use App\Models\AccountTypeSymbolGroup;
use App\Models\Group;
use App\Models\SettingLeverage;
use App\Models\SymbolGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlatformConfigurationController extends Controller
{
    public function ctrader(Request $request)
    {
        $leverages = SettingLeverage::query()
            ->get()
            ->pluck('leverage', 'value');

        $groups = Group::query()
            ->whereNotNull('display')
            ->get()
            ->pluck('display', 'id');

        return Inertia::render('PlatformConfiguration/Ctrader', [
            'leverages' => $leverages,
            'groups' => $groups,
        ]);
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

    public function addAccountType(AccountTypeRequest $request): \Illuminate\Http\RedirectResponse
    {
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : null;
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : null;
        $accountType =  AccountType::create([
            'name' => $request->name,
            'group' => $request->group,
            'minimum_deposit' => $request->minimum_deposit,
            'leverage' => $request->leverage,
            'currency' => $request->currency,
            'allow_create_account' => $request->allow_create_account,
            'type' => 'dollar',
            'commission_structure' => $request->commission_structure,
            'trade_open_duration' => $request->trade_open_duration,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'show_register' => false,
        ]);
        $symbolGroups = SymbolGroup::all();
        foreach ($symbolGroups as $row) {
            AccountTypeSymbolGroup::create(['account_type' => $accountType->id, 'symbol_group' => $row['id']]);
        }

        return redirect()->back()->with('toast', 'The new account type has been created successfully!');
    }

    public function updateAccountType(AccountTypeRequest $request): \Illuminate\Http\RedirectResponse
    {
        $accountType = AccountType::find($request->id);
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : null;
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : null;
        $accountType->update([
            'name' => $request->name,
            'group' => $request->group,
            'minimum_deposit' => $request->minimum_deposit,
            'leverage' => $request->leverage,
            'currency' => $request->currency,
            'allow_create_account' => $request->allow_create_account,
            'commission_structure' => $request->commission_structure,
            'trade_open_duration' => $request->trade_open_duration,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);

        return redirect()->back()->with('toast', 'The account type details has been saved successfully!');
    }

    public function deleteAccountType(Request $request): \Illuminate\Http\RedirectResponse
    {
        $accountType = AccountType::find($request->id);
        $accountType->delete();

        $accountTypeSymbolGroup = AccountTypeSymbolGroup::where('account_type', $request->id)->get();
        foreach ($accountTypeSymbolGroup as $accountGroup) {
            $accountGroup->delete();
        }

        return redirect()->back()->with('toast', 'The account type details has been deleted successfully!');
    }
}
