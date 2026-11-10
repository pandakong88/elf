<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs text-slate-400 mb-4">
        <a href="{{ route('sensus.campaigns') }}" class="hover:text-slate-600 dark:hover:text-slate-200">Sensus</a>
        <span>/</span>
        <span>{{ $campaign->name }}</span>
        <span>/</span>
        <span class="text-slate-800 dark:text-slate-200 font-semibold">Review: {{ $dormitory->name }}</span>
    </div>

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    🏢 Verifikasi Fasilitas &amp; Lemari
                </span>
                <span class="text-xs font-bold px-2 py-0.5 rounded {{ $dormitory->gender === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' }}">
                    {{ $dormitory->gender === 'L' ? 'Putra' : 'Putri' }}
                </span>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $cd->getStatusBadgeClass() }}">
                    {{ $cd->getStatusLabel() }}
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $dormitory->name }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Periksa laporan kondisi fasilitas dan inventaris lemari santri sebelum disetujui secara permanen.</p>
        </div>

        <!-- Approval / Reject Actions -->
        @if ($cd->status === 'submitted')
            <div class="flex items-center gap-2">
                <button type="button" wire:click="$set('showRejectModal', true)" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-2xl text-xs transition-all border border-rose-200 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-300">
                    ❌ Kembalikan Draf
                </button>
                <button type="button" onclick="confirm('Setujui laporan sensus fasilitas ini? Data kondisi dan lemari akan disinkronkan ke sistem.') || event.stopImmediatePropagation()" wire:click="approveAndApply" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl text-xs transition-all shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20">
                    ✅ Setujui &amp; Terapkan
                </button>
            </div>
        @else
            <div class="text-xs text-slate-400 italic">
                Laporan berstatus <strong>{{ $cd->getStatusLabel() }}</strong>.
            </div>
        @endif
    </div>

    <!-- Summary Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Dicek</span>
            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $stats['total'] }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-950/60 shadow-sm bg-emerald-50/20">
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">🟢 Baik</span>
            <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ $stats['baik'] }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-amber-100 dark:border-amber-950/60 shadow-sm bg-amber-50/20">
            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">🟡 Rusak Ringan</span>
            <span class="text-2xl font-black text-amber-700 dark:text-amber-300">{{ $stats['rusak_ringan'] }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-rose-100 dark:border-rose-950/60 shadow-sm bg-rose-50/20">
            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider block">🔴 Rusak Berat</span>
            <span class="text-2xl font-black text-rose-700 dark:text-rose-300">{{ $stats['rusak_berat'] }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-purple-100 dark:border-purple-950/60 shadow-sm bg-purple-50/20 col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider block">🟣 Hilang</span>
            <span class="text-2xl font-black text-purple-700 dark:text-purple-300">{{ $stats['hilang'] }}</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
        <!-- Filter Condition Tabs -->
        <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-950 p-1 rounded-2xl w-full md:w-auto overflow-x-auto">
            <button type="button" wire:click="$set('filterCondition', 'all')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterCondition === 'all' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Semua Kondisi
            </button>
            <button type="button" wire:click="$set('filterCondition', 'damaged')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterCondition === 'damaged' ? 'bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                ⚠️ Perlu Perbaikan / Hilang ({{ $stats['rusak_ringan'] + $stats['rusak_berat'] + $stats['hilang'] }})
            </button>
            <button type="button" wire:click="$set('filterCondition', 'baik')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterCondition === 'baik' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Kondisi Baik
            </button>
        </div>

        <!-- Filter Item Type Tabs -->
        <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-950 p-1 rounded-2xl w-full md:w-auto overflow-x-auto">
            <button type="button" wire:click="$set('filterType', 'all')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'all' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Semua Tipe
            </button>
            <button type="button" wire:click="$set('filterType', 'komplek')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'komplek' ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Komplek
            </button>
            <button type="button" wire:click="$set('filterType', 'kamar')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'kamar' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Kamar
            </button>
            <button type="button" wire:click="$set('filterType', 'lemari')" class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $filterType === 'lemari' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Lemari
            </button>
        </div>
    </div>

    <!-- Review Items Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-800">
                        <th class="p-4 sm:px-6">Tipe</th>
                        <th class="p-4">Nama Sarana / Item</th>
                        <th class="p-4">Lokasi Kamar</th>
                        <th class="p-4">Santri Pemilik</th>
                        <th class="p-4">Kondisi Fisik</th>
                        <th class="p-4 sm:px-6">Catatan Kerusakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse ($items as $item)
                        <tr>
                            <!-- Tipe -->
                            <td class="p-4 sm:px-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $item->item_type === 'komplek' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : ($item->item_type === 'kamar' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300') }}">
                                    {{ $item->item_type_label }}
                                </span>
                            </td>

                            <!-- Nama Item -->
                            <td class="p-4 font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                {{ $item->item_name }}
                            </td>

                            <!-- Lokasi Kamar -->
                            <td class="p-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $item->room ? $item->room->name : 'Area Bersama Komplek' }}
                            </td>

                            <!-- Pemilik -->
                            <td class="p-4 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                @if ($item->assignedPerson)
                                    <div>
                                        <span class="font-bold block">{{ $item->assignedPerson->name }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $item->assignedPerson->santriProfile?->nis ?? '-' }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <!-- Kondisi -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $item->condition_badge_class }}">
                                    {{ $item->condition_label }}
                                </span>
                            </td>

                            <!-- Catatan -->
                            <td class="p-4 sm:px-6 text-slate-500 dark:text-slate-400">
                                {{ $item->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Tidak ada sarana prasarana yang sesuai kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reject Modal -->
    @if ($showRejectModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 border border-slate-100 dark:border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center font-bold">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white">Kembalikan Laporan Sensus</h3>
                        <p class="text-xs text-slate-400">Beri alasan penolakan agar pengisi dapat memperbaiki data.</p>
                    </div>
                </div>

                <div>
                    <textarea wire:model="rejectionNotes" rows="4" placeholder="Tuliskan catatan perbaikan (contoh: Lemari L-03 belum diisi pemiliknya, cek ulang kran air)..." class="w-full text-xs rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3 text-slate-800 dark:text-slate-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-500"></textarea>
                    @error('rejectionNotes') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="reject" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-rose-600/10">
                        Kirim Penolakan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
