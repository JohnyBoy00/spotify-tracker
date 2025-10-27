<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ListeningHistory;
use App\Models\DailyListeningSummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Job to aggregate daily listening statistics.
 */
class AggregateDailyListening implements ShouldQueue
{
    use Queueable;

    protected $date;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Carbon|null $date
     */
    public function __construct(User $user, Carbon $date = null)
    {
        $this->user = $user;
        $this->date = $date ?? Carbon::yesterday();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get all listening history for this user on this date
            $history = ListeningHistory::where('user_id', $this->user->id)
                ->whereDate('played_at', $this->date)
                ->get();

            if ($history->isEmpty()) {
                return;
            }

            // Calculate totals
            $totalMinutes = round($history->sum('listened_ms') / 60000); // Convert ms to minutes
            $totalTracks = $history->count();
            $uniqueArtists = $history->pluck('artist_name')->unique()->count();
            $uniqueAlbums = $history->pluck('album_name')->filter()->unique()->count();

            // Update or create daily summary
            DailyListeningSummary::updateOrCreate(
                [
                    'user_id' => $this->user->id,
                    'date' => $this->date->toDateString(),
                ],
                [
                    'total_minutes' => $totalMinutes,
                    'total_tracks' => $totalTracks,
                    'unique_artists' => $uniqueArtists,
                    'unique_albums' => $uniqueAlbums,
                ]
            );

            // Update user's total listening minutes
            $allTimeTotalMinutes = DailyListeningSummary::where('user_id', $this->user->id)
                ->sum('total_minutes');

            $this->user->update(['total_listening_minutes' => $allTimeTotalMinutes]);

            Log::info("Aggregated daily listening for user {$this->user->id} on {$this->date->toDateString()}: {$totalMinutes} minutes, {$totalTracks} tracks");

        } catch (\Exception $e) {
            Log::error("Error aggregating daily listening for user {$this->user->id}: " . $e->getMessage());
        }
    }
}
