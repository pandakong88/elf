<div class="py-6 px-4 sm:px-6 lg:px-8">
    @if (!$cd)
        <div class="p-6 text-center">
            <p class="text-rose-500 font-bold">Data tidak ditemukan atau Anda tidak berwenang mengakses asrama ini.</p>
        </div>
    @else
        <!-- Header & Rejection Notes -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('sensus.campaigns') }}" class="hover:underline">Sensus Fleksibel</a>
                        <span>&rsaquo;</span>
                        <span>Lembar Input</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Sensus: {{ $cd->campaign->name }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Mengisi data asrama: <strong class="text-slate-700 dark:text-slate-300">{{ $cd->dormitory->name }}</strong> (Target {{ $cd->progress_total }} santri)</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('sensus.campaigns') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all">
                        Kembali ke Dasbor
                    </a>
                </div>
            </div>

            @if ($cd->status === 'rejected')
                <div class="mt-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 rounded-2xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <h4 class="font-bold text-sm">Laporan Ditolak / Dikembalikan oleh Admin:</h4>
                        <p class="text-xs mt-1 text-slate-650 dark:text-slate-300 italic">"{{ $cd->rejection_notes }}"</p>
                        <p class="text-[10px] mt-2 font-bold text-rose-500 uppercase">Silakan perbaiki data di bawah lalu kirim kembali.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Success & Error Alert -->
        <!-- Controls Toolbar Card -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3 flex-1">
                <!-- Room Filter -->
                <div class="w-full sm:w-48">
                    <select wire:model.live="roomFilter" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Kamar</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Actions buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if ($cd->campaign->allow_excel)
                    <!-- Excel Download -->
                    <button type="button" wire:click="downloadTemplate" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5" title="Unduh Template Excel">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg><span>Unduh Excel</span>
                    </button>

                    <!-- Excel Upload (Only if editable) -->
                    @if (in_array($cd->status, ['pending', 'in_progress', 'rejected']))
                        <label class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer" title="Unggah Excel">
                            <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg><span>Unggah Excel</span>
                            <input type="file" wire:model="excelFile" class="hidden" accept=".xlsx,.xls">
                        </label>
                    @endif
                @endif

                @if (in_array($cd->status, ['pending', 'in_progress', 'rejected']))
                    <button type="button" wire:click="bulkConfirm" class="px-4 py-2 bg-indigo-50/80 hover:bg-indigo-100 text-indigo-650 dark:bg-indigo-950/30 dark:hover:bg-indigo-950/60 dark:text-indigo-400 font-bold rounded-xl text-xs transition-all flex items-center gap-1">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg><span>Konfirmasi Massal (Semua Mukim &amp; Hadir)</span>
                    </button>
                    <button type="button" wire:click="saveDraft" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-800 dark:bg-slate-200 dark:hover:bg-white text-white dark:text-slate-900 font-bold rounded-xl text-xs shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>Simpan Draft
                    </button>
                    <button type="button" onclick="confirm('Apakah Anda yakin ingin mengirim laporan sensus asrama ini ke pusat? Data tidak dapat diubah lagi setelah dikirim.') || event.stopImmediatePropagation()" wire:click="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>Kirim Laporan
                    </button>
                @else
                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-200/50 dark:border-slate-700/50"><svg class="w-3.5 h-3.5 text-slate-500 mr-1.5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Pengisian sensus ditutup (Status: {{ $cd->getStatusLabel() }})</span>
                @endif
            </div>
        </div>

        <!-- Input Table Grid -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-800">
                            <th class="p-4 sm:px-6 w-12 text-center">No</th>
                            <th class="p-4 w-28">Kamar</th>
                            <th class="p-4">Nama Santri</th>
                            <th class="p-4 w-44">Status Anggota (Enrollment)</th>
                            <th class="p-4 w-44">Status Keberadaan (Presence)</th>

                            <!-- Dynamic fields headers from template -->
                            @foreach ($cd->campaign->template->fields as $field)
                                <th class="p-4 min-w-[8rem]">{{ $field->field_label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse ($santriList as $idx => $s)
                            @php
                                $personId = $s->person_id;
                                $isInactive = $enrollmentStatuses[$personId] !== 'aktif';
                                $isAlpa = $presenceStatuses[$personId] === 'alpa';
                                $rowBg = '';
                                if ($isInactive) {
                                    $rowBg = 'bg-slate-50/50 dark:bg-slate-950/20 opacity-60';
                                } elseif ($isAlpa) {
                                    $rowBg = 'bg-rose-500/5 dark:bg-rose-950/5 border-l-2 border-l-rose-500';
                                }
                            @endphp
                            <tr class="{{ $rowBg }} transition-all duration-200">
                                <!-- No -->
                                <td class="p-4 sm:px-6 text-center font-semibold text-slate-400">{{ $idx + 1 }}</td>

                                <!-- Room -->
                                <td class="p-4">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $s->room_name ?? '-' }}
                                    </span>
                                </td>

                                <!-- Santri Info -->
                                <td class="p-4">
                                    <div>
                                        <span class="block font-bold text-slate-800 dark:text-white">{{ $s->person_name }}</span>
                                        <span class="block text-[10px] text-slate-400">NIK: {{ $s->nik ?: '-' }}</span>
                                    </div>
                                </td>

                                <!-- Enrollment Status -->
                                <td class="p-4">
                                    @can('change-enrollment-status')
                                        <select wire:model.live="enrollmentStatuses.{{ $personId }}" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-[11px] focus:border-emerald-500 focus:ring-emerald-500 w-full">
                                            <option value="aktif">Aktif</option>
                                            <option value="alumni">Alumni</option>
                                            <option value="keluar_resmi">Keluar Resmi</option>
                                            <option value="dikeluarkan">Dikeluarkan</option>
                                            <option value="tanpa_keterangan">Tanpa Keterangan</option>
                                        </select>
                                    @else
                                        @php
                                            $labels = [
                                                'aktif' => 'Aktif', 'alumni' => 'Alumni', 'keluar_resmi' => 'Keluar Resmi',
                                                'dikeluarkan' => 'Dikeluarkan', 'tanpa_keterangan' => 'Tanpa Keterangan'
                                            ];
                                        @endphp
                                        <span class="font-medium text-slate-700 dark:text-slate-300">
                                            {{ $labels[$enrollmentStatuses[$personId]] ?? $enrollmentStatuses[$personId] }}
                                        </span>
                                    @endcan
                                </td>

                                <!-- Presence Status -->
                                <td class="p-4">
                                    @if ($isInactive)
                                        <span class="text-slate-400 italic">N/A (Non-aktif)</span>
                                    @else
                                        @can('change-presence-status')
                                            <select wire:model.live="presenceStatuses.{{ $personId }}" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-[11px] focus:border-emerald-500 focus:ring-emerald-500 w-full">
                                                <option value="mukim">Mukim</option>
                                                <option value="laju">Laju</option>
                                                <option value="izin">Izin</option>
                                                <option value="alpa">Alpa</option>
                                            </select>
                                        @else
                                            @php
                                                $pLabels = [
                                                    'mukim' => 'Mukim', 'laju' => 'Laju', 'izin' => 'Izin', 'alpa' => 'Alpa'
                                                ];
                                            @endphp
                                            <span class="font-medium text-slate-700 dark:text-slate-300">
                                                {{ $pLabels[$presenceStatuses[$personId]] ?? $presenceStatuses[$personId] }}
                                            </span>
                                        @endcan
                                    @endif
                                </td>

                                <!-- Dynamic fields input -->
                                @foreach ($cd->campaign->template->fields as $field)
                                    <td class="p-4">
                                        @if ($isInactive)
                                            <span class="text-slate-400 italic">N/A</span>
                                        @else
                                            @php
                                                $key = $field->field_key;
                                                $type = $field->field_type;
                                                $options = $field->field_options ?: [];
                                            @endphp

                                            <!-- Render input depending on field type -->
                                            @if ($type === 'text')
                                                <input type="text" wire:model.blur="responses.{{ $personId }}.{{ $key }}" placeholder="{{ $field->placeholder_text ?: 'Isi...' }}" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                            @elseif ($type === 'number')
                                                <input type="number" wire:model.blur="responses.{{ $personId }}.{{ $key }}" placeholder="0" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                            @elseif ($type === 'date')
                                                <input type="date" wire:model.blur="responses.{{ $personId }}.{{ $key }}" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-855 dark:text-slate-200 px-2 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                            @elseif ($type === 'textarea')
                                                <textarea wire:model.blur="responses.{{ $personId }}.{{ $key }}" rows="1" placeholder="{{ $field->placeholder_text ?: 'Catatan...' }}" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                                            @elseif ($type === 'dropdown')
                                                <select wire:model.blur="responses.{{ $personId }}.{{ $key }}" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-2 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                                    <option value="">-- Pilih --</option>
                                                    @foreach ($options as $opt)
                                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif ($type === 'boolean')
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                    <input type="checkbox" wire:model="responses.{{ $personId }}.{{ $key }}" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                                    <span class="text-xs text-slate-400">Ya</span>
                                                </label>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 5 + $cd->campaign->template->fields->count() }}" class="p-6 text-center text-slate-400">Tidak ada santri di asrama ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
