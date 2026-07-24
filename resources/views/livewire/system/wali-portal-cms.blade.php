<div class="space-y-8">
    <!-- Header Page -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 uppercase tracking-wider">
                    Pengaturan Sistem CMS
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-3 text-white">
                    CMS Portal Wali Santri
                </h1>
                <p class="text-slate-400 text-xs sm:text-sm mt-1 leading-relaxed max-w-2xl">
                    Kelola nama bank, nomor rekening bank, nama pemilik rekening, serta kontak WhatsApp Bendahara secara dinamis dipisahkan untuk unit **Putra** dan **Putri**.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('portal-wali.search') }}" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Pratinjau Portal Wali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Form Settings Card -->
    <form wire:submit.prevent="save" class="space-y-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- TABEL / BLOK PUTRA -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
                        👳‍♂️
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">Pengaturan Unit Putra (Laki-laki)</h3>
                        <p class="text-xs text-slate-400">Nama Bank, Rekening & WA Bendahara Santri Putra</p>
                    </div>
                </div>

                <!-- WA Bendahara Putra -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-xs font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">Kontak WA Bendahara Putra</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Bendahara Putra</label>
                        <input type="text" wire:model.defer="wa_bendahara_putra" placeholder="6281234567890" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-emerald-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format dengan kode negara (contoh: 6281234567890)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Tampilan Bendahara Putra</label>
                        <input type="text" wire:model.defer="wa_bendahara_putra_name" placeholder="Ust. Ahmad / Bendahara Putra" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Rekening Bank Putra -->
                <div class="space-y-5 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <h4 class="text-xs font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">Rekening Bank 1 & 2 Putra</h4>

                    <!-- BANK 1 PUTRA -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                        <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Bank 1 (Utama) — Putra</span>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bank 1</label>
                            <input type="text" wire:model.defer="bank1_name_putra" placeholder="Contoh: Bank Syariah Indonesia (BSI) / Bank Mandiri" 
                                   class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. Rekening</label>
                                <input type="text" wire:model.defer="rekening_bsi_putra" placeholder="7123456789" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Atas Nama (A.N)</label>
                                <input type="text" wire:model.defer="rekening_bsi_putra_an" placeholder="Pesantren Al-Fithroh Putra" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- BANK 2 PUTRA -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                        <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Bank 2 (Alternatif) — Putra</span>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bank 2</label>
                            <input type="text" wire:model.defer="bank2_name_putra" placeholder="Contoh: Bank BRI / Bank BCA" 
                                   class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. Rekening</label>
                                <input type="text" wire:model.defer="rekening_bri_putra" placeholder="001201009876504" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Atas Nama (A.N)</label>
                                <input type="text" wire:model.defer="rekening_bri_putra_an" placeholder="Yayasan Al-Fithroh Putra" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL / BLOK PUTRI -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-pink-100 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400 flex items-center justify-center font-bold text-lg">
                        🧕
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">Pengaturan Unit Putri (Perempuan)</h3>
                        <p class="text-xs text-slate-400">Nama Bank, Rekening & WA Bendahara Santri Putri</p>
                    </div>
                </div>

                <!-- WA Bendahara Putri -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-xs font-black uppercase text-pink-600 dark:text-pink-400 tracking-wider">Kontak WA Bendahara Putri</h4>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Bendahara Putri</label>
                        <input type="text" wire:model.defer="wa_bendahara_putri" placeholder="6281234567891" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-pink-500">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format dengan kode negara (contoh: 6281234567891)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Tampilan Bendahara Putri</label>
                        <input type="text" wire:model.defer="wa_bendahara_putri_name" placeholder="Ustadzah Fatimah / Bendahara Putri" 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-pink-500">
                    </div>
                </div>

                <!-- Rekening Bank Putri -->
                <div class="space-y-5 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <h4 class="text-xs font-black uppercase text-pink-600 dark:text-pink-400 tracking-wider">Rekening Bank 1 & 2 Putri</h4>

                    <!-- BANK 1 PUTRI -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                        <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Bank 1 (Utama) — Putri</span>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bank 1</label>
                            <input type="text" wire:model.defer="bank1_name_putri" placeholder="Contoh: Bank Syariah Indonesia (BSI) / Bank Mandiri" 
                                   class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. Rekening</label>
                                <input type="text" wire:model.defer="rekening_bsi_putri" placeholder="7987654321" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-pink-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Atas Nama (A.N)</label>
                                <input type="text" wire:model.defer="rekening_bsi_putri_an" placeholder="Pesantren Al-Fithroh Putri" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                    </div>

                    <!-- BANK 2 PUTRI -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                        <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Bank 2 (Alternatif) — Putri</span>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Bank 2</label>
                            <input type="text" wire:model.defer="bank2_name_putri" placeholder="Contoh: Bank BRI / Bank BCA" 
                                   class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. Rekening</label>
                                <input type="text" wire:model.defer="rekening_bri_putri" placeholder="001201009876505" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-pink-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Atas Nama (A.N)</label>
                                <input type="text" wire:model.defer="rekening_bri_putri_an" placeholder="Yayasan Al-Fithroh Putri" 
                                       class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-900 dark:text-slate-100 font-bold focus:ring-2 focus:ring-pink-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PENGUMUMAN WALI SANTRI -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Catatan & Pengumuman Khusus Portal Wali</span>
            </h3>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Catatan / Pengumuman</label>
                <textarea wire:model.defer="wali_announcement" rows="3" 
                          placeholder="Contoh: Pembayaran tagihan santri dilakukan sebelum tanggal 10 setiap bulannya..." 
                          class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-3 text-xs text-slate-900 dark:text-slate-100 font-medium focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
        </div>

        <!-- Save Action -->
        <div class="flex justify-end pt-4">
            <button type="submit" 
                    class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold rounded-2xl shadow-xl transition-all flex items-center gap-2 text-xs uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Pengaturan CMS Wali</span>
            </button>
        </div>

    </form>
</div>
