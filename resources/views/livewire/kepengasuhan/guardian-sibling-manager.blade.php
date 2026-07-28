<div class="space-y-6">
    {{-- ============================================================ --}}
    {{-- Header Page                                                  --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                Wali &amp; Hubungan Keluarga
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola data wali santri secara terpusat, integrasi saudara kandung, serta penentuan kelayakan diskon / potongan pembayaran.
            </p>
        </div>
    </div>

    {{-- Alerts --}}
    {{-- ============================================================ --}}
    {{-- Tabs Switch                                                  --}}
    {{-- ============================================================ --}}
    <div class="flex border-b border-slate-200 dark:border-slate-800">
        <button type="button" wire:click="$set('activeTab', 'guardians')"
            class="inline-flex items-center gap-2 px-6 py-3 font-bold text-sm border-b-2 transition-all {{ $activeTab === 'guardians' ? 'border-violet-600 text-violet-600 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Wali Santri</span>
        </button>
        <button type="button" wire:click="$set('activeTab', 'siblings')"
            class="inline-flex items-center gap-2 px-6 py-3 font-bold text-sm border-b-2 transition-all {{ $activeTab === 'siblings' ? 'border-violet-600 text-violet-600 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span>Hubungan Saudara</span>
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB 1: Wali Santri                                           --}}
    {{-- ============================================================ --}}
    @if($activeTab === 'guardians')
        <div class="space-y-4">
            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-4 rounded-2xl shadow-sm">
                <div class="relative flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Cari nama, kota, atau nomor HP wali..."
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                    <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                @canany(['create-person', 'update-person', 'manage-sensus'])
                <button type="button" wire:click="openCreateModal"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-violet-500/20 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Wali
                </button>
                @endcanany
            </div>

            {{-- Guardians List Table --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Wali</th>
                                <th class="px-6 py-4">Kontak HP</th>
                                <th class="px-6 py-4">Pekerjaan</th>
                                <th class="px-6 py-4">Asal Kota</th>
                                <th class="px-6 py-4 text-center">Jumlah Santri</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 text-sm">
                            @forelse($guardians as $g)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $g->name }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $g->education_level ?? 'Pendidikan -' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $g->phone_primary }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $g->occupation ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $g->city ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center font-bold">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs {{ $g->santri_count > 0 ? 'bg-violet-100 dark:bg-violet-950/40 text-violet-700 dark:text-violet-300' : 'bg-slate-100 text-slate-400' }}">
                                            {{ $g->santri_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1.5">
                                            <button type="button" wire:click="openDetail('{{ $g->id }}')"
                                                class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-violet-600 transition-colors" title="Detail Hubungan">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                            @canany(['update-person', 'manage-sensus', 'create-person'])
                                            <button type="button" wire:click="openEditModal('{{ $g->id }}')"
                                                class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 transition-colors" title="Edit Wali">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button type="button" wire:click="openMergeModal('{{ $g->id }}')"
                                                class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-amber-600 transition-colors" title="Gabungkan Wali Duplikat">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                            @endcanany
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-slate-400 italic">
                                        Tidak ada data wali yang cocok dengan pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($guardians->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $guardians->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- TAB 2: Hubungan Saudara                                      --}}
    {{-- ============================================================ --}}
    @if($activeTab === 'siblings')
        <div class="space-y-6">
            {{-- Toolbar Deteksi Otomatis --}}
            <div class="bg-gradient-to-br from-violet-600 to-purple-700 p-6 rounded-3xl text-white shadow-lg shadow-violet-500/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-lg">Deteksi Otomatis Saudara Kandung</h3>
                        <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                            Sistem memindai seluruh data santri dan mencocokkan secara otomatis berdasarkan kesamaan nama Ayah &amp; Ibu kandung, atau nomor kontak wali yang didaftarkan.
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="requestRunAutoDetection"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-violet-700 font-bold rounded-xl text-xs hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Pindai &amp; Deteksi Saudara</span>
                </button>
            </div>

            {{-- Unconfirmed Sibling Requests --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Deteksi Saudara (Belum Dikonfirmasi)</span>
                    </h2>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">{{ count($unconfirmedSiblings) }} Perlu Konfirmasi</span>
                </div>
                
                @if(count($unconfirmedSiblings) === 0)
                    <div class="text-center py-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl text-slate-400 italic text-sm">
                        Tidak ada usulan deteksi saudara baru yang tertunda.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($unconfirmedSiblings as $relation)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-950/40 flex items-center justify-center text-violet-700 dark:text-violet-300 font-bold text-xs">
                                                S1
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 font-semibold">Santri 1</p>
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $relation->person->name }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/40 flex items-center justify-center text-purple-700 dark:text-purple-300 font-bold text-xs">
                                                S2
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 font-semibold">Santri 2</p>
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $relation->sibling->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400">
                                        Auto Detected
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $relation->notes }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <select wire:model.defer="siblingRelationship" class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs focus:outline-none flex-1">
                                        <option value="saudara">Saudara Kandung</option>
                                        <option value="kakak">S2 adalah Kakak S1</option>
                                        <option value="adik">S2 adalah Adik S1</option>
                                        <option value="kembar">Kembar</option>
                                    </select>
                                    <button type="button" wire:click="confirmSibling('{{ $relation->id }}')"
                                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition-colors shadow-sm">
                                        Konfirmasi
                                    </button>
                                    <button type="button" wire:click="rejectSibling('{{ $relation->id }}')"
                                        class="px-3 py-1.5 border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 font-bold rounded-lg text-xs transition-colors">
                                        Tolak
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sibling Discount & Status Management Section --}}
            <div class="space-y-4 pt-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Daftar Status Saudara &amp; Kualifikasi Diskon</span>
                    </h2>

                    {{-- Scope Badge --}}
                    @if($userGender)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-violet-100 dark:bg-violet-950/40 text-violet-700 dark:text-violet-300">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Scope Otomatis: {{ $userGender === 'L' ? 'Putra (L)' : 'Putri (P)' }}</span>
                        </span>
                    @endif
                </div>

                {{-- Filter Panel --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-4 rounded-2xl shadow-sm space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        {{-- Search Nama --}}
                        <div class="relative sm:col-span-2">
                            <input type="text" wire:model.live="siblingSearch" placeholder="Cari nama santri..."
                                class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        {{-- Gender Filter (Only for Super Admin / Manajemen) --}}
                        @if(!$userGender)
                            <div>
                                <select wire:model.live="siblingFilterGender" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none">
                                    <option value="">Semua Gender</option>
                                    <option value="L">Putra (L)</option>
                                    <option value="P">Putri (P)</option>
                                </select>
                            </div>
                        @endif

                        {{-- Filter Asrama --}}
                        <div>
                            <select wire:model.live="siblingFilterDormitoryId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none">
                                <option value="">Semua Asrama</option>
                                @foreach($dormitories as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Kelas --}}
                        <div>
                            <select wire:model.live="siblingFilterKelasId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Status Mukim/Laju --}}
                        <div>
                            <select wire:model.live="siblingFilterPresenceStatus" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none">
                                <option value="">Semua Status Kehadiran</option>
                                <option value="mukim">Mukim</option>
                                <option value="laju">Laju / Non-Mukim</option>
                            </select>
                        </div>

                        {{-- Filter Status Bersaudara --}}
                        <div>
                            <select wire:model.live="siblingStatusFilter" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs focus:outline-none">
                                <option value="all">Semua Status Saudara</option>
                                <option value="has_sibling">Bersaudara (Aktif)</option>
                                <option value="no_sibling">Santri Tunggal</option>
                            </select>
                        </div>
                    </div>

                    {{-- Reset Filter Button --}}
                    @if($siblingSearch || $siblingStatusFilter !== 'all' || $siblingFilterGender || $siblingFilterDormitoryId || $siblingFilterKelasId || $siblingFilterPresenceStatus)
                        <div class="flex justify-end pt-1">
                            <button type="button" wire:click="resetSiblingFilters" class="inline-flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Reset Semua Filter</span>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Bulk Action Bar (When selected) --}}
                @if(count($selectedSantriIds) > 0)
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-950 p-4 rounded-2xl text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-3 border border-slate-700">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-violet-500 text-white font-bold text-xs">
                                {{ count($selectedSantriIds) }}
                            </span>
                            <span class="text-xs font-semibold text-slate-200">Santri Terpilih untuk Perubahan Status Massal</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" wire:click="requestBulkSetSibling(true)"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Set: Bersaudara (Aktif Diskon)</span>
                            </button>
                            <button type="button" wire:click="requestBulkSetSibling(false)"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Set: Santri Tunggal (Non-Diskon)</span>
                            </button>
                            <button type="button" wire:click="$set('selectedSantriIds', [])"
                                class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-xl text-xs transition-colors">
                                Batal Pilih
                            </button>
                        </div>
                    </div>
                @endif
                
                {{-- Data Table --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <th class="px-4 py-4 text-center w-10">
                                        @php
                                            $currentPageIds = $siblingSantriList->pluck('id')->toArray();
                                            $allSelectedOnPage = count($currentPageIds) > 0 && count(array_intersect($currentPageIds, $selectedSantriIds)) === count($currentPageIds);
                                        @endphp
                                        @if($allSelectedOnPage)
                                            <input type="checkbox" wire:click="deselectAllOnPage({{ json_encode($currentPageIds) }})" checked
                                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                        @else
                                            <input type="checkbox" wire:click="selectAllOnPage({{ json_encode($currentPageIds) }})"
                                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                        @endif
                                    </th>
                                    <th class="px-6 py-4">Nama Santri</th>
                                    <th class="px-6 py-4">Asrama / Kelas</th>
                                    <th class="px-6 py-4 text-center">Status Kehadiran</th>
                                    <th class="px-6 py-4 text-center">Status Bersaudara</th>
                                    <th class="px-6 py-4 text-right">Tindakan Toggle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 text-sm">
                                @forelse($siblingSantriList as $santri)
                                    @php
                                        $hasSib = $santri->santriProfile?->has_active_sibling ?? false;
                                        $isSelected = in_array($santri->id, $selectedSantriIds);
                                        $presence = $santri->activeRoles->first()?->presence_status ?? 'mukim';
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors {{ $isSelected ? 'bg-violet-50/50 dark:bg-violet-950/20' : '' }}">
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" wire:click="toggleSantriSelection('{{ $santri->id }}')" {{ $isSelected ? 'checked' : '' }}
                                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                                <span>{{ $santri->name }}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold {{ $santri->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/40 dark:text-pink-300' }}">
                                                    {{ $santri->gender }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs space-y-0.5">
                                            <div class="text-slate-700 dark:text-slate-300 font-semibold">
                                                🏠 {{ $santri->roomAssignments->first()?->room?->dormitory?->name ?? 'Belum ada asrama' }}
                                            </div>
                                            <div class="text-slate-400">
                                                📚 {{ $santri->madrasahEnrollments->first()?->kelas?->name ?? 'Kelas -' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold uppercase {{ $presence === 'mukim' ? 'bg-indigo-100 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300' : 'bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300' }}">
                                                {{ $presence }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold">
                                            @if($hasSib)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    <span>Bersaudara (Aktif Diskon)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-semibold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    <span>Santri Tunggal (Non-Diskon)</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button type="button" wire:click="requestToggleSingle('{{ $santri->id }}')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm {{ $hasSib ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50' }}">
                                                @if($hasSib)
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Ubah ke Santri Tunggal</span>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Ubah ke Bersaudara</span>
                                                @endif
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-slate-400 italic">
                                            Tidak ada data santri yang cocok dengan kriteria pencarian/filter Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($siblingSantriList->hasPages())
                        <div class="px-6 py-4 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
                            {{ $siblingSantriList->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL KONFIRMASI (Alert Confirm)                             --}}
    {{-- ============================================================ --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showConfirmModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-5 text-center">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-slate-100">{{ $confirmTitle }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                        {{ $confirmMessage }}
                    </p>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" wire:click="$set('showConfirmModal', false)"
                        class="flex-1 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="executeConfirmedAction"
                        class="flex-1 py-2.5 bg-gradient-to-br from-violet-600 to-purple-700 hover:from-violet-500 hover:to-purple-600 text-white font-bold rounded-xl text-xs transition-colors shadow-lg shadow-violet-500/20">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Guardian Detail & Santris                             --}}
    {{-- ============================================================ --}}
    @if($showDetailModal && $selectedGuardian)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                    <div class="min-w-0">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">Detail Wali: {{ $selectedGuardian->name }}</h3>
                        <p class="text-xs text-slate-400">Kontak HP: {{ $selectedGuardian->phone_primary }}</p>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Info Card --}}
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="block text-slate-400 font-semibold mb-0.5">Pendidikan</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedGuardian->education_level ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-semibold mb-0.5">Pekerjaan</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedGuardian->occupation ?? '-' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-slate-400 font-semibold mb-0.5">Alamat Wali</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedGuardian->address ?? '-' }}, {{ $selectedGuardian->city ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Linked Santri list --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">👦 Santri yang Di-wali</h4>
                            <button type="button" wire:click="openLinkModal('{{ $selectedGuardian->id }}')"
                                class="px-2.5 py-1.5 bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300 rounded-lg text-xs font-bold hover:bg-violet-100 transition-colors">
                                🔗 Hubungkan Santri Baru
                            </button>
                        </div>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden">
                            @forelse($selectedGuardian->santri as $s)
                                <div class="px-4 py-3 flex items-center justify-between gap-4 bg-white dark:bg-slate-900">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $s->name }}</p>
                                        <p class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.2 bg-violet-100 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 rounded-md font-bold uppercase text-[9px]">{{ $s->pivot->relationship }}</span>
                                            @if($s->pivot->is_primary)
                                                <span class="text-emerald-500 font-bold">★ Wali Utama</span>
                                            @endif
                                        </p>
                                    </div>
                                    <button type="button" wire:click="unlinkSantri('{{ $s->id }}')"
                                        class="p-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors text-xs font-bold">
                                        Lepas
                                    </button>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-slate-400 italic text-xs">
                                    Wali ini belum dihubungkan ke santri manapun.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Create / Edit Guardian Form                           --}}
    {{-- ============================================================ --}}
    @if($showFormModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showFormModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        {{ $isEditing ? 'Ubah Data Wali' : 'Tambah Wali Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showFormModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" wire:model.defer="guardianName" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                        @error('guardianName') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">No HP / WhatsApp</label>
                        <input type="text" wire:model.defer="guardianPhone" placeholder="cth: 0812345678" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                        @error('guardianPhone') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pendidikan</label>
                            <select wire:model.defer="guardianEducation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                                <option value="">Pilih</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="Tidak Sekolah">Tidak Sekolah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan</label>
                            <input type="text" wire:model.defer="guardianOccupation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kota / Kabupaten</label>
                        <input type="text" wire:model.defer="guardianCity" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                        <textarea wire:model.defer="guardianAddress" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                        <textarea wire:model.defer="guardianNotes" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="saveGuardian" class="px-5 py-2 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-violet-500/20 transition-all">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Link Santri to Guardian                               --}}
    {{-- ============================================================ --}}
    @if($showLinkModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4" wire:click.self="$set('showLinkModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Hubungkan Santri Baru</h3>
                    <button type="button" wire:click="$set('showLinkModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Search Santri --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Cari Nama Santri</label>
                        <input type="text" wire:model.live="linkSearch" placeholder="Ketik nama santri..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                    </div>

                    {{-- Search results --}}
                    @if(!empty($linkSearch))
                        <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden max-h-40 overflow-y-auto">
                            @forelse($linkSantriList as $santri)
                                <div class="px-4 py-2.5 flex justify-between items-center gap-4 bg-white dark:bg-slate-900 text-xs">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $santri->name }}</span>
                                    <button type="button" wire:click="linkSantri('{{ $santri->id }}')"
                                        class="px-2.5 py-1 bg-violet-600 hover:bg-violet-700 text-white rounded font-bold">
                                        Hubungkan
                                    </button>
                                </div>
                            @empty
                                <div class="px-4 py-4 text-center text-slate-400 italic text-xs">
                                    Santri tidak ditemukan.
                                </div>
                            @endforelse
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Hubungan Relasi</label>
                            <select wire:model="linkRelationship" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs">
                                @foreach($guardianRelationOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="linkIsPrimary" class="rounded text-violet-600 border-slate-300 dark:border-slate-700">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Set Wali Utama</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Merge duplicate guardians                             --}}
    {{-- ============================================================ --}}
    @if($showMergeModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showMergeModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Gabungkan Wali Duplikat</h3>
                    <button type="button" wire:click="$set('showMergeModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        ⚠️ <strong>PENTING:</strong> Gabungkan wali duplikat ini dengan wali lainnya. Semua santri yang terhubung ke wali ini akan dialihkan ke wali tujuan. Setelah digabungkan, wali ini akan <strong>dihapus permanen</strong>.
                    </p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Wali Tujuan (Target Penggabungan)</label>
                        <select wire:model="mergeTargetId" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                            <option value="">-- Pilih Wali Tujuan --</option>
                            @foreach($mergeCandidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->phone_primary }})</option>
                            @endforeach
                        </select>
                        @error('mergeTargetId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showMergeModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="mergeGuardians" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-sm shadow-lg transition-all">Gabungkan &amp; Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
