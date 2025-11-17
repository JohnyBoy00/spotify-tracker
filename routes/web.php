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

// Terms acceptance
Route::post('/accept-terms', [SpotifyController::class, 'acceptTerms'])->name('accept.terms');

// Dashboard (requires authentication)
Route::get('/dashboard', [SpotifyController::class, 'dashboard'])->name('dashboard');

// Stats page (requires authentication)
Route::get('/stats', [SpotifyController::class, 'stats'])->name('stats');

// Search results page (requires authentication)
Route::get('/search', [SpotifyController::class, 'search'])->name('search');

// Track details page (requires authentication)
Route::get('/track/{id}', [SpotifyController::class, 'trackDetails'])->name('track.details');

// API endpoint for autocomplete search
Route::get('/api/search', [SpotifyController::class, 'apiSearch'])->name('api.search');

// Import history routes
Route::post('/preview-history', [SpotifyController::class, 'previewHistory'])->name('preview.history');
Route::post('/import-history', [SpotifyController::class, 'importHistory'])->name('import.history');
Route::get('/import-status', [SpotifyController::class, 'importStatus'])->name('import.status');

// Logout
Route::get('/logout', [SpotifyController::class, 'logout'])->name('spotify.logout');
