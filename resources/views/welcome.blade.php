<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spotify Tracker</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%231DB954'><path stroke-linecap='round' stroke-linejoin='round' d='M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3'/></svg>">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
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
                transform: translateY(-2px);
            }
            .spotify-logo {
                filter: drop-shadow(0 0 20px rgba(29, 185, 84, 0.3));
            }
            </style>
    </head>
    <body class="bg-black text-white min-h-screen flex items-center justify-center p-4 overflow-hidden">
        <!-- Animated background gradient -->
        <div class="fixed inset-0 bg-gradient-to-br from-black via-gray-900 to-black opacity-90"></div>
        <div class="fixed inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-4xl mx-auto">
            <!-- Header with Spotify Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 mb-4">
                    <!-- Spotify Icon -->
                    <svg class="w-12 h-12 spotify-logo" fill="#1DB954" viewBox="0 0 24 24">
                        <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                    </svg>
                    <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                        Spotify Tracker
                    </h1>
                </div>
                <p class="text-gray-400 text-lg">Discover insights about your music taste</p>
            </div>

            <!-- Main Card -->
            <div class="glass-card rounded-2xl p-8 md:p-12 shadow-2xl">
                <!-- Features Grid -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <!-- Feature 1 -->
                    <div class="feature-card rounded-xl p-6">
                        <div class="flex flex-col items-center text-center gap-3">
                            <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Your Top Music</h3>
                                <p class="text-sm text-gray-400">Most played tracks, artists, and genres</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card rounded-xl p-6">
                        <div class="flex flex-col items-center text-center gap-3">
                            <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Listening History</h3>
                                <p class="text-sm text-gray-400">Recent tracks and patterns</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card rounded-xl p-6">
                        <div class="flex flex-col items-center text-center gap-3">
                            <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Privacy First</h3>
                                <p class="text-sm text-gray-400">Data stays secure, never shared</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Privacy Notice -->
                <div class="bg-gray-900 bg-opacity-60 rounded-xl p-6 mb-8 border border-gray-800">
                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-white mb-2">What Data We Access</h3>
                            <ul class="text-sm text-gray-400 space-y-2">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Your top tracks and artists</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Recently played tracks</span>
                        </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Saved library (tracks and albums)</span>
                        </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Basic profile information (name, email, country)</span>
                        </li>
                    </ul>
                        </div>
                    </div>
                    <div class="bg-green-500 bg-opacity-10 border border-green-500 border-opacity-30 rounded-lg p-4">
                        <p class="text-sm text-gray-300">
                            <span class="font-semibold text-green-400">🔒 Privacy Guarantee:</span> 
                            We only display your data to you. We do not store, sell, or share your information with third parties. This is a personal project for viewing your own Spotify statistics.
                        </p>
                    </div>
                </div>

                <!-- Connect Button -->
                <div class="text-center mb-6">
                    <a href="{{ route('spotify.auth') }}" class="inline-flex items-center gap-3 px-8 py-4 spotify-gradient hover:shadow-lg hover:shadow-green-500/50 text-white font-semibold rounded-full transition-all duration-300 transform hover:scale-105 text-lg group">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                    </svg>
                        Connect with Spotify
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                    </a>
                </div>

                <!-- Footer -->
                <p class="text-center text-xs text-gray-500">
                    By connecting, you agree to share the data listed above. 
                    You can revoke access anytime from your 
                    <a href="https://www.spotify.com/account/apps/" target="_blank" class="text-green-400 hover:text-green-300 underline transition-colors">
                        Spotify account settings
                    </a>.
                </p>
            </div>

            <!-- Powered by -->
            <div class="text-center mt-6 text-gray-600 text-sm">
                <p>Built with Laravel • Powered by Spotify Web API</p>
            </div>
        </div>
    </body>
</html>
