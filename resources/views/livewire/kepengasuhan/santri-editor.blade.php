<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('kepengasuhan.peta-santri') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Edit Biodata Santri</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $person->name }} — NIS: {{ $person->santriProfile?->nis ?? '-' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 text-xs font-extrabold rounded-full {{ $person->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                {{ $person->gender === 'L' ? '👦 Putra' : '👧 Putri' }}
            </span>
            <span class="px-3 py-1 text-xs font-extrabold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                NIK: {{ $person->nik ?? 'Belum ada' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: FORM EDIT (2/3 lebar) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Seksi 1: Data Pribadi --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 p-5 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-2 h-5 rounded-full bg-emerald-500"></span>
                    <h2 class="font-extrabold text-sm text-slate-900 dark:text-slate-100 uppercase tracking-wide">1. Data Pribadi Santri</h2>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="formName" placeholder="Nama lengkap santri"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('formName') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">No. HP / WA Santri</label>
                        <input type="text" wire:model="formPhone" placeholder="08xxxxxxxxxx"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tempat Lahir</label>
                        <input type="text" wire:model="formBirthPlace" placeholder="Kota kelahiran"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tanggal Lahir</label>
                        <input type="date" wire:model="formBirthDate"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Alamat Lengkap</label>
                        <textarea wire:model="formAddress" rows="2" placeholder="Alamat lengkap santri"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Catatan Internal</label>
                        <textarea wire:model="formNotes" rows="2" placeholder="Catatan khusus (opsional)"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Seksi 2: Kesehatan & Sekolah Formal --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 p-5 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-2 h-5 rounded-full bg-rose-500"></span>
                    <h2 class="font-extrabold text-sm text-slate-900 dark:text-slate-100 uppercase tracking-wide">2. Kesehatan &amp; Sekolah Formal</h2>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Golongan Darah</label>
                        <select wire:model="formBloodType"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                            <option value="">-- Pilih Golongan Darah --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Sekolah Formal / Luar</label>
                        <input type="text" wire:model="formSchoolName" placeholder="Nama sekolah formal"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tingkat / Kelas Formal</label>
                        <input type="text" wire:model="formSchoolYear" placeholder="Contoh: Kelas 7 / Semester 2"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Riwayat Penyakit</label>
                        <input type="text" wire:model="formMedicalHistory" placeholder="Tidak ada / nama penyakit"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Alergi</label>
                        <input type="text" wire:model="formAllergies" placeholder="Tidak ada / daftar alergi"
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- Seksi 3: Data Orang Tua --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 p-5 border-b border-slate-100 dark:border-slate-800">
                    <span class="w-2 h-5 rounded-full bg-indigo-500"></span>
                    <h2 class="font-extrabold text-sm text-slate-900 dark:text-slate-100 uppercase tracking-wide">3. Data Orang Tua / Wali</h2>
                </div>
                <div class="p-5 space-y-5">
                    {{-- Ayah --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-sm">👨</span>
                            <h3 class="text-xs font-extrabold text-indigo-700 dark:text-indigo-300 uppercase tracking-wide">Ayah Kandung</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Nama Ayah</label>
                                <input type="text" wire:model="formFatherName" placeholder="Nama lengkap ayah"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">No. HP Ayah</label>
                                <input type="text" wire:model="formFatherPhone" placeholder="08xxxxxxxxxx"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Pekerjaan Ayah</label>
                                <input type="text" wire:model="formFatherOccupation" placeholder="Pekerjaan"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Alamat Ayah</label>
                                <input type="text" wire:model="formFatherAddress" placeholder="Alamat"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800"></div>

                    {{-- Ibu --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-sm">👩</span>
                            <h3 class="text-xs font-extrabold text-pink-700 dark:text-pink-300 uppercase tracking-wide">Ibu Kandung</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Nama Ibu</label>
                                <input type="text" wire:model="formMotherName" placeholder="Nama lengkap ibu"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">No. HP Ibu</label>
                                <input type="text" wire:model="formMotherPhone" placeholder="08xxxxxxxxxx"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Pekerjaan Ibu</label>
                                <input type="text" wire:model="formMotherOccupation" placeholder="Pekerjaan"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Alamat Ibu</label>
                                <input type="text" wire:model="formMotherAddress" placeholder="Alamat"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800"></div>

                    {{-- Wali Santri (Non-Orang Tua / Opsional) --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-sm">👤</span>
                            <h3 class="text-xs font-extrabold text-amber-700 dark:text-amber-300 uppercase tracking-wide">Wali Santri (Non-Orang Tua / Opsional)</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Nama Wali</label>
                                <input type="text" wire:model="formGuardianName" placeholder="Nama wali (jika ikut wali)"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">No. HP / WA Wali</label>
                                <input type="text" wire:model="formGuardianPhone" placeholder="08xxxxxxxxxx"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Hubungan Wali</label>
                                <input type="text" wire:model="formGuardianRelationship" placeholder="Contoh: Paman, Kakek, Kakak, Wali"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('kepengasuhan.peta-santri') }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-all">
                    Batal
                </a>
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold rounded-xl text-sm shadow-md transition-all flex items-center gap-2">
                    <span wire:loading wire:target="save" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>

        {{-- KOLOM KANAN: HISTORY PERUBAHAN (1/3 lebar) --}}
        <div class="space-y-4">
            {{-- Info Penempatan (readonly) --}}
            @php
                $prof     = $person->santriProfile;
                $latestAssignment = $person->roomAssignments()->where('is_active', true)->with('room.dormitory')->first();
                $latestKelas = $person->madrasahEnrollments()->where('is_active', true)->with('kelas')->first();
                $roleInfo = $person->roles()->where('role_type','santri')->where('is_active',true)->first();
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm p-4 space-y-3">
                <h3 class="font-extrabold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wide flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Status Penempatan
                    <span class="text-[10px] font-normal text-slate-400">(readonly)</span>
                </h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold text-emerald-600">{{ strtoupper($roleInfo?->enrollment_status ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Keberadaan</span>
                        <span class="font-bold text-indigo-600">{{ strtoupper($roleInfo?->presence_status ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Komplek</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $latestAssignment?->room?->dormitory?->name ?? 'Non-Asrama' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kamar</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $latestAssignment?->room?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelas</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $latestKelas?->kelas?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- History Perubahan --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm p-4 space-y-3">
                <h3 class="font-extrabold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wide flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Riwayat Perubahan
                </h3>
                <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($activityLogs as $log)
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50 space-y-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-relaxed">{{ $log->description }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                <span>👤 {{ $log->causer?->name ?? 'Sistem' }}</span>
                                <span>•</span>
                                <span>{{ $log->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400 italic">Belum ada riwayat perubahan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
