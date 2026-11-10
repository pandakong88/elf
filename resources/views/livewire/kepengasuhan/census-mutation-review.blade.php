<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('sensus.campaigns') }}" class="hover:underline">Sensus</a>
                <span>&rsaquo;</span>
                <span>Review Sensus Santri</span>
                <span>&rsaquo;</span>
                <span class="text-slate-800 dark:text-slate-200">{{ $cd?->dormitory?->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Review Mutasi &mdash; {{ $cd?->dormitory?->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Periksa daftar usulan mutasi kamar dan status santri sebelum disetujui & diterapkan ke database aktif.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('sensus.campaigns') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-xs transition-all">
                Kembali
            </a>
            <button type="button" wire:click="$set('showRejectModal', true)" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 font-semibold rounded-xl text-xs transition-all">
                Kembalikan Laporan
            </button>
            <button type="button" wire:click="approveAndApply" wire:confirm="Seluruh mutasi kamar dan status santri yang valid akan langsung diterapkan ke sistem. Lanjutkan?" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-lg shadow-emerald-600/20 hover:-translate-y-0.5 transition-all">
                Setujui & Terapkan ({{ $stats['pindah'] + $stats['status'] }})
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Pindah Kamar</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['pindah'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 flex items-center justify-center font-bold">
                &rarr;
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Ubah Status</span>
                <div class="text-2xl font-black text-amber-500 mt-1">{{ $stats['status'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 flex items-center justify-center font-bold">
                &#9998;
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Tidak Ditemukan</span>
                <div class="text-2xl font-black text-slate-600 dark:text-slate-300 mt-1">{{ $stats['hilang'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center font-bold">
                ?
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500">Gagal / Konflik</span>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['failed'] }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 flex items-center justify-center font-bold">
                !
            </div>
        </div>
    </div>

    <!-- Review Diff Table (Sebelum -> Sesudah) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Rincian Perubahan Santri</h3>
                <p class="text-xs text-slate-500">Hanya santri dengan perubahan (pindah, ubah status, atau hilang) yang perlu verifikasi.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 text-slate-400 uppercase font-semibold text-[10px] tracking-wider">
                        <th class="py-3 px-4">Nama Santri</th>
                        <th class="py-3 px-4">Tindakan</th>
                        <th class="py-3 px-4">Sebelum</th>
                        <th class="py-3 px-4">Sesudah</th>
                        <th class="py-3 px-4">Kesiapan / Status</th>
                        <th class="py-3 px-4">Catatan Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse ($entries->where('action', '!=', 'tetap') as $entry)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-100 text-sm">
                                    {{ $entry->person?->name }}
                                </div>
                                <span class="text-slate-400 text-[11px]">NIS: {{ $entry->person?->santriProfile?->nis ?? '-' }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($entry->action === 'pindah_kamar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                        Pindah Kamar
                                    </span>
                                @elseif ($entry->action === 'ubah_status')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        Ubah Status
                                    </span>
                                @elseif ($entry->action === 'tidak_ditemukan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        Tidak Ditemukan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                @if ($entry->action === 'pindah_kamar')
                                    <span class="font-medium">{{ $entry->fromRoom?->name ?? 'Belum ada kamar' }}</span>
                                @elseif ($entry->action === 'ubah_status')
                                    <span class="capitalize">Aktif (Mukim)</span>
                                @else
                                    <span>{{ $entry->fromRoom?->name ?? '-' }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($entry->action === 'pindah_kamar')
                                    <div class="space-y-1">
                                        <select
                                            wire:change="updateTargetRoom('{{ $entry->id }}', $event.target.value)"
                                            class="text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 py-1.5 px-2.5 max-w-xs"
                                        >
                                            <option value="">-- Pilih Kamar --</option>
                                            @foreach ($targetRooms as $tr)
                                                <option value="{{ $tr['id'] }}" {{ $entry->to_room_id === $tr['id'] ? 'selected' : '' }}>
                                                    {{ $tr['name'] }} ({{ $tr['remaining'] }}/{{ $tr['capacity'] }}) {{ $tr['is_full'] ? '[PENUH]' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif ($entry->action === 'ubah_status')
                                    <span class="font-bold text-amber-600 dark:text-amber-400 capitalize">
                                        {{ str_replace('_', ' ', $entry->new_enrollment_status) }}
                                    </span>
                                @else
                                    <span class="text-rose-500 font-semibold">Tandai hilang</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($entry->apply_status === 'applied')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        Sudah Diterapkan
                                    </span>
                                @elseif ($entry->apply_status === 'failed')
                                    <div class="text-rose-600 dark:text-rose-400 text-xs font-semibold">
                                        <span>Gagal: {{ $entry->apply_error }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        Siap Diterapkan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-slate-500 italic max-w-xs truncate">
                                {{ $entry->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                Seluruh santri di asrama ini berstatus <strong>Tetap di Kamar</strong>. Tidak ada usulan mutasi yang perlu diubah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Reject / Kembalikan Laporan -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full border border-slate-100 dark:border-slate-800 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Kembalikan Laporan Sensus</h3>
                <p class="text-xs text-slate-500">Tuliskan catatan perbaikan agar petugas dapat mengoreksi data sebelum diajukan kembali.</p>

                <textarea
                    wire:model="rejectionNotes"
                    rows="3"
                    placeholder="Contoh: Tolong cek ulang santri Ahmad, kamar tujuan sudah penuh..."
                    class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3"
                ></textarea>
                @error('rejectionNotes') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Batal
                    </button>
                    <button type="button" wire:click="reject" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-xl">
                        Kembalikan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
