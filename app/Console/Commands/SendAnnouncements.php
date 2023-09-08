<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SendAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check announcements and trigger send notification if post date matches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->toDateString();

        // Query announcements with start_date matching today
        $announcements = Announcement::query()
            ->where('status', '=', 'Active')
            ->whereDate('start_date', $today)
            ->get();

        foreach ($announcements as $announcement) {
            switch ($announcement->recipient) {
                case('all'):
                    $users = User::query()->where('status', 1)->whereNot('role', '=', 'admin')->get();
                    foreach ($users as $user) {
                        $user->notify(new AnnouncementNotification($announcement));
                    }

                    break;

                case('ib'):
                    $users = User::query()->where('status', 1)->where('role', '=', 'ib')->get();
                    foreach ($users as $user) {
                        $user->notify(new AnnouncementNotification($announcement));
                    }

                    break;

                case('member'):
                    $users = User::query()->where('status', 1)->where('role', '=', 'member')->get();
                    foreach ($users as $user) {
                        $user->notify(new AnnouncementNotification($announcement));
                    }

                    break;
            }
        }
    }
}
