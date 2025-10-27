<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stats</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231DB954'><path stroke-linecap='round' stroke-linejoin='round' d='M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'/></svg>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .glass-card {
            background: rgba(23, 23, 23, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .track-card {
            background: rgba(29, 185, 84, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .track-card:hover {
            background: rgba(29, 185, 84, 0.1);
            border-color: rgba(29, 185, 84, 0.3);
            transform: translateX(8px);
        }
        .artist-card {
            background: rgba(29, 185, 84, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .artist-card:hover {
            background: rgba(29, 185, 84, 0.1);
            border-color: rgba(29, 185, 84, 0.3);
            transform: translateY(-4px);
        }
        .spotify-gradient {
            background: linear-gradient(135deg, #1DB954 0%, #1ed760 100%);
        }
        .rank-badge {
            background: linear-gradient(135deg, #1DB954 0%, #1ed760 100%);
            box-shadow: 0 4px 15px rgba(29, 185, 84, 0.4);
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #1DB954;
        }
        .nav-link.active {
            color: #1DB954;
            border-bottom: 2px solid #1DB954;
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tab-button {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .tab-button:hover {
            background: rgba(29, 185, 84, 0.1);
        }
        .tab-button.active {
            background: linear-gradient(135deg, #1DB954 0%, #1ed760 100%);
            color: white;
        }
        .autocomplete-dropdown {
            max-height: 400px;
            overflow-y: auto;
            display: none;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .autocomplete-dropdown::-webkit-scrollbar {
            display: none;
        }
        .autocomplete-dropdown.show {
            display: block;
        }
        .autocomplete-item {
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .autocomplete-item:hover {
            background: rgba(29, 185, 84, 0.15) !important;
            transform: translateX(4px);
        }
        .autocomplete-item:last-child {
            border-bottom: none !important;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen overflow-x-hidden">
    <!-- Animated background gradient -->
    <div class="fixed inset-0 bg-gradient-to-br from-black via-gray-900 to-black opacity-90"></div>
    <div class="fixed inset-0 opacity-20">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-50 glass-card border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8" fill="#1DB954" viewBox="0 0 24 24">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                        </svg>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                            Spotify Tracker
                        </h1>
                    </div>
                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('dashboard') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Dashboard</a>
                        <a href="{{ route('stats') }}" class="nav-link active py-5 px-2 text-sm font-medium">Stats</a>
                    </div>
                </div>
                
                <!-- Search Bar with Autocomplete -->
                <div class="flex-1 max-w-lg mx-8 hidden lg:block relative z-50">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="navSearchInput"
                            placeholder="Search for songs, artists, or albums..." 
                            autocomplete="off"
                            class="w-full pl-11 pr-4 py-2.5 bg-gray-900/70 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/50 transition-all"
                        >
                        
                        <!-- Autocomplete Dropdown -->
                        <div id="navAutocompleteDropdown" class="autocomplete-dropdown absolute w-full mt-2 bg-gray-900 border border-gray-700 rounded-lg shadow-2xl">
                            <!-- Results will be inserted here via JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        @if(isset($user->images[0]))
                            <img src="{{ $user->images[0]->url }}" alt="Profile" class="w-10 h-10 rounded-full ring-2 ring-green-500">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center">
                                <span class="text-white font-bold">{{ substr($user->display_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="text-gray-300 font-medium hidden sm:block">{{ $user->display_name }}</span>
                    </div>
                    <a href="{{ route('spotify.logout') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2">Your Music Stats</h1>
            <p class="text-gray-400">Detailed insights into your listening habits</p>
        </div>

        <!-- Stats Card with Tabs -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl">
            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-3 mb-6 border-b border-gray-800 pb-4">
                <button onclick="switchTab('tracks')" class="tab-button active px-6 py-3 rounded-lg font-semibold flex items-center gap-2" id="tracks-tab">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                    Top Tracks
                </button>
                <button onclick="switchTab('artists')" class="tab-button px-6 py-3 rounded-lg font-semibold text-gray-400 flex items-center gap-2" id="artists-tab">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Top Artists
                </button>
                <button onclick="switchTab('minutes')" class="tab-button px-6 py-3 rounded-lg font-semibold text-gray-400 flex items-center gap-2 relative" id="minutes-tab">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Minutes Listened
                    <span class="ml-2 px-2 py-0.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-bold rounded-full">
                        SOON
                    </span>
                </button>
            </div>

            <!-- Time Range Filter -->
            <div id="time-range-filter" class="flex flex-wrap gap-2 mb-6">
                <a href="?range=short_term" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('range', 'medium_term') == 'short_term' ? 'spotify-gradient text-white shadow-lg' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                    Last Month
                </a>
                <a href="?range=medium_term" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('range', 'medium_term') == 'medium_term' ? 'spotify-gradient text-white shadow-lg' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                    Last 6 Months
                </a>
                <a href="?range=long_term" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('range', 'medium_term') == 'long_term' ? 'spotify-gradient text-white shadow-lg' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                    All Time
                </a>
            </div>

            <!-- Tracks Content -->
            <div id="tracks-content" class="tab-content active">
                <div class="space-y-3">
                    @foreach($topTracks->items as $index => $track)
                        <div class="track-card rounded-xl p-4 flex items-center gap-4">
                            <div class="rank-badge w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold">{{ $index + 1 }}</span>
                            </div>
                            @if(isset($track->album->images[2]))
                                <img src="{{ $track->album->images[2]->url }}" alt="Album" class="w-16 h-16 rounded-lg shadow-lg">
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-white text-lg truncate">{{ $track->name }}</p>
                                <p class="text-sm text-gray-400 truncate">
                                    {{ collect($track->artists)->pluck('name')->join(', ') }}
                                </p>
                            </div>
                            <div class="text-right hidden md:block">
                                <p class="text-sm text-gray-400 truncate max-w-xs">{{ $track->album->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Artists Content -->
            <div id="artists-content" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($topArtists->items as $index => $artist)
                        <div class="artist-card rounded-xl p-5 flex items-center gap-4">
                            @if(isset($artist->images[2]))
                                <img src="{{ $artist->images[2]->url }}" alt="Artist" class="w-16 h-16 rounded-full shadow-lg ring-2 ring-green-500 ring-opacity-50">
                            @else
                                <div class="w-16 h-16 rounded-full spotify-gradient flex items-center justify-center">
                                    <span class="text-white font-bold text-xl">{{ substr($artist->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-white truncate">{{ $artist->name }}</p>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ ucfirst(collect($artist->genres)->take(2)->join(', ')) ?: 'Artist' }}
                                </p>
                                <p class="text-xs text-green-400 mt-1">{{ number_format($artist->followers->total) }} followers</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Minutes Content -->
            <div id="minutes-content" class="tab-content">
                @if($listeningMinutes && ($listeningMinutes['this_week'] > 0 || $listeningMinutes['this_month'] > 0 || $listeningMinutes['this_year'] > 0 || $listeningMinutes['all_time'] > 0))
                    <!-- Real Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gradient-to-br from-green-500/10 to-emerald-600/10 rounded-xl p-6 border border-green-500/20">
                            <p class="text-gray-400 text-sm mb-2">This Week</p>
                            <p class="text-4xl font-bold text-green-400">{{ number_format($listeningMinutes['this_week']) }}</p>
                            <p class="text-xs text-gray-500 mt-2">minutes</p>
                            <p class="text-xs text-green-400/70 mt-1">≈ {{ round($listeningMinutes['this_week'] / 60, 1) }} hours</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-500/10 to-cyan-600/10 rounded-xl p-6 border border-blue-500/20">
                            <p class="text-gray-400 text-sm mb-2">This Month</p>
                            <p class="text-4xl font-bold text-blue-400">{{ number_format($listeningMinutes['this_month']) }}</p>
                            <p class="text-xs text-gray-500 mt-2">minutes</p>
                            <p class="text-xs text-blue-400/70 mt-1">≈ {{ round($listeningMinutes['this_month'] / 60, 1) }} hours</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500/10 to-pink-600/10 rounded-xl p-6 border border-purple-500/20">
                            <p class="text-gray-400 text-sm mb-2">This Year</p>
                            <p class="text-4xl font-bold text-purple-400">{{ number_format($listeningMinutes['this_year']) }}</p>
                            <p class="text-xs text-gray-500 mt-2">minutes</p>
                            <p class="text-xs text-purple-400/70 mt-1">≈ {{ round($listeningMinutes['this_year'] / 60, 1) }} hours</p>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500/10 to-orange-600/10 rounded-xl p-6 border border-yellow-500/20">
                            <p class="text-gray-400 text-sm mb-2">All Time</p>
                            <p class="text-4xl font-bold text-yellow-400">{{ number_format($listeningMinutes['all_time']) }}</p>
                            <p class="text-xs text-gray-500 mt-2">minutes</p>
                            <p class="text-xs text-yellow-400/70 mt-1">≈ {{ round($listeningMinutes['all_time'] / 60, 1) }} hours</p>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div class="flex items-start gap-4 bg-green-500/10 border border-green-500/30 rounded-xl p-6">
                        <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-green-200 font-semibold mb-2">Tracking Active! 🎵</p>
                            <p class="text-green-300/90 text-sm leading-relaxed">
                                Your listening data is being tracked. Run <code class="bg-black/30 px-2 py-0.5 rounded text-xs">php artisan spotify:track</code> 
                                to manually update your stats, or they'll be updated automatically when scheduled tasks are running.
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Preview Stats Grid (No Data Yet) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 opacity-50 pointer-events-none">
                        <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
                            <p class="text-gray-500 text-sm mb-2">This Week</p>
                            <p class="text-4xl font-bold text-gray-600">0</p>
                            <p class="text-xs text-gray-600 mt-2">minutes</p>
                        </div>
                        <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
                            <p class="text-gray-500 text-sm mb-2">This Month</p>
                            <p class="text-4xl font-bold text-gray-600">0</p>
                            <p class="text-xs text-gray-600 mt-2">minutes</p>
                        </div>
                        <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
                            <p class="text-gray-500 text-sm mb-2">This Year</p>
                            <p class="text-4xl font-bold text-gray-600">0</p>
                            <p class="text-xs text-gray-600 mt-2">minutes</p>
                        </div>
                        <div class="bg-gray-900/50 rounded-xl p-6 border border-gray-800">
                            <p class="text-gray-500 text-sm mb-2">All Time</p>
                            <p class="text-4xl font-bold text-gray-600">0</p>
                            <p class="text-xs text-gray-600 mt-2">minutes</p>
                        </div>
                    </div>

                    <!-- Getting Started Message -->
                    <div class="flex items-start gap-4 bg-blue-500/10 border border-blue-500/30 rounded-xl p-6">
                        <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-blue-200 font-semibold mb-2">No Listening Data Yet</p>
                            <p class="text-blue-300/90 text-sm leading-relaxed">
                                Your listening minutes will appear here once tracking begins. Since Spotify doesn't provide 
                                this data directly, we track it by monitoring your playback activity over time. Start listening 
                                to music on Spotify, and your stats will automatically populate here!
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>Powered by Spotify Web API • Built with Laravel</p>
        </div>

    </div>

    <script>
        function switchTab(tab) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
                button.classList.add('text-gray-400');
            });
            
            // Show selected tab content
            document.getElementById(tab + '-content').classList.add('active');
            
            // Activate selected tab button
            const activeButton = document.getElementById(tab + '-tab');
            activeButton.classList.add('active');
            activeButton.classList.remove('text-gray-400');

            // Show/hide time range filter based on tab
            const timeRangeFilter = document.getElementById('time-range-filter');
            if (tab === 'minutes') {
                timeRangeFilter.style.display = 'none';
            } else {
                timeRangeFilter.style.display = 'flex';
            }
        }
    </script>

    <!-- Autocomplete JavaScript -->
    <script>
        const searchInput = document.getElementById('navSearchInput');
        const dropdown = document.getElementById('navAutocompleteDropdown');
        let debounceTimer;
        let currentResults = [];

        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        async function fetchResults(query) {
            if (query.length < 2) {
                dropdown.classList.remove('show');
                currentResults = [];
                return;
            }

            dropdown.innerHTML = '<div class="p-4 text-gray-400 text-center text-sm">Searching...</div>';
            dropdown.classList.add('show');

            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.tracks && data.tracks.length > 0) {
                    currentResults = data.tracks;
                    displayResults(data.tracks);
                } else {
                    currentResults = [];
                    dropdown.innerHTML = '<div class="p-4 text-gray-400 text-center text-sm">No results found</div>';
                    dropdown.classList.add('show');
                }
            } catch (error) {
                console.error('Search error:', error);
                currentResults = [];
                dropdown.innerHTML = '<div class="p-4 text-red-400 text-center text-sm">Error searching</div>';
                dropdown.classList.add('show');
            }
        }

        function displayResults(tracks) {
            dropdown.innerHTML = tracks.map(track => `
                <a href="/track/${track.id}" class="autocomplete-item flex items-center gap-3 p-3 border-b border-gray-800 cursor-pointer block">
                    ${track.image ? `<img src="${track.image}" alt="Album" class="w-12 h-12 rounded-lg shadow-lg flex-shrink-0">` : '<div class="w-12 h-12 bg-gray-800 rounded-lg flex-shrink-0"></div>'}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white truncate text-sm">${escapeHtml(track.name)}</p>
                        <p class="text-xs text-gray-400 truncate">${escapeHtml(track.artists)}</p>
                    </div>
                    <div class="text-xs text-gray-500 flex-shrink-0">${track.duration}</div>
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            `).join('');
            dropdown.classList.add('show');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        searchInput.addEventListener('input', debounce((e) => {
            fetchResults(e.target.value);
        }, 300));

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        searchInput.addEventListener('focus', () => {
            if (dropdown.innerHTML && searchInput.value.length >= 2) {
                dropdown.classList.add('show');
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdown.classList.remove('show');
            } else if (e.key === 'Enter') {
                e.preventDefault();
                // Navigate to search results page with the query
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            }
        });
    </script>
</body>
</html>

