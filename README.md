# Spotify Tracker

A Laravel-based web application that tracks your Spotify listening history, analyzes your music preferences, and provides detailed statistics about your listening habits.

![Spotify Tracker](https://img.shields.io/badge/Laravel-11.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php)
![License](https://img.shields.io/badge/License-MIT-green)

## Table of Contents

- [Features](#features)
- [Screenshots](#screenshots)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Manual Setup](#manual-setup)
- [Listening Minutes Tracking](#listening-minutes-tracking)
- [Configuration](#configuration)
- [Usage](#usage)
- [Development](#development)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Features

### Core Features
- **Spotify OAuth Authentication** - Secure login with your Spotify account
- **Comprehensive Stats Dashboard** - Beautiful overview of your listening habits
- **Listening Minutes Tracking** - Track your total listening time (This Week, This Month, This Year, All Time)
- **Top Tracks Analysis** - Discover your most played songs with time range filters
- **Top Artists Tracking** - See your favorite artists ranked by play count
- **Recently Played** - View your most recent listening activity
- **Smart Search** - Search for any song with live autocomplete
- **Track Details** - View comprehensive information including:
  - Album artwork with download functionality
  - YouTube music videos (when available)
  - Song lyrics links via Genius (when available)
  - Audio features (energy, danceability, tempo, etc.)
  - 30-second previews
  - Release information

### Design & UX
- **Modern Dark Theme** - Sleek black background with glassmorphism effects
- **Spotify Green Accents** - Familiar Spotify branding throughout
- **Fully Responsive** - Perfect on desktop, tablet, and mobile
- **Smooth Animations** - Polished transitions and hover effects
- **Custom Icons** - Beautiful SVG icons from Heroicons
- **Album Art Download** - Click any album cover to download high-quality artwork

### Automation
- **Scheduled Tracking** - Automatically fetches recently played tracks every 5 minutes
- **Token Auto-Refresh** - Never worry about expired tokens
- **Daily Aggregation** - Automatic calculation of daily listening summaries
- **Efficient Storage** - Optimized database schema (only ~6.5MB per year of data)

### Statistics & Insights
- **Time Range Filters** - View stats for Last Month, Last 6 Months, or All Time
- **Real-time Updates** - Hourly aggregation of today's listening data
- **Historical Data** - Complete listening history stored indefinitely
- **Detailed Metrics** - Track count, unique artists, unique albums per day

## Screenshots

### Landing Page
Beautiful glassmorphism design with Spotify branding and feature highlights.

### Dashboard
Overview of your top tracks and recently played music.

### Stats Page
Comprehensive statistics with tabbed interface:
- **Top Tracks** - Your most played songs
- **Top Artists** - Your favorite artists
- **Minutes Listened** - Total listening time breakdown

### Track Details
Detailed information about any track including audio features, album art, and preview player.

## Requirements

- **PHP** 8.2 or higher
- **Composer** 2.x
- **Node.js** 18+ and npm
- **Database** SQLite 3 (recommended) or MySQL 8.0+
- **Spotify Developer Account** (free at [developer.spotify.com](https://developer.spotify.com/dashboard))

## Quick Start

1. **Clone or navigate to the project:**
   ```bash
   cd /path/to/spotify-tracker
   ```

2. **Run the setup script (first time only):**
   ```bash
   ./start.sh
   ```

   This will:
   - Copy `.env.example` to `.env` if needed
   - Generate application key
   - Create SQLite database (if using SQLite)
   - Run migrations
   - Install npm dependencies

3. **Start the development server:**
   ```bash
   ./serve.sh
   ```

   This starts the server with optimized settings for large file uploads (Spotify history imports).

4. **Open your browser:**
   Navigate to `http://localhost:8001`

## Manual Setup

If you prefer manual setup or the script doesn't work:

### 1. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Database Setup

**Option A: SQLite (Simpler for development)**
```bash
# Create the database file
touch database/database.sqlite

# Update .env
DB_CONNECTION=sqlite
```

**Option B: MySQL (Recommended for production)**
```bash
# Create database
mysql -u root -p
CREATE DATABASE spotify_tracker;
exit;

# Update .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spotify_tracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Start Development Servers

**Terminal 1 - Laravel:**
```bash
php artisan serve
```

**Terminal 2 - Vite (for hot reload):**
```bash
npm run dev
```

## Configuration

### Spotify API Setup

1. **Create a Spotify App:**
   - Go to [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
   - Click "Create App"
   - Fill in the required information:
     - **App name:** Spotify Tracker
     - **App description:** Personal Spotify statistics tracker
     - **Redirect URI:** `http://localhost:8000/callback` (or `http://127.0.0.1:8000/callback`)
   - Check "Web API" and accept terms
   - Save and note your **Client ID** and **Client Secret**

2. **Add Credentials to `.env`:**
   ```env
   SPOTIFY_CLIENT_ID=your_client_id_here
   SPOTIFY_CLIENT_SECRET=your_client_secret_here
   SPOTIFY_REDIRECT_URI=http://localhost:8000/callback
   ```

3. **Scopes Included:**
   The app requests these Spotify permissions:
   - `user-top-read` - Read top tracks and artists
   - `user-read-recently-played` - Access recently played tracks
   - `user-library-read` - Access saved tracks
   - `user-read-private` - Access user profile data
   - `user-read-email` - Access email address
   - `user-follow-read` - Access following list

### YouTube API Setup (Optional)

To display music videos on track pages:

1. **Create a Google Cloud Project:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select an existing one
   - Enable the **YouTube Data API v3**

2. **Get an API Key:**
   - Go to "Credentials" in the left sidebar
   - Click "Create Credentials" → "API Key"
   - Copy the generated API key

3. **Add to `.env`:**
   ```env
   YOUTUBE_API_KEY=your_youtube_api_key_here
   ```

Note: The app works fine without this - music videos just won't be displayed.

## Listening Minutes Tracking

One of the most powerful features! Since Spotify doesn't provide total listening time directly, we track it by monitoring your playback activity.

### How It Works

1. **Initial Setup:** When you first log in, we fetch your last 50 recently played tracks
2. **Background Jobs:** Every 5 minutes, we automatically check for new tracks
3. **Token Refresh:** Tokens are automatically refreshed so tracking never stops
4. **Daily Aggregation:** Data is summarized daily for fast queries
5. **Real-time Updates:** Today's stats update every hour

### Starting Listening Tracking

**Automated (Recommended):**

Run these two commands in separate terminals:

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

**Manual (For Testing):**

```bash
# Track recently played songs
php artisan spotify:track

# Aggregate today's data
php artisan spotify:aggregate-today

# Process the queued jobs
php artisan queue:work --once
```

### Storage Requirements

Don't worry about database size! The tracking system is incredibly efficient:

- **1 year:** ~6.5 MB
- **10 years:** ~64 MB
- **50 tracks:** ~10 KB

### Data Collected

For each track play:
- Track ID, name, artist, album
- Duration (milliseconds)
- Timestamp when played
- Whether completed

### Production Deployment

For continuous tracking, add to crontab:

```bash
# Open crontab
crontab -e

# Add this line (adjust path to your project)
* * * * * cd /path/to/spotify-tracker && php artisan schedule:run >> /dev/null 2>&1
```

Then keep queue worker running with a process manager like Supervisor.

For detailed setup instructions, see [TRACKING_GUIDE.md](TRACKING_GUIDE.md).

## Usage

### First Time Setup

1. **Visit the homepage** at `http://localhost:8000`
2. **Click "Connect with Spotify"**
3. **Authorize the app** on Spotify's login page
4. **You're in!** Your dashboard will load with your Spotify data

### Viewing Your Stats

- **Dashboard:** Overview with top 5 tracks and recent plays
- **Stats Page:** Detailed statistics with three tabs:
  - **Top Tracks:** Your most played songs (up to 50)
  - **Top Artists:** Your favorite artists (up to 50)  
  - **Minutes Listened:** Total listening time breakdown

### Searching for Songs

1. Use the **search bar** in the navigation
2. Type at least 2 characters
3. See **live autocomplete** results
4. **Click a result** to go to track details
5. **Press Enter** to see full search results page

### Track Details

Click any song to see:
- High-quality album artwork (click to download)
- Artist, album, release year
- Track duration and popularity
- Audio features (energy, danceability, etc.)
- 30-second preview (if available)
- Direct link to play on Spotify

### Time Range Filters

On the Stats page, filter your Top Tracks and Artists by:
- **Last Month** - Recent favorites
- **Last 6 Months** - Medium-term trends
- **All Time** - Overall preferences

## Project Structure

```
spotify-tracker/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── TrackListeningData.php      # Manual tracking command
│   │       └── AggregateTodayListening.php # Aggregate today's data
│   ├── Http/
│   │   └── Controllers/
│   │       └── SpotifyController.php       # Main application controller
│   ├── Jobs/
│   │   ├── TrackRecentlyPlayed.php        # Background job for tracking
│   │   └── AggregateDailyListening.php    # Daily aggregation job
│   └── Models/
│       ├── User.php                        # User model with Spotify fields
│       ├── ListeningHistory.php            # Individual track plays
│       └── DailyListeningSummary.php       # Daily aggregated stats
├── config/
│   └── spotify.php                         # Spotify API configuration
├── database/
│   └── migrations/
│       ├── *_create_listening_history_table.php
│       ├── *_create_daily_listening_summary_table.php
│       ├── *_add_spotify_fields_to_users_table.php
│       └── *_make_users_fields_nullable.php
├── resources/
│   └── views/
│       ├── welcome.blade.php               # Landing page
│       ├── dashboard.blade.php             # Main dashboard
│       ├── stats.blade.php                 # Statistics page
│       ├── search.blade.php                # Search results
│       └── track.blade.php                 # Track details
├── routes/
│   ├── web.php                            # Web routes
│   └── console.php                        # Scheduled tasks
├── TRACKING_GUIDE.md                      # Detailed tracking documentation
└── README.md                              # This file
```

## Development

### Starting the Server

```bash
# Recommended: Use the serve script (includes large file upload support)
./serve.sh

# Alternative: Standard Laravel server
php artisan serve
```

The application will be available at:
- `http://localhost:8001` (when using `./serve.sh`)
- `http://localhost:8000` (when using `php artisan serve`)

**Note:** The `serve.sh` script is recommended as it includes optimized PHP settings for:
- Large file uploads (25MB) - needed for Spotify history imports
- Extended execution time (300 seconds)
- Increased memory limit (512MB)

### Useful Laravel Commands

```bash
# Create a new controller
php artisan make:controller SpotifyController

# Create a new model with migration
php artisan make:model Track -m

# Create a new migration
php artisan make:migration create_tracks_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Run Laravel's interactive shell (Tinker)
php artisan tinker

# List all routes
php artisan route:list
```

### Building for Production

```bash
# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/SpotifyTest.php

# Run tests with coverage
php artisan test --coverage
```

## Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Bootcamp:** https://bootcamp.laravel.com
- **Laracasts:** https://laracasts.com (Video tutorials)
- **Spotify Web API:** https://developer.spotify.com/documentation/web-api
- **Spotify Web API PHP:** https://github.com/jwilsson/spotify-web-api-php
- **Detailed Tracking Guide:** [TRACKING_GUIDE.md](TRACKING_GUIDE.md)

## Troubleshooting

### Access Token Expired Errors

If you see "access token expired" in logs:
- **Solution:** Token refresh is now automatic! Just make sure `schedule:work` and `queue:work` are running.
- The system will automatically refresh tokens every hour.

### No Listening Data Yet

If the "Minutes Listened" tab shows zeros:
1. Run `php artisan spotify:aggregate-today` to aggregate today's data
2. Make sure `queue:work` is running to process the aggregation
3. Refresh your Stats page

### Database Connection Error

If you see "could not find driver":
```bash
# For SQLite
sudo apt-get update
sudo apt-get install php8.3-sqlite3

# Restart your web server
php artisan serve
```

### Permission Errors

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Port Already in Use

If port 8000 is in use:
```bash
php artisan serve --port=8080
```

### Jobs Not Processing

If background jobs aren't running:
1. Make sure `queue:work` is running in a terminal
2. Check logs: `tail -f storage/logs/laravel.log`
3. Try: `php artisan queue:work --once` to process one job

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Acknowledgments

- Built with [Laravel 11](https://laravel.com) - The PHP Framework for Web Artisans
- Powered by [Spotify Web API](https://developer.spotify.com/documentation/web-api)
- Using [jwilsson/spotify-web-api-php](https://github.com/jwilsson/spotify-web-api-php) - Spotify Web API PHP wrapper
- Styled with [Tailwind CSS](https://tailwindcss.com) (CDN)
- Icons from [Heroicons](https://heroicons.com)
- Database: SQLite 3 for efficient local storage

## Key Features Highlights

- **Zero Configuration** - Works with SQLite out of the box
- **Automatic Token Refresh** - Never expires, tracks 24/7
- **Efficient Storage** - Only ~6.5MB per year of data
- **Beautiful UI** - Modern glassmorphism design
- **Smart Search** - Live autocomplete for instant results
- **Comprehensive Stats** - Minutes, tracks, artists, albums
- **Privacy Focused** - All data stored locally on your machine

---

**Happy tracking!** 

Start exploring your Spotify listening habits today. For detailed setup and tracking configuration, see [TRACKING_GUIDE.md](TRACKING_GUIDE.md).

## What's Next?

Potential future enhancements:
- Charts and graphs for listening trends
- Top genres tracking
- Mobile app
- Social features (compare with friends)
- Year-in-review (Spotify Wrapped style)
- Playlist generation based on stats
- Multi-user support with authentication

---

**Built with Laravel • Spotify API • Tailwind CSS**
