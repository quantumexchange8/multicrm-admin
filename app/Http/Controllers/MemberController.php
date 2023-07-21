<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class MemberController extends Controller
{
    //
    public function MemberListing()
    {

        $members = User::where('role', 'member')->with(['tradingAccounts'])->get();

        return Inertia::render('Member/MemberListing', [
            'members' => $members,
        ]);
    }
}
