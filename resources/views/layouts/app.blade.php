<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elvith.id — Sistem Informasi Pesantren Al-Fithroh</title>
    <!-- Tailwind CSS (via Vite) & Livewire Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
    <script>
        // Apply dark/light theme immediately on first load (before paint) AND after every wire:navigate
        function applyTheme() {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyTheme();
        // Re-apply after every Livewire SPA navigation
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
    <style>[x-cloak] { display: none !important; }</style>
    {{-- Choices.js (Select2-like search dropdown) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        /* ============================================================
           Choices.js Theme Override (Dark Mode Compatible)
           ============================================================ */
        .choices {
            font-family: inherit;
            font-size: 0.875rem;
        }
        .choices__inner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.375rem 0.75rem;
            min-height: unset;
            font-size: 0.875rem;
        }
        .dark .choices__inner {
            background: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        .choices__list--dropdown,
        .choices__list[aria-expanded] {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
            overflow: hidden;
            z-index: 9999;
        }
        .dark .choices__list--dropdown,
        .dark .choices__list[aria-expanded] {
            background: #1e293b;
            border-color: #334155;
        }
        .choices__list--dropdown .choices__item,
        .choices__list[aria-expanded] .choices__item {
            font-size: 0.8125rem;
            padding: 0.5rem 1rem;
            color: #475569;
        }
        .dark .choices__list--dropdown .choices__item,
        .dark .choices__list[aria-expanded] .choices__item {
            color: #cbd5e1;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted,
        .choices__list[aria-expanded] .choices__item--selectable.is-highlighted {
            background: #ecfdf5;
            color: #065f46;
        }
        .dark .choices__list--dropdown .choices__item--selectable.is-highlighted,
        .dark .choices__list[aria-expanded] .choices__item--selectable.is-highlighted {
            background: #064e3b;
            color: #6ee7b7;
        }
        .choices__input {
            background: transparent;
            color: inherit;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            margin-bottom: 0;
        }
        .choices[data-type*=select-one] .choices__input {
            border-bottom: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            margin: 0;
            width: 100%;
        }
        .dark .choices[data-type*=select-one] .choices__input,
        .dark .choices__list--dropdown .choices__input,
        .dark .choices__input,
        .dark .choices__input--cloned {
            border-bottom-color: #334155 !important;
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }
        .dark .choices__placeholder,
        .dark .choices__input::placeholder {
            color: #64748b !important;
            opacity: 0.8 !important;
        }
        .choices[data-type*=select-one]::after {
            border-color: #64748b transparent transparent;
        }
        .dark .choices[data-type*=select-one]::after {
            border-color: #94a3b8 transparent transparent;
        }
        .choices[data-type*=select-one].is-open::after {
            border-color: transparent transparent #10b981;
        }
        /* Selected item */
        .choices__list--single .choices__item {
            color: #1e293b;
        }
        .dark .choices__list--single .choices__item {
            color: #e2e8f0;
        }
    </style>
    @stack('styles')

</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased min-h-screen flex flex-col transition-colors duration-300" x-data="{ sidebarOpen: false }">

    <div class="flex flex-1 relative overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:flex lg:z-auto"
               x-cloak>
            <!-- Logo Header -->
            <div class="p-6 border-b border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-emerald-500 rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-emerald-500/20 text-lg">E</div>
                    <div>
                        <h1 class="font-bold text-white leading-tight">ELF System</h1>
                        <span class="text-xs text-slate-500">Pondok Al-Fithroh</span>
                    </div>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button type="button" @click="sidebarOpen = false" class="p-1 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg lg:hidden transition-colors" title="Tutup Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                    <span>Dasbor</span>
                </a>

                <div class="pt-4 pb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kepengurusan</div>

                <!-- Pusat Kendali Asrama & Kelas (Unified Control Center) -->
                <a href="{{ route('kepengasuhan.asrama-kelas') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('kepengasuhan.asrama-kelas*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Pusat Kendali Asrama &amp; Kelas</span>
                </a>

                <!-- Data Santri Master (Tabel Semua Santri: Mukim, Laju, Boyong) -->
                <a href="{{ route('kepengasuhan.peta-santri') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('kepengasuhan.peta-santri*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Data Santri (Master Tabel)</span>
                </a>

                <!-- Wizard Kenaikan & Kelulusan Kelas Massal -->
                <a href="{{ route('madrasah.kenaikan-kelas') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('madrasah.kenaikan-kelas*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Wizard Kenaikan Kelas</span>
                </a>

                <!-- Perizinan -->
                @can('view-perizinan')
                    <a href="{{ route('kepengasuhan.perizinan') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('kepengasuhan.perizinan') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Perizinan Santri</span>
                    </a>
                @endcan

                <!-- Pelanggaran -->
                @can('view-pelanggaran')
                    <a href="{{ route('kepengasuhan.violations') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('kepengasuhan.violations') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Buku Pelanggaran</span>
                    </a>
                @endcan

                <!-- Kegiatan -->
                @can('manage-kegiatan')
                    <a href="{{ route('kepengasuhan.activities') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('kepengasuhan.activities') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Absensi Kegiatan</span>
                    </a>
                @endcan

                {{-- Sensus Bulanan --}}
                @can('manage-sensus')
                    <a href="{{ route('kepengasuhan.wali-saudara') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('kepengasuhan.wali-saudara') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Wali &amp; Saudara</span>
                    </a>
                @endcan

                {{-- Sensus V3 (Fleksibel) --}}
                @if(auth()->user()->can('manage-sensus-v3') || auth()->user()->can('input-census-v3'))
                    <a href="{{ route('sensus.campaigns') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('sensus.campaigns*') || request()->routeIs('sensus.input*') || request()->routeIs('sensus.review*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Sensus Fleksibel</span>
                    </a>
                @endif
                @can('manage-census-template')
                    <a href="{{ route('sensus.templates') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('sensus.templates*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Template Sensus</span>
                    </a>
                @endcan

                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('bendahara-pondok') || auth()->user()->hasRole('bendahara-unit') || auth()->user()->hasRole('bendahara-putra') || auth()->user()->hasRole('bendahara-putri') || auth()->user()->hasRole('manajemen'))
                    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Modul Keuangan</div>
                    
                    <a href="{{ route('keuangan.billing') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('keuangan.billing*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pusat Kendali Keuangan & Tagihan</span>
                    </a>
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('bendahara-pondok') || auth()->user()->hasRole('manajemen') || auth()->user()->hasRole('pengasuh') || auth()->user()->can('manage-setoran-kolektif'))
                        <a href="{{ route('keuangan.lembar-setoran') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('keuangan.lembar-setoran*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Lembar Setoran Kolektif</span>
                        </a>
                    @endif
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('bendahara-pondok') || auth()->user()->hasRole('bendahara-putra') || auth()->user()->hasRole('bendahara-putri') || auth()->user()->hasRole('manajemen') || auth()->user()->hasRole('pengasuh'))
                        <a href="{{ route('keuangan.majek') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('keuangan.majek*') ? 'bg-slate-800 text-white font-medium' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>🍽️ Majek (Katering)</span>
                        </a>
                    @endif
                @endif



                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('manajemen') || auth()->user()->can('manage-roles'))
                    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keamanan & Sistem</div>
                    <a href="{{ route('system.wali-cms') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('system.wali-cms') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>CMS Portal Wali</span>
                    </a>
                    <a href="{{ route('system.cms') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('system.cms') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span>CMS Landing Page</span>
                    </a>
                    <a href="{{ route('system.santri.import') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('system.santri.import') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Pusat Setup Data Master (Excel)</span>
                    </a>
                    <a href="{{ route('system.roles-permissions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->routeIs('system.roles-permissions') ? 'bg-slate-800 text-white font-medium' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Manajemen Hak Akses</span>
                    </a>
                @endif
            </nav>

            <!-- User Session Profile -->
            <div class="p-4 border-t border-slate-800 flex items-center justify-between gap-3 bg-slate-950">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="overflow-hidden">
                        <h4 class="font-bold text-sm text-white truncate">{{ auth()->user()->name }}</h4>
                        <span class="text-xs text-slate-500 truncate block">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="p-2 text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" title="Logout">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="flex-1 flex flex-col overflow-y-auto">
            <!-- Top Header Navbar -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between px-4 sm:px-6 lg:px-8 transition-colors duration-300">
                <div class="flex items-center gap-3">
                    <!-- Hamburger Toggle Button -->
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl lg:hidden transition-colors" title="Buka Menu">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-sm font-semibold text-slate-500 dark:text-slate-400 hidden sm:block">Dasbor Developer & Administrator</h2>
                    <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 sm:hidden">ELF System</h2>
                </div>
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Shortcut Portal Wali Santri -->
                    <a href="{{ url('/portal-wali') }}" target="_blank" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-sm">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="hidden sm:inline">Portal Wali Santri</span>
                        <span class="sm:hidden">Wali</span>
                    </a>

                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" type="button" class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all" title="Ubah Tema">
                        <!-- Dark Icon -->
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <!-- Light Icon -->
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05L5.75 4.35a1 1 0 10-1.41 1.41l.71.71zM4 11a1 1 0 100-2H3a1 1 0 100 2h1zm-7.49 4.343a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium hidden md:inline">Sesi Web Aktif</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 sm:p-6 lg:p-8 flex-1">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- ====================================================== --}}
    {{-- GLOBAL TOAST NOTIFICATION SYSTEM (Alpine.js)           --}}
    {{-- ====================================================== --}}
    <div
        x-data="toastManager()"
        x-init="init()"
        class="fixed bottom-6 right-6 z-[9999] flex flex-col-reverse gap-3 pointer-events-none"
        aria-live="polite"
        aria-atomic="false"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                class="pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-2xl border backdrop-blur-sm max-w-sm w-full"
                :class="{
                    'bg-emerald-950/90 border-emerald-700/60 text-emerald-100': toast.type === 'success',
                    'bg-rose-950/90 border-rose-700/60 text-rose-100': toast.type === 'error',
                    'bg-amber-950/90 border-amber-700/60 text-amber-100': toast.type === 'warning',
                    'bg-slate-900/90 border-slate-700/60 text-slate-100': toast.type === 'info',
                }"
            >
                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5">
                    {{-- success --}}
                    <svg x-show="toast.type === 'success'" class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{-- error --}}
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{-- warning --}}
                    <svg x-show="toast.type === 'warning'" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{-- info --}}
                    <svg x-show="toast.type === 'info'" class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>

                {{-- Text --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold leading-snug" x-text="toast.title" x-show="toast.title"></p>
                    <p class="text-sm leading-snug" :class="toast.title ? 'text-opacity-80 mt-0.5' : ''" x-text="toast.message"></p>
                </div>

                {{-- Close button --}}
                <button @click="dismiss(toast.id)" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- Progress bar --}}
                <div class="absolute bottom-0 left-0 h-[3px] rounded-full opacity-40" :class="{
                    'bg-emerald-400': toast.type === 'success',
                    'bg-rose-400': toast.type === 'error',
                    'bg-amber-400': toast.type === 'warning',
                    'bg-slate-400': toast.type === 'info',
                }" :style="`width: ${toast.progress}%; transition: width ${toast.duration}ms linear`"></div>
            </div>
        </template>
    </div>

    @livewireScripts
    @stack('scripts')
    
    <script>
        // ============================================================
        // GLOBAL TOAST MANAGER (Alpine.js)
        // ============================================================
        function toastManager() {
            return {
                toasts: [],
                nextId: 0,

                init() {
                    // Listen to Livewire events
                    window.addEventListener('toast-show', (event) => {
                        this.show(event.detail[0] || event.detail);
                    });
                    // Also support native browser event (for non-Livewire)
                    window.addEventListener('app-toast', (event) => {
                        this.show(event.detail);
                    });
                },

                show(payload) {
                    if (!payload) return;

                    const message = payload.message || '';
                    const type = payload.type || 'info';
                    const title = payload.title || null;

                    // Cegah toast duplikat dalam waktu 100ms (misal akibat double dispatch/bubbling)
                    const now = Date.now();
                    if (this._lastToast &&
                        this._lastToast.message === message &&
                        this._lastToast.type === type &&
                        this._lastToast.title === title &&
                        (now - this._lastToast.time) < 100) {
                        return;
                    }
                    this._lastToast = { message, type, title, time: now };

                    const id = ++this.nextId;
                    const duration = payload.duration || 4000;
                    const toast = {
                        id,
                        type,
                        title,
                        message,
                        visible: true,
                        duration,
                        progress: 100,
                    };
                    this.toasts.unshift(toast);

                    // Animate progress bar to 0
                    this.$nextTick(() => {
                        const el = this.toasts.find(t => t.id === id);
                        if (el) el.progress = 0;
                    });

                    // Auto-dismiss
                    setTimeout(() => this.dismiss(id), duration + 200);

                    // Limit to 5 toasts
                    if (this.toasts.length > 5) {
                        this.toasts.pop();
                    }
                },

                dismiss(id) {
                    const idx = this.toasts.findIndex(t => t.id === id);
                    if (idx !== -1) {
                        this.toasts[idx].visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }
                },
            };
        }

        // ============================================================
        // THEME TOGGLE — re-init on every wire:navigate
        // ============================================================
        function initThemeToggle() {
            var darkIcon  = document.getElementById('theme-toggle-dark-icon');
            var lightIcon = document.getElementById('theme-toggle-light-icon');
            var btn       = document.getElementById('theme-toggle');
            if (!darkIcon || !lightIcon || !btn) return;

            // Sync icon with current class
            if (document.documentElement.classList.contains('dark')) {
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            } else {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            }

            // Remove any previous listener to avoid double-binding
            btn.replaceWith(btn.cloneNode(true));
            var freshBtn = document.getElementById('theme-toggle');
            var freshDark  = document.getElementById('theme-toggle-dark-icon');
            var freshLight = document.getElementById('theme-toggle-light-icon');

            freshBtn.addEventListener('click', function () {
                var isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                    freshDark.classList.remove('hidden');
                    freshLight.classList.add('hidden');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                    freshLight.classList.remove('hidden');
                    freshDark.classList.add('hidden');
                }
            });
        }

        // Init on first load and after every Livewire SPA navigation
        initThemeToggle();
        document.addEventListener('livewire:navigated', initThemeToggle);
    </script>
</body>
</html>
