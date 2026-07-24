<div class="space-y-8 max-w-7xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        <a href="{{ route('keuangan.billing', ['activeTab' => 'exceptions']) }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Pusat Kendali Keuangan</a>
        <span>&middot;</span>
        <span class="font-bold text-slate-900 dark:text-white">Edit Kelompok Dispensasi</span>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-serif-display flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Kelompok Dispensasi
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Ubah tarif nominal keringanan, perbarui alasan, atau kelola anggota kelompok ini secara massal.</p>
        </div>
        <a href="{{ route('keuangan.billing', ['activeTab' => 'exceptions']) }}" 
            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 self-start md:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    @if(session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-xs font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- FORM PANEL (LEFT) -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm space-y-6">
            
            <!-- step 1: Locked Config -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-[10px] font-extrabold text-slate-450 uppercase tracking-wider">1. Pilih Iuran / Tagihan (Terkunci)</label>
                    <span class="inline-flex items-center gap-1 text-[9px] text-amber-600 dark:text-amber-450 font-bold bg-amber-500/10 px-2 py-0.5 rounded-md">
                        🔒 Iuran Tidak Dapat Dipindahkan
                    </span>
                </div>
                <div class="relative">
                    <select disabled class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-850 text-slate-400 dark:text-slate-500 rounded-xl px-4 py-2.5 text-xs font-bold cursor-not-allowed">
                        @if($targetConfig)
                            <option selected>{{ $targetConfig->label }} (Rp {{ number_format($targetConfig->amount, 0, ',', '.') }})</option>
                        @else
                            <option selected>Iuran Tidak Ditemukan</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- step 2: Filter and Select Students -->
            <div class="space-y-4 border-t border-slate-100 dark:border-slate-800/80 pt-5">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-450 uppercase tracking-wider mb-3">2. Saring & Kelola Anggota Kelompok</h3>
                    
                    <!-- Search Filter Row -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-slate-50/50 dark:bg-slate-950/40 p-4 border border-slate-200/40 dark:border-slate-800/60 rounded-2xl">
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Komplek (Asrama)</label>
                            <select wire:model.live="filterDormitoryId" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-blue-500">
                                <option value="">Semua Komplek</option>
                                @foreach($dormitories as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Kelas Madrasah</label>
                            <select wire:model.live="filterKelasId" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-blue-500">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->jenjang }})</option>
                                @endforeach
                            </select>
                        </div>
                        @if(!$this->genderScope())
                            <div>
                                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                                <select wire:model.live="filterGender" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-blue-500">
                                    <option value="">Semua</option>
                                    <option value="L">Putra (L)</option>
                                    <option value="P">Putri (P)</option>
                                </select>
                            </div>
                        @else
                            <div class="flex items-end pb-2">
                                <span class="inline-block px-3 py-1.5 bg-slate-100 dark:bg-slate-850 text-slate-600 dark:text-slate-400 text-[10px] font-bold rounded-xl uppercase tracking-wider">
                                    Gender: {{ $this->genderScope() === 'L' ? 'PUTRA (L)' : 'PUTRI (P)' }}
                                </span>
                            </div>
                        @endif
                        <div class="@if($this->genderScope()) col-span-2 @endif">
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Cari Nama</label>
                            <input type="text" wire:model.live="filterSearch" placeholder="Ketik nama santri..." class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-[11px] focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Accelerator Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs pt-1">
                    <div class="flex flex-wrap gap-2">
                        @php $filteredStudentIds = $students->pluck('id')->toArray(); @endphp
                        @if(!empty($filteredStudentIds))
                            <button type="button" wire:click="selectAllFiltered({{ json_encode($filteredStudentIds) }})" class="px-3.5 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-bold rounded-xl text-[10px] transition-all">
                                ✓ Pilih Semua Hasil Filter ({{ count($filteredStudentIds) }})
                            </button>
                            <button type="button" wire:click="deselectAllFiltered({{ json_encode($filteredStudentIds) }})" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-bold rounded-xl text-[10px] transition-all">
                                ✕ Batal Pilih Hasil Filter
                            </button>
                        @endif
                    </div>
                    
                    <button type="button" wire:click="autoSelectSiblingDiscountRecipients" class="px-3.5 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold rounded-xl text-[10px] transition-all flex items-center gap-1">
                        👥 Deteksi Otomatis Santri Bersaudara
                    </button>
                </div>

                <!-- Students Selection Table -->
                <div class="border border-slate-200/60 dark:border-slate-800 rounded-2xl overflow-hidden max-h-72 overflow-y-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="sticky top-0 bg-slate-100 dark:bg-slate-950 z-10">
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                <th class="py-2.5 px-4 text-center w-12">Pilih</th>
                                <th class="py-2.5 px-4">Nama Santri</th>
                                <th class="py-2.5 px-4">Komplek / Kamar</th>
                                <th class="py-2.5 px-4">Kelas Madrasah</th>
                                <th class="py-2.5 px-4 text-center">Gender</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                            @forelse($students as $s)
                                @php
                                    $isSelected = in_array($s->id, $excSantriIds);
                                    $dormName = $s->roomAssignments->firstWhere('is_active', true)?->room?->dormitory?->name ?? '—';
                                    $kelasName = $s->madrasahEnrollments->firstWhere('is_active', true)?->kelas?->name ?? '—';
                                @endphp
                                <tr wire:key="student-row-{{ $s->id }}" wire:click="toggleSantri('{{ $s->id }}')" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 cursor-pointer transition-colors {{ $isSelected ? 'bg-blue-500/5 dark:bg-blue-500/10' : '' }}">
                                    <td class="py-2.5 px-4 text-center">
                                        <input wire:key="student-check-{{ $s->id }}" type="checkbox" @checked($isSelected) class="w-4 h-4 rounded border-2 border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-slate-800 dark:text-slate-200">
                                        {{ $s->name }}
                                        @if(isset($existingExceptionsMap[$s->id]))
                                            <span class="inline-block ml-2 px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[8px] font-extrabold rounded-md" title="{{ $existingExceptionsMap[$s->id] }}">
                                                ⚠️ {{ $existingExceptionsMap[$s->id] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-500">{{ $dormName }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-500">{{ $kelasName }}</td>
                                    <td class="py-2.5 px-4 text-center uppercase text-[9px] font-bold text-slate-400">{{ $s->gender }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 font-medium">Santri tidak ditemukan dengan filter di atas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- step 3: Waiver rate details -->
            <div class="space-y-4 border-t border-slate-100 dark:border-slate-800/80 pt-5">
                <h3 class="text-xs font-extrabold text-slate-450 uppercase tracking-wider">3. Detail Keringanan Tarif & Alasan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Tipe Dispensasi</label>
                        <select wire:model.live="excType" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-blue-500">
                            <option value="discount">Potongan (Diskon Rupiah)</option>
                            <option value="waived">Bebas Biaya (Lunas / Rp 0)</option>
                            <option value="custom_rate">Tarif Khusus Tetap (Custom Rate)</option>
                        </select>
                        @error('excType') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($excType !== 'waived')
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Nominal Uang (Rp)</label>
                            <input type="number" wire:model.live.debounce.300ms="excAmount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-blue-500 text-right font-bold">
                            @error('excAmount') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Nama / Keterangan Potongan (Nama Kelompok)</label>
                    <input type="text" wire:model.live.debounce.300ms="excNotes" placeholder="Contoh: Diskon Kakak-Adik, Beasiswa Abdi Dalem..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-blue-500 font-bold">
                    @error('excNotes') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    <p class="text-[9px] text-slate-400 mt-1.5">Alasan/Keterangan ini wajib diisi untuk mengelompokkan dispensasi dalam satu baris kelompok.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button wire:click="saveException" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                    Simpan Perubahan Dispensasi
                </button>
            </div>
        </div>

        <!-- RIGHT PANEL (LIVE PREVIEW & SELECTED SUMMARY) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Live Preview Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest block font-serif-display">Simulasi Live Preview Potongan</h3>
                
                @if(!$previewData)
                    <div class="py-10 text-center text-slate-400 text-xs font-semibold">
                        Masukkan nominal potongan untuk melihat simulasi tarif santri.
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="p-6 bg-slate-950 text-white rounded-2xl space-y-4 shadow-xl border border-slate-800 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-24 h-24 bg-blue-500/25 rounded-full blur-xl"></div>
                            
                            <div>
                                <span class="text-[8px] font-extrabold text-blue-400 uppercase tracking-widest">KARTU SIMULASI TARIF</span>
                                <h4 class="text-sm font-bold truncate leading-snug">{{ $previewData['label'] }}</h4>
                            </div>

                            <div class="border-t border-slate-800 pt-3 space-y-2 text-xs">
                                <div class="flex justify-between text-slate-400">
                                    <span>Tarif Dasar Asli</span>
                                    <span class="line-through">Rp {{ number_format($previewData['original'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-emerald-400 font-semibold">
                                    <span>Potongan / Keringanan</span>
                                    <span>- Rp {{ number_format($previewData['discount_applied'], 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-slate-800/80 pt-2 flex justify-between text-sm font-extrabold">
                                    <span>Tarif Baru Santri</span>
                                    <span class="text-blue-400 text-base font-black">Rp {{ number_format($previewData['final'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-2xl text-[10px] text-blue-700 dark:text-blue-400 leading-normal font-semibold space-y-1">
                            <span class="block font-bold">💡 Aturan Penerapan Tagihan:</span>
                            @if($previewData['final'] == 0.00)
                                <p>Seluruh santri terpilih akan dibebaskan penuh dari biaya iuran ini. Tagihan berjalan yang belum lunas otomatis akan ditandai **LUNAS (Rp 0)**.</p>
                            @else
                                <p>Santri terpilih hanya akan ditagih sebesar **Rp {{ number_format($previewData['final'], 0, ',', '.') }}** untuk iuran ini. Tagihan berjalan yang belum lunas otomatis terpotong nominalnya.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Selected Students Summary Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-850 pb-2.5">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest block font-serif-display">Anggota Kelompok</h3>
                        <span class="text-[10px] text-slate-500 font-bold block">{{ count($selectedStudents) }} Santri Terlihat</span>
                    </div>
                    @if(!empty($excSantriIds))
                        <button type="button" wire:click="clearSelected" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-[10px] font-bold rounded-xl transition-all">
                            Bersihkan Semua
                        </button>
                    @endif
                </div>

                @if($selectedStudents->isEmpty())
                    <div class="py-12 text-center text-slate-400 text-[11px] font-semibold">
                        Belum ada santri yang terpilih. Silakan centang nama santri di tabel sebelah kiri.
                    </div>
                @else
                    <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                        @foreach($selectedStudents as $st)
                            @php
                                $dName = $st->roomAssignments->firstWhere('is_active', true)?->room?->dormitory?->name ?? '—';
                                $kName = $st->madrasahEnrollments->firstWhere('is_active', true)?->kelas?->name ?? '—';
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800/80 rounded-2xl text-[11px]">
                                <div class="overflow-hidden pr-2">
                                    <strong class="text-slate-850 dark:text-slate-200 block truncate font-bold">{{ $st->name }}</strong>
                                    <span class="text-[9px] text-slate-450 block truncate font-semibold">Asrama: {{ $dName }} | Kelas: {{ $kName }}</span>
                                    @if(isset($existingExceptionsMap[$st->id]))
                                        <span class="text-[9px] text-amber-600 dark:text-amber-400 font-extrabold block mt-0.5">
                                            ⚠️ Menimpa: {{ $existingExceptionsMap[$st->id] }}
                                        </span>
                                    @endif
                                </div>
                                <button type="button" wire:click="toggleSantri('{{ $st->id }}')" class="p-1.5 bg-slate-200/50 dark:bg-slate-800 hover:bg-rose-500/10 dark:hover:bg-rose-500/20 hover:text-rose-600 rounded-xl text-slate-400 transition-all font-bold" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Hidden count note for sibling matching gender-restriction -->
                    @if($hiddenSelectedCount > 0)
                        <div class="p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl text-[10px] text-blue-700 dark:text-blue-400 font-semibold leading-normal">
                            ℹ️ **Catatan Backend**: Terdapat **{{ $hiddenSelectedCount }} santri bergender lain** yang tergabung dalam kelompok ini di latar belakang (tidak ditampilkan di UI karena pembatasan gender wewenang Anda) yang akan tetap dipertahankan dan diperbarui secara aman.
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</div>
