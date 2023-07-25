<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\TradingAccount;
use Illuminate\Support\Facades\Auth;
use App\Models\IbAccountType;
use App\Models\AccountType;
use App\Models\IbAccountTypeSymbolGroupRate;
use App\Models\RebateAllocation;
use App\Models\RebateAllocationRate;

class MemberController extends Controller
{
    
    public function MemberListing(Request $request)
    {

        $search = $request->input('search');

        if($search)
        {
            
            $members = User::where('role', 'member')
                        ->where(function ($query) use ($search) {
                            $query->where('first_name', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%");
                    })
                   ->with(['tradingAccounts'])
                   ->get();
        }
        else {
            
            $members = User::where('role', 'member')->with(['tradingAccounts'])->get();
        }

        // dd($members);

        return Inertia::render('Member/MemberListing', [
            'members' => $members,
        ]);
    }

    public function TradingAccountListing()
    {

        $tradingAccs = TradingAccount::with(['ofUser', 'accountType'])->get();

        return Inertia::render('AccountListing/TradingAccountListing', [
            'tradingAccs' => $tradingAccs,
        ]);
    }

    public function getrebateallocation()
    {
        $user = Auth::user();
        $search = \Request::input('search');

        $ibs = IbAccountType::where('user_id', $user->id)->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType'])->first();

        $childrenIds = $ibs ->getIbChildrenIds();

        $query = IbAccountType::whereIn('id', $childrenIds)
            ->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType']);
        

        if ($search) {
            $query->whereHas('ofUser', function ($userQuery) use ($search) {
                $userQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $childrens = $query->get();

        $childrens->each(function ($child) {
            $profilePicUrl = $child->ofUser->getFirstMediaUrl('profile_photo');
            $child->profile_pic = $profilePicUrl;
        });

        

        return Inertia::render('Member/RebateAllocation', [
            'ibs' => $ibs,
            'childrens' => $childrens,
            'filters' => \Request::only(['search'])
        ]);
    }

    public function updateRebateAllocation(Request $request)
    {
        $curIb = IbAccountType::find($request->user_id);
        $upline = IbAccountType::where('user_id', Auth::id())->first();
        $downline = $curIb->downline;
        $curIbRate = IbAccountTypeSymbolGroupRate::where('ib_account_type', $request->user_id)->get()->keyBy('symbol_group');

        foreach ($request->symbolGroupItems as $key => $amount) {
            $parent = IbAccountTypeSymbolGroupRate::with('symbolGroup')->where('ib_account_type', $upline->id)->where('symbol_group', $key)->first();
            if ($parent && $amount > $parent->amount) {
                throw ValidationException::withMessages([
                    'symbolGroupItems' => [$key => 'Invalid Amount for ' . $parent->symbolGroup->name],
                ]);
            }
        }

        foreach ($downline as $child) {
            foreach ($request->symbolGroupItems as $key => $amount) {
                $childRate = IbAccountTypeSymbolGroupRate::with('symbolGroup')->where('ib_account_type', $child->id)->where('symbol_group', $key)->first();
                if ($amount < $childRate->amount) {
                    return response()->json([
                        'invalidAmount' => 'Invalid Amount for ' . $childRate->symbolGroup->name
                    ]);
                }
            }
        }

        $rebateAllocation = RebateAllocation::create(['from' => $curIb->upline_id, 'to' => $request->user_id]);

        foreach ($request->symbolGroupItems as $key => $amount) {

            RebateAllocationRate::create([
                'allocation_id' => $rebateAllocation->id,
                'symbol_group' => $key,
                'old' => $curIbRate[$key]->amount,
                'new' => $amount
            ]);

            $curIbRate[$key]->update(['amount' => $amount]);

        }

        return back()->with('toast', 'The rebate allocation has been saved!');
    }

}
