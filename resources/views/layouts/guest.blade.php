<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Al-Fithroh — ELF</title>
    <!-- Tailwind CSS (via Vite) & Livewire Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
        }
        .font-serif-display {
            font-family: 'Playfair Display', serif;
        }
    </style>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 min-h-screen flex flex-col transition-colors duration-200">
    @php
        $logoUrl = \App\Modules\Core\Models\LandingPageContent::where('key', 'logo_url')->value('value');
    @endphp

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-900/80 border-b border-slate-200/50 dark:border-slate-800/50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-8 w-8 object-contain">
                @else
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-emerald-500/20">F</span>
                @endif
                <span class="font-extrabold text-sm tracking-tight text-slate-950 dark:text-white uppercase">Al-Fithroh</span>
            </a>
            
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <a href="#profil" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Profil</a>
                <a href="#visi-misi" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Visi & Misi</a>
                <a href="#kegiatan" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Kegiatan</a>
                <a href="#kontak" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Kontak</a>
            </nav>

            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <button id="theme-toggle" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 dark:focus:ring-slate-700 rounded-lg text-sm p-2 transition-colors">
                    <svg id="theme-toggle-dark-icon" class="hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>
                
                @auth
                    <a href="/dashboard" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/20">Dashboard</a>
                @else
                    <a href="/login" class="px-4 py-2 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold transition-all">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 dark:bg-slate-950 border-t border-slate-800 py-12 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-8 w-8 object-contain">
                @else
                    <span class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-emerald-500/20">F</span>
                @endif
                <span class="font-extrabold text-sm tracking-tight text-white uppercase">Al-Fithroh</span>
            </div>
            <p class="text-xs text-slate-500">&copy; 2026 Pondok Pesantren Al-Fithroh. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <script>
        // Toggle Dark/Light Mode Theme
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>
