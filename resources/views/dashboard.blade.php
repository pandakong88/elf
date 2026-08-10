<x-app-layout>
    <div class="space-y-8">
        <!-- 1. Executive Welcome Banner -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white rounded-3xl p-6 sm:p-8 border border-slate-700/60 shadow-xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                            ERP Pondok Al-Fithroh
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-slate-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ $stats['gregorian_date'] }}</span>
                        </span>
                        @if($stats['gender_scope'])
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 border border-amber-500/20 text-amber-300">
                                Scope: {{ $stats['gender_scope'] === 'L' ? '👦 Putra' : '👧 Putri' }}
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-blue-500/10 border border-blue-500/20 text-blue-300">
                                🌐 Scope Global (Putra &amp; Putri)
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white font-serif-display">
                        Ahlan wa Sahlan, {{ $user->name }}!
                    </h1>
                    <p class="text-slate-400 text-xs sm:text-sm max-w-2xl leading-relaxed">
                        Selamat datang di Pusat Kontrol Operasional Pondok Pesantren &amp; Diniyyah. Pantau metrik statistik, tagihan, perizinan, dan aktivitas harian santri secara real-time.
                    </p>
                </div>

                <div class="flex flex-wrap lg:flex-col items-start lg:items-end gap-2 shrink-0">
                    <div class="flex flex-wrap gap-1.5 justify-end">
                        @foreach ($roles as $role)
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-xl shadow-xs">
                                🛡️ {{ $role }}
                            </span>
                        @endforeach
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono bg-slate-900/80 px-3 py-1 rounded-lg border border-slate-800">
                        {{ $user->email }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Dev Corner Arcade Box (Multi-Game Randomizer) -->
        <div x-data="{
            showDevCorner: false,
            activeGame: 'dice',
            games: ['dice', 'khodam', 'slot', 'reflex'],
            
            // Game 1: Dice & Anti-Judi
            rollingDice: false,
            diceResult: 1,
            rollCount: 0,
            copiedDawah: false,
            dawahList: @js($dawahList),
            currentDawah: null,

            // Game 2: Khodam Santri
            khodamList: @js($khodamList),
            khodamName: '{{ addslashes($user->name) }}',
            scanningKhodam: false,
            currentKhodam: null,

            // Game 3: Slot Barokah
            spinningSlot: false,
            slotReels: ['🌙', '📚', '☕'],
            slotSymbols: ['🌙', '📚', '☕', '💰', '🌟', '🕌', '🕊️'],
            slotSpinCount: 0,
            slotSatireQuotes: @js($slotSatireQuotes),
            currentSlotSatire: null,

            // Game 4: Reflex Test
            reflexState: 'idle',
            reflexStartTime: 0,
            reflexTime: 0,
            reflexRank: '',
            reflexTimeoutId: null,

            init() {
                this.switchRandomGame();
                if (this.dawahList && this.dawahList.length > 0) {
                    this.currentDawah = this.dawahList[0];
                }
                if (this.khodamList && this.khodamList.length > 0) {
                    this.currentKhodam = this.khodamList[0];
                }
                if (this.slotSatireQuotes && this.slotSatireQuotes.length > 0) {
                    this.currentSlotSatire = this.slotSatireQuotes[0];
                }
            },

            switchRandomGame() {
                const available = this.games.filter(g => g !== this.activeGame);
                this.activeGame = available[Math.floor(Math.random() * available.length)];
            },

            // Dice Game logic
            rollDice() {
                if (this.rollingDice) return;
                this.rollingDice = true;
                let count = 0;
                const interval = setInterval(() => {
                    this.diceResult = Math.floor(Math.random() * 6) + 1;
                    count++;
                    if (count > 12) {
                        clearInterval(interval);
                        this.rollingDice = false;
                        this.rollCount++;
                        this.currentDawah = this.dawahList[this.diceResult - 1];
                    }
                }, 70);
            },

            // Khodam logic
            scanKhodam() {
                if (this.scanningKhodam) return;
                this.scanningKhodam = true;
                setTimeout(() => {
                    this.scanningKhodam = false;
                    const idx = Math.floor(Math.random() * this.khodamList.length);
                    this.currentKhodam = this.khodamList[idx];
                }, 1200);
            },

            // Slot Machine logic
            spinSlot() {
                if (this.spinningSlot) return;
                this.spinningSlot = true;
                let count = 0;
                const interval = setInterval(() => {
                    this.slotReels = [
                        this.slotSymbols[Math.floor(Math.random() * this.slotSymbols.length)],
                        this.slotSymbols[Math.floor(Math.random() * this.slotSymbols.length)],
                        this.slotSymbols[Math.floor(Math.random() * this.slotSymbols.length)]
                    ];
                    count++;
                    if (count > 15) {
                        clearInterval(interval);
                        this.spinningSlot = false;
                        this.slotSpinCount++;
                        if (this.slotSatireQuotes && this.slotSatireQuotes.length > 0) {
                            const idx = Math.floor(Math.random() * this.slotSatireQuotes.length);
                            this.currentSlotSatire = this.slotSatireQuotes[idx];
                        }
                    }
                }, 80);
            },

            // Reflex Test logic
            startReflex() {
                this.reflexState = 'waiting';
                const delay = Math.floor(Math.random() * 2500) + 1500;
                this.reflexTimeoutId = setTimeout(() => {
                    this.reflexState = 'ready';
                    this.reflexStartTime = Date.now();
                }, delay);
            },

            clickReflex() {
                if (this.reflexState === 'waiting') {
                    clearTimeout(this.reflexTimeoutId);
                    this.reflexState = 'idle';
                    alert('⚠️ Terlalu cepat! Tunggu kotak berubah hijau baru diklik.');
                    return;
                }
                if (this.reflexState === 'ready') {
                    this.reflexTime = Date.now() - this.reflexStartTime;
                    this.reflexState = 'result';
                    if (this.reflexTime < 220) {
                        this.reflexRank = '⚡ Kecepatan Musyrif Bangunin Santri Subuh! (Refleks Dewa)';
                    } else if (this.reflexTime < 320) {
                        this.reflexRank = '🏃 Kecepatan Santri Lari Ke Katering Pas Bel Makan!';
                    } else if (this.reflexTime < 450) {
                        this.reflexRank = '☕ Kecepatan Normal Setelah Minum Kopi Diniyyah';
                    } else {
                        this.reflexRank = '😴 Status: Mengantuk Berat Pas Sorogan Kitab (Butuh Wudhu)';
                    }
                }
            }
        }">
            <!-- Compact Banner saat Dev Corner disembunyikan -->
            <div x-show="!showDevCorner" class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white border border-slate-800/80 p-4 sm:p-5 rounded-3xl shadow-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xl shrink-0">
                        🎮
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-emerald-400 font-mono flex items-center gap-2">
                            <span>DEV CORNER ARCADE</span>
                            <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 text-[10px] font-mono">Disembunyikan 🔒</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Mini game interaktif acak (Dadu Nasihat, Khodam, Slot Barokah, &amp; Tes Refleks Santri).</p>
                    </div>
                </div>

                <button type="button" @click="showDevCorner = true" class="w-full sm:w-auto px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 text-xs font-mono font-bold rounded-xl transition-all flex items-center justify-center gap-2 shrink-0">
                    <span>🎮 Buka Dev Corner Arcade</span>
                </button>
            </div>

            <!-- Full Dev Corner Box (tampil jika showDevCorner = true) -->
            <div x-show="showDevCorner" x-cloak class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white border border-slate-800 p-5 sm:p-7 rounded-3xl shadow-xl relative overflow-hidden space-y-5">
                <!-- Control Header Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🚀</span>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-emerald-400 font-serif-display flex items-center gap-2">
                                <span>DEV CORNER ARCADE</span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-mono">Hiburan Santri 🎮</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">Mini game interaktif acak pengisi waktu luang pengurus &amp; ustadz.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <!-- Game Switcher Button -->
                        <button type="button" @click="switchRandomGame()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white text-xs font-mono font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5">
                            <span>🔀 Putar Game Random</span>
                        </button>
                        <!-- Hide Button -->
                        <button type="button" @click="showDevCorner = false" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <span>🙈 Sembunyikan</span>
                        </button>
                    </div>
                </div>

            <!-- Mini Game Navigation Tabs -->
            <div class="flex flex-wrap gap-2 border-b border-slate-800/80 pb-3">
                <button type="button" @click="activeGame = 'dice'" :class="activeGame === 'dice' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-slate-200'" class="px-3 py-1.5 text-xs font-bold font-mono rounded-xl border transition-all flex items-center gap-1.5">
                    <span>🎲 Dadu Anti-Judi</span>
                </button>
                <button type="button" @click="activeGame = 'khodam'" :class="activeGame === 'khodam' ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-slate-200'" class="px-3 py-1.5 text-xs font-bold font-mono rounded-xl border transition-all flex items-center gap-1.5">
                    <span>🔮 Cek Khodam Santri</span>
                </button>
                <button type="button" @click="activeGame = 'slot'" :class="activeGame === 'slot' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-slate-200'" class="px-3 py-1.5 text-xs font-bold font-mono rounded-xl border transition-all flex items-center gap-1.5">
                    <span>🎰 Slot Barokah</span>
                </button>
                <button type="button" @click="activeGame = 'reflex'" :class="activeGame === 'reflex' ? 'bg-blue-500/20 text-blue-300 border-blue-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-slate-200'" class="px-3 py-1.5 text-xs font-bold font-mono rounded-xl border transition-all flex items-center gap-1.5">
                    <span>⚡ Tes Refleks Santri</span>
                </button>
            </div>

            <!-- GAME 1: DADU ANTI-JUDI ONLINE -->
            <div x-show="activeGame === 'dice'" class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl flex flex-col items-center justify-center text-center space-y-4 shadow-inner">
                        <span class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">Kocok Dadu Gratis &amp; Berpahala</span>
                        <div @click="rollDice()" class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-teal-700 hover:from-emerald-400 hover:to-teal-600 rounded-3xl border-2 border-emerald-300/40 shadow-xl flex items-center justify-center cursor-pointer transition-all duration-300 transform active:scale-90 hover:scale-105 relative" :class="{ 'animate-spin': rollingDice }">
                            <span class="text-4xl">🎲 <span x-text="diceResult">1</span></span>
                        </div>
                        <button type="button" @click="rollDice()" :disabled="rollingDice" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-md font-mono">
                            <span x-show="!rollingDice">🎲 Kocok Dadu Nasihat</span>
                            <span x-show="rollingDice" class="text-amber-200">⏳ Mengocok Dadu...</span>
                        </button>
                    </div>

                    <div class="lg:col-span-8 bg-slate-950/80 border border-slate-800 p-5 rounded-2xl space-y-3 shadow-inner">
                        <div class="p-3.5 bg-rose-950/40 border border-rose-500/30 rounded-xl space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-400 flex items-center gap-1.5">
                                🛑 PERINGATAN ANTI-JUDI ONLINE (JUDOL):
                            </span>
                            <p class="text-xs text-rose-200 font-medium leading-relaxed">
                                "Wahai kawan! Mengocok dadu di Dev Corner ini 100% gratis &amp; berpahala nasihat. Tapi ingat, <strong>JANGAN SEKALI-KALI MENDEKATI JUDI ONLINE (JUDOL)!</strong> Judol itu merusak dompet, membatalkan ketenangan, dan merugikan dunia akhirat!"
                            </p>
                        </div>

                        <div class="pt-2 text-right">
                            <h4 class="text-lg font-extrabold text-amber-300 font-serif leading-loose" dir="rtl" x-text="currentDawah ? currentDawah.arab : ''"></h4>
                        </div>
                        <p class="text-xs text-slate-400 italic leading-relaxed border-l-2 border-slate-700 pl-3" x-text="currentDawah ? '« ' + currentDawah.translation + ' »' : ''"></p>
                        <div class="p-3 bg-emerald-950/30 border border-emerald-500/20 rounded-xl">
                            <p class="text-xs text-emerald-300 font-medium leading-relaxed" x-text="currentDawah ? currentDawah.hikmah : ''"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GAME 2: CEK KHODAM SANTRI & USTADZ -->
            <div x-show="activeGame === 'khodam'" class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-5 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl space-y-4 shadow-inner">
                        <span class="text-[10px] font-mono text-purple-400 uppercase tracking-widest block font-bold">🔮 Aura Scanner Santri</span>
                        
                        <div class="space-y-2">
                            <label for="khodamNameInput" class="text-xs font-bold text-slate-300 block">Nama Santri / Pengurus:</label>
                            <input type="text" id="khodamNameInput" x-model="khodamName" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-3.5 py-2.5 text-xs font-mono focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Masukkan nama...">
                        </div>

                        <button type="button" @click="scanKhodam()" :disabled="scanningKhodam" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-500 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-md font-mono flex items-center justify-center gap-2">
                            <span x-show="!scanningKhodam">🔮 Cek Khodam Saya Hari Ini</span>
                            <span x-show="scanningKhodam" class="text-purple-200 animate-pulse">✨ Memindai Aura Santri...</span>
                        </button>
                    </div>

                    <div class="lg:col-span-7 bg-slate-950/90 border border-purple-500/30 p-6 rounded-2xl space-y-3 relative overflow-hidden shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <span class="text-xs font-bold text-slate-400 font-mono">Hasil Terawang Aura Khodam:</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-mono font-bold" x-text="currentKhodam ? currentKhodam.badge : ''"></span>
                        </div>

                        <div class="flex items-center gap-4 pt-1">
                            <span class="text-5xl shrink-0 p-3 bg-purple-950/60 border border-purple-500/30 rounded-2xl" x-text="currentKhodam ? currentKhodam.icon : '🔮'"></span>
                            <div class="space-y-1">
                                <h4 class="text-base font-black text-purple-300 font-serif-display" x-text="currentKhodam ? currentKhodam.title : ''"></h4>
                                <p class="text-xs text-slate-300 leading-relaxed font-medium" x-text="currentKhodam ? currentKhodam.description : ''"></p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-800/80 text-[10px] text-slate-400 italic">
                            💡 *Khodam ini hanya hiburan lucu-lucuan khas santri &amp; pengurus pesantren.*
                        </div>
                    </div>
                </div>
            </div>

            <!-- GAME 3: MESIN SLOT BAROKAH (ANTI-JUDI SIMULATION) -->
            <div x-show="activeGame === 'slot'" class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-5 bg-gradient-to-br from-amber-950/40 via-slate-900 to-amber-950/40 border border-amber-500/40 p-6 rounded-2xl flex flex-col items-center justify-center text-center space-y-4 shadow-xl relative overflow-hidden">
                        <div class="flex items-center justify-between w-full border-b border-amber-500/20 pb-2">
                            <span class="text-[10px] font-mono text-amber-400 font-bold uppercase tracking-widest">🎰 SIMULASI SLOT BAROKAH (MODAL RP 0,-)</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[9px] font-mono font-bold">RTP: 1000% BERKAH</span>
                        </div>

                        <!-- 3 Reels Display -->
                        <div class="flex items-center justify-center gap-3 bg-slate-950/90 p-4 rounded-2xl border-2 border-amber-500/50 w-full shadow-2xl ring-2 ring-amber-500/20">
                            <div class="w-16 h-20 bg-slate-900 border border-amber-500/40 rounded-2xl flex items-center justify-center text-4xl shadow-inner transition-transform" :class="{ 'animate-bounce': spinningSlot }">
                                <span x-text="slotReels[0]">🌙</span>
                            </div>
                            <div class="w-16 h-20 bg-slate-900 border border-amber-500/40 rounded-2xl flex items-center justify-center text-4xl shadow-inner transition-transform" :class="{ 'animate-bounce': spinningSlot }">
                                <span x-text="slotReels[1]">📚</span>
                            </div>
                            <div class="w-16 h-20 bg-slate-900 border border-amber-500/40 rounded-2xl flex items-center justify-center text-4xl shadow-inner transition-transform" :class="{ 'animate-bounce': spinningSlot }">
                                <span x-text="slotReels[2]">☕</span>
                            </div>
                        </div>

                        <div class="w-full flex gap-2">
                            <button type="button" @click="spinSlot()" :disabled="spinningSlot" class="flex-1 py-2.5 px-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 active:scale-95 text-slate-950 font-black text-xs rounded-xl transition-all shadow-lg font-mono tracking-wider">
                                <span x-show="!spinningSlot">🎰 PUTAR SLOT GACOR</span>
                                <span x-show="spinningSlot" class="text-slate-950 animate-pulse">⏳ MEMUTAR REEL...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Side Satire Quote Display -->
                    <div class="lg:col-span-7 bg-slate-950/95 border border-amber-500/30 p-6 rounded-2xl space-y-4 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                            <span class="text-xs font-bold text-amber-400 font-mono flex items-center gap-1.5">
                                💡 NASIHAT ANTI-JUDI ONLINE &amp; HUMOR DEVS
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-mono font-bold">
                                Total Spin: <strong x-text="slotSpinCount">0</strong>x
                            </span>
                        </div>

                        <div class="p-4 bg-amber-950/40 border border-amber-500/30 rounded-xl space-y-2">
                            <h4 class="text-xs font-black uppercase tracking-wider text-amber-400 font-mono flex items-center gap-2" x-text="currentSlotSatire ? currentSlotSatire.title : 'Cara Mengubah Dosa Menjadi Saldo DANA'">
                            </h4>
                            <p class="text-xs text-amber-100 font-medium leading-relaxed" x-text="currentSlotSatire ? currentSlotSatire.quote : ''">
                            </p>
                        </div>

                        <div class="p-3 bg-rose-950/40 border border-rose-500/30 rounded-xl space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-400 flex items-center gap-1.5">
                                🛑 PERINGATAN RESMI:
                            </span>
                            <p class="text-xs text-rose-200 font-medium leading-relaxed">
                                "Di Dev Corner main slot 100% gratis, aman, &amp; berpahala. Kalau di situs judi online asli, yang dapet jackpot cuma bandarnya, pemainnya auto-rungkad! 😂 Jangan pernah tergiur judi online!"
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GAME 4: TES REFLEKS KILAT SANTRI -->
            <div x-show="activeGame === 'reflex'" class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-5 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl flex flex-col items-center justify-center text-center space-y-4 shadow-inner">
                        <span class="text-[10px] font-mono text-blue-400 uppercase tracking-widest font-bold">⚡ Tes Refleks Kecepatan Santri</span>

                        <!-- Reflex Click Box -->
                        <div @click="if (reflexState === 'idle') startReflex(); else if (reflexState === 'ready' || reflexState === 'waiting') clickReflex();"
                             class="w-full h-32 rounded-2xl border-2 transition-all flex flex-col items-center justify-center cursor-pointer p-4 text-center select-none shadow-md"
                             :class="{
                                'bg-slate-950 border-slate-800 hover:border-blue-500/50': reflexState === 'idle' || reflexState === 'result',
                                'bg-rose-600 border-rose-400 text-white animate-pulse': reflexState === 'waiting',
                                'bg-emerald-500 border-emerald-300 text-white scale-105 shadow-xl': reflexState === 'ready'
                             }">
                            <template x-if="reflexState === 'idle'">
                                <span class="text-xs font-bold text-slate-300 font-mono">Klik Tombol Di Bawah Untuk Mulai Tes</span>
                            </template>
                            <template x-if="reflexState === 'waiting'">
                                <span class="text-sm font-black tracking-wider text-amber-200">TUNGGU WAKTU... KOTAK BERUBAH HIJAU BARU DIKLIK!</span>
                            </template>
                            <template x-if="reflexState === 'ready'">
                                <span class="text-base font-black tracking-wider text-white">KLIK SEKARANG JUGA! ⚡</span>
                            </template>
                            <template x-if="reflexState === 'result'">
                                <div>
                                    <span class="text-2xl font-black text-emerald-400 font-mono" x-text="reflexTime + ' ms'"></span>
                                    <span class="text-[10px] text-slate-400 block mt-1">Klik untuk tes ulang</span>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="startReflex()" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-md font-mono">
                            ⚡ Mulai Uji Kecepatan Refleks
                        </button>
                    </div>

                    <div class="lg:col-span-7 bg-slate-950/90 border border-blue-500/30 p-6 rounded-2xl space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-blue-400 font-mono">Hasil Skor Refleks:</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-[10px] font-mono font-bold" x-text="reflexTime > 0 ? reflexTime + ' ms' : 'Belum Ada Test'"></span>
                        </div>

                        <div class="p-4 bg-blue-950/40 border border-blue-500/20 rounded-xl space-y-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peringkat Kecepatan Santri:</h4>
                            <p class="text-sm font-black text-blue-300 font-serif-display leading-relaxed" x-text="reflexRank ? reflexRank : 'Silakan klik tombol Mulai Uji Kecepatan Refleks...'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- 3. Interactive Widget Kalender Masehi & Hijriah -->
        <div x-data="{
            monthOffset: 0,
            currentMonthName: '{{ $calendarData['masehi_month_name'] }}',
            currentHijriName: '{{ $calendarData['hijri_month_name'] }}',
            currentTimeStr: '{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB',
            init() {
                setInterval(() => {
                    const now = new Date();
                    this.currentTimeStr = now.toLocaleTimeString('id-ID', { timeZone: 'Asia/Jakarta' }) + ' WIB (Asia/Jakarta)';
                }, 1000);
            }
        }" class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm space-y-6">
            
            <!-- Calendar Header with Prev/Next Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Kalender Masehi &amp; Hijriah Interaktif</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Penanggalan Masehi &amp; Hijriah resmi dengan zona waktu <strong class="text-emerald-600 dark:text-emerald-400">Asia/Jakarta (WIB)</strong>.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-mono font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span x-text="currentTimeStr"></span>
                    </div>

                    <div class="px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs font-black">
                        🗓️ Masehi: <strong x-text="currentMonthName"></strong>
                    </div>

                    <div class="px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl text-xs font-black">
                        🌙 Hijriah: <strong x-text="currentHijriName"></strong>
                    </div>
                </div>
            </div>

            <!-- Main Calendar Grid & Important Events Split Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left: Highlight Today & Hari-Hari Penting (5 Cols) -->
                <div class="lg:col-span-5 space-y-5">
                    <!-- Today Highlight Box -->
                    <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 text-white border border-slate-800 p-6 rounded-3xl shadow-lg relative overflow-hidden space-y-4">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Waktu Sekarang (WIB)
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono" x-text="currentTimeStr"></span>
                        </div>

                        <div class="space-y-1">
                            <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Tanggal Masehi Hari Ini</span>
                            <h2 class="text-2xl font-black text-white font-serif-display tracking-tight">{{ $calendarData['today_masehi'] }}</h2>
                        </div>

                        <div class="pt-3 border-t border-slate-800 space-y-1">
                            <span class="text-xs text-amber-400/90 uppercase font-bold tracking-wider flex items-center gap-1">
                                🌙 Tanggal Hijriah Hari Ini
                            </span>
                            <h3 class="text-xl font-extrabold text-amber-300 tracking-tight">{{ $calendarData['today_hijri'] }}</h3>
                        </div>
                    </div>

                    <!-- Important Days & PHBI Card -->
                    <div class="bg-slate-50/80 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 p-5 rounded-3xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800 pb-3">
                            <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span>📌 Agenda &amp; Hari Penting (PHBI &amp; Nasional)</span>
                            </h4>
                        </div>

                        <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                            @foreach($importantEvents as $evt)
                                <div class="p-3 bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 rounded-2xl flex items-center justify-between gap-3 text-xs shadow-xs hover:border-emerald-500/40 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl shrink-0">{{ $evt['icon'] }}</span>
                                        <div class="space-y-0.5">
                                            <h5 class="font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ $evt['title'] }}</h5>
                                            <span class="text-[10px] text-slate-400 block font-mono">{{ $evt['date'] }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider shrink-0 shadow-xs border
                                        {{ $evt['category'] === 'nasional' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20' : '' }}
                                        {{ $evt['category'] === 'santri' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : '' }}
                                        {{ $evt['category'] === 'islamic' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20' : '' }}
                                    ">
                                        {{ $evt['badge'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Monthly Grid Calendar (7 Cols) -->
                <div class="lg:col-span-7 bg-slate-50/80 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800/80 p-5 rounded-3xl space-y-4">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                Grid Penanggalan Bulanan
                            </h4>
                            <p class="text-[10px] text-slate-400">Atas: Masehi &bull; Bawah: Hijriah</p>
                        </div>

                        <!-- Reset / Today Button -->
                        <button type="button" @click="monthOffset = 0; $dispatch('toast', 'Kembali ke bulan berjalan');" class="px-3 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-extrabold rounded-xl transition-all">
                            Hari Ini
                        </button>
                    </div>

                    <!-- Days Header -->
                    <div class="grid grid-cols-7 gap-1 sm:gap-1.5 text-center text-[10px] sm:text-[11px] font-black uppercase text-slate-400 tracking-wider py-1.5 border-b border-slate-200/60 dark:border-slate-800">
                        <span class="text-rose-500">Ahd</span>
                        <span>Sen</span>
                        <span>Sel</span>
                        <span>Rab</span>
                        <span>Kam</span>
                        <span class="text-emerald-500">Jum</span>
                        <span>Sab</span>
                    </div>

                    <!-- Days Grid -->
                    <div class="grid grid-cols-7 gap-1 sm:gap-2">
                        @foreach($calendarData['calendar_days'] as $day)
                            @if(is_null($day))
                                <div class="h-10 sm:h-14 bg-transparent rounded-xl sm:rounded-2xl"></div>
                            @else
                                <div class="h-10 sm:h-14 rounded-xl sm:rounded-2xl border p-1 sm:p-2 flex flex-col justify-between transition-all relative overflow-hidden group cursor-pointer {{ $day['is_today'] ? 'bg-emerald-600 text-white border-emerald-500 shadow-md font-bold ring-2 ring-emerald-400/50' : 'bg-white dark:bg-slate-900 border-slate-200/70 dark:border-slate-800 text-slate-800 dark:text-slate-200 hover:border-emerald-500/50 hover:shadow-xs' }}">
                                    <div class="flex items-center justify-between leading-none">
                                        <span class="text-[10px] sm:text-xs font-extrabold {{ $day['is_today'] ? 'text-white' : '' }}">{{ $day['day'] }}</span>
                                    </div>
                                    <div class="text-right leading-none">
                                        <span class="text-[8px] sm:text-[9px] font-bold {{ $day['is_today'] ? 'text-amber-200' : 'text-amber-600 dark:text-amber-400' }}">{{ $day['hijri_day'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="pt-2 text-center text-[10px] text-slate-400">
                        <span>💡 Kalender Masehi &amp; Hijriah disinkronkan secara otomatis sesuai dengan penetapan zona waktu <strong class="text-slate-600 dark:text-slate-300">WIB (Asia/Jakarta)</strong>.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Role-Based Quick Actions Shortcuts Grid (Di Bawah Kalender) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 p-6 sm:p-8 rounded-3xl shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white uppercase tracking-wider font-serif-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Pintasan Aksi Cepat Operasional</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Modul utama yang siap diakses berdasarkan wewenang otorisasi akun Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Keuangan: Pusat Keuangan & Kasir -->
                @if($user->hasRole('super-admin') || $user->hasRole('bendahara-pondok') || $user->hasRole('bendahara-unit') || $user->hasRole('bendahara-putra') || $user->hasRole('bendahara-putri') || $user->can('view-tagihan') || $user->can('record-pembayaran') || $user->can('manage-billing-config'))
                    <a href="{{ route('keuangan.billing') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 text-sm">Pusat Keuangan &amp; Kasir</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kasir utama, pembayaran syahriah, dan konfigurasi tarif.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Lembar Setoran Kolektif -->
                @if($user->hasRole('super-admin') || $user->hasRole('bendahara-pondok') || $user->hasRole('manajemen') || $user->hasRole('pengasuh') || $user->can('manage-setoran-kolektif'))
                    <a href="{{ route('keuangan.lembar-setoran') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-teal-600 dark:group-hover:text-teal-400 text-sm">Lembar Setoran Kolektif</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Verifikasi setoran kasir harian ke bendahara pusat.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-teal-600 dark:text-teal-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Perizinan Santri -->
                @can('view-perizinan')
                    <a href="{{ route('kepengasuhan.perizinan') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-blue-500/50 hover:bg-blue-50/20 dark:hover:bg-blue-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 text-sm">Buku Perizinan Santri</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Catat izin santri keluar, checkout/checkin, dan persetujuan.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endcan

                <!-- Buku Pelanggaran -->
                @can('view-pelanggaran')
                    <a href="{{ route('kepengasuhan.violations') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-rose-500/50 hover:bg-rose-50/20 dark:hover:bg-rose-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-sm">Buku Pelanggaran</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Pencatatan jenis pelanggaran &amp; akumulasi poin santri.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endcan

                <!-- Master Data Santri -->
                <a href="{{ route('kepengasuhan.peta-santri') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-purple-500/50 hover:bg-purple-50/20 dark:hover:bg-purple-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-purple-400 text-sm">Master Tabel Santri</h4>
                            <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kelola biodata santri mukim, laju, dan riwayat status santri.</p>
                        </div>
                    </div>
                    <span class="mt-4 text-[11px] font-bold text-purple-600 dark:text-purple-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                </a>

                <!-- Asrama & Kamar -->
                @if($user->hasRole('super-admin') || $user->hasRole('pengasuh') || $user->can('manage-asrama'))
                    <a href="{{ route('kepengasuhan.asrama-kelas') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-amber-500/50 hover:bg-amber-50/20 dark:hover:bg-amber-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 text-sm">Pusat Kendali Asrama &amp; Kelas</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Kapasitas gedung, peta kamar, dan rombel madrasah.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Majek / Katering -->
                @if($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->hasRole('pengasuh') || $user->can('manage-majek'))
                    <a href="{{ route('keuangan.majek') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-orange-500/50 hover:bg-orange-50/20 dark:hover:bg-orange-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 text-sm">Majek (Katering Santri)</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Manajemen katering porsi makan pagi &amp; sore santri.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-orange-600 dark:text-orange-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- Wizard Kenaikan Kelas -->
                @if($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->can('manage-kenaikan-kelas'))
                    <a href="{{ route('madrasah.kenaikan-kelas') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-indigo-500/50 hover:bg-indigo-50/20 dark:hover:bg-indigo-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 text-sm">Wizard Kenaikan Kelas</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Proses promosi kenaikan &amp; kelulusan kelas massal.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif

                <!-- CMS Portal Wali -->
                @if($user->hasRole('super-admin') || $user->hasRole('manajemen') || $user->can('manage-roles'))
                    <a href="{{ route('system.wali-cms') }}" class="group p-5 border border-slate-200/80 dark:border-slate-800 hover:border-emerald-500/50 hover:bg-emerald-50/20 dark:hover:bg-emerald-950/20 rounded-2xl transition-all duration-300 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 text-sm">CMS Portal Wali</h4>
                                <p class="text-slate-400 text-xs mt-1 leading-relaxed">Pengaturan nomor rekening bank &amp; WA Bendahara.</p>
                            </div>
                        </div>
                        <span class="mt-4 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Modul &rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
