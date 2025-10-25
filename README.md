# 🎵 Spotify Tracker

A Laravel-based web application to track your Spotify listening history, analyze your music preferences, and discover new music.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Manual Setup](#manual-setup)
- [Configuration](#configuration)
- [Project Structure](#project-structure)
- [Development](#development)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

- 🎧 Track your Spotify listening history
- 📊 Analyze your music preferences and statistics
- 🔍 Discover new music based on your taste
- 📈 Visualize your listening patterns
- 🎵 Create and manage playlists
- 📱 Responsive design for mobile and desktop

## 🔧 Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+ or SQLite 3
- Spotify Developer Account (for API access)

## 🚀 Quick Start

1. **Clone or navigate to the project:**
   ```bash
   cd /path/to/spotify-tracker
   ```

2. **Run the startup script:**
   ```bash
   ./start.sh
   ```

   This will:
   - Copy `.env.example` to `.env` if needed
   - Generate application key
   - Create SQLite database (if using SQLite)
   - Run migrations
   - Install npm dependencies
   - Start Laravel and Vite dev servers

3. **Open your browser:**
   Navigate to `http://localhost:8000`

## 🛠️ Manual Setup

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

## ⚙️ Configuration

### Spotify API Setup

1. **Create a Spotify App:**
   - Go to [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
   - Click "Create App"
   - Fill in the required information
   - Add redirect URI: `http://localhost:8000/callback`

2. **Add Credentials to `.env`:**
   ```env
   SPOTIFY_CLIENT_ID=your_client_id_here
   SPOTIFY_CLIENT_SECRET=your_client_secret_here
   SPOTIFY_REDIRECT_URI=http://localhost:8000/callback
   ```

3. **Create Spotify Config File:**
   Create `config/spotify.php`:
   ```php
   <?php

   return [
       'client_id' => env('SPOTIFY_CLIENT_ID'),
       'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
       'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
   ];
   ```

### Application Name

Update your app name in `.env`:
```env
APP_NAME="Spotify Tracker"
```

## 📁 Project Structure

```
spotify-tracker/
├── app/                    # Application core
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── bootstrap/             # Framework bootstrap
├── config/               # Configuration files
├── database/             # Migrations, factories, seeders
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Public assets (entry point)
├── resources/            # Views, raw assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── views/           # Blade templates
├── routes/              # Route definitions
│   ├── web.php         # Web routes
│   └── api.php         # API routes
├── storage/            # Compiled files, logs, cache
├── tests/              # Automated tests
├── vendor/             # Composer dependencies
├── .env                # Environment configuration
├── artisan             # Laravel command-line tool
├── composer.json       # PHP dependencies
├── package.json        # Node.js dependencies
├── start.sh            # Quick start script
└── SETUP.md            # Detailed setup guide
```

## 🧪 Development

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

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/SpotifyTest.php

# Run tests with coverage
php artisan test --coverage
```

## 📚 Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Bootcamp:** https://bootcamp.laravel.com
- **Laracasts:** https://laracasts.com (Video tutorials)
- **Spotify Web API:** https://developer.spotify.com/documentation/web-api
- **Spotify Web API PHP:** https://github.com/jwilsson/spotify-web-api-php

## 🐛 Troubleshooting

### Database Connection Error

If you see "could not find driver":
```bash
# For MySQL
sudo apt-get install php8.3-mysql

# For SQLite
sudo apt-get install php8.3-sqlite3
```

### Permission Errors

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Node Version Issues

Your current Node version (16.x) is outdated. Update to Node 18+:
```bash
# Using nvm
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
nvm install 20
nvm use 20
```

### Port Already in Use

If port 8000 is in use:
```bash
php artisan serve --port=8080
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- Powered by [Spotify Web API](https://developer.spotify.com/documentation/web-api)
- UI components styled with [Tailwind CSS](https://tailwindcss.com)

---

**Happy tracking! 🎵** 

For more detailed setup instructions, see [SETUP.md](SETUP.md).
