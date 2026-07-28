<div class="space-y-6">
    <!-- Main Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg">Pusat Setup System</span>
                <span class="text-xs text-slate-400">• Super Admin &amp; Manajemen</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">Pusat Setup Data Master (Excel)</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Siapkan struktur Asrama &amp; Kamar, Kelas Madrasah, serta Impor data Santri &amp; Wali secara masal menggunakan template Excel.</p>
        </div>

        <!-- Dynamic Action Buttons depending on Active Tab -->
        <div class="flex items-center gap-3">
            @if($activeTab === 'asrama')
                <a href="{{ route('system.asrama.download-template') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Template Asrama Excel</span>
                </a>
                <button type="button" 
                        wire:click="openAsramaImportModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <span>Impor Asrama &amp; Kamar</span>
                </button>
            @elseif($activeTab === 'kelas')
                <a href="{{ route('system.kelas.download-template') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Template Kelas Excel</span>
                </a>
                <button type="button" 
                        wire:click="openKelasImportModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <span>Impor Kelas Madrasah</span>
                </button>
            @else
                <a href="{{ route('system.santri.download-template') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Template Santri Excel</span>
                </a>
                <button type="button" 
                        wire:click="openImportModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <span>Impor Santri &amp; Wali</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs Bar -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
        <button type="button" 
                wire:click="setTab('asrama')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all {{ $activeTab === 'asrama' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>1. Setup Komplek &amp; Kamar</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'asrama' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $dormCount }} Komplek / {{ $roomCount }} Kamar</span>
        </button>

        <button type="button" 
                wire:click="setTab('kelas')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all {{ $activeTab === 'kelas' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span>2. Setup Kelas Madrasah</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'kelas' ? 'bg-indigo-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $kelasCount }} Kelas</span>
        </button>

        <button type="button" 
                wire:click="setTab('santri')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all {{ $activeTab === 'santri' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>3. Setup Santri &amp; Wali</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'santri' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $santriCount }} Santri</span>
        </button>
    </div>

    <!-- TAB 1: ASRAMA & KAMAR -->
    @if($activeTab === 'asrama')
        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Komplek Asrama</span>
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ number_format($dormCount, 0, ',', '.') }} Komplek</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Unit Kamar Terdaftar</span>
                        <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($roomCount, 0, ',', '.') }} Kamar</span>
                    </div>
                </div>
            </div>

            <!-- List Asrama & Kamar -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-100">Daftar Komplek &amp; Jumlah Kamar Aktif</h3>
                        <p class="text-xs text-slate-400">Pastikan nama komplek sudah terdaftar sebelum melakukan impor santri mukim.</p>
                    </div>
                    <a href="{{ route('kepengasuhan.asrama-kelas') }}" class="text-xs font-bold text-emerald-600 hover:underline">Kelola di Pusat Kendali &rarr;</a>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentDormitories as $dorm)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm {{ $dorm->gender === 'L' ? 'bg-blue-500/10 text-blue-600' : 'bg-pink-500/10 text-pink-600' }}">
                                    {{ $dorm->gender === 'L' ? 'L' : 'P' }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $dorm->name }}</h4>
                                    <span class="text-xs text-slate-400">Gender: {{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg">
                                    {{ $dorm->rooms_count }} Kamar Terdaftar
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            Belum ada Komplek Asrama. Silakan unduh template &amp; impor Excel Asrama.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: KELAS MADRASAH -->
    @if($activeTab === 'kelas')
        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-500/10 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Kelas Madrasah</span>
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ number_format($kelasCount, 0, ',', '.') }} Kelas</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Status Tingkatan Madrasah</span>
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">Aktif &amp; Siap Diisi</span>
                    </div>
                </div>
            </div>

            <!-- List Kelas -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-100">Daftar Kelas Madrasah Terdaftar</h3>
                        <p class="text-xs text-slate-400">Daftar nama kelas yang siap dikaitkan dengan data santri.</p>
                    </div>
                    <a href="{{ route('kepengasuhan.asrama-kelas') }}" class="text-xs font-bold text-indigo-600 hover:underline">Kelola di Pusat Kendali &rarr;</a>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentKelas as $k)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                    {{ substr($k->level ?? 'K', 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $k->name }}</h4>
                                    <span class="text-xs text-slate-400">Tingkat: {{ $k->level ?? '-' }} | Gender: {{ $k->gender ?? 'Campur' }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="px-3 py-1 text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg">
                                    Kapasitas: {{ $k->capacity ?? 40 }} Santri
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            Belum ada Kelas Madrasah. Silakan unduh template &amp; impor Excel Kelas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: SANTRI & WALI -->
    @if($activeTab === 'santri')
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Santri Terdaftar</span>
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ number_format($santriCount, 0, ',', '.') }} Santri</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Santri Mukim (Berkamar)</span>
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($mukimCount, 0, ',', '.') }} Mukim</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-500/10 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Santri Laju (Non-Mukim)</span>
                        <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($lajuCount, 0, ',', '.') }} Laju</span>
                    </div>
                </div>
            </div>

            <!-- Recent Registered Santri Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Daftar Terakhir Santri Terdaftar</h3>
                    <span class="text-xs text-slate-400 font-mono">Menampilkan 15 Santri Terbaru</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3.5 font-semibold">Nama Santri</th>
                                <th class="px-6 py-3.5 font-semibold">NIS</th>
                                <th class="px-6 py-3.5 font-semibold">Gender</th>
                                <th class="px-6 py-3.5 font-semibold">Status</th>
                                <th class="px-6 py-3.5 font-semibold">Orang Tua / No. WA</th>
                                <th class="px-6 py-3.5 font-semibold">Terdaftar Pada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($recentSantri as $s)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-100">
                                        {{ $s->name }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-slate-600 dark:text-slate-400">
                                        {{ $s->santriProfile?->additional_info['nis'] ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($s->gender === 'L')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-blue-500/10 text-blue-600 rounded-lg">L (Putra)</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-pink-500/10 text-pink-600 rounded-lg">P (Putri)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $pStatus = $s->activeRoles->first()?->presence_status ?? 'mukim';
                                        @endphp
                                        @if($pStatus === 'mukim')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 rounded-lg">Mukim</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-indigo-500/10 text-indigo-600 rounded-lg">Laju</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-800 dark:text-slate-200">
                                            {{ $s->santriProfile?->father_name ?? $s->santriProfile?->mother_name ?? 'Wali Santri' }}
                                        </div>
                                        <span class="text-xs text-slate-400 font-mono">{{ $s->phone ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        {{ $s->created_at ? $s->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        Belum ada santri terdaftar. Silakan unggah berkas Excel.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $recentSantri->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 1: IMPOR EXCEL ASRAMA & KAMAR -->
    @if($showAsramaImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Impor Massal Asrama &amp; Kamar</h3>
                    <button type="button" wire:click="closeAsramaImportModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Upload File Excel Asrama (.xlsx / .xls)</label>
                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-600 dark:file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div wire:loading wire:target="excelFile" class="text-xs text-emerald-500 mt-2 font-medium">Membaca berkas Excel...</div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="processAsramaImport" wire:loading.attr="disabled" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-emerald-600/20">
                            Validasi &amp; Pratinjau
                        </button>
                    </div>

                    <!-- Validation Preview -->
                    @if(!empty($tempValidAsrama) || !empty($tempInvalidAsrama))
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 rounded-lg">{{ count($tempValidAsrama) }} Valid</span>
                                <span class="px-3 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 rounded-lg">{{ count($tempInvalidAsrama) }} Error</span>
                            </div>

                            @if(!empty($tempValidAsrama))
                                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl max-h-40 overflow-y-auto space-y-1 text-xs">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 block mb-2">Pratinjau Data Valid (Siap Disimpan):</span>
                                    @foreach($tempValidAsrama as $va)
                                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-300 py-1 border-b border-slate-200/50 dark:border-slate-700/50 last:border-0">
                                            <span>Baris {{ $va['row'] }}: <strong>{{ $va['dorm_name'] }}</strong> - {{ $va['room_name'] }}</span>
                                            <span class="font-mono text-emerald-600 dark:text-emerald-400">Gender: {{ $va['gender'] }} | Kapasitas: {{ $va['capacity'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($tempInvalidAsrama))
                                <div class="bg-rose-50 dark:bg-rose-950/30 p-4 rounded-xl max-h-40 overflow-y-auto space-y-2 text-xs border border-rose-200 dark:border-rose-800/50">
                                    <span class="font-bold text-rose-700 dark:text-rose-400 block">Data Berkas Error (Tidak Akan Diimpor):</span>
                                    @foreach($tempInvalidAsrama as $ia)
                                        <div class="text-rose-600 dark:text-rose-300">
                                            <strong>Baris {{ $ia['row'] }} ({{ $ia['name'] }}):</strong>
                                            <ul class="list-disc list-inside ml-2">
                                                @foreach($ia['reasons'] as $r)
                                                    <li>{{ $r }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/50">
                    <button type="button" wire:click="closeAsramaImportModal" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800">Batal</button>
                    @if(!empty($tempValidAsrama))
                        <button type="button" wire:click="confirmAndSaveAsramaImport" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-emerald-600/20">
                            Simpan {{ count($tempValidAsrama) }} Data Asrama
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: IMPOR EXCEL KELAS MADRASAH -->
    @if($showKelasImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Impor Massal Kelas Madrasah</h3>
                    <button type="button" wire:click="closeKelasImportModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Upload File Excel Kelas (.xlsx / .xls)</label>
                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/10 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div wire:loading wire:target="excelFile" class="text-xs text-indigo-500 mt-2 font-medium">Membaca berkas Excel...</div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="processKelasImport" wire:loading.attr="disabled" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-indigo-600/20">
                            Validasi &amp; Pratinjau
                        </button>
                    </div>

                    <!-- Validation Preview -->
                    @if(!empty($tempValidKelas) || !empty($tempInvalidKelas))
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 text-xs font-bold bg-indigo-500/10 text-indigo-600 rounded-lg">{{ count($tempValidKelas) }} Valid</span>
                                <span class="px-3 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 rounded-lg">{{ count($tempInvalidKelas) }} Error</span>
                            </div>

                            @if(!empty($tempValidKelas))
                                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl max-h-40 overflow-y-auto space-y-1 text-xs">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 block mb-2">Pratinjau Data Valid (Siap Disimpan):</span>
                                    @foreach($tempValidKelas as $vk)
                                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-300 py-1 border-b border-slate-200/50 dark:border-slate-700/50 last:border-0">
                                            <span>Baris {{ $vk['row'] }}: <strong>{{ $vk['name'] }}</strong></span>
                                            <span class="font-mono text-indigo-600 dark:text-indigo-400">Tingkat: {{ $vk['level'] }} | Gender: {{ $vk['gender'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($tempInvalidKelas))
                                <div class="bg-rose-50 dark:bg-rose-950/30 p-4 rounded-xl max-h-40 overflow-y-auto space-y-2 text-xs border border-rose-200 dark:border-rose-800/50">
                                    <span class="font-bold text-rose-700 dark:text-rose-400 block">Data Berkas Error:</span>
                                    @foreach($tempInvalidKelas as $ik)
                                        <div class="text-rose-600 dark:text-rose-300">
                                            <strong>Baris {{ $ik['row'] }} ({{ $ik['name'] }}):</strong>
                                            <ul class="list-disc list-inside ml-2">
                                                @foreach($ik['reasons'] as $r)
                                                    <li>{{ $r }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/50">
                    <button type="button" wire:click="closeKelasImportModal" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800">Batal</button>
                    @if(!empty($tempValidKelas))
                        <button type="button" wire:click="confirmAndSaveKelasImport" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-indigo-600/20">
                            Simpan {{ count($tempValidKelas) }} Data Kelas
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: IMPOR EXCEL SANTRI & WALI -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-3xl w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100">Impor Massal Santri &amp; Wali</h3>
                    <button type="button" wire:click="closeImportModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Upload File Excel Santri (.xlsx / .xls)</label>
                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-600 dark:file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div wire:loading wire:target="excelFile" class="text-xs text-emerald-500 mt-2 font-medium">Membaca berkas Excel...</div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="processImport" wire:loading.attr="disabled" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-emerald-600/20">
                            Validasi &amp; Pratinjau
                        </button>
                    </div>

                    <!-- Validation Preview -->
                    @if(!empty($tempValidSantri) || !empty($tempInvalidSantri))
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 rounded-lg">{{ count($tempValidSantri) }} Valid</span>
                                <span class="px-3 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 rounded-lg">{{ count($tempInvalidSantri) }} Error</span>
                            </div>

                            @if(!empty($tempValidSantri))
                                <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 p-4 rounded-xl max-h-64 overflow-y-auto space-y-2 text-xs">
                                    <span class="font-bold text-emerald-800 dark:text-emerald-300 block mb-2">✅ Pratinjau Data Valid ({{ count($tempValidSantri) }} santri siap disimpan):</span>
                                    @foreach($tempValidSantri as $vs)
                                        @php $hasWarning = !empty($vs['warnings']); @endphp
                                        <div class="p-2.5 rounded-lg bg-white dark:bg-slate-800 border shadow-sm {{ $hasWarning ? 'border-amber-300 dark:border-amber-600/50' : 'border-emerald-200 dark:border-emerald-700/50' }}">
                                            {{-- Baris utama: nama + badge status --}}
                                            <div class="flex items-start justify-between gap-2">
                                                <span class="text-slate-900 dark:text-slate-100 font-semibold">
                                                    Baris {{ $vs['row'] }}: {{ $vs['name'] }}
                                                    <span class="font-normal text-slate-600 dark:text-slate-300">({{ $vs['gender'] === 'L' ? 'Putra' : 'Putri' }})</span>
                                                </span>
                                                <div class="flex items-center gap-1 shrink-0">
                                                    @if($vs['presence_status'] === 'laju')
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">LAJU</span>
                                                    @else
                                                        <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                                            {{ $vs['dorm_name'] }} / {{ $vs['room_name'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Info tambahan --}}
                                            <div class="mt-1.5 space-y-0.5">
                                                {{-- Wali --}}
                                                @if(!empty($vs['parent_name']))
                                                    <div class="text-slate-700 dark:text-slate-200">
                                                        👤 <span class="text-slate-500 dark:text-slate-400">Wali:</span>
                                                        <span class="font-semibold">{{ $vs['parent_name'] }}</span>
                                                        <span class="text-slate-500 dark:text-slate-400">({{ $vs['parent_rel'] ?: '-' }})</span>
                                                        · <span class="font-mono">{{ $vs['parent_phone'] ?: '-' }}</span>
                                                    </div>
                                                @else
                                                    <div class="text-amber-700 dark:text-amber-400 flex items-center gap-1">
                                                        ⚠️ <span>Tanpa data wali — tidak akan ada relasi wali &amp; kakak-adik untuk santri ini.</span>
                                                    </div>
                                                @endif
                                                {{-- Kelas --}}
                                                @if($vs['kelas_name'])
                                                    <div class="text-slate-700 dark:text-slate-200">
                                                        🏫 <span class="text-slate-500 dark:text-slate-400">Kelas:</span>
                                                        <span class="font-semibold">{{ $vs['kelas_name'] }}</span>
                                                    </div>
                                                @endif
                                                {{-- Sekolah Formal --}}
                                                @if($vs['school_name'])
                                                    <div class="text-slate-700 dark:text-slate-200">
                                                        🎓 <span class="text-slate-500 dark:text-slate-400">Sekolah Formal:</span>
                                                        <span class="font-semibold">{{ $vs['school_name'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif




                            @if(!empty($tempInvalidSantri))
                                <div class="bg-rose-50 dark:bg-rose-950/30 p-4 rounded-xl max-h-48 overflow-y-auto space-y-2 text-xs border border-rose-200 dark:border-rose-800/50">
                                    <span class="font-bold text-rose-700 dark:text-rose-400 block">Data Berkas Error:</span>
                                    @foreach($tempInvalidSantri as $is)
                                        <div class="text-rose-600 dark:text-rose-300">
                                            <strong>Baris {{ $is['row'] }} ({{ $is['name'] }}):</strong>
                                            <ul class="list-disc list-inside ml-2">
                                                @foreach($is['reasons'] as $r)
                                                    <li>{{ $r }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/50">
                    <button type="button" wire:click="closeImportModal" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800">Batal</button>
                    @if(!empty($tempValidSantri))
                        <button type="button" wire:click="confirmAndSaveImport" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-emerald-600/20">
                            Simpan {{ count($tempValidSantri) }} Data Santri
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
