<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\AggregateDailyListening;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * Console command to aggregate today's listening data.
 */
class AggregateTodayListening extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:aggregate-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate listening data for today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Aggregating today\'s listening data...');

        // Get all users with access tokens
        $users = User::whereNotNull('access_token')->get();

        if ($users->isEmpty()) {
            $this->warn('No users with access tokens found.');
            return;
        }

        $this->info("Found {$users->count()} user(s).");

        foreach ($users as $user) {
            $this->info("Aggregating data for: {$user->spotify_display_name} (ID: {$user->id})");
            
            // Dispatch job to aggregate today's data
            AggregateDailyListening::dispatch($user, Carbon::today());
        }

        $this->info('Aggregation jobs dispatched!');
        $this->comment('Note: Jobs are queued. They should process automatically if queue:work is running.');
    }
}
