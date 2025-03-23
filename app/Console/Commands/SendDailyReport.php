<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use App\Models\Progress;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailyReport extends Command
{
    protected $signature = 'app:send-daily-report';
    protected $description = 'Send a daily health progress report to users';

    public function handle()
    {
        Log::info("Report sending to users");
        Log::info(Carbon::now());
        $userProfiles = UserProfile::where('receive_daily_report', true)->get();

        foreach ($userProfiles as $userProfile) {
            $user = $userProfile->user;
            $progress = $user->progress()->whereDate('date', Carbon::yesterday())->first();

            if (!$progress) {
                $progress = Progress::create([
                    'user_id' => $user->id,
                    'date' => Carbon::yesterday(),
                ]);
            }
            Mail::to($user->email)->send(new DailyReportMail($progress));

            Log::info("Report sent to: " . $user->email);
        }
    }
}
