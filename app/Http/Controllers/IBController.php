<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class IBController extends Controller
{
    
    public function IBListing(Request $request)
    {

        $search = $request->input('search');

        if($search)
        {
            $ibs = User::where('role', 'ib')
                ->where(function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%");
                })
                ->with(['tradingAccounts'])
                ->get();
        }
        else
        {
            $ibs = User::where('role', 'ib')->with(['tradingAccounts'])->get();
        }

        return Inertia::render('Ib/IbListing', [
            'ibs' => $ibs,
        ]);
    }
}
