<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Lembar Presensi Kegiatan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Bulk logging absensi santri untuk kegiatan wajib, kajian harian, shalat berjamaah, dan absensi berkala.</p>
        </div>
        <button type="button" wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Mulai Kegiatan Baru</span>
        </button>
    </div>

    <!-- Alert Messages -->
    <!-- Activity Selector -->
    <div class="bg-white dark:bg-slate-900 p-6 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm space-y-4">
        <div>
            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pilih Kegiatan Aktif</label>
            <select wire:model.live="selectedActivityId" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-semibold">
                <option value="">-- Pilih Kegiatan / Absensi --</option>
                @foreach ($activities as $act)
                    <option value="{{ $act->id }}">{{ $act->date->isoFormat('D MMMM YYYY') }} -- {{ $act->name }} ({{ $act->organization->name }})</option>
                @endforeach
            </select>
        </div>

        @if ($selectedActivity)
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="block text-slate-400 dark:text-slate-500 uppercase font-semibold">Nama Kegiatan</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 block">{{ $selectedActivity->name }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 dark:text-slate-500 uppercase font-semibold">Jenis Kegiatan</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 block">{{ $selectedActivity->activityType->name }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 dark:text-slate-500 uppercase font-semibold">Unit Penyelenggara</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 block">{{ $selectedActivity->organization->name }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 dark:text-slate-500 uppercase font-semibold">Tanggal</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 block">{{ $selectedActivity->date->isoFormat('D MMMM YYYY') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Attendance Sheet Section -->
    @if ($selectedActivityId)
        <div class="space-y-6">
            <!-- Search & Info Bar -->
            <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-slate-900 p-4 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm items-center justify-between">
                <div class="relative w-full sm:w-80">
                    <input type="text" wire:model.live.debounce.300ms="searchSantri" placeholder="Cari nama santri..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                    <div class="absolute left-3.5 top-3 text-slate-400 dark:text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Setiap perubahan status otomatis tersimpan ke server.</span>
                </div>
            </div>

            <!-- Santri Attendance Table -->
            <x-card bodyClass="p-0 overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 text-left">Nama Santri</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 text-center w-72">Status Kehadiran</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 text-left">Keterangan Tambahan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 text-center w-24">Simpan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm text-slate-700 dark:text-slate-300">
                        @forelse ($santriList as $santri)
                            @php
                                $status = $attendanceStatuses[$santri->id] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                        <span>{{ $santri->name }}</span>
                                        @if (session()->has('status_saved_' . $santri->id))
                                            <span class="inline-flex text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20 px-1.5 py-0.5 rounded border border-emerald-100 dark:border-emerald-900/40 animate-in fade-in slide-in-from-top-1 duration-300">Tersimpan</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">NIK: {{ $santri->nik }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-950 p-1 gap-1 border border-slate-200 dark:border-slate-800">
                                        <!-- Hadir -->
                                        <button type="button" wire:click="setAttendanceStatus('{{ $santri->id }}', 'hadir')" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'hadir' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900' }}">
                                            Hadir
                                        </button>
                                        <!-- Sakit -->
                                        <button type="button" wire:click="setAttendanceStatus('{{ $santri->id }}', 'sakit')" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'sakit' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900' }}">
                                            Sakit
                                        </button>
                                        <!-- Izin -->
                                        <button type="button" wire:click="setAttendanceStatus('{{ $santri->id }}', 'izin')" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'izin' ? 'bg-blue-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900' }}">
                                            Izin
                                        </button>
                                        <!-- Alfa -->
                                        <button type="button" wire:click="setAttendanceStatus('{{ $santri->id }}', 'alfa')" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'alfa' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900' }}">
                                            Absen
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" wire:model.lazy="attendanceNotes.{{ $santri->id }}" 
                                           wire:blur="saveNote('{{ $santri->id }}')" 
                                           placeholder="Sakit demam, ijin pulang, dll..."
                                           class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-700 dark:text-slate-300 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if (session()->has('note_saved_' . $santri->id))
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">Ok</span>
                                    @else
                                        <button type="button" wire:click="saveNote('{{ $santri->id }}')" 
                                                class="text-xs text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-bold transition-colors">
                                            Simpan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                                    Tidak ada data santri untuk unit organisasi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 rounded-2xl shadow-sm flex flex-col items-center justify-center gap-3">
            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <div class="font-bold text-slate-600 dark:text-slate-300 text-sm">Silakan pilih kegiatan aktif terlebih dahulu</div>
            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs leading-relaxed">Pilih kegiatan di dropdown atas untuk memunculkan lembar input absensi santri.</p>
        </div>
    @endif

    <!-- Create Activity Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Mulai Kegiatan & Presensi Baru</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Formulir pendaftaran aktivitas harian santri.</p>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitActivity" class="p-6 space-y-4">
                    <!-- Activity Name -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Nama Kegiatan</label>
                        <input type="text" wire:model.live="name" placeholder="Contoh: Shalat Shubuh Berjamaah, Kajian Kitab Kuning"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Organization -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Organisasi Penyelenggara</label>
                            <select wire:model.live="selectedOrgId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                                <option value="">Pilih Organisasi</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedOrgId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Activity Type -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Jenis Kegiatan</label>
                            <select wire:model.live="selectedActivityTypeId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                                <option value="">Pilih Jenis</option>
                                @foreach ($activityTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedActivityTypeId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tanggal Kegiatan</label>
                            <input type="date" wire:model.live="date" 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            @error('date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Keterangan / Kebutuhan</label>
                        <textarea wire:model.live="description" rows="2" placeholder="Catatan tambahan kegiatan..." 
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm"></textarea>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-emerald-500/10">
                            Mulai & Buat Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
