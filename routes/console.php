<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Jobs\TrackRecentlyPlayed;
use App\Jobs\AggregateDailyListening;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Track recently played tracks every 5 minutes
Schedule::call(function () {
    $users = User::whereNotNull('access_token')->get();
    foreach ($users as $user) {
        TrackRecentlyPlayed::dispatch($user);
    }
})->everyFiveMinutes()->name('track-recently-played');

// Schedule: Aggregate yesterday's data at midnight
Schedule::call(function () {
    $users = User::whereNotNull('access_token')->get();
    foreach ($users as $user) {
        AggregateDailyListening::dispatch($user, now()->yesterday());
    }
})->daily()->name('aggregate-daily-listening');

// Schedule: Aggregate today's data every hour (for real-time updates)
Schedule::call(function () {
    $users = User::whereNotNull('access_token')->get();
    foreach ($users as $user) {
        AggregateDailyListening::dispatch($user, now()->today());
    }
})->hourly()->name('aggregate-today-listening');
