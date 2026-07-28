<div class="min-h-screen bg-slate-50 dark:bg-slate-950 p-4 md:p-6">

    {{-- Flash Messages --}}
    @if(session('majek_success') || $flashSuccess)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
             class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-2">
            ✅ {{ session('majek_success') ?: $flashSuccess }}
        </div>
    @endif
    @if($flashError)
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
             class="mb-4 px-4 py-3 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 rounded-2xl text-xs font-bold flex items-center gap-2">
            ⚠️ {{ $flashError }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- HEADER: Navigasi Bulan                                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs mb-5 overflow-hidden">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            {{-- Kiri: Judul --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 flex items-center justify-center text-xl shrink-0">
                    🍽️
                </div>
                <div>
                    <h1 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wide">Majek (Katering)</h1>
                    <p class="text-[10px] text-slate-400 font-semibold">Pendaftaran & Setoran Pembayaran Katering</p>
                </div>
            </div>

            {{-- Tengah: Navigasi Bulan --}}
            <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded-2xl px-2 py-1.5">
                <button type="button" wire:click="decrementMonth"
                    class="w-7 h-7 rounded-xl bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm flex items-center justify-center shadow-xs transition-all">
                    ◀
                </button>
                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wide px-3 min-w-32 text-center">
                    {{ $this->monthLabel }}
                </span>
                <button type="button" wire:click="incrementMonth"
                    class="w-7 h-7 rounded-xl bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm flex items-center justify-center shadow-xs transition-all">
                    ▶
                </button>
            </div>

            {{-- Kanan: Status Periode --}}
            @if($this->activePeriod)
                <div class="flex items-center gap-3 text-[10px]">
                    <div class="text-center">
                        <div class="text-slate-400 font-bold uppercase tracking-wider">Hari Aktif</div>
                        <div class="font-black text-slate-800 dark:text-slate-100 text-sm">{{ $this->activePeriod->active_days }} hari</div>
                    </div>
                    <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="text-center">
                        <div class="text-slate-400 font-bold uppercase tracking-wider">👦 Putra (1x / 2x)</div>
                        <div class="font-black text-amber-600 text-xs">Rp {{ number_format($this->tarif1x, 0, ',', '.') }} / {{ number_format($this->tarif2x, 0, ',', '.') }}</div>
                    </div>
                    <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="text-center">
                        <div class="text-slate-400 font-bold uppercase tracking-wider">👧 Putri (1x / 2x)</div>
                        <div class="font-black text-purple-600 dark:text-purple-400 text-xs">Rp {{ number_format($this->tarif1xPutri, 0, ',', '.') }} / {{ number_format($this->tarif2xPutri, 0, ',', '.') }}</div>
                    </div>
                    <button type="button" wire:click="openPeriodModal"
                        class="ml-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-[10px] font-bold transition-all">
                        ⚙️ Edit
                    </button>
                </div>
            @else
                <button type="button" wire:click="openPeriodModal"
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                    ⚙️ Buat Konfigurasi Periode
                </button>
            @endif
        </div>

        {{-- Notes Periode --}}
        @if($this->activePeriod?->notes)
            <div class="px-6 py-2.5 bg-amber-50 dark:bg-amber-950/20 border-t border-amber-200/60 dark:border-amber-800/30 text-[10px] text-amber-700 dark:text-amber-400 font-semibold flex items-center gap-2">
                📝 {{ $this->activePeriod->notes }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT                                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 rounded-3xl shadow-xs overflow-hidden">

        {{-- Sub-header: Statistik + Tombol --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/20 dark:bg-slate-950/5">
            <div class="flex items-center gap-5 text-[10px]">
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block">Total Peserta</span>
                    <span class="font-black text-slate-800 dark:text-slate-100 text-base">{{ $this->overallStats['total'] }}</span>
                </div>
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block">Lunas</span>
                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-base">{{ $this->overallStats['paid'] }}</span>
                </div>
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block">Sebagian (Cicilan)</span>
                    <span class="font-black text-amber-600 dark:text-amber-400 text-base">{{ $this->overallStats['partial'] }}</span>
                </div>
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block">Belum Bayar</span>
                    <span class="font-black text-rose-500 dark:text-rose-400 text-base">{{ $this->overallStats['unpaid'] }}</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" wire:click="openCopyPeriodModal"
                    class="px-3.5 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                    📋 Salin Peserta dari Bulan Lain
                </button>
                <button type="button" wire:click="openAddModal"
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                    + Tambah Peserta
                </button>
            </div>
        </div>

        {{-- Search and Filter Bar --}}
        <div class="px-6 py-3.5 bg-slate-50/50 dark:bg-slate-950/10 border-b border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row gap-3 items-center">
            {{-- Search input --}}
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none text-xs">🔍</span>
                <input type="text" wire:model.live.debounce.300ms="searchParticipant" placeholder="Cari nama, NIK, atau NIS..."
                    class="w-full pl-9 pr-4 py-2.5 text-xs border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-850 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 outline-none transition-all">
            </div>

            {{-- Status Filter --}}
            <div class="w-full sm:w-40 shrink-0">
                <select wire:model.live="filterStatus"
                    class="w-full px-3 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                    <option value="all">💳 Semua Status</option>
                    <option value="paid">🟢 Lunas</option>
                    <option value="unpaid">🔴 Belum Bayar</option>
                    <option value="partial">🟡 Sebagian</option>
                </select>
            </div>

            {{-- Dormitory Multiselect --}}
            <div x-data="{ open: false }" class="relative w-full sm:w-60 shrink-0" @click.away="open = false">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-bold border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-amber-500/20 transition-all text-left">
                    <span class="truncate flex items-center gap-1.5">
                        <span>🏢</span>
                        <span>
                            @if(empty($filterDormitoryIds))
                                Semua Komplek
                            @else
                                {{ count($filterDormitoryIds) }} Komplek Terpilih
                            @endif
                        </span>
                    </span>
                    <span class="text-[9px] text-slate-400">▼</span>
                </button>
                
                <div x-show="open" x-transition
                    class="absolute right-0 mt-1.5 w-full bg-white dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-2xl shadow-lg z-30 max-h-60 overflow-y-auto p-2.5 space-y-1">
                    @foreach($this->dormitories as $dorm)
                        <label class="flex items-center gap-2 px-2.5 py-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-xl cursor-pointer select-none">
                            <input type="checkbox" value="{{ $dorm->id }}" wire:model.live="filterDormitoryIds"
                                class="rounded text-amber-500 focus:ring-amber-500 border-slate-300 dark:border-slate-700">
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-semibold">{{ $dorm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Reset Filters Button --}}
            @if($searchParticipant !== '' || !empty($filterDormitoryIds) || $filterStatus !== 'all')
                <button type="button" wire:click="resetFilters"
                    class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shrink-0 border border-slate-200/40 dark:border-slate-700/60 shadow-xs">
                    🧹 Reset
                </button>
            @endif
        </div>

        {{-- Tabel Peserta --}}
        @if($this->registrations->isEmpty())
            <div class="py-20 text-center">
                <div class="text-4xl mb-3">🍽️</div>
                @if($searchParticipant || !empty($filterDormitoryIds) || $filterStatus !== 'all')
                    <div class="text-slate-400 font-bold text-sm">Tidak ada peserta yang cocok dengan filter</div>
                    <p class="text-slate-400 text-xs mt-1">Coba sesuaikan kata kunci pencarian atau filter Anda.</p>
                @else
                    <div class="text-slate-400 font-bold text-sm">Belum ada peserta terdaftar</div>
                    <p class="text-slate-400 text-xs mt-1">
                        @if(!$this->activePeriod)
                            Buat konfigurasi periode terlebih dahulu, lalu tambahkan peserta.
                        @else
                            Klik tombol "+ Tambah Peserta" untuk mendaftarkan santri.
                        @endif
                    </p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 sticky top-0 left-0 bg-slate-50 dark:bg-slate-950 z-20 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">No</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Nama Santri</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Komplek</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Sesi</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-right text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Sisa Tagihan</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">Status</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-emerald-600 font-extrabold uppercase tracking-wider text-[9px]">Bayar?</th>
                            <th class="py-3 px-4 sticky top-0 bg-slate-50 dark:bg-slate-950 z-10 text-center text-slate-400 font-extrabold uppercase tracking-wider text-[9px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($this->registrations as $i => $reg)
                            @php
                                $detail   = $this->paidDetails[$reg->id] ?? ['status' => 'unpaid', 'paid' => 0, 'remaining' => (float)$reg->amount_pagi + (float)$reg->amount_sore];
                                $status   = $detail['status'];
                                $paidAmt  = $detail['paid'];
                                $remAmt   = $detail['remaining'];
                                $total    = (float)$reg->amount_pagi + (float)$reg->amount_sore;
                                $dormName = $reg->person?->roomAssignments?->first()?->room?->dormitory?->name ?? '—';
                                $customDays = $reg->active_days ?? ($this->activePeriod ? $this->activePeriod->active_days : 30);
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors {{ $status === 'paid' ? 'opacity-60' : '' }}">
                                <td class="py-3 px-4 text-slate-400 text-[10px] sticky left-0 bg-white dark:bg-slate-900 shadow-xs">{{ ($this->registrations->firstItem() ?? 1) + $i }}</td>
                                <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                    <div>{{ $reg->person->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold flex items-center gap-1.5 mt-0.5">
                                        <span>📅 {{ $customDays }} hari aktif</span>
                                        @if($reg->notes)
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <span class="text-slate-500 truncate max-w-44" title="{{ $reg->notes }}">📝 {{ $reg->notes }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $dormName }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($reg->session_pagi && $reg->session_sore)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-extrabold uppercase">🍽️🍽️ 2x</span>
                                    @elseif($reg->session_pagi)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[9px] font-extrabold uppercase">🌅 Pagi</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[9px] font-extrabold uppercase">🌆 Sore</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono">
                                    @if($status === 'partial')
                                        <div class="font-extrabold text-rose-600 dark:text-rose-400 text-xs">
                                            Rp {{ number_format($remAmt, 0, ',', '.') }} <span class="text-[9px] font-sans font-semibold text-rose-500">(sisa)</span>
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-sans">
                                            Total {{ number_format($total/1000, 0) }}k · Masuk {{ number_format($paidAmt/1000, 0) }}k
                                        </div>
                                    @elseif($status === 'paid')
                                        <div class="font-bold text-emerald-600 dark:text-emerald-400 text-xs">
                                            Rp 0 <span class="text-[9px] font-sans font-semibold text-emerald-500">(Lunas)</span>
                                        </div>
                                        <div class="text-[9px] text-slate-400 font-sans">
                                            Total Rp {{ number_format($total, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($status === 'paid')
                                        <span class="px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-extrabold uppercase tracking-wide border border-emerald-500/20">✓ Lunas</span>
                                    @elseif($status === 'partial')
                                        <span class="px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-extrabold uppercase tracking-wide border border-amber-500/20">◑ Sebagian</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 text-[9px] font-extrabold uppercase tracking-wide">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($status !== 'paid')
                                        <input type="checkbox"
                                            wire:model.live="paymentChecks.{{ $reg->id }}"
                                            class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 cursor-pointer">
                                    @else
                                        <span class="text-emerald-500 font-bold text-sm">✓</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($status !== 'paid')
                                            <button type="button" wire:click="openEditModal('{{ $reg->id }}')"
                                                class="text-sky-500 hover:text-sky-600 transition-colors text-xs font-bold px-1.5 py-1 rounded-lg hover:bg-sky-50 dark:hover:bg-sky-950/30" title="Edit Detail">
                                                ✏️
                                            </button>
                                            <button type="button" wire:click="confirmRemovePeserta('{{ $reg->id }}', '{{ addslashes($reg->person->name) }}')"
                                                class="text-rose-400 hover:text-rose-600 transition-colors text-xs font-bold px-1.5 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30" title="Hapus">
                                                ✕
                                            </button>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-700 text-[10px] font-bold">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer for Participants Table -->
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-[11px] font-semibold text-slate-400">
                    Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $this->registrations->firstItem() ?? 0 }}</span> s.d. <span class="font-bold text-slate-700 dark:text-slate-300">{{ $this->registrations->lastItem() ?? 0 }}</span> dari <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $this->registrations->total() }}</span> peserta
                </div>
                <div>
                    {{ $this->registrations->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        @endif

        {{-- Footer: Total + Action --}}
        @if($this->registrations->isNotEmpty())
            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/20">
                <div class="flex items-center gap-6 text-xs">
                    <div>
                        <span class="text-slate-400 text-[9px] font-extrabold uppercase tracking-wider block">Dicentang</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $countChecked }} peserta</span>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[9px] font-extrabold uppercase tracking-wider block">Total Setoran</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-base">Rp {{ number_format($totalChecked, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Metode Bayar --}}
                    <select wire:model="payMethod"
                        class="text-xs border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl px-3 py-2 font-semibold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-400 outline-none">
                        <option value="cash">💵 Cash</option>
                        <option value="transfer">🏦 Transfer</option>
                    </select>
                    <button type="button" wire:click="confirmSetoran" @if($countChecked === 0) disabled @endif
                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md
                            @if($countChecked > 0) bg-emerald-600 hover:bg-emerald-700 text-white
                            @else bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed @endif">
                        Proses & Simpan Setoran ({{ $countChecked }})
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Konfigurasi Periode                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showPeriodModal)
        <div wire:key="period-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-lg overflow-hidden animate-zoom-in">
                
                {{-- Header --}}
                <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                            ⚙️
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Konfigurasi Periode Majaek</h3>
                            <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $this->monthLabel }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closePeriodModal" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold transition-all flex items-center justify-center">✕</button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                    
                    {{-- Section 1: Hari Aktif --}}
                    <div class="bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 p-4 rounded-2xl space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            📅 Jumlah Hari Aktif Makan
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <input type="number" wire:model.live.debounce.500ms="periodActiveDays" min="1" max="31"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-black focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 outline-none transition-all">
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-extrabold px-2">Hari / Bulan</span>
                        </div>
                        @error('periodActiveDays') <p class="text-rose-500 text-[10px] font-bold mt-1">⚠️ {{ $message }}</p> @enderror
                        <p class="text-[10px] text-slate-400 font-medium">Contoh: 25 hari jika terdapat 6 hari libur/kegiatan khusus.</p>
                    </div>

                    {{-- Section 2: Tarif Harian per Gender --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        
                        {{-- Tarif Putra --}}
                        <div class="bg-amber-500/5 border border-amber-500/15 p-4 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>👦</span> Tarif Putra / Hari
                                </span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">1 Sesi</span>
                            </div>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs font-bold text-amber-600 dark:text-amber-400">Rp</span>
                                <input type="number" step="0.01" wire:model.live.debounce.500ms="periodTarifPerHari" min="1"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-amber-200 dark:border-amber-800/50 bg-white dark:bg-slate-900 text-amber-900 dark:text-amber-100 text-xs font-black font-mono focus:ring-2 focus:ring-amber-500/30 outline-none">
                            </div>
                            @error('periodTarifPerHari') <p class="text-rose-500 text-[10px] font-bold">⚠️ {{ $message }}</p> @enderror
                            <p class="text-[9px] text-slate-400 font-semibold">Rp 3.333,33 / hari = Rp 100rb (1x)</p>
                        </div>

                        {{-- Tarif Putri --}}
                        <div class="bg-purple-500/5 border border-purple-500/15 p-4 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold text-purple-700 dark:text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>👧</span> Tarif Putri / Hari
                                </span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300">1 Sesi</span>
                            </div>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs font-bold text-purple-600 dark:text-purple-400">Rp</span>
                                <input type="number" step="0.01" wire:model.live.debounce.500ms="periodTarifPerHariPutri" min="1"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/50 bg-white dark:bg-slate-900 text-purple-900 dark:text-purple-100 text-xs font-black font-mono focus:ring-2 focus:ring-purple-500/30 outline-none">
                            </div>
                            @error('periodTarifPerHariPutri') <p class="text-rose-500 text-[10px] font-bold">⚠️ {{ $message }}</p> @enderror
                            <p class="text-[9px] text-slate-400 font-semibold">Rp 3.000,00 / hari = Rp 90rb (1x)</p>
                        </div>
                    </div>

                    {{-- Section 3: Ringkasan Kalkulasi Total --}}
                    @if($periodActiveDays > 0 && $periodTarifPerHari > 0 && $periodTarifPerHariPutri > 0)
                        <div class="bg-slate-900 text-white dark:bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                    <span>📊</span> Estimasi Total 1 Bulan ({{ $periodActiveDays }} Hari)
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-xs">
                                {{-- Putra --}}
                                <div class="space-y-1">
                                    <div class="text-[10px] font-extrabold text-amber-400 uppercase tracking-wide">👦 Putra</div>
                                    <div class="flex justify-between text-[11px] text-slate-300">
                                        <span>1x Sesi:</span>
                                        <strong class="font-mono text-white">Rp {{ number_format($periodTarifPerHari * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-slate-300">
                                        <span>2x Sesi:</span>
                                        <strong class="font-mono font-bold text-amber-400">Rp {{ number_format($periodTarifPerHari * 2 * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                                {{-- Putri --}}
                                <div class="space-y-1 border-l border-slate-800 pl-4">
                                    <div class="text-[10px] font-extrabold text-purple-400 uppercase tracking-wide">👧 Putri</div>
                                    <div class="flex justify-between text-[11px] text-slate-300">
                                        <span>1x Sesi:</span>
                                        <strong class="font-mono text-white">Rp {{ number_format($periodTarifPerHariPutri * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-slate-300">
                                        <span>2x Sesi:</span>
                                        <strong class="font-mono font-bold text-purple-400">Rp {{ number_format($periodTarifPerHariPutri * 2 * $periodActiveDays, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Section 4: Catatan --}}
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">📝 Catatan Periode (Opsional)</label>
                        <textarea wire:model="periodNotes" rows="2"
                            placeholder="Tulis keterangan atau alasan pengubahan tarif/hari aktif..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 outline-none resize-none transition-all"></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <button type="button" wire:click="closePeriodModal"
                        class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="savePeriod"
                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2">
                        💾 Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Tambah Peserta (Tabbed & Configurable)                            --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showAddModal)
        <div wire:key="add-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-4xl overflow-hidden animate-zoom-in">
                
                {{-- Header --}}
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Pendaftaran Peserta Majek</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $this->monthLabel }}</p>
                    </div>
                    <button type="button" wire:click="closeAddModal" class="text-slate-400 hover:text-slate-600 transition-colors">✕</button>
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 px-6">
                    <button type="button" wire:click="switchTab('komplek')"
                        class="py-3 px-4 text-xs font-extrabold uppercase tracking-wider border-b-2 transition-all {{ $addTab === 'komplek' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                        🏢 Pendaftaran per Komplek (Bulk)
                    </button>
                    <button type="button" wire:click="switchTab('pencarian')"
                        class="py-3 px-4 text-xs font-extrabold uppercase tracking-wider border-b-2 transition-all {{ $addTab === 'pencarian' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                        🔍 Pencarian Nama (Single)
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[460px] overflow-y-auto">

                    {{-- ─── KERANJANG PENDAFTARAN (Daftar Santri Terpilih Sementara) ─── --}}
                    @php
                        $selectedList = $this->selectedStudentsList;
                    @endphp
                    @if(count($selectedList) > 0)
                        <div class="bg-amber-500/5 border border-amber-500/10 p-4 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-amber-700 dark:text-amber-400 font-extrabold uppercase tracking-wider">🛒 Keranjang Pendaftaran ({{ count($selectedList) }} Santri Terpilih)</span>
                                <span class="text-[9px] text-slate-400 font-semibold italic">Pindah komplek tidak menghapus pilihan ini</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto p-1 bg-white dark:bg-slate-950/40 rounded-xl border border-slate-100 dark:border-slate-800">
                                @foreach($selectedList as $selStd)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-[10px] font-bold">
                                        {{ $selStd['name'] }}
                                        <button type="button" wire:click="uncheckStudent('{{ $selStd['id'] }}')" class="hover:text-rose-600 text-amber-500 font-black text-xs">✕</button>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- ────────────────────────────────────────────────────────── --}}
                    {{-- TAB 1: Pendaftaran Komplek                                 --}}
                    {{-- ────────────────────────────────────────────────────────── --}}
                    @if($addTab === 'komplek')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Komplek / Dormitory</label>
                                <select wire:model.live="selectedDormitoryId"
                                    class="w-full max-w-xs px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-850 dark:text-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500/30">
                                    <option value="">-- Pilih Komplek --</option>
                                    @foreach($this->dormitories as $dorm)
                                        <option value="{{ $dorm->id }}">{{ $dorm->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($selectedDormitoryId)
                                @if(empty($dormitoryStudents))
                                    <p class="text-xs text-slate-400 italic py-4">Semua santri di komplek ini sudah terdaftar Majek bulan ini.</p>
                                @else
                                    <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead>
                                                <tr class="bg-slate-100 dark:bg-slate-950 text-[10px] text-slate-500 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                                                    <th class="py-3 px-4 text-center w-14">Pilih</th>
                                                    <th class="py-3 px-4">Nama Santri</th>
                                                    <th class="py-3 px-4 w-44">Sesi Makan</th>
                                                    <th class="py-3 px-4 w-28">Hari Aktif</th>
                                                    <th class="py-3 px-4">Catatan Khusus</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                                @foreach($dormitoryStudents as $std)
                                                    @php
                                                        $isSelected = $bulkSelections[$std['id']] ?? false;
                                                        $isRegistered = $std['is_registered'] ?? false;
                                                    @endphp
                                                    @if($isRegistered)
                                                        <tr class="transition-colors border-b border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40 text-slate-400 opacity-60">
                                                            <td class="py-3 px-4 text-center">
                                                                <span class="text-emerald-500 font-extrabold text-sm" title="Terdaftar">✓</span>
                                                            </td>
                                                            <td class="py-3 px-4 font-bold text-xs tracking-wide">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span>{{ $std['name'] }}</span>
                                                                    <span class="px-1.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[8px] font-black uppercase tracking-wider">✓ Terdaftar</span>
                                                                </div>
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <select disabled
                                                                    class="w-full px-3 py-1.5 text-xs font-bold bg-slate-100 dark:bg-slate-950 text-slate-400 border border-slate-200 dark:border-slate-800 rounded-xl outline-none opacity-60 cursor-not-allowed">
                                                                    <option>{{ $std['session'] === '2x' ? '🍽️🍽️ 2x Makan' : ($std['session'] === 'pagi' ? '🌅 Pagi Saja' : '🌆 Sore Saja') }}</option>
                                                                </select>
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <input type="text" disabled value="{{ $std['days'] }}"
                                                                    class="w-full px-3 py-1.5 text-xs font-mono font-black text-center bg-slate-100 dark:bg-slate-950 text-slate-400 border border-slate-200 dark:border-slate-800 rounded-xl outline-none opacity-60 cursor-not-allowed">
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <input type="text" disabled value="{{ $std['notes'] }}"
                                                                    class="w-full px-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-950 text-slate-400 border border-slate-200 dark:border-slate-800 rounded-xl outline-none opacity-60 cursor-not-allowed">
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr class="transition-colors border-b border-slate-200 dark:border-slate-800
                                                            {{ $isSelected 
                                                                ? 'bg-amber-500/10 dark:bg-amber-950/30 hover:bg-amber-500/15 dark:hover:bg-amber-950/40 text-amber-950 dark:text-amber-200' 
                                                                : 'bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-900 dark:text-slate-100' }}">
                                                            <td class="py-3 px-4 text-center">
                                                                <input type="checkbox" wire:model.live="bulkSelections.{{ $std['id'] }}"
                                                                    class="w-5 h-5 rounded text-amber-500 focus:ring-amber-500 border-slate-300 dark:border-slate-650 cursor-pointer">
                                                            </td>
                                                            <td class="py-3 px-4 font-black text-xs tracking-wide">
                                                                {{ $std['name'] }}
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <select wire:model="bulkSessions.{{ $std['id'] }}" @if(!$isSelected) disabled @endif
                                                                    class="w-full px-3 py-1.5 text-xs font-bold border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500/35 transition-all
                                                                        {{ $isSelected 
                                                                            ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border-amber-300 dark:border-amber-700' 
                                                                            : 'bg-slate-100 dark:bg-slate-950 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-60' }}">
                                                                    <option value="2x">🍽️🍽️ 2x Makan</option>
                                                                    <option value="pagi">🌅 Pagi Saja</option>
                                                                    <option value="sore">🌆 Sore Saja</option>
                                                                </select>
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <input type="number" min="1" max="31" wire:model="bulkDays.{{ $std['id'] }}" @if(!$isSelected) disabled @endif
                                                                    class="w-full px-3 py-1.5 text-xs font-mono font-black text-center border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500/35 transition-all
                                                                        {{ $isSelected 
                                                                            ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border-amber-300 dark:border-amber-700' 
                                                                            : 'bg-slate-100 dark:bg-slate-950 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-60' }}">
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <input type="text" placeholder="Catatan opsional..." wire:model="bulkNotes.{{ $std['id'] }}" @if(!$isSelected) disabled @endif
                                                                    class="w-full px-3 py-1.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-2 focus:ring-amber-500/35 transition-all
                                                                        {{ $isSelected 
                                                                            ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border-amber-300 dark:border-amber-700' 
                                                                            : 'bg-slate-100 dark:bg-slate-950 text-slate-400 dark:text-slate-600 cursor-not-allowed opacity-60' }}">
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    {{-- ────────────────────────────────────────────────────────── --}}
                    {{-- TAB 2: Pencarian Bebas                                     --}}
                    {{-- ────────────────────────────────────────────────────────── --}}
                    @if($addTab === 'pencarian')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Cari Santri</label>
                                <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                    placeholder="Ketik nama santri..."
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-850 dark:text-slate-200 text-sm focus:ring-2 focus:ring-amber-500/30 focus:border-amber-400 outline-none">

                                {{-- INLINE SEARCH RESULTS (Prevent overflow cutting off in modal) --}}
                                @if(count($searchResults) > 0)
                                    <div class="mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                                        @foreach($searchResults as $result)
                                            @if($result['is_registered'])
                                                <div class="w-full flex items-center justify-between px-4 py-3 opacity-55 cursor-not-allowed bg-slate-50/50 dark:bg-slate-900/50 text-left">
                                                    <span class="font-semibold text-slate-500 dark:text-slate-400 text-xs flex items-center gap-1.5">
                                                        {{ $result['name'] }}
                                                        <span class="px-1.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[8px] font-black uppercase tracking-wider">✓ Terdaftar</span>
                                                    </span>
                                                    <span class="text-[9px] text-slate-400 font-semibold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg">{{ $result['dormitory'] }}</span>
                                                </div>
                                            @else
                                                <div wire:click="selectPerson('{{ $result['id'] }}', '{{ addslashes($result['name']) }}')"
                                                    class="w-full flex items-center justify-between px-4 py-3 hover:bg-amber-500/10 dark:hover:bg-amber-500/20 cursor-pointer transition-colors text-left group">
                                                    <span class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-amber-900 dark:group-hover:text-amber-300 text-xs">{{ $result['name'] }}</span>
                                                    <span class="text-[10px] text-slate-400 font-semibold bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg">{{ $result['dormitory'] }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if($selectedPersonId)
                                <div class="p-4 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4 animate-zoom-in">
                                    <div class="text-xs">
                                        <span class="text-slate-400 font-bold uppercase tracking-wide block">Santri Terpilih</span>
                                        <strong class="text-slate-800 dark:text-white font-black text-sm block mt-0.5">🌟 {{ $selectedPersonName }}</strong>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Sesi Makan</label>
                                            <select wire:model="selectedSesi"
                                                class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/30 outline-none">
                                                <option value="2x">🍽️🍽️ 2x Makan</option>
                                                <option value="pagi">🌅 Pagi Saja</option>
                                                <option value="sore">🌆 Sore Saja</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Hari Aktif khusus</label>
                                            <input type="number" min="1" max="31" wire:model="selectedPersonDays"
                                                class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/30 outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Catatan Khusus (Opsional)</label>
                                        <input type="text" placeholder="Contoh: Masuk mulai pertengahan bulan..." wire:model="selectedPersonNotes"
                                            class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/30 outline-none">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($flashError)
                        <p class="text-rose-500 text-[10px] font-bold">⚠️ {{ $flashError }}</p>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <button type="button" wire:click="closeAddModal"
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    @if($addTab === 'komplek')
                        <button type="button" wire:click="addPesertaBulk" @if(count($selectedList) === 0) disabled @endif
                            class="px-5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm
                                {{ count($selectedList) > 0 ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed' }}">
                            💾 Daftarkan Terpilih ({{ count($selectedList) }})
                        </button>
                    @else
                        <button type="button" wire:click="addPeserta" @if(!$selectedPersonId) disabled @endif
                            class="px-5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm
                                {{ $selectedPersonId ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed' }}">
                            💾 Daftarkan Santri
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Edit Detail Peserta                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showEditModal)
        <div wire:key="edit-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-md overflow-hidden animate-zoom-in">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Edit Peserta Majek</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Ubah konfigurasi detail porsi santri</p>
                    </div>
                    <button type="button" wire:click="closeEditModal" class="text-slate-400 hover:text-slate-600 transition-colors">✕</button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-wide block">Nama Santri</span>
                        <strong class="text-slate-800 dark:text-white text-sm font-extrabold mt-0.5 block">{{ $editPersonName }}</strong>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Sesi Makan</label>
                            <select wire:model="editSesi"
                                class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/30 outline-none">
                                <option value="2x">🍽️🍽️ 2x Makan</option>
                                <option value="pagi">🌅 Pagi Saja</option>
                                <option value="sore">🌆 Sore Saja</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Hari Aktif Makan</label>
                            <input type="number" min="1" max="31" wire:model.live.debounce.500ms="editDays"
                                class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/30 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Catatan Khusus</label>
                        <input type="text" placeholder="Tulis catatan dispensasi/keterangan..." wire:model="editNotes"
                            class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs text-slate-850 dark:text-slate-250 focus:ring-2 focus:ring-amber-500/30 outline-none">
                    </div>

                    {{-- Preview Nominal --}}
                    @if($this->activePeriod && $editDays > 0)
                        @php
                            $editReg = \App\Modules\Keuangan\Models\MajekRegistration::with('person')->find($editRegId);
                            $dRate = $this->activePeriod->getTarifPerHariForGender($editReg?->person?->gender);
                            $tPrice = ($editSesi === '2x' ? ($dRate * 2) : $dRate) * $editDays;
                        @endphp
                        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-800/30 rounded-2xl p-4 text-xs space-y-1 bg-amber-500/5 border-amber-500/10 flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400 font-bold">Kalkulasi Tagihan Baru:</span>
                            <strong class="text-amber-600 font-mono text-sm">Rp {{ number_format($tPrice, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 bg-slate-50/50 dark:bg-slate-950/20">
                    <button type="button" wire:click="closeEditModal"
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="saveEdit"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Preview & Konfirmasi Setoran (Kasir / Cicilan)                    --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showConfirmModal)
        <div wire:key="confirm-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-2xl overflow-hidden animate-zoom-in">
                {{-- Header --}}
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/20">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Preview Setoran & Cicilan Majek</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $this->monthLabel }} · Kasir dapat menyesuaikan nominal bayar jika santri mencicil.</p>
                    </div>
                    <span class="text-[10px] font-extrabold bg-amber-500/10 text-amber-700 dark:text-amber-400 px-3 py-1 rounded-xl border border-amber-500/20">{{ $countChecked }} Peserta Terpilih</span>
                </div>

                {{-- Tabel Preview --}}
                <div class="p-6 space-y-4">
                    <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden max-h-72 overflow-y-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950 text-[9px] text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-2.5 px-3">No</th>
                                    <th class="py-2.5 px-3">Nama Santri</th>
                                    <th class="py-2.5 px-3 text-center">Sesi</th>
                                    <th class="py-2.5 px-3 text-right">Sisa Tagihan</th>
                                    <th class="py-2.5 px-3 text-right w-44">Nominal Setoran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-[11px]">
                                @foreach($this->previewData as $i => $item)
                                    @php
                                        $isOver = $item['pay_amt'] > ($item['remaining'] + 0.01);
                                    @endphp
                                    <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/30 transition-colors {{ $isOver ? 'bg-rose-500/10' : '' }}">
                                        <td class="py-2.5 px-3 text-slate-400 font-bold">{{ $i + 1 }}</td>
                                        <td class="py-2.5 px-3 font-bold text-slate-800 dark:text-slate-200">
                                            <div>{{ $item['name'] }}</div>
                                            <div class="text-[9px] text-slate-400 font-normal">Total Majaek: Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-2.5 px-3 text-center text-slate-500 font-semibold">{{ $item['sesi'] }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-600 dark:text-slate-400">
                                            Rp {{ number_format($item['remaining'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-2 px-3 text-right">
                                            <div class="relative flex items-center">
                                                <span class="absolute left-2.5 text-[10px] font-bold {{ $isOver ? 'text-rose-500' : 'text-slate-400' }}">Rp</span>
                                                <input type="number" step="5000" min="0" max="{{ $item['remaining'] }}"
                                                    wire:model.live.debounce.300ms="paymentAmounts.{{ $item['id'] }}"
                                                    class="w-full pl-7 pr-2 py-1.5 rounded-xl border {{ $isOver ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 focus:ring-rose-500' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-emerald-500/30 focus:border-emerald-500' }} text-xs font-black font-mono text-right outline-none transition-all">
                                            </div>
                                            @if($isOver)
                                                <div class="text-[9px] text-rose-600 dark:text-rose-400 font-extrabold text-right mt-0.5">
                                                    ⚠️ Melebihi sisa (Maks. Rp {{ number_format($item['remaining'], 0, ',', '.') }})
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Setoran Kasir --}}
                    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div>
                            <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-extrabold uppercase tracking-wider block">Total uang diterima (Kasir)</span>
                            <span class="text-[10px] text-slate-400">Otomatis menghitung seluruh nominal setoran santri di atas.</span>
                        </div>
                        <strong class="text-emerald-600 dark:text-emerald-400 text-xl font-black font-mono">Rp {{ number_format($totalChecked, 0, ',', '.') }}</strong>
                    </div>

                    {{-- Konfirmasi Checkbox --}}
                    <label class="flex items-start gap-3 p-3.5 bg-rose-500/5 border border-rose-500/10 rounded-2xl cursor-pointer">
                        <input type="checkbox" wire:model.live="confirmCheck"
                            class="mt-0.5 rounded text-rose-600 focus:ring-rose-500 border-slate-300 dark:border-slate-700">
                        <span class="text-[10px] leading-relaxed text-rose-700 dark:text-rose-300 font-bold select-none">
                            Saya menyatakan data setoran/cicilan di atas sudah sama dengan uang kasir / bukti setoran fisik yang diterima.
                        </span>
                    </label>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" wire:click="cancelConfirm"
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        ⬅️ Kembali
                    </button>
                    <button type="button" wire:click="prosesSetoran" @if(!$confirmCheck) disabled @endif
                        class="px-6 py-2.5 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2
                            {{ $confirmCheck ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-300 dark:bg-slate-800 text-slate-400 cursor-not-allowed' }}">
                        💾 Simpan Setoran Majek
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Konfirmasi Hapus Peserta                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
        <div wire:key="delete-modal-container" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 border border-slate-205 dark:border-slate-800 rounded-3xl shadow-xl w-full max-w-md overflow-hidden animate-zoom-in">
                <div class="p-6 text-center space-y-4">
                    <div class="w-16 h-16 bg-rose-500/10 dark:bg-rose-500/20 text-rose-550 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto text-2xl">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wide">Konfirmasi Hapus Peserta</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Apakah Anda yakin ingin menghapus <strong class="text-slate-800 dark:text-white font-bold">{{ $deletePersonName }}</strong> dari daftar katering Majek bulan ini?
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1">Tagihan unpaid yang terkait dengan peserta ini juga akan otomatis dihapus.</p>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" wire:click="closeDeleteModal"
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="button" wire:click="removePeserta"
                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        🗑️ Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: SALIN PESERTA DARI BULAN LAIN                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showCopyPeriodModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="closeCopyPeriodModal">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-violet-100 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 flex items-center justify-center text-xl shrink-0">
                            📋
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-slate-100">Salin Peserta Majek dari Bulan Lain</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Duplikasi pendaftar katering ke periode <strong>{{ $this->monthLabel }}</strong>.</p>
                        </div>
                    </div>

                    @if($this->genderScope())
                        <span class="px-2.5 py-1 bg-purple-50 dark:bg-purple-950/50 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 rounded-xl text-[10px] font-extrabold uppercase tracking-wider">
                            {{ $this->genderScope() === 'L' ? '👦 Scope Putra' : '👧 Scope Putri' }}
                        </span>
                    @endif
                </div>

                <div class="space-y-4 pt-1">
                    @if(!$this->activePeriod)
                        <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-2xl text-xs space-y-2">
                            <div class="font-extrabold text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Konfigurasi Periode {{ $this->monthLabel }} Belum Dibuat!</span>
                            </div>
                            <p class="text-amber-600 dark:text-amber-400 text-[11px] leading-relaxed">
                                Untuk mencegah kesalahan tarif/nominal, Anda wajib mengatur <strong>Konfigurasi Periode (Hari Aktif & Tarif Majek)</strong> bulan ini terlebih dahulu sebelum menyalin santri.
                            </p>
                            <button type="button" wire:click="closeCopyPeriodModal(); openPeriodModal();" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-[10px] transition-all inline-flex items-center gap-1">
                                ⚙️ Atur Konfigurasi {{ $this->monthLabel }} Sekarang
                            </button>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Bulan Asal</label>
                            <select wire:model.live="copySourceMonth" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-200">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Tahun Asal</label>
                            <select wire:model.live="copySourceYear" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 dark:text-slate-200">
                                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    @php
                        $preview = $this->copyPreviewData;
                    @endphp

                    <!-- Summary Stats Pill -->
                    <div class="grid grid-cols-3 gap-2 text-center text-[10px]">
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block font-bold uppercase">Total Bulan Asal</span>
                            <span class="text-sm font-black text-slate-800 dark:text-slate-100">{{ $preview['total_source'] }} Santri</span>
                        </div>
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/30 rounded-2xl border border-emerald-200/60 dark:border-emerald-800/40">
                            <span class="text-emerald-600 dark:text-emerald-400 block font-bold uppercase">Akan Disalin</span>
                            <span class="text-sm font-black text-emerald-700 dark:text-emerald-300">+{{ $preview['will_copy_count'] }} Santri</span>
                        </div>
                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200/60 dark:border-amber-800/40">
                            <span class="text-amber-600 dark:text-amber-400 block font-bold uppercase">Sudah Ada (Skip)</span>
                            <span class="text-sm font-black text-amber-700 dark:text-amber-300">{{ $preview['already_registered_count'] }} Santri</span>
                        </div>
                    </div>

                    <!-- Interactive Live Preview Table of Santri List -->
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Preview Daftar Santri yang Akan Disalin:</label>
                        <div class="border border-slate-200/60 dark:border-slate-800 rounded-2xl overflow-hidden max-h-48 overflow-y-auto bg-slate-50/50 dark:bg-slate-950/30">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="sticky top-0 bg-slate-100 dark:bg-slate-950 z-10">
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 font-extrabold uppercase tracking-wider text-[9px]">
                                        <th class="py-2 px-3">Nama Santri</th>
                                        <th class="py-2 px-3 text-center">Sesi</th>
                                        <th class="py-2 px-3 text-right">Status Penyalinan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                                    @forelse($preview['students'] as $st)
                                        <tr class="hover:bg-white dark:hover:bg-slate-800/40 transition-colors">
                                            <td class="py-2 px-3 font-bold text-slate-800 dark:text-slate-200">
                                                {{ $st['name'] }}
                                                <span class="text-[9px] font-normal text-slate-400">({{ $st['gender'] === 'L' ? 'Putra' : 'Putri' }})</span>
                                            </td>
                                            <td class="py-2 px-3 text-center text-[10px] font-semibold text-slate-500">
                                                {{ $st['sesi'] }}
                                            </td>
                                            <td class="py-2 px-3 text-right">
                                                @if($st['is_already'])
                                                    <span class="inline-block px-2 py-0.5 bg-slate-200/60 dark:bg-slate-800 text-slate-500 text-[9px] font-bold rounded-lg">
                                                        Sudah Ada (Dilewati)
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[9px] font-bold rounded-lg border border-emerald-200 dark:border-emerald-900/50">
                                                        ✓ Akan Disalin
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-6 text-center text-slate-400 text-xs italic">
                                                Tidak ada santri peserta Majek pada bulan yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" wire:click="closeCopyPeriodModal"
                        class="flex-1 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="copyRegistrationsFromPeriod"
                        @if(!$this->activePeriod || $preview['will_copy_count'] === 0) disabled @endif
                        class="flex-1 py-2.5 bg-gradient-to-br from-violet-600 to-purple-700 hover:from-violet-500 hover:to-purple-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold rounded-xl text-xs transition-colors shadow-lg shadow-violet-500/20">
                        Ya, Salin {{ $preview['will_copy_count'] }} Santri
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
