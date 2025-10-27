<?php

namespace App\Http\Controllers;

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

            // Get user profile from Spotify
            $api = new SpotifyWebAPI();
            $api->setAccessToken($accessToken);
            $spotifyUser = $api->me();

            // Create or update user in database
            $user = \App\Models\User::updateOrCreate(
                ['spotify_id' => $spotifyUser->id],
                [
                    'name' => $spotifyUser->display_name,
                    'email' => $spotifyUser->email ?? null,
                    'spotify_display_name' => $spotifyUser->display_name,
                    'spotify_email' => $spotifyUser->email ?? null,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_expires_at' => now()->addHours(1), // Spotify tokens expire after 1 hour
                ]
            );

            // Store tokens in session for immediate use
            session([
                'spotify_access_token' => $accessToken,
                'spotify_refresh_token' => $refreshToken,
                'spotify_user_id' => $user->id,
            ]);

            return redirect()->route('dashboard')->with('success', 'Successfully connected to Spotify!');
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

        if (!$accessToken) {
            return redirect()->route('home')->with('error', 'Please connect to Spotify first.');
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        try {
            // Get user's top 5 tracks for the last month (short_term)
            $topTracks = $api->getMyTop('tracks', [
                'limit' => 5,
                'time_range' => 'short_term' // last ~4 weeks
            ]);

            // Get recently played tracks
            $recentlyPlayed = $api->getMyRecentTracks([
                'limit' => 20
            ]);

            // Get user profile
            $user = $api->me();

            return view('dashboard', compact('topTracks', 'recentlyPlayed', 'user'));

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error fetching data from Spotify: ' . $e->getMessage());
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

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        // Get time range from query parameter, default to medium_term (6 months)
        $timeRange = $request->query('range', 'medium_term');
        
        // Validate time range
        $validRanges = ['short_term', 'medium_term', 'long_term'];
        if (!in_array($timeRange, $validRanges)) {
            $timeRange = 'medium_term';
        }

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
            if ($userId) {
                $dbUser = \App\Models\User::find($userId);
                if ($dbUser) {
                    $listeningMinutes = [
                        'today' => \App\Models\DailyListeningSummary::where('user_id', $userId)
                            ->where('date', now()->toDateString())
                            ->sum('total_minutes'),
                        'this_week' => \App\Models\DailyListeningSummary::where('user_id', $userId)
                            ->where('date', '>=', now()->startOfWeek())
                            ->sum('total_minutes'),
                        'this_month' => \App\Models\DailyListeningSummary::where('user_id', $userId)
                            ->where('date', '>=', now()->startOfMonth())
                            ->sum('total_minutes'),
                        'this_year' => \App\Models\DailyListeningSummary::where('user_id', $userId)
                            ->where('date', '>=', now()->startOfYear())
                            ->sum('total_minutes'),
                        'all_time' => $dbUser->total_listening_minutes,
                    ];
                }
            }

            return view('stats', compact('topTracks', 'topArtists', 'user', 'listeningMinutes'));

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error fetching data from Spotify: ' . $e->getMessage());
        }
    }

    /**
     * Search for tracks on Spotify.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $accessToken = session('spotify_access_token');

        if (!$accessToken) {
            return redirect()->route('home')->with('error', 'Please connect to Spotify first.');
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        try {
            $user = $api->me();
            $results = null;

            // If there's a search query, perform the search
            if ($request->has('q') && !empty($request->query('q'))) {
                $query = $request->query('q');
                
                $results = $api->search($query, 'track', [
                    'limit' => 50
                ]);
            }

            return view('search', compact('user', 'results'));

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error searching Spotify: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for autocomplete search.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSearch(Request $request)
    {
        $accessToken = session('spotify_access_token');

        if (!$accessToken) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        try {
            $query = $request->query('q');
            
            if (empty($query)) {
                return response()->json(['tracks' => []]);
            }

            $results = $api->search($query, 'track', [
                'limit' => 10 // Limit to 10 for autocomplete
            ]);

            // Format results for frontend
            $tracks = array_map(function($track) {
                return [
                    'id' => $track->id,
                    'name' => $track->name,
                    'artists' => implode(', ', array_map(fn($artist) => $artist->name, $track->artists)),
                    'image' => $track->album->images[2]->url ?? null,
                    'duration' => gmdate('i:s', $track->duration_ms / 1000),
                    'spotify_url' => $track->external_urls->spotify,
                ];
            }, $results->tracks->items);

            return response()->json(['tracks' => $tracks]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display track details page.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function trackDetails($id)
    {
        $accessToken = session('spotify_access_token');

        if (!$accessToken) {
            return redirect()->route('home')->with('error', 'Please connect to Spotify first.');
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        try {
            // Get track details
            $track = $api->getTrack($id);
            
            // Try to get audio features (may fail with 403 if no permission)
            $audioFeatures = null;
            try {
                $audioFeatures = $api->getAudioFeatures($id);
            } catch (\Exception $e) {
                \Log::info('Could not fetch audio features: ' . $e->getMessage());
            }
            
            // Get user profile
            $user = $api->me();

            return view('track', compact('track', 'audioFeatures', 'user'));

        } catch (\Exception $e) {
            \Log::error('Track details error: ' . $e->getMessage());
            return redirect()->route('search')->with('error', 'Error fetching track details: ' . $e->getMessage());
        }
    }

    /**
     * Logout and clear Spotify session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        session()->forget(['spotify_access_token', 'spotify_refresh_token']);
        return redirect()->route('home')->with('success', 'Disconnected from Spotify.');
    }
}
