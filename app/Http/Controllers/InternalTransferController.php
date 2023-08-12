<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class InternalTransferController extends Controller
{
    public function internal_transfer_report()
    {
        return Inertia::render('Transaction/InternalTransferReport');
    }

    public function getInternalTransferHistory(Request $request)
    {
        $internal_transfers = Payment::query()->with('ofUser')
            ->whereIn('category', ['internal_transfer', 'rebate_payout'])
            ->whereNot('type', 'RebateEarned')
            ->when($request->filled('type'), function ($query) use ($request) {
                $type = $request->input('type');
                $query->where(function ($innerQuery) use ($type) {
                    $innerQuery->where('type', $type);
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->whereHas('ofUser', function ($user_query) use ($search) {
                    $user_query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $start_date = Carbon::createFromFormat('Y-m-d', $date[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $date[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return response()->json($internal_transfers);
    }
}
