<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Buku Pelanggaran Santri</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pencatatan pelanggaran aturan kepesantrenan, akumulasi poin tata tertib, dan pelacakan sanksi/resolusi.</p>
        </div>
        <button type="button" wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-rose-500/10 hover:shadow-rose-500/20 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Pelanggaran</span>
        </button>
    </div>

    <!-- Alert Messages -->
    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Side: Top Violators Summary -->
        <div class="lg:col-span-4 space-y-6">
            <x-card title="Poin Kumulatif Terbanyak" subtitle="Santri dengan akumulasi poin pelanggaran aktif">
                <div class="space-y-4">
                    @forelse ($topViolators as $violator)
                        @if ($violator->unresolved_points > 0)
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-xl">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ $violator->name }}</h4>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider">NIK: {{ $violator->nik }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-extrabold text-xs {{ $violator->unresolved_points >= 50 ? 'bg-rose-100 text-rose-700' : ($violator->unresolved_points >= 25 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300') }}">
                                        {{ $violator->unresolved_points }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-6 text-slate-400 dark:text-slate-500 text-xs italic">Semua santri bersih dari poin pelanggaran aktif.</div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Right Side: Violations Table -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Filter Section -->
            <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-slate-900 p-4 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama santri..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                </div>
                <div class="w-full sm:w-44">
                    <select wire:model.live="severityFilter" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                        <option value="">Semua Tingkat</option>
                        <option value="ringan">Ringan</option>
                        <option value="sedang">Sedang</option>
                        <option value="berat">Berat</option>
                    </select>
                </div>
            </div>

            <!-- Violations Table -->
            <x-card bodyClass="p-0 overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="person_id">Nama Santri</x-table-header>
                            <x-table-header>Pelanggaran & Deskripsi</x-table-header>
                            <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="severity">Tingkat</x-table-header>
                            <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="points">Poin</x-table-header>
                            <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="violation_date">Tanggal</x-table-header>
                            <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="status">Status</x-table-header>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm text-slate-700 dark:text-slate-300">
                        @forelse ($violations as $violation)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ $violation->person->name }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">NIK: {{ $violation->person->nik }}</div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $violation->violationType->name }}</div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate" title="{{ $violation->description }}">{{ $violation->description }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :type="$violation->severity === 'berat' ? 'danger' : ($violation->severity === 'sedang' ? 'warning' : 'neutral')">
                                        {{ ucfirst($violation->severity) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $violation->points }} Poin
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    {{ $violation->violation_date->isoFormat('D MMMM YYYY, HH:mm') }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :type="$violation->status === 'resolved' ? 'success' : 'danger'">
                                        {{ $violation->status === 'resolved' ? 'Diselesaikan' : 'Belum Selesai' }}
                                    </x-badge>
                                    @if ($violation->punishment)
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 max-w-xs truncate" title="Sanksi: {{ $violation->punishment }}">Sanksi: {{ $violation->punishment }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($violation->status !== 'resolved')
                                        <button type="button" wire:click="openResolveModal('{{ $violation->id }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 dark:bg-slate-100 dark:hover:bg-slate-200 dark:active:bg-slate-300 dark:text-slate-900 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                            Selesaikan
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500 italic">Resolved</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                                    Tidak ada data catatan pelanggaran ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>

            <div class="mt-4">
                {{ $violations->links() }}
            </div>
        </div>
    </div>

    <!-- Report Violation Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Catat Pelanggaran Santri</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Formulir pelaporan kejadian pelanggaran santri.</p>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitViolation" class="p-6 space-y-4">
                    <!-- Search & Select Santri -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pilih Santri</label>
                        @if ($selectedSantriId)
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-xl flex items-center justify-between">
                                <div>
                                    <span class="block text-xs text-emerald-500 dark:text-emerald-400 font-bold uppercase tracking-wider">Santri Melanggar</span>
                                    <h4 class="font-bold text-sm text-emerald-800 dark:text-emerald-300">{{ $selectedSantriName }}</h4>
                                </div>
                                <button type="button" wire:click="$set('selectedSantriId', null)" class="text-xs text-rose-600 font-bold hover:underline">Ganti</button>
                            </div>
                        @else
                            <input type="text" wire:model.live.debounce.300ms="searchSantri" placeholder="Cari nama santri..." 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            
                            @if ($searchSantri)
                                <div class="mt-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800 shadow-sm max-h-40 overflow-y-auto">
                                    @forelse ($modalSantriList as $santri)
                                        <button type="button" wire:click="selectSantriForViolation('{{ $santri->id }}', '{{ $santri->name }}')"
                                                class="w-full p-2.5 text-left text-sm hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between transition-colors text-slate-700 dark:text-slate-300">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $santri->name }}</span>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Pilih</span>
                                        </button>
                                    @empty
                                        <div class="p-3 text-center text-xs text-slate-400 dark:text-slate-500 italic">Santri tidak ditemukan.</div>
                                    @endforelse
                                </div>
                            @endif
                        @endif
                        @error('selectedSantriId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Organization -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Organisasi</label>
                            <select wire:model.live="selectedOrgId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                                <option value="">Pilih Organisasi</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedOrgId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Violation Type -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Jenis Pelanggaran</label>
                            <select wire:model.live="selectedViolationTypeId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                                <option value="">Pilih Jenis</option>
                                @foreach ($violationTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedViolationTypeId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <!-- Severity -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tingkat</label>
                            <select wire:model.live="severity" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                                <option value="ringan">Ringan</option>
                                <option value="sedang">Sedang</option>
                                <option value="berat">Berat</option>
                            </select>
                            @error('severity') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Points -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Bobot Poin</label>
                            <input type="number" wire:model.live="points" min="0"
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            @error('points') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Waktu Kejadian</label>
                            <input type="datetime-local" wire:model.live="violationDate" 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            @error('violationDate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Punishment -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Rencana Tindakan Disiplin (Sanksi Awal)</label>
                        <input type="text" wire:model.live="punishment" placeholder="Contoh: Membersihkan halaman asrama"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Keterangan Kejadian</label>
                        <textarea wire:model.live="description" rows="3" placeholder="Sebutkan detail kronologi kejadian..." 
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm"></textarea>
                        @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-rose-500/10">
                            Catat Pelanggaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Resolve Violation Modal -->
    @if($showResolveModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Selesaikan Pelanggaran</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Konfirmasi penerapan tindakan disiplin / sanksi kepada santri.</p>
                    </div>
                    <button type="button" wire:click="closeResolveModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitResolve" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tindakan Sanksi Yang Telah Diberikan</label>
                        <textarea wire:model.live="punishmentApplied" rows="3" placeholder="Sebutkan sanksi yang telah diselesaikan santri..." 
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm"></textarea>
                        @error('punishmentApplied') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="closeResolveModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-emerald-500/10">
                            Selesaikan Pelanggaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
