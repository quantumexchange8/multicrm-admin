<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class NetworkController extends Controller
{
    private function buildTree($users, $isRoot = false, $parentLevel = 0) {
        $tree = [];
        foreach ($users as $user) {
            $level = $isRoot ? $parentLevel : $parentLevel + 1;

            $userNode = [
                'name' => $user->first_name,
                'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
                'total_group_deposit' => $user->totalGroupDeposit($user->id),
                'total_group_withdrawal' => $user->totalGroupWithdrawal($user->id),
                'email' => $user->email,
                'role' => $user->role,
                'level' => $level,
                'total_ib' => count($user->getIbUserIds()),
                'total_member' => count($user->getMemberUserIds()),
            ];

            if ($user->downline->isNotEmpty()) {
                // Pass the correct arguments: $user->downline for users and $isRoot remains the same
                // Also, increment the level for the recursive call
                $userNode['children'] = $this->buildTree($user->downline, false, $level);
            }

            // If it's the root node and has only one child with no further children,
            // return its single child directly instead of an array containing the child
            if ($isRoot && count($tree) === 0 && isset($userNode['children'])) {
                return $userNode['children'];
            }

            $tree[] = $userNode;
        }

        return $tree;
    }

    public function member_tree(Request $request)
    {
        $usersQuery = User::query()
            ->with('downline')
            ->whereNot('role', '=', 'admin');

        $view = $request->input('view') === 'yes';

        if ($request->filled('search')) {
            $search = $request->input('search');
            $usersQuery->where(function ($innerQuery) use ($search) {
                $innerQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->get();

        if ($view && $users->first() && $users->first()->upline) {
            // For view = yes and has an upline, set the root node to the upline's data
            $rootNode = [
                'name' => $users->first()->upline->first_name,
                'profile_photo' => $users->first()->upline->getFirstMediaUrl('profile_photo'),
                'total_group_deposit' => $users->first()->upline->totalGroupDeposit($users->first()->upline->id),
                'total_group_withdrawal' => $users->first()->upline->totalGroupWithdrawal($users->first()->upline->id),
                'total_ib' => count($users->first()->upline->getIbUserIds()),
                'total_member' => count($users->first()->upline->getMemberUserIds()),
                'email' => $users->first()->upline->email,
                'role' => $users->first()->upline->role,
                'level' => 0,
                'children' => []
            ];
            $tree = $this->buildTree($users, 1, 1);
            $searchNode = [
                'name' => $request->input('search', ''),
                'profile_photo' => $users->first()->getFirstMediaUrl('profile_photo'),
                'total_group_deposit' => $users->first()->totalGroupDeposit($users->first()->id),
                'total_group_withdrawal' => $users->first()->totalGroupWithdrawal($users->first()->id),
                'total_ib' => count($users->first()->getIbUserIds()),
                'total_member' => count($users->first()->getMemberUserIds()),
                'email' => $users->first()->email,
                'role' => $users->first()->role,
                'level' => 1,
                'children' => $tree,
            ];
            $rootNode['children'][] = $searchNode;
        } else {
            if ($users->isEmpty()) {
                // For view = no or no upline and no users, set the root node to an empty array
                $rootNode = [
                    'name' => 'No Records'
                ];
            } else {
                // For view = no or no upline, set the root node to the current user's data
                $rootNode = [
                    'name' => $users->first()->first_name,
                    'profile_photo' => $users->first()->getFirstMediaUrl('profile_photo'),
                    'total_group_deposit' => $users->first()->totalGroupDeposit($users->first()->id),
                    'total_group_withdrawal' => $users->first()->totalGroupWithdrawal($users->first()->id),
                    'total_ib' => count($users->first()->getIbUserIds()),
                    'total_member' => count($users->first()->getMemberUserIds()),
                    'email' => $users->first()->email,
                    'role' => $users->first()->role,
                    'level' => 1,
                    'children' => []
                ];
            }

            $tree = $this->buildTree($users, 1, 1);
            $rootNode['children'] = $tree;
        }

        return Inertia::render('Member/MemberTree/MemberTree', [
            'root' => $rootNode,
            'filters' => $request->only('search', 'view')
        ]);
    }
}
