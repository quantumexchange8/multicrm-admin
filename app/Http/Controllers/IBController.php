<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\IbAccountType;
use App\Models\IbAccountTypeSymbolGroupRate;
use App\Models\SettingCountry;
use App\Models\SymbolGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Models\User;

class IBController extends Controller
{
    private function updateUserHierarchyList($user, $list, $id)
    {
        $downline = $user->downline;
        if (count($downline)) {
            foreach ($downline as $child) {
                //$child->hierarchyList = substr($list, -1) . substr($child->hierarchyList, strpos($child->hierarchyList, $id) + strlen($id));
                $child->hierarchyList = substr($list, 0, -1) . $id;
                $child->save();
                $this->updateUserHierarchyList($child, $list, $id . $child->id . '-');
            }
        }
    }

    private function updateUplineIbAccountTypeHierarchyList($user, $accountType, $symbolGroups)
    {
        $upline = $user->upline;
        if ($upline) {
            $ib = IbAccountType::firstWhere(['user_id' => $user->id, 'account_type' => $accountType]);
            if ($ib) return $ib;


            $res = $this->updateUplineIbAccountTypeHierarchyList($upline, $accountType, $symbolGroups);

            $ib =  IbAccountType::create([
                'user_id' => $user->id,
                'account_type' => $accountType,
                'upline_id' => $res->id,
                'hierarchyList' => $res->hierarchyList ? $res->hierarchyList . $res->id . '-' : '-' . $res->id . '-',
            ]);

            foreach ($symbolGroups as $row) {
                IbAccountTypeSymbolGroupRate::create([
                    'ib_account_type' => $ib->id,
                    'symbol_group' => $row->id,
                    'amount' => 0,
                ]);
            }

            return $ib;
        }
        $ib = IbAccountType::firstOrCreate(['user_id' => $user->id, 'account_type' => $accountType], [
            'upline_id' => NULL,
            'hierarchyList' => NULL,
        ]);

        if ($ib->wasRecentlyCreated) {
            foreach ($symbolGroups as $row) {
                IbAccountTypeSymbolGroupRate::create([
                    'ib_account_type' => $ib->id,
                    'symbol_group' => $row->id,
                    'amount' => 0,
                ]);
            }
        }
        return $ib;
    }

    private function updateIbAccountTypeHierarchyList($user, $accountType, $symbolGroups, $uplineIb)
    {
        $downline = $user->downline;
        if (count($downline)) {
            foreach ($downline as $child) {

                $ib =  IbAccountType::firstWhere(['user_id' => $child->id, 'account_type' => $accountType]);

                if ($ib) {
                    $ib->update([
                        'upline_id' => $uplineIb->id,
                        'hierarchyList' =>  $uplineIb->hierarchyList ? $uplineIb->hierarchyList . $uplineIb->id . '-' : '-' . $uplineIb->id . '-',
                    ]);

                    foreach ($symbolGroups as $symbolGroup) {
                        IbAccountTypeSymbolGroupRate::firstWhere([
                            'ib_account_type' => $ib->id,
                            'symbol_group' => $symbolGroup->id,
                        ])->update(['amount' => 0]);
                    }

                    $this->updateIbAccountTypeHierarchyList($child, $accountType, $symbolGroups, $ib);
                }
            }
        }
    }

    public function transfer_ib(Request $request)
    {
        $user = User::find($request->id);
        $newUpline = User::where('email', $request->new_ib);
        if (auth()->user()->remark == "vietnam plan") {
            $newUpline =  $newUpline->where('remark', "vietnam plan");
        }
        $newUpline = $newUpline->first();

        if ($request->new_ib && !$newUpline) {
            throw ValidationException::withMessages(['new_ib' => trans('public.IB Not Found')]);
        }
        $oldUpline = User::find($user->upline_id);

        $role = $user->role;

        if ($role == "ib") {

            if ($newUpline) {
                if ($user->id == $newUpline->id) {
                    throw ValidationException::withMessages(['new_ib' => trans('public.Referral cannot be themselves')]);
                }

                $list = Str::of($newUpline->hierarchyList)->trim('-')->explode('-');

                if ($list->contains($user->id)) {
                    throw ValidationException::withMessages(['new_ib' => trans('public.Referral cannot be same line')]);
                }

                if ($oldUpline) {
                    if ($newUpline->id == $oldUpline->id) {
                        throw ValidationException::withMessages(['new_ib' => trans('public.New Referral cannot be the same as current')]);
                    }
                    $oldUpline->decrement('direct_ib');
                    $upline = $oldUpline;
                    while ($upline) {
                        $upline->total_ib -= ($user->total_ib + 1);
                        $upline->save();
                        $upline = $upline->upline;
                    }
                }
                $newUpline = User::firstWhere('email', $request->new_ib);
                $newUpline->increment('direct_ib');
                $upline = $newUpline;
                while ($upline) {
                    $upline->total_ib += ($user->total_ib + 1);
                    $upline->save();
                    $upline = $upline->upline;
                }

                $hierarchyList = null;
                if (empty($newUpline->hierarchyList)) {
                    $hierarchyList = "-" . $newUpline->id . "-";
                } else {
                    $hierarchyList = $newUpline->hierarchyList . $newUpline->id . "-";
                }

                $user->referral = $newUpline->ib_id;
                $user->upline_id = $newUpline->id;
                $user->hierarchyList = $hierarchyList;
                $user->save();

                $this->updateUserHierarchyList($user, $hierarchyList, '-' . $user->id . '-');


                $userIbAccountTypes = $user->ibAccountTypes;
                $newUplineIbAccountTypes = $newUpline->ibAccountTypes;

                $userAccountTypes = collect($userIbAccountTypes->pluck('account_type'));
                $diffAccountTypes = $userAccountTypes->diff(collect($newUplineIbAccountTypes->pluck('account_type')));
                $symbolGroups = SymbolGroup::all();
                foreach ($diffAccountTypes as $accountType) {
                    $this->updateUplineIbAccountTypeHierarchyList($newUpline, $accountType, $symbolGroups);
                }
                foreach ($userAccountTypes as $accountType) {
                    $uplineIb = IbAccountType::where('user_id', $newUpline->id)->where('account_type', $accountType)->first();
                    $ib =  IbAccountType::firstWhere(['user_id' => $user->id, 'account_type' => $accountType]);

                    if ($ib) {
                        $ib->update([
                            'upline_id' => $uplineIb->id,
                            'hierarchyList' =>  $uplineIb->hierarchyList ? $uplineIb->hierarchyList . $uplineIb->id . '-' : '-' . $uplineIb->id . '-',
                        ]);

                        foreach ($symbolGroups as $symbolGroup) {
                            IbAccountTypeSymbolGroupRate::firstWhere([
                                'ib_account_type' => $ib->id,
                                'symbol_group' => $symbolGroup->id,
                            ])->update(['amount' => 0]);
                        }

                        $this->updateIbAccountTypeHierarchyList($user, $accountType, $symbolGroups, $ib);
                    }
                }
                return redirect()->back()->with('toast', trans('public.Successfully Transfer'));

            } else {
                if ($oldUpline) {
                    $oldUpline->decrement('direct_ib');
                    $upline = $oldUpline;
                    while ($upline) {
                        $upline->total_ib -= ($user->total_ib + 1);
                        $upline->save();
                        $upline = $upline->upline;
                    }

                    $user->referral = NULL;
                    $user->upline_id = NULL;
                    $user->hierarchyList = NULL;
                    $user->save();

                    $symbolGroups = SymbolGroup::all();
                    $accountTypes = AccountType::all()->pluck('id');
                    foreach ($accountTypes as $accountType) {

                        $ib =  IbAccountType::firstOrCreate(['user_id' => $user->id, 'account_type' => $accountType]);


                        $ib->update([
                            'upline_id' => NULL,
                            'hierarchyList' => NULL,
                        ]);

                        foreach ($symbolGroups as $symbolGroup) {
                            IbAccountTypeSymbolGroupRate::updateOrCreate([
                                'ib_account_type' => $ib->id,
                                'symbol_group' => $symbolGroup->id,
                            ], ['amount' => 0]);
                        }

                        $this->updateIbAccountTypeHierarchyList($user, $accountType, $symbolGroups, $ib);
                    }

                    return redirect()->back()->with('toast', trans('public.Successfully Transfer'));
                }
                //no to no
                return response()->json(['success' => false, 'message' => trans('public.Empty')]);
            }
        } else if ($role == "member") {
            if ($newUpline) {
                if ($oldUpline) {
                    if ($newUpline->id == $oldUpline->id) {
                        return response()->json(['success' => false, 'message' => trans('public.No changes')]);
                    }

                    $list = Str::of($newUpline->hierarchyList)->trim('-')->explode('-');


                    if ($list->contains($user->id)) {
                        throw ValidationException::withMessages(['new_ib' => trans('public.Referral cannot be same line')]);
                    }


                    $oldUpline->decrement('direct_client');
                    $upline = $oldUpline;
                    while ($upline) {
                        $upline->total_client -= ($user->total_client + 1);
                        $upline->save();
                        $upline = $upline->upline;
                    }
                }
                $newUpline = User::firstWhere('email', $request->new_ib);
                $newUpline->increment('direct_client');
                $upline = $newUpline;
                while ($upline) {
                    $upline->total_client += ($user->total_client + 1);
                    $upline->save();
                    $upline = $upline->upline;
                }

                $hierarchyList = null;
                if (empty($newUpline->hierarchyList)) {
                    $hierarchyList = "-" . $newUpline->id . "-";
                } else {
                    $hierarchyList = $newUpline->hierarchyList . $newUpline->id . "-";
                }
                $user->referral = $newUpline->ib_id;
                $user->upline_id = $newUpline->id;
                $user->hierarchyList = $hierarchyList;
                $user->save();

                $userAccountTypes = $user->tradingUsers->pluck('account_type')->unique();
                $newUplineIbAccountTypes = $newUpline->ibAccountTypes;
                $diffAccountTypes = $userAccountTypes->diff(collect($newUplineIbAccountTypes->pluck('account_type')));
                $symbolGroups = SymbolGroup::all();
                foreach ($diffAccountTypes as $accountType) {
                    $this->updateUplineIbAccountTypeHierarchyList($newUpline, $accountType, $symbolGroups);
                }

                return redirect()->back()->with('toast', trans('public.Successfully Transfer'));

            } else {
                if ($oldUpline) {
                    $oldUpline->decrement('direct_client');
                    $upline = $oldUpline;
                    while ($upline) {
                        $upline->total_client -= ($user->total_client + 1);
                        $upline->save();
                        $upline = $upline->upline;
                    }

                    $user->referral = NULL;
                    $user->upline_id = NULL;
                    $user->hierarchyList = NULL;
                    $user->save();

                    return redirect()->back()->with('toast', trans('public.Successfully Transfer'));
                }
                //no to no
                return ['success' => false, 'message' => trans('public.Invalid New Upline')];
            }
        }
        return ['success' => false, 'message' => trans('public.Failed')];
    }
}
