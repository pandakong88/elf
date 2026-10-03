<div>
<style>
    .elvith-card-shell {
        position: relative;
        width: 100%;
        aspect-ratio: 5 / 7;
        min-height: 185px;
        max-height: 310px;
    }
    .elvith-3d-scene {
        perspective: 1000px;
        -webkit-perspective: 1000px;
    }
    .elvith-flipper {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        -webkit-transform-style: preserve-3d;
        transition: transform 0.65s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .elvith-flipper.flipped {
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
    }
    .elvith-card-face {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
    .elvith-card-back {
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
    }
</style>
<div x-data="{
    audioCtx: null,
    soundEnabled: true,
    isFlipped: false,
    revealingState: null, // 'won', 'tie', 'lost'
    roundStatusText: '',
    animationTimeout: null,
    
    initAudio() {
        if (!this.audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.audioCtx = new AudioContext();
            }
        }
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
    },

    triggerHaptic(type = 'success') {
        if (window.navigator && window.navigator.vibrate) {
            try {
                if (type === 'success') {
                    window.navigator.vibrate(35);
                } else if (type === 'error') {
                    window.navigator.vibrate([60, 40, 60]);
                }
            } catch (e) {}
        }
    },

    playCardFlip() {
        if (!this.soundEnabled) return;
        this.initAudio();
        if (!this.audioCtx) return;

        const ctx = this.audioCtx;
        const now = ctx.currentTime;
        const bufferSize = ctx.sampleRate * 0.09;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
            data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.25));
        }
        const noise = ctx.createBufferSource();
        noise.buffer = buffer;
        const filter = ctx.createBiquadFilter();
        filter.type = 'bandpass';
        filter.frequency.setValueAtTime(700, now);
        filter.frequency.exponentialRampToValueAtTime(250, now + 0.09);

        const gain = ctx.createGain();
        gain.gain.setValueAtTime(0.35, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.09);

        noise.connect(filter);
        filter.connect(gain);
        gain.connect(ctx.destination);
        noise.start(now);
    },

    playDing(isTie = false) {
        if (!this.soundEnabled) return;
        this.initAudio();
        if (!this.audioCtx) return;

        const ctx = this.audioCtx;
        const now = ctx.currentTime;

        if (isTie) {
            // Harmoni Seri
            [523.25, 659.25, 783.99].forEach((freq, i) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, now + i * 0.07);
                gain.gain.setValueAtTime(0.2, now + i * 0.07);
                gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.07 + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now + i * 0.07);
                osc.stop(now + i * 0.07 + 0.4);
            });
        } else {
            // Lonceng Kemenangan
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, now);
            osc.frequency.exponentialRampToValueAtTime(1760, now + 0.18);
            gain.gain.setValueAtTime(0.25, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.3);
        }
    },

    playGameOver() {
        if (!this.soundEnabled) return;
        this.initAudio();
        if (!this.audioCtx) return;

        const ctx = this.audioCtx;
        const now = ctx.currentTime;
        [280, 210, 150].forEach((freq, idx) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(freq, now + idx * 0.16);
            gain.gain.setValueAtTime(0.2, now + idx * 0.16);
            gain.gain.exponentialRampToValueAtTime(0.01, now + idx * 0.16 + 0.22);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now + idx * 0.16);
            osc.stop(now + idx * 0.16 + 0.25);
        });
    },

    launchConfetti() {
        const count = 50;
        const container = document.getElementById('card-arena-container');
        if (!container) return;

        for (let i = 0; i < count; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'absolute rounded-sm pointer-events-none z-50';
            const colors = ['#f59e0b', '#10b981', '#3b82f6', '#ec4899', '#8b5cf6', '#ef4444'];
            const color = colors[Math.floor(Math.random() * colors.length)];
            const size = Math.floor(Math.random() * 8) + 5;
            const left = Math.floor(Math.random() * 100);
            
            confetti.style.width = `${size}px`;
            confetti.style.height = `${size * 0.6}px`;
            confetti.style.backgroundColor = color;
            confetti.style.left = `${left}%`;
            confetti.style.top = '10%';
            confetti.style.opacity = '1';
            confetti.style.transition = 'all 1.6s cubic-bezier(0.25, 1, 0.5, 1)';
            
            container.appendChild(confetti);

            setTimeout(() => {
                const moveX = (Math.random() - 0.5) * 260;
                const moveY = Math.random() * 350 + 120;
                const rot = Math.random() * 720;
                confetti.style.transform = `translate(${moveX}px, ${moveY}px) rotate(${rot}deg)`;
                confetti.style.opacity = '0';
            }, 20);

            setTimeout(() => {
                confetti.remove();
            }, 1800);
        }
    }
}" 
x-init="
    $wire.on('game-started', () => { 
        isFlipped = false;
        revealingState = null;
        playCardFlip(); 
    });

    $wire.on('start-reveal-animation', (...args) => {
        let data = {};
        if (args && args.length > 0) {
            if (Array.isArray(args[0])) {
                data = args[0][0] || {};
            } else if (typeof args[0] === 'object' && args[0] !== null) {
                data = args[0].detail ? args[0].detail : args[0];
            }
        }

        const isTie = (data.isTie === true) || ($wire.roundResult === 'tie');
        const isCorrect = (data.isCorrect === true) || ($wire.isRoundWon === true);

        // 1. Bunyikan efek suara kartu berputar
        playCardFlip();
        
        // 2. Putar kartu misteri (3D Flip)
        isFlipped = true;

        if (isTie) {
            revealingState = 'tie';
            roundStatusText = '🤝 HASIL SERI! AMAN!';
            triggerHaptic('success');
            setTimeout(() => { playDing(true); }, 220);
        } else if (isCorrect) {
            revealingState = 'won';
            roundStatusText = '🎉 TEBAKAN TEPAT! MENANG!';
            triggerHaptic('success');
            setTimeout(() => { playDing(false); }, 220);
        } else {
            revealingState = 'lost';
            roundStatusText = '💀 TEBAKAN SALAH!';
            triggerHaptic('error');
            setTimeout(() => { playGameOver(); }, 220);
        }

        // 3. Berikan waktu jeda animasi agar pemain bisa melihat kartu yang keluar dengan jelas!
        clearTimeout(animationTimeout);
        animationTimeout = setTimeout(() => {
            if (isCorrect || isTie) {
                isFlipped = false;
                revealingState = null;
                $wire.advanceRound();
            } else {
                $wire.finalizeGameOver();
            }
        }, 1400);
    });

    $wire.on('show-game-over-summary', (event) => {
        if (event.isNewHighScore) {
            launchConfetti();
        }
    });
"
@keydown.window="
    if ($event.target.tagName === 'INPUT' || $event.target.tagName === 'TEXTAREA') return;
    if ($wire.gameState === 'playing' && !isFlipped) {
        if ($event.key === 'ArrowUp' || $event.key === 'w' || $event.key === 'W') {
            $event.preventDefault();
            $wire.guess('higher');
        } else if ($event.key === 'ArrowDown' || $event.key === 's' || $event.key === 'S') {
            $event.preventDefault();
            $wire.guess('lower');
        }
    } else if ($wire.gameState === 'idle' || $wire.gameState === 'game_over') {
        if ($event.key === 'Enter') {
            $event.preventDefault();
            $wire.startGame();
        }
    }
"
id="card-arena-container"
class="relative w-full rounded-2xl sm:rounded-3xl overflow-hidden border border-amber-500/30 shadow-2xl bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white"
>
    <!-- Background Velvet Felt Glow -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-950/60 via-slate-950/90 to-slate-950 pointer-events-none"></div>
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header Bar Arena (Responsive Mobile Friendly) -->
    <div class="relative z-10 px-3 sm:px-6 py-3 sm:py-4 border-b border-slate-800/80 bg-slate-950/60 backdrop-blur-md flex items-center justify-between gap-2 sm:gap-4">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-amber-600 to-yellow-400 p-0.5 shadow-lg shadow-amber-500/20 flex items-center justify-center shrink-0">
                <div class="w-full h-full bg-slate-950 rounded-[10px] sm:rounded-[14px] flex items-center justify-center text-lg sm:text-xl">
                    🃏
                </div>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <h2 class="text-sm sm:text-lg font-black tracking-wide bg-gradient-to-r from-amber-200 via-yellow-300 to-amber-400 bg-clip-text text-transparent font-serif-display uppercase truncate">
                        Elvith Royale: Hi-Lo
                    </h2>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-300 border border-amber-500/30 shrink-0">
                        Live Board
                    </span>
                </div>
                <p class="hidden sm:block text-xs text-slate-400 truncate">
                    Bandingkan kartu: <span class="text-emerald-400 font-bold">▲ BESAR</span> atau <span class="text-rose-400 font-bold">▼ KECIL</span>!
                </p>
            </div>
        </div>

        <!-- Rules & Sound Control -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <button 
                type="button" 
                @click="soundEnabled = !soundEnabled"
                class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-700/80 bg-slate-900/80 hover:bg-slate-800 text-[11px] sm:text-xs font-semibold text-slate-300 transition flex items-center gap-1 shadow-sm"
                :title="soundEnabled ? 'Matikan Suara SFX' : 'Aktifkan Suara SFX'"
            >
                <span x-text="soundEnabled ? '🔊 SFX' : '🔇 Bisu'"></span>
            </button>
            <a 
                href="#leaderboard-section" 
                class="xl:hidden px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/30 text-[11px] font-bold text-amber-300 hover:bg-amber-500/20 transition flex items-center gap-1"
            >
                🏆 <span class="hidden xs:inline">Rank</span>
            </a>
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/90 border border-slate-800 text-[11px] text-slate-400 font-mono">
                <span class="text-amber-300 font-bold">As = 14</span>
                <span>•</span>
                <span class="text-emerald-400 font-bold">Seri = Aman</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Card Table (Left/Center) + Live Leaderboard (Right) -->
    <div class="relative z-10 grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 p-2.5 sm:p-5 lg:p-8">
        
        <!-- CARD TABLE ARENA (8 Cols) -->
        <div class="xl:col-span-8 flex flex-col items-center justify-between rounded-2xl sm:rounded-3xl p-3 sm:p-6 lg:p-8 border border-emerald-900/50 bg-gradient-to-b from-[#063327]/90 via-[#032219]/95 to-[#021811] shadow-[inset_0_2px_15px_rgba(0,0,0,0.6)] relative overflow-hidden min-h-[460px] sm:min-h-[560px]">
            
            <!-- Table Texture / Felt Pattern -->
            <div class="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute inset-1.5 sm:inset-2.5 rounded-[16px] sm:rounded-[22px] border border-amber-500/20 pointer-events-none"></div>

            <!-- Top Game HUD (Score, Message, Streak) - Mobile Optimized -->
            <div class="w-full flex items-center justify-between gap-1.5 sm:gap-4 z-10 mb-2 sm:mb-4">
                <!-- Streak Meter -->
                <div class="flex items-center gap-1.5 sm:gap-3 bg-slate-950/85 backdrop-blur-md px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl border border-emerald-500/30 shadow-lg">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black text-sm sm:text-lg shrink-0">
                        🔥
                    </div>
                    <div>
                        <div class="text-[8px] sm:text-[10px] uppercase font-bold tracking-wider text-slate-400 leading-none">Streak</div>
                        <div class="text-sm sm:text-xl font-black text-amber-400 font-mono leading-none mt-0.5">
                            {{ $streak }}<span class="text-[10px] sm:text-xs text-amber-500 font-bold">x</span>
                        </div>
                    </div>
                </div>

                <!-- Center Status Banner -->
                <div class="flex-1 max-w-sm text-center px-1">
                    @if($gameState === 'playing' || $gameState === 'revealing')
                        <div 
                            class="inline-block px-2.5 sm:px-4 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-bold border transition duration-300 shadow-md leading-tight"
                            :class="{
                                'bg-emerald-500/25 text-emerald-300 border-emerald-400/50 animate-pulse': revealingState === 'won',
                                'bg-amber-500/25 text-amber-300 border-amber-400/50 animate-pulse': revealingState === 'tie',
                                'bg-rose-500/25 text-rose-300 border-rose-400/50 animate-pulse': revealingState === 'lost',
                                'bg-slate-900/80 text-slate-300 border-slate-700/60': !revealingState
                            }"
                        >
                            <span x-text="roundStatusText || '{{ addslashes($resultMessage) }}'"></span>
                        </div>
                    @elseif($gameState === 'game_over')
                        <div class="inline-block px-2.5 sm:px-4 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-md leading-tight">
                            💀 GAME OVER — {{ $resultMessage }}
                        </div>
                    @else
                        <div class="inline-block px-2.5 sm:px-4 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-bold bg-amber-500/10 text-amber-300 border border-amber-500/30 shadow-md">
                            Klik Mulai untuk main!
                        </div>
                    @endif
                </div>

                <!-- Score Board -->
                <div class="flex items-center gap-1.5 sm:gap-3 bg-slate-950/85 backdrop-blur-md px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl border border-amber-500/30 shadow-lg">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-sm sm:text-lg shrink-0">
                        💎
                    </div>
                    <div class="text-right">
                        <div class="text-[8px] sm:text-[10px] uppercase font-bold tracking-wider text-slate-400 leading-none">Skor</div>
                        <div class="text-sm sm:text-xl font-black text-emerald-400 font-mono leading-none mt-0.5">
                            {{ number_format($score) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Playing Area: SIDE-BY-SIDE ON ALL SCREENS (Mobile & Desktop) -->
            <div class="relative z-10 flex flex-col items-center justify-center my-auto py-2 sm:py-4 w-full">
                
                @if($gameState === 'idle')
                    <!-- Idle Screen: Ready to Play -->
                    <div class="flex flex-col items-center text-center space-y-4 sm:space-y-6">
                        <div class="relative w-40 h-56 sm:w-56 sm:h-80 group cursor-pointer" wire:click="startGame">
                            <div class="absolute inset-0 translate-x-2 translate-y-2 rounded-2xl bg-slate-900/90 border border-slate-700/50 shadow-xl"></div>
                            <div class="absolute inset-0 translate-x-1 translate-y-1 rounded-2xl bg-slate-800/90 border border-slate-700/60 shadow-xl"></div>
                            
                            <!-- Top Realistic Card Back -->
                            <div class="relative w-full h-full rounded-2xl p-2.5 sm:p-3 bg-white shadow-2xl transition transform group-hover:-translate-y-2 group-hover:shadow-[0_25px_50px_rgba(0,0,0,0.6)] flex items-center justify-center border border-slate-300">
                                <div class="w-full h-full rounded-xl bg-gradient-to-br from-blue-900 via-indigo-950 to-blue-950 border-4 border-amber-400/80 relative overflow-hidden flex flex-col items-center justify-center text-amber-300 p-3 sm:p-4">
                                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#fbbf24_2px,transparent_2px)] [background-size:12px_12px]"></div>
                                    <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-full border-2 border-amber-400/60 flex items-center justify-center relative">
                                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full border border-amber-400/40 flex items-center justify-center text-2xl sm:text-3xl font-serif">
                                            🂠
                                        </div>
                                    </div>
                                    <span class="mt-2.5 sm:mt-4 text-[10px] sm:text-[11px] font-black uppercase tracking-widest text-amber-200 font-serif-display">
                                        ELVITH DECK
                                    </span>
                                    <span class="text-[8px] sm:text-[9px] text-amber-400/80 font-mono mt-0.5">52 Acak Realistis</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button 
                                type="button" 
                                wire:click="startGame"
                                class="px-6 sm:px-8 py-3 sm:py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-xl shadow-amber-500/25 transform hover:-translate-y-0.5 active:scale-95 transition duration-150 flex items-center gap-2 cursor-pointer mx-auto touch-manipulation"
                            >
                                <span>🃏 MULAI PERMAINAN</span>
                                <span class="hidden sm:inline px-2 py-0.5 bg-black/15 text-[10px] font-mono">ENTER</span>
                            </button>
                            <p class="text-[11px] text-slate-400 mt-2">Sentuh tombol untuk mengocok kartu</p>
                        </div>
                    </div>

                @else
                    <!-- Active Gameplay: TWO CARDS SIDE-BY-SIDE (Optimized for Mobile) -->
                    <div class="flex flex-row items-center justify-center gap-1.5 xs:gap-3 sm:gap-6 lg:gap-10 w-full max-w-3xl px-0.5 sm:px-0">
                        
                        <!-- CARD 1: KARTU SAAT INI (FACE UP) -->
                        <div class="flex flex-col items-center flex-1 max-w-[170px] sm:max-w-[220px]">
                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-400 mb-1 sm:mb-2 font-mono flex items-center gap-1 truncate">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                                <span>Kartu Kamu</span>
                            </span>

                            <div class="elvith-card-shell rounded-xl sm:rounded-2xl p-2 sm:p-3.5 bg-gradient-to-br from-white via-[#fafafc] to-[#f0f0f4] shadow-[0_12px_30px_rgba(0,0,0,0.6),0_0_0_1px_rgba(255,255,255,0.9)_inset] border border-slate-300 flex flex-col justify-between select-none text-{{ $currentCard['color'] === 'red' ? 'rose-600' : 'slate-900' }}">
                                <!-- Top-Left Index -->
                                <div class="flex flex-col items-start leading-none space-y-0.5">
                                    <span class="text-xl xs:text-2xl sm:text-3xl font-black font-serif-display">{{ $currentCard['rank_label'] }}</span>
                                    <span class="text-sm xs:text-base sm:text-xl font-black">{!! $currentCard['symbol_html'] !!}</span>
                                </div>

                                <!-- Center Artwork -->
                                <div class="flex flex-col items-center justify-center my-auto">
                                    @if($currentCard['rank'] === 14)
                                        <div class="text-4xl xs:text-5xl sm:text-6xl font-serif font-black drop-shadow-md leading-none">
                                            {!! $currentCard['symbol_html'] !!}
                                        </div>
                                        <div class="mt-1 px-1.5 sm:px-2.5 py-0.5 rounded-full border border-current text-[7px] sm:text-[8px] font-black uppercase tracking-widest bg-current/5">
                                            ★ ACE (14) ★
                                        </div>
                                    @elseif($currentCard['rank'] >= 11)
                                        <div class="text-3xl xs:text-4xl sm:text-5xl">
                                            @if($currentCard['rank'] === 13) 👑 @elseif($currentCard['rank'] === 12) 👸 @else 🗡️ @endif
                                        </div>
                                        <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider mt-0.5 truncate max-w-full">
                                            {{ $currentCard['rank_label'] }} {{ $currentCard['suit_label'] }}
                                        </span>
                                        <span class="text-[8px] sm:text-[10px] font-mono opacity-70">Nilai: {{ $currentCard['rank'] }}</span>
                                    @else
                                        <div class="text-3xl xs:text-4xl sm:text-6xl font-black leading-none">
                                            {!! $currentCard['symbol_html'] !!}
                                        </div>
                                        <span class="text-[9px] sm:text-xs font-mono font-bold mt-1 text-slate-600">Nilai: {{ $currentCard['rank'] }}</span>
                                    @endif
                                </div>

                                <!-- Bottom-Right Index -->
                                <div class="flex flex-col items-end leading-none space-y-0.5 rotate-180">
                                    <span class="text-xl xs:text-2xl sm:text-3xl font-black font-serif-display">{{ $currentCard['rank_label'] }}</span>
                                    <span class="text-sm xs:text-base sm:text-xl font-black">{!! $currentCard['symbol_html'] !!}</span>
                                </div>
                            </div>

                            <span class="text-[10px] sm:text-xs font-mono text-slate-300 mt-1 sm:mt-2 font-bold truncate max-w-full text-center">
                                {{ $currentCard['title'] }}
                            </span>
                        </div>

                        <!-- VS / CHOICE INDICATOR -->
                        <div class="flex flex-col items-center justify-center my-auto shrink-0 px-0.5 sm:px-2">
                            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-slate-950/90 border border-amber-500/40 text-amber-300 flex items-center justify-center font-black text-[10px] sm:text-xs shadow-xl font-mono">
                                VS
                            </div>
                            @if($lastGuess)
                                <div class="mt-1 sm:mt-2 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-lg sm:rounded-xl text-[8px] sm:text-[10px] font-black uppercase font-mono tracking-wider {{ $lastGuess === 'higher' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                    {{ $lastGuess === 'higher' ? '▲ BESAR' : '▼ KECIL' }}
                                </div>
                            @endif
                        </div>

                        <!-- CARD 2: KARTU BERIKUTNYA DENGAN EFEK 3D FLIP ANIMATION -->
                        <div class="flex flex-col items-center flex-1 max-w-[170px] sm:max-w-[220px]">
                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-400 mb-1 sm:mb-2 font-mono flex items-center gap-1 truncate">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping shrink-0"></span>
                                <span>Berikutnya</span>
                            </span>

                            <!-- 3D Card Container with Perspective -->
                            <div class="elvith-card-shell elvith-3d-scene">
                                <!-- Inner Flipper -->
                                <div 
                                    class="elvith-flipper"
                                    :class="{ 'flipped': isFlipped }"
                                >
                                    <!-- FRONT (CARD BACK / FACEDOWN MISTERI) -->
                                    <div class="elvith-card-face rounded-xl sm:rounded-2xl p-2 sm:p-3 bg-white shadow-2xl border border-slate-300">
                                        <div class="w-full h-full rounded-lg sm:rounded-xl bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-950 border-2 sm:border-4 border-amber-400/80 relative overflow-hidden flex flex-col items-center justify-center text-amber-300 p-2 sm:p-3 text-center">
                                            <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#fbbf24_2px,transparent_2px)] [background-size:12px_12px]"></div>
                                            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full border-2 border-amber-400/60 flex items-center justify-center relative shadow-inner">
                                                <span class="text-xl sm:text-2xl font-black animate-pulse">❓</span>
                                            </div>
                                            <span class="mt-2 sm:mt-3 text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-amber-200 font-serif-display">
                                                MISTERI
                                            </span>
                                            <span class="text-[7px] sm:text-[8px] text-amber-400/80 font-mono mt-0.5">Tebak!</span>
                                        </div>
                                    </div>

                                    <!-- BACK (REVEALED REAL CARD) [Rotated 180deg] -->
                                    @if($nextCard)
                                        <div 
                                            class="elvith-card-face elvith-card-back rounded-xl sm:rounded-2xl p-2 sm:p-3.5 bg-gradient-to-br from-white via-[#fafafc] to-[#f0f0f4] shadow-[0_12px_30px_rgba(0,0,0,0.6),0_0_0_1px_rgba(255,255,255,0.9)_inset] border-2 flex flex-col justify-between select-none text-{{ $nextCard['color'] === 'red' ? 'rose-600' : 'slate-900' }}"
                                            :class="{
                                                'border-emerald-500 shadow-[0_0_25px_rgba(16,185,129,0.5)]': revealingState === 'won',
                                                'border-amber-400 shadow-[0_0_25px_rgba(251,191,36,0.5)]': revealingState === 'tie',
                                                'border-rose-500 shadow-[0_0_25px_rgba(244,63,94,0.5)]': revealingState === 'lost',
                                                'border-slate-300': !revealingState
                                            }"
                                        >
                                            <!-- Top-Left Index -->
                                            <div class="flex flex-col items-start leading-none space-y-0.5">
                                                <span class="text-xl xs:text-2xl sm:text-3xl font-black font-serif-display">{{ $nextCard['rank_label'] }}</span>
                                                <span class="text-sm xs:text-base sm:text-xl font-black">{!! $nextCard['symbol_html'] !!}</span>
                                            </div>

                                            <!-- Center Artwork -->
                                            <div class="flex flex-col items-center justify-center my-auto">
                                                @if($nextCard['rank'] === 14)
                                                    <div class="text-4xl xs:text-5xl sm:text-6xl font-serif font-black drop-shadow-md leading-none">
                                                        {!! $nextCard['symbol_html'] !!}
                                                    </div>
                                                    <div class="mt-1 px-1.5 sm:px-2.5 py-0.5 rounded-full border border-current text-[7px] sm:text-[8px] font-black uppercase tracking-widest bg-current/5">
                                                        ★ ACE (14) ★
                                                    </div>
                                                @elseif($nextCard['rank'] >= 11)
                                                    <div class="text-3xl xs:text-4xl sm:text-5xl">
                                                        @if($nextCard['rank'] === 13) 👑 @elseif($nextCard['rank'] === 12) 👸 @else 🗡️ @endif
                                                    </div>
                                                    <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider mt-0.5 truncate max-w-full">
                                                        {{ $nextCard['rank_label'] }} {{ $nextCard['suit_label'] }}
                                                    </span>
                                                    <span class="text-[8px] sm:text-[10px] font-mono opacity-70">Nilai: {{ $nextCard['rank'] }}</span>
                                                @else
                                                    <div class="text-3xl xs:text-4xl sm:text-6xl font-black leading-none">
                                                        {!! $nextCard['symbol_html'] !!}
                                                    </div>
                                                    <span class="text-[9px] sm:text-xs font-mono font-bold mt-1 text-slate-600">Nilai: {{ $nextCard['rank'] }}</span>
                                                @endif
                                            </div>

                                            <!-- Bottom-Right Index -->
                                            <div class="flex flex-col items-end leading-none space-y-0.5 rotate-180">
                                                <span class="text-xl xs:text-2xl sm:text-3xl font-black font-serif-display">{{ $nextCard['rank_label'] }}</span>
                                                <span class="text-sm xs:text-base sm:text-xl font-black">{!! $nextCard['symbol_html'] !!}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <span class="text-[10px] sm:text-xs font-mono text-slate-300 mt-1 sm:mt-2 font-bold truncate max-w-full text-center" x-text="isFlipped ? '{{ $nextCard['title'] ?? '' }}' : 'Tertutup 🔒'"></span>
                        </div>

                    </div>
                @endif

            </div>

            <!-- Bottom Controller Action Bar: THUMB-FRIENDLY ON MOBILE -->
            <div class="w-full z-10 pt-3 sm:pt-4 border-t border-emerald-800/40">
                @if($gameState === 'playing' || $gameState === 'revealing')
                    <div class="flex flex-row items-center justify-center gap-2.5 sm:gap-4 max-w-xl mx-auto w-full px-0.5 sm:px-0">
                        
                        <!-- Button LEBIH BESAR -->
                        <button 
                            type="button" 
                            wire:click="guess('higher')" 
                            :disabled="isFlipped"
                            class="flex-1 w-full py-3.5 sm:py-4 px-3 sm:px-6 rounded-xl sm:rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 active:scale-95 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-emerald-600/30 flex items-center justify-center sm:justify-between gap-1.5 border border-emerald-300/40 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none touch-manipulation"
                        >
                            <span class="flex items-center gap-1.5 sm:gap-2">
                                <span class="text-lg sm:text-2xl">▲</span>
                                <span>LEBIH BESAR</span>
                            </span>
                            <span class="hidden sm:inline px-2 py-0.5 bg-black/25 rounded-md text-[10px] font-mono">W / ↑</span>
                        </button>

                        <!-- Button LEBIH KECIL -->
                        <button 
                            type="button" 
                            wire:click="guess('lower')" 
                            :disabled="isFlipped"
                            class="flex-1 w-full py-3.5 sm:py-4 px-3 sm:px-6 rounded-xl sm:rounded-2xl bg-gradient-to-r from-rose-600 via-rose-500 to-pink-600 hover:from-rose-500 hover:to-pink-500 active:scale-95 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-rose-600/30 flex items-center justify-center sm:justify-between gap-1.5 border border-rose-300/40 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none touch-manipulation"
                        >
                            <span class="flex items-center gap-1.5 sm:gap-2">
                                <span class="text-lg sm:text-2xl">▼</span>
                                <span>LEBIH KECIL</span>
                            </span>
                            <span class="hidden sm:inline px-2 py-0.5 bg-black/25 rounded-md text-[10px] font-mono">S / ↓</span>
                        </button>

                    </div>
                @elseif($gameState === 'game_over')
                    <!-- Game Over Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-4 max-w-md mx-auto w-full px-1">
                        <button 
                            type="button" 
                            wire:click="startGame"
                            class="w-full py-3 sm:py-3.5 px-6 rounded-xl sm:rounded-2xl bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-xl shadow-amber-500/30 transition transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 cursor-pointer touch-manipulation"
                        >
                            <span>🔄 MAIN LAGI SEKARANG</span>
                            <span class="hidden sm:inline px-2 py-0.5 bg-black/20 rounded-md text-[10px] font-mono">ENTER</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="resetGame"
                            class="w-full sm:w-auto py-2.5 sm:py-3.5 px-5 rounded-xl sm:rounded-2xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider border border-slate-700 transition cursor-pointer touch-manipulation"
                        >
                            Beranda Game
                        </button>
                    </div>
                @else
                    <div class="text-center text-[11px] sm:text-xs text-slate-400">
                        Tekan tombol <strong class="text-amber-300">Mulai Permainan</strong> di atas untuk bertarung di leaderboard!
                    </div>
                @endif
            </div>

        </div>

        <!-- LEADERBOARD PANEL (4 Cols) -->
        <div id="leaderboard-section" class="xl:col-span-4 flex flex-col space-y-4">
            
            <!-- User Status Card -->
            @if(Auth::check())
                <div class="bg-slate-950/80 rounded-2xl p-3.5 sm:p-4 border border-slate-800 shadow-lg relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-300 flex items-center justify-center font-bold text-slate-950 text-sm sm:text-base shadow-sm shrink-0">
                                🎖️
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                                <span class="text-[9px] sm:text-[10px] text-emerald-400 font-mono font-semibold">Pemain Terdaftar</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[9px] sm:text-[10px] uppercase text-slate-400 font-bold">Peringkat</span>
                            <div class="text-base sm:text-lg font-black text-amber-400 font-mono">
                                #{{ $userStats['rank'] ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-800 text-center font-mono">
                        <div class="bg-slate-900/60 p-1.5 sm:p-2 rounded-xl border border-slate-800/80">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">High Score</span>
                            <span class="text-xs sm:text-sm font-black text-emerald-400">{{ number_format($userStats['high_score'] ?? 0) }}</span>
                        </div>
                        <div class="bg-slate-900/60 p-1.5 sm:p-2 rounded-xl border border-slate-800/80">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">Max Streak</span>
                            <span class="text-xs sm:text-sm font-black text-amber-400">{{ $userStats['max_streak'] ?? 0 }}x</span>
                        </div>
                        <div class="bg-slate-900/60 p-1.5 sm:p-2 rounded-xl border border-slate-800/80">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">Main</span>
                            <span class="text-xs sm:text-sm font-black text-slate-300">{{ $userStats['total_games'] ?? 0 }}x</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Podium & Top 10 Leaderboard Table -->
            <div class="bg-slate-950/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800/90 shadow-xl flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3 sm:mb-4 pb-2 sm:pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="text-lg sm:text-xl">🏆</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-white font-serif-display uppercase tracking-wide">
                                Dewan Jawara Kartu
                            </h3>
                            <p class="text-[9px] sm:text-[10px] text-slate-400">Peringkat skor pemain se-Pesantren</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="loadLeaderboardData" 
                        class="text-xs text-amber-400 hover:text-amber-300 p-1 rounded-lg hover:bg-slate-800 transition"
                        title="Segarkan Leaderboard"
                    >
                        🔄
                    </button>
                </div>

                @if(count($leaderboard) === 0)
                    <div class="flex-1 flex flex-col items-center justify-center p-6 text-center text-slate-500 space-y-2">
                        <span class="text-3xl sm:text-4xl">🎴</span>
                        <p class="text-xs">Belum ada pemain yang mencatat rekor skor!</p>
                        <p class="text-[11px] text-amber-400 font-semibold">Jadilah yang pertama menaklukkan takhta juara!</p>
                    </div>
                @else
                    <!-- Top 3 Mini Podium -->
                    @if(count($leaderboard) >= 1)
                        <div class="grid grid-cols-3 gap-1.5 sm:gap-2 mb-3 sm:mb-4 items-end pt-2 sm:pt-4">
                            <!-- Juara 2 -->
                            <div class="flex flex-col items-center text-center">
                                @if(isset($leaderboard[1]))
                                    <span class="text-base sm:text-lg">🥈</span>
                                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-300 truncate w-full mt-0.5">{{ $leaderboard[1]['name'] }}</span>
                                    <span class="text-[9px] sm:text-[10px] font-mono text-emerald-400 font-bold">{{ number_format($leaderboard[1]['high_score']) }}</span>
                                    <div class="w-full h-9 sm:h-12 bg-slate-800/80 rounded-t-xl mt-1 border-t-2 border-slate-400 flex items-center justify-center text-[10px] sm:text-xs font-black text-slate-400">
                                        #2
                                    </div>
                                @else
                                    <div class="w-full h-8 sm:h-10 bg-slate-900/40 rounded-t-xl mt-1 border-t border-slate-800"></div>
                                @endif
                            </div>

                            <!-- Juara 1 (Paling Tinggi) -->
                            <div class="flex flex-col items-center text-center">
                                <span class="text-xl sm:text-2xl animate-bounce">👑</span>
                                <span class="text-[10px] sm:text-[11px] font-black text-amber-300 truncate w-full mt-0.5">{{ $leaderboard[0]['name'] }}</span>
                                <span class="text-[10px] sm:text-xs font-mono text-yellow-300 font-black">{{ number_format($leaderboard[0]['high_score']) }}</span>
                                <div class="w-full h-14 sm:h-18 bg-gradient-to-t from-amber-950/80 to-amber-600/30 rounded-t-xl mt-1 border-t-2 border-amber-400 flex items-center justify-center text-xs sm:text-sm font-black text-amber-300 shadow-md">
                                    🥇 #1
                                </div>
                            </div>

                            <!-- Juara 3 -->
                            <div class="flex flex-col items-center text-center">
                                @if(isset($leaderboard[2]))
                                    <span class="text-base sm:text-lg">🥉</span>
                                    <span class="text-[10px] sm:text-[11px] font-bold text-amber-200/80 truncate w-full mt-0.5">{{ $leaderboard[2]['name'] }}</span>
                                    <span class="text-[9px] sm:text-[10px] font-mono text-emerald-400 font-bold">{{ number_format($leaderboard[2]['high_score']) }}</span>
                                    <div class="w-full h-7 sm:h-9 bg-slate-800/60 rounded-t-xl mt-1 border-t-2 border-amber-700 flex items-center justify-center text-[10px] sm:text-xs font-black text-amber-700">
                                        #3
                                    </div>
                                @else
                                    <div class="w-full h-6 sm:h-8 bg-slate-900/40 rounded-t-xl mt-1 border-t border-slate-800"></div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- List Leaderboard Ranks 1 - 10 -->
                    <div class="space-y-1.5 overflow-y-auto max-h-48 sm:max-h-56 pr-1 custom-scrollbar">
                        @foreach($leaderboard as $player)
                            <div class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $player['is_current_user'] ? 'bg-amber-500/15 border border-amber-500/40 text-amber-200' : 'bg-slate-900/60 hover:bg-slate-900 border border-slate-800/80 text-slate-300' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-4 font-mono font-bold text-center {{ $player['rank'] === 1 ? 'text-amber-400' : ($player['rank'] === 2 ? 'text-slate-300' : ($player['rank'] === 3 ? 'text-amber-600' : 'text-slate-500')) }}">
                                        {{ $player['rank'] }}
                                    </span>
                                    <div class="min-w-0 truncate">
                                        <div class="font-bold truncate flex items-center gap-1">
                                            <span class="truncate">{{ $player['name'] }}</span>
                                            @if($player['is_current_user'])
                                                <span class="px-1 py-0.2 rounded text-[8px] bg-amber-400/20 text-amber-300 font-mono shrink-0">Kamu</span>
                                            @endif
                                        </div>
                                        <div class="text-[9px] text-slate-400 flex items-center gap-1.5">
                                            <span>Streak: <strong class="text-amber-400 font-mono">{{ $player['max_streak'] }}x</strong></span>
                                            <span>•</span>
                                            <span class="truncate">{{ $player['role'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right font-mono font-black text-emerald-400 shrink-0 pl-1">
                                    {{ number_format($player['high_score']) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>
</div>

