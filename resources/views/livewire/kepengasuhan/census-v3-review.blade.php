<div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ openReject: @entangle('showRejectModal') }">
    @if (!$cd)
        <div class="p-6 text-center">
            <p class="text-rose-500 font-bold">Data tidak ditemukan.</p>
        </div>
    @else
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('sensus.campaigns') }}" class="hover:underline">Sensus Fleksibel</a>
                        <span>&rsaquo;</span>
                        <span>Review &amp; Persetujuan</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Review: {{ $cd->campaign->name }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Laporan Asrama: <strong class="text-slate-700 dark:text-slate-300">{{ $cd->dormitory->name }}</strong> (Diserahkan oleh {{ $cd->assignedUser->name ?? 'Sistem' }})</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('sensus.campaigns') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Success & Error Alert -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Table & Responses Grid -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Response Table Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Isi Laporan Sensus</h3>
                        <span class="text-xs text-slate-400">Total {{ $responses->count() }} Santri</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-800">
                                    <th class="p-4 sm:px-6 w-12 text-center">No</th>
                                    <th class="p-4">Kamar</th>
                                    <th class="p-4">Nama Santri</th>
                                    <th class="p-4">Status Keberadaan</th>
                                    <!-- Dynamic fields headers from template -->
                                    @foreach ($cd->campaign->template->fields as $field)
                                        <th class="p-4 min-w-[7rem]">{{ $field->field_label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @forelse ($responses as $idx => $res)
                                    @php
                                        $role = $res->person->activeRoles->firstWhere('role_type', 'santri');
                                        $isInactive = $role ? ($role->enrollment_status !== 'aktif') : false;
                                        $isAlpa = $role ? ($role->presence_status === 'alpa') : false;
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
                                                {{ $res->room->name ?? '-' }}
                                            </span>
                                        </td>

                                        <!-- Santri Info -->
                                        <td class="p-4">
                                            <span class="font-bold text-slate-800 dark:text-white">{{ $res->person->name }}</span>
                                        </td>

                                        <!-- Presence -->
                                        <td class="p-4">
                                            @if ($isInactive)
                                                <span class="text-slate-400 italic">Non-aktif</span>
                                            @else
                                                @php
                                                    $pStatus = $role ? $role->presence_status : '-';
                                                    $color = 'slate';
                                                    $label = '-';
                                                    if ($role) {
                                                        $label = $role->presence_status_label;
                                                        if ($pStatus === 'mukim') $color = 'emerald';
                                                        elseif ($pStatus === 'laju') $color = 'indigo';
                                                        elseif ($pStatus === 'izin') $color = 'blue';
                                                        elseif ($pStatus === 'alpa') $color = 'rose';
                                                    }
                                                    $badgeClass = 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-350';
                                                    if ($color === 'emerald') {
                                                        $badgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                                                    } elseif ($color === 'indigo') {
                                                        $badgeClass = 'bg-indigo-100 text-indigo-850 dark:bg-indigo-950 dark:text-indigo-300';
                                                    } elseif ($color === 'blue') {
                                                        $badgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
                                                    } elseif ($color === 'rose') {
                                                        $badgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badgeClass }}">
                                                    {{ $label }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Dynamic fields output -->
                                        @foreach ($cd->campaign->template->fields as $field)
                                            <td class="p-4 text-slate-700 dark:text-slate-300">
                                                @php
                                                    $val = $res->getValue($field->field_key);
                                                @endphp
                                                @if ($field->field_type === 'boolean')
                                                    <span class="text-xs font-bold">{{ $val ? 'Ya' : 'Tidak' }}</span>
                                                @else
                                                    {{ $val ?: '-' }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 4 + $cd->campaign->template->fields->count() }}" class="p-6 text-center text-slate-400">Tidak ada respon yang terisi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Actions, Profile Sync & Sync Logs -->
            <div class="space-y-6">
                <!-- Action Panel Card -->
                @if ($cd->status === 'submitted')
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Persetujuan Laporan</h3>
                        <p class="text-xs text-slate-400">Tinjau laporan sensus ini dan sinkronkan data baru ke database profil utama.</p>
                        
                        <div class="flex flex-col gap-2 pt-2">
                            <button type="button" wire:click="approve" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-500/20 hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Setujui &amp; Sinkronisasi
                            </button>
                            <button type="button" @click="openReject = true" class="w-full py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-rose-500 font-bold rounded-2xl transition-all">
                                <svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Tolak &amp; Kembalikan
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm text-center py-8">
                        <div class="text-slate-400 mb-2 flex justify-center"><svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <h4 class="font-bold text-slate-800 dark:text-white">Laporan Sensus Selesai</h4>
                        <p class="text-xs text-slate-400 mt-1">Laporan ini sudah disetujui atau ditutup.</p>
                        @php
                            $dormColor = $cd->getStatusColor();
                            $dormBadgeClass = 'bg-slate-100 text-slate-850 dark:bg-slate-800 dark:text-slate-300';
                            if ($dormColor === 'yellow') {
                                $dormBadgeClass = 'bg-amber-100 text-amber-850 dark:bg-amber-950 dark:text-amber-300';
                            } elseif ($dormColor === 'blue') {
                                $dormBadgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
                            } elseif ($dormColor === 'green') {
                                $dormBadgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                            } elseif ($dormColor === 'red') {
                                $dormBadgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
                            }
                        @endphp
                        <span class="inline-flex mt-3 px-3 py-1 rounded-full text-xs font-bold {{ $dormBadgeClass }}">
                            {{ $cd->getStatusLabel() }}
                        </span>
                    </div>
                @endif

                <!-- Profile Changes Sync Preview Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Sinkronisasi Profil ({{ $profileChanges->count() }})</h3>
                    <p class="text-xs text-slate-400 mb-4">Perubahan pada field sistem akan disinkronkan ke tabel utama profil santri jika disetujui.</p>

                    <div class="space-y-4 max-h-[24rem] overflow-y-auto pr-1">
                        @forelse ($profileChanges as $change)
                            <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100 dark:border-slate-800/80 space-y-2">
                                <span class="block font-bold text-xs text-slate-800 dark:text-white">{{ $change->person->name }}</span>
                                <div class="space-y-1">
                                    @foreach ($change->profile_change_preview as $key => $preview)
                                        <div class="text-[10px] flex items-center justify-between gap-1 flex-wrap">
                                            <span class="text-slate-400">{{ $preview['label'] }}</span>
                                            <div class="flex items-center gap-1">
                                                <span class="px-1.5 py-0.5 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded line-through">{{ $preview['old'] ?: '-' }}</span>
                                                <span class="text-slate-400">&rarr;</span>
                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-850 dark:bg-emerald-950 dark:text-emerald-300 rounded font-bold">{{ $preview['new'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">Tidak ada perubahan profil utama terdeteksi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejection Notes Modal -->
        <div x-show="openReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden" @click.away="openReject = false">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Catatan Penolakan</h3>
                    <button type="button" @click="openReject = false" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-650 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-400">Tulis alasan penolakan agar musyrif asrama tahu bagian mana saja yang perlu diperbaiki / diisi kembali.</p>
                    
                    <div>
                        <label for="reject_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-450 mb-1">Catatan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea id="reject_notes" wire:model="rejectionNotes" rows="4" placeholder="Contoh: Tolong data hafalan juz santri Ahmad Fauzi dikoreksi kembali, sepertinya keliru..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        @error('rejectionNotes') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="openReject = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="reject" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs shadow-md shadow-rose-600/10 hover:shadow-rose-500/20 transition-all">
                        Tolak Laporan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
