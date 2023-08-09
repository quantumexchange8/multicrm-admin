<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function deposit_report(Request $request)
    {
        $search = $request->search;
        $requestDate = $request->date;
        $type = $request->type;

        $deposits = Payment::query()->with('ofUser')
            ->where('category', 'payment')
            ->where('type', 'Deposit');

        if (auth()->user()->remark == "vietnam plan") {
            $deposits =  $deposits->whereRelation('ofUser', 'remark', "vietnam plan");
        }

        if ($type) {
            $deposits =  $deposits->where('channel', $type);
        }

        if ($requestDate) {
            $start_date = Carbon::createFromFormat('Y-m-d', $requestDate[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $requestDate[1])->endOfDay();
            $deposits->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($search) {
            $deposits->whereRelation('ofUser', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $deposits =  $deposits->latest()->paginate(10);

        return Inertia::render('Transaction/DepositReport',[
            'deposits' => $deposits,
            'filters' => $request->only(['search', 'type', 'date']),
        ]);
    }
}
