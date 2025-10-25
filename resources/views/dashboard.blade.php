<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🎵 Spotify Tracker</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 dark:text-gray-300">{{ $user->display_name ?? 'User' }}</span>
                    <a href="{{ route('spotify.logout') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- User Profile -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Welcome back, {{ $user->display_name }}!</h2>
            <div class="flex items-center space-x-4">
                @if(isset($user->images[0]))
                    <img src="{{ $user->images[0]->url }}" alt="Profile" class="w-20 h-20 rounded-full">
                @endif
                <div>
                    <p class="text-gray-600 dark:text-gray-300">Email: {{ $user->email }}</p>
                    <p class="text-gray-600 dark:text-gray-300">Country: {{ $user->country }}</p>
                    <p class="text-gray-600 dark:text-gray-300">Followers: {{ number_format($user->followers->total) }}</p>
                </div>
            </div>
        </div>

        <!-- Top Tracks -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">🎵 Your Top Tracks</h2>
            <div class="space-y-4">
                @foreach($topTracks->items as $index => $track)
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                        <span class="text-2xl font-bold text-gray-400">{{ $index + 1 }}</span>
                        @if(isset($track->album->images[2]))
                            <img src="{{ $track->album->images[2]->url }}" alt="Album" class="w-16 h-16 rounded">
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $track->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ collect($track->artists)->pluck('name')->join(', ') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $track->album->name }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Artists -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">🎤 Your Top Artists</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($topArtists->items as $index => $artist)
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                        @if(isset($artist->images[2]))
                            <img src="{{ $artist->images[2]->url }}" alt="Artist" class="w-16 h-16 rounded-full">
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $artist->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ ucfirst(collect($artist->genres)->take(2)->join(', ')) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ number_format($artist->followers->total) }} followers</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recently Played -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">🕒 Recently Played</h2>
            <div class="space-y-3">
                @foreach($recentlyPlayed->items as $item)
                    <div class="flex items-center space-x-3 p-2 bg-gray-50 dark:bg-gray-700 rounded">
                        @if(isset($item->track->album->images[2]))
                            <img src="{{ $item->track->album->images[2]->url }}" alt="Album" class="w-12 h-12 rounded">
                        @endif
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $item->track->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                {{ collect($item->track->artists)->pluck('name')->join(', ') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($item->played_at)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</body>
</html>

