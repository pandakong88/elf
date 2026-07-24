<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 rounded-3xl shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-xl text-slate-900 dark:text-slate-100">Pengaturan Tarif Pendaftaran Santri Baru &amp; Kitab</h1>
                    <p class="text-xs text-slate-400">Kelola komponen rincian harga pendaftaran, seragam, almari, dan paket kitab per kelas</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.billing') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors">
                &larr; Kembali ke Pusat Keuangan
            </a>
            <button type="button" wire:click="openItemModal" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold rounded-xl shadow-md transition-all text-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Item Tarif Baru</span>
            </button>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
        <button type="button" wire:click="$set('activeTab', 'items')"
            class="px-5 py-2.5 font-extrabold text-xs rounded-xl transition-all flex items-center gap-2 {{ $activeTab === 'items' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Item Tarif Registrasi (Pendaftaran, Seragam, Almari)</span>
        </button>
        <button type="button" wire:click="$set('activeTab', 'kitab')"
            class="px-5 py-2.5 font-extrabold text-xs rounded-xl transition-all flex items-center gap-2 {{ $activeTab === 'kitab' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Tarif Paket Kitab Per Kelas Madrasah</span>
        </button>
    </div>

    {{-- TAB 1: ITEM TARIF REGISTRASI --}}
    @if($activeTab === 'items')
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Daftar Komponen Tarif Pendaftaran Santri Baru</h3>
                    <p class="text-xs text-slate-400">Setiap item di bawah ini akan muncul sebagai pilihan checklist pada formulir pendaftaran santri baru</p>
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-200/60 dark:border-slate-800 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-3">Nama Item Tarif</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Target Gender</th>
                            <th class="p-3">Target Keberadaan</th>
                            <th class="p-3 text-right">Nominal Tarif (Rp)</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @forelse($registrationItems as $item)
                            @php
                                $filters = $item->target_filters ?? [];
                                $cat = $filters['category'] ?? 'dasar';
                                $gen = $filters['gender'] ?? 'ALL';
                                $res = $filters['residence'] ?? 'ALL';
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                <td class="p-3">
                                    <div class="font-extrabold text-slate-900 dark:text-slate-100">{{ $item->label }}</div>
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $cat }}
                                    </span>
                                </td>
                                <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                    @if($gen === 'L')
                                        <span class="text-blue-600 font-bold">Putra (L)</span>
                                    @elseif($gen === 'P')
                                        <span class="text-rose-600 font-bold">Putri (P)</span>
                                    @else
                                        <span>Semua Gender</span>
                                    @endif
                                </td>
                                <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">
                                    @if($res === 'mukim')
                                        <span class="text-indigo-600 font-bold">Khusus Mukim</span>
                                    @elseif($res === 'laju')
                                        <span class="text-amber-600 font-bold">Khusus Laju</span>
                                    @else
                                        <span>Mukim &amp; Laju</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" wire:click="toggleItemActive('{{ $item->id }}')"
                                        class="px-2.5 py-1 text-[10px] font-extrabold rounded-full transition-all {{ $item->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $item->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </button>
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" wire:click="openItemModal('{{ $item->id }}')"
                                        class="px-3 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-xs transition-colors">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">Belum ada item tarif pendaftaran yang dikonfigurasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 2: TARIF KITAB PER KELAS --}}
    @if($activeTab === 'kitab')
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">Pengaturan Tarif Paket Kitab Per Kelas Madrasah</h3>
                    <p class="text-xs text-slate-400">Atur nominal harga paket kitab untuk masing-masing tingkat kelas secara langsung</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($kitabPrices as $kelasId => $kData)
                    <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/40 dark:border-slate-700/40 pb-2">
                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                {{ $kData['jenjang'] }}
                            </span>
                            <span class="font-extrabold text-sm text-slate-900 dark:text-slate-100">{{ $kData['kelas_name'] }}</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Nominal Paket Kitab (Rp)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model="kitabPrices.{{ $kelasId }}.amount"
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                <button type="button" wire:click="saveKitabPrice('{{ $kelasId }}')"
                                    class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow transition-all flex-shrink-0">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- MODAL EDIT / TAMBAH ITEM TARIF --}}
    @if($showItemModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-extrabold text-base text-slate-900 dark:text-slate-100">
                        {{ $editingItemId ? 'Edit Item Tarif Registrasi' : 'Tambah Item Tarif Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showItemModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nama / Label Item Tarif</label>
                        <input type="text" wire:model="itemLabel" placeholder="Contoh: Seragam Khusus Putri" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Nominal Harga (Rp)</label>
                        <input type="number" wire:model="itemAmount" placeholder="150000" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-slate-800 dark:text-slate-100">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Target Gender</label>
                            <select wire:model="itemGender" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                <option value="ALL">Semua Gender</option>
                                <option value="L">Putra (L) Saja</option>
                                <option value="P">Putri (P) Saja</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Target Keberadaan</label>
                            <select wire:model="itemResidence" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                                <option value="ALL">Mukim &amp; Laju</option>
                                <option value="mukim">Mukim Saja</option>
                                <option value="laju">Laju Saja</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-300 mb-1">Kategori Item</label>
                        <select wire:model="itemCategory" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100">
                            <option value="dasar">Dasar Pendaftaran</option>
                            <option value="asrama">Fasilitas Asrama</option>
                            <option value="seragam">Seragam</option>
                            <option value="konsumsi">Konsumsi / Majek</option>
                            <option value="kitab">Kitab</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="$set('showItemModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="button" wire:click="saveItem" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow">Simpan Item Tarif</button>
                </div>
            </div>
        </div>
    @endif
</div>
