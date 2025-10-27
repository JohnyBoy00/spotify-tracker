<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\TrackRecentlyPlayed;
use App\Jobs\AggregateDailyListening;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * Console command to manually track listening data.
 */
class TrackListeningData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:track {--aggregate : Also aggregate daily data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track recently played Spotify tracks for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to track listening data...');

        // Get all users with access tokens
        $users = User::whereNotNull('access_token')->get();

        if ($users->isEmpty()) {
            $this->warn('No users with access tokens found.');
            return;
        }

        $this->info("Found {$users->count()} user(s) to track.");

        foreach ($users as $user) {
            $this->info("Tracking user: {$user->spotify_display_name} (ID: {$user->id})");
            
            // Dispatch job to track recently played
            TrackRecentlyPlayed::dispatch($user);

            // If aggregate flag is set, also aggregate yesterday's data
            if ($this->option('aggregate')) {
                $this->info("  - Aggregating yesterday's data...");
                AggregateDailyListening::dispatch($user, Carbon::yesterday());
            }
        }

        $this->info('Tracking jobs dispatched!');
        $this->comment('Note: Jobs are queued. Run "php artisan queue:work" to process them.');
    }
}
