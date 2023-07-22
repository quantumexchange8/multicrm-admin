<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

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

}
