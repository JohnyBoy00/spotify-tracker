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
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('dashboard') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Dashboard</a>
                        <a href="{{ route('stats') }}" class="nav-link py-5 px-2 text-sm font-medium text-gray-400">Stats</a>
                        <a href="{{ route('search') }}" class="nav-link active py-5 px-2 text-sm font-medium">Search</a>
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
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-2">Search Spotify</h1>
            <p class="text-gray-400">Find any song, artist, or album</p>
        </div>

        <!-- Search Box -->
        <div class="glass-card rounded-2xl p-8 mb-8 shadow-2xl">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="q" 
                            id="searchInput"
                            value="{{ request('q') }}"
                            placeholder="Search for songs, artists, or albums..." 
                            class="search-input w-full pl-14 pr-4 py-4 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-400 text-lg transition-all"
                            autocomplete="off"
                            autofocus
                        >
                        
                        <!-- Autocomplete Dropdown -->
                        <div id="autocompleteDropdown" class="autocomplete-dropdown absolute w-full mt-2 bg-gray-900 border border-gray-700 rounded-lg shadow-2xl z-50">
                            <!-- Results will be inserted here via JavaScript -->
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-4 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                        Search
                    </button>
                </div>
            </form>
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
                            <div class="track-card rounded-xl p-4 flex items-center gap-4">
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
                                @if($track->preview_url)
                                    <a href="{{ $track->external_urls->spotify }}" target="_blank" class="px-4 py-2 bg-gray-800 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                                        </svg>
                                        Play
                                    </a>
                                @endif
                            </div>
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
                    <span class="px-4 py-2 bg-gray-800 rounded-full text-sm text-gray-300">Try: "Radiohead"</span>
                    <span class="px-4 py-2 bg-gray-800 rounded-full text-sm text-gray-300">Try: "Let Down"</span>
                    <span class="px-4 py-2 bg-gray-800 rounded-full text-sm text-gray-300">Try: "OK Computer"</span>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>Powered by Spotify Web API • Built with Laravel</p>
        </div>

    </div>

    <!-- Autocomplete JavaScript -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const dropdown = document.getElementById('autocompleteDropdown');
        const emptyState = document.getElementById('emptyState');
        let debounceTimer;

        // Debounce function to avoid too many API calls
        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Fetch search results
        async function fetchResults(query) {
            if (query.length < 2) {
                dropdown.classList.remove('show');
                if (emptyState) emptyState.style.display = 'block';
                return;
            }

            // Hide empty state when searching
            if (emptyState) emptyState.style.display = 'none';

            // Show loading state
            dropdown.innerHTML = '<div class="p-4 text-gray-400 text-center">Searching...</div>';
            dropdown.classList.add('show');

            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.tracks && data.tracks.length > 0) {
                    displayResults(data.tracks);
                } else {
                    dropdown.innerHTML = '<div class="p-4 text-gray-400 text-center">No results found</div>';
                    dropdown.classList.add('show');
                }
            } catch (error) {
                console.error('Search error:', error);
                dropdown.innerHTML = '<div class="p-4 text-red-400 text-center">Error searching. Please try again.</div>';
                dropdown.classList.add('show');
            }
        }

        // Display results in dropdown
        function displayResults(tracks) {
            dropdown.innerHTML = tracks.map(track => `
                <a href="${track.spotify_url}" target="_blank" class="autocomplete-item flex items-center gap-4 p-4 border-b border-gray-800 cursor-pointer block">
                    ${track.image ? `<img src="${track.image}" alt="Album" class="w-14 h-14 rounded-lg shadow-lg flex-shrink-0">` : '<div class="w-14 h-14 bg-gray-800 rounded-lg flex-shrink-0"></div>'}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white truncate text-base">${escapeHtml(track.name)}</p>
                        <p class="text-sm text-gray-400 truncate">${escapeHtml(track.artists)}</p>
                    </div>
                    <div class="text-xs text-gray-500 flex-shrink-0">${track.duration}</div>
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            `).join('');
            dropdown.classList.add('show');
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Listen for input changes
        searchInput.addEventListener('input', debounce((e) => {
            fetchResults(e.target.value);
        }, 300));

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
                // Show empty state again if no search query
                if (emptyState && searchInput.value.length < 2) {
                    emptyState.style.display = 'block';
                }
            }
        });

        // Show dropdown again when input is focused (if it has content)
        searchInput.addEventListener('focus', () => {
            if (dropdown.innerHTML && searchInput.value.length >= 2) {
                dropdown.classList.add('show');
                if (emptyState) emptyState.style.display = 'none';
            }
        });

        // Close dropdown when pressing Escape
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdown.classList.remove('show');
                // Show empty state again if no search query
                if (emptyState && searchInput.value.length < 2) {
                    emptyState.style.display = 'block';
                }
            }
        });
    </script>
</body>
</html>

