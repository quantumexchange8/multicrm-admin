<?php

namespace App\Http\Controllers;

use App\Exports\DepositsExport;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DepositController extends Controller
{
    public function deposit_report(Request $request)
    {
        return Inertia::render('Transaction/DepositReport');
    }

    public function getDepositReport(Request $request)
    {
        $search = $request->search;
        $requestDate = $request->date;
        $type = $request->type;

        $depositsQuery = Payment::query()->with('ofUser')
            ->where('category', 'payment')
            ->where('type', 'Deposit');

        if (auth()->user()->remark == "vietnam plan") {
            $depositsQuery =  $depositsQuery->whereRelation('ofUser', 'remark', "vietnam plan");
        }

        if ($type) {
            $depositsQuery =  $depositsQuery->where('channel', $type);
        }

        if ($requestDate) {
            $start_date = Carbon::createFromFormat('Y-m-d', $requestDate[0])->startOfDay();
            $end_date = Carbon::createFromFormat('Y-m-d', $requestDate[1])->endOfDay();
            $depositsQuery->whereBetween('created_at', [$start_date, $end_date]);
        }

        if ($search) {
            $depositsQuery->where(function ($query) use ($search) {
                $query->whereRelation('ofUser', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('payment_id', 'like', "%{$search}%");
            });
        }

        if ($request->has('export')) {
            return Excel::download(new DepositsExport($depositsQuery), Carbon::now() . '-deposits-report.xlsx');
        }

        $paginatedDeposits = $depositsQuery->latest()->paginate(10);

        $totalDepositQuery = clone $depositsQuery;
        $totalDeposit = $totalDepositQuery->where('status', 'Successful')
            ->get()
            ->sum('amount');

        return response()->json([
            'deposits' => $paginatedDeposits,
            'totalDeposit' => $totalDeposit,
        ]);
    }
}
