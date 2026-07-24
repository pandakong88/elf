<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg">Master Setup</span>
                <span class="text-xs text-slate-400">• Super Admin & Management</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">Setup Data Santri & Wali Massal</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Unggah berkas Excel untuk mendaftarkan ratusan santri, penempatan asrama, kelas madrasah, dan kontak orang tua sekaligus.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('system.santri.download-template') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh Template Excel</span>
            </a>
            <button type="button" 
                    wire:click="openImportModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span>Impor File Excel</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center font-bold text-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Santri Terdaftar</span>
                <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ number_format($santriCount, 0, ',', '.') }} Santri</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Santri Mukim (Berkamar)</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($mukimCount, 0, ',', '.') }} Mukim</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-500/10 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xl">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Santri Laju (Non-Mukim)</span>
                <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($lajuCount, 0, ',', '.') }} Laju</span>
            </div>
        </div>
    </div>

    <!-- Recent Registered Santri Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 dark:text-slate-100">Daftar Terakhir Santri Terdaftar</h3>
            <span class="text-xs text-slate-400 font-mono">Menampilkan 15 Santri Terbaru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">Nama Santri</th>
                        <th class="px-6 py-3.5 font-semibold">NIS</th>
                        <th class="px-6 py-3.5 font-semibold">Gender</th>
                        <th class="px-6 py-3.5 font-semibold">Status</th>
                        <th class="px-6 py-3.5 font-semibold">Orang Tua / No. WA</th>
                        <th class="px-6 py-3.5 font-semibold">Terdaftar Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentSantri as $s)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-100">
                                {{ $s->name }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600 dark:text-slate-400">
                                {{ $s->santriProfile?->additional_info['nis'] ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($s->gender === 'L')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-blue-500/10 text-blue-600 rounded-lg">L (Putra)</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-pink-500/10 text-pink-600 rounded-lg">P (Putri)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $pStatus = $s->activeRoles->first()?->presence_status ?? 'mukim';
                                @endphp
                                @if($pStatus === 'mukim')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 rounded-lg">Mukim</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-indigo-500/10 text-indigo-600 rounded-lg">Laju</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700 dark:text-slate-200">{{ $s->santriProfile?->father_name ?? $s->santriProfile?->mother_name ?? 'Wali' }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $s->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $s->created_at ? $s->created_at->diffForHumans() : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Belum ada data santri yang terdaftar. Klik <strong>Impor File Excel</strong> untuk melakukan setup.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recentSantri->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $recentSantri->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Import Excel Setup Santri -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">
                
                <!-- Modal Header -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Setup Massal — Impor Excel Santri & Wali</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Unggah file Excel hasil pengisian template untuk diperiksa dan diimpor ke database.</p>
                    </div>
                    <button type="button" wire:click="closeImportModal" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-200/50 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6 overflow-y-auto flex-1">

                    <!-- File Upload Input -->
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/40 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl text-center space-y-3">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-xl flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 dark:text-slate-200 text-sm cursor-pointer hover:text-emerald-600">
                                <span>Pilih Berkas Excel (.xlsx)</span>
                                <input type="file" wire:model="excelFile" class="hidden" accept=".xlsx,.xls">
                            </label>
                            <p class="text-xs text-slate-400 mt-1">Gunakan template yang telah diunduh dari tombol "Unduh Template Excel".</p>
                        </div>

                        <div wire:loading wire:target="excelFile" class="text-xs text-emerald-600 font-semibold animate-pulse">
                            Mengunggah berkas Excel...
                        </div>

                        @if($excelFile)
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 text-emerald-600 rounded-lg text-xs font-semibold mt-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $excelFile->getClientOriginalName() }}</span>
                            </div>
                            <div class="pt-2">
                                <button type="button" wire:click="processImport" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                                    Periksa & Tampilkan Pratinjau
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Pre-Import Preview Section -->
                    @if(!empty($tempValidSantri) || !empty($tempInvalidSantri))
                        <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-800 dark:text-slate-100">Hasil Pemeriksaan Berkas Excel</h4>
                                <div class="flex gap-2">
                                    <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 text-xs font-bold rounded-lg">{{ count($tempValidSantri) }} Valid (Siap Impor)</span>
                                    <span class="px-2.5 py-1 bg-rose-500/10 text-rose-600 text-xs font-bold rounded-lg">{{ count($tempInvalidSantri) }} Gagal (Format Salah)</span>
                                </div>
                            </div>

                            <!-- Invalid Table -->
                            @if(!empty($tempInvalidSantri))
                                <div class="p-4 bg-rose-500/5 border border-rose-500/20 rounded-xl space-y-2">
                                    <h5 class="text-xs font-bold text-rose-600 uppercase tracking-wider">Baris Yang Gagal / Perlu Diperbaiki:</h5>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        @foreach($tempInvalidSantri as $inv)
                                            <div class="p-2.5 bg-white dark:bg-slate-800 rounded-lg text-xs border border-rose-200 dark:border-rose-900/30 flex items-start justify-between">
                                                <div>
                                                    <span class="font-bold text-rose-600">Baris {{ $inv['row'] }}: {{ $inv['name'] }}</span>
                                                    <ul class="list-disc list-inside text-slate-500 mt-1 space-y-0.5">
                                                        @foreach($inv['reasons'] as $r)
                                                            <li>{{ $r }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Valid Table -->
                            @if(!empty($tempValidSantri))
                                <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                                    <div class="p-3 bg-emerald-500/5 border-b border-slate-200 dark:border-slate-800">
                                        <h5 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Pratinjau Data Santri Siap Diimpor ({{ count($tempValidSantri) }} Data):</h5>
                                    </div>
                                    <div class="max-h-60 overflow-y-auto">
                                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                                            <thead class="bg-slate-100 dark:bg-slate-800 font-bold">
                                                <tr>
                                                    <th class="p-2.5">Nama Santri</th>
                                                    <th class="p-2.5">NIS</th>
                                                    <th class="p-2.5">Gender</th>
                                                    <th class="p-2.5">Status</th>
                                                    <th class="p-2.5">Komplek / Kamar</th>
                                                    <th class="p-2.5">Kelas</th>
                                                    <th class="p-2.5">Wali / WA</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                                @foreach($tempValidSantri as $vs)
                                                    <tr>
                                                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-100">{{ $vs['name'] }}</td>
                                                        <td class="p-2.5 font-mono text-slate-500">{{ $vs['nis'] ?: '(Auto)' }}</td>
                                                        <td class="p-2.5 font-bold {{ $vs['gender'] === 'L' ? 'text-blue-600' : 'text-pink-600' }}">{{ $vs['gender'] }}</td>
                                                        <td class="p-2.5 font-semibold capitalize">{{ $vs['presence_status'] }}</td>
                                                        <td class="p-2.5">{{ $vs['dorm_name'] ? $vs['dorm_name'] . ' (' . $vs['room_name'] . ')' : '-' }}</td>
                                                        <td class="p-2.5">{{ $vs['kelas_name'] ?: '-' }}</td>
                                                        <td class="p-2.5">{{ $vs['parent_name'] }} ({{ $vs['parent_phone'] }})</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <button type="button" wire:click="closeImportModal" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-xl transition-all">
                        Batal
                    </button>

                    @if(!empty($tempValidSantri))
                        <button type="button" 
                                wire:click="confirmAndSaveImport"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Impor & Simpan {{ count($tempValidSantri) }} Santri Sekarang</span>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
