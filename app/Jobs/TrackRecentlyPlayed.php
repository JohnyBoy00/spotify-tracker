<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ListeningHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

/**
 * Job to track recently played tracks for all users.
 */
class TrackRecentlyPlayed implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Skip if user hasn't accepted terms and conditions
        if (!$this->user->hasAcceptedCurrentTerms()) {
            Log::info("Skipping tracking for user {$this->user->id} - terms not accepted");
            return;
        }

        // Skip if user has no access token
        if (!$this->user->access_token) {
            return;
        }

        try {
            // Check if token is expired and refresh if needed
            if ($this->user->token_expires_at && $this->user->token_expires_at->isPast()) {
                Log::info("Token expired for user {$this->user->id}, refreshing...");
                $this->refreshToken();
            }

            $api = new SpotifyWebAPI();
            $api->setAccessToken($this->user->access_token);

            // Get recently played tracks (limit 50, max per request)
            $options = ['limit' => 50];
            
            // If we've tracked before, only get tracks since last tracking
            if ($this->user->last_tracked_at) {
                $options['after'] = $this->user->last_tracked_at->timestamp * 1000; // Convert to milliseconds
            }

            $recentTracks = $api->getMyRecentTracks($options);

            $newTracksCount = 0;
            $tracks = collect($recentTracks->items)->sortBy(function($item) {
                return strtotime($item->played_at);
            })->values();

            foreach ($tracks as $index => $item) {
                try {
                    $playedAt = strtotime($item->played_at);
                    $durationMs = $item->track->duration_ms;
                    $trackId = $item->track->id;
                    $listenedMs = $durationMs; // Default to full duration
                    $completed = true;

                    // Check if there's a next track to compare timing
                    if (isset($tracks[$index + 1])) {
                        $nextTrack = $tracks[$index + 1];
                        $nextTrackId = $nextTrack->track->id;
                        $nextPlayedAt = strtotime($nextTrack->played_at);
                        $timeBetweenMs = ($nextPlayedAt - $playedAt) * 1000;

                        // Only consider it a skip if:
                        // 1. The time between tracks is less than the song duration
                        // 2. AND it's a DIFFERENT track (different track_id)
                        // If same track_id, user likely paused and resumed
                        if ($timeBetweenMs < $durationMs && $trackId !== $nextTrackId) {
                            $listenedMs = $timeBetweenMs;
                            $completed = false;
                            
                            // Ensure we don't have negative or zero values
                            if ($listenedMs < 0) {
                                $listenedMs = 0;
                            }
                        }
                        // If same track_id, it's a pause/resume - count full duration
                    }

                    // Try to create the record - will fail silently if duplicate
                    ListeningHistory::create([
                        'user_id' => $this->user->id,
                        'track_id' => $trackId,
                        'track_name' => $item->track->name,
                        'artist_name' => $item->track->artists[0]->name ?? 'Unknown',
                        'album_name' => $item->track->album->name ?? null,
                        'duration_ms' => $durationMs,
                        'played_at' => $item->played_at,
                        'listened_ms' => $listenedMs,
                        'completed' => $completed,
                    ]);

                    $newTracksCount++;
                } catch (QueryException $error) {
                    // Ignore duplicate entry errors (constraint violation)
                    if ($error->getCode() !== '23000') {
                        // If it's not a constraint violation, log it
                        Log::error("Error inserting track for user {$this->user->id}: " . $error->getMessage());
                    }
                    // Silently skip duplicates
                }
            }

            // Update last tracked timestamp
            $this->user->update(['last_tracked_at' => now()]);

            Log::info("Tracked {$newTracksCount} new tracks for user {$this->user->id}");

        } catch (\Exception $error) {
            Log::error("Error tracking recently played for user {$this->user->id}: " . $error->getMessage());
        }
    }

    /**
     * Refresh the Spotify access token using the refresh token.
     */
    protected function refreshToken(): void
    {
        try {
            $session = new Session(
                config('spotify.client_id'),
                config('spotify.client_secret'),
                config('spotify.redirect_uri')
            );

            $session->refreshAccessToken($this->user->refresh_token);
            
            $newAccessToken = $session->getAccessToken();
            
            $this->user->update([
                'access_token' => $newAccessToken,
                'token_expires_at' => now()->addHours(1),
            ]);

            Log::info("Successfully refreshed token for user {$this->user->id}");
            
        } catch (\Exception $error) {
            Log::error("Failed to refresh token for user {$this->user->id}: " . $error->getMessage());
            throw $error;
        }
    }
}
