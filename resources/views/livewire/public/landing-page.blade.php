<div class="space-y-32 pb-32 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-950 pt-24 pb-36 text-white">
        <!-- High-quality professional background overlay -->
        <div class="absolute inset-0 bg-cover bg-center opacity-15 mix-blend-overlay" style="background-image: url('https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&q=80&w=1600')"></div>
        
        <!-- Animated background glow blobs -->
        <div class="absolute top-1/4 left-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-widest">
                        Portal Resmi Al-Fithroh
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1] font-serif-display text-white">
                        {{ $data['hero_title'] }}
                    </h1>
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ $data['hero_subtitle'] }}
                    </p>
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                        @auth
                            <a href="/dashboard" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-lg shadow-emerald-500/20 hover:scale-[1.02] flex items-center gap-2">
                                <span>Masuk Dashboard</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <a href="/portal-wali" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-lg shadow-emerald-500/20 hover:scale-[1.02] flex items-center gap-2">
                                <span>Portal Wali Santri</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <a href="/login" class="px-6 py-3.5 border border-slate-700 hover:bg-slate-900 text-slate-200 rounded-2xl text-xs font-bold transition-all">Login Pengurus</a>
                        @endauth
                    </div>
                </div>

                <!-- Right Visual Element (Modern Framed Photography) -->
                <div class="lg:col-span-5 relative hidden lg:block">
                    <div class="w-full aspect-square relative flex items-center justify-center">
                        <!-- Main Card with Premium Unsplash Image -->
                        <div class="w-80 h-96 rounded-[2.5rem] bg-gradient-to-tr from-slate-900 via-emerald-950 to-slate-900 border border-emerald-500/20 p-2 shadow-2xl relative overflow-hidden group">
                            <img src="https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&q=80&w=800" alt="Islamic Study" class="w-full h-full object-cover rounded-[2.2rem] group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent rounded-[2.2rem]"></div>
                            
                            <div class="absolute bottom-6 left-6 right-6 text-white space-y-1">
                                <span class="text-[9px] uppercase tracking-wider font-extrabold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20 inline-block">Masyayikh Mandate</span>
                                <h3 class="text-lg font-bold font-serif-display mt-1">Sanad Keilmuan Mutawatir</h3>
                                <p class="text-[10px] text-slate-300">Menjaga kemurnian ajaran Ahlussunnah wal Jama'ah sejak 1970.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Minimalist Stats Section (Overlapping) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white/90 dark:bg-slate-900/90 border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 rounded-3xl shadow-xl backdrop-blur-md hover:translate-y-[-2px] transition-all text-center space-y-1">
                <span class="text-3xl sm:text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 block tracking-tight">1.200+</span>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Santri Aktif</span>
            </div>
            
            <div class="bg-white/90 dark:bg-slate-900/90 border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 rounded-3xl shadow-xl backdrop-blur-md hover:translate-y-[-2px] transition-all text-center space-y-1">
                <span class="text-3xl sm:text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 block tracking-tight">80+</span>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Ustaz & Pembimbing</span>
            </div>

            <div class="bg-white/90 dark:bg-slate-900/90 border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 rounded-3xl shadow-xl backdrop-blur-md hover:translate-y-[-2px] transition-all text-center space-y-1">
                <span class="text-3xl sm:text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 block tracking-tight">25+</span>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Tahun Mengabdi</span>
            </div>

            <div class="bg-white/90 dark:bg-slate-900/90 border border-slate-200/50 dark:border-slate-800/60 p-6 sm:p-8 rounded-3xl shadow-xl backdrop-blur-md hover:translate-y-[-2px] transition-all text-center space-y-1">
                <span class="text-3xl sm:text-4xl font-extrabold text-emerald-600 dark:text-emerald-400 block tracking-tight">12</span>
                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Asrama Komplek</span>
            </div>
        </div>
    </section>

    <!-- Profil Pondok Section -->
    <section id="profil" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Side: Image with elegant border grid -->
            <div class="lg:col-span-5 relative">
                <div class="w-full aspect-[4/3] rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-200/40 dark:border-slate-800/60 group">
                    <img src="https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&q=80&w=800" alt="Pesantren Atmosphere" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <!-- Accent background element -->
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl"></div>
            </div>
            
            <!-- Right Side: Content -->
            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-2">
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Tentang Pesantren</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Profil Al-Fithroh</h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    {{ $data['about_profile'] }}
                </p>
            </div>
        </div>
    </section>

    <!-- Kenapa Memilih Kami Section (Value Proposition) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-2 text-center mb-16">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Keunggulan Kami</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Pilar Utama Pendidikan</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-lg mx-auto">Kami menyinergikan pendidikan salafiyah dengan pendidikan formal modern guna melahirkan santri paripurna.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all space-y-4 relative group overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <!-- Custom SVG Icon: Book Open -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-emerald-500 transition-colors">Kajian Kitab Kuning</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Kurikulum salafiyah terstruktur dengan sanad keilmuan yang bersambung langsung ke muassis pesantren.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all space-y-4 relative group overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/30 text-amber-500 dark:text-amber-400 flex items-center justify-center">
                    <!-- Custom SVG Icon: Shield Check -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-amber-500 transition-colors">Program Tahfidzul Qur'an</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Program menghafal Al-Qur'an secara intensif dengan metode tahsin yang tepat dibimbing oleh pengajar berpengalaman.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all space-y-4 relative group overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-teal-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/30 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <!-- Custom SVG Icon: Academic Cap -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/> </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-teal-500 transition-colors">Pendidikan Formal</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Sekolah formal terakreditasi guna mempersiapkan santri unggul dalam ilmu sains dan teknologi masa kini.
                </p>
            </div>
        </div>
    </section>

    <!-- Dawuh Pengasuh (Premium Quote Spotlight) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-[2.5rem] bg-gradient-to-br from-emerald-950 to-slate-950 border border-emerald-500/20 p-8 sm:p-16 text-white shadow-2xl overflow-hidden">
            <div class="absolute -left-16 -top-16 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="absolute -right-16 -bottom-16 w-48 h-48 bg-amber-500/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 text-center space-y-6 max-w-3xl mx-auto">
                <span class="text-5xl text-amber-500 font-serif-display leading-none block">“</span>
                <h3 class="text-xl sm:text-2xl font-serif-display text-emerald-100 leading-relaxed italic">
                    {{ $data['about_vision'] }}
                </h3>
                <div class="w-12 h-0.5 bg-amber-500/50 mx-auto"></div>
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-wider font-extrabold text-amber-500 block">Visi & Tujuan Utama</span>
                    <span class="text-[11px] text-slate-400 block">Pondok Pesantren Al-Fithroh</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi & Misi Detail Section -->
    <section id="visi-misi" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Left Side: Vision Statement -->
            <div class="space-y-6 bg-white dark:bg-slate-900 p-8 sm:p-10 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white font-serif-display">Arah & Haluan Pesantren</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Kami merumuskan arah pembinaan santri agar tidak hanya pandai secara keilmuan akademis, tetapi juga memiliki adab yang luhur serta kesetiaan yang utuh terhadap manhaj Ahlussunnah wal Jama'ah.
                    </p>
                </div>
                <div class="pt-6 border-t border-slate-50 dark:border-slate-800/50 text-[10px] sm:text-xs text-slate-400">
                    Sistem evaluasi berkala menjamin mutu kualitas pengajaran.
                </div>
            </div>

            <!-- Right Side: Misi List -->
            <div class="bg-white dark:bg-slate-900 p-8 sm:p-10 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white font-serif-display">Misi Pengabdian</h3>
                </div>
                
                <ul class="space-y-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    @foreach(explode("\n", $data['about_mission']) as $index => $missionLine)
                        @if(trim($missionLine) !== '')
                            <li class="flex items-start gap-4 p-3 bg-slate-50 dark:bg-slate-950/40 rounded-2xl border border-slate-100 dark:border-slate-800/60 hover:translate-x-1 transition-transform">
                                <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <span>{{ preg_replace('/^\d+\.\s*/', '', $missionLine) }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <!-- Galeri Kegiatan Publik Section -->
    <section id="kegiatan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="space-y-2 mb-12 text-center">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Kilas Kegiatan</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Aktivitas & Galeri Santri</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-xl mx-auto">Dokumentasi kegiatan harian, kajian ilmiah, serta kreasi santri Al-Fithroh yang bersifat umum.</p>
        </div>

        @if($activities->isEmpty())
            <div class="p-16 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[2rem] text-slate-400 dark:text-slate-600 text-xs sm:text-sm font-semibold bg-white dark:bg-slate-900">
                Belum ada dokumentasi kegiatan publik terbaru saat ini.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($activities as $activity)
                    <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between hover:scale-[1.01] hover:shadow-md transition-all group">
                        <div>
                            <!-- Photo / Thumbnail -->
                            @if($activity->hasMedia('photos'))
                                <div class="overflow-hidden">
                                    <img src="{{ $activity->getFirstMediaUrl('photos') }}" alt="{{ $activity->name }}" class="w-full aspect-[16/10] object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                            @else
                                <div class="w-full aspect-[16/10] bg-slate-50 dark:bg-slate-950 flex items-center justify-center text-slate-400">
                                    <span class="text-xs font-semibold">Dokumentasi Kegiatan</span>
                                </div>
                            @endif
                            
                            <div class="p-6 space-y-3">
                                <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-md text-[9px] font-extrabold uppercase tracking-wider inline-block">
                                    {{ $activity->activityType->name ?? 'Kegiatan' }}
                                </span>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight leading-snug">
                                    {{ $activity->name }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                    {{ $activity->description }}
                                </p>
                            </div>
                        </div>

                        <div class="p-6 pt-0 border-t border-slate-50 dark:border-slate-800/50 flex items-center justify-between text-[10px] text-slate-400 font-medium">
                            <span>Oleh: {{ $activity->organization->name ?? 'Pondok' }}</span>
                            <span>{{ $activity->date->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Alur Pendaftaran Santri (Interactive Timeline) -->
    <section id="pendaftaran" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="space-y-2 text-center mb-16">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Prosedur PSB</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Langkah Pendaftaran Santri</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-lg mx-auto">Panduan langkah mudah mendaftarkan putra-putri Anda ke Pondok Pesantren Al-Fithroh.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            <!-- Step 1 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm text-center space-y-4 relative group">
                <span class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm mx-auto shadow-md shadow-emerald-500/20">1</span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white font-serif-display">Lengkapi Berkas</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Siapkan FC KK, Akta Kelahiran, Surat Keterangan Sehat, serta Pas Foto santri terbaru sebelum mendaftar.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm text-center space-y-4 relative group">
                <span class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm mx-auto shadow-md shadow-emerald-500/20">2</span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white font-serif-display">Pendaftaran & Tes</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Kunjungi kantor sekretariat pondok untuk menyerahkan berkas dan melaksanakan tes lisan dasar santri.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm text-center space-y-4 relative group">
                <span class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm mx-auto shadow-md shadow-emerald-500/20">3</span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white font-serif-display">Mulai Mukim</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Setelah dinyatakan lolos, lakukan registrasi ulang dan penempatan kamar asrama untuk mulai mukim/belajar.
                </p>
            </div>
        </div>
    </section>

    <!-- Buku Pedoman Santri Section -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="p-8 sm:p-16 bg-gradient-to-br from-emerald-800 to-emerald-950 rounded-[2rem] text-white shadow-2xl relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-8 group">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/5 rounded-full blur-xl group-hover:scale-110 transition-transform"></div>
            
            <div class="space-y-3 text-center sm:text-left max-w-xl relative z-10">
                <h3 class="text-xl sm:text-2xl font-bold font-serif-display tracking-tight">{{ $data['pedoman_title'] }}</h3>
                <p class="text-xs text-emerald-100 leading-relaxed max-w-md">
                    {{ $data['pedoman_description'] }}
                </p>
            </div>
            
            <div class="relative z-10">
                @if(!empty($data['pedoman_file_url']))
                    <a href="{{ $data['pedoman_file_url'] }}" target="_blank" class="px-6 py-3.5 bg-white text-emerald-900 hover:bg-emerald-50 rounded-2xl text-xs font-bold transition-all shadow-md inline-block whitespace-nowrap hover:scale-105">
                        Unduh Buku Pedoman
                    </a>
                @else
                    <button disabled class="px-6 py-3.5 bg-white/10 text-white/50 rounded-2xl text-xs font-bold cursor-not-allowed whitespace-nowrap border border-white/10">
                        Segera Hadir
                    </button>
                @endif
            </div>
        </div>
    </section>

    <!-- Informasi Pendaftaran & Kontak Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Informasi Pendaftaran -->
            <div class="p-8 sm:p-10 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm space-y-6 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white font-serif-display">Ketentuan Pendaftaran</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ $data['registration_info'] }}
                    </p>
                </div>
                <div class="p-4 bg-amber-500/5 border border-amber-500/10 rounded-2xl text-[10px] text-amber-600 dark:text-amber-400 leading-relaxed font-semibold">
                    💡 Catatan: Administrasi keuangan pendaftaran bagi santri baru dapat diangsur selama 3 bulan pertama sejak santri mulai mukim di asrama.
                </div>
            </div>

            <!-- Kontak Pondok -->
            <div class="p-8 sm:p-10 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm space-y-8 flex flex-col justify-between">
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white font-serif-display">Hubungi Humas</h3>
                    
                    <div class="space-y-4 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                        <div class="flex items-start gap-4">
                            <span class="text-lg text-emerald-600 dark:text-emerald-400">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <span>{{ $data['contact_address'] }}</span>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-lg text-emerald-600 dark:text-emerald-400">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <span>{{ $data['contact_phone'] }}</span>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-lg text-emerald-600 dark:text-emerald-400">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <span>{{ $data['contact_email'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 dark:border-slate-800/50 flex items-center justify-between text-[11px] font-semibold">
                    <span class="text-slate-400">Sekretariat Al-Fithroh</span>
                    <a href="/login" class="text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">Akses Portal Wali &rarr;</a>
                </div>
            </div>
        </div>
    </section>
</div>
