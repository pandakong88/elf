<div class="py-6 px-4 sm:px-6 lg:px-8">
    @if ($mode === 'list')
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Template Sensus</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Kelola formulir sensus fleksibel untuk berbagai keperluan pondok.</p>
            </div>
            <button type="button" wire:click="showCreate" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-2xl shadow-lg shadow-emerald-600/10 hover:shadow-emerald-500/20 hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Template Baru</span>
            </button>
        </div>

        <!-- Search & Filter Controls -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau deskripsi template..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 pl-10 pr-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
            </div>
            <div class="sm:w-56">
                <select wire:model.live="filterType" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                    <option value="all">Semua Tipe</option>
                    <option value="default">Hanya Default</option>
                    <option value="custom">Hanya Kustom</option>
                </select>
            </div>
        </div>

        <!-- Template Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($templates as $tpl)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-slate-200 dark:hover:border-slate-700 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white truncate">{{ $tpl->name }}</h3>
                            @if ($tpl->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Default</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 mb-6 min-h-[4.5rem]">{{ $tpl->description ?: 'Tidak ada deskripsi.' }}</p>

                        <div class="border-t border-slate-100 dark:border-slate-800/60 pt-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Field Sensus ({{ $tpl->fields->count() }})</h4>
                            <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto pr-1">
                                @foreach ($tpl->fields as $field)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700/50">
                                        @if ($field->is_system_field)
                                            <svg class="w-3 h-3 text-emerald-500 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" title="Field Sistem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        @endif
                                        {{ $field->field_label }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Oleh {{ $tpl->creator->name ?? 'Sistem' }}</span>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="duplicateTemplate('{{ $tpl->id }}')" class="p-2 text-slate-400 hover:text-emerald-500 hover:bg-emerald-500/10 rounded-xl transition-all" title="Duplikat Template">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                            </button>
                            <button type="button" wire:click="openPreviewModal('{{ $tpl->id }}')" class="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-500/10 rounded-xl transition-all" title="Pratinjau Excel">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button type="button" wire:click="downloadSampleExcel('{{ $tpl->id }}')" class="p-2 text-slate-400 hover:text-cyan-500 hover:bg-cyan-500/10 rounded-xl transition-all" title="Unduh Contoh Excel">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </button>
                            <button type="button" wire:click="showEdit('{{ $tpl->id }}')" class="p-2 text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all" title="Edit Template">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button type="button" wire:click="confirmArchive('{{ $tpl->id }}')" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all" title="Arsipkan Template">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="text-slate-300 dark:text-slate-700 mb-3 flex justify-center"><svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg></div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-300">Belum ada template</h3>
                    <p class="text-slate-400 mt-1">Mulai dengan membuat template sensus pertama Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- ============================================================ --}}
        {{-- MODAL KONFIRMASI HAPUS/ARSIP TEMPLATE                       --}}
        {{-- ============================================================ --}}
        <div
            x-data="{ show: @entangle('confirmingArchiveId').live }"
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            {{-- Backdrop --}}
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="$wire.cancelArchive()"
                class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"
            ></div>

            {{-- Dialog Box --}}
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-7 z-10"
            >
                {{-- Icon --}}
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-900 mx-auto mb-5">
                    <svg class="w-7 h-7 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                {{-- Title & Text --}}
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Hapus Template?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Template ini akan diarsipkan dan tidak muncul di daftar. Tindakan ini tidak dapat dibatalkan secara langsung.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="cancelArchive"
                        class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-sm transition-all"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="archiveTemplate"
                        wire:loading.attr="disabled"
                        wire:target="archiveTemplate"
                        class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-rose-600/20 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg wire:loading wire:target="archiveTemplate" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="archiveTemplate">Ya, Hapus Template</span>
                        <span wire:loading wire:target="archiveTemplate">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL PRATINJAU LAYOUT EXCEL                                -->
        <!-- ============================================================ -->
        <div
            x-data="{ show: @entangle('previewTemplateId').live, tab: 'simulator' }"
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        >
            <!-- Backdrop -->
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="$wire.closePreviewModal()"
                class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"
            ></div>

            <!-- Dialog Box -->
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 max-w-5xl w-full flex flex-col max-h-[85vh] z-10 overflow-hidden"
            >
                @if ($this->previewTemplate)
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-6 h-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Pratinjau Layout Excel - {{ $this->previewTemplate->name }}</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Melihat rancangan kolom dan aturan validasi data sheet sensus.</p>
                        </div>
                        <button type="button" @click="$wire.closePreviewModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Tabs Switcher -->
                    <div class="px-6 border-b border-slate-100 dark:border-slate-800 flex gap-4">
                        <button
                            type="button"
                            @click="tab = 'simulator'"
                            :class="tab === 'simulator' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="py-3.5 text-sm font-semibold border-b-2 transition-all"
                        >
                            Simulasi Spreadsheet
                        </button>
                        <button
                            type="button"
                            @click="tab = 'guide'"
                            :class="tab === 'guide' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="py-3.5 text-sm font-semibold border-b-2 transition-all"
                        >
                            Aturan & Panduan Kolom
                        </button>
                    </div>

                    <!-- Scrollable Modal Body -->
                    <div class="p-6 flex-1 overflow-y-auto min-h-0 space-y-6">
                        
                        <!-- Info Alert -->
                        <div class="p-4 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 rounded-2xl flex gap-3 text-sm text-blue-700 dark:text-blue-300">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="space-y-1">
                                <p class="font-bold">Informasi Layout Sheet</p>
                                <p class="text-xs leading-relaxed text-blue-600 dark:text-blue-400">
                                    Kolom pertama **ID Santri (Kolom A)** disembunyikan secara otomatis di dalam berkas Excel untuk menjaga kerapian tampilan. Kolom ini penting bagi sistem untuk mengimpor dan memetakan data dengan benar.
                                </p>
                            </div>
                        </div>

                        <!-- TAB: SPREADSHEET SIMULATOR -->
                        <div x-show="tab === 'simulator'" class="space-y-4">
                            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-955 max-w-full">
                                <table class="min-w-full border-collapse text-left text-xs font-mono">
                                    <thead>
                                        <!-- Excel Letter Headers -->
                                        <tr class="bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-center text-slate-400 dark:text-slate-500">
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800 w-10"></th>
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800 bg-slate-200/50 dark:bg-slate-800/40 text-slate-500 dark:text-slate-300">A (Hidden)</th>
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800">B</th>
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800">C</th>
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800">D</th>
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800">E</th>
                                            @php $colLetterCode = 70; @endphp <!-- 'F' is ASCII 70 -->
                                            @foreach ($this->previewTemplate->fields as $field)
                                                <th class="p-2 border-r border-slate-200 dark:border-slate-800">
                                                    {{ chr($colLetterCode++) }}
                                                </th>
                                            @endforeach
                                        </tr>
                                        <!-- Excel Heading Labels -->
                                        <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-bold text-center">
                                            <th class="p-2 border-r border-slate-200 dark:border-slate-800 text-slate-400 bg-slate-100 dark:bg-slate-900 text-center">1</th>
                                            <th class="p-2.5 border-r border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 font-semibold italic text-left">
                                                ID Santri (Jangan Diubah)
                                            </th>
                                            <th class="p-2.5 border-r border-slate-200 dark:border-slate-800 text-left">Nama Lengkap</th>
                                            <th class="p-2.5 border-r border-slate-200 dark:border-slate-800 text-left">Kamar</th>
                                            <th class="p-2.5 border-r border-slate-200 dark:border-slate-800 text-left">Status Anggota</th>
                                            <th class="p-2.5 border-r border-slate-200 dark:border-slate-800 text-left">Status Keberadaan</th>
                                            @foreach ($this->previewTemplate->fields as $field)
                                                <th class="p-2.5 border-r border-slate-200 dark:border-slate-800 text-left whitespace-nowrap">
                                                    {{ $field->field_label }}
                                                    @if ($field->is_required)
                                                        <span class="text-rose-500">*</span>
                                                    @endif
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300">
                                        @foreach ($this->previewRows as $index => $row)
                                            <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                                <td class="p-2 border-r border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 text-center text-slate-400 font-bold">{{ $index + 2 }}</td>
                                                <td class="p-2 border-r border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/20 text-slate-400 dark:text-slate-500 italic">{{ $row['id'] }}</td>
                                                <td class="p-2.5 border-r border-slate-100 dark:border-slate-800 font-sans font-medium text-slate-900 dark:text-white">{{ $row['name'] }}</td>
                                                <td class="p-2.5 border-r border-slate-100 dark:border-slate-800 font-sans">{{ $row['room'] }}</td>
                                                <td class="p-2.5 border-r border-slate-100 dark:border-slate-800 text-emerald-600 dark:text-emerald-400 font-semibold">{{ $row['enrollment'] }}</td>
                                                <td class="p-2.5 border-r border-slate-100 dark:border-slate-800 text-blue-600 dark:text-blue-400 font-semibold">{{ $row['presence'] }}</td>
                                                @foreach ($this->previewTemplate->fields as $field)
                                                    <td class="p-2.5 border-r border-slate-100 dark:border-slate-800 font-sans">
                                                        {{ $row['custom'][$field->field_key] ?? '' }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB: COLUMN GUIDE & VALIDATION -->
                        <div x-show="tab === 'guide'" class="space-y-4">
                            <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                                    <thead class="bg-slate-50 dark:bg-slate-900">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left font-bold text-slate-700 dark:text-slate-300">Kolom</th>
                                            <th scope="col" class="px-4 py-3 text-left font-bold text-slate-700 dark:text-slate-300">Nama Header</th>
                                            <th scope="col" class="px-4 py-3 text-left font-bold text-slate-700 dark:text-slate-300">Tipe Data</th>
                                            <th scope="col" class="px-4 py-3 text-center font-bold text-slate-700 dark:text-slate-300">Wajib</th>
                                            <th scope="col" class="px-4 py-3 text-left font-bold text-slate-700 dark:text-slate-300">Aturan & Validasi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs text-slate-600 dark:text-slate-400">
                                        <!-- Column A -->
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                            <td class="px-4 py-3 font-mono font-bold text-slate-500">A</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">ID Santri (Jangan Diubah)</td>
                                            <td class="px-4 py-3">UUID (Sistem)</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">Sistem</span>
                                            </td>
                                            <td class="px-4 py-3 text-slate-500 italic">Disembunyikan otomatis. JANGAN diubah agar proses impor tidak error.</td>
                                        </tr>
                                        <!-- Column B -->
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                            <td class="px-4 py-3 font-mono font-bold text-slate-500">B</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Nama Lengkap</td>
                                            <td class="px-4 py-3">Teks (Read-only)</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">Sistem</span>
                                            </td>
                                            <td class="px-4 py-3">Hanya baca sebagai pemandu identitas santri.</td>
                                        </tr>
                                        <!-- Column C -->
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                            <td class="px-4 py-3 font-mono font-bold text-slate-500">C</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Kamar</td>
                                            <td class="px-4 py-3">Teks (Read-only)</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">Sistem</span>
                                            </td>
                                            <td class="px-4 py-3">Kamar asrama santri saat ini.</td>
                                        </tr>
                                        <!-- Column D -->
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                            <td class="px-4 py-3 font-mono font-bold text-slate-500">D</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Status Anggota</td>
                                            <td class="px-4 py-3">Dropdown Pilihan</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Ya</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="font-semibold text-slate-700 dark:text-slate-300">Pilihan Dropdown:</span><br>
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">aktif</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">alumni</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">keluar_resmi</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">dikeluarkan</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">tanpa_keterangan</code>
                                            </td>
                                        </tr>
                                        <!-- Column E -->
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                            <td class="px-4 py-3 font-mono font-bold text-slate-500">E</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Status Keberadaan</td>
                                            <td class="px-4 py-3">Dropdown Pilihan</td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Ya</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="font-semibold text-slate-700 dark:text-slate-300">Pilihan Dropdown:</span><br>
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">mukim</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">laju</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">izin</code>, 
                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">alpa</code>
                                            </td>
                                        </tr>
                                        <!-- Custom fields mapping -->
                                        @php $colLetterCode = 70; @endphp
                                        @foreach ($this->previewTemplate->fields as $field)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                                <td class="px-4 py-3 font-mono font-bold text-slate-500">{{ chr($colLetterCode++) }}</td>
                                                <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">
                                                    {{ $field->field_label }} <span class="text-slate-400 dark:text-slate-500 font-mono text-[10px]">({{ $field->field_key }})</span>
                                                </td>
                                                <td class="px-4 py-3">{{ $field->getTypeLabel() }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($field->is_required)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Ya</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-505 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">Tidak</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($field->field_type === 'dropdown')
                                                        <span class="font-semibold text-slate-700 dark:text-slate-300">Pilihan Dropdown:</span><br>
                                                        @if (is_array($field->field_options))
                                                            @foreach ($field->field_options as $opt)
                                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">{{ $opt }}</code>{{ !$loop->last ? ',' : '' }}
                                                            @endforeach
                                                        @else
                                                            @foreach (explode(',', $field->field_options) as $opt)
                                                                <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">{{ trim($opt) }}</code>{{ !$loop->last ? ',' : '' }}
                                                            @endforeach
                                                        @endif
                                                    @elseif ($field->field_type === 'boolean')
                                                        <span class="font-semibold text-slate-700 dark:text-slate-300">Pilihan Dropdown:</span> 
                                                        <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">YA</code>, 
                                                        <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">TIDAK</code>
                                                    @elseif ($field->field_type === 'number')
                                                        Angka bilangan bulat/desimal.
                                                    @elseif ($field->field_type === 'date')
                                                        Format tanggal ISO: <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-400">YYYY-MM-DD</code> (Contoh: 2026-06-27).
                                                    @elseif ($field->field_type === 'textarea')
                                                        Teks narasi panjang bebas.
                                                    @else
                                                        Teks singkat bebas.
                                                    @endif

                                                    @if ($field->placeholder_text)
                                                        <br><span class="text-slate-450 italic text-[11px]">Placeholder: "{{ $field->placeholder_text }}"</span>
                                                    @endif
                                                    @if ($field->help_text)
                                                        <br><span class="text-slate-450 italic text-[11px]">Petunjuk: "{{ $field->help_text }}"</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                        <button
                            type="button"
                            wire:click="downloadSampleExcel('{{ $this->previewTemplate->id }}')"
                            class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-555 text-white font-semibold rounded-xl text-sm shadow-lg shadow-cyan-600/10 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh File Excel Contoh</span>
                        </button>
                        <button
                            type="button"
                            @click="$wire.closePreviewModal()"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl text-sm transition-all"
                        >
                            Tutup
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Builder Form -->
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 mb-4 text-sm text-slate-500">
                <button type="button" wire:click="cancel" class="hover:underline">Template Sensus</button>
                <span>&rsaquo;</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $mode === 'create' ? 'Buat Baru' : 'Edit Template' }}</span>
            </div>

            <!-- Title & Status -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $mode === 'create' ? 'Buat Template Baru' : 'Edit Template Sensus' }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Tentukan kolom isian, tipe data, dan parameter validasi sensus.</p>
                </div>
            </div>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Left: Metadata & Live Preview (span 4) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Metadata card -->
                        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm space-y-5">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Informasi Template</h3>
                            
                            <div>
                                <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Template <span class="text-rose-500">*</span></label>
                                <input type="text" id="name" wire:model.blur="name" placeholder="Contoh: Sensus Hafalan Quran" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                                <textarea id="description" wire:model.blur="description" rows="4" placeholder="Jelaskan tujuan sensus ini..." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors"></textarea>
                                @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_default" wire:model="is_default" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                <label for="is_default" class="text-sm font-medium text-slate-700 dark:text-slate-300">Set sebagai default saat buat sensus</label>
                            </div>
                        </div>

                        <!-- Live Form Preview card -->
                        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-800 dark:border-slate-800 pb-3 mb-4">
                                <h3 class="text-md font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Pratinjau Formulir Riil
                                </h3>
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 rounded-full">Live Preview</span>
                            </div>
                            
                            <div class="space-y-4 max-h-[480px] overflow-y-auto pr-1">
                                <div class="p-3.5 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200/50 dark:border-slate-800/80 mb-2">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Kamar Santri (Read-only)</div>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">Kamar Umar Bin Khattab - 01</div>
                                </div>

                                @forelse ($fields as $field)
                                    <div class="space-y-1">
                                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $field['field_label'] ?: '(Tanpa Label)' }}
                                            @if ($field['is_required'])
                                                <span class="text-rose-500">*</span>
                                            @endif
                                        </label>

                                        @if ($field['field_type'] === 'dropdown')
                                            <select disabled class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 px-3 py-2.5 text-xs cursor-not-allowed">
                                                <option>{{ $field['placeholder_text'] ?: '-- Pilih Opsi --' }}</option>
                                                @if (!empty($field['field_options']))
                                                    @foreach (explode(',', $field['field_options']) as $opt)
                                                        <option>{{ trim($opt) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif ($field['field_type'] === 'textarea')
                                            <textarea disabled placeholder="{{ $field['placeholder_text'] ?: 'Masukkan teks panjang...' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 px-3 py-2 text-xs cursor-not-allowed" rows="2"></textarea>
                                        @elseif ($field['field_type'] === 'boolean')
                                            <div class="flex items-center gap-2 py-1">
                                                <input type="checkbox" disabled class="w-4 h-4 rounded text-emerald-600 border-slate-300 cursor-not-allowed">
                                                <span class="text-xs text-slate-400">Ya / Tidak (Checkbox)</span>
                                            </div>
                                        @elseif ($field['field_type'] === 'number')
                                            <input type="number" disabled placeholder="{{ $field['placeholder_text'] ?: '0' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 px-3 py-2.5 text-xs cursor-not-allowed">
                                        @elseif ($field['field_type'] === 'date')
                                            <input type="date" disabled class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 px-3 py-2.5 text-xs cursor-not-allowed">
                                        @else
                                            <input type="text" disabled placeholder="{{ $field['placeholder_text'] ?: 'Masukkan teks...' }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 px-3 py-2.5 text-xs cursor-not-allowed">
                                        @endif

                                        @if ($field['help_text'])
                                            <p class="text-[10px] text-slate-400 italic">{{ $field['help_text'] }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="py-6 text-center text-xs text-slate-400 italic">Belum ada field formulir.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right: Field Builder (span 8) -->
                    <div class="lg:col-span-8 space-y-6">
                        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Field / Kolom Formulir</h3>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="addCustomField" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg><span>Kolom Kustom</span>
                                    </button>
                                </div>
                            </div>

                            <!-- System Field Adder with Choices.js (searchable) -->
                            <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 mb-6 flex flex-col sm:flex-row items-end gap-3">
                                <div class="flex-1 w-full" wire:ignore
                                    x-data="{
                                        choicesInstance: null,
                                        init() {
                                            this.$nextTick(() => {
                                                const el = this.$el.querySelector('#sys_field');
                                                if (el && typeof Choices !== 'undefined') {
                                                    this.choicesInstance = new Choices(el, {
                                                        searchEnabled: true,
                                                        searchPlaceholderValue: 'Cari field sistem...',
                                                        itemSelectText: '',
                                                        noResultsText: 'Field tidak ditemukan',
                                                        noChoicesText: 'Semua field sudah ditambahkan',
                                                        shouldSort: false,
                                                        classNames: {
                                                            containerOuter: ['choices', 'choices--custom'],
                                                        },
                                                    });
                                                    // Sync Choices.js value back to Livewire
                                                    el.addEventListener('change', () => {
                                                        @this.set('selectedSystemField', el.value);
                                                    });
                                                    // Reset after Livewire updates
                                                    this.$watch('$wire.selectedSystemField', (val) => {
                                                        if (!val && this.choicesInstance) {
                                                            this.choicesInstance.setChoiceByValue('');
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    }"
                                >
                                    <label for="sys_field" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Tambah Field Sistem (Tersinkron dengan Profil)</label>
                                    <select id="sys_field" wire:model="selectedSystemField" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                        <option value="">-- Pilih Field Sistem --</option>
                                        @foreach ($availableSystemFields as $sys)
                                            <option value="{{ $sys['key'] }}">{{ $sys['label'] }} ({{ $sys['group'] }})</option>
                                        @endforeach
                                    </select>
                                    @error('selectedSystemField') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <button type="button" wire:click="addSystemField" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 dark:bg-slate-200 dark:hover:bg-white text-white dark:text-slate-900 font-semibold rounded-xl text-sm transition-all whitespace-nowrap">
                                    Tambah Field
                                </button>
                            </div>

                            @error('fields') <p class="text-rose-500 text-sm font-semibold mb-4">{{ $message }}</p> @enderror

                            <!-- Fields List -->
                            <div class="space-y-4">
                                @forelse ($fields as $index => $field)
                                    <div wire:key="field-{{ $index }}-{{ $field['field_type'] }}" class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/60 dark:border-slate-800 flex items-start gap-3 transition-colors duration-200">
                                        <!-- Reorder controls -->
                                        <div class="flex flex-col gap-1 pt-1.5">
                                            <button type="button" wire:click="moveUp({{ $index }})" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded transition-all" title="Naikkan" {{ $index === 0 ? 'disabled' : '' }}>
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button type="button" wire:click="moveDown({{ $index }})" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded transition-all" title="Turunkan" {{ $index === count($fields) - 1 ? 'disabled' : '' }}>
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>

                                        <!-- Field Config -->
                                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                            <!-- Key -->
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Key (Unik)</label>
                                                <input type="text" wire:model.blur="fields.{{ $index }}.field_key" {{ $field['is_system_field'] ? 'readonly' : '' }} class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors {{ $field['is_system_field'] ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed' : '' }}">
                                                @error("fields.{$index}.field_key") <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                            </div>

                                            <!-- Label -->
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Label</label>
                                                <input type="text" wire:model.blur="fields.{{ $index }}.field_label" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                                @error("fields.{$index}.field_label") <p class="text-rose-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                            </div>

                                            <!-- Type -->
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Tipe</label>
                                                @if ($field['is_system_field'])
                                                    <input type="text" readonly value="{{ $field['field_type'] }}" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed px-3 py-1.5 text-xs">
                                                @else
                                                    <select wire:model.live="fields.{{ $index }}.field_type" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                                        <option value="text">Teks Singkat</option>
                                                        <option value="textarea">Teks Panjang</option>
                                                        <option value="dropdown">Pilihan Ganda</option>
                                                        <option value="boolean">Ya / Tidak</option>
                                                        <option value="number">Angka</option>
                                                        <option value="date">Tanggal</option>
                                                    </select>
                                                @endif
                                            </div>

                                            <!-- Group Name -->
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Kelompok Field</label>
                                                <input type="text" wire:model.blur="fields.{{ $index }}.group_name" placeholder="Contoh: Kesehatan" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                            </div>

                                            <!-- Dropdown Options (shows only if type is dropdown) -->
                                            @if ($field['field_type'] === 'dropdown')
                                                <div class="col-span-full">
                                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Pilihan Opsi (Pisahkan dengan koma)</label>
                                                    <input type="text" wire:model.blur="fields.{{ $index }}.field_options" placeholder="Contoh: A, B, AB, O" class="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-1.5 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                                    <p class="text-[9px] text-slate-400 mt-0.5">Tulis semua pilihan yang dipisahkan tanda koma.</p>
                                                </div>
                                            @endif

                                            <!-- More settings (Required tag / info) -->
                                            <div class="col-span-full flex items-center justify-between mt-2 pt-2 border-t border-slate-200/40 dark:border-slate-700/50">
                                                <div class="flex items-center gap-6">
                                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" wire:model="fields.{{ $index }}.is_required" class="w-3.5 h-3.5 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                                        <span class="text-xs font-semibold text-slate-500">Wajib Diisi</span>
                                                    </label>
                                                    
                                                    @if ($field['is_system_field'])
                                                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.5 rounded-full flex items-center gap-1">
                                                            <svg class="w-3 h-3 text-emerald-500 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg><span>Field Sistem</span>
                                                            @if ($field['profile_field_key'])
                                                                <span class="text-slate-400">&rarr; profil.{{ $field['profile_field_key'] }}</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-[10px] font-bold text-slate-500 bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded-full">Field Kustom</span>
                                                    @endif
                                                </div>

                                                <!-- Delete button -->
                                                <button type="button" wire:click="removeField({{ $index }})" class="text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-550/10 px-2 py-1 rounded-lg transition-all" title="Hapus Field">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center bg-slate-50 dark:bg-slate-800/20 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                                        <p class="text-sm text-slate-400">Belum ada field formulir. Tambah field sistem atau kustom di atas.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" wire:click="cancel" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-2xl text-sm transition-all">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-2xl text-sm shadow-lg shadow-emerald-600/10 hover:shadow-emerald-500/20 hover:-translate-y-0.5 transition-all duration-200">
                                Simpan Template
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>
