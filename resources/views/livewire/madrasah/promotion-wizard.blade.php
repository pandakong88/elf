<div class="space-y-6">
    {{-- Header Page --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-3">
                <span>Wizard Kenaikan &amp; Kelulusan Kelas Massal</span>
                @if($isGenderLocked)
                    <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-full border border-emerald-300/30">
                        Scope: {{ $genderFilter === 'L' ? 'Putra (L)' : 'Putri (P)' }}
                    </span>
                @endif
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Modul otomatisasi memproses kenaikan kelas, santri tinggal kelas, dan kelulusan madrasah serentak antar Tahun Ajaran.
            </p>
        </div>

        {{-- Top Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="openLogModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-xl shadow-md transition-all text-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Lihat Riwayat &amp; Log Kenaikan Kelas</span>
            </button>

            @if($lastPromotionBatch)
                <button type="button" wire:click="openUndoConfirmModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white font-extrabold rounded-xl shadow-md transition-all text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <span>Batalkan (Undo) Kenaikan Kelas Terakhir</span>
                </button>
            @endif

            <button type="button" wire:click="loadPromotionData"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow transition-all text-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Muat Ulang Pemetaan Data</span>
            </button>
        </div>
    </div>

    {{-- Undo Banner Notification (If last execution batch exists) --}}
    @if($lastPromotionBatch)
        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="text-xs">
                    <div class="font-extrabold text-amber-900 dark:text-amber-200">
                        Kenaikan Kelas Massal Baru Saja Di-eksekusi pada {{ $lastPromotionBatch['executed_at'] }}
                    </div>
                    <div class="text-amber-700 dark:text-amber-400 mt-0.5">
                        Memproses {{ $lastPromotionBatch['total_students'] }} santri dari TA {{ $lastPromotionBatch['from_academic_year'] }} ke TA {{ $lastPromotionBatch['to_academic_year'] }}. Salah input data? Anda bisa membatalkannya sekarang.
                    </div>
                </div>
            </div>

            <button type="button" wire:click="openUndoConfirmModal"
                class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-xs rounded-xl shadow transition-all">
                Batalkan (Undo) Eksekusi Ini
            </button>
        </div>
    @endif

    {{-- Academic Year Config Bar --}}
    <div class="bg-white dark:bg-slate-900 p-5 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Ajaran Asal (Lama)</label>
                <input type="text" wire:model="fromAcademicYear" placeholder="Contoh: 2025/2026"
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-100">
            </div>

            <div class="flex items-center justify-center pt-4 md:pt-0">
                <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Ajaran Tujuan (Baru)</label>
                <input type="text" wire:model="toAcademicYear" placeholder="Contoh: 2026/2027"
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-100">
            </div>
        </div>
    </div>

    {{-- Live Summary Statistics Widget --}}
    @if(!empty($promotionMap))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Card 1: Total Santri --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-bold block">Total Santri Diproses</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-slate-100 font-mono">{{ $this->summaryStats['total'] }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">di {{ $this->summaryStats['classes'] }} Kelas Madrasah</span>
                </div>
            </div>

            {{-- Card 2: Akan Naik Kelas --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold block">Akan Naik Kelas</span>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ $this->summaryStats['promoted'] }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Santri Lanjut Kelas</span>
                </div>
            </div>

            {{-- Card 3: Akan Tinggal Kelas --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-rose-600 dark:text-rose-400 font-bold block">Akan Tinggal Kelas</span>
                    <span class="text-2xl font-black text-rose-600 dark:text-rose-400 font-mono">{{ $this->summaryStats['retained'] }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Santri Mengulang</span>
                </div>
            </div>

            {{-- Card 4: Akan Lulus --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 flex items-center justify-center font-bold flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <span class="text-xs text-purple-600 dark:text-purple-400 font-bold block">Akan Lulus Madrasah</span>
                    <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono">{{ $this->summaryStats['graduated'] }}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Santri Alumni Madrasah</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Class Promotion Mapping Grid --}}
    <div class="space-y-6">
        @forelse($promotionMap as $classId => $classData)
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                {{-- Class Header & Target Selection --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 text-xs font-extrabold uppercase rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ $classData['jenjang'] }}
                        </span>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">{{ $classData['class_name'] }}</h3>
                            <span class="text-xs text-slate-400">Jumlah Santri: {{ count($classData['students']) }} Santri</span>
                        </div>
                    </div>

                    {{-- Target Class Selector --}}
                    <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <span class="text-xs font-bold text-slate-500">Target Kenaikan:</span>
                        <select wire:change="updateTargetClass('{{ $classId }}', $event.target.value)"
                            class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 focus:outline-none">
                            <option value="lulus" {{ $classData['target_class_id'] === 'lulus' ? 'selected' : '' }}>[ LULUS MADRASAH ]</option>
                            @foreach($allClasses as $optClass)
                                <option value="{{ $optClass->id }}" {{ $classData['target_class_id'] === $optClass->id ? 'selected' : '' }}>
                                    {{ strtoupper($optClass->jenjang) }} — {{ $optClass->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Quick Batch Actions per Class --}}
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="setAllStudentsStatusInClass('{{ $classId }}', 'promoted')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-bold rounded-lg transition-colors">
                            Semua Naik
                        </button>
                        <button type="button" wire:click="setAllStudentsStatusInClass('{{ $classId }}', 'retained')" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 font-bold rounded-lg transition-colors">
                            Semua Tinggal Kelas
                        </button>
                    </div>
                </div>

                {{-- Student Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                <th class="py-2.5 px-3 w-10 text-center">No</th>
                                <th class="py-2.5 px-3">Nama Santri</th>
                                <th class="py-2.5 px-3">NIS</th>
                                <th class="py-2.5 px-3 text-center">Gender</th>
                                <th class="py-2.5 px-3 text-center w-64">Status Kenaikan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($classData['students'] as $sIdx => $st)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                    <td class="py-2.5 px-3 text-center font-semibold text-slate-400">{{ $sIdx + 1 }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $st['name'] }}</td>
                                    <td class="py-2.5 px-3 font-mono text-slate-500">{{ $st['nis'] }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded {{ $st['gender'] === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                                            {{ $st['gender'] === 'L' ? 'Putra' : 'Putri' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="inline-flex p-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                            <button type="button" wire:click="toggleStudentStatus('{{ $classId }}', '{{ $st['person_id'] }}', 'promoted')"
                                                class="px-2.5 py-1 rounded-md font-bold text-[10px] transition-all {{ $st['status'] === 'promoted' ? 'bg-emerald-600 text-white shadow' : 'text-slate-500 hover:text-slate-800' }}">
                                                Naik Kelas
                                            </button>
                                            <button type="button" wire:click="toggleStudentStatus('{{ $classId }}', '{{ $st['person_id'] }}', 'retained')"
                                                class="px-2.5 py-1 rounded-md font-bold text-[10px] transition-all {{ $st['status'] === 'retained' ? 'bg-rose-600 text-white shadow' : 'text-slate-500 hover:text-slate-800' }}">
                                                Tinggal Kelas
                                            </button>
                                            <button type="button" wire:click="toggleStudentStatus('{{ $classId }}', '{{ $st['person_id'] }}', 'graduated')"
                                                class="px-2.5 py-1 rounded-md font-bold text-[10px] transition-all {{ $st['status'] === 'graduated' ? 'bg-indigo-600 text-white shadow' : 'text-slate-500 hover:text-slate-800' }}">
                                                Lulus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="p-12 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl text-center text-slate-400 text-sm">
                Belum ada data pendaftaran kelas aktif untuk Tahun Ajaran {{ $fromAcademicYear }}.
            </div>
        @endforelse
    </div>

    {{-- Floating Execution Bottom Bar --}}
    @if(!empty($promotionMap))
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-6">
            <div>
                <div class="font-extrabold text-sm text-white">Eksekusi Kenaikan Kelas Serentak</div>
                <div class="text-xs text-slate-400">Memproses seluruh santri dari TA {{ $fromAcademicYear }} ke TA {{ $toAcademicYear }}</div>
            </div>

            <button type="button" wire:click="requestMassPromotionConfirm"
                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Eksekusi Kenaikan &amp; Kelulusan Massal</span>
            </button>
        </div>
    @endif

    {{-- MODAL KONFIRMASI UNDO / ROLLBACK --}}
    @if($showUndoConfirmModal && $lastPromotionBatch)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>

                <div>
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Batalkan (Undo) Kenaikan Kelas Massal?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Tindakan ini akan mengembalikan pendaftaran kelas <strong>{{ $lastPromotionBatch['total_students'] }} santri</strong> dari TA {{ $lastPromotionBatch['to_academic_year'] }} kembali ke kelas awal mereka di TA {{ $lastPromotionBatch['from_academic_year'] }}.
                    </p>
                </div>

                <div class="bg-rose-50 dark:bg-rose-950/40 p-3 rounded-xl border border-rose-200/50 dark:border-rose-800/40 text-[11px] text-rose-800 dark:text-rose-300 font-semibold">
                    Waktu Eksekusi Terakhir: {{ $lastPromotionBatch['executed_at'] }}
                </div>

                <div class="flex items-center justify-end gap-2 pt-3">
                    <button type="button" wire:click="$set('showUndoConfirmModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="executeUndoMassPromotion" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow">Ya, Batalkan &amp; Revert Data</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL KONFIRMASI KOSTUM ELEGAN (NO BROWSER ALERT) --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">{{ $confirmTitle }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfirmasi Eksekusi Massal</p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                    {{ $confirmMessage }}
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showConfirmModal', false)"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="processConfirmedAction"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-lg transition-all">
                        {{ $confirmButtonText }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL PUSAT LOG RIWAYAT KENAIKAN & KELULUSAN KELAS --}}
    @if($showLogModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-5xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pusat Log Riwayat Kenaikan &amp; Kelulusan Kelas</h3>
                            <p class="text-xs text-slate-400">Catatan sejarah keputusan promosi, pengulangan kelas, dan alumni madrasah</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeLogModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Filter Bar & Search --}}
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    {{-- Filter Tabs --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs">
                        <button type="button" wire:click="$set('logFilter', 'all')"
                            class="px-3 py-1.5 font-bold rounded-lg transition-all {{ $logFilter === 'all' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            Semua Status
                        </button>
                        <button type="button" wire:click="$set('logFilter', 'promoted')"
                            class="px-3 py-1.5 font-bold rounded-lg transition-all {{ $logFilter === 'promoted' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            Naik Kelas
                        </button>
                        <button type="button" wire:click="$set('logFilter', 'retained')"
                            class="px-3 py-1.5 font-bold rounded-lg transition-all {{ $logFilter === 'retained' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            Tinggal Kelas
                        </button>
                        <button type="button" wire:click="$set('logFilter', 'graduated')"
                            class="px-3 py-1.5 font-bold rounded-lg transition-all {{ $logFilter === 'graduated' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            Lulus Madrasah
                        </button>
                        <button type="button" wire:click="$set('logFilter', 'batches')"
                            class="px-3 py-1.5 font-bold rounded-lg transition-all {{ $logFilter === 'batches' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                            Riwayat Batch
                        </button>
                    </div>

                    {{-- Search Input --}}
                    @if($logFilter !== 'batches')
                        <div class="relative w-full md:w-64">
                            <input type="text" wire:model.live.debounce.300ms="logSearch" placeholder="Cari nama / NIS / kelas..."
                                class="w-full pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Table Content --}}
                <div class="flex-1 overflow-y-auto min-h-[300px] border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                    @if($logFilter === 'batches')
                        {{-- Batch Execution History Table --}}
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] sticky top-0 border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="p-3">Waktu Eksekusi</th>
                                    <th class="p-3">Tahun Ajaran</th>
                                    <th class="p-3 text-center">Total Santri</th>
                                    <th class="p-3 text-center">Naik / Tinggal / Lulus</th>
                                    <th class="p-3">Eksekutor</th>
                                    <th class="p-3 text-center">Status Batch</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                @forelse($this->batchHistory as $batch)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                        <td class="p-3 font-mono text-slate-700 dark:text-slate-300">{{ $batch['executed_at'] }}</td>
                                        <td class="p-3 font-bold text-slate-800 dark:text-slate-100">{{ $batch['from_academic_year'] }} &rarr; {{ $batch['to_academic_year'] }}</td>
                                        <td class="p-3 text-center font-bold font-mono">{{ $batch['total_students'] }}</td>
                                        <td class="p-3 text-center">
                                            <span class="text-emerald-600 font-bold">{{ $batch['total_promoted'] ?? 0 }} Naik</span> •
                                            <span class="text-rose-600 font-bold">{{ $batch['total_retained'] ?? 0 }} Ulang</span> •
                                            <span class="text-purple-600 font-bold">{{ $batch['total_graduated'] ?? 0 }} Lulus</span>
                                        </td>
                                        <td class="p-3 text-slate-600 dark:text-slate-300">{{ $batch['executed_by'] ?? 'Admin' }}</td>
                                        <td class="p-3 text-center">
                                            @if(($batch['status'] ?? 'sukses') === 'sukses')
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">SUKSES</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">DI-UNDO</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat eksekusi batch kenaikan kelas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        {{-- Detailed Student Log Table --}}
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] sticky top-0 border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="p-3">Santri &amp; NIS</th>
                                    <th class="p-3">Kelas &amp; Jenjang</th>
                                    <th class="p-3">Tahun Ajaran</th>
                                    <th class="p-3 text-center">Status Akademik</th>
                                    <th class="p-3">Keterangan Log</th>
                                    <th class="p-3">Tanggal Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                @forelse($this->promotionLogs as $log)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                        <td class="p-3">
                                            <div class="font-extrabold text-slate-900 dark:text-slate-100">{{ $log['person_name'] }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">NIS: {{ $log['nis'] }} ({{ $log['gender'] }})</div>
                                        </td>
                                        <td class="p-3">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $log['kelas_name'] }}</span>
                                            <span class="text-[10px] text-slate-400 block font-semibold">{{ $log['jenjang'] }}</span>
                                        </td>
                                        <td class="p-3 font-mono font-semibold text-slate-600 dark:text-slate-300">{{ $log['academic_year'] }}</td>
                                        <td class="p-3 text-center">
                                            @if($log['status'] === 'promoted')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                    <span>NAIK KELAS</span>
                                                </span>
                                            @elseif($log['status'] === 'retained')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span>TINGGAL KELAS</span>
                                                </span>
                                            @elseif($log['status'] === 'graduated')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                                    <span>LULUS MADRASAH</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-slate-600 dark:text-slate-300 text-xs">{{ $log['detail'] }}</td>
                                        <td class="p-3 font-mono text-[11px] text-slate-400">{{ $log['date'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data log yang sesuai dengan filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="closeLogModal" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs">Tutup Log</button>
                </div>
            </div>
        </div>
    @endif
</div>
