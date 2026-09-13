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
            @elseif($activeTab === 'tunggakan')
                <a href="{{ $this->tunggakanDownloadUrl }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Template Tunggakan Excel</span>
                </a>
                <button type="button" 
                        wire:click="openTunggakanImportModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-amber-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <span>Impor Saldo Tunggakan</span>
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
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto">
        <button type="button" 
                wire:click="setTab('asrama')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $activeTab === 'asrama' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>1. Setup Komplek &amp; Kamar</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'asrama' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $dormCount }} Komplek / {{ $roomCount }} Kamar</span>
        </button>

        <button type="button" 
                wire:click="setTab('kelas')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $activeTab === 'kelas' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span>2. Setup Kelas Madrasah</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'kelas' ? 'bg-indigo-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $kelasCount }} Kelas</span>
        </button>

        <button type="button" 
                wire:click="setTab('santri')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $activeTab === 'santri' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>3. Setup Santri &amp; Wali</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'santri' ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $santriCount }} Santri</span>
        </button>

        <button type="button" 
                wire:click="setTab('tunggakan')" 
                class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $activeTab === 'tunggakan' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>4. Saldo Awal / Tunggakan Tagihan</span>
            <span class="px-2 py-0.5 text-xs rounded-full {{ $activeTab === 'tunggakan' ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">{{ $tunggakanCount }} Tagihan</span>
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

    <!-- TAB 4: SALDO AWAL / TUNGGAKAN TAGIHAN -->
    @if($activeTab === 'tunggakan')
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500/10 text-amber-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Tagihan Tunggakan Lampau</span>
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ number_format($tunggakanCount, 0, ',', '.') }} Tagihan</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-500/10 text-rose-600 rounded-xl flex items-center justify-center font-bold text-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Nominal Tunggakan</span>
                        <span class="text-2xl font-black text-rose-600 dark:text-rose-400">Rp {{ number_format($tunggakanTotalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Bar (Unduh Template & Upload) -->
            <div class="p-6 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-200/80 dark:border-amber-900/40 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h4 class="font-extrabold text-sm text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span>⚡ Generator &amp; Impor Tunggakan Lampau</span>
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xl">
                        Gunakan tombol di sebelah kanan untuk menyaring data santri (Asrama / Kelas / Gender), melihat simulasi tabel Excel secara langsung, lalu unduh berkas template untuk diisi nominalnya.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" wire:click="openTunggakanTemplateModal" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-600/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Template (Filter &amp; Pratinjau)</span>
                    </button>
                    <button type="button" wire:click="openTunggakanImportModal" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span>Unggah Berkas Excel</span>
                    </button>
                </div>
            </div>

            <!-- Table of Recent Tunggakan Bills -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden space-y-0">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Daftar Tagihan Tunggakan Lampau Terdaftar</h3>
                        <p class="text-xs text-slate-400">Daftar tagihan santri sebelum tahun 2026 atau berstatus tunggakan saldo awal.</p>
                    </div>

                    <!-- Filter Controls & Bulk Action Bar -->
                    <div class="flex items-center gap-2.5 flex-wrap">
                        @if(count($selectedTunggakanIds) > 0)
                            <button type="button" wire:click="openBulkDeleteTunggakan" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md shadow-rose-600/20 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus {{ count($selectedTunggakanIds) }} Terpilih</span>
                            </button>
                        @endif

                        <!-- Search -->
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="tunggakanSearch" placeholder="Cari nama / NIS..." class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500 w-40 sm:w-48">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Type -->
                        <select wire:model.live="tunggakanFilterType" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Semua Iuran</option>
                            <option value="kebersihan">Kebersihan</option>
                            <option value="syahriah_pondok">Syahriah Pondok</option>
                            <option value="syahriah_madrasah">Syahriah Madrasah</option>
                            <option value="kas_komplek">Kas Komplek</option>
                            <option value="lainnya">Lainnya</option>
                        </select>

                        <!-- Filter Year -->
                        <select wire:model.live="tunggakanFilterYear" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Semua Tahun</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-100 dark:border-slate-800">
                                <th class="px-4 py-4 w-10 text-center">
                                    <input type="checkbox" wire:model.live="selectAllTunggakan" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                </th>
                                <th class="px-6 py-4">Santri</th>
                                <th class="px-6 py-4">Tipe Iuran</th>
                                <th class="px-6 py-4">Periode</th>
                                <th class="px-6 py-4">Nominal</th>
                                <th class="px-6 py-4">Keterangan / Catatan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($recentTunggakan as $tb)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors {{ in_array((string)$tb->id, $selectedTunggakanIds) ? 'bg-amber-500/5 dark:bg-amber-500/10' : '' }}">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" wire:model.live="selectedTunggakanIds" value="{{ (string)$tb->id }}" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-800 dark:text-slate-100 block">{{ $tb->person?->name ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $tb->person?->nis ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ str_replace('_', ' ', $tb->bill_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $tb->period_formatted }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-rose-600 dark:text-rose-400">
                                        Rp {{ number_format($tb->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                        {{ $tb->notes ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300 uppercase">
                                            {{ $tb->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($tb->amount_paid == 0)
                                            <button type="button" wire:click="openDeleteSingleTunggakan('{{ $tb->id }}')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition-colors" title="Hapus Tagihan Ini">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">Sudah dicicil</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                        Tidak ada data tunggakan lampau yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $recentTunggakan->links() }}
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

    <!-- MODAL 4: IMPOR EXCEL TUNGGAKAN TAGIHAN -->
    @if($showTunggakanImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-3xl w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📥</span>
                        <h3 class="font-bold text-slate-800 dark:text-slate-100">Impor Saldo Awal / Tunggakan Tagihan (Excel)</h3>
                    </div>
                    <button type="button" wire:click="closeTunggakanImportModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Upload File Excel Tunggakan (.xlsx / .xls)</label>
                            <button type="button" wire:click="openTunggakanTemplateModal" class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-bold flex items-center gap-1">
                                <span>⬇ Filter &amp; Unduh Template Excel</span>
                            </button>
                        </div>
                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-500/10 file:text-amber-600 dark:file:text-amber-400 hover:file:bg-amber-500/20 cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl">
                        <div wire:loading wire:target="excelFile" class="text-xs text-amber-500 mt-2 font-medium">Membaca berkas Excel...</div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="processTunggakanImport" wire:loading.attr="disabled" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-amber-600/20">
                            Validasi &amp; Pratinjau
                        </button>
                    </div>

                    <!-- Validation Preview -->
                    @if(!empty($tempValidTunggakan) || !empty($tempInvalidTunggakan))
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 rounded-lg">{{ count($tempValidTunggakan) }} Valid Siap Disimpan</span>
                                <span class="px-3 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 rounded-lg">{{ count($tempInvalidTunggakan) }} Baris Error</span>
                            </div>

                            @if(!empty($tempValidTunggakan))
                                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl max-h-56 overflow-y-auto space-y-2 text-xs">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 block mb-2">Pratinjau Data Valid (Siap Terbit Tagihan):</span>
                                    <div class="space-y-1.5">
                                        @foreach($tempValidTunggakan as $vt)
                                            <div class="p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
                                                <div>
                                                    <span class="font-bold text-slate-900 dark:text-white">Baris {{ $vt['row_num'] }}: {{ $vt['santri_name'] }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">({{ $vt['nis'] }})</span>
                                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                                        🏷️ {{ ucfirst(str_replace('_', ' ', $vt['bill_type'])) }} · Periode: {{ $vt['period_year'] }} · <em>"{{ $vt['notes'] }}"</em>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-black text-rose-600 dark:text-rose-400 block">{{ $vt['formatted_amount'] }}</span>
                                                    <span class="text-[9px] font-extrabold uppercase text-amber-600 dark:text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded">Unpaid</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($tempInvalidTunggakan))
                                <div class="bg-rose-50 dark:bg-rose-950/30 p-4 rounded-xl max-h-48 overflow-y-auto space-y-2 text-xs border border-rose-200 dark:border-rose-800/50">
                                    <span class="font-bold text-rose-700 dark:text-rose-400 block">Data Berkas Error (Tidak Akan Diimpor):</span>
                                    @foreach($tempInvalidTunggakan as $it)
                                        <div class="text-rose-600 dark:text-rose-300 pb-1 border-b border-rose-200/50 dark:border-rose-800/30 last:border-0">
                                            <strong>Baris {{ $it['row_num'] }} (NIS: {{ $it['nis'] ?: 'KOSONG' }} - {{ $it['santri_name'] }}):</strong>
                                            <ul class="list-disc list-inside ml-2 mt-0.5">
                                                @foreach($it['errors'] as $err)
                                                    <li>{{ $err }}</li>
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
                    <button type="button" wire:click="closeTunggakanImportModal" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800">Batal</button>
                    @if(!empty($tempValidTunggakan))
                        <button type="button" wire:click="commitTunggakanImport" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-amber-600/20">
                            Simpan {{ count($tempValidTunggakan) }} Tagihan Tunggakan ke Database
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 5: GENERATOR TEMPLATE EXCEL TUNGGAKAN DENGAN FILTER & PRATINJAU -->
    @if($showTunggakanTemplateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-5xl w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg">
                            📑
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Filter &amp; Pratinjau Template Excel Tunggakan</h3>
                            <p class="text-xs text-slate-400">Sesuaikan kriteria santri dan jenis tagihan sebelum mengunduh berkas spreadsheet.</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeTunggakanTemplateModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (2 Columns Layout) -->
                <div class="p-6 space-y-6 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        
                        <!-- Left Column: Filter Controls -->
                        <div class="lg:col-span-5 space-y-4 bg-slate-50/60 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-200/70 dark:border-slate-800">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                                <h4 class="font-extrabold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                    <span>⚙️ Kriteria Filter Santri</span>
                                </h4>
                                <button type="button" wire:click="resetTemplateFilters" class="text-[11px] text-amber-600 dark:text-amber-400 hover:underline font-semibold">
                                    Reset Filter
                                </button>
                            </div>

                            <!-- Filter Asrama -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Komplek Asrama</label>
                                <select wire:model.live="templateDormitoryId" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                    <option value="">Semua Komplek Asrama</option>
                                    @foreach($recentDormitories as $dorm)
                                        <option value="{{ $dorm->id }}">{{ $dorm->name }} ({{ $dorm->gender === 'L' ? 'Putra' : 'Putri' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Kelas -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Kelas Madrasah</label>
                                <select wire:model.live="templateKelasId" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                    <option value="">Semua Kelas Madrasah</option>
                                    @foreach($recentKelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Gender -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                                <select wire:model.live="templateGender" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                    <option value="">Semua Gender (Putra &amp; Putri)</option>
                                    <option value="L">Putra Saja (L)</option>
                                    <option value="P">Putri Saja (P)</option>
                                </select>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 space-y-3">
                                <h4 class="font-extrabold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                                    🏷️ Konfigurasi Tagihan
                                </h4>

                                <!-- Jenis Tagihan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Jenis Tagihan / Iuran</label>
                                    <select wire:model.live="templateBillType" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                        <option value="kebersihan">🧹 Kebersihan / Kas Sampah</option>
                                        <option value="syahriah_pondok">🏠 Syahriah Pondok</option>
                                        <option value="syahriah_madrasah">📚 Syahriah Madrasah</option>
                                        <option value="kas_komplek">🏢 Kas Komplek</option>
                                        <option value="lainnya">🏷️ Lainnya / Tunggakan Bebas</option>
                                    </select>
                                </div>

                                <!-- Tahun Tagihan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Tahun Periode</label>
                                    <input type="number" wire:model.live="templateYear" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-amber-500 focus:border-amber-500 shadow-sm">
                                </div>
                            </div>

                            <!-- Toggle Auto Pre-fill -->
                            <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                <label class="flex items-start gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-amber-400 transition-colors">
                                    <input type="checkbox" wire:model.live="templatePrefill" class="mt-0.5 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                    <div class="text-xs">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block">Pre-fill Nama &amp; NIS Santri</span>
                                        <span class="text-slate-400 text-[11px]">Otomatis isi daftar santri hasil filter pada sheet Excel dengan nominal kosong.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Right Column: Live Sheet Preview Simulator -->
                        <div class="lg:col-span-7 space-y-4">
                            <!-- Preview Header & Stats -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-4 bg-amber-500/10 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/50 rounded-2xl">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 dark:text-amber-400 block">Hasil Filter Santri</span>
                                    <span class="text-lg font-black text-slate-800 dark:text-slate-100">
                                        {{ $this->filteredSantriPreview['total'] }} Santri Ditemukan
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg bg-amber-600 text-white shadow-sm">
                                        {{ ucfirst(str_replace('_', ' ', $templateBillType)) }}
                                    </span>
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        Tahun {{ $templateYear }}
                                    </span>
                                </div>
                            </div>

                            <!-- Simulated Spreadsheet Box -->
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                                <!-- Excel Header bar -->
                                <div class="bg-emerald-700 text-white px-4 py-2.5 text-xs font-bold flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-200" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                                        <span>Pratinjau Berkas: template_tunggakan_{{ $templateBillType }}_{{ $templateYear }}.xlsx</span>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-800/80 font-mono text-emerald-200">Sheet 1: Input_Tunggakan</span>
                                </div>

                                <!-- Spreadsheet Table Mock -->
                                <div class="overflow-x-auto max-h-72">
                                    <table class="w-full text-left border-collapse text-[11px]">
                                        <thead>
                                            <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-bold font-mono text-[10px]">
                                                <th class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-center w-8">#</th>
                                                <th class="px-3 py-2 border-r border-slate-200 dark:border-slate-700">A: NIS</th>
                                                <th class="px-3 py-2 border-r border-slate-200 dark:border-slate-700">B: NAMA SANTRI</th>
                                                <th class="px-3 py-2 border-r border-slate-200 dark:border-slate-700">C: JENIS TAGIHAN</th>
                                                <th class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-center">D: TAHUN</th>
                                                <th class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-right bg-amber-50/60 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300">F: NOMINAL (RP)</th>
                                                <th class="px-3 py-2">G: CATATAN</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono">
                                            @if($templatePrefill && $this->filteredSantriPreview['total'] > 0)
                                                @foreach($this->filteredSantriPreview['samples'] as $idx => $santri)
                                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                                        <td class="px-3 py-2 text-center text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 border-r border-slate-200 dark:border-slate-700">{{ $idx + 2 }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold">{{ $santri->nis ?: '-' }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 font-sans font-medium">{{ $santri->name }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $templateBillType }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-center text-slate-600 dark:text-slate-400">{{ $templateYear }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-right text-amber-600 dark:text-amber-400 bg-amber-50/30 dark:bg-amber-950/10 italic">
                                                            [Isi Nominal]
                                                        </td>
                                                        <td class="px-3 py-2 text-slate-400 text-[10px] truncate max-w-[140px]">Tunggakan {{ $templateYear }}</td>
                                                    </tr>
                                                @endforeach

                                                @if($this->filteredSantriPreview['total'] > 6)
                                                    <tr class="bg-amber-500/5 text-amber-700 dark:text-amber-400 font-sans italic text-center">
                                                        <td colspan="7" class="py-2.5 px-4 text-xs font-semibold">
                                                            ⬇ ... dan +{{ $this->filteredSantriPreview['total'] - 6 }} santri lainnya otomatis disertakan di baris berikutnya pada file Excel.
                                                        </td>
                                                    </tr>
                                                @endif
                                            @elseif($templatePrefill && $this->filteredSantriPreview['total'] === 0)
                                                <tr>
                                                    <td colspan="7" class="py-8 text-center text-slate-400 font-sans">
                                                        Tidak ada santri yang sesuai kriteria filter saat ini.
                                                    </td>
                                                </tr>
                                            @else
                                                {{-- Format Kosong Manual --}}
                                                @for($i = 2; $i <= 5; $i++)
                                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                                        <td class="px-3 py-2 text-center text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 border-r border-slate-200 dark:border-slate-700">{{ $i }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-slate-400 italic font-sans">[Isi NIS Santri]</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-slate-400 italic font-sans">[Nama Santri]</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ $templateBillType }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-center text-slate-600 dark:text-slate-400">{{ $templateYear }}</td>
                                                        <td class="px-3 py-2 border-r border-slate-200 dark:border-slate-700 text-right text-slate-400 italic">[Nominal]</td>
                                                        <td class="px-3 py-2 text-slate-400 text-[10px] italic">[Keterangan]</td>
                                                    </tr>
                                                @endfor
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tips Box -->
                            <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/50 text-xs text-blue-700 dark:text-blue-300 flex items-start gap-2.5">
                                <span class="text-base leading-none">💡</span>
                                <div class="space-y-0.5">
                                    <span class="font-bold block">Tips Praktis Pengurus:</span>
                                    <p class="text-[11px] text-blue-600/90 dark:text-blue-400">
                                        Santri yang <strong>tidak memiliki tunggakan / sudah lunas</strong> cukup <strong>dikosongkan nominalnya</strong> di Excel. Sistem akan otomatis melewatinya saat file diunggah.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="text-xs text-slate-400">
                        File dilengkapi Sheet 2 (Daftar Referensi NIS Santri Seluruh Pesantren).
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="closeTunggakanTemplateModal" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200">
                            Tutup
                        </button>
                        <a href="{{ $this->tunggakanDownloadUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-600/25 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Berkas Excel (.xlsx)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 6: KONFIRMASI HAPUS TUNGGAKAN -->
    @if($showDeleteTunggakanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-rose-500/10 dark:bg-rose-950/30">
                    <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                        <span class="text-xl">⚠️</span>
                        <h3 class="font-bold text-sm">Konfirmasi Hapus Tagihan Tunggakan</h3>
                    </div>
                    <button type="button" wire:click="closeDeleteTunggakanModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-3 text-xs text-slate-600 dark:text-slate-300">
                    @if($isBulkDeleteTunggakan)
                        <p>
                            Apakah Anda yakin ingin menghapus <strong class="text-rose-600 font-bold">{{ count($selectedTunggakanIds) }} tagihan tunggakan terpilih</strong>?
                        </p>
                    @else
                        <p>
                            Apakah Anda yakin ingin menghapus tagihan tunggakan milik:
                        </p>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200/60 dark:border-slate-700/60 font-semibold space-y-1">
                            <div>Santri: <span class="text-slate-900 dark:text-white font-bold">{{ $deletingTunggakanName }}</span></div>
                            <div>Nominal: <span class="text-rose-600 font-bold">{{ $deletingTunggakanNominal }}</span></div>
                        </div>
                    @endif
                    <p class="text-slate-500 dark:text-slate-400 text-[11px] italic">
                        * Data yang dihapus akan dibatalkan dari sistem. Tagihan yang sudah pernah dicicil di kasir tidak dapat dihapus.
                    </p>
                </div>

                <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/50">
                    <button type="button" wire:click="closeDeleteTunggakanModal" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-800">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmDeleteTunggakan" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-rose-600/20">
                        Ya, Hapus Tagihan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
