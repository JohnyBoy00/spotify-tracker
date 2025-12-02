<?php

namespace App\Http\Controllers;

use App\Models\DailyListeningSummary;
use App\Models\ImportedStreamingHistory;
use App\Models\ListeningHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

/**
 * Controller for handling Spotify authentication and API interactions.
 */
class SpotifyController extends Controller
{
    /**
     * Redirect user to Spotify authorization page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToSpotify()
    {
        $session = new Session(
            config('spotify.client_id'),
            config('spotify.client_secret'),
            config('spotify.redirect_uri')
        );

        // The library expects an array of scopes, not a string
        $options = [
            'scope' => config('spotify.scopes'),
        ];

        $authorizeUrl = $session->getAuthorizeUrl($options);

        return redirect($authorizeUrl);
    }

    /**
     * Handle callback from Spotify after authorization.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleSpotifyCallback(Request $request)
    {
        $session = new Session(
            config('spotify.client_id'),
            config('spotify.client_secret'),
            config('spotify.redirect_uri')
        );

        // Request access token using the code from Spotify
        if ($request->has('code')) {
            $session->requestAccessToken($request->code);

            $accessToken = $session->getAccessToken();
            $refreshToken = $session->getRefreshToken();

            $api = new SpotifyWebAPI();
            $api->setAccessToken($accessToken);
            $spotifyUser = $api->me();

            $existingUser = User::where('spotify_id', $spotifyUser->id)->first();
            
            if ($existingUser) {
                $existingUser->update([
                    'name' => $spotifyUser->display_name,
                    'email' => $spotifyUser->email ?? $existingUser->email,
                    'spotify_display_name' => $spotifyUser->display_name,
                    'spotify_email' => $spotifyUser->email ?? $existingUser->spotify_email,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_expires_at' => now()->addHours(1),
                ]);
                
                $user = $existingUser;
                
                // Check if they have accepted current terms
                if (!$user->hasAcceptedCurrentTerms()) {
                    session([
                        'spotify_access_token' => $accessToken,
                        'spotify_refresh_token' => $refreshToken,
                        'spotify_user_id' => $user->id,
                    ]);
                    
                    // Redirect to homepage with modal flag (terms updated)
                    return redirect()->route('home')
                        ->with('show_terms_modal', true)
                        ->with('terms_updated', true);
                }
                
                session([
                    'spotify_access_token' => $accessToken,
                    'spotify_refresh_token' => $refreshToken,
                    'spotify_user_id' => $user->id,
                ]);
                
                return redirect()->route('dashboard')->with('success', 'Welcome back!');
            } else {
                // New user - create account but don't set terms yet
                $user = User::create([
                    'spotify_id' => $spotifyUser->id,
                    'name' => $spotifyUser->display_name,
                    'email' => $spotifyUser->email ?? null,
                    'spotify_display_name' => $spotifyUser->display_name,
                    'spotify_email' => $spotifyUser->email ?? null,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_expires_at' => now()->addHours(1),
                ]);
                
                session([
                    'spotify_access_token' => $accessToken,
                    'spotify_refresh_token' => $refreshToken,
                    'spotify_user_id' => $user->id,
                ]);
                
                // Redirect to homepage with modal flag (new user)
                return redirect()->route('home')
                    ->with('show_terms_modal', true)
                    ->with('new_user', true);
            }
        }

        return redirect()->route('home')->with('error', 'Failed to connect to Spotify.');
    }

    /**
     * Display user's Spotify dashboard (overview).
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $accessToken = session('spotify_access_token');
        $userId = session('spotify_user_id');

        if (!$accessToken) {
            return redirect()->route('home')->with('error', 'Please connect to Spotify first.');
        }

        // Check if user has accepted terms
        if ($userId) {
            $user = User::find($userId);
            if ($user && !$user->hasAcceptedCurrentTerms()) {
                return redirect()->route('home')
                    ->with('show_terms_modal', true)
                    ->with('error', 'Please accept the terms and conditions to continue.');
            }
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        try {
            $topTracks = $api->getMyTop('tracks', [
                'limit' => 5,
                'time_range' => 'short_term' // last ~4 weeks
            ]);

            $recentlyPlayed = $api->getMyRecentTracks([
                'limit' => 20
            ]);

            $user = $api->me();

            return view('dashboard', compact('topTracks', 'recentlyPlayed', 'user'));

        } catch (\Exception $error) {
            return redirect()->route('home')->with('error', 'Error fetching data from Spotify: ' . $error->getMessage());
        }
    }

    /**
     * Display detailed statistics page with time range filters.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function stats(Request $request)
    {
        $accessToken = session('spotify_access_token');
        $userId = session('spotify_user_id');

        if (!$accessToken) {
            return redirect()->route('home')->with('error', 'Please connect to Spotify first.');
        }

        // Check if user has accepted terms
        if ($userId) {
            $user = User::find($userId);
            if ($user && !$user->hasAcceptedCurrentTerms()) {
                return redirect()->route('home')
                    ->with('show_terms_modal', true)
                    ->with('error', 'Please accept the terms and conditions to continue.');
            }
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        // Get time range from query parameter, default to medium_term (6 months)
        $timeRange = $request->query('range', 'medium_term');
        
        // Validate time range
        $validRanges = ['short_term', 'medium_term', 'long_term'];
        if (!in_array($timeRange, $validRanges)) {
            $timeRange = 'medium_term';
        }

        // Get month/year for monthly chart navigation
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($selectedMonth . '-01');

        try {
            // Get user's top tracks with selected time range
            $topTracks = $api->getMyTop('tracks', [
                'limit' => 50,
                'time_range' => $timeRange
            ]);

            // Get user's top artists with selected time range
            $topArtists = $api->getMyTop('artists', [
                'limit' => 50,
                'time_range' => $timeRange
            ]);

            // Get user profile
            $user = $api->me();

            // Get listening minutes stats from database
            $listeningMinutes = null;
            $weeklyChartData = [];
            $monthlyChartData = [];
            if ($userId) {
                $dbUser = User::find($userId);
                if ($dbUser) {
                    $listeningMinutes = [
                        'today' => DailyListeningSummary::where('user_id', $userId)
                            ->whereDate('date', now()->toDateString())
                            ->sum('total_minutes'),
                        'this_week' => DailyListeningSummary::where('user_id', $userId)
                            ->whereDate('date', '>=', now()->startOfWeek())
                            ->sum('total_minutes'),
                        'this_month' => DailyListeningSummary::where('user_id', $userId)
                            ->whereDate('date', '>=', now()->startOfMonth())
                            ->sum('total_minutes'),
                        'this_year' => DailyListeningSummary::where('user_id', $userId)
                            ->whereDate('date', '>=', now()->startOfYear())
                            ->sum('total_minutes'),
                        'all_time' => $dbUser->total_listening_minutes,
                    ];

                    // Get daily data for the current week (Monday to Sunday)
                    $startOfWeek = now()->startOfWeek();
                    $endOfWeek = now()->endOfWeek();
                    
                    $weeklyData = DailyListeningSummary::where('user_id', $userId)
                        ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                        ->orderBy('date', 'asc')
                        ->get();

                    // Create array with all 7 days (Monday to Sunday, fill missing days with 0)
                    for ($i = 0; $i < 7; $i++) {
                        $date = now()->startOfWeek()->addDays($i);
                        $dateString = $date->toDateString();
                        
                        // Find matching data by comparing date strings
                        $dayData = $weeklyData->first(function($item) use ($dateString) {
                            return Carbon::parse($item->date)->toDateString() === $dateString;
                        });
                        
                        $weeklyChartData[] = [
                            'label' => $date->format('D'),
                            'y' => $dayData ? (int)$dayData->total_minutes : 0,
                            'date' => $date->format('M j'),
                        ];
                    }

                    // Get weekly data for the selected month
                    $startOfMonth = $monthDate->copy()->startOfMonth();
                    $endOfMonth = $monthDate->copy()->endOfMonth();
                    
                    $monthlyData = DailyListeningSummary::where('user_id', $userId)
                        ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                        ->orderBy('date', 'asc')
                        ->get();

                    // Group by week
                    $weeksInMonth = [];
                    $currentWeek = $startOfMonth->copy()->startOfWeek();
                    $weekNumber = 1;
                    
                    while ($currentWeek->lte($endOfMonth)) {
                        $weekStart = $currentWeek->copy();
                        $weekEnd = $currentWeek->copy()->endOfWeek();
                        
                        // Clamp to month boundaries
                        if ($weekStart->lt($startOfMonth)) $weekStart = $startOfMonth->copy();
                        if ($weekEnd->gt($endOfMonth)) $weekEnd = $endOfMonth->copy();
                        
                        // Calculate total minutes for this week
                        $weekMinutes = $monthlyData->filter(function($item) use ($weekStart, $weekEnd) {
                            $itemDate = Carbon::parse($item->date);
                            return $itemDate->gte($weekStart) && $itemDate->lte($weekEnd);
                        })->sum('total_minutes');
                        
                        $monthlyChartData[] = [
                            'label' => 'Week ' . $weekNumber,
                            'y' => (int)$weekMinutes,
                            'dateRange' => $weekStart->format('M j') . ' - ' . $weekEnd->format('M j'),
                        ];
                        
                        $currentWeek->addWeek();
                        $weekNumber++;
                        
                        // Safety break to avoid infinite loops
                        if ($weekNumber > 6) break;
                    }
                }
            }

            // Calculate top genres from top artists with artist names
            $genreData = [];
            foreach ($topArtists->items as $artist) {
                if (isset($artist->genres)) {
                    foreach ($artist->genres as $genre) {
                        if (!isset($genreData[$genre])) {
                            $genreData[$genre] = ['count' => 0, 'artists' => []];
                        }
                        $genreData[$genre]['count']++;
                        $genreData[$genre]['artists'][] = $artist->name;
                    }
                }
            }
            // Sort by count and get top 20
            uasort($genreData, function($a, $b) {
                return $b['count'] - $a['count'];
            });
            $topGenres = array_slice($genreData, 0, 20, true);

            $weeklyTopTracks = [];
            $selectedWeek = $request->query('week', now()->format('Y-\WW'));

            if ($request->ajax() && $request->query('type') === 'weekly' && $userId) {
                $weeklyTopTracks = $this->getWeeklyTopTracks($userId, $selectedWeek);
                return response()->json([
                    'weeklyTopTracks' => $weeklyTopTracks,
                    'selectedWeek' => $selectedWeek,
                ]);
            }

            // If AJAX request, return only the monthly chart data
            if ($request->ajax() || $request->query('ajax')) {
                return response()->json([
                    'monthlyChartData' => $monthlyChartData,
                    'selectedMonth' => $selectedMonth,
                ]);
            }

            return view('stats', compact('topTracks', 'topArtists', 'topGenres', 'user', 'listeningMinutes', 'weeklyChartData', 'monthlyChartData', 'selectedMonth', 'weeklyTopTracks', 'selectedWeek'));

        } catch (\Exception $error) {
            return redirect()->route('home')->with('error', 'Error fetching data from Spotify: ' . $error->getMessage());
        }
    }

    /**
     * Get top 5 tracks for a specific week from listening history.
     *
     * @param int $userId
     * @param string $week Format: Y-Www (e.g., 2025-W47)
     * @return array
     */
    private function getWeeklyTopTracks($userId, $week)
    {
        // Parse the week string (e.g., "2025-W47")
        $parts = explode('-W', $week);
        $year = (int)$parts[0];
        $weekNumber = (int)$parts[1];
        
        $weekDate = Carbon::now()->setISODate($year, $weekNumber);
        
        $startOfWeek = $weekDate->copy()->startOfWeek();
        $endOfWeek = $weekDate->copy()->endOfWeek();

        $topTracks = \DB::table('listening_history')
            ->select(
                'track_id',
                'track_name',
                'artist_name',
                'album_name',
                \DB::raw('COUNT(*) as play_count'),
                \DB::raw('SUM(listened_ms) as total_listened_ms')
            )
            ->where('user_id', $userId)
            ->whereBetween('played_at', [$startOfWeek, $endOfWeek])
            ->groupBy('track_id', 'track_name', 'artist_name', 'album_name')
            ->orderByDesc('play_count')
            ->limit(5)
            ->get();

        $accessToken = session('spotify_access_token');
        $api = null;
        if ($accessToken) {
            try {
                $api = new SpotifyWebAPI();
                $api->setAccessToken($accessToken);
            } catch (\Exception $error) {
                \Log::warning("Failed to initialize Spotify API for album artwork: " . $error->getMessage());
            }
        }

        $formattedTracks = [];
        
        $trackIds = $topTracks->pluck('track_id')->filter()->toArray();
        $spotifyTracks = [];
        
        if ($api && !empty($trackIds)) {
            try {
                $batchResult = $api->getTracks($trackIds);
                foreach ($batchResult->tracks as $spotifyTrack) {
                    if ($spotifyTrack) {
                        $spotifyTracks[$spotifyTrack->id] = $spotifyTrack;
                    }
                }
            } catch (\Exception $error) {
                \Log::warning("Failed to batch fetch album artwork: " . $error->getMessage());
            }
        }
        
        foreach ($topTracks as $index => $track) {
            $albumImage = null;
            
            if (isset($spotifyTracks[$track->track_id])) {
                $spotifyTrack = $spotifyTracks[$track->track_id];
                if (isset($spotifyTrack->album->images[2])) {
                    $albumImage = $spotifyTrack->album->images[2]->url;
                } elseif (isset($spotifyTrack->album->images[0])) {
                    $albumImage = $spotifyTrack->album->images[0]->url;
                }
            }
            
            $formattedTracks[] = [
                'rank' => $index + 1,
                'track_id' => $track->track_id,
                'track_name' => $track->track_name,
                'artist_name' => $track->artist_name,
                'album_name' => $track->album_name,
                'album_image' => $albumImage,
                'play_count' => $track->play_count,
                'total_minutes' => round($track->total_listened_ms / 60000, 1),
                'total_hours' => round($track->total_listened_ms / 3600000, 2),
            ];
        }

        return $formattedTracks;
    }

    /**
     * Search for tracks on Spotify.
     * This endpoint is accessible to both logged-in and non-logged-in users.
     * Non-logged-in users can search, but won't have access to personalized features.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $accessToken = session('spotify_access_token');
        $user = null;
        $results = null;

        if ($accessToken) {
            $api = new SpotifyWebAPI();
            $api->setAccessToken($accessToken);

            try {
                $user = $api->me();
            } catch (\Exception $error) {
                session()->forget(['spotify_access_token', 'spotify_refresh_token', 'spotify_user_id']); // Clear session if token is expired
            }
        }

        if ($request->has('q') && !empty($request->query('q'))) {
            $query = $request->query('q');
            
            // Use a fresh API instance with client credentials for non-logged-in users
            if (!$accessToken) {
                try {
                    $session = new Session(
                        config('spotify.client_id'),
                        config('spotify.client_secret')
                    );
                    $session->requestCredentialsToken();
                    $accessToken = $session->getAccessToken();
                } catch (\Exception $error) {
                    return view('search', [
                        'user' => null,
                        'results' => null,
                        'error' => 'Unable to connect to Spotify. Please try again later.'
                    ]);
                }
            }

            try {
                $api = new SpotifyWebAPI();
                $api->setAccessToken($accessToken);
                
                $results = $api->search($query, 'track', [
                    'limit' => 50
                ]);
            } catch (\Exception $error) {
                return view('search', [
                    'user' => $user,
                    'results' => null,
                    'error' => 'Error searching Spotify: ' . $error->getMessage()
                ]);
            }
        }

        return view('search', compact('user', 'results'));
    }

    /**
     * API endpoint for autocomplete search.
     * This endpoint is accessible to both logged-in and non-logged-in users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSearch(Request $request)
    {
        $accessToken = session('spotify_access_token');

        // If user is not logged in, use client credentials
        if (!$accessToken) {
            try {
                $session = new Session(
                    config('spotify.client_id'),
                    config('spotify.client_secret')
                );
                $session->requestCredentialsToken();
                $accessToken = $session->getAccessToken();
            } catch (\Exception $error) {
                return response()->json(['error' => 'Unable to connect to Spotify'], 500);
            }
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        try {
            $query = $request->query('q');
            
            if (empty($query)) {
                return response()->json(['tracks' => ['items' => []]]);
            }

            $results = $api->search($query, 'track', [
                'limit' => 10
            ]);

            // Return full track objects for Alpine.js to use
            return response()->json(['tracks' => $results->tracks]);

        } catch (\Exception $error) {
            \Log::error('API Search Error: ' . $error->getMessage());
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * Display track details page.
     * This endpoint is accessible to both logged-in and non-logged-in users.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function trackDetails($id)
    {
        $accessToken = session('spotify_access_token');
        $user = null;

        // If user is not logged in, use client credentials
        if (!$accessToken) {
            try {
                $session = new Session(
                    config('spotify.client_id'),
                    config('spotify.client_secret')
                );
                $session->requestCredentialsToken();
                $accessToken = $session->getAccessToken();
            } catch (\Exception $error) {
                return redirect()->route('home')->with('error', 'Unable to connect to Spotify.');
            }
        } else {
            $api = new SpotifyWebAPI();
            $api->setAccessToken($accessToken);
            try {
                $user = $api->me();
            } catch (\Exception $error) {
                session()->forget(['spotify_access_token', 'spotify_refresh_token', 'spotify_user_id']); // Clear session if token is expired
                $user = null;
            }
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        // Get track details, lyrics, audio features, and YouTube music video
        try {
            $track = $api->getTrack($id);
            
            $audioFeatures = null;
            try {
                $audioFeatures = $api->getAudioFeatures($id);
            } catch (\Exception $error) {
                \Log::info('Could not fetch audio features: ' . $error->getMessage());
            }

            $youtubeVideoId = $this->searchYouTubeVideo($track);

            $lyrics = $this->fetchLyrics($track);

            return view('track', compact('track', 'audioFeatures', 'user', 'youtubeVideoId', 'lyrics'));

        } catch (\Exception $error) {
            \Log::error('Track details error: ' . $error->getMessage());
            return redirect()->route('home')->with('error', 'Error fetching track details: ' . $error->getMessage());
        }
    }

    /**
     * Search for a YouTube music video based on track information.
     *
     * @param object $track
     * @return string|null
     */
    private function searchYouTubeVideo($track)
    {
        $apiKey = env('YOUTUBE_API_KEY');
        
        if (!$apiKey) {
            return null;
        }

        try {
            // Build search query: "Artist - Track Name"
            $artists = collect($track->artists)->pluck('name')->join(', ');
            $query = urlencode("{$artists} {$track->name}");
            
            // Call YouTube Data API - search for music videos with more results to filter
            $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q={$query}&type=video&videoCategoryId=10&videoEmbeddable=true&maxResults=5&key={$apiKey}";
            
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if (isset($data['items']) && count($data['items']) > 0) {
                // Prioritize official music videos and avoid auto-generated content
                foreach ($data['items'] as $item) {
                    $title = strtolower($item['snippet']['title']);
                    $channelTitle = strtolower($item['snippet']['channelTitle']);
                    
                    // Skip auto-generated "Topic" channels
                    if (strpos($channelTitle, 'topic') !== false) {
                        continue;
                    }
                    
                    // Prefer videos with "official" or "music video" in title
                    if (strpos($title, 'official') !== false || strpos($title, 'music video') !== false) {
                        return $item['id']['videoId'];
                    }
                }
                
                // If no official video found, return the first non-Topic result
                foreach ($data['items'] as $item) {
                    $channelTitle = strtolower($item['snippet']['channelTitle']);
                    if (strpos($channelTitle, 'topic') === false) {
                        return $item['id']['videoId'];
                    }
                }
                
                // Last resort: return first result even if it's a Topic channel
                return $data['items'][0]['id']['videoId'];
            }
            
            return null;
        } catch (\Exception $error) {
            \Log::error('YouTube search error: ' . $error->getMessage());
            return null;
        }
    }

    /**
     * Fetch song lyrics from Genius API.
     *
     * @param object $track
     * @return array|null
     */
    private function fetchLyrics($track)
    {
        $apiKey = env('GENIUS_API_KEY');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $artists = collect($track->artists)->pluck('name')->first(); // Get first artist
            $trackName = $track->name;
            
            // Remove any text in parentheses or brackets for better search results
            $cleanTrackName = preg_replace('/[\(\[].*?[\)\]]/', '', $trackName);
            $cleanTrackName = trim($cleanTrackName);
            
            $searchQuery = urlencode("{$artists} {$cleanTrackName}");
            $searchUrl = "https://api.genius.com/search?q={$searchQuery}";
            
            $context = stream_context_create([
                'http' => [
                    'header' => "Authorization: Bearer {$apiKey}\r\n"
                ]
            ]);
            
            $response = @file_get_contents($searchUrl, false, $context);
            if ($response === false) {
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (isset($data['response']['hits'][0])) {
                $song = $data['response']['hits'][0]['result'];
                $geniusUrl = $song['url'] ?? null;
                
                // Scrape lyrics from Genius page
                $lyricsText = null;
                if ($geniusUrl) {
                    $lyricsText = $this->scrapeLyricsFromGenius($geniusUrl);
                }
                
                return [
                    'title' => $song['title'] ?? null,
                    'artist' => $song['primary_artist']['name'] ?? null,
                    'url' => $geniusUrl,
                    'thumbnail' => $song['song_art_image_thumbnail_url'] ?? null,
                    'genius_id' => $song['id'] ?? null,
                    'text' => $lyricsText,
                ];
            }
            
            return null;
        } catch (\Exception $error) {
            \Log::error('Lyrics fetch error: ' . $error->getMessage());
            return null;
        }
    }

    /**
     * Scrape lyrics from Genius page.
     *
     * @param string $url
     * @return string|null
     */
    private function scrapeLyricsFromGenius($url)
    {
        try {
            $html = @file_get_contents($url);
            if ($html === false) {
                return null;
            }

            // Use DOMDocument to parse HTML
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            // Genius uses data-lyrics-container attribute for lyrics divs
            $lyricsNodes = $xpath->query("//*[@data-lyrics-container='true']");
            
            if ($lyricsNodes->length === 0) {
                return null;
            }

            $lyrics = '';
            foreach ($lyricsNodes as $node) {
                $nodeText = $this->getNodeText($node);
                
                // Remove Genius metadata/intro text that appears at the start
                // These usually contain numbers (contributors, translations) and words like "Lyrics", "Contributors"
                $lines = explode("\n", $nodeText);
                $cleanLines = [];
                $skipIntro = true;
                
                foreach ($lines as $line) {
                    $trimmedLine = trim($line);
                    
                    // Skip lines that look like metadata
                    if ($skipIntro) {
                        // Check if line contains metadata patterns
                        if (preg_match('/^\d+\s*(Contributors?|Translations?|Lyrics|Türkçe|Português|Español|Français)/i', $trimmedLine)) {
                            continue;
                        }
                        // Check if line is mostly numbers and special characters
                        if (preg_match('/^[\d\s\.,]+$/', $trimmedLine) && strlen($trimmedLine) < 50) {
                            continue;
                        }
                        // Check for "Read More" or similar links
                        if (preg_match('/Read More|See more|Learn more/i', $trimmedLine)) {
                            continue;
                        }
                        // If we find a line that looks like actual lyrics, stop skipping
                        if (!empty($trimmedLine) && strlen($trimmedLine) > 5) {
                            $skipIntro = false;
                        }
                    }
                    
                    if (!$skipIntro || !empty($trimmedLine)) {
                        $cleanLines[] = $line;
                    }
                }
                
                $lyrics .= implode("\n", $cleanLines) . "\n\n";
            }

            return trim($lyrics);
        } catch (\Exception $error) {
            \Log::error('Lyrics scraping error: ' . $error->getMessage());
            
            return null;
        }
    }

    /**
     * Extract text from DOM node, preserving line breaks.
     *
     * @param \DOMNode $node
     * @return string
     */
    private function getNodeText($node)
    {
        $text = '';
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $text .= $child->nodeValue;
            } elseif ($child->nodeName === 'br') {
                $text .= "\n";
            } elseif ($child->hasChildNodes()) {
                $text .= $this->getNodeText($child);
            }
        }

        return $text;
    }

    /**
     * Import Spotify Extended Streaming History JSON files.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importHistory(Request $request)
    {
        $userId = session('spotify_user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Check if user has accepted terms
        $user = User::find($userId);
        if (!$user || !$user->hasAcceptedCurrentTerms()) {
            return response()->json(['error' => 'Please accept terms and conditions first'], 403);
        }

        // Validate files
        if (!$request->hasFile('history_files')) {
            return response()->json(['error' => 'No files uploaded'], 422);
        }

        $files = $request->file('history_files');
        
        // Validate each file
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json(['error' => 'Invalid file upload'], 422);
            }
            
            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension !== 'json') {
                return response()->json(['error' => 'All files must be JSON format'], 422);
            }
            
            // Check file size (25MB = 26214400 bytes)
            if ($file->getSize() > 26214400) {
                return response()->json(['error' => 'File too large. Maximum size is 25MB'], 422);
            }
        }
        
        $totalImported = 0;
        $totalSkipped = 0;
        $errors = [];

        foreach ($files as $file) {
            try {
                $jsonContent = file_get_contents($file->getRealPath());
                $data = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors[] = $file->getClientOriginalName() . ': Invalid JSON - ' . json_last_error_msg();
                    continue;
                }

                if (!is_array($data)) {
                    $errors[] = $file->getClientOriginalName() . ': Must be an array';
                    continue;
                }

                // Process in chunks
                $chunks = array_chunk($data, 1000);
                $imported = 0;
                $skipped = 0;

                foreach ($chunks as $chunk) {
                    $records = [];
                    
                    foreach ($chunk as $entry) {
                        try {
                            // Skip if essential data is missing
                            if (empty($entry['ts']) || !isset($entry['ms_played'])) {
                                $skipped++;
                                continue;
                            }

                            // Only import audio tracks
                            if (empty($entry['master_metadata_track_name']) || empty($entry['spotify_track_uri'])) {
                                $skipped++;
                                continue;
                            }

                            $playedAt = Carbon::parse($entry['ts']);

                            // Check if exists
                            $exists = ImportedStreamingHistory::where('user_id', $userId)
                                ->where('played_at', $playedAt)
                                ->where('spotify_track_uri', $entry['spotify_track_uri'])
                                ->exists();

                            if ($exists) {
                                $skipped++;
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

                        } catch (\Exception $error) {
                            // Skip this entry
                        }
                    }

                    // Bulk insert
                    if (!empty($records)) {
                        ImportedStreamingHistory::insert($records);
                    }
                }

                $totalImported += $imported;
                $totalSkipped += $skipped;

            } catch (\Exception $error) {
                $errors[] = $file->getClientOriginalName() . ': ' . $error->getMessage();
            }
        }

        // Update user's total listening minutes (combine imported + current tracking)
        $user = User::find($userId);
        if ($user) {
            // Get imported minutes
            $importedMinutes = ImportedStreamingHistory::where('user_id', $userId)
                ->sum('ms_played') / 1000 / 60;
            
            // Get current tracking minutes
            $currentMinutes = ListeningHistory::where('user_id', $userId)
                ->sum('listened_ms') / 1000 / 60;
            
            // Combine both
            $user->total_listening_minutes = round($importedMinutes + $currentMinutes);
            $user->save();
        }

        return response()->json([
            'success' => true,
            'imported' => $totalImported,
            'skipped' => $totalSkipped,
            'errors' => $errors,
            'total_minutes' => $user ? $user->total_listening_minutes : 0,
        ]);
    }

    /**
     * Preview/analyze JSON files without importing them.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function previewHistory(Request $request)
    {
        $userId = session('spotify_user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Check if user has accepted terms
        $user = User::find($userId);
        if (!$user || !$user->hasAcceptedCurrentTerms()) {
            return response()->json(['error' => 'Please accept terms and conditions first'], 403);
        }

        // Validate files
        if (!$request->hasFile('history_files')) {
            return response()->json(['error' => 'No files uploaded'], 422);
        }

        $files = $request->file('history_files');
        
        // Validate each file
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json(['error' => 'Invalid file upload'], 422);
            }
            
            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension !== 'json') {
                return response()->json(['error' => 'All files must be JSON format'], 422);
            }
            
            // Check file size (25MB = 26214400 bytes)
            if ($file->getSize() > 26214400) {
                return response()->json(['error' => 'File too large. Maximum size is 25MB'], 422);
            }
        }

        $files = $request->file('history_files');
        
        $stats = [
            'total_tracks' => 0,
            'total_minutes' => 0,
            'total_hours' => 0,
            'skipped_tracks' => 0,
            'completed_tracks' => 0,
            'oldest_date' => null,
            'newest_date' => null,
            'top_artists' => [],
            'top_tracks' => [],
            'files_processed' => 0,
            'errors' => []
        ];

        foreach ($files as $file) {
            try {
                $jsonContent = file_get_contents($file->getRealPath());
                $data = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $stats['errors'][] = $file->getClientOriginalName() . ': Invalid JSON';
                    continue;
                }

                if (!is_array($data)) {
                    $stats['errors'][] = $file->getClientOriginalName() . ': Must be an array';
                    continue;
                }

                $artistCounts = [];
                $trackCounts = [];

                foreach ($data as $entry) {
                    // Only count audio tracks
                    if (empty($entry['master_metadata_track_name']) || empty($entry['spotify_track_uri'])) {
                        continue;
                    }

                    $stats['total_tracks']++;
                    $stats['total_minutes'] += ($entry['ms_played'] ?? 0) / 1000 / 60;

                    if (isset($entry['skipped']) && $entry['skipped']) {
                        $stats['skipped_tracks']++;
                    } else {
                        $stats['completed_tracks']++;
                    }

                    // Track dates
                    if (isset($entry['ts'])) {
                        $date = $entry['ts'];
                        if (!$stats['oldest_date'] || $date < $stats['oldest_date']) {
                            $stats['oldest_date'] = $date;
                        }
                        if (!$stats['newest_date'] || $date > $stats['newest_date']) {
                            $stats['newest_date'] = $date;
                        }
                    }

                    // Count artists and tracks
                    $artistName = $entry['master_metadata_album_artist_name'] ?? 'Unknown';
                    $trackName = $entry['master_metadata_track_name'] ?? 'Unknown';
                    
                    if (!isset($artistCounts[$artistName])) {
                        $artistCounts[$artistName] = 0;
                    }
                    $artistCounts[$artistName]++;

                    $trackKey = $trackName . ' - ' . $artistName;
                    if (!isset($trackCounts[$trackKey])) {
                        $trackCounts[$trackKey] = 0;
                    }
                    $trackCounts[$trackKey]++;
                }

                // Get top 10 artists and tracks
                arsort($artistCounts);
                arsort($trackCounts);
                
                $stats['top_artists'] = array_merge($stats['top_artists'], array_slice($artistCounts, 0, 10, true));
                $stats['top_tracks'] = array_merge($stats['top_tracks'], array_slice($trackCounts, 0, 10, true));

                $stats['files_processed']++;

            } catch (\Exception $error) {
                $stats['errors'][] = $file->getClientOriginalName() . ': ' . $error->getMessage();
            }
        }

        // Recalculate top artists and tracks from combined data
        if (!empty($stats['top_artists'])) {
            arsort($stats['top_artists']);
            $stats['top_artists'] = array_slice($stats['top_artists'], 0, 10, true);
        }
        if (!empty($stats['top_tracks'])) {
            arsort($stats['top_tracks']);
            $stats['top_tracks'] = array_slice($stats['top_tracks'], 0, 10, true);
        }

        $stats['total_minutes'] = round($stats['total_minutes']);
        $stats['total_hours'] = round($stats['total_minutes'] / 60, 1);
        
        // Format dates
        if ($stats['oldest_date']) {
            $stats['oldest_date'] = Carbon::parse($stats['oldest_date'])->format('Y-m-d');
        }
        if ($stats['newest_date']) {
            $stats['newest_date'] = Carbon::parse($stats['newest_date'])->format('Y-m-d');
        }

        return response()->json($stats);
    }

    /**
     * Get import status and statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function importStatus()
    {
        $userId = session('spotify_user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $totalEntries = ImportedStreamingHistory::where('user_id', $userId)->count();
        $totalMinutes = ImportedStreamingHistory::where('user_id', $userId)
            ->sum('ms_played') / 1000 / 60;
        
        $oldestEntry = ImportedStreamingHistory::where('user_id', $userId)
            ->orderBy('played_at', 'asc')
            ->first();
        
        $newestEntry = ImportedStreamingHistory::where('user_id', $userId)
            ->orderBy('played_at', 'desc')
            ->first();

        return response()->json([
            'total_entries' => $totalEntries,
            'total_minutes' => round($totalMinutes),
            'total_hours' => round($totalMinutes / 60, 1),
            'oldest_entry' => $oldestEntry ? $oldestEntry->played_at->format('Y-m-d') : null,
            'newest_entry' => $newestEntry ? $newestEntry->played_at->format('Y-m-d') : null,
        ]);
    }

    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->forget(['spotify_access_token', 'spotify_refresh_token', 'spotify_user_id']);
        return redirect()->route('home')->with('success', 'Disconnected from Spotify.');
    }

    /**
     * Accept terms and conditions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptTerms(Request $request)
    {
        $userId = session('spotify_user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update([
            'accepted_terms_version' => config('terms.version'),
            'terms_accepted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);
    }
}
