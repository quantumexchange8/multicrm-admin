<?php

namespace App\Http\Middleware;

use App\Services\RightbarService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $rightBarService = new RightbarService();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'csrf_token' => csrf_token(),
            'toast' => session('toast'),
            'totalApprovedDeposit' => $rightBarService->getTotalApprovedDeposit(),
            'totalPendingDeposit' => $rightBarService->getTotalPendingDeposit(),
            'totalApprovedWithdrawal' => $rightBarService->getTotalApprovedWithdrawal(),
            'totalPendingWithdrawal' => $rightBarService->getTotalPendingWithdrawal(),
            'pendingWithdrawalCount' => $rightBarService->getPendingWithdrawalCount(),
        ]);
    }
}
