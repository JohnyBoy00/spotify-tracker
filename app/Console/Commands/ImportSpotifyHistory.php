<?php

namespace App\Console\Commands;

use App\Models\ImportedStreamingHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Command to import Spotify Extended Streaming History JSON files.
 */
class ImportSpotifyHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:import-history {user_id} {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Spotify Extended Streaming History JSON file for a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $filePath = $this->argument('file_path');

        // Validate user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        // Validate file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading file: {$filePath}");
        
        // Read and decode JSON
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON file: " . json_last_error_msg());
            return 1;
        }

        if (!is_array($data)) {
            $this->error("JSON file must contain an array of streaming history entries.");
            return 1;
        }

        $this->info("Found " . count($data) . " entries to import.");
        $this->info("Starting import...");

        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        // Process in chunks for better performance
        $chunks = array_chunk($data, 1000);

        foreach ($chunks as $chunk) {
            $records = [];
            
            foreach ($chunk as $entry) {
                try {
                    // Skip if essential data is missing
                    if (empty($entry['ts']) || !isset($entry['ms_played'])) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    // Only import audio tracks (skip podcasts, audiobooks, videos)
                    if (empty($entry['master_metadata_track_name']) || empty($entry['spotify_track_uri'])) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    $playedAt = Carbon::parse($entry['ts']);

                    // Check if this exact entry already exists
                    $exists = ImportedStreamingHistory::where('user_id', $userId)
                        ->where('played_at', $playedAt)
                        ->where('spotify_track_uri', $entry['spotify_track_uri'])
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    $records[] = [
                        'user_id' => $userId,
                        'played_at' => $playedAt,
                        'platform' => $entry['platform'] ?? null,
                        'ms_played' => $entry['ms_played'],
                        'track_name' => $entry['master_metadata_track_name'] ?? null,
                        'artist_name' => $entry['master_metadata_album_artist_name'] ?? null,
                        'album_name' => $entry['master_metadata_album_album_name'] ?? null,
                        'spotify_track_uri' => $entry['spotify_track_uri'] ?? null,
                        'skipped' => $entry['skipped'] ?? false,
                        'reason_start' => $entry['reason_start'] ?? null,
                        'reason_end' => $entry['reason_end'] ?? null,
                        'shuffle' => $entry['shuffle'] ?? false,
                        'offline' => $entry['offline'] ?? false,
                        'incognito_mode' => $entry['incognito_mode'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $imported++;
                    $bar->advance();

                } catch (\Exception $error) {
                    $errors++;
                    $bar->advance();
                }
            }

            // Bulk insert records
            if (!empty($records)) {
                ImportedStreamingHistory::insert($records);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Import completed!");
        $this->info("Imported: {$imported}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");

        // Update user's total listening minutes
        $this->info("Calculating total listening minutes...");
        $totalMinutes = ImportedStreamingHistory::where('user_id', $userId)
            ->sum('ms_played') / 1000 / 60; // Convert ms to minutes

        $user->total_listening_minutes = round($totalMinutes);
        $user->save();

        $this->info("Total listening minutes updated: " . number_format($user->total_listening_minutes) . " minutes");
        $this->info("That's approximately " . round($user->total_listening_minutes / 60) . " hours!");

        return 0;
    }
}
