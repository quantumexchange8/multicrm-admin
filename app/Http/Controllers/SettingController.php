<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SettingHighlight;
use App\Services\CTraderService;
use App\Services\MetaTrader5\Group\FetchGroupByPos;
use App\Services\MetaTrader5\Group\FetchTotalGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function trading_account_setting()
    {
        return Inertia::render('Setting/TradingAccountSetting');
    }

    public function refreshGroup()
    {
        $conn = (new CTraderService)->connectionStatus();
        if ($conn['code'] != 0) {
            if ($conn['code'] == 10) {
                return response()->json(['success' => false, 'message' => 'No connection with cTrader Server'], 422);
            }
            return response()->json(['success' => false, 'message' => $conn['message']], 422);
        }
        $total_group = (new FetchTotalGroup)->execute();
        $groups = collect();
        for ($i = 0; $i < $total_group; $i++) {
            $group =  collect((new FetchGroupByPos)->execute($i));
            Group::firstOrCreate(['meta_group_name' => $group['Group']]);
        }

        return response()->json(['success' => true, 'message' => 'Successfully refresh group']);
    }

    public function getTradingAccountSettings(Request $request)
    {
        $groups = Group::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('display', 'like', '%' . $search . '%')
                        ->orWhere('value', 'like', '%' . $search . '%')
                        ->orWhere('meta_group_name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10);

        return response()->json([
            'groups' => $groups,
        ]);
    }

    public function highlights_setting()
    {
        $setting_highlight = SettingHighlight::query()->latest()->first();
        $highlightImage = null;

        if (!empty($setting_highlight)) {
            $highlightImage = $setting_highlight->getMedia('highlights');
        }

        return Inertia::render('Setting/Highlight', [
            'highlightImage' => $highlightImage
        ]);
    }

    public function update_highlights(Request $request)
    {
        $imageHighlight = $request->get('imageHighlight');

        if ($imageHighlight) {

            $setting_highlight = SettingHighlight::create([
                'handle_by' => \Auth::id()
            ]);

            $images = explode('|', $imageHighlight);

            foreach ($images as $image) {
                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    // If it's a valid URL, handle it accordingly
                    $setting_highlight
                        ->addMediaFromUrl($image)
                        ->toMediaCollection('highlights');
                } else {
                    // If it's not a valid URL, process it using your existing method
                    $this->processImage($setting_highlight, $image);
                }
            }
            $old_highlights = SettingHighlight::whereNot('id', $setting_highlight->id)->get();
            foreach ($old_highlights as $old_highlight) {
                $old_highlight->clearMediaCollection('highlights');
            }
        }

        return redirect()->back()->with('toast', 'Successfully Update Highlights Images');
    }

    public function upload_highlight_image(Request $request)
    {
        if ($request->hasFile('imageHighlight')) {
            $file = $request->file('imageHighlight');

            // Get the original filename of the uploaded file
            $originalFilename = $file->getClientOriginalName();

            // Store the file using its original filename
            return $file->storeAs('uploads/highlights', $originalFilename, 'public');
        }
        return '';
    }

    public function revert_highlight_image(Request $request): void
    {
        if ($image = $request->get('imageHighlight')) {
            $path = storage_path('/app/public/' . $image);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    protected function processImage($setting_highlight, $image): void
    {
        $path = storage_path('/app/public/' . $image);
        if (file_exists($path)) {
            $setting_highlight->addMedia($path)->toMediaCollection('highlights');
        }
    }
}
