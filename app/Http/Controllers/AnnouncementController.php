<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        return Inertia::render('Announcement/Announcement');
    }

    public function getAnnouncements(Request $request): \Illuminate\Http\JsonResponse
    {
//        Announcement::withTrashed()
//            ->where('id', 3)
//            ->restore();
        $announcements = Announcement::query()
            ->with('media')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('title', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');
                $query->where(function ($innerQuery) use ($status) {
                    $innerQuery->where('status', $status);
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $date = $request->input('date');
                $start_date = Carbon::createFromFormat('Y-m-d', $date[0])->startOfDay();
                $end_date = Carbon::createFromFormat('Y-m-d', $date[1])->endOfDay();
                $query->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->latest()
            ->paginate(6);

        return response()->json([
            'announcements' => $announcements
        ]);
    }

    public function create_announcement(AnnouncementRequest $request): \Illuminate\Http\RedirectResponse
    {
        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'recipient' => $request->recipient,
            'popup' => $request->popup ? $request->popup : false,
            'popup_daily' => $request->popup_daily ? $request->popup_daily : false,
        ]);

        if($request->hasFile('image')) {
            $announcement->addMedia($request->image)->toMediaCollection('announcement_image');
        }

        return redirect()->back()->with('toast', 'The new announcement has been created!');
    }

    public function edit_announcement(AnnouncementRequest $request): \Illuminate\Http\RedirectResponse
    {
        $announcement = Announcement::find($request->id);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->input('content'),
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'recipient' => $request->recipient,
            'popup' => $request->popup ? $request->popup : false,
            'popup_daily' => $request->popup_daily ? $request->popup_daily : false,
            'status' => $request->status,
        ]);

        if($request->hasFile('image')) {
            $announcement->clearMediaCollection('announcement_image');
            $announcement->addMedia($request->image)->toMediaCollection('announcement_image');
        }

        return redirect()->back()->with('toast', 'The announcement details have been saved!');
    }

    public function delete_announcement(Request $request): \Illuminate\Http\RedirectResponse
    {
        $announcement = Announcement::find($request->id);

        $announcement->delete();

        return redirect()->back()->with('toast', 'The announcement details have been deleted!');
    }
}
