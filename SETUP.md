# Spotify Tracker - Setup Guide

Welcome to your Spotify Tracker Laravel project! This guide will help you get started.

## What's Already Done

- Laravel 12 installed
- Composer dependencies installed
- NPM packages installed
- Application key generated
- Basic project structure created

## Configuration Steps

### 1. Database Configuration

You need to configure your database. Open the `.env` file and update these settings:

**Option A: Using MySQL** (Recommended)
```env
APP_NAME="Spotify Tracker"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spotify_tracker
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

Then create the database:
```bash
mysql -u root -p
CREATE DATABASE spotify_tracker;
exit;
```

**Option B: Using SQLite** (Simpler for development)
First, install the SQLite PHP extension:
```bash
sudo apt-get install php8.3-sqlite3
```

Then in `.env`:
```env
DB_CONNECTION=sqlite
```

### 2. Run Database Migrations

After configuring your database, run the migrations:
```bash
php artisan migrate
```

### 3. Start the Development Server

You can start Laravel's built-in development server:
```bash
php artisan serve
```

This will start the server at `http://localhost:8000`

### 4. Compile Frontend Assets

In a new terminal, run:
```bash
npm run dev
```

This will compile your CSS and JavaScript files using Vite.

## Project Structure

```
spotify-tracker/
├── app/                # Application core files
│   ├── Http/          # Controllers, Middleware
│   ├── Models/        # Eloquent models
│   └── Providers/     # Service providers
├── bootstrap/         # Framework bootstrap files
├── config/           # Configuration files
├── database/         # Migrations, factories, seeders
│   ├── migrations/   # Database migrations
│   └── seeders/      # Database seeders
├── public/           # Public web files (entry point)
├── resources/        # Views, raw assets
│   ├── css/         # CSS files
│   ├── js/          # JavaScript files
│   └── views/       # Blade templates
├── routes/          # Route definitions
│   ├── web.php     # Web routes
│   └── api.php     # API routes
├── storage/         # Compiled files, logs, cache
├── tests/           # Automated tests
└── vendor/          # Composer dependencies
```

## Next Steps for Spotify Integration

### 1. Register Your Application with Spotify

1. Go to [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
2. Create a new app
3. Get your Client ID and Client Secret
4. Add redirect URI: `http://localhost:8000/callback`

### 2. Install Spotify Web API Package

```bash
composer require jwilsson/spotify-web-api-php
```

### 3. Add Spotify Credentials to .env

```env
SPOTIFY_CLIENT_ID=your_client_id_here
SPOTIFY_CLIENT_SECRET=your_client_secret_here
SPOTIFY_REDIRECT_URI=http://localhost:8000/callback
```

### 4. YouTube API Setup (Optional - for Music Videos)

To display YouTube music videos on track pages:

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the **YouTube Data API v3**
4. Go to "Credentials" and create an **API Key**
5. Add the API key to your `.env` file:

```env
YOUTUBE_API_KEY=your_youtube_api_key_here
```

Note: Without this key, the app will still work but won't display music videos.

### 5. Create a Config File for Spotify

Create `config/spotify.php`:
```php
<?php

return [
    'client_id' => env('SPOTIFY_CLIENT_ID'),
    'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
    'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
];
```

## Useful Laravel Commands

```bash
# Create a new controller
php artisan make:controller SpotifyController

# Create a new model
php artisan make:model Track -m  # -m creates migration too

# Create a new migration
php artisan make:migration create_tracks_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Run tests
php artisan test

# Open Tinker (Laravel REPL)
php artisan tinker
```

## Development Workflow

1. **Routes**: Define your routes in `routes/web.php` or `routes/api.php`
2. **Controllers**: Create controllers to handle requests
3. **Models**: Create models to interact with database
4. **Views**: Create Blade templates in `resources/views/`
5. **Migrations**: Create database tables using migrations

## Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Bootcamp](https://bootcamp.laravel.com/)
- [Laracasts](https://laracasts.com/) - Video tutorials
- [Spotify Web API Documentation](https://developer.spotify.com/documentation/web-api)

## Troubleshooting

### "Could not find driver" error
Install the PHP database driver:
```bash
sudo apt-get install php8.3-mysql  # For MySQL
# OR
sudo apt-get install php8.3-sqlite3  # For SQLite
```

### Node version warning
Your current Node version (16.x) is old. Update to Node 18+ for better compatibility:
```bash
# Using nvm (recommended)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
nvm install 20
nvm use 20
```

### Permission errors
```bash
chmod -R 775 storage bootstrap/cache
```

## Project Goals

This is a Spotify Tracker application where you can:
- Track your listening history
- Analyze your music preferences
- Create playlists
- Discover new music
- View statistics about your listening habits
