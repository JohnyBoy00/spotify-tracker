<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $track->name }} - Spotify Tracker</title>
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
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card {
            background: rgba(29, 185, 84, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            background: rgba(29, 185, 84, 0.1);
            border-color: rgba(29, 185, 84, 0.3);
            transform: translateY(-2px);
        }
        .audio-feature-bar {
            transition: width 0.5s ease;
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
                        <a href="{{ route('stats') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Stats</a>
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

        <!-- Track Header -->
        <div class="glass-card rounded-2xl p-8 mb-8 shadow-2xl fade-in">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Album Art -->
                <div class="flex-shrink-0 relative group cursor-pointer" id="album-art-container">
                    @if(isset($track->album->images[0]))
                        <img src="{{ $track->album->images[0]->url }}" alt="Album Art" class="w-64 h-64 rounded-xl shadow-2xl transition-transform duration-300 group-hover:scale-105">
                        <!-- Download Overlay -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl flex flex-col items-center justify-center pointer-events-none">
                            <svg class="w-16 h-16 text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <p class="text-white font-semibold text-lg">Click to Download</p>
                            <p class="text-gray-300 text-sm mt-1">High Quality ({{ $track->album->images[0]->width ?? '640' }}x{{ $track->album->images[0]->height ?? '640' }})</p>
                        </div>
                    @endif
                </div>

                <!-- Track Info -->
                <div class="flex-1 flex flex-col justify-center">
                    <p class="text-sm text-gray-400 uppercase tracking-wide mb-2">Song</p>
                    <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                        {{ $track->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-lg mb-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-white font-medium">{{ collect($track->artists)->pluck('name')->join(', ') }}</span>
                        </div>
                        <span class="text-gray-600">•</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"></path>
                            </svg>
                            <span class="text-gray-300">{{ $track->album->name }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $track->external_urls->spotify }}" target="_blank" class="px-6 py-3 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                            </svg>
                            Play on Spotify
                        </a>
                        @if($track->preview_url)
                            <button onclick="togglePreview()" id="previewBtn" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all flex items-center gap-2">
                                <svg id="playIcon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                <span id="previewText">Preview</span>
                            </button>
                            <audio id="audioPreview" src="{{ $track->preview_url }}"></audio>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Duration Card -->
            <div class="group stat-card rounded-xl p-6 text-center fade-in relative overflow-hidden" style="animation-delay: 0.1s;">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 mb-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent mb-1">
                        {{ gmdate('i:s', $track->duration_ms / 1000) }}
                    </p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Duration</p>
                </div>
            </div>

            <!-- Popularity Card -->
            <div class="group stat-card rounded-xl p-6 text-center fade-in relative overflow-hidden" style="animation-delay: 0.2s;">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-green-500/20 to-emerald-500/20 mb-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-1">
                        {{ $track->popularity }}<span class="text-xl">/100</span>
                    </p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Popularity</p>
                </div>
            </div>

            <!-- Track Number Card -->
            <div class="group stat-card rounded-xl p-6 text-center fade-in relative overflow-hidden" style="animation-delay: 0.3s;">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-purple-500/20 to-pink-500/20 mb-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-1">
                        #{{ $track->track_number }}
                    </p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Track Number</p>
                </div>
            </div>

            <!-- Release Year Card -->
            <div class="group stat-card rounded-xl p-6 text-center fade-in relative overflow-hidden" style="animation-delay: 0.4s;">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-orange-500/20 to-red-500/20 mb-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent mb-1">
                        {{ date('Y', strtotime($track->album->release_date)) }}
                    </p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Release Year</p>
                </div>
            </div>
        </div>

        @if(isset($youtubeVideoId))
        <!-- YouTube Music Video -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl fade-in mb-8" style="animation-delay: 0.5s;">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                <h2 class="text-2xl font-bold">Music Video</h2>
            </div>
            
            <div class="relative w-full" style="padding-bottom: 56.25%;">
                <iframe 
                    class="absolute top-0 left-0 w-full h-full rounded-xl"
                    src="https://www.youtube.com/embed/{{ $youtubeVideoId }}?rel=0&modestbranding=1" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        @if(isset($lyrics) && ($lyrics['text'] || $lyrics['url']))
        <!-- Song Lyrics -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl fade-in mb-8" style="animation-delay: 0.6s;">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-yellow-400 to-orange-400 rounded-lg">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Lyrics</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Powered by Genius</p>
                    </div>
                </div>
                @if(isset($lyrics['url']))
                <a href="{{ $lyrics['url'] }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-black text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-105 shadow-lg">
                    <span>View on Genius</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                @endif
            </div>
            
            @if(isset($lyrics['text']))
            <!-- Display lyrics text -->
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-orange-500/5 rounded-xl blur-xl"></div>
                <div class="relative bg-gradient-to-br from-black/50 to-gray-900/50 rounded-xl p-8 max-h-[500px] overflow-y-auto border border-yellow-500/20 backdrop-blur-sm"
                     style="scrollbar-width: thin; scrollbar-color: rgba(234, 179, 8, 0.5) rgba(0, 0, 0, 0.3);">
                    <style>
                        .lyrics-container::-webkit-scrollbar {
                            width: 8px;
                        }
                        .lyrics-container::-webkit-scrollbar-track {
                            background: rgba(0, 0, 0, 0.3);
                            border-radius: 10px;
                        }
                        .lyrics-container::-webkit-scrollbar-thumb {
                            background: rgba(234, 179, 8, 0.5);
                            border-radius: 10px;
                        }
                        .lyrics-container::-webkit-scrollbar-thumb:hover {
                            background: rgba(234, 179, 8, 0.7);
                        }
                        .lyrics-text {
                            line-height: 1.8;
                            letter-spacing: 0.02em;
                        }
                    </style>
                    <pre class="lyrics-text text-gray-100 whitespace-pre-wrap font-sans text-base leading-relaxed">{{ $lyrics['text'] }}</pre>
                </div>
            </div>
            @else
            <!-- Fallback if lyrics couldn't be scraped -->
            <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-xl p-6 border border-yellow-500/20">
                <div class="flex items-center gap-4">
                    @if(isset($lyrics['thumbnail']))
                    <img src="{{ $lyrics['thumbnail'] }}" alt="Song artwork" class="w-20 h-20 rounded-xl shadow-lg">
                    @endif
                    
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-white mb-1">{{ $lyrics['title'] ?? 'Unknown' }}</h3>
                        <p class="text-gray-400 text-sm mb-2">{{ $lyrics['artist'] ?? 'Unknown Artist' }}</p>
                        <p class="text-yellow-400 text-sm font-medium">🎵 Full lyrics available on Genius.com</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        @if(isset($audioFeatures))
        <!-- Audio Features -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl fade-in" style="animation-delay: 0.6s;">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
                <h2 class="text-2xl font-bold">Audio Features</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Energy -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Energy</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->energy * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ $audioFeatures->energy * 100 }}%"></div>
                    </div>
                </div>

                <!-- Danceability -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Danceability</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->danceability * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ $audioFeatures->danceability * 100 }}%"></div>
                    </div>
                </div>

                <!-- Valence (Positivity) -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Positivity</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->valence * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ $audioFeatures->valence * 100 }}%"></div>
                    </div>
                </div>

                <!-- Acousticness -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Acousticness</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->acousticness * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ $audioFeatures->acousticness * 100 }}%"></div>
                    </div>
                </div>

                <!-- Instrumentalness -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Instrumentalness</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->instrumentalness * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ $audioFeatures->instrumentalness * 100 }}%"></div>
                    </div>
                </div>

                <!-- Speechiness -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Speechiness</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->speechiness * 100) }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ $audioFeatures->speechiness * 100 }}%"></div>
                    </div>
                </div>

                <!-- Tempo -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Tempo</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->tempo) }} BPM</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ min(100, ($audioFeatures->tempo / 200) * 100) }}%"></div>
                    </div>
                </div>

                <!-- Loudness -->
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-300 font-medium">Loudness</span>
                        <span class="text-green-400 font-bold">{{ round($audioFeatures->loudness) }} dB</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3">
                        <div class="audio-feature-bar spotify-gradient h-3 rounded-full" style="width: {{ max(0, min(100, (($audioFeatures->loudness + 60) / 60) * 100)) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-6 border-t border-gray-800">
                <div class="text-center">
                    <p class="text-gray-400 text-sm mb-1">Key</p>
                    <p class="text-white font-bold">{{ ['C', 'C♯', 'D', 'D♯', 'E', 'F', 'F♯', 'G', 'G♯', 'A', 'A♯', 'B'][$audioFeatures->key] ?? 'Unknown' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-400 text-sm mb-1">Mode</p>
                    <p class="text-white font-bold">{{ $audioFeatures->mode == 1 ? 'Major' : 'Minor' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-400 text-sm mb-1">Time Signature</p>
                    <p class="text-white font-bold">{{ $audioFeatures->time_signature }}/4</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-400 text-sm mb-1">Liveness</p>
                    <p class="text-white font-bold">{{ round($audioFeatures->liveness * 100) }}%</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>Powered by Spotify Web API • Built with Laravel</p>
        </div>

    </div>

    <!-- Preview Audio Controls Script -->
    <script>
        const audio = document.getElementById('audioPreview');
        const playIcon = document.getElementById('playIcon');
        const previewText = document.getElementById('previewText');
        const previewBtn = document.getElementById('previewBtn');

        function togglePreview() {
            if (audio.paused) {
                audio.play();
                playIcon.innerHTML = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
                previewText.textContent = 'Pause';
            } else {
                audio.pause();
                playIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>';
                previewText.textContent = 'Preview';
            }
        }

        // Reset button when audio ends
        if (audio) {
            audio.addEventListener('ended', () => {
                playIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>';
                previewText.textContent = 'Preview';
            });
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

    <!-- Album Art Download Script -->
    <script>
        // Handle click-to-download album art
        const albumArtContainer = document.getElementById('album-art-container');
        
        if (albumArtContainer) {
            albumArtContainer.addEventListener('click', async function() {
                const imageUrl = '{{ $track->album->images[0]->url ?? '' }}';
                const fileName = '{{ $track->name }} - {{ $track->album->name }}.jpg';
                
                if (!imageUrl) return;
                
                try {
                    // Fetch the image
                    const response = await fetch(imageUrl);
                    const blob = await response.blob();
                    
                    // Create a temporary URL for the blob
                    const blobUrl = window.URL.createObjectURL(blob);
                    
                    // Create a temporary anchor element and trigger download
                    const link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    
                    // Clean up
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(blobUrl);
                } catch (error) {
                    console.error('Download failed:', error);
                    // Fallback: open in new tab
                    window.open(imageUrl, '_blank');
                }
            });
        }
    </script>
</body>
</html>

