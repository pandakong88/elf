<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">CMS Landing Page</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola konten, teks informasi, pendaftaran, dan buku pedoman di Landing Page Pondok Al-Fithroh secara visual.</p>
        </div>
        
        <div>
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/10 flex items-center gap-2">
                <span wire:loading.remove wire:target="save">📁 Simpan Perubahan</span>
                <span wire:loading wire:target="save" class="inline-block animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </div>

    <!-- CMS Forms Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Form Fields -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Section 0: General Branding (Logo) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">Identitas Pondok (Logo)</h3>
                
                <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                    <div class="flex-1 space-y-1 text-center sm:text-left">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Upload Logo Pondok</span>
                        <span class="text-[10px] text-slate-400 block">Format wajib PNG, JPG, JPEG, atau SVG. Ukuran maksimal 2MB.</span>
                        @if(!empty($existing_logo_url))
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $existing_logo_url }}" class="h-10 w-10 object-contain rounded-lg border border-slate-200 p-1 bg-white">
                                <span class="text-[10px] text-emerald-500 font-semibold">Logo Aktif Terunggah</span>
                            </div>
                        @else
                            <span class="text-[10px] text-amber-500 block mt-1">⚠ Belum ada logo terunggah.</span>
                        @endif
                    </div>
                    
                    <div class="relative">
                        <input type="file" wire:model="logo_file" id="logo_file" class="hidden">
                        <label for="logo_file" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700 cursor-pointer block whitespace-nowrap">
                            <span wire:loading.remove wire:target="logo_file">Pilih Logo</span>
                            <span wire:loading wire:target="logo_file" class="inline-block animate-spin rounded-full h-2.5 w-2.5 border-2 border-emerald-500 border-t-transparent mr-1"></span>
                            <span wire:loading wire:target="logo_file">Mengunggah...</span>
                        </label>
                    </div>
                </div>
                @error('logo_file') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
            </div>
            
            <!-- Section 1: Hero Banner -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">1. Bagian Hero (Halaman Utama)</h3>
                
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Judul Utama Hero (Tagline)</label>
                        <input type="text" wire:model="hero_title" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('hero_title') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Sub-Judul Hero (Deskripsi Singkat)</label>
                        <textarea wire:model="hero_subtitle" rows="3" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                        @error('hero_subtitle') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                        <div class="flex-1 space-y-1 text-center sm:text-left">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Gambar Latar Belakang / Banner Hero</span>
                            <span class="text-[10px] text-slate-400 block">Format wajib PNG, JPG, JPEG, atau WEBP. Ukuran maksimal 3MB.</span>
                            @if(!empty($existing_hero_image_url))
                                <div class="mt-2 flex items-center gap-2">
                                    <img src="{{ $existing_hero_image_url }}" class="h-10 w-20 object-cover rounded-lg border border-slate-200">
                                    <span class="text-[10px] text-emerald-500 font-semibold">Banner Aktif Terpasang</span>
                                </div>
                            @else
                                <span class="text-[10px] text-amber-500 block mt-1">⚠ Menggunakan default ilustrasi gradient.</span>
                            @endif
                        </div>
                        
                        <div class="relative">
                            <input type="file" wire:model="hero_image_file" id="hero_image_file" class="hidden">
                            <label for="hero_image_file" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700 cursor-pointer block whitespace-nowrap">
                                <span wire:loading.remove wire:target="hero_image_file">Pilih Banner</span>
                                <span wire:loading wire:target="hero_image_file" class="inline-block animate-spin rounded-full h-2.5 w-2.5 border-2 border-emerald-500 border-t-transparent mr-1"></span>
                                <span wire:loading wire:target="hero_image_file">Mengunggah...</span>
                            </label>
                        </div>
                    </div>
                    @error('hero_image_file') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 2: Profil & Visi Misi -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">2. Bagian Profil, Visi & Misi</h3>
                
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Profil Singkat Pondok</label>
                        <textarea wire:model="about_profile" rows="4" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                        @error('about_profile') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Visi Pondok</label>
                        <textarea wire:model="about_vision" rows="3" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                        @error('about_vision') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Misi Pondok (Tulis baris baru untuk setiap poin misi)</label>
                        <textarea wire:model="about_mission" rows="5" placeholder="Contoh:&#10;1. Menyelenggarakan pendidikan...&#10;2. Menanamkan adab..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                        @error('about_mission') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Buku Pedoman Santri (PDF Upload) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">3. Buku Pedoman Santri</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Judul Buku Pedoman</label>
                            <input type="text" wire:model="pedoman_title" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            @error('pedoman_title') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Deskripsi Singkat</label>
                            <input type="text" wire:model="pedoman_description" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            @error('pedoman_description') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="p-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl flex flex-col sm:flex-row items-center gap-4">
                        <div class="flex-1 space-y-1 text-center sm:text-left">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Upload File Pedoman Baru (PDF)</span>
                            <span class="text-[10px] text-slate-400 block">Format wajib PDF, ukuran file maksimal 10 MB.</span>
                            @if(!empty($existing_pedoman_url))
                                <span class="text-[10px] text-emerald-500 font-semibold block mt-1">✔ File aktif terupload: <a href="{{ $existing_pedoman_url }}" target="_blank" class="underline">Unduh Buku Pedoman Saat Ini</a></span>
                            @else
                                <span class="text-[10px] text-amber-500 block mt-1">⚠ Belum ada buku pedoman yang diunggah.</span>
                            @endif
                        </div>
                        
                        <div class="relative">
                            <input type="file" wire:model="pedoman_file" id="pedoman_file" class="hidden">
                            <label for="pedoman_file" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all border border-slate-200 dark:border-slate-700 cursor-pointer block whitespace-nowrap">
                                <span wire:loading.remove wire:target="pedoman_file">Pilih Berkas PDF</span>
                                <span wire:loading wire:target="pedoman_file" class="inline-block animate-spin rounded-full h-2.5 w-2.5 border-2 border-emerald-500 border-t-transparent mr-1"></span>
                                <span wire:loading wire:target="pedoman_file">Mengunggah...</span>
                            </label>
                        </div>
                    </div>
                    @error('pedoman_file') <span class="text-[10px] text-rose-500 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 4: Kontak & Informasi Pendaftaran -->
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2 uppercase tracking-wider">4. Bagian Kontak & Pendaftaran</h3>
                
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Informasi Penerimaan Santri Baru (PSB)</label>
                        <textarea wire:model="registration_info" rows="3" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"></textarea>
                        @error('registration_info') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Alamat Lengkap Sekretariat</label>
                        <input type="text" wire:model="contact_address" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @error('contact_address') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nomor Telepon Humas (WhatsApp)</label>
                            <input type="text" wire:model="contact_phone" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            @error('contact_phone') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Email Humas</label>
                            <input type="email" wire:model="contact_email" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            @error('contact_email') <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Right Column: Sidebar Guidance -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5 text-xs text-amber-600 dark:text-amber-400 space-y-3">
                <div class="flex items-center gap-2 font-bold">
                    <span>💡</span>
                    <span>Petunjuk Pengisian Konten</span>
                </div>
                <ul class="list-disc pl-4 space-y-2 leading-relaxed">
                    <li>Semua teks di kolom sebelah kiri akan langsung ter-update di landing page utama begitu tombol **Simpan** ditekan.</li>
                    <li>Untuk Misi Pondok, harap memisahkan setiap butir misi dengan menekan tombol **Enter** agar tata letak list/bullet di landing page tetap rapi secara otomatis.</li>
                    <li>Pastikan ukuran file PDF Buku Pedoman tidak melebihi 10MB sebelum mengunggahnya.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
