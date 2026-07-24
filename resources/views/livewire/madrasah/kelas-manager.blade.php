<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-serif-display">Manajemen Kelas Madrasah</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Kelola kelas, jenjang, wali kelas, dan pendaftaran santri ke kelas.</p>
        </div>
    </div>

    <!-- Alerts -->
    @if($successMessage)
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ $successMessage }}</span>
            <button wire:click="$set('successMessage', null)" class="ml-4 opacity-60 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
    @if($errorMessage)
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ $errorMessage }}</span>
            <button wire:click="$set('errorMessage', null)" class="ml-4 opacity-60 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 gap-2">
        <button wire:click="$set('activeTab', 'list')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'list' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            <span>Daftar Kelas</span>
        </button>
        <button wire:click="$set('activeTab', 'form')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'form' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>{{ $editingKelasId ? 'Edit Kelas' : 'Buat Kelas Baru' }}</span>
        </button>
        <button wire:click="$set('activeTab', 'assign')" class="px-5 py-3 text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'assign' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Assign Santri</span>
        </button>
    </div>

    <!-- Tab: Daftar Kelas -->
    @if($activeTab === 'list')
        <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm">
            @if($kelasList->isEmpty())
                <div class="py-16 text-center text-slate-400 text-xs font-semibold">
                    Belum ada kelas terdaftar. Buat kelas baru di tab sebelah.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4">Nama Kelas</th>
                                <th class="py-3 px-4">Jenjang</th>
                                <th class="py-3 px-4">Tahun Ajaran</th>
                                <th class="py-3 px-4">Wali Kelas</th>
                                <th class="py-3 px-4 text-center">Santri</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-slate-700 dark:text-slate-300">
                            @foreach($kelasList as $kelas)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3.5 px-4 font-semibold">{{ $kelas->name }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase
                                            {{ $kelas->jenjang === 'ula' ? 'bg-sky-500/10 text-sky-600' : ($kelas->jenjang === 'wustho' ? 'bg-amber-500/10 text-amber-600' : 'bg-purple-500/10 text-purple-600') }}">
                                            {{ $kelas->jenjang_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">{{ $kelas->academic_year }}</td>
                                    <td class="py-3.5 px-4">{{ $kelas->waliKelas?->name ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-emerald-600">
                                        {{ $kelas->activeEnrollments()->count() }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($kelas->is_active)
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 rounded-md text-[9px] font-extrabold uppercase">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-md text-[9px] font-extrabold uppercase">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="editKelas('{{ $kelas->id }}')" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-500/10 rounded-lg transition-all" title="Edit Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="deleteKelas('{{ $kelas->id }}')" wire:confirm="Yakin hapus kelas {{ $kelas->name }}?" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $kelasList->links() }}</div>
            @endif
        </div>
    @endif

    <!-- Tab: Form Buat/Edit Kelas -->
    @if($activeTab === 'form')
        <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-8 rounded-3xl shadow-sm max-w-2xl">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-6 font-serif-display">
                {{ $editingKelasId ? 'Edit Kelas' : 'Buat Kelas Baru' }}
            </h3>
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Nama Kelas</label>
                        <input type="text" wire:model="formName" placeholder="Contoh: Kelas 1 Ula A" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                        @error('formName') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Jenjang</label>
                        <select wire:model="formJenjang" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                            <option value="ula">Ula (Ibtidaiyah)</option>
                            <option value="wustho">Wustho (Tsanawiyah)</option>
                            <option value="ulya">Ulya (Aliyah)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Tahun Ajaran</label>
                        <input type="text" wire:model="formAcademicYear" placeholder="2025/2026" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Wali Kelas (Opsional)</label>
                        <select wire:model="formWaliKelasId" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($guruOptions as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="formIsActive" id="formIsActive" class="rounded text-emerald-600">
                    <label for="formIsActive" class="text-xs text-slate-600 dark:text-slate-300 font-semibold">Kelas Aktif</label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="saveKelas" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                        {{ $editingKelasId ? 'Simpan Perubahan' : 'Buat Kelas' }}
                    </button>
                    <button wire:click="resetForm(); $set('activeTab', 'list')" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab: Assign Santri ke Kelas -->
    @if($activeTab === 'assign')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Pilih Kelas -->
            <div class="lg:col-span-4 space-y-5">
                <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Pilih Kelas</label>
                        <select wire:model.live="selectedKelasId" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasOptions as $k)
                                <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->academic_year }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Tahun Ajaran Enrollment</label>
                        <input type="text" wire:model.live="enrollAcademicYear" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                    </div>
                    @if($selectedKelas)
                        <div class="p-3 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                            <p class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider">Kelas Terpilih</p>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">{{ $selectedKelas->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $selectedKelas->jenjang_label }} — {{ $selectedKelas->academic_year }}</p>
                        </div>
                    @endif

                    <!-- Cari Santri -->
                    @if($selectedKelasId)
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Tambah Santri</label>
                            <input type="text" wire:model.live="searchSantri" placeholder="Ketik nama santri..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                            @if(count($santriSearchResults) > 0)
                                <div class="mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-lg">
                                    @foreach($santriSearchResults as $s)
                                        <button wire:click="enrollSantri('{{ $s->id }}')" class="w-full text-left px-4 py-2.5 hover:bg-emerald-500/5 text-xs text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800 last:border-0 flex items-center justify-between gap-2 transition-colors">
                                            <span class="font-semibold">{{ $s->name }}</span>
                                            <span class="text-[9px] uppercase font-extrabold text-slate-400">{{ $s->gender === 'L' ? 'Putra' : 'Putri' }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif(strlen($searchSantri) >= 3)
                                <p class="text-[10px] text-slate-400 mt-2">Santri tidak ditemukan atau sudah terdaftar di kelas ini.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Daftar Santri di Kelas -->
            <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm">
                @if(!$selectedKelasId)
                    <div class="py-16 text-center text-slate-400 text-xs font-semibold">Pilih kelas terlebih dahulu untuk melihat daftar santri.</div>
                @elseif($enrolledSantri->isEmpty())
                    <div class="py-16 text-center text-slate-400 text-xs font-semibold">Belum ada santri yang terdaftar di kelas ini.</div>
                @else
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display">
                            Santri di Kelas — {{ $selectedKelas?->name }}
                        </h3>
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 rounded-full text-xs font-bold">
                            {{ $enrolledSantri->count() }} santri
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-2.5 px-3">No</th>
                                    <th class="py-2.5 px-3">Nama Santri</th>
                                    <th class="py-2.5 px-3 text-center">Gender</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-slate-600 dark:text-slate-300">
                                @foreach($enrolledSantri as $i => $enrollment)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 px-3 text-slate-400">{{ $i + 1 }}</td>
                                        <td class="py-3 px-3 font-semibold">{{ $enrollment->person->name }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase {{ $enrollment->person->gender === 'L' ? 'bg-sky-500/10 text-sky-600' : 'bg-rose-500/10 text-rose-500' }}">
                                                {{ $enrollment->person->gender === 'L' ? 'Putra' : 'Putri' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <button wire:click="unenrollSantri('{{ $enrollment->id }}')" wire:confirm="Keluarkan {{ $enrollment->person->name }} dari kelas ini?" class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 rounded-lg transition-all" title="Keluarkan dari Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
