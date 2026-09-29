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
        $cms = \App\Modules\Core\Models\LandingPageContent::getContent();
        $logoUrl = $cms['logo_url'] ?? null;
        $pondokName = $cms['pondok_name'] ?? 'Al-Fithroh';
        $heroSubtitle = $cms['hero_subtitle'] ?? 'Pondok Pesantren Al-Fithroh membimbing santri dengan tradisi keilmuan Ahlussunnah wal Jama\'ah sejak tahun 1970.';
        $contactAddress = $cms['contact_address'] ?? 'Jejeran, Wonokromo, Pleret, Bantul, DIY';
        $contactEmail = $cms['contact_email'] ?? 'info@alfithroh.ac.id';
        $igUsername = ltrim($cms['ig_username'] ?? 'alfithroh.jejeran', '@');
        $waPutra1 = $cms['wa_putra1'] ?? '0812-3456-789';
        $waPutra2 = $cms['wa_putra2'] ?? '0812-9876-543';
        $waPutri = $cms['wa_putri'] ?? '0811-1222-333';
        $waPutraText = implode(' / ', array_filter([$waPutra1, $waPutra2]));
    @endphp

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-900/80 border-b border-slate-200/50 dark:border-slate-800/50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo {{ $pondokName }}" class="h-11 sm:h-12 max-h-12 w-auto object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm flex-shrink-0" style="max-height: 48px; width: auto;">
                @else
                    <span class="w-11 h-11 rounded-2xl bg-emerald-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-emerald-500/20">F</span>
                @endif
                <div class="flex flex-col">
                    <span class="font-extrabold text-base sm:text-lg tracking-tight text-slate-950 dark:text-white uppercase leading-tight font-serif-display">{{ $pondokName }}</span>
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 tracking-wider uppercase">Jejeran Bantul</span>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600 dark:text-slate-400">
                <a href="#profil" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Profil</a>
                <a href="#visi-misi" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Visi & Misi</a>
                <a href="#kegiatan" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Kegiatan</a>
                <a href="#pendaftaran" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Pendaftaran</a>
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
    <footer class="bg-slate-900 dark:bg-slate-950 border-t border-slate-800 py-16 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">

                {{-- Brand --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo" class="h-9 w-9 object-contain">
                        @else
                            <span class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-emerald-500/20">F</span>
                        @endif
                        <div>
                            <span class="font-extrabold text-sm tracking-tight text-white uppercase block">{{ $pondokName }}</span>
                            <span class="text-[10px] text-slate-500">Jejeran Bantul, DIY</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        {{ $heroSubtitle }}
                    </p>
                    @if($igUsername)
                        <a href="https://www.instagram.com/{{ $igUsername }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 text-xs text-pink-400 hover:text-pink-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            &#64;{{ $igUsername }}
                        </a>
                    @endif
                </div>

                {{-- Quick Links --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Navigasi</h4>
                    <ul class="space-y-2">
                        @foreach([
                            ['#profil', 'Profil Pondok'],
                            ['#visi-misi', 'Visi & Misi'],
                            ['#kegiatan', 'Aktivitas & Galeri'],
                            ['#pendaftaran', 'Langkah Pendaftaran'],
                            ['#kontak', 'Kontak & Lokasi'],
                            ['/portal-wali', 'Portal Wali Santri'],
                        ] as [$href, $label])
                            <li>
                                <a href="{{ $href }}" class="text-xs text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500/50 inline-block"></span>
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Kontak Ringkas --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Hubungi Kami</h4>
                    <ul class="space-y-3">
                        @if($contactAddress)
                            <li class="flex items-start gap-2 text-xs text-slate-400">
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $contactAddress }}
                            </li>
                        @endif
                        @if($waPutraText)
                            <li class="flex items-start gap-2 text-xs">
                                <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                <span class="text-green-400">Putra: {{ $waPutraText }}</span>
                            </li>
                        @endif
                        @if($waPutri)
                            <li class="flex items-start gap-2 text-xs">
                                <svg class="w-3.5 h-3.5 text-rose-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                <span class="text-rose-400">Putri: {{ $waPutri }}</span>
                            </li>
                        @endif
                        @if($contactEmail)
                            <li class="flex items-start gap-2 text-xs text-slate-400">
                                <svg class="w-3.5 h-3.5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ $contactEmail }}
                            </li>
                        @endif
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} {{ $pondokName }}. Hak Cipta Dilindungi.</p>
                <a href="/login" class="text-xs text-slate-500 hover:text-emerald-400 transition-colors">Login Pengurus →</a>
            </div>
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
