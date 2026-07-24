<x-app-layout>
    <div class="space-y-8">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-3xl p-8 border border-slate-700 shadow-xl relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 uppercase tracking-wider">Informasi Sesi Aktif</span>
                    <h1 class="text-3xl font-extrabold tracking-tight mt-3 text-white">Ahlan wa Sahlan, {{ $user->name }}!</h1>
                    <p class="text-slate-400 text-sm mt-1 leading-relaxed">
                        Anda masuk sebagai bagian dari pengasuh pondok. Silakan gunakan dasbor ini untuk menguji dan meninjau otorisasi akun Anda.
                    </p>
                </div>
                <div class="flex-shrink-0 flex gap-3">
                    <span class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-2xl text-xs font-semibold text-slate-300">
                        {{ $user->email }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Info Panel (Otoritas & Hak Akses) -->
            <div class="lg:col-span-5 space-y-8">
                <x-card title="Otoritas & Hak Akses" subtitle="Role dan permission aktif Anda saat ini">
                    <div class="space-y-6">
                        <!-- Roles -->
                        <div>
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Role Aktif</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($roles as $role)
                                    <x-badge type="info">{{ $role }}</x-badge>
                                @empty
                                    <span class="text-xs text-slate-400">Tidak ada role terdaftar.</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div>
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Permissions Terkunci</h4>
                            <div class="flex flex-wrap gap-1.5 max-h-60 overflow-y-auto pr-1">
                                @forelse ($permissions as $permission)
                                    <x-badge type="default">{{ $permission }}</x-badge>
                                @empty
                                    <span class="text-xs text-slate-400">Tidak ada permission khusus.</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Organizations -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Scope Organisasi / Asrama</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($organizationIds as $orgId)
                                    <span class="inline-flex items-center text-xs font-medium text-slate-600 bg-slate-100 dark:text-slate-300 dark:bg-slate-800 px-2.5 py-1 rounded-md">
                                        ID: {{ substr($orgId, 0, 8) }}...
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400">Wewenang Global (Semua Organisasi)</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Shortcuts Panel (Modul Tersedia) -->
            <div class="lg:col-span-7 space-y-8">
                <x-card title="Modul Kepengurusan" subtitle="Pintasan cepat berdasarkan otorisasi Anda">
                    <div class="grid sm:grid-cols-2 gap-4">
                        
                        <!-- Dormitories Shortcut -->
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('pengasuh') || auth()->user()->can('manage-asrama'))
                            <a href="{{ route('kepengasuhan.dormitories') }}" class="group p-5 border border-slate-100 dark:border-slate-800 hover:border-emerald-100 dark:hover:border-emerald-900/40 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 group-hover:bg-emerald-100/80 dark:group-hover:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">Asrama & Kamar</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kelola peta kamar, kapasitas, dan penempatan kamar santri secara berkala.</p>
                            </a>
                        @endif

                        <!-- Perizinan Shortcut -->
                        @can('view-perizinan')
                            <a href="{{ route('kepengasuhan.perizinan') }}" class="group p-5 border border-slate-100 dark:border-slate-800 hover:border-blue-100 dark:hover:border-blue-900/40 hover:bg-blue-50/20 dark:hover:bg-blue-950/20 rounded-2xl transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 group-hover:bg-blue-100/80 dark:group-hover:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-700 dark:group-hover:text-blue-400 transition-colors">Perizinan Santri</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Catat izin santri keluar pondok, checkout/checkin, dan riwayat persetujuan izin.</p>
                            </a>
                        @endcan

                        <!-- Pelanggaran Shortcut -->
                        @can('view-pelanggaran')
                            <a href="{{ route('kepengasuhan.violations') }}" class="group p-5 border border-slate-100 dark:border-slate-800 hover:border-rose-100 dark:hover:border-rose-900/40 hover:bg-rose-50/20 dark:hover:bg-rose-950/20 rounded-2xl transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 group-hover:bg-rose-100/80 dark:group-hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-rose-700 dark:group-hover:text-rose-400 transition-colors">Buku Pelanggaran</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Pencatatan jenis pelanggaran, akumulasi poin tata tertib, dan sanksi santri.</p>
                            </a>
                        @endcan

                        <!-- Kegiatan Shortcut -->
                        @can('manage-kegiatan')
                            <a href="{{ route('kepengasuhan.activities') }}" class="group p-5 border border-slate-100 dark:border-slate-800 hover:border-amber-100 dark:hover:border-amber-900/40 hover:bg-amber-50/20 dark:hover:bg-amber-950/20 rounded-2xl transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 group-hover:bg-amber-100/80 dark:group-hover:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">Absensi Kegiatan</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Lembar presensi bulk santri per kegiatan asrama/kajian secara real-time.</p>
                            </a>
                        @endcan

                        <!-- CMS Portal Wali Shortcut -->
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('manajemen') || auth()->user()->can('manage-roles'))
                            <a href="{{ route('system.wali-cms') }}" class="group p-5 border border-slate-100 dark:border-slate-800 hover:border-emerald-100 dark:hover:border-emerald-900/40 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 group-hover:bg-emerald-100/80 dark:group-hover:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">CMS Portal Wali</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Pengaturan nomor rekening bank & WA Bendahara (Unit Putra & Putri).</p>
                            </a>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
