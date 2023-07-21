<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class IBController extends Controller
{
    //
    public function IBListing()
    {

        $ibs = User::where('role', 'ib')->with(['tradingAccounts'])->get();

        return Inertia::render('Ib/IbListing', [
            'ibs' => $ibs,
        ]);
    }
}
