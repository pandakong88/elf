<div class="py-6 px-4 sm:px-6 lg:px-8">
    <!-- Success & Error Alert -->
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Sensus Fleksibel</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Dasbor utama pengelolaan kampanye sensus terdistribusi di asrama.</p>
        </div>
        @can('manage-sensus-v3')
            <a href="{{ route('sensus.campaigns.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-2xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-500/20 hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Mulai Kampanye Sensus</span>
            </a>
        @endcan
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center gap-4 mb-8">
        <!-- Search Input -->
        <div class="flex-1 w-full relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau deskripsi kampanye..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 pl-10 pr-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
        </div>
        
        <!-- Status Tabs -->
        <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-950 p-1 rounded-2xl w-full sm:w-auto">
            <button type="button" wire:click="$set('statusFilter', 'active')" class="flex-1 sm:flex-initial py-2 px-4 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $statusFilter === 'active' ? 'bg-white dark:bg-slate-900 text-slate-950 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
                Sensus Aktif
            </button>
            <button type="button" wire:click="$set('statusFilter', 'draft')" class="flex-1 sm:flex-initial py-2 px-4 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $statusFilter === 'draft' ? 'bg-white dark:bg-slate-900 text-slate-950 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
                Draft
            </button>
            <button type="button" wire:click="$set('statusFilter', 'closed')" class="flex-1 sm:flex-initial py-2 px-4 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $statusFilter === 'closed' ? 'bg-white dark:bg-slate-900 text-slate-950 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300' }}">
                Selesai
            </button>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="space-y-8">
        @forelse ($campaigns as $camp)
            @php
                $progress = $camp->getOverallProgress();
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight">{{ $camp->name }}</h2>
                            @php
                                $campColor = $camp->getStatusColor();
                                $campBadgeClass = 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-350';
                                if ($campColor === 'blue') {
                                    $campBadgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
                                } elseif ($campColor === 'yellow') {
                                    $campBadgeClass = 'bg-amber-100 text-amber-850 dark:bg-amber-950 dark:text-amber-300';
                                } elseif ($campColor === 'purple') {
                                    $campBadgeClass = 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300';
                                } elseif ($campColor === 'green') {
                                    $campBadgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                                }
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $campBadgeClass }}">
                                {{ $camp->getStatusLabel() }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-400">
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Template: <strong>{{ $camp->template->name }}</strong></span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Periode: <strong>{{ $camp->getMonthName() }} {{ $camp->year }}</strong></span>
                            <span class="flex items-center gap-1">⏱️ Alur: <strong>{{ $camp->getWorkflowLabel() }}</strong></span>
                        </div>
                        @if ($camp->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 pt-1">{{ $camp->description }}</p>
                        @endif
                    </div>

                    <!-- Overall Progress bar -->
                    <div class="w-full md:w-64 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Penyelesaian Asrama</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $progress['done'] }} / {{ $progress['total'] }} ({{ $progress['percentage'] }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                        </div>

                        <!-- Top-level Campaign actions -->
                        <div class="flex items-center justify-end gap-2 pt-2">
                            @if ($camp->status === 'draft')
                                @can('manage-sensus-v3')
                                    <button type="button" wire:click="publishCampaign('{{ $camp->id }}')" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                                        Terbitkan Sensus
                                    </button>
                                    <button type="button" onclick="confirm('Apakah Anda yakin ingin menghapus draft kampanye ini?') || event.stopImmediatePropagation()" wire:click="deleteCampaign('{{ $camp->id }}')" class="px-3 py-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-550/10 rounded-xl text-xs transition-all">
                                        Hapus
                                    </button>
                                @endcan
                            @elseif ($camp->status === 'collecting')
                                @can('manage-sensus-v3')
                                    <button type="button" wire:click="closeForReview('{{ $camp->id }}')" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-indigo-600/10 hover:shadow-indigo-500/20 transition-all">
                                        Hentikan &amp; Review
                                    </button>
                                @endcan
                            @elseif ($camp->status === 'review')
                                @can('manage-sensus-v3')
                                    <button type="button" onclick="confirm('Apakah Anda yakin ingin menyelesaikan kampanye sensus ini? Data profil santri yang disinkronkan akan diperbarui secara permanen.') || event.stopImmediatePropagation()" wire:click="finalizeCampaign('{{ $camp->id }}')" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs shadow-md shadow-emerald-600/10 hover:shadow-emerald-500/20 transition-all">
                                        Finalisasi &amp; Selesai
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dormitories List (Target) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-800">
                                <th class="p-4 sm:px-6">Nama Asrama</th>
                                <th class="p-4">Penanggung Jawab</th>
                                <th class="p-4">Penyelesaian Isian</th>
                                <th class="p-4">Status Laporan</th>
                                <th class="p-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                            @forelse ($camp->dormitories as $cd)
                                <tr>
                                    <!-- Dorm Name -->
                                    <td class="p-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">{{ $cd->dormitory->gender === 'L' ? 'Putra' : 'Putri' }}</span>
                                            <div>
                                                <span class="block font-bold text-slate-800 dark:text-white">{{ $cd->dormitory->name }}</span>
                                                <span class="block text-[10px] text-slate-400">{{ $cd->dormitory->gender === 'L' ? 'Komplek Putra' : 'Komplek Putri' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Assigned User -->
                                    <td class="p-4">
                                        @if ($cd->assignedUser)
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $cd->assignedUser->name }}</span>
                                        @else
                                            <span class="text-slate-400 italic">Belum ditugaskan</span>
                                        @endif
                                    </td>

                                    <!-- Filled Progress -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-3 min-w-[8rem]">
                                            <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $cd->getProgressPercentage() }}%"></div>
                                            </div>
                                            <span class="font-bold text-slate-800 dark:text-white whitespace-nowrap">{{ $cd->progress_filled }} / {{ $cd->progress_total }}</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="p-4">
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $dormBadgeClass }}">
                                            {{ $cd->getStatusLabel() }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 sm:px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Web input form button -->
                                            @if ($camp->status === 'collecting' && $camp->allow_direct_input)
                                                @php
                                                    $isAssigned = $cd->assigned_to === auth()->id() || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('manajemen');
                                                @endphp
                                                @if ($isAssigned)
                                                    <a href="{{ route('sensus.input', ['campaign' => $camp->id, 'dormitory' => $cd->dormitory_id]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-lg hover:shadow-sm transition-all" title="Isi Lembar Sensus">
                                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Isi Sensus
                                                    </a>
                                                @endif
                                            @endif

                                            <!-- Review / Approval page for admin -->
                                            @if ($cd->status === 'submitted')
                                                @can('approve-census-v3')
                                                    <a href="{{ route('sensus.review', ['campaign' => $camp->id, 'dormitory' => $cd->dormitory_id]) }}" class="px-3 py-1.5 bg-indigo-650 hover:bg-indigo-600 text-white font-bold rounded-lg shadow-sm hover:shadow transition-all" title="Review Hasil Sensus">
                                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Review Laporan
                                                    </a>
                                                @endcan
                                            @endif

                                            <!-- Read-only view for closed or approved -->
                                            @if ($cd->status === 'approved' || $camp->status === 'closed')
                                                <a href="{{ route('sensus.review', ['campaign' => $camp->id, 'dormitory' => $cd->dormitory_id]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-bold rounded-lg transition-all" title="Lihat Laporan">
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Lihat Hasil
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">Tidak ada target komplek asrama untuk sensus ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="py-16 text-center bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl">
                <div class="text-slate-300 dark:text-slate-700 mb-4 flex justify-center"><svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg></div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-300">Tidak ada kampanye sensus</h3>
                <p class="text-slate-400 mt-1 max-w-sm mx-auto text-sm">Tidak ada sensus dengan kriteria status ini. Klik "Mulai Kampanye Sensus" untuk membuat baru.</p>
            </div>
        @endforelse

        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
