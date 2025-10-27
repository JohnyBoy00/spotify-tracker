# Spotify Listening Minutes Tracking Guide

## Setup Complete!

Your Spotify Tracker now has **listening minutes tracking** fully implemented! This guide will help you start tracking your data.

---

## What's Been Built

### Database Tables:
1. **`listening_history`** - Stores individual track plays
   - Track name, artist, album
   - When played, duration
   - Timestamp of playback

2. **`daily_listening_summary`** - Pre-aggregated daily stats
   - Total minutes per day
   - Total tracks played
   - Unique artists and albums

3. **`users`** - Extended with Spotify fields
   - Spotify ID, display name, email
   - Access/refresh tokens
   - Total listening minutes (all-time counter)

### Background Jobs:
1. **`TrackRecentlyPlayed`** - Fetches recently played tracks from Spotify
2. **`AggregateDailyListening`** - Calculates daily summaries

### Stats Display:
- **This Week** - Minutes listened in current week
- **This Month** - Minutes listened in current month  
- **This Year** - Minutes listened in current year
- **All Time** - Total minutes ever tracked

---

## Getting Started

### Step 1: Log in to Spotify
1. Go to your homepage (`http://127.0.0.1:8001`)
2. Click "Connect with Spotify"
3. Authorize the app

You're now set up in the database!

### Step 2: Run Your First Track
Open your terminal and run:

```bash
php artisan spotify:track
```

This will:
- Fetch your last 50 recently played tracks
- Store them in the database
- You'll see a message: "Tracking jobs dispatched!"

**Important:** The jobs are queued, so run the queue worker:

```bash
php artisan queue:work
```

You should see output like:
```
[timestamp] Processing: App\Jobs\TrackRecentlyPlayed
[timestamp] Processed:  App\Jobs\TrackRecentlyPlayed
```

### Step 3: Aggregate Your Data
To calculate daily summaries and see stats for today, run:

```bash
php artisan spotify:aggregate-today
```

Then process the queue:

```bash
php artisan queue:work --once
```

This will calculate your listening minutes for today into the `daily_listening_summary` table.

### Step 4: View Your Stats
1. Go to Stats page (`http://127.0.0.1:8001/stats`)
2. Click the "Minutes Listened" tab
3. See your listening time!

---

## Automated Tracking (Recommended)

For continuous tracking, you'll want to automate this process.

### Automated Setup:

The scheduler is already configured in `routes/console.php` with:
- Track recently played every 5 minutes
- Aggregate today's data every hour
- Aggregate yesterday's data at midnight

**Run these two commands in separate terminals:**

```bash
# Terminal 1: Process background jobs
php artisan queue:work

# Terminal 2: Run scheduled tasks
php artisan schedule:work
```

This will automatically:
- Track new songs every 5 minutes
- Aggregate today's data every hour
- Calculate yesterday's totals at midnight
- Refresh expired tokens automatically

### Production Deployment:

For production, add this to your crontab (run `crontab -e`):

```bash
* * * * * cd /home/jean/Documents/Personal/projects/spotify-tracker && php artisan schedule:run >> /dev/null 2>&1
```

Then keep the queue worker running using a process manager like `supervisor`.

---

## Testing & Manual Commands

### Test Tracking:
```bash
# Track recently played
php artisan spotify:track

# Aggregate today's data
php artisan spotify:aggregate-today

# Process queued jobs
php artisan queue:work --once
```

### Process Queue:
```bash
# Run once
php artisan queue:work --once

# Run continuously
php artisan queue:work

# Run in background (Linux/Mac)
nohup php artisan queue:work &
```

### Check Database:
```bash
# See tracked history
php artisan tinker
>>> \App\Models\ListeningHistory::count()
>>> \App\Models\ListeningHistory::latest()->first()

# See daily summaries
>>> \App\Models\DailyListeningSummary::all()

# See your user data
>>> \App\Models\User::first()
```

---

## How It Works

### Data Flow:

```
1. Spotify API (Recently Played)
   ↓
2. TrackRecentlyPlayed Job
   ↓
3. Store in listening_history table
   ↓
4. AggregateDailyListening Job (runs hourly for today, daily for yesterday)
   ↓
5. Summarize into daily_listening_summary
   ↓
6. Display on Stats page
```

### Why Track This Way?
- Spotify doesn't provide total listening time directly
- We monitor your "Recently Played" endpoint
- Tracks are stored individually with timestamps
- Daily summaries make queries fast
- All-time total is cached in `users` table

### Automatic Token Refresh:
- Access tokens expire after 1 hour
- The system automatically refreshes them using refresh tokens
- Tracking continues 24/7 without re-authentication
- Token refresh happens every time before fetching new tracks

---

## Tips & Best Practices

### Storage:
- **1 year of tracking:** ~6.5 MB
- **10 years:** ~64 MB
- No need to worry about database size!

### Accuracy:
- We assume you listened to the full track (Spotify's API limitation)
- Tracks are only counted once (duplicate prevention)
- Data is as accurate as Spotify's "Recently Played" endpoint

### Privacy:
- All data stored locally in your SQLite database
- Access tokens stored securely
- Only you can see your stats

### Tracking Multiple Users:
- The system supports multiple users
- Each user has their own data
- Jobs run for all users with access tokens

---

## Troubleshooting

### No data showing:
1. Check if you've run `php artisan spotify:track`
2. Make sure queue worker is running (`php artisan queue:work`)
3. Run `php artisan spotify:aggregate-today` to aggregate today's data
4. Check logs: `tail -f storage/logs/laravel.log`
5. Verify database: `php artisan tinker` → `ListeningHistory::count()`

### "No users with access tokens":
- Make sure you've logged in via Spotify
- Check session: `session('spotify_user_id')`
- Verify database: `User::whereNotNull('access_token')->count()`

### Jobs not processing:
- Run `php artisan queue:work` to process the queue
- Check for errors in `storage/logs/laravel.log`
- Try running synchronously: `php artisan queue:work --once`

### "Access token expired" errors:
- The system should automatically refresh tokens
- Make sure `schedule:work` is running (it refreshes tokens hourly)
- If issues persist, log out and log back in via Spotify

### Daily summary showing 0 minutes:
- Run `php artisan spotify:aggregate-today` to aggregate today's data
- Make sure `queue:work` is running to process the aggregation
- Wait a few seconds and refresh your Stats page

---

## Next Steps

### Future Enhancements:
1. **Graphs & Charts** - Visualize listening trends over time
2. **Genre Tracking** - Most listened genres
3. **Export Data** - Download your stats as CSV/JSON
4. **Listening Streaks** - Track consecutive days of listening
5. **Year in Review** - Spotify Wrapped style summary

### Production Deployment:
If you want to deploy this:
1. Use a process manager (Supervisor) for queue workers
2. Set up proper cron jobs for scheduling
3. Consider using Redis for queue instead of database
4. Use MySQL or PostgreSQL for production database

---

## Database Schema Reference

### listening_history:
```
id, user_id, track_id, track_name, artist_name, album_name,
duration_ms, played_at, listened_ms, completed, created_at, updated_at
```

### daily_listening_summary:
```
id, user_id, date, total_minutes, total_tracks,
unique_artists, unique_albums, top_genre, created_at, updated_at
```

### users (added fields):
```
spotify_id, spotify_display_name, spotify_email,
access_token, refresh_token, token_expires_at,
total_listening_minutes, last_tracked_at
```

---

## You're All Set!

Start tracking your Spotify listening data now:

```bash
# Terminal 1: Start queue worker
php artisan queue:work

# Terminal 2: Run scheduler
php artisan schedule:work

# Terminal 3 (optional): Manual tracking test
php artisan spotify:track
php artisan spotify:aggregate-today
```

Then visit `http://127.0.0.1:8001/stats` and click "Minutes Listened" to see your stats!

Happy tracking!
