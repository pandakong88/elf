<div class="space-y-8 max-w-7xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        <a href="{{ route('keuangan.billing', ['activeTab' => 'exceptions']) }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Pusat Kendali Keuangan</a>
        <span>&middot;</span>
        <span class="font-bold text-slate-900 dark:text-white">Tambah Dispensasi Baru</span>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-serif-display flex items-center gap-2">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Tambah Kelompok Dispensasi Baru
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Daftarkan potongan harga, keringanan, atau bebas biaya untuk kelompok santri tertentu.</p>
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
            
            <!-- Validation Errors Global Banner -->
            @if($errors->any())
                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 rounded-2xl text-xs space-y-2 shadow-sm">
                    <div class="font-extrabold flex items-center gap-2 text-rose-600 dark:text-rose-400">
                        <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mohon Lengkapi Kolom yang Diperlukan:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-600 dark:text-rose-300 pl-1 font-semibold">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- step 1: Select Config -->
            <div class="space-y-2">
                <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">1. Pilih Iuran / Tagihan (Gunakan Pencarian)</label>
                <div wire:ignore
                     x-data="{
                         choicesInstance: null,
                         init() {
                             this.$nextTick(() => {
                                 const el = this.$el.querySelector('#excConfigId');
                                 if (el && typeof Choices !== 'undefined') {
                                     this.choicesInstance = new Choices(el, {
                                         searchEnabled: true,
                                         searchPlaceholderValue: 'Ketik nama iuran...',
                                         itemSelectText: '',
                                         noResultsText: 'Iuran tidak ditemukan',
                                         shouldSort: false,
                                         classNames: {
                                             containerOuter: ['choices', 'choices--custom'],
                                         },
                                     });
                                     el.addEventListener('change', () => {
                                         @this.set('excConfigId', el.value);
                                     });
                                 }
                             });
                         }
                     }">
                    <select id="excConfigId" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500 font-bold">
                        <option value="">-- Cari & Pilih Konfigurasi Iuran --</option>
                        @foreach($activeConfigs as $c)
                            <option value="{{ $c->id }}" @selected($c->id == $excConfigId)>{{ $c->label }} (Rp {{ number_format($c->amount, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                @error('excConfigId') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- step 2: Filter and Select Students -->
            <div class="space-y-4 border-t border-slate-100 dark:border-slate-800/80 pt-5">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-extrabold text-slate-450 uppercase tracking-wider">2. Saring &amp; Pilih Santri Penerima</h3>
                        @if(count($excSantriIds) === 0)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40">
                                ⚠️ Belum ada santri terpilih
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/40">
                                ✓ {{ count($excSantriIds) }} Santri Terpilih
                            </span>
                        @endif
                    </div>
                    @error('excSantriIds')
                        <span class="text-xs text-rose-500 font-semibold mb-2 block">{{ $message }}</span>
                    @enderror
                    
                    <!-- Search Filter Row -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 bg-slate-50/50 dark:bg-slate-950/40 p-4 border border-slate-200/40 dark:border-slate-800/60 rounded-2xl">
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Komplek (Asrama)</label>
                            <select wire:model.live="filterDormitoryId" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-emerald-500">
                                <option value="">Semua Komplek</option>
                                @foreach($dormitories as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Kelas Madrasah</label>
                            <select wire:model.live="filterKelasId" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-emerald-500">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->jenjang }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Kehadiran</label>
                            <select wire:model.live="filterPresenceStatus" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-emerald-500">
                                <option value="">Semua Status</option>
                                <option value="mukim">Mukim</option>
                                <option value="laju">Laju / Non-Mukim</option>
                            </select>
                        </div>
                        @if(!$this->genderScope())
                            <div>
                                <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                                <select wire:model.live="filterGender" class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-2.5 py-1.5 text-[11px] focus:ring-emerald-500">
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
                        <div class="@if($this->genderScope()) col-span-1 @endif">
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Cari Nama</label>
                            <input type="text" wire:model.live="filterSearch" placeholder="Ketik nama santri..." class="w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-1.5 text-[11px] focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- Accelerator Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs pt-1">
                    <div class="flex flex-wrap gap-2">
                        @php $filteredStudentIds = $students->pluck('id')->toArray(); @endphp
                        @if(!empty($filteredStudentIds))
                            <button type="button" wire:click="selectAllFiltered({{ json_encode($filteredStudentIds) }})" class="px-3.5 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold rounded-xl text-[10px] transition-all">
                                ✓ Pilih Semua Hasil Filter ({{ count($filteredStudentIds) }})
                            </button>
                            <button type="button" wire:click="deselectAllFiltered({{ json_encode($filteredStudentIds) }})" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-bold rounded-xl text-[10px] transition-all">
                                ✕ Batal Pilih Hasil Filter
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2">
                        @if(!empty($existingGroupsList))
                            <div class="relative">
                                <select wire:model.live="copyFromGroupKey" class="px-3 py-1.5 bg-violet-50 dark:bg-violet-950/40 border border-violet-200 dark:border-violet-800 text-violet-700 dark:text-violet-300 font-bold rounded-xl text-[10px] focus:ring-violet-500 cursor-pointer">
                                    <option value="">📋 Salin Santri dari Dispensasi Lain...</option>
                                    @foreach($existingGroupsList as $groupItem)
                                        <option value="{{ $groupItem['key'] }}">{{ $groupItem['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <button type="button" wire:click="autoSelectSiblingDiscountRecipients" class="px-3.5 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 font-bold rounded-xl text-[10px] transition-all flex items-center gap-1">
                            👥 Deteksi Otomatis Santri Bersaudara
                        </button>
                    </div>
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
                                <tr wire:key="student-row-{{ $s->id }}" wire:click="toggleSantri('{{ $s->id }}')" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 cursor-pointer transition-colors {{ $isSelected ? 'bg-emerald-500/5 dark:bg-emerald-500/10' : '' }}">
                                    <td class="py-2.5 px-4 text-center">
                                        <input wire:key="student-check-{{ $s->id }}" type="checkbox" @checked($isSelected) class="w-4 h-4 rounded border-2 border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
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
                        <select wire:model.live="excType" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500">
                            <option value="discount">Potongan (Diskon Rupiah)</option>
                            <option value="waived">Bebas Biaya (Lunas / Rp 0)</option>
                            <option value="custom_rate">Tarif Khusus Tetap (Custom Rate)</option>
                        </select>
                        @error('excType') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($excType !== 'waived')
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Nominal Uang (Rp)</label>
                            <input type="number" wire:model.live.debounce.300ms="excAmount" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500 text-right font-bold">
                            @error('excAmount') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Nama / Keterangan Potongan (Nama Kelompok)</label>
                    <input type="text" wire:model.live.debounce.300ms="excNotes" placeholder="Contoh: Diskon Kakak-Adik, Beasiswa Abdi Dalem, Yatim Piatu..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-emerald-500 font-bold">
                    <p class="text-[9px] text-slate-400 mt-1.5">Alasan/Keterangan ini wajib diisi dengan seragam untuk mengelompokkan dispensasi dalam satu baris kelompok.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button wire:click="requestSaveConfirmation" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan &amp; Terapkan Dispensasi</span>
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
                        Pilih jenis iuran dan masukkan nominal potongan untuk melihat simulasi tarif santri.
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="p-6 bg-slate-950 text-white rounded-2xl space-y-4 shadow-xl border border-slate-800 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-24 h-24 bg-emerald-500/20 rounded-full blur-xl"></div>
                            
                            <div>
                                <span class="text-[8px] font-extrabold text-emerald-500 uppercase tracking-widest">KARTU SIMULASI TARIF</span>
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
                                    <span class="text-emerald-400 text-base font-black">Rp {{ number_format($previewData['final'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl text-[10px] text-emerald-700 dark:text-emerald-400 leading-normal font-semibold space-y-1">
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

            <!-- Selected Students Summary Card (Tampilan Santri yang Terpilih) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-850 pb-2.5">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest block font-serif-display">Daftar Santri Terpilih</h3>
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
                            ℹ️ **Catatan Backend**: Terdapat **{{ $hiddenSelectedCount }} santri bergender lain** yang juga ikut terpilih di latar belakang (misalnya hasil deteksi relasi saudara lintas gender) dan akan ikut tersimpan dalam kelompok ini.
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI SIMPAN DISPENSASI --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showConfirmModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-5 text-center">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-slate-100">Konfirmasi Simpan Dispensasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                        Anda akan menerapkan dispensasi untuk <strong class="text-slate-800 dark:text-slate-200">{{ count($excSantriIds) }} santri</strong> terpilih.
                    </p>

                    @if($overwriteCount > 0)
                        <div class="mt-4 p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-2xl text-left text-xs space-y-1.5">
                            <div class="font-bold text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Peringatan Overwrite (Menimpa Status)</span>
                            </div>
                            <p class="text-amber-600 dark:text-amber-400 text-[11px] leading-relaxed">
                                Sebanyak <strong>{{ $overwriteCount }} santri</strong> sudah memiliki dispensasi terdahulu pada iuran ini. Menyimpan akan memperbarui tarif mereka.
                            </p>
                            @if(!empty($overwriteSantriNames))
                                <div class="text-[10px] text-amber-600/80 dark:text-amber-400/80 font-medium italic pt-0.5">
                                    Santri: {{ implode(', ', $overwriteSantriNames) }}@if($overwriteCount > count($overwriteSantriNames)), dkk.@endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" wire:click="$set('showConfirmModal', false)"
                        class="flex-1 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="executeSaveException"
                        class="flex-1 py-2.5 bg-gradient-to-br from-emerald-600 to-teal-700 hover:from-emerald-500 hover:to-teal-600 text-white font-bold rounded-xl text-xs transition-colors shadow-lg shadow-emerald-500/20">
                        Ya, Simpan &amp; Terapkan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
