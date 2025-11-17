<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231DB954'><path stroke-linecap='round' stroke-linejoin='round' d='M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'/></svg>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        .spotify-gradient {
            background: linear-gradient(135deg, #1DB954 0%, #1ed760 100%);
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
        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 185, 84, 0.3);
        }
        .autocomplete-dropdown {
            max-height: 500px;
            overflow-y: auto;
            display: none;
            /* Hide scrollbar for Chrome, Safari and Opera */
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .autocomplete-dropdown::-webkit-scrollbar {
            display: none;  /* Chrome, Safari and Opera */
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
    <nav class="relative z-10 glass-card border-b border-gray-800">
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
                    @if($user)
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('dashboard') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Dashboard</a>
                        <a href="{{ route('stats') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Stats</a>
                        <a href="{{ route('search') }}" class="nav-link active py-5 px-2 text-sm font-medium">Search</a>
                    </div>
                    @else
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('home') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Home</a>
                        <a href="{{ route('search') }}" class="nav-link active py-5 px-2 text-sm font-medium">Search</a>
                    </div>
                    @endif
                </div>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8 hidden md:block">
                    <form action="{{ route('search') }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="Search for songs, artists, or albums..." 
                                class="w-full pl-12 pr-4 py-3 bg-gray-900/70 border border-gray-700 rounded-lg text-white placeholder-gray-400 text-base focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/50 transition-all"
                                autofocus
                            >
                        </div>
                    </form>
                </div>

                <div class="flex items-center gap-4">
                    @if($user)
                    <!-- User Menu Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white/5 transition-all duration-200 group">
                            @if(isset($user->images[0]))
                                <img src="{{ $user->images[0]->url }}" alt="Profile" class="w-10 h-10 rounded-full ring-2 ring-green-500 group-hover:ring-green-400 transition-all">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center ring-2 ring-green-500 group-hover:ring-green-400 transition-all">
                                    <span class="text-white font-bold text-sm">{{ substr($user->display_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="hidden sm:block text-left">
                                <p class="text-white font-semibold text-sm">{{ $user->display_name }}</p>
                                <p class="text-gray-400 text-xs">{{ $user->email ?? 'Spotify User' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 hidden sm:block" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                             class="absolute right-0 mt-3 w-64 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden z-50"
                             style="display: none; background: rgba(17, 17, 17, 0.95); backdrop-filter: blur(20px);">
                            <div class="py-2">
                                <!-- User Info Header -->
                                <div class="px-4 py-4 border-b border-gray-700 bg-gradient-to-br from-green-500/10 to-emerald-500/10">
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Signed in as</p>
                                    <p class="text-sm font-semibold text-white truncate">{{ $user->display_name }}</p>
                                    @if($user->email)
                                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                                    @endif
                                </div>
                                
                                <!-- Menu Items -->
                                <div class="py-1">
                                    <a href="{{ $user->external_urls->spotify ?? '#' }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-green-500/10 hover:text-green-400 transition-all group">
                                        <div class="p-1.5 rounded-lg bg-green-500/10 group-hover:bg-green-500/20 transition-colors">
                                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                                            </svg>
                                        </div>
                                        <span class="font-medium">View Spotify Profile</span>
                                        <svg class="w-3.5 h-3.5 ml-auto opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                                
                                <div class="border-t border-gray-700/50 my-1"></div>
                                
                                <!-- Logout -->
                                <div class="py-1">
                                    <a href="{{ route('spotify.logout') }}" 
                                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-all group">
                                        <div class="p-1.5 rounded-lg bg-red-500/10 group-hover:bg-red-500/20 transition-colors">
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium">Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Login Button for Non-Logged-In Users -->
                    <a href="{{ route('home') }}" class="px-6 py-2.5 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-full transition-all duration-300 transform hover:scale-105">
                        Login
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Login Banner for Non-Logged-In Users -->
        @if(!$user)
            <div class="mb-8 glass-card rounded-2xl p-6 border-2 border-green-500/30 shadow-2xl">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">Unlock More Features!</h3>
                        <p class="text-gray-300 mb-4">
                            You're currently browsing as a guest. Connect your Spotify account to access personalized features like listening history, analytics, top tracks, and more!
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-2.5 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                                </svg>
                                Connect with Spotify
                            </a>
                            <a href="{{ route('home') }}#features" class="inline-flex items-center gap-2 px-6 py-2.5 glass-card hover:bg-white/10 text-white font-medium rounded-lg transition-all duration-300">
                                Learn More
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-500 text-red-200 px-6 py-4 rounded-lg flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Page Header -->
        <div class="mb-8">
            @if(request('q'))
                <h1 class="text-4xl font-bold mb-2">Search Results</h1>
                <p class="text-gray-400">Showing results for "<span class="text-green-400">{{ request('q') }}</span>"</p>
            @else
                <h1 class="text-4xl font-bold mb-2">Search Spotify</h1>
                <p class="text-gray-400">Use the search bar above to find any song, artist, or album</p>
            @endif
        </div>

        @if(request('q'))
            @if(isset($results) && count($results->tracks->items) > 0)
                <!-- Search Results -->
                <div class="glass-card rounded-2xl p-8 shadow-2xl">
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        <h2 class="text-2xl font-bold">Search Results for "{{ request('q') }}"</h2>
                        <span class="text-gray-400 text-sm">({{ count($results->tracks->items) }} tracks found)</span>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($results->tracks->items as $track)
                            <a href="{{ route('track.details', $track->id) }}" class="track-card rounded-xl p-4 flex items-center gap-4 block">
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
                                    <p class="text-xs text-gray-500">{{ gmdate('i:s', $track->duration_ms / 1000) }}</p>
                                </div>
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- No Results -->
                <div class="glass-card rounded-2xl p-12 shadow-2xl text-center">
                    <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold mb-2">No results found</h3>
                    <p class="text-gray-400">Try searching for something else</p>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div id="emptyState" class="glass-card rounded-2xl p-12 shadow-2xl text-center">
                <svg class="w-24 h-24 mx-auto mb-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-2xl font-bold mb-2">Start Searching</h3>
                <p class="text-gray-400 mb-6">Find any song, artist, or album on Spotify</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('search') }}?q=Radiohead" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-full text-sm text-gray-300 hover:text-white transition-all">Try: "Radiohead"</a>
                    <a href="{{ route('search') }}?q=Let+Down" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-full text-sm text-gray-300 hover:text-white transition-all">Try: "Let Down"</a>
                    <a href="{{ route('search') }}?q=OK+Computer" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-full text-sm text-gray-300 hover:text-white transition-all">Try: "OK Computer"</a>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>Powered by Spotify Web API • Built with Laravel</p>
        </div>

    </div>
</body>
</html>

