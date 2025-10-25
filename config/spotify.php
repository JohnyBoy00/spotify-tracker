<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Spotify API Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials are used to authenticate with the Spotify API.
    | You can obtain these by creating an app in the Spotify Developer Dashboard.
    |
    */

    'client_id' => env('SPOTIFY_CLIENT_ID'),
    'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
    'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | Spotify API Scopes
    |--------------------------------------------------------------------------
    |
    | Define the scopes your application needs to access user data.
    |
    */

    'scopes' => [
        'user-top-read',              // Top artists, tracks, and genres
        'user-read-recently-played',   // Recently played tracks
        'user-read-playback-position', // Progress in tracks
        'user-library-read',           // Saved tracks and albums
        'user-read-email',             // User email
        'user-read-private',           // User profile info
        'streaming',                   // Web Playback SDK (for future)
        'user-read-playback-state',    // Current playback state
        'user-modify-playback-state',  // Control playback
    ],

];

