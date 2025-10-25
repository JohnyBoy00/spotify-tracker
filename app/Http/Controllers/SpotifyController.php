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

            // Store tokens in session (you might want to store in database later)
            session([
                'spotify_access_token' => $accessToken,
                'spotify_refresh_token' => $refreshToken,
            ]);

            return redirect()->route('dashboard')->with('success', 'Successfully connected to Spotify!');
        }

        return redirect()->route('home')->with('error', 'Failed to connect to Spotify.');
    }

    /**
     * Display user's Spotify statistics dashboard.
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
            // Get user's top tracks
            $topTracks = $api->getMyTop('tracks', [
                'limit' => 10,
                'time_range' => 'medium_term' // last 6 months
            ]);

            // Get user's top artists
            $topArtists = $api->getMyTop('artists', [
                'limit' => 10,
                'time_range' => 'medium_term'
            ]);

            // Get recently played tracks
            $recentlyPlayed = $api->getMyRecentTracks([
                'limit' => 20
            ]);

            // Get user profile
            $user = $api->me();

            return view('dashboard', compact('topTracks', 'topArtists', 'recentlyPlayed', 'user'));

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Error fetching data from Spotify: ' . $e->getMessage());
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
