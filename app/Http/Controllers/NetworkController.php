<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NetworkController extends Controller
{
    
    public function Network(Request $request)
    {
        // $user = Auth::user();

        // get no upline user
        $membersNoUpline = User::get();

        // get have upline user
        // $membersUpline = User::where('upline_id', '!=', null)->get();
        // dd($membersUpline);
        $search = $request->input('search');

        $members = $this->convertToNestedStructure($membersNoUpline, $search);

        return Inertia::render('NetworkTree/NetworkTree', [
            'members' => $members,
        ]);
    }

    private function convertToNestedStructure($membersNoUpline, $search = null, $level = 0)
{
    $nestedData = []; // Create an empty array to hold the nested data for all users

    foreach ($membersNoUpline as $user) {
        // Load the downline relationships for the current user
        $user->load('downline:id,first_name,email,total_group_deposit,total_group_withdrawal,upline_id,role');

        // Count the total_ib and total_client using collection methods
        $totalIB = $user->downline->where('role', 'ib')->count();
        $totalClient = $user->downline->where('role', 'member')->count();

        // Create the user data array for the current user
        $userData = [
            'parent' => $user,
            'level' => $level,
            'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
            'total_ib' => $totalIB,
            'total_client' => $totalClient,
        ];

        $children = $user->downline;

        // If there's a search query, filter the children based on the search criteria
        if ($search) {
            $children = $children->filter(function ($child) use ($search) {
                if (str_contains($child->first_name, $search) || str_contains($child->email, $search)) {
                    return true;
                }

                if ($child->downline->count() > 0) {
                    // Recursively search in deeper levels
                    $nestedResult = $this->convertToNestedStructure([$child], $search, 1);
                    return !empty($nestedResult['children']);
                }

                return false;
            });
        }

        // Recursively process the children to create nested structure
        if ($children->count() > 0) {
            $userData['children'] = [];
            foreach ($children as $child) {
                $userData['children'][] = $this->convertToNestedStructure($children, $search, $level + 1);
            }
            
        }

        // Append user data to the nestedData array
        $nestedData[] = $userData;
    }

    return $nestedData;
}


}
