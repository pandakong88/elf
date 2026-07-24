<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Perizinan Keluar Santri</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pengajuan izin, persetujuan bertingkat, logging keluar-masuk, dan riwayat perjalanan santri.</p>
        </div>
        <button type="button" wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Ajukan Izin Baru</span>
        </button>
    </div>

    <!-- Alert Messages -->
    <!-- Tabs Layout -->
    <div class="border-b border-slate-200 dark:border-slate-800">
        <nav class="flex gap-6 -mb-px">
            <button type="button" wire:click="$set('activeTab', 'persetujuan')" 
                    class="py-4 px-1 border-b-2 text-sm font-bold transition-all {{ $activeTab === 'persetujuan' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 hover:border-slate-300' }}">
                Persetujuan Izin
            </button>
            <button type="button" wire:click="$set('activeTab', 'keluar')" 
                    class="py-4 px-1 border-b-2 text-sm font-bold transition-all {{ $activeTab === 'keluar' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 hover:border-slate-300' }}">
                Sedang Keluar
            </button>
            <button type="button" wire:click="$set('activeTab', 'riwayat')" 
                    class="py-4 px-1 border-b-2 text-sm font-bold transition-all {{ $activeTab === 'riwayat' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 hover:border-slate-300' }}">
                Riwayat Izin
            </button>
        </nav>
    </div>

    <!-- Search Section -->
    <div class="flex bg-white dark:bg-slate-900 p-4 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
        <div class="relative flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama santri..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
            <div class="absolute left-3.5 top-3.5 text-slate-400 dark:text-slate-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <x-card bodyClass="p-0 overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="person_id">Nama Santri</x-table-header>
                    <x-table-header>Jenis & Alasan</x-table-header>
                    <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="start_date">Jadwal Keluar</x-table-header>
                    <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="end_date">Batas Kembali</x-table-header>
                    <x-table-header :sortField="$sortField" :sortDirection="$sortDirection" field="status">Status</x-table-header>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm text-slate-700 dark:text-slate-300">
                @forelse ($perizinans as $perizinan)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $perizinan->person->name }}</div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">NIK: {{ $perizinan->person->nik }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge type="info">{{ $perizinan->permissionType->name }}</x-badge>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs truncate" title="{{ $perizinan->reason }}">{{ $perizinan->reason }}</p>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                            {{ $perizinan->start_date->isoFormat('D MMMM YYYY, HH:mm') }}
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                            {{ $perizinan->end_date->isoFormat('D MMMM YYYY, HH:mm') }}
                            @if ($perizinan->actual_return_date)
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 font-normal">Kembali: {{ $perizinan->actual_return_date->isoFormat('D MMMM YYYY, HH:mm') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="$perizinan->status">
                                {{ match($perizinan->status) {
                                    'pending' => 'Pending Approval',
                                    'approved' => 'Disetujui',
                                    'out' => 'Sedang Keluar',
                                    'returned' => 'Kembali Tepat Waktu',
                                    'late' => 'Terlambat Kembali',
                                    'rejected' => 'Ditolak',
                                    'cancelled' => 'Dibatalkan',
                                    default => $perizinan->status
                                } }}
                            </x-badge>
                            
                            <!-- Workflow Step Progress Indicator -->
                            @if($perizinan->status === 'pending' && $perizinan->workflowInstance)
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 mt-1">Langkah: <span class="font-bold">{{ $perizinan->workflowInstance->current_step }}</span></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-y-1">
                            @if ($activeTab === 'persetujuan')
                                @if ($perizinan->status === 'pending')
                                    <!-- Musyrif or Pengasuh actions -->
                                    <button type="button" wire:click="approveLeave('{{ $perizinan->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                        Setujui
                                    </button>
                                    <button type="button" wire:click="openRejectModal('{{ $perizinan->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 dark:bg-rose-950/20 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-bold border border-rose-100 dark:border-rose-900/50 transition-colors">
                                        Tolak
                                    </button>
                                @elseif ($perizinan->status === 'approved')
                                    <!-- Checkout -->
                                    <button type="button" wire:click="checkoutLeave('{{ $perizinan->id }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 dark:bg-slate-100 dark:hover:bg-slate-200 dark:active:bg-slate-300 dark:text-slate-900 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                        Checkout (Keluar)
                                    </button>
                                @endif
                            @elseif ($activeTab === 'keluar')
                                <button type="button" wire:click="checkinLeave('{{ $perizinan->id }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm">
                                    Checkin (Kembali)
                                </button>
                            @else
                                <span class="text-xs text-slate-400 dark:text-slate-500 italic">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500">
                            Tidak ada data perizinan santri ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $perizinans->links() }}
    </div>

    <!-- Create Application Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Ajukan Izin Keluar Baru</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Isi formulir lengkap pengajuan izin keluar santri.</p>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitLeave" class="p-6 space-y-4">
                    <!-- Search & Select Santri -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pilih Santri</label>
                        @if ($selectedSantriId)
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-xl flex items-center justify-between">
                                <div>
                                    <span class="block text-xs text-emerald-500 dark:text-emerald-400 font-bold uppercase tracking-wider">Santri Terpilih</span>
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
                                        <button type="button" wire:click="selectSantriForLeave('{{ $santri->id }}', '{{ $santri->name }}')"
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

                        <!-- Leave Type -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Jenis Izin</label>
                            <select wire:model.live="selectedPermissionTypeId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                                <option value="">Pilih Jenis</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedPermissionTypeId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Start Date -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Waktu Keluar</label>
                            <input type="datetime-local" wire:model.live="startDate" 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            @error('startDate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Batas Kembali</label>
                            <input type="datetime-local" wire:model.live="endDate" 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            @error('endDate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Workflow Template -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Workflow Approval</label>
                        <select wire:model.live="selectedWorkflowTemplateId" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm">
                            <option value="">Pilih Alur Approval</option>
                            @foreach ($workflowTemplates as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedWorkflowTemplateId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alasan Izin</label>
                        <textarea wire:model.live="reason" rows="3" placeholder="Sebutkan alasan atau keperluan izin santri..." 
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm"></textarea>
                        @error('reason') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-emerald-500/10">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Rejection Notes Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Tolak Pengajuan Izin</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Sebutkan alasan mengapa perizinan santri ditolak.</p>
                    </div>
                    <button type="button" wire:click="closeRejectModal" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 dark:text-slate-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="rejectLeave" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alasan Penolakan</label>
                        <textarea wire:model.live="rejectReason" rows="3" placeholder="Sebutkan alasan penolakan..." 
                                  class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm"></textarea>
                        @error('rejectReason') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="closeRejectModal" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-rose-500/10">
                            Tolak Izin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
