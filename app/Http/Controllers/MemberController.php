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
use App\Models\RebateAllocationRequest;
use App\Models\SettingCountry;
use App\Models\TradingAccount;
use App\Models\User;
use App\Services\RunningNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use function GuzzleHttp\Promise\all;

class MemberController extends Controller
{
    public function member_listing(Request $request)
    {
        $members = User::query()
            ->whereIn('role', ['member', 'ib'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $role = $request->input('role');
                $query->where('role', $role);
            })
            ->with(['tradingAccounts', 'media', 'upline'])
            ->orderByDesc('created_at')
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
            'filters' => \Request::only(['search', 'role']),
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

    public function getIbDownlineRebateInfo(Request $request)
    {
        $ib = IbAccountType::find($request->id);

        return IbAccountType::whereIn('id', $ib->getIbChildrenIds())
            ->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType', 'ofUser.upline'])
            ->get();
    }

    public function getNewIbRebateInfo(Request $request)
    {
        $user = User::where('email', $request->new_ib)->first();

        return IbAccountTypeSymbolGroupRate::whereRelation('ibAccountType', 'user_id', '=', $user->id)
            ->whereRelation('ibAccountType', 'account_type', '=', 1)
            ->with('symbolGroup')
            ->get();
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

    public function rebate_allocation(Request $request)
    {
        $ibs = IbAccountType::query()
            ->with(['ofUser', 'symbolGroups.symbolGroup', 'accountType', 'ofUser.upline', 'upline.symbolGroups', 'upline.symbolGroups.symbolGroup'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->whereHas('ofUser', function ($innerQuery) use ($search) {
                    $innerQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        $defaultAccountSymbolGroup = AccountTypeSymbolGroup::where('account_type', 1)
            ->with(['symbolGroup'])
            ->get();

        return Inertia::render('Member/RebateAllocation', [
            'ibs' => $ibs,
            'filters' => $request->only('search'),
            'defaultAccountSymbolGroup' => $defaultAccountSymbolGroup,
            'get_ibs_sel' => User::where('role', 'ib')->pluck('email')->toArray(),
        ]);
    }

    public function updateRebateAllocation(Request $request)
    {
        $curIb = IbAccountType::find($request->user_id);
        $upline = IbAccountType::find($request->upline_id);
        $downline = $curIb->downline;
        $curIbRate = IbAccountTypeSymbolGroupRate::where('ib_account_type', $request->user_id)->get()->keyBy('symbol_group');

        $validationErrors = new MessageBag();

        // Collect the validation errors for ibGroupRates
        foreach ($request->ibGroupRates as $key => $amount) {
            if ($upline) {
                $parent = IbAccountTypeSymbolGroupRate::with('symbolGroup')
                    ->where('ib_account_type', $upline->id)
                    ->where('symbol_group', $key)
                    ->first();
            } else {
                $parent = AccountTypeSymbolGroup::where('account_type', 1)
                    ->with('symbolGroup')
                    ->where('symbol_group', $key)
                    ->first();
            }

            if ($parent && $amount >= $parent->amount) {
                $fieldKey = 'ibGroupRates.' . $key;
                $errorMessage = $parent->symbolGroup->name . ' amount cannot be higher than ' . $parent->amount;
                $validationErrors->add($fieldKey, $errorMessage);
            }
        }

        // Collect the validation errors for each downline's ibGroupRates
        foreach ($downline as $child) {
            foreach ($request->ibGroupRates as $key => $amount) {
                $childRate = IbAccountTypeSymbolGroupRate::with('symbolGroup')
                    ->where('ib_account_type', $child->id)
                    ->where('symbol_group', $key)
                    ->first();

                if ($childRate && $amount < $childRate->amount) {
                    $fieldKey = 'ibGroupRates.' . $key;
                    $errorMessage = $childRate->symbolGroup->name . ' amount cannot be lower than ' . $childRate->amount;
                    $validationErrors->add($fieldKey, $errorMessage);
                }
            }
        }

        // If there are validation errors, return them in the response
        if ($validationErrors->count() > 0) {
            return redirect()->back()->withErrors($validationErrors);
        }

        $rebateAllocation = RebateAllocation::create(['from' => $curIb->upline_id, 'to' => $request->user_id]);

        foreach ($request->ibGroupRates as $key => $amount) {

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
        $all = $request->ibGroupRates;
        $ibIds = collect($all)->keys();
        foreach ($all as $ib => $symbol_group) {
            $curIb = IbAccountType::find($ib);

            $exists = RebateAllocation::where('to', $ib)->whereRelation('request', 'status', '=', 'pending')->exists();
            if ($exists)
                return response()->json(['success' => false, 'message' => $ib . ' has a pending request']);

            $validationErrors = new MessageBag();

            $upline = $curIb->upline;
            $downline = $curIb->downline;
            foreach ($symbol_group as $key => $amount) {
                $parentRate = IbAccountTypeSymbolGroupRate::where('ib_account_type', $upline->id)->where('symbol_group', $key)->first();
                if ($amount >= $parentRate->amount) {
                    $fieldKey = 'ibGroupRates.' . $ib . '.' . $key;
                    $errorMessage = 'Cannot higher than ' . $parentRate->amount;
                    $validationErrors->add($fieldKey, $errorMessage);
                }
            }

            foreach ($downline as $child) {
                foreach ($symbol_group as $key => $amount) {
                    $childRate = IbAccountTypeSymbolGroupRate::where('ib_account_type', $child->id)->where('symbol_group', $key)->first();
                    if ($amount < $childRate->amount) {
                        $fieldKey = 'ibGroupRates.' . $ib . '.' . $key;
                        $errorMessage = 'Cannot lower than ' . $childRate->amount;
                        $validationErrors->add($fieldKey, $errorMessage);
                    }
                }
            }

            // If there are validation errors, return them in the response
            if ($validationErrors->count() > 0) {
                return redirect()->back()->withErrors($validationErrors);
            }

        }

        $allocationRequest = RebateAllocationRequest::create([
            'requestBy' => Auth::id(),
            'handleBy' => Auth::id(),
            'description' => 'Edit Rebate Allocation Structure',
            'status' => 'Completed',
        ]);

        foreach ($all as $ib => $symbol_group) {
            $curIb = IbAccountType::find($ib);
            $downline = $curIb->downline;

            $rebateAllocation = RebateAllocation::create([
                'from' => $curIb->upline_id,
                'to' => $ib,
                'request_id' => $allocationRequest->id,
                'status' => 'approve',
            ]);
            $curIbRate = IbAccountTypeSymbolGroupRate::where('ib_account_type', $ib)->get()->keyBy('symbol_group');

            foreach ($symbol_group as $key => $amount) {
                $curAmount = $curIbRate[$key]->amount;
                RebateAllocationRate::create([
                    'allocation_id' => $rebateAllocation->id,
                    'symbol_group' => $key,
                    'old' => $curAmount,
                    'new' => $amount,
                ]);
                $curIbRate[$key]->update(['amount' => $amount]);
            }
        }
        return redirect()->back()->with('toast', 'New rebate allocation has been saved!');
    }

}
