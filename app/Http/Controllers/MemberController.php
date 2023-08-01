<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\ResetMemberPasswordRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\AccountType;
use App\Models\AccountTypeSymbolGroup;
use App\Models\IbAccountType;
use App\Models\IbAccountTypeSymbolGroupRate;
use App\Models\RebateAllocation;
use App\Models\RebateAllocationRate;
use App\Models\SettingCountry;
use App\Models\TradingAccount;
use App\Models\User;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class MemberController extends Controller
{

    public function member_listing(Request $request)
    {
        $members = User::query()
            ->where('role', '=', 'member')
            ->with(['tradingAccounts', 'media'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        $countries = SettingCountry::query()
            ->select(['id', 'name_en', 'phone_code'])
            ->get();

        $accountTypes = AccountType::where('id', 1)->first();

        return Inertia::render('Member/MemberListing', [
            'members' => $members,
            'countries' => $countries,
            'accountTypes' => $accountTypes,
        ]);
    }

    public function member_update(UpdateMemberRequest $request)
    {
        $user = User::find($request->user_id);

        $user->update([
            'first_name' => $request->first_name,
            'chinese_name' => $request->chinese_name,
            'dob' => $request->dob,
            'country' => $request->country,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('toast', 'The member’s detail has been edited successfully!');

    }

    public function getIBAccountTypeSymbolGroupRate(Request $request)
    {
        $user = User::find($request->id);

        $upline = $user->upline_id;
        if ($upline) {
            $rates = IbAccountTypeSymbolGroupRate::whereRelation('ibAccountType', 'user_id', '=', $user->upline_id)
                ->whereRelation('ibAccountType', 'account_type', '=', $request->account_type)
                ->with('symbolGroup')
                ->get();
        } else {
            $rates = AccountTypeSymbolGroup::where('account_type', $request->account_type)
                ->with('symbolGroup')
                ->get();
        }

        return $rates;
    }

    public function upgradeIb(Request $request)
    {
        $user = User::find($request->id);

        if ($user->role == 'member') {
            $ib_id = RunningNumberService::getID('broker_id');

            $user->ib_id = $ib_id;
            $user->role = 'ib';
            $user->assignRole('ib');
            $user->save();

            $upline = $user->upline;
            if ($upline) {
                $upline->increment('direct_ib');
                $upline->decrement('direct_client');
                while ($upline) {
                    $upline->increment('total_ib');
                    $upline->decrement('total_client');
                    $upline->save();
                    $upline = $upline->upline;
                }
            }
        }

        $exists = IbAccountType::where('user_id', $request->id)->where('account_type', $request->account_type)->exists();
        if (!$exists) {
            $ib = new IbAccountType;
            $ib->user_id = $request->id;
            $ib->account_type = $request->account_type;
            if ($user->upline_id) {
                $ib_upline = IbAccountType::where('user_id', $user->upline_id)->where('account_type', $request->account_type)->first();
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

            $symbolGroups = $request->ibGroupRates;
            foreach ($symbolGroups as $id => $value) {

                IbAccountTypeSymbolGroupRate::create([
                    'ib_account_type' => $ib->id,
                    'symbol_group' => $id,
                    'amount' => $value,
                ]);
            }
        } else {
            return response(['success' => false, 'message' => 'Already have this account type']);
        }
        return redirect()->back()->with('toast', 'New IB Created');
    }

    public function reset_member_password(ResetMemberPasswordRequest $request)
    {
        $user = User::find($request->user_id);
        $validated = $request->validated();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('toast', 'The member portal password has been reset successfully');
    }

    public function delete_member(Request $request)
    {
        $user = User::find($request->user_id);

        $user->delete();

        return redirect()->back()->with('toast', 'The member has been deleted successfully');
    }

    public function trading_account_listing()
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

        $ibs = IbAccountType::where('user_id', $user->id)->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType', 'ofUser.upline'])->first();

        // $childrenIds = $ibs ->getIbChildrenIds();
        
        // $query = IbAccountType::whereIn('id', $childrenIds)
        //     ->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType', 'ofUser.upline']);
        
        if ($search) {
            $query->whereHas('ofUser', function ($userQuery) use ($search) {
                $userQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // $childrens = $query->get();

        // $childrens->each(function ($child) {
        //     $profilePicUrl = $child->ofUser->getFirstMediaUrl('profile_photo');
        //     $child->profile_pic = $profilePicUrl;
        // });

        // get account_type_symbol_groups default amount
        $allibs = IbAccountType::with(['ofUser', 'symbolGroups.symbolGroup', 'accountType', 'ofUser.upline', 'upline.symbolGroups'])->get();
        $childdownline = [];
        foreach($allibs as $key => $ibdownline){
            
            if($ibdownline->getIbChildrenIds()){
                $downline = IbAccountType::whereIn('id', $ibdownline->getIbChildrenIds())
                    ->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType', 'ofUser.upline'])
                    ->get();

                $allibs[$key]->downline = $downline;

                // Store the downline data in the $childdownline array
                $childdownline = array_merge($childdownline, $downline->toArray());
            }
        }

        $defaultAccountSymbolGroup = AccountTypeSymbolGroup::where('account_type', 1)
                ->with(['symbolGroup'])
                ->get();
        // dd($defaultAccountSymbolGroup);

        return Inertia::render('Member/RebateAllocation', [
            'ibs' => $ibs,
            // 'childrens' => $childrens,
            'filters' => \Request::only(['search']),
            'childdownline' => $childdownline,
            // 'childrenAccounts' => $childrenAccounts,
            'allibs' => $allibs,
            'defaultAccountSymbolGroup' => $defaultAccountSymbolGroup,
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

    public function updateRebateStructure(Request $request)
    {
        dd($request->all());
    }

}
