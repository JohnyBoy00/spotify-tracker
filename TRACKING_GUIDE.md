# 🎵 Spotify Listening Minutes Tracking Guide

## ✅ Setup Complete!

Your Spotify Tracker now has **listening minutes tracking** fully implemented! This guide will help you start tracking your data.

---

## 📊 What's Been Built

### **Database Tables:**
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

### **Background Jobs:**
1. **`TrackRecentlyPlayed`** - Fetches recently played tracks from Spotify
2. **`AggregateDailyListening`** - Calculates daily summaries

### **Stats Display:**
- **This Week** - Minutes listened in current week
- **This Month** - Minutes listened in current month  
- **This Year** - Minutes listened in current year
- **All Time** - Total minutes ever tracked

---

## 🚀 Getting Started

### **Step 1: Log in to Spotify**
1. Go to your homepage (`http://127.0.0.1:8001`)
2. Click "Connect with Spotify"
3. Authorize the app

✅ You're now set up in the database!

### **Step 2: Run Your First Track**
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

### **Step 3: Aggregate Your Data (Optional)**
To calculate daily summaries, run:

```bash
php artisan spotify:track --aggregate
```

This will also aggregate yesterday's data into the `daily_listening_summary` table.

### **Step 4: View Your Stats**
1. Go to Stats page (`http://127.0.0.1:8001/stats`)
2. Click the "Minutes Listened" tab
3. See your listening time! 🎉

---

## ⏰ Automated Tracking (Recommended)

For continuous tracking, you'll want to automate this process.

### **Option 1: Laravel Scheduler (Recommended)**

1. Open `app/Console/Kernel.php` and add:

```php
protected function schedule(Schedule $schedule)
{
    // Track recently played every 5 minutes
    $schedule->call(function () {
        $users = \App\Models\User::whereNotNull('access_token')->get();
        foreach ($users as $user) {
            \App\Jobs\TrackRecentlyPlayed::dispatch($user);
        }
    })->everyFiveMinutes();

    // Aggregate yesterday's data at midnight
    $schedule->call(function () {
        $users = \App\Models\User::whereNotNull('access_token')->get();
        foreach ($users as $user) {
            \App\Jobs\AggregateDailyListening::dispatch($user, \Carbon\Carbon::yesterday());
        }
    })->daily();
}
```

2. Add this to your crontab (run `crontab -e`):

```bash
* * * * * cd /home/jean/Documents/Personal/projects/spotify-tracker && php artisan schedule:run >> /dev/null 2>&1
```

3. Keep the queue worker running:

```bash
php artisan queue:work
```

**Tip:** Use a process manager like `supervisor` to keep the queue worker running in the background.

---

## 🧪 Testing & Manual Commands

### **Test Tracking:**
```bash
# Track recently played
php artisan spotify:track

# Track + aggregate
php artisan spotify:track --aggregate
```

### **Process Queue:**
```bash
# Run once
php artisan queue:work --once

# Run continuously
php artisan queue:work

# Run in background (Linux/Mac)
nohup php artisan queue:work &
```

### **Check Database:**
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

## 📈 How It Works

### **Data Flow:**

```
1. Spotify API (Recently Played)
   ↓
2. TrackRecentlyPlayed Job
   ↓
3. Store in listening_history table
   ↓
4. AggregateDailyListening Job (runs daily)
   ↓
5. Summarize into daily_listening_summary
   ↓
6. Display on Stats page
```

### **Why Track This Way?**
- Spotify doesn't provide total listening time directly
- We monitor your "Recently Played" endpoint
- Tracks are stored individually with timestamps
- Daily summaries make queries fast
- All-time total is cached in `users` table

---

## 💡 Tips & Best Practices

### **Storage:**
- **1 year of tracking:** ~6.5 MB
- **10 years:** ~64 MB
- No need to worry about database size!

### **Accuracy:**
- We assume you listened to the full track (Spotify's API limitation)
- Tracks are only counted once (duplicate prevention)
- Data is as accurate as Spotify's "Recently Played" endpoint

### **Privacy:**
- All data stored locally in your SQLite database
- Access tokens stored securely
- Only you can see your stats

### **Tracking Multiple Users:**
- The system supports multiple users
- Each user has their own data
- Jobs run for all users with access tokens

---

## 🐛 Troubleshooting

### **No data showing:**
1. Check if you've run `php artisan spotify:track`
2. Make sure queue worker is running (`php artisan queue:work`)
3. Check logs: `tail -f storage/logs/laravel.log`
4. Verify database: `php artisan tinker` → `ListeningHistory::count()`

### **"No users with access tokens":**
- Make sure you've logged in via Spotify
- Check session: `session('spotify_user_id')`
- Verify database: `User::whereNotNull('access_token')->count()`

### **Jobs not processing:**
- Run `php artisan queue:work` to process the queue
- Check for errors in `storage/logs/laravel.log`
- Try running synchronously: `php artisan queue:work --once`

### **Spotify API errors:**
- Access tokens expire after 1 hour
- You may need to implement token refresh logic (future enhancement)
- For now, just log in again if you get auth errors

---

## 🎯 Next Steps

### **Future Enhancements:**
1. **Automatic Token Refresh** - Keep tracking without re-login
2. **Real-time Tracking** - Use Spotify's Currently Playing endpoint
3. **Graphs & Charts** - Visualize listening trends over time
4. **Genre Tracking** - Most listened genres
5. **Export Data** - Download your stats as CSV/JSON

### **Production Deployment:**
If you want to deploy this:
1. Use a process manager (Supervisor) for queue workers
2. Set up proper cron jobs for scheduling
3. Consider using Redis for queue instead of database
4. Implement token refresh logic

---

## 📚 Database Schema Reference

### **listening_history:**
```
id, user_id, track_id, track_name, artist_name, album_name,
duration_ms, played_at, listened_ms, completed, created_at, updated_at
```

### **daily_listening_summary:**
```
id, user_id, date, total_minutes, total_tracks,
unique_artists, unique_albums, top_genre, created_at, updated_at
```

### **users (added fields):**
```
spotify_id, spotify_display_name, spotify_email,
access_token, refresh_token, token_expires_at,
total_listening_minutes, last_tracked_at
```

---

## 🎉 You're All Set!

Start tracking your Spotify listening data now:

```bash
# Terminal 1: Start queue worker
php artisan queue:work

# Terminal 2: Run tracking
php artisan spotify:track
```

Then visit `http://127.0.0.1:8001/stats` and click "Minutes Listened" to see your stats!

Happy tracking! 🎵✨

