<div class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors" x-data="{ showPedomanModal: false, showGalleryModal: false, activeActivity: null, activePhotoIdx: 0 }">

    {{-- ============================================================ --}}
    {{-- HERO SECTION                                                  --}}
    {{-- ============================================================ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-950 pt-24 pb-36 text-white">
        {{-- Background overlay dari CMS --}}
        @if(!empty($data['hero_image_url']))
            <div class="absolute inset-0 bg-cover bg-center opacity-20 mix-blend-overlay" style="background-image: url('{{ $data['hero_image_url'] }}')"></div>
        @endif

        {{-- Animated glow blobs --}}
        <div class="absolute top-1/4 left-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-amber-500/8 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                {{-- Left Content --}}
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
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
                            <a href="/dashboard" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-sm font-bold transition-all shadow-lg shadow-emerald-500/20 hover:scale-[1.02] flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                                <span>Masuk Dashboard</span>
                            </a>
                        @else
                            {{-- Tombol 1: Portal Wali Santri --}}
                            <a href="/portal-wali"
                               class="px-7 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-sm font-bold transition-all shadow-lg shadow-emerald-500/25 hover:scale-[1.02] flex items-center gap-2 group">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Portal Wali Santri</span>
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>

                            {{-- Tombol 2: Login Pengurus --}}
                            <a href="/login"
                               class="px-7 py-3.5 border border-slate-600 hover:border-slate-400 hover:bg-slate-800/60 text-slate-200 hover:text-white rounded-2xl text-sm font-bold transition-all backdrop-blur-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Login Pengurus</span>
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right Visual — Grand Floating Banner/Calligraphy --}}
                <div class="lg:col-span-5 relative flex items-center justify-center pt-8 lg:pt-0">
                    <div class="relative w-full max-w-md lg:max-w-none flex flex-col items-center justify-center group">
                        
                        {{-- Aura Glow Radial Background --}}
                        <div class="absolute w-72 h-72 sm:w-96 sm:h-96 bg-gradient-to-tr from-emerald-500/25 via-teal-400/20 to-amber-500/15 rounded-full blur-3xl -z-10 group-hover:scale-110 transition-transform duration-700"></div>
                        <div class="absolute w-64 h-64 bg-emerald-600/10 rounded-full blur-2xl -z-10 animate-pulse"></div>

                        {{-- Main Image Visual: Kaligrafi Arab --}}
                        @if(!empty($data['hero_image_url']))
                            <div class="relative z-10 flex flex-col items-center justify-center p-4">
                                <img src="{{ $data['hero_image_url'] }}" 
                                     alt="Banner Kaligrafi Al-Fithroh" 
                                     class="max-h-[380px] sm:max-h-[440px] lg:max-h-[480px] w-auto object-contain drop-shadow-[0_20px_40px_rgba(16,185,129,0.35)] group-hover:scale-105 transition-all duration-500 hover:drop-shadow-[0_25px_50px_rgba(16,185,129,0.45)]">
                            </div>
                        @else
                            {{-- Fallback: Large HD Logo --}}
                            @if(!empty($data['logo_url']))
                                <img src="{{ $data['logo_url'] }}" 
                                     alt="Logo Al-Fithroh" 
                                     class="max-h-[320px] w-auto object-contain drop-shadow-[0_20px_40px_rgba(16,185,129,0.35)] group-hover:scale-105 transition-transform duration-500">
                            @endif
                        @endif

                        {{-- Floating Glass Subtitle Badge --}}
                        <div class="mt-4 px-5 py-2.5 rounded-2xl bg-white/10 dark:bg-slate-900/60 backdrop-blur-md border border-white/15 dark:border-slate-800/80 shadow-xl flex items-center gap-3 group-hover:border-emerald-500/40 transition-colors">
                            @if(!empty($data['logo_url']))
                                <img src="{{ $data['logo_url'] }}" alt="Logo" class="h-6 max-h-6 w-auto object-contain flex-shrink-0" style="max-height: 24px; width: auto;">
                            @endif
                            <div class="text-left">
                                <h4 class="text-xs font-bold text-white tracking-tight leading-none">Pondok Pesantren Al-Fithroh</h4>
                                <p class="text-[10px] text-emerald-300 font-medium mt-0.5">Jejeran, Pleret, Bantul — Est. 1970</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- PROFIL PONDOK                                                 --}}
    {{-- ============================================================ --}}
    <section id="profil" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 scroll-mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {{-- Gambar Kiri --}}
            <div class="lg:col-span-5 relative">
                @if(!empty($data['about_image_url']))
                    <div class="w-full aspect-[4/5] max-h-[520px] rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-200/40 dark:border-slate-800/60 group relative">
                        <img src="{{ $data['about_image_url'] }}" alt="Gedung Utama Pondok Pesantren Al-Fithroh Jejeran Bantul" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent opacity-60"></div>
                        <div class="absolute bottom-5 left-5 right-5 p-4 rounded-2xl bg-white/10 dark:bg-slate-900/60 backdrop-blur-md border border-white/20 dark:border-slate-700/50 text-white shadow-lg">
                            <h4 class="text-xs font-bold font-serif-display">Gedung Utama Al-Fithroh</h4>
                            <p class="text-[10px] text-emerald-300 font-medium">Jejeran, Wonokromo, Pleret, Bantul</p>
                        </div>
                    </div>
                @else
                    {{-- Placeholder gambar pondok --}}
                    <div class="w-full aspect-[4/3] rounded-[2.5rem] bg-gradient-to-br from-emerald-900 via-slate-800 to-slate-900 shadow-2xl border border-emerald-500/10 overflow-hidden relative flex items-center justify-center group">
                        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, #10b981 1px, transparent 1px); background-size: 24px 24px;"></div>
                        <div class="text-center space-y-3 relative z-10 p-8">
                            <div class="w-20 h-20 rounded-[1.5rem] bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto">
                                <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Foto Pondok Pesantren</p>
                            <p class="text-[10px] text-slate-500">Dapat diupload via CMS</p>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                    </div>
                @endif
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            {{-- Konten Kanan --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-2">
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Tentang Pesantren</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Profil Al-Fithroh</h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    {{ $data['about_profile'] }}
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold border border-emerald-100 dark:border-emerald-900/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Kajian Kitab Kuning
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-xs font-semibold border border-amber-100 dark:border-amber-900/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Tahfidzul Qur'an
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 dark:bg-teal-950/30 text-teal-700 dark:text-teal-400 text-xs font-semibold border border-teal-100 dark:border-teal-900/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        Madrasah Diniyah
                    </span>

                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- QUOTE SPOTLIGHT — VISI UTAMA                                  --}}
    {{-- ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28">
        <div class="relative rounded-[2.5rem] bg-gradient-to-br from-emerald-950 to-slate-950 border border-emerald-500/20 p-10 sm:p-16 text-white shadow-2xl overflow-hidden">
            <div class="absolute -left-16 -top-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-amber-500/8 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>

            <div class="relative z-10 text-center space-y-6 max-w-3xl mx-auto">
                <span class="text-6xl text-amber-400/70 font-serif-display leading-none block">"</span>
                <h3 class="text-xl sm:text-2xl font-serif-display text-emerald-100 leading-relaxed italic -mt-4">
                    {{ $data['about_vision'] }}
                </h3>
                <div class="w-12 h-0.5 bg-amber-500/50 mx-auto"></div>
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-widest font-extrabold text-amber-400 block">Visi & Tujuan Utama</span>
                    <span class="text-[11px] text-slate-400 block">Pondok Pesantren Al-Fithroh Jejeran Bantul</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MISI PENGABDIAN — full width                                  --}}
    {{-- ============================================================ --}}
    <section id="visi-misi" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-28 scroll-mt-24">
        <div class="space-y-2 text-center mb-12">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Komitmen Pondok</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Misi Pengabdian</h2>
        </div>

        <div class="bg-white dark:bg-slate-900 p-8 sm:p-12 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
            <ul class="space-y-4 text-sm sm:text-base text-slate-600 dark:text-slate-300">
                @php
                    $rawMisi = $data['about_mission'] ?? '';
                    $misiList = array_values(array_filter(
                        array_map('trim', explode("\n", $rawMisi)),
                        fn($line) => !empty($line)
                    ));
                @endphp
                @foreach($misiList as $i => $misi)
                    <li class="flex items-start gap-5 p-4 sm:p-5 bg-slate-50 dark:bg-slate-950/40 rounded-2xl border border-slate-100 dark:border-slate-800/60 hover:translate-x-1 hover:border-emerald-100 dark:hover:border-emerald-900/40 transition-all duration-200 group">
                        <span class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-md shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                            {{ $i + 1 }}
                        </span>
                        <span class="leading-relaxed">{{ preg_replace('/^\d+[\.\)]\s*/', '', $misi) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- GALERI KEGIATAN                                               --}}
    {{-- ============================================================ --}}
    <section id="kegiatan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28 scroll-mt-24">
        <div class="space-y-2 mb-12 text-center">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Kilas Kegiatan</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Aktivitas & Galeri Santri</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-xl mx-auto">Dokumentasi kegiatan harian, kajian ilmiah, serta kreasi santri Al-Fithroh yang bersifat umum.</p>
        </div>

        @if($activities->isEmpty())
            {{-- Empty state --}}
            <div class="p-16 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[2rem] bg-white dark:bg-slate-900 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-400 dark:text-slate-600 text-sm font-semibold">Belum ada dokumentasi kegiatan publik terbaru saat ini.</p>
                <p class="text-slate-400 dark:text-slate-600 text-xs">Ikuti kegiatan kami di media sosial</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($activities as $activity)
                    @php
                        $photoUrls = $activity->getPhotoUrls();
                        $coverUrl  = $activity->getFirstPhotoUrl();
                        $actPayload = json_encode([
                            'title' => $activity->name,
                            'category' => $activity->activityType->name ?? 'Kegiatan',
                            'date' => $activity->date ? $activity->date->format('d M Y') : '',
                            'org' => $activity->organization->name ?? 'Pondok Pesantren Al-Fithroh',
                            'description' => $activity->description,
                            'photos' => $photoUrls,
                        ]);
                    @endphp

                    <div @click='activeActivity = {{ $actPayload }}; activePhotoIdx = 0; showGalleryModal = true'
                         class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col hover:shadow-xl hover:scale-[1.01] transition-all duration-300 group cursor-pointer">
                        
                        {{-- Photo / Thumbnail --}}
                        <div class="relative overflow-hidden aspect-[16/10] bg-slate-950">
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $activity->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-emerald-950 to-slate-900 flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, #10b981 1px, transparent 1px); background-size: 18px 18px;"></div>
                                    <svg class="w-10 h-10 text-emerald-400/40 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            {{-- Photo Count Badge --}}
                            @if(count($photoUrls) > 0)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-950/70 backdrop-blur-md text-amber-300 text-[10px] font-extrabold flex items-center gap-1.5 border border-white/10 shadow-md">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                        <span>{{ count($photoUrls) }} Foto</span>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 flex flex-col p-6 space-y-3">
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-md text-[9px] font-extrabold uppercase tracking-wider inline-block w-fit">
                                {{ $activity->activityType->name ?? 'Kegiatan' }}
                            </span>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight leading-snug group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $activity->name }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed flex-1">
                                {{ $activity->description }}
                            </p>
                            <div class="pt-4 border-t border-slate-50 dark:border-slate-800/50 flex items-center justify-between text-[10px] text-slate-400 font-medium">
                                <span>{{ $activity->organization->name ?? 'Pondok' }}</span>
                                <span>{{ $activity->date->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Tombol Lihat Semua Kegiatan --}}
        @php
            $cleanIg = ltrim($data['ig_username'] ?? 'alfithroh.jejeran', '@');
        @endphp
        <div class="mt-10 text-center">
            <a href="https://www.instagram.com/{{ $cleanIg }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2.5 px-6 py-3.5 border-2 border-slate-200 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-600 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-400 rounded-2xl text-sm font-bold transition-all duration-200 group">
                {{-- Instagram Icon --}}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
                <span>Lihat Semua Kegiatan di Instagram</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- LANGKAH PENDAFTARAN PSB                                       --}}
    {{-- ============================================================ --}}
    <section id="pendaftaran" class="bg-slate-100/70 dark:bg-slate-900/40 py-28 scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-2 text-center mb-16">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Prosedur PSB</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Langkah Pendaftaran Santri</h2>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-lg mx-auto">
                    Penerimaan Santri Baru (PSB) Pondok Pesantren Al-Fithroh Bantul — panduan lengkap pendaftaran putra-putri Anda.
                </p>
            </div>

            @php
                $psbSteps = [
                    [
                        'no'      => '1',
                        'badgeBg' => 'bg-emerald-600 dark:bg-emerald-500 shadow-emerald-500/20',
                        'barBg'   => 'bg-emerald-500',
                        'title'   => 'Sowan kepada Pengasuh',
                        'desc'    => 'Orang tua/wali santri sowan (menghadap) langsung kepada Pengasuh Pondok sebagai langkah pertama dan paling utama dalam proses pendaftaran.',
                    ],
                    [
                        'no'      => '2',
                        'badgeBg' => 'bg-amber-600 dark:bg-amber-500 shadow-amber-500/20',
                        'barBg'   => 'bg-amber-500',
                        'title'   => 'Masa Training (Ta\'aruf)',
                        'desc'    => 'Santri baru mengikuti masa training atau ta\'aruf di pondok dengan durasi maksimal 10 hari untuk pengenalan lingkungan dan penilaian awal.',
                    ],
                    [
                        'no'      => '3',
                        'badgeBg' => 'bg-teal-600 dark:bg-teal-500 shadow-teal-500/20',
                        'barBg'   => 'bg-teal-500',
                        'title'   => 'Melengkapi Berkas',
                        'desc'    => 'Siapkan dan serahkan fotokopi Kartu Keluarga (KK) serta pas foto formal berwarna terbaru santri kepada pihak sekretariat pondok.',
                    ],
                    [
                        'no'      => '4',
                        'badgeBg' => 'bg-sky-600 dark:bg-sky-500 shadow-sky-500/20',
                        'barBg'   => 'bg-sky-500',
                        'title'   => 'Administrasi Masuk',
                        'desc'    => 'Melunasi biaya administrasi pendaftaran sesuai ketentuan yang berlaku. Dapat diangsur selama 3 bulan pertama sejak santri mulai mukim.',
                    ],
                    [
                        'no'      => '5',
                        'badgeBg' => 'bg-rose-600 dark:bg-rose-500 shadow-rose-500/20',
                        'barBg'   => 'bg-rose-500',
                        'title'   => 'Mulai Mukim',
                        'desc'    => 'Santri baru wajib menetap di asrama selama minimal 40 hari pertama sejak dinyatakan diterima, sebagai masa adaptasi penuh di lingkungan pondok.',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($psbSteps as $step)
                    <div class="bg-white dark:bg-slate-900 p-5 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs group hover:shadow-md transition-all relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute top-0 left-0 h-1.5 w-full {{ $step['barBg'] }} opacity-80 rounded-t-3xl"></div>
                        <div class="flex items-start gap-4">
                            <span class="w-10 h-10 rounded-2xl {{ $step['badgeBg'] }} text-white flex items-center justify-center font-black text-sm shrink-0 shadow-md">
                                {{ $step['no'] }}
                            </span>
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white font-serif-display leading-tight">
                                    {{ $step['title'] }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                    {{ $step['desc'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- BUKU PEDOMAN SANTRI                                           --}}
    {{-- ============================================================ --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 py-28">
        <div class="p-8 sm:p-14 bg-gradient-to-br from-emerald-800 via-emerald-900 to-emerald-950 rounded-[2rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-8 -bottom-8 w-40 h-40 bg-amber-400/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-8">
                {{-- Kiri: Teks --}}
                <div class="space-y-3 text-center sm:text-left max-w-xl">
                    <div class="flex items-center gap-2 justify-center sm:justify-start">
                        <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-300">Dokumen Resmi</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold font-serif-display tracking-tight">{{ $data['pedoman_title'] }}</h3>
                    <p class="text-xs text-emerald-100 leading-relaxed max-w-md">
                        {{ $data['pedoman_description'] }}
                    </p>
                </div>

                {{-- Kanan: Tombol --}}
                <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                    @if(!empty($data['pedoman_file_url']))
                        {{-- Tombol Baca Online (Trigger Modal) --}}
                        <button type="button" @click="showPedomanModal = true"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/40 text-white rounded-2xl text-xs font-bold transition-all backdrop-blur-sm whitespace-nowrap cursor-pointer hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Baca Online
                        </button>
                        {{-- Tombol Unduh PDF --}}
                        <a href="{{ route('pedoman.download') }}" download
                           class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-white hover:bg-emerald-50 text-emerald-900 rounded-2xl text-xs font-bold transition-all shadow-md whitespace-nowrap hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh PDF
                        </a>
                    @else
                        <button disabled class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-white/10 border border-white/10 text-white/40 rounded-2xl text-xs font-bold cursor-not-allowed whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Baca Online
                        </button>
                        <button disabled class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-white/10 border border-white/10 text-white/40 rounded-2xl text-xs font-bold cursor-not-allowed whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh PDF
                        </button>
                    @endif
                </div>
            </div>

            @if(empty($data['pedoman_file_url']))
                <div class="relative z-10 mt-6 pt-4 border-t border-white/10 text-center">
                    <span class="text-[10px] text-emerald-300/60 font-medium">📋 Buku pedoman sedang dalam proses penerbitan — segera hadir</span>
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- KONTAK & LOKASI — 2 Column Layout with Embedded Google Map     --}}
    {{-- ============================================================ --}}
    <section id="kontak" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28 scroll-mt-24">
        <div class="space-y-2 mb-10 text-center">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Hubungi Kami</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Kontak Sekretariat & Peta Lokasi</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            {{-- Kolom Kiri: Info Kontak & WA (7 cols) --}}
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6 sm:p-8 flex flex-col justify-between space-y-6">
                
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white font-serif-display border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Informasi Sekretariat Pusat</span>
                    </h3>

                    {{-- Alamat --}}
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Alamat Pesantren</span>
                            <span class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 font-medium leading-relaxed block mt-0.5">{{ $data['contact_address'] }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Email --}}
                        <a href="mailto:{{ $data['contact_email'] }}"
                           class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 hover:border-amber-400 transition-all group">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Email Resmi</span>
                                <span class="text-xs text-slate-700 dark:text-slate-200 font-medium truncate block group-hover:text-amber-600 transition-colors">{{ $data['contact_email'] }}</span>
                            </div>
                        </a>

                        {{-- Instagram --}}
                        @php
                            $cleanIg = ltrim($data['ig_username'] ?? 'alfithroh.jejeran', '@');
                        @endphp
                        <a href="https://www.instagram.com/{{ $cleanIg }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 hover:border-pink-400 transition-all group">
                            <span class="w-8 h-8 rounded-xl bg-pink-100 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Instagram</span>
                                <span class="text-xs text-pink-600 dark:text-pink-400 font-medium group-hover:underline truncate block">&#64;{{ $cleanIg }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- WhatsApp Admins --}}
                <div class="space-y-3 pt-2">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Layanan WhatsApp Fast Response</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- WA Putra 1 --}}
                        @if(!empty($data['wa_putra1']))
                            @php
                                $cleanWaP1 = preg_replace('/[^0-9]/', '', $data['wa_putra1']);
                                if (str_starts_with($cleanWaP1, '0')) $cleanWaP1 = '62' . substr($cleanWaP1, 1);
                            @endphp
                            <a href="https://wa.me/{{ $cleanWaP1 }}?text=Assalamu%27alaikum%2C%20saya%20ingin%20bertanya%20mengenai%20Pondok%20Pesantren%20Al-Fithroh"
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2.5 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all group">
                                <span class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-[9px] uppercase font-extrabold text-emerald-600 dark:text-emerald-400 block tracking-wider">WA Putra 1</span>
                                    <span class="text-xs text-slate-700 dark:text-slate-200 font-bold truncate block group-hover:text-emerald-600">{{ $data['wa_putra1'] }}</span>
                                </div>
                            </a>
                        @endif

                        {{-- WA Putra 2 --}}
                        @if(!empty($data['wa_putra2']))
                            @php
                                $cleanWaP2 = preg_replace('/[^0-9]/', '', $data['wa_putra2']);
                                if (str_starts_with($cleanWaP2, '0')) $cleanWaP2 = '62' . substr($cleanWaP2, 1);
                            @endphp
                            <a href="https://wa.me/{{ $cleanWaP2 }}?text=Assalamu%27alaikum%2C%20saya%20ingin%20bertanya%20mengenai%20Pondok%20Pesantren%20Al-Fithroh"
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2.5 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all group">
                                <span class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-[9px] uppercase font-extrabold text-emerald-600 dark:text-emerald-400 block tracking-wider">WA Putra 2</span>
                                    <span class="text-xs text-slate-700 dark:text-slate-200 font-bold truncate block group-hover:text-emerald-600">{{ $data['wa_putra2'] }}</span>
                                </div>
                            </a>
                        @endif

                        {{-- WA Putri --}}
                        @if(!empty($data['wa_putri']))
                            @php
                                $cleanWaPi = preg_replace('/[^0-9]/', '', $data['wa_putri']);
                                if (str_starts_with($cleanWaPi, '0')) $cleanWaPi = '62' . substr($cleanWaPi, 1);
                            @endphp
                            <a href="https://wa.me/{{ $cleanWaPi }}?text=Assalamu%27alaikum%2C%20saya%20ingin%20bertanya%20mengenai%20Pondok%20Pesantren%20Al-Fithroh"
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2.5 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/50 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all group">
                                <span class="w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </span>
                                <div class="min-w-0">
                                    <span class="text-[9px] uppercase font-extrabold text-rose-600 dark:text-rose-400 block tracking-wider">WA Putri</span>
                                    <span class="text-xs text-slate-700 dark:text-slate-200 font-bold truncate block group-hover:text-rose-600">{{ $data['wa_putri'] }}</span>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Google Maps Embed (5 cols) --}}
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-4 flex flex-col justify-between space-y-4">
                <div class="w-full h-64 lg:h-full min-h-[250px] rounded-2xl overflow-hidden relative border border-slate-100 dark:border-slate-800">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.477610058869!2d110.38870631477815!3d-7.845012994347514!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a57a0753066d9%3A0xbbfd1cf434e35ab0!2sPondok%20Pesantren%20Al-Fithroh%20Jejeran!5e0!3m2!1sid!2sid!4v1650000000000!5m2!1sid!2sid"
                            class="w-full h-full border-0 filter grayscale dark:invert dark:opacity-80 hover:grayscale-0 dark:hover:invert-0 transition-all duration-500"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                @if(!empty($data['gmaps_url']))
                    <a href="{{ $data['gmaps_url'] }}" target="_blank" rel="noopener noreferrer"
                       class="w-full py-3 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Buka di Google Maps</span>
                    </a>
                @endif
            </div>

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MODAL BACA ONLINE BUKU PEDOMAN (MOBILE FRIENDLY STREAM)      --}}
    {{-- ============================================================ --}}
    @if(!empty($data['pedoman_file_url']))
        <div x-show="showPedomanModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="showPedomanModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 lg:p-8">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showPedomanModal = false"></div>

            {{-- Modal Dialog --}}
            <div class="relative w-full max-w-5xl h-[85vh] sm:h-[90vh] bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Header Modal --}}
                <div class="px-4 sm:px-6 py-3.5 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800 gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs sm:text-sm font-bold font-serif-display text-white truncate">{{ $data['pedoman_title'] }}</h3>
                            <p class="text-[9px] text-slate-400 truncate">Dokumen Resmi Pondok Pesantren Al-Fithroh Jejeran Bantul</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('pedoman.stream') }}" target="_blank" rel="noopener noreferrer"
                           class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span class="hidden sm:inline">Buka Tab Baru</span><span class="sm:hidden">Buka</span>
                        </a>
                        <a href="{{ route('pedoman.download') }}" download
                           class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span class="hidden sm:inline">Unduh</span>
                        </a>
                        <button type="button" @click="showPedomanModal = false"
                                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body: Dual Viewer Engine --}}
                <div class="flex-1 w-full bg-slate-950 relative overflow-hidden flex flex-col">
                    {{-- Mobile Notification Banner --}}
                    <div class="p-3 bg-emerald-950 border-b border-emerald-800/40 text-xs text-emerald-200 flex items-center justify-between gap-3 px-4 shrink-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-base shrink-0">📱</span>
                            <span class="text-[11px] font-medium leading-tight">Tekan <b>"Buka PDF Layar Penuh"</b> untuk membaca dokumen PDF secara langsung dan nyaman di HP Anda.</span>
                        </div>
                        <a href="{{ route('pedoman.stream') }}" target="_blank" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-black text-xs shrink-0 shadow-md transition-all">
                            Buka PDF ➔
                        </a>
                    </div>
                    
                    <div class="flex-1 w-full relative">
                        <iframe src="{{ route('pedoman.stream') }}#toolbar=1" 
                                class="w-full h-full border-0" 
                                title="Buku Pedoman Santri Viewer">
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- KONTAK & LOKASI — 2 Column Layout with Embedded Google Map     --}}
    {{-- ============================================================ --}}
    <section id="kontak" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-28 scroll-mt-24">
        <div class="space-y-2 mb-10 text-center">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Hubungi Kami</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white font-serif-display">Kontak Sekretariat & Peta Lokasi</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            {{-- Kolom Kiri: Info Kontak & WA (7 cols) --}}
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6 sm:p-8 flex flex-col justify-between space-y-6">
                
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white font-serif-display border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Informasi Sekretariat Pusat</span>
                    </h3>

                    {{-- Alamat --}}
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Alamat Pesantren</span>
                            <span class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 font-medium leading-relaxed block mt-0.5">{{ $data['contact_address'] }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Email --}}
                        <a href="mailto:{{ $data['contact_email'] }}"
                           class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 hover:border-amber-400 transition-all group">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Email Resmi</span>
                                <span class="text-xs text-slate-700 dark:text-slate-200 font-medium truncate block group-hover:text-amber-600 transition-colors">{{ $data['contact_email'] }}</span>
                            </div>
                        </a>

                        {{-- Instagram --}}
                        @php
                            $cleanIg = ltrim($data['ig_username'] ?? 'alfithroh.jejeran', '@');
                        @endphp
                        <a href="https://www.instagram.com/{{ $cleanIg }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 hover:border-pink-400 transition-all group">
                            <span class="w-8 h-8 rounded-xl bg-pink-100 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Instagram</span>
                                <span class="text-xs text-pink-600 dark:text-pink-400 font-medium group-hover:underline truncate block">&#64;{{ $cleanIg }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- WhatsApp Admins --}}
                <div class="space-y-3 pt-2">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Layanan WhatsApp Fast Response</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- WA Putra 1 --}}
                        @php
                            $numWa1 = '62' . ltrim(preg_replace('/[^0-9]/', '', $data['wa_putra1'] ?? '08123456789'), '0');
                        @endphp
                        <a href="https://wa.me/{{ $numWa1 }}?text=Assalamualaikum%2C%20saya%20ingin%20bertanya%20mengenai%20pendaftaran%20santri%20putra."
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2.5 p-3 rounded-2xl bg-green-50/50 dark:bg-green-950/20 border border-green-200/60 dark:border-green-900/40 hover:bg-green-100/60 transition-all group">
                            <span class="w-7 h-7 rounded-xl bg-green-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[9px] text-green-700 dark:text-green-400 font-bold leading-none mb-0.5">WA Putra 1</p>
                                <p class="text-xs text-slate-800 dark:text-slate-100 font-semibold truncate">{{ $data['wa_putra1'] }}</p>
                            </div>
                        </a>

                        {{-- WA Putra 2 --}}
                        @php
                            $numWa2 = '62' . ltrim(preg_replace('/[^0-9]/', '', $data['wa_putra2'] ?? '08129876543'), '0');
                        @endphp
                        <a href="https://wa.me/{{ $numWa2 }}?text=Assalamualaikum%2C%20saya%20ingin%20bertanya%20mengenai%20pendaftaran%20santri%20putra."
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2.5 p-3 rounded-2xl bg-green-50/50 dark:bg-green-950/20 border border-green-200/60 dark:border-green-900/40 hover:bg-green-100/60 transition-all group">
                            <span class="w-7 h-7 rounded-xl bg-green-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[9px] text-green-700 dark:text-green-400 font-bold leading-none mb-0.5">WA Putra 2</p>
                                <p class="text-xs text-slate-800 dark:text-slate-100 font-semibold truncate">{{ $data['wa_putra2'] }}</p>
                            </div>
                        </a>

                        {{-- WA Putri --}}
                        @php
                            $numWaPutri = '62' . ltrim(preg_replace('/[^0-9]/', '', $data['wa_putri'] ?? '08111222333'), '0');
                        @endphp
                        <a href="https://wa.me/{{ $numWaPutri }}?text=Assalamualaikum%2C%20saya%20ingin%20bertanya%20mengenai%20pendaftaran%20santri%20putri."
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2.5 p-3 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200/60 dark:border-rose-900/40 hover:bg-rose-100/60 transition-all group">
                            <span class="w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[9px] text-rose-700 dark:text-rose-400 font-bold leading-none mb-0.5">WA Putri</p>
                                <p class="text-xs text-slate-800 dark:text-slate-100 font-semibold truncate">{{ $data['wa_putri'] }}</p>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Kolom Kanan: Embedded Google Maps Interactive (5 cols) --}}
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-4 flex flex-col space-y-3 justify-between">
                
                {{-- Header Maps --}}
                <div class="px-2 pt-1 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Peta Lokasi Pesantren</span>
                    </div>
                    <a href="{{ !empty($data['gmaps_url']) ? $data['gmaps_url'] : 'https://maps.app.goo.gl/KTznYAfUtWU2B2hz7' }}" target="_blank" rel="noopener noreferrer"
                       class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                        <span>Buka Aplikasi GMaps</span>
                        <span>↗</span>
                    </a>
                </div>

                {{-- Embedded Interactive Google Map Iframe --}}
                <div class="relative w-full h-[340px] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950">
                    <iframe 
                        src="https://maps.google.com/maps?q={{ urlencode('Pesantren AL-FITHROH Jejeran, 49MP+MMX, Jl. Imogiri Timur, Wonokromo, Pleret, Bantul') }}&t=&z=17&ie=UTF8&iwloc=&output=embed" 
                        class="w-full h-full border-0" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta Lokasi Pesantren AL-FITHROH Jejeran">
                    </iframe>
                </div>

                {{-- Subtext / Hint --}}
                <p class="text-[10px] text-slate-400 text-center pb-1">
                    📍 Pesantren AL-FITHROH Jejeran, 49MP+MMX, Jl. Imogiri Timur — Petunjuk arah presisi dapat diklik langsung pada peta.
                </p>

            </div>

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MODAL BACA ONLINE BUKU PEDOMAN (PDF VIEWER)                   --}}
    {{-- ============================================================ --}}
    @if(!empty($data['pedoman_file_url']))
        <div x-show="showPedomanModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="showPedomanModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 lg:p-8">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showPedomanModal = false"></div>

            {{-- Modal Dialog --}}
            <div class="relative w-full max-w-5xl h-[85vh] sm:h-[90vh] bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Header Modal --}}
                <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold font-serif-display text-white">{{ $data['pedoman_title'] }}</h3>
                            <p class="text-[10px] text-slate-400">Dokumen Resmi Pondok Pesantren Al-Fithroh Jejeran Bantul</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('pedoman.download') }}" download
                           class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh PDF</span>
                        </a>
                        <button type="button" @click="showPedomanModal = false"
                                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body: Embedded PDF Viewer --}}
                <div class="flex-1 w-full bg-slate-950 relative overflow-hidden">
                    <iframe src="{{ route('pedoman.stream') }}#toolbar=1" 
                            class="w-full h-full border-0" 
                            title="Buku Pedoman Santri Viewer">
                    </iframe>
                </div>

            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODAL LIGHTBOX GALERI MULTI-FOTO KEGIATAN                     --}}
    {{-- ============================================================ --}}
    <div x-show="showGalleryModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="showGalleryModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 lg:p-8">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md" @click="showGalleryModal = false"></div>

        {{-- Modal Dialog --}}
        <div class="relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col z-10 max-h-[90vh]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            {{-- Header Modal --}}
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800 gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold uppercase flex-shrink-0" x-text="activeActivity?.category || 'Kegiatan'"></span>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold font-serif-display text-white truncate" x-text="activeActivity?.title"></h3>
                        <p class="text-[10px] text-slate-400 truncate" x-text="(activeActivity?.date || '') + ' — ' + (activeActivity?.org || '')"></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Tombol Download Foto Aktif --}}
                    <template x-if="activeActivity?.photos && activeActivity.photos[activePhotoIdx]">
                        <a :href="activeActivity.photos[activePhotoIdx]" download
                           class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-emerald-500/20 flex items-center gap-1.5 hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Foto</span>
                        </a>
                    </template>

                    <button type="button" @click="showGalleryModal = false" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors">✕</button>
                </div>
            </div>

            {{-- Body: Main Photo Display --}}
            <div class="relative flex-1 bg-slate-950 flex items-center justify-center min-h-[320px] max-h-[480px] overflow-hidden group">
                <template x-if="activeActivity?.photos && activeActivity.photos.length > 0">
                    <img :src="activeActivity.photos[activePhotoIdx]" class="max-h-[460px] w-auto max-w-full object-contain transition-all duration-300">
                </template>

                <template x-if="!activeActivity?.photos || activeActivity.photos.length === 0">
                    <div class="text-slate-500 text-xs text-center p-8">Belum ada foto dokumentasi diunggah.</div>
                </template>

                {{-- Previous / Next Navigation Arrows --}}
                <template x-if="activeActivity?.photos && activeActivity.photos.length > 1">
                    <div class="absolute inset-y-0 inset-x-3 flex items-center justify-between pointer-events-none">
                        <button type="button" 
                                @click="activePhotoIdx = (activePhotoIdx > 0) ? activePhotoIdx - 1 : activeActivity.photos.length - 1"
                                class="pointer-events-auto w-10 h-10 rounded-full bg-slate-900/80 hover:bg-emerald-500 text-white flex items-center justify-center backdrop-blur-md transition-colors shadow-lg border border-white/10">
                            ❮
                        </button>
                        <button type="button" 
                                @click="activePhotoIdx = (activePhotoIdx < activeActivity.photos.length - 1) ? activePhotoIdx + 1 : 0"
                                class="pointer-events-auto w-10 h-10 rounded-full bg-slate-900/80 hover:bg-emerald-500 text-white flex items-center justify-center backdrop-blur-md transition-colors shadow-lg border border-white/10">
                            ❯
                        </button>
                    </div>
                </template>
            </div>

            {{-- Footer: Thumbnail Strip & Description --}}
            <div class="p-6 space-y-4 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
                {{-- Thumbnails Strip --}}
                <template x-if="activeActivity?.photos && activeActivity.photos.length > 1">
                    <div class="flex items-center gap-2 overflow-x-auto pb-2">
                        <template x-for="(photo, idx) in activeActivity.photos" :key="idx">
                            <button type="button" @click="activePhotoIdx = idx" 
                                    class="w-14 h-14 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0"
                                    :class="activePhotoIdx === idx ? 'border-emerald-500 scale-105 shadow-md' : 'border-transparent opacity-50 hover:opacity-100'">
                                <img :src="photo" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </template>

                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal" x-text="activeActivity?.description"></p>
            </div>

        </div>
    </div>

</div>
