<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotifyController;

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Spotify authentication routes
Route::get('/auth/spotify', [SpotifyController::class, 'redirectToSpotify'])->name('spotify.auth');
Route::get('/callback', [SpotifyController::class, 'handleSpotifyCallback'])->name('spotify.callback');

// Dashboard (requires authentication)
Route::get('/dashboard', [SpotifyController::class, 'dashboard'])->name('dashboard');

// Stats page (requires authentication)
Route::get('/stats', [SpotifyController::class, 'stats'])->name('stats');

// Logout
Route::get('/logout', [SpotifyController::class, 'logout'])->name('spotify.logout');
