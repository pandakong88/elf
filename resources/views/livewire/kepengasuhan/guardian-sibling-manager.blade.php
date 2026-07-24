<div class="space-y-6">
    {{-- ============================================================ --}}
    {{-- Header Page                                                  --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                Wali &amp; Hubungan Keluarga
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola data wali santri secara terpusat, integrasi saudara kandung, dan kelayakan diskon syahriah.
            </p>
        </div>
    </div>

    {{-- Alerts --}}
    {{-- ============================================================ --}}
    {{-- Tabs Switch                                                  --}}
    {{-- ============================================================ --}}
    <div class="flex border-b border-slate-200 dark:border-slate-800">
        <button type="button" wire:click="$set('activeTab', 'guardians')"
            class="px-6 py-3 font-bold text-sm border-b-2 transition-all {{ $activeTab === 'guardians' ? 'border-violet-600 text-violet-600 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            👨‍👦 Wali Santri
        </button>
        <button type="button" wire:click="$set('activeTab', 'siblings')"
            class="px-6 py-3 font-bold text-sm border-b-2 transition-all {{ $activeTab === 'siblings' ? 'border-violet-600 text-violet-600 dark:text-white' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            🤝 Hubungan Saudara
        </button>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB 1: Wali Santri                                           --}}
    {{-- ============================================================ --}}
    @if($activeTab === 'guardians')
        <div class="space-y-4">
            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-4 rounded-2xl shadow-sm">
                <div class="relative flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Cari nama, kota, atau nomor HP wali..."
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 transition-all text-sm">
                    <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="button" wire:click="openCreateModal"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-violet-500/20 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Wali
                </button>
            </div>

            {{-- Guardians List Table --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Wali</th>
                                <th class="px-6 py-4">Kontak HP</th>
                                <th class="px-6 py-4">Pekerjaan</th>
                                <th class="px-6 py-4">Asal Kota</th>
                                <th class="px-6 py-4 text-center">Jumlah Santri</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 text-sm">
                            @forelse($guardians as $g)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $g->name }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ $g->education_level ?? 'Pendidikan -' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $g->phone_primary }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $g->occupation ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $g->city ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center font-bold">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs {{ $g->santri_count > 0 ? 'bg-violet-100 dark:bg-violet-950/40 text-violet-700 dark:text-violet-300' : 'bg-slate-100 text-slate-400' }}">
                                            {{ $g->santri_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1.5">
                                            <button type="button" wire:click="openDetail('{{ $g->id }}')"
                                                class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Detail Hubungan">
                                                👁️
                                            </button>
                                            <button type="button" wire:click="openEditModal('{{ $g->id }}')"
                                                class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Edit Wali">
                                                ✏️
                                            </button>
                                            <button type="button" wire:click="openMergeModal('{{ $g->id }}')"
                                                class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" title="Gabungkan Wali Duplikat">
                                                🧩
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-slate-400 italic">
                                        Tidak ada data wali yang cocok dengan pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($guardians->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $guardians->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- TAB 2: Hubungan Saudara                                      --}}
    {{-- ============================================================ --}}
    @if($activeTab === 'siblings')
        <div class="space-y-6">
            {{-- Toolbar Deteksi Otomatis --}}
            <div class="bg-gradient-to-br from-violet-500 to-purple-600 p-6 rounded-3xl text-white shadow-lg shadow-violet-500/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-extrabold text-lg">💡 Deteksi Otomatis Saudara Kandung</h3>
                    <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                        Sistem dapat memindai seluruh data santri dan mencocokkan secara otomatis berdasarkan kesamaan data nama Ayah &amp; Ibu kandung, atau kesamaan nomor kontak wali yang didaftarkan.
                    </p>
                </div>
                <button type="button" wire:click="runAutoDetection"
                    class="px-5 py-2.5 bg-white text-violet-700 font-bold rounded-xl text-xs hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
                    🔍 Pindai &amp; Deteksi Saudara
                </button>
            </div>

            {{-- Unconfirmed Sibling Requests --}}
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span>⏳ Deteksi Saudara yang Belum Dikonfirmasi</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">{{ count($unconfirmedSiblings) }} Perlu Konfirmasi</span>
                </h2>
                
                @if(count($unconfirmedSiblings) === 0)
                    <div class="text-center py-10 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl text-slate-400 italic text-sm">
                        Tidak ada usulan deteksi saudara baru yang tertunda.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($unconfirmedSiblings as $relation)
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-950/40 flex items-center justify-center text-violet-700 dark:text-violet-300 font-bold text-xs">
                                                S1
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 font-semibold">Santri 1</p>
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $relation->person->name }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/40 flex items-center justify-center text-purple-700 dark:text-purple-300 font-bold text-xs">
                                                S2
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400 font-semibold">Santri 2</p>
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $relation->sibling->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400">
                                        Auto Detected
                                    </span>
                                </div>
                                <div class="text-xs text-slate-400 bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-lg">
                                    ℹ️ {{ $relation->notes }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <select wire:model.defer="siblingRelationship" class="px-2 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-xs focus:outline-none flex-1">
                                        <option value="saudara">Saudara Kandung</option>
                                        <option value="kakak">S2 adalah Kakak S1</option>
                                        <option value="adik">S2 adalah Adik S1</option>
                                        <option value="kembar">Kembar</option>
                                    </select>
                                    <button type="button" wire:click="confirmSibling('{{ $relation->id }}')"
                                        class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-lg text-xs transition-colors">
                                        Konfirmasi
                                    </button>
                                    <button type="button" wire:click="rejectSibling('{{ $relation->id }}')"
                                        class="px-3 py-1.5 border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold rounded-lg text-xs transition-colors">
                                        Tolak
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sibling Discount Eligible Santris --}}
            <div class="space-y-4 pt-4">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                    💰 Kelayakan Diskon Syahriah Saudara (Terkonfirmasi)
                </h2>
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Santri</th>
                                <th class="px-6 py-4">Komplek / Asrama</th>
                                <th class="px-6 py-4 text-center">Jumlah Saudara Aktif</th>
                                <th class="px-6 py-4 text-center">Eligible Diskon</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 text-sm">
                            @forelse($discountEligible as $santri)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">{{ $santri->name }}</td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $santri->roomAssignments->first()?->room?->dormitory?->name ?? 'Belum ada kamar' }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-violet-600 dark:text-violet-400">
                                        {{ $santri->santriProfile->active_sibling_count }} saudara
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold">
                                            Eligible (Flag Aktif)
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-slate-400 italic">
                                        Belum ada santri yang memiliki relasi saudara kandung terkonfirmasi aktif di pondok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Guardian Detail & Santris                             --}}
    {{-- ============================================================ --}}
    @if($showDetailModal && $selectedGuardian)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                    <div class="min-w-0">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">Detail Wali: {{ $selectedGuardian->name }}</h3>
                        <p class="text-xs text-slate-400">Kontak HP: {{ $selectedGuardian->phone_primary }}</p>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Info Card --}}
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="block text-slate-400 font-semibold mb-0.5">Pendidikan</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedGuardian->education_level ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-semibold mb-0.5">Pekerjaan</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedGuardian->occupation ?? '-' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-slate-400 font-semibold mb-0.5">Alamat Wali</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedGuardian->address ?? '-' }}, {{ $selectedGuardian->city ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Linked Santri list --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">👦 Santri yang Di-wali</h4>
                            <button type="button" wire:click="openLinkModal('{{ $selectedGuardian->id }}')"
                                class="px-2.5 py-1.5 bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300 rounded-lg text-xs font-bold hover:bg-violet-100 transition-colors">
                                🔗 Hubungkan Santri Baru
                            </button>
                        </div>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden">
                            @forelse($selectedGuardian->santri as $s)
                                <div class="px-4 py-3 flex items-center justify-between gap-4 bg-white dark:bg-slate-900">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $s->name }}</p>
                                        <p class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                            <span class="px-1.5 py-0.2 bg-violet-100 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 rounded-md font-bold uppercase text-[9px]">{{ $s->pivot->relationship }}</span>
                                            @if($s->pivot->is_primary)
                                                <span class="text-emerald-500 font-bold">★ Wali Utama</span>
                                            @endif
                                        </p>
                                    </div>
                                    <button type="button" wire:click="unlinkSantri('{{ $s->id }}')"
                                        class="p-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors text-xs font-bold">
                                        Lepas
                                    </button>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-slate-400 italic text-xs">
                                    Wali ini belum dihubungkan ke santri manapun.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Create / Edit Guardian Form                           --}}
    {{-- ============================================================ --}}
    @if($showFormModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showFormModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        {{ $isEditing ? 'Ubah Data Wali' : 'Tambah Wali Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showFormModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" wire:model.defer="guardianName" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                        @error('guardianName') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">No HP / WhatsApp</label>
                        <input type="text" wire:model.defer="guardianPhone" placeholder="cth: 0812345678" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                        @error('guardianPhone') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pendidikan</label>
                            <select wire:model.defer="guardianEducation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                                <option value="">Pilih</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="Tidak Sekolah">Tidak Sekolah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan</label>
                            <input type="text" wire:model.defer="guardianOccupation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kota / Kabupaten</label>
                        <input type="text" wire:model.defer="guardianCity" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                        <textarea wire:model.defer="guardianAddress" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                        <textarea wire:model.defer="guardianNotes" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="saveGuardian" class="px-5 py-2 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-violet-500/20 transition-all">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Link Santri to Guardian                               --}}
    {{-- ============================================================ --}}
    @if($showLinkModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4" wire:click.self="$set('showLinkModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Hubungkan Santri Baru</h3>
                    <button type="button" wire:click="$set('showLinkModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Search Santri --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Cari Nama Santri</label>
                        <input type="text" wire:model.live="linkSearch" placeholder="Ketik nama santri..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                    </div>

                    {{-- Search results --}}
                    @if(!empty($linkSearch))
                        <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden max-h-40 overflow-y-auto">
                            @forelse($linkSantriList as $santri)
                                <div class="px-4 py-2.5 flex justify-between items-center gap-4 bg-white dark:bg-slate-900 text-xs">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $santri->name }}</span>
                                    <button type="button" wire:click="linkSantri('{{ $santri->id }}')"
                                        class="px-2.5 py-1 bg-violet-600 hover:bg-violet-700 text-white rounded font-bold">
                                        Hubungkan
                                    </button>
                                </div>
                            @empty
                                <div class="px-4 py-4 text-center text-slate-400 italic text-xs">
                                    Santri tidak ditemukan.
                                </div>
                            @endforelse
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Hubungan Relasi</label>
                            <select wire:model="linkRelationship" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-xs">
                                @foreach($guardianRelationOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="linkIsPrimary" class="rounded text-violet-600 border-slate-300 dark:border-slate-700">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Set Wali Utama</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL: Merge duplicate guardians                             --}}
    {{-- ============================================================ --}}
    @if($showMergeModal)
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="$set('showMergeModal', false)">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Gabungkan Wali Duplikat</h3>
                    <button type="button" wire:click="$set('showMergeModal', false)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        ⚠️ <strong>PENTING:</strong> Gabungkan wali duplikat ini dengan wali lainnya. Semua santri yang terhubung ke wali ini akan dialihkan ke wali tujuan. Setelah digabungkan, wali ini akan <strong>dihapus permanen</strong>.
                    </p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Wali Tujuan (Target Penggabungan)</label>
                        <select wire:model="mergeTargetId" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 text-sm">
                            <option value="">-- Pilih Wali Tujuan --</option>
                            @foreach($mergeCandidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->phone_primary }})</option>
                            @endforeach
                        </select>
                        @error('mergeTargetId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" wire:click="$set('showMergeModal', false)" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors">Batal</button>
                    <button type="button" wire:click="mergeGuardians" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl text-sm shadow-lg transition-all">Gabungkan &amp; Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
