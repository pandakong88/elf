<div class="space-y-8" x-data="{ iframeSrc: '{{ url('/') }}' }">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">CMS Landing Page</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800">
                    🟢 Live Preview Ready
                </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola konten, galeri kegiatan multi-foto, informasi kontak, dan buku pedoman secara visual.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" target="_blank" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Landing Page ↗</span>
            </a>
            @if($activeTab === 'content')
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                        @click="setTimeout(() => { $refs.previewIframe.src = $refs.previewIframe.src }, 1200)"
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/10 flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">📁 Simpan & Update Konten</span>
                    <span wire:loading wire:target="save" class="inline-block animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            @else
                <button type="button" wire:click="openCreateActivityModal"
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/10 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Kegiatan Baru</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-1">
        <button type="button" wire:click="setTab('content')"
                class="px-4 py-3 rounded-t-2xl text-xs font-bold transition-all flex items-center gap-2 border-b-2 {{ $activeTab === 'content' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Pengaturan Konten & Media Utama</span>
        </button>

        <button type="button" wire:click="setTab('activities')"
                class="px-4 py-3 rounded-t-2xl text-xs font-bold transition-all flex items-center gap-2 border-b-2 {{ $activeTab === 'activities' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Kelola Galeri & Dokumentasi Kegiatan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeTab === 'activities' ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                {{ $activities->count() }}
            </span>
        </button>
    </div>

    <!-- CMS Forms Grid + Live Preview Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Dynamic Tab Content (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            @if($activeTab === 'content')
                {{-- TAB 1: PENGATURAN KONTEN UTAMA --}}
                
                <!-- Section 0: Identitas Logo -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">Identitas Pondok (Logo)</h3>
                    
                    <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                        <div class="flex-1 space-y-1 text-center sm:text-left">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Upload Logo Pondok</span>
                            <span class="text-[10px] text-slate-400 block">Format PNG, JPG, JPEG, WEBP, atau SVG (maks 5MB).</span>
                            @if ($logo_file)
                                <div class="mt-2 flex items-center gap-2.5 p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                    <img src="{{ $logo_file->temporaryUrl() }}" class="h-10 w-10 object-contain rounded-lg border border-emerald-300 p-1 bg-white">
                                    <div>
                                        <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold block">✔ Logo Baru Dipilih!</span>
                                        <span class="text-[9px] text-slate-500 dark:text-slate-400 block">Klik <strong>Simpan & Update</strong> untuk menyimpan.</span>
                                    </div>
                                </div>
                            @elseif ($existing_logo_url)
                                <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                    <img src="{{ $existing_logo_url }}" class="h-8 w-8 object-contain rounded border p-0.5 bg-white">
                                    <span>Logo saat ini aktif.</span>
                                </div>
                            @endif
                        </div>
                        <label class="cursor-pointer px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                            <span>Pilih File...</span>
                            <input type="file" wire:model="logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="hidden">
                        </label>
                    </div>
                </div>

                <!-- Section 1: Hero Banner & Kaligrafi -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">Hero Section & Kaligrafi</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Utama Hero</label>
                            <input type="text" wire:model.defer="hero_title" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            @error('hero_title') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sub-Judul Hero</label>
                            <textarea wire:model.defer="hero_subtitle" rows="3" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                            @error('hero_subtitle') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-1 space-y-1 text-center sm:text-left">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Upload Banner / Kaligrafi Arab Hero</span>
                                <span class="text-[10px] text-slate-400 block">Gambar kaligrafi transparan (maks 5MB).</span>
                                @if ($hero_image_file)
                                    <div class="mt-2 flex items-center gap-2.5 p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                        <img src="{{ $hero_image_file->temporaryUrl() }}" class="h-10 w-auto object-contain rounded border p-1 bg-white">
                                        <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold">✔ Banner Baru Dipilih!</span>
                                    </div>
                                @elseif ($existing_hero_image_url)
                                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                        <img src="{{ $existing_hero_image_url }}" class="h-8 w-auto object-contain rounded border p-1 bg-white">
                                        <span>Banner saat ini aktif.</span>
                                    </div>
                                @endif
                            </div>
                            <label class="cursor-pointer px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                                <span>Pilih Banner...</span>
                                <input type="file" wire:model="hero_image_file" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Profil & Visi Misi -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">Profil & Visi Misi</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Profil Singkat Pondok</label>
                            <textarea wire:model.defer="about_profile" rows="3" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Visi Utama</label>
                            <textarea wire:model.defer="about_vision" rows="2" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Misi Pondok (1 poin per baris)</label>
                            <textarea wire:model.defer="about_mission" rows="4" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>

                        <!-- Foto Profil Gedung -->
                        <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-1 space-y-1 text-center sm:text-left">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Foto Profil Gedung Pondok</span>
                                <span class="text-[10px] text-slate-400 block">Foto utama gedung pondok di section profil (maks 5MB).</span>
                                @if ($about_image_file)
                                    <div class="mt-2 flex items-center gap-2.5 p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                        <img src="{{ $about_image_file->temporaryUrl() }}" class="h-10 w-10 object-cover rounded-lg border p-0.5">
                                        <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold">✔ Foto Gedung Dipilih!</span>
                                    </div>
                                @elseif ($existing_about_image_url)
                                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                        <img src="{{ $existing_about_image_url }}" class="h-8 w-8 object-cover rounded border p-0.5">
                                        <span>Foto gedung aktif saat ini.</span>
                                    </div>
                                @endif
                            </div>
                            <label class="cursor-pointer px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                                <span>Pilih Foto Gedung...</span>
                                <input type="file" wire:model="about_image_file" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kontak & Sosial Media -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">Kontak & Sosial Media</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Pesantren</label>
                            <input type="text" wire:model.defer="contact_address" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Humas</label>
                                <input type="email" wire:model.defer="contact_email" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Instagram (@username)</label>
                                <input type="text" wire:model.defer="ig_username" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Link Google Maps</label>
                            <input type="url" wire:model.defer="gmaps_url" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WA Admin Putra 1</label>
                                <input type="text" wire:model.defer="wa_putra1" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WA Admin Putra 2</label>
                                <input type="text" wire:model.defer="wa_putra2" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WA Admin Putri</label>
                                <input type="text" wire:model.defer="wa_putri" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Buku Pedoman PDF -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">Buku Pedoman Santri (PDF)</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Dokumen Pedoman</label>
                            <input type="text" wire:model.defer="pedoman_title" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Ringkas Pedoman</label>
                            <textarea wire:model.defer="pedoman_description" rows="2" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>

                        <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-1 space-y-1 text-center sm:text-left">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Upload Berkas Buku Pedoman (PDF)</span>
                                <span class="text-[10px] text-slate-400 block">Format PDF (maksimal 10MB).</span>
                                @if ($pedoman_file)
                                    <div class="mt-2 flex items-center gap-2 p-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">📄 Berkas PDF Baru Dipilih!</span>
                                    </div>
                                @elseif ($existing_pedoman_url)
                                    <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                        <span>File PDF aktif:</span>
                                        <a href="{{ $existing_pedoman_url }}" target="_blank" class="text-emerald-600 underline font-bold">Lihat File ↗</a>
                                    </div>
                                @endif
                            </div>
                            <label class="cursor-pointer px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                                <span>Pilih PDF...</span>
                                <input type="file" wire:model="pedoman_file" accept=".pdf" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

            @else
                {{-- TAB 2: KELOLA GALERI & DOKUMENTASI KEGIATAN --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 font-serif-display">Dokumentasi & Galeri Kegiatan Santri</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola foto kegiatan publik yang tampil di Landing Page. Setiap kegiatan dapat memiliki beberapa foto sekaligus.</p>
                        </div>
                        <button type="button" wire:click="openCreateActivityModal"
                                class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/20 flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Kegiatan</span>
                        </button>
                    </div>

                    @if($activities->isEmpty())
                        <div class="p-12 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                                📷
                            </div>
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada kegiatan yang ditambahkan</h4>
                            <p class="text-xs text-slate-400">Klik tombol di atas untuk menambah kegiatan & upload foto dokumentasi.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($activities as $act)
                                @php
                                    $photoCount = $act->getMedia('photos')->count();
                                    $coverUrl = $act->getFirstPhotoUrl();
                                @endphp
                                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 shadow-sm flex flex-col justify-between space-y-4 hover:border-emerald-200 dark:hover:border-emerald-800 transition-all">
                                    
                                    {{-- Photo Cover & Header Info --}}
                                    <div class="space-y-3">
                                        <div class="relative w-full aspect-[16/9] rounded-xl overflow-hidden bg-slate-950 border border-slate-100 dark:border-slate-800">
                                            @if($coverUrl)
                                                <img src="{{ $coverUrl }}" alt="{{ $act->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-500 bg-slate-900 text-xs">
                                                    <span>Belum Ada Foto</span>
                                                </div>
                                            @endif

                                            <div class="absolute top-2 left-2 flex items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $act->visibility === 'umum' ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-300' }}">
                                                    {{ $act->visibility === 'umum' ? 'Publik' : 'Internal' }}
                                                </span>
                                            </div>

                                            <div class="absolute top-2 right-2">
                                                <span class="px-2 py-1 rounded-lg bg-slate-950/70 backdrop-blur-md text-amber-300 text-[10px] font-extrabold flex items-center gap-1 border border-white/10">
                                                    📷 {{ $photoCount }} Foto
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-bold mb-1">
                                                <span>{{ $act->date ? $act->date->format('d M Y') : '-' }}</span>
                                                <span>{{ $act->activityType->name ?? 'Kegiatan' }}</span>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-snug line-clamp-2">{{ $act->name }}</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-1">{{ $act->description }}</p>
                                        </div>

                                        {{-- Existing Photos Thumbnail List --}}
                                        @if($photoCount > 0)
                                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                                <span class="text-[10px] font-bold text-slate-400 block mb-2">Foto Dokumentasi (Klik X untuk Hapus Foto):</span>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($act->getMedia('photos') as $media)
                                                        <div class="relative group w-12 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                                                            <img src="{{ route('media.stream', $media->id) }}" class="w-full h-full object-cover">
                                                            <button type="button" wire:click="deletePhoto('{{ $act->id }}', '{{ $media->id }}')"
                                                                    wire:confirm="Yakin ingin menghapus foto dokumentasi ini?"
                                                                    class="absolute inset-0 bg-rose-600/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold">
                                                                ✕
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                                        <button type="button" wire:click="editActivity('{{ $act->id }}')"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                                            <span>✏ Edit</span>
                                        </button>
                                        <button type="button" wire:click="deleteActivity('{{ $act->id }}')"
                                                wire:confirm="Apakah Anda yakin ingin menghapus kegiatan ini secara permanen?"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                                            <span>🗑 Hapus</span>
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Right Column: Live Interactive Preview Sidebar (5 cols sticky) -->
        <div class="lg:col-span-5 space-y-4 sticky top-6">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-lg overflow-hidden flex flex-col">
                
                <!-- Preview Toolbar -->
                <div class="px-4 py-3 bg-slate-800 text-white flex items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold">Live Preview</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="$refs.previewIframe.src = $refs.previewIframe.src" 
                                class="px-2.5 py-1 bg-white/10 hover:bg-white/20 rounded-lg text-[10px] font-semibold transition-all flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Refresh</span>
                        </button>
                        <a href="{{ url('/') }}" target="_blank" class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 rounded-lg text-[10px] font-semibold transition-all">
                            Tab Baru ↗
                        </a>
                    </div>
                </div>

                <!-- Iframe Wrapper -->
                <div class="relative w-full bg-slate-950 overflow-hidden" style="height: 620px;">
                    <iframe x-ref="previewIframe" :src="iframeSrc" 
                            class="w-full h-full border-0 select-none pointer-events-auto"
                            title="Preview Landing Page">
                    </iframe>
                </div>

                <!-- Footer Hint -->
                <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 text-[10px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
                    <span>💡 Perubahan langsung dapat dilihat setelah di-refresh.</span>
                </div>
            </div>

            <!-- Extra Help Card -->
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 text-xs text-amber-700 dark:text-amber-400 space-y-2">
                <div class="flex items-center gap-2 font-bold">
                    <span>💡 Petunjuk Galeri Multi-Foto:</span>
                </div>
                <ul class="list-disc pl-4 space-y-1 text-[11px] leading-relaxed">
                    <li>Saat menambah kegiatan, Anda dapat memilih <strong>beberapa foto sekaligus</strong> (Multi-Select).</li>
                    <li>Foto utama pertama akan otomatis dijadikan foto sampul di Landing Page.</li>
                    <li>Pengunjung web dapat mengklik kegiatan untuk melihat seluruh slide foto dokumentasi.</li>
                </ul>
            </div>

        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL FORM TAMBAH / EDIT KEGIATAN & MULTI-PHOTO UPLOAD       -->
    <!-- ============================================================ -->
    @if($showActivityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" wire:click="$set('showActivityModal', false)"></div>

            <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 overflow-hidden z-10 space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 font-serif-display">
                            {{ $editingActivityId ? 'Edit Kegiatan Santri' : 'Tambah Dokumentasi Kegiatan Baru' }}
                        </h3>
                        <p class="text-xs text-slate-400">Isi detail kegiatan & upload foto dokumentasi publik.</p>
                    </div>
                    <button type="button" wire:click="$set('showActivityModal', false)" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-slate-100 flex items-center justify-center">✕</button>
                </div>

                <form wire:submit.prevent="saveActivity" class="space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Nama Kegiatan</label>
                        <input type="text" wire:model.defer="act_name" placeholder="contoh: Kajian Kitab Bulanan & Ziarah Masyayikh" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('act_name') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Kegiatan</label>
                            <input type="date" wire:model.defer="act_date" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                            @error('act_date') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Publikasi</label>
                            <select wire:model.defer="act_visibility" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="umum">🟢 Publik (Tampil di Landing Page)</option>
                                <option value="internal">🔒 Internal (Santri Only / Draft)</option>
                            </select>
                            @error('act_visibility') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Kegiatan</label>
                        <textarea wire:model.defer="act_description" rows="3" placeholder="Tuliskan gambaran ringkas kegiatan ini..." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        @error('act_description') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Multi-Photo Upload Field -->
                    <div class="p-4 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl space-y-3 bg-slate-50/50 dark:bg-slate-950/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Upload Foto Dokumentasi (Multi-Select)</span>
                                <span class="text-[10px] text-slate-400 block">Anda bisa memilih **beberapa foto sekaligus** (PNG, JPG, WEBP, maks 5MB per foto).</span>
                            </div>
                            <label class="cursor-pointer px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-sm whitespace-nowrap">
                                <span>Pilih Foto...</span>
                                <input type="file" wire:model="new_photos" multiple accept="image/png,image/jpeg,image/webp" class="hidden">
                            </label>
                        </div>

                        <!-- Instant Temporary Upload Previews -->
                        @if ($new_photos)
                            <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block">✔ {{ count($new_photos) }} Foto Dipilih & Siap Diunggah:</span>
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                    @foreach($new_photos as $idx => $photo)
                                        <div class="relative group aspect-square rounded-xl overflow-hidden border border-emerald-300 dark:border-emerald-700">
                                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                            <button type="button" wire:click="removeNewPhoto({{ $idx }})"
                                                    class="absolute top-1 right-1 w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[10px] font-bold shadow-md hover:bg-rose-600">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('showActivityModal', false)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/20 flex items-center gap-2">
                            <span wire:loading.remove wire:target="saveActivity">Simpan Kegiatan</span>
                            <span wire:loading wire:target="saveActivity" class="inline-block animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></span>
                            <span wire:loading wire:target="saveActivity">Menyimpan...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
