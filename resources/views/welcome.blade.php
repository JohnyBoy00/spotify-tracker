<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Spotify Tracker - Discover Your Music Journey</title>
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231DB954'><path stroke-linecap='round' stroke-linejoin='round' d='M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'/></svg>">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }
            .spotify-gradient {
                background: linear-gradient(135deg, #1DB954 0%, #1ed760 100%);
            }
            .glass-card {
                background: rgba(23, 23, 23, 0.6);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .feature-card {
                background: rgba(29, 185, 84, 0.08);
                border: 1px solid rgba(29, 185, 84, 0.2);
                transition: all 0.3s ease;
            }
            .feature-card:hover {
                background: rgba(29, 185, 84, 0.12);
                border-color: rgba(29, 185, 84, 0.3);
                transform: translateY(-4px);
            }
            .spotify-logo {
                filter: drop-shadow(0 0 20px rgba(29, 185, 84, 0.3));
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            .float-animation {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .fade-in-up {
                animation: fadeInUp 0.6s ease-out forwards;
            }
            .delay-100 { animation-delay: 0.1s; }
            .delay-200 { animation-delay: 0.2s; }
            .delay-300 { animation-delay: 0.3s; }
            .delay-400 { animation-delay: 0.4s; }
            .delay-500 { animation-delay: 0.5s; }
            .delay-600 { animation-delay: 0.6s; }
            
            /* Scroll reveal */
            .scroll-reveal {
                opacity: 0;
                transform: translateY(50px);
                transition: all 0.8s ease-out;
            }
            .scroll-reveal.revealed {
                opacity: 1;
                transform: translateY(0);
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 10px;
            }
            ::-webkit-scrollbar-track {
                background: #0a0a0a;
            }
            ::-webkit-scrollbar-thumb {
                background: #1DB954;
                border-radius: 5px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #1ed760;
            }
        </style>
    </head>
    <body class="bg-black text-white overflow-x-hidden" x-data="{ loginModal: false, termsAccepted: false }">
        <!-- Animated background gradient -->
        <div class="fixed inset-0 bg-gradient-to-br from-black via-gray-900 to-black"></div>
        <div class="fixed inset-0 opacity-30 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 right-1/3 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <!-- Navigation Bar -->
        <nav class="relative z-50 px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 spotify-logo" fill="#1DB954" viewBox="0 0 24 24">
                        <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                    </svg>
                    <span class="text-xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">Spotify Tracker</span>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Search Bar -->
                    <div class="hidden md:block relative" x-data="{ 
                        searchQuery: '', 
                        searchResults: [], 
                        isSearching: false,
                        showDropdown: false,
                        async searchTracks() {
                            if (this.searchQuery.length < 2) {
                                this.searchResults = [];
                                this.showDropdown = false;
                                return;
                            }
                            
                            this.isSearching = true;
                            try {
                                const response = await fetch(`/api/search?q=${encodeURIComponent(this.searchQuery)}`);
                                const data = await response.json();
                                this.searchResults = data.tracks?.items || [];
                                this.showDropdown = this.searchResults.length > 0;
                            } catch (error) {
                                console.error('Search error:', error);
                            } finally {
                                this.isSearching = false;
                            }
                        },
                        submitSearch() {
                            if (this.searchQuery.trim()) {
                                window.location.href = `/search?q=${encodeURIComponent(this.searchQuery)}`;
                            }
                        }
                    }" @click.away="showDropdown = false">
                        <form @submit.prevent="submitSearch()">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    @input.debounce.300ms="searchTracks()"
                                    @focus="if (searchResults.length > 0) showDropdown = true"
                                    @keydown.enter="submitSearch()"
                                    placeholder="Search tracks..." 
                                    class="w-64 pl-10 pr-4 py-2 glass-card text-white placeholder-gray-400 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-green-500/50 transition-all"
                                >
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                
                                <!-- Loading Spinner -->
                                <div x-show="isSearching" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <svg class="animate-spin h-4 w-4 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Search Results Dropdown -->
                        <div x-show="showDropdown" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute top-full mt-2 w-96 glass-card rounded-2xl shadow-2xl border border-gray-700 overflow-hidden z-50 max-h-96 overflow-y-auto"
                             style="display: none; scrollbar-width: thin; scrollbar-color: rgba(29, 185, 84, 0.5) rgba(0, 0, 0, 0.3);">
                            <template x-for="track in searchResults.slice(0, 8)" :key="track.id">
                                <a :href="`/track/${track.id}`" 
                                   class="flex items-center gap-3 p-3 hover:bg-green-500/10 transition-all border-b border-gray-800/50 last:border-b-0">
                                    <img :src="track.album.images[2]?.url || track.album.images[0]?.url" 
                                         :alt="track.name"
                                         class="w-12 h-12 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium truncate" x-text="track.name"></p>
                                        <p class="text-gray-400 text-sm truncate" x-text="track.artists.map(a => a.name).join(', ')"></p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </template>
                            
                            <!-- View All Results Option -->
                            <div x-show="searchQuery.length >= 2" class="border-t border-gray-700/50">
                                <button @click="submitSearch()" class="w-full p-3 text-center text-sm text-green-400 hover:bg-green-500/10 transition-all flex items-center justify-center gap-2">
                                    <span>View all results for "<span x-text="searchQuery"></span>"</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button @click="loginModal = true" class="px-6 py-2.5 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-full transition-all duration-300 transform hover:scale-105">
                        Get Started
                    </button>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative z-10 min-h-screen flex items-center justify-center px-6 pt-20 pb-32">
            <div class="max-w-6xl mx-auto text-center">
                <div class="fade-in-up opacity-0">
                    <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                        <span class="bg-gradient-to-r from-green-400 via-emerald-400 to-green-500 bg-clip-text text-transparent">
                            Discover Your
                        </span>
                        <br>
                        <span class="text-white">Music Journey</span>
                    </h1>
                </div>
                
                <p class="text-xl md:text-2xl text-gray-400 mb-12 max-w-3xl mx-auto fade-in-up opacity-0 delay-100">
                    Unlock deep insights into your Spotify listening habits. Track your favorite songs, analyze patterns, and explore your musical evolution.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center fade-in-up opacity-0 delay-150">
                    <button @click="loginModal = true" class="group inline-flex items-center gap-3 px-8 py-4 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-full transition-all duration-300 transform hover:scale-105 text-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                        </svg>
                        Connect with Spotify
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                    <a href="{{ route('search') }}" class="inline-flex items-center gap-2 px-8 py-4 glass-card hover:bg-white/10 text-white font-semibold rounded-full transition-all duration-300">
                        Browse Music
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Floating mockup placeholder -->
                <div class="mt-20 fade-in-up opacity-0 delay-300">
                    <div class="glass-card rounded-3xl p-8 max-w-4xl mx-auto float-animation">
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="h-3 bg-gradient-to-r from-green-400 to-emerald-400 rounded-full"></div>
                            <div class="h-3 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full"></div>
                            <div class="h-3 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full"></div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 bg-black/40 rounded-xl p-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-600 rounded-lg"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                                    <div class="h-3 bg-gray-800 rounded w-1/2"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 bg-black/40 rounded-xl p-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-600 rounded-lg"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 bg-gray-700 rounded w-2/3"></div>
                                    <div class="h-3 bg-gray-800 rounded w-1/3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative z-10 px-6 py-32">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20 scroll-reveal">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">
                        <span class="bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">Powerful Features</span>
                    </h2>
                    <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                        Everything you need to understand your music taste and listening habits
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="feature-card rounded-2xl p-8 scroll-reveal">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Top Tracks & Artists</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Discover your most played songs and favorite artists. See what you've been vibing to the most.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card rounded-2xl p-8 scroll-reveal">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Listening Analytics</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Visualize your listening patterns with beautiful charts. Track minutes listened over time.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card rounded-2xl p-8 scroll-reveal">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-cyan-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Recent History</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Browse your recently played tracks with detailed information and accurate skip detection.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card rounded-2xl p-8 scroll-reveal">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Track Search</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Search any song and get detailed information including YouTube videos and lyrics.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card rounded-2xl p-8 scroll-reveal">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-rose-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Import History</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Import your extended Spotify streaming history for deeper insights into your listening journey.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card rounded-2xl p-8 scroll-reveal">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Privacy First</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Your data stays secure and private. We never store, sell, or share your information.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative z-10 px-6 py-32">
            <div class="max-w-4xl mx-auto text-center scroll-reveal">
                <div class="glass-card rounded-3xl p-12 md:p-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">
                        Ready to Explore Your <span class="bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">Music Story</span>?
                    </h2>
                    <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto">
                        Connect your Spotify account in seconds and start discovering insights about your listening habits.
                    </p>
                    <button @click="loginModal = true" class="group inline-flex items-center gap-3 px-10 py-5 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-bold rounded-full transition-all duration-300 transform hover:scale-105 text-xl">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                        </svg>
                        Get Started Now
                        <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 px-6 py-12 border-t border-gray-800">
            <div class="max-w-7xl mx-auto text-center">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <svg class="w-8 h-8" fill="#1DB954" viewBox="0 0 24 24">
                        <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-400">Spotify Tracker</span>
                </div>
                <p class="text-gray-500 text-sm mb-4">Built with Laravel • Powered by Spotify Web API</p>
                <p class="text-gray-600 text-xs">
                    Not affiliated with Spotify AB. All Spotify trademarks are property of Spotify AB.
                </p>
            </div>
        </footer>

        <!-- Login Modal -->
        <div x-show="loginModal" 
             x-cloak
             @click.self="loginModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="w-full max-w-2xl rounded-3xl overflow-hidden"
                 style="background: rgba(17, 17, 17, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(107, 114, 128, 0.3);"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop>
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Connect with Spotify</h3>
                                <p class="text-sm text-gray-400">Review terms before continuing</p>
                            </div>
                        </div>
                        <button @click="loginModal = false" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[60vh] overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: rgba(29, 185, 84, 0.5) rgba(0, 0, 0, 0.3);">
                    <!-- Terms & Conditions -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            What Data We Collect & Store
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Listening History:</strong> Recently played tracks with timestamps to calculate accurate listening minutes and detect skipped songs</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Top Tracks & Artists:</strong> Your most played songs and favorite artists for personalized insights</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Daily Summaries:</strong> Aggregated listening data to display charts and monthly statistics</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Profile Information:</strong> Basic details (name, email, country) to personalize your dashboard</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Imported History:</strong> Optional extended streaming history files you choose to upload for deeper insights</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Privacy Guarantee -->
                    <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-200 font-semibold mb-1">🔒 Privacy Guarantee</p>
                                <p class="text-sm text-gray-300">
                                    Your data is stored securely in our database solely for displaying your personal statistics. We <strong>never sell or share</strong> your information with third parties. All data is linked only to your account and visible only to you.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Why We Store Data -->
                    <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
                        <h5 class="text-sm font-semibold text-white mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Why We Store Your Data
                        </h5>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            We store your listening history to provide accurate analytics, track your listening minutes over time, generate monthly charts, and maintain historical data even when Spotify's API only provides recent activity. This allows you to see your complete music journey.
                        </p>
                    </div>

                    <!-- Revoke Access Info -->
                    <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
                        <p class="text-sm text-gray-400">
                            You can revoke access anytime from your 
                            <a href="https://www.spotify.com/account/apps/" target="_blank" class="text-green-400 hover:text-green-300 underline transition-colors font-medium">
                                Spotify account settings
                            </a>.
                        </p>
                    </div>

                    <!-- Terms Acceptance Checkbox -->
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" 
                                   x-model="termsAccepted" 
                                   class="w-5 h-5 rounded border-2 border-gray-600 bg-gray-800 checked:bg-green-500 checked:border-green-500 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all cursor-pointer">
                        </div>
                        <span class="text-sm text-gray-300 group-hover:text-white transition-colors">
                            I have read and agree to the data access terms and privacy policy outlined above
                        </span>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 border-t border-gray-700 bg-gray-900/50">
                    <div class="flex gap-3">
                        <button @click="loginModal = false" class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-xl transition-all">
                            Cancel
                        </button>
                        <a :href="termsAccepted ? '{{ route('spotify.auth') }}' : '#'" 
                           @click="if (!termsAccepted) { $event.preventDefault(); }"
                           :class="termsAccepted ? 'opacity-100 cursor-pointer' : 'opacity-50 cursor-not-allowed'"
                           class="flex-1 px-6 py-3 spotify-gradient text-white font-semibold rounded-xl transition-all text-center flex items-center justify-center gap-2 hover:shadow-lg hover:shadow-green-500/50">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                            </svg>
                            Continue with Spotify
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Reveal Script -->
        <script>
            // Scroll reveal animation
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, observerOptions);

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
            });
        </script>
    </body>
</html>
