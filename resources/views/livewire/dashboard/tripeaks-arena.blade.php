<div>
<style>
    /* ========================================================
       TRI-PEAKS ROYALE - LUXURY CASINO FELT & ARCADE ANIMATIONS
       ======================================================== */
    .tripeaks-felt-mat {
        background: radial-gradient(circle at 50% 25%, #064e3b 0%, #04392b 50%, #022c22 80%, #011a13 100%);
        box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.85), 0 20px 40px rgba(0, 0, 0, 0.6);
        border: 2px solid rgba(245, 158, 11, 0.5);
    }

    .tripeaks-card {
        position: absolute;
        width: 9.4%;
        aspect-ratio: 5 / 7.2;
        border-radius: 0.45rem;
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s ease, filter 0.2s ease;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
    }
    @media (min-width: 640px) {
        .tripeaks-card {
            border-radius: 0.75rem;
        }
    }

    /* Expand touch hit area on mobile to prevent misclicks */
    .tripeaks-card::before {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: inherit;
        pointer-events: auto;
    }
    @media (min-width: 640px) {
        .tripeaks-card::before {
            inset: 0;
        }
    }

    /* Face-Up Luxury Playing Card */
    .tripeaks-card-face {
        width: 100%;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(155deg, #ffffff 0%, #fafafb 50%, #f1f1f5 100%);
        border: 1.5px solid rgba(203, 213, 225, 0.9);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.85);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2px 3px;
        position: relative;
        overflow: hidden;
    }
    @media (min-width: 640px) {
        .tripeaks-card-face {
            padding: 4px 6px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.45), inset 0 0 0 1px rgba(255, 255, 255, 0.9);
        }
    }

    /* Playable Card Glow (Selisih +-1) - Neon Arcade Pulse */
    @keyframes neon-gold-pulse {
        0% {
            box-shadow: 0 0 6px 1px rgba(251, 191, 36, 0.7), inset 0 0 4px rgba(251, 191, 36, 0.4);
            border-color: #f59e0b;
        }
        50% {
            box-shadow: 0 0 16px 4px rgba(251, 191, 36, 0.95), 0 0 28px 6px rgba(245, 158, 11, 0.45), inset 0 0 8px rgba(251, 191, 36, 0.6);
            border-color: #fef08a;
        }
        100% {
            box-shadow: 0 0 6px 1px rgba(251, 191, 36, 0.7), inset 0 0 4px rgba(251, 191, 36, 0.4);
            border-color: #f59e0b;
        }
    }

    .tripeaks-card-playable {
        cursor: pointer;
        border: 2px solid #fbbf24 !important;
        animation: neon-gold-pulse 1.3s infinite ease-in-out alternate;
        transform: translateY(-2px);
    }
    .tripeaks-card-playable:hover {
        transform: translateY(-5px) scale(1.08) !important;
        box-shadow: 0 0 22px 6px rgba(251, 191, 36, 0.95), 0 12px 24px rgba(0, 0, 0, 0.6) !important;
        z-index: 60 !important;
    }
    .tripeaks-card-playable:active {
        transform: translateY(0px) scale(0.92) !important;
        filter: brightness(1.2);
    }

    /* Face-Down Card Back (Royal Diamond Pattern) */
    .tripeaks-card-back {
        width: 100%;
        height: 100%;
        border-radius: inherit;
        background: radial-gradient(circle at 50% 50%, #1e3a8a 0%, #172554 100%);
        border: 2px solid #38bdf8;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        position: relative;
        overflow: hidden;
    }
    .tripeaks-card-back::before {
        content: "";
        position: absolute;
        inset: 2px;
        border: 1px dashed rgba(255, 255, 255, 0.35);
        border-radius: inherit;
    }
    .tripeaks-card-back::after {
        content: "♠";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(56, 189, 248, 0.3);
        font-size: 1rem;
    }
    @media (min-width: 640px) {
        .tripeaks-card-back::after {
            font-size: 1.25rem;
        }
    }

    /* 3D Flip Card Reveal Animation */
    @keyframes card-flip-reveal {
        0% { transform: scale(0.9) rotateY(180deg); }
        50% { transform: scale(1.15) rotateY(90deg); filter: brightness(1.4); }
        100% { transform: scale(1) rotateY(0deg); filter: brightness(1); }
    }
    .card-flip-reveal {
        animation: card-flip-reveal 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    /* Card Shake on Invalid Tap */
    @keyframes card-shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-4px) rotate(-3deg); }
        40%, 80% { transform: translateX(4px) rotate(3deg); }
    }
    .card-shake {
        animation: card-shake 0.35s ease-in-out !important;
    }

    /* Floating Point / Combo Toast */
    @keyframes float-up-fade {
        0% { transform: translate(-50%, 0) scale(0.8); opacity: 1; }
        50% { transform: translate(-50%, -24px) scale(1.2); opacity: 1; }
        100% { transform: translate(-50%, -48px) scale(1); opacity: 0; }
    }
    .floating-toast {
        position: absolute;
        z-index: 80;
        pointer-events: none;
        animation: float-up-fade 0.85s ease-out forwards;
        font-family: ui-monospace, monospace;
        font-weight: 900;
        text-shadow: 0 2px 8px rgba(0,0,0,0.9), 0 0 12px rgba(245,158,11,0.8);
    }

    /* Peak Explosion Star Badge */
    @keyframes peak-burst {
        0% { transform: scale(0.5); opacity: 0; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    .peak-cleared-glow {
        animation: peak-burst 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        box-shadow: 0 0 20px 4px rgba(251, 191, 36, 0.9) !important;
    }

    /* 3D Button Effects */
    .btn-tripeaks-gold {
        background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 60%, #d97706 100%);
        box-shadow: 0 4px 0 #b45309, 0 8px 18px rgba(245, 158, 11, 0.45);
        border-top: 1px solid rgba(255, 255, 255, 0.5);
        transition: all 0.1s ease;
        touch-action: manipulation;
    }
    .btn-tripeaks-gold:active {
        transform: translateY(3px);
        box-shadow: 0 1px 0 #b45309, 0 4px 8px rgba(245, 158, 11, 0.25);
    }

    .btn-tripeaks-blue {
        background: linear-gradient(180deg, #38bdf8 0%, #0284c7 60%, #0369a1 100%);
        box-shadow: 0 4px 0 #075985, 0 8px 18px rgba(2, 132, 199, 0.45);
        border-top: 1px solid rgba(255, 255, 255, 0.4);
        transition: all 0.1s ease;
        touch-action: manipulation;
    }
    .btn-tripeaks-blue:active {
        transform: translateY(3px);
        box-shadow: 0 1px 0 #075985, 0 4px 8px rgba(2, 132, 199, 0.25);
    }

    /* Timer Danger Heartbeat Animation (<= 15s) */
    @keyframes timer-danger-heartbeat {
        0%, 100% { transform: scale(1); box-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        20% { transform: scale(1.06); box-shadow: 0 0 22px rgba(239, 68, 68, 0.85); border-color: #ef4444; }
        40% { transform: scale(1); box-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        60% { transform: scale(1.04); box-shadow: 0 0 16px rgba(239, 68, 68, 0.7); }
    }
    .timer-danger-pulse {
        animation: timer-danger-heartbeat 1s infinite ease-in-out !important;
    }

    /* Timer Bonus Surge (+2s Pop) */
    @keyframes timer-bonus-surge-anim {
        0% { transform: scale(1); }
        35% { transform: scale(1.15); border-color: #10b981 !important; box-shadow: 0 0 24px 6px rgba(16, 185, 129, 0.75) !important; background-color: rgba(6, 78, 59, 0.5) !important; }
        100% { transform: scale(1); }
    }
    .timer-bonus-surge {
        animation: timer-bonus-surge-anim 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
    }

    /* Floating +2s Time Pill on Header */
    @keyframes float-time-surge {
        0% { transform: translate(-50%, 4px) scale(0.7); opacity: 0; }
        25% { transform: translate(-50%, -10px) scale(1.18); opacity: 1; }
        70% { transform: translate(-50%, -20px) scale(1.05); opacity: 1; }
        100% { transform: translate(-50%, -36px) scale(0.9); opacity: 0; }
    }
    .floating-time-pill {
        position: absolute;
        top: -12px;
        left: 50%;
        z-index: 100;
        pointer-events: none;
        animation: float-time-surge 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        font-family: ui-monospace, monospace;
        font-weight: 900;
        white-space: nowrap;
    }

    /* Score Deduction Shake & Flash (-150 Penalty) */
    @keyframes score-deduct-flash-anim {
        0% { transform: scale(1); }
        15% { transform: scale(0.92) translateX(-4px); border-color: #ef4444 !important; background-color: rgba(239, 68, 68, 0.3) !important; box-shadow: 0 0 20px 4px rgba(239, 68, 68, 0.75) !important; }
        35% { transform: scale(0.94) translateX(4px); }
        55% { transform: scale(0.97) translateX(-2px); }
        80% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    .score-deduct-surge {
        animation: score-deduct-flash-anim 0.55s ease-in-out forwards !important;
    }

    /* Floating -150 Pill on Header Score Box */
    @keyframes float-penalty-pill-surge {
        0% { transform: translateY(0) scale(0.6); opacity: 0; }
        30% { transform: translateY(-10px) scale(1.15); opacity: 1; }
        100% { transform: translateY(-26px) scale(0.9); opacity: 0; }
    }
    .floating-penalty-pill {
        position: absolute;
        top: -6px;
        right: 4px;
        z-index: 60;
        pointer-events: none;
        animation: float-penalty-pill-surge 0.75s ease-out forwards;
        font-family: ui-monospace, monospace;
        font-weight: 900;
        text-shadow: 0 2px 8px rgba(0,0,0,0.9), 0 0 12px rgba(239, 68, 68, 0.9);
    }

    /* Floating Penalty Toast on Felt Board */
    @keyframes penalty-float-surge {
        0% { transform: translate(-50%, 0) scale(0.7); opacity: 0; }
        20% { transform: translate(-50%, -12px) scale(1.15); opacity: 1; }
        70% { transform: translate(-50%, -24px) scale(1.02); opacity: 1; }
        100% { transform: translate(-50%, -42px) scale(0.9); opacity: 0; }
    }
    .floating-penalty-toast {
        position: absolute;
        z-index: 85;
        pointer-events: none;
        animation: penalty-float-surge 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        font-family: ui-monospace, monospace;
        font-weight: 900;
        text-shadow: 0 2px 10px rgba(0,0,0,0.95), 0 0 16px rgba(239, 68, 68, 0.95);
    }
</style>

<div 
    x-data="tripeaksEngine()" 
    x-init="initEngine()"
    id="tripeaks-arena-root"
    class="relative rounded-2xl sm:rounded-3xl bg-slate-950/95 border border-amber-500/30 p-2.5 sm:p-6 shadow-2xl backdrop-blur-xl overflow-hidden"
>
    <!-- Background Ambient Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Canvas for Particle Confetti & Star Bursts -->
    <canvas id="tripeaks-fx-canvas" class="absolute inset-0 w-full h-full pointer-events-none z-50"></canvas>

    <!-- HEADER / STATS BAR -->
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-5 border-b border-slate-800">
        
        <!-- Left: Title & Peaks Progress -->
        <div class="flex items-center justify-between md:justify-start gap-2 sm:gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-600 flex items-center justify-center text-base sm:text-xl shadow-lg border border-amber-300/40 shrink-0">
                    🏔️
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <h2 class="text-sm sm:text-xl font-black text-white font-serif-display uppercase tracking-wide truncate">
                            Tri-Peaks
                        </h2>
                        <!-- 3 Peaks Conquered Indicators -->
                        <div class="flex items-center gap-0.5 text-xs">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black transition-all {{ $peaksCleared[0] ? 'bg-amber-400 text-slate-950 shadow-sm peak-cleared-glow font-black' : 'bg-slate-800/80 text-slate-500' }}" title="Puncak Kiri">
                                ⭐P1
                            </span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black transition-all {{ $peaksCleared[1] ? 'bg-amber-400 text-slate-950 shadow-sm peak-cleared-glow font-black' : 'bg-slate-800/80 text-slate-500' }}" title="Puncak Tengah">
                                ⭐P2
                            </span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black transition-all {{ $peaksCleared[2] ? 'bg-amber-400 text-slate-950 shadow-sm peak-cleared-glow font-black' : 'bg-slate-800/80 text-slate-500' }}" title="Puncak Kanan">
                                ⭐P3
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Quick Actions & Glow Help status -->
            <div class="flex items-center gap-1 shrink-0">
                @if($gameState === 'playing')
                    @if($showGlowHelp)
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-0.5 animate-pulse" title="Bantuan Langkah Awal">
                            <span>💡</span> <span>{{ $movesCount + 1 }}/3</span>
                        </span>
                    @else
                        <span class="hidden xs:inline-flex px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/25 items-center gap-0.5">
                            <span>⚔️</span>
                        </span>
                    @endif
                @endif
                <button 
                    type="button" 
                    @click="toggleSound()"
                    class="p-1.5 sm:p-2 rounded-xl border border-slate-800 bg-slate-900/80 text-xs font-bold text-slate-300 md:hidden"
                    :title="soundEnabled ? 'Matikan Suara FX' : 'Aktifkan Suara FX'"
                >
                    <span x-text="soundEnabled ? '🔊' : '🔇'"></span>
                </button>
                <button 
                    type="button" 
                    @click="showRules = !showRules"
                    class="p-1.5 sm:p-2 rounded-xl border border-slate-800 bg-slate-900/80 text-xs font-bold text-amber-300 md:hidden"
                    title="Panduan Cara Main"
                >
                    ℹ️
                </button>
            </div>
        </div>

        <!-- Right: Scores, Real-time Combo Meter & Controls -->
        <div class="flex items-center justify-between md:justify-end gap-1.5 sm:gap-3 w-full md:w-auto">
            
            <!-- Time Attack Countdown Clock (80s Initial + 2s/Card) - Note: NO overflow-hidden so +2s pill floats freely -->
            <div 
                id="tripeaks-timer-badge"
                class="relative flex-1 md:flex-initial px-2 py-1.5 sm:px-4 sm:py-2 rounded-xl sm:rounded-2xl border flex items-center gap-1.5 sm:gap-2 transition-all duration-300 select-none"
                :class="$wire.gameState === 'playing' ? (timeLeft <= 15 ? 'timer-danger-pulse bg-red-950/50 border-red-500 shadow-[0_0_18px_rgba(239,68,68,0.6)]' : (timeLeft <= 30 ? 'bg-amber-950/30 border-amber-500/50 shadow-[0_0_12px_rgba(245,158,11,0.25)]' : 'bg-slate-900/90 border-slate-800')) : 'bg-slate-900/60 border-slate-800/80 opacity-80'"
            >
                <span class="text-xs sm:text-sm shrink-0" :class="$wire.gameState === 'playing' && timeLeft <= 15 ? 'animate-bounce' : ''">⏱️</span>
                <div class="min-w-[38px] sm:min-w-[54px]">
                    <span class="text-[7px] sm:text-[9px] uppercase tracking-wider block font-bold leading-tight" :class="$wire.gameState === 'playing' ? (timeLeft <= 15 ? 'text-red-400 font-extrabold' : (timeLeft <= 30 ? 'text-amber-400' : 'text-slate-400')) : 'text-slate-500'">Waktu</span>
                    <span id="tripeaks-timer-clock-text" class="text-xs sm:text-base font-black font-mono tabular-nums leading-tight block transition-all" :class="$wire.gameState === 'playing' ? (timeLeft <= 15 ? 'text-red-300 font-extrabold' : (timeLeft <= 30 ? 'text-amber-300' : 'text-emerald-300')) : 'text-slate-400'" x-text="$wire.gameState === 'playing' ? formatTime(timeLeft) : '01:20'">01:20</span>
                </div>

                <!-- Visual Battery Bar at bottom of timer badge with dedicated overflow-hidden container -->
                @if($gameState === 'playing')
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-950/60 overflow-hidden pointer-events-none rounded-b-xl">
                        <div 
                            class="h-full transition-all duration-1000 ease-linear"
                            :class="timeLeft <= 15 ? 'bg-red-500 shadow-[0_0_8px_#ef4444]' : (timeLeft <= 30 ? 'bg-amber-400 shadow-[0_0_8px_#f59e0b]' : 'bg-emerald-400 shadow-[0_0_8px_#10b981]')"
                            :style="`width: ${Math.min(100, (timeLeft / 80) * 100)}%`"
                        ></div>
                    </div>
                @endif
            </div>

            <!-- Score Meter with Shake/Flash on penalty -->
            <div 
                id="tripeaks-score-box"
                class="relative flex-1 md:flex-initial bg-slate-900/90 border border-slate-800 px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-xl sm:rounded-2xl flex items-center gap-1.5 sm:gap-2 transition-all duration-200 select-none"
            >
                <span class="text-amber-400 text-xs sm:text-sm shrink-0">💰</span>
                <div class="min-w-[42px] sm:min-w-[54px]">
                    <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-slate-400 block font-bold leading-tight">Skor</span>
                    <span class="text-xs sm:text-base font-black font-mono tabular-nums text-white leading-tight block">{{ number_format($score) }}</span>
                </div>
            </div>

            <!-- Combo Meter with Fire Glow -->
            <div 
                id="tripeaks-combo-box"
                class="relative flex-1 md:flex-initial bg-slate-900/90 border {{ $combo > 1 ? 'border-amber-400 bg-amber-950/30 shadow-[0_0_15px_rgba(251,191,36,0.3)]' : 'border-slate-800' }} px-2.5 py-1.5 sm:px-4 sm:py-2 rounded-xl sm:rounded-2xl flex items-center gap-1.5 sm:gap-2 transition-all duration-200 select-none"
            >
                <span class="text-amber-400 text-xs sm:text-sm shrink-0 {{ $combo > 1 ? 'animate-bounce' : '' }}">🔥</span>
                <div class="min-w-[34px] sm:min-w-[42px]">
                    <span class="text-[8px] sm:text-[9px] uppercase tracking-wider text-slate-400 block font-bold leading-tight">Combo</span>
                    <span class="text-xs sm:text-base font-black font-mono tabular-nums leading-tight block {{ $combo > 1 ? 'text-amber-400' : 'text-slate-300' }}">
                        x{{ $combo }}
                    </span>
                </div>
            </div>

            <!-- Desktop Sound FX Toggle -->
            <button 
                type="button" 
                @click="toggleSound()"
                class="hidden md:flex px-2.5 py-2 rounded-xl border border-slate-800 bg-slate-900/80 hover:bg-slate-800 text-xs font-bold text-slate-300 transition items-center gap-1 cursor-pointer"
                :title="soundEnabled ? 'Matikan Suara FX' : 'Aktifkan Suara FX'"
            >
                <span x-text="soundEnabled ? '🔊' : '🔇'"></span>
                <span class="text-[11px]" x-text="soundEnabled ? 'FX On' : 'Mute'"></span>
            </button>

            <!-- Desktop Rules Trigger -->
            <button 
                type="button" 
                @click="showRules = !showRules"
                class="hidden md:flex px-3 py-2 rounded-xl border border-slate-800 bg-slate-900/80 hover:bg-slate-800 text-xs font-bold text-amber-300 transition items-center gap-1 cursor-pointer"
            >
                <span>ℹ️</span>
                <span class="text-[11px]">Panduan</span>
            </button>

            <!-- Start / Restart Button -->
            @if($gameState === 'playing')
                <button 
                    type="button" 
                    wire:click="startGame"
                    class="btn-tripeaks-blue px-3 sm:px-4 py-2 rounded-xl text-white font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 cursor-pointer shrink-0"
                >
                    <span>🔄</span>
                    <span class="hidden sm:inline">Acak Ulang</span>
                </button>
            @else
                <button 
                    type="button" 
                    wire:click="startGame"
                    class="btn-tripeaks-gold px-4 sm:px-5 py-2 rounded-xl text-slate-950 font-black text-xs uppercase tracking-wider flex items-center gap-1.5 cursor-pointer shadow-lg animate-pulse shrink-0"
                >
                    <span>▶</span>
                    <span>Mulai</span>
                </button>
            @endif
        </div>
    </div>

    <!-- HOW TO PLAY ACCORDION / DRAWER -->
    <div 
        x-show="showRules" 
        x-transition 
        class="mt-3 p-3.5 sm:p-4 rounded-2xl bg-slate-900/95 border border-amber-500/40 text-slate-300 text-xs sm:text-sm space-y-2 relative z-20"
    >
        <button @click="showRules = false" class="absolute top-3 right-3 text-slate-400 hover:text-white text-base cursor-pointer">✕</button>
        <h4 class="font-black text-amber-400 text-sm flex items-center gap-1.5">
            <span>📜</span> ATURAN MAIN TRI-PEAKS SOLITAIRE
        </h4>
        <ul class="list-disc list-inside space-y-1 text-slate-300 text-[11px] sm:text-xs leading-relaxed">
            <li><strong>Tujuan Utama:</strong> Bersihkan seluruh 28 kartu di 3 puncak piramida sebelum waktu habis!</li>
            <li><strong>Aturan Selisih ±1:</strong> Ambil kartu terbuka di piramida yang bernilai 1 angka di atas atau di bawah kartu umpan (Contoh: jika umpan <strong>7</strong>, ambil <strong>6</strong> atau <strong>8</strong>).</li>
            <li><strong>King &amp; As Terhubung:</strong> Kartu <strong>King (K)</strong> terhubung berputar dengan <strong>As (A)</strong>!</li>
            <li><strong>Time Attack (80s):</strong> Waktu awal 80 detik. Setiap berhasil mengambil kartu piramida, kamu mendapat tambahan <strong>+2 detik</strong>!</li>
            <li><strong>Tumpukan Cadangan (14 Kartu):</strong> Cadangan dibatasi hanya 14 kartu. Menarik kartu cadangan dikenakan <strong>Penalti -150 Poin</strong> & me-reset streak combo ke 0!</li>
            <li><strong>Bonus Kemenangan Spektakuler:</strong> Taklukkan semua puncak untuk meraih <strong>+5,000 Poin</strong> + bonus sisa cadangan (<strong>+250/kartu</strong>) + bonus sisa waktu (<strong>+100/detik</strong>)!</li>
        </ul>
    </div>

    <!-- MAIN GAME LAYOUT: TABLE FELT & LEADERBOARD (12 COLS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-5 mt-3 sm:mt-5 items-start">
        
        <!-- 1. CASINO VELVET TABLEAU (8 COLS) -->
        <div class="lg:col-span-8 flex flex-col space-y-3 sm:space-y-4">
            
            <!-- Table Felt Mat Area -->
            <div 
                id="tripeaks-felt-board"
                class="tripeaks-felt-mat rounded-xl sm:rounded-3xl p-2 sm:p-5 relative min-h-[350px] sm:min-h-[420px] md:min-h-[460px] flex flex-col justify-between overflow-hidden"
            >
                
                @if($gameState === 'idle')
                    <!-- Idle Welcome Screen -->
                    <div class="my-auto py-10 sm:py-16 flex flex-col items-center justify-center text-center space-y-4 z-20">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-3xl sm:text-4xl shadow-xl animate-pulse">
                            🏔️
                        </div>
                        <div class="max-w-md space-y-1.5 px-4">
                            <h3 class="text-xl sm:text-3xl font-black text-white font-serif-display">
                                Siap Menaklukkan Tri-Peaks?
                            </h3>
                            <p class="text-xs sm:text-sm text-emerald-200/80 leading-relaxed">
                                Bersihkan ketiga puncak piramida kartu dengan mencocokkan selisih ±1 angka. Buktikan keahlianmu dan raih rekor combo tertinggi di meja Royale!
                            </p>
                        </div>
                        <button 
                            type="button" 
                            wire:click="startGame"
                            class="btn-tripeaks-gold px-7 sm:px-9 py-3 sm:py-3.5 rounded-2xl text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider flex items-center gap-2 cursor-pointer shadow-xl hover:scale-105 transition"
                        >
                            <span>▶ MULAI GAME SEKARANG</span>
                        </button>
                    </div>
                @else
                    <!-- Active 28 Cards Pyramid Tableau -->
                    <div class="relative w-full h-[200px] sm:h-[260px] md:h-[300px] mx-auto select-none">
                        @foreach($tableau as $index => $node)
                            @php
                                $coord = $cardCoordinates[$index] ?? ['x' => 0, 'row' => 0];
                                $leftPercent = $coord['x'] * 10.0888;
                                $topPercent = match($coord['row']) {
                                    0 => 2,
                                    1 => 20,
                                    2 => 38,
                                    3 => 56,
                                    default => 0,
                                };
                                $zIndex = ($coord['row'] + 1) * 10;
                                $isPlayable = in_array($index, $playableIndices);
                                $isGlowing = $isPlayable && $showGlowHelp;
                            @endphp

                            <div 
                                id="tripeaks-card-node-{{ $index }}"
                                class="tripeaks-card {{ $node['is_removed'] ? 'opacity-0 pointer-events-none' : '' }}"
                                style="left: {{ $leftPercent }}%; top: {{ $topPercent }}%; z-index: {{ $zIndex }};"
                            >
                                @if(!$node['is_removed'])
                                    @if($node['is_face_up'])
                                        <!-- Face-Up Card with enhanced mobile readability -->
                                        <div 
                                            @if($isPlayable)
                                                @click="$wire.pickCard({{ $index }}, timeLeft)"
                                            @else
                                                @click="triggerInvalidShake({{ $index }})"
                                            @endif
                                            class="tripeaks-card-face {{ $isGlowing ? 'tripeaks-card-playable' : ($isPlayable ? 'cursor-pointer hover:border-amber-300 hover:scale-105 active:scale-95 transition-all' : 'cursor-pointer active:scale-95') }}"
                                            title="{{ $isPlayable ? 'Klik untuk ambil (+Poin Combo & +2s)' : $node['card']['title'] }}"
                                        >
                                            <!-- Top Bold Rank & Suit (Crisp & High-Contrast on Mobile) -->
                                            <div class="flex flex-col items-center leading-none {{ $node['card']['color'] === 'red' ? 'text-red-600' : 'text-slate-950' }}">
                                                <span class="text-xs sm:text-sm md:text-base font-black font-mono leading-tight tracking-tighter">{{ $node['card']['rank_label'] }}</span>
                                                <span class="text-[11px] sm:text-xs md:text-sm leading-none mt-0.5">{!! $node['card']['symbol_html'] !!}</span>
                                            </div>

                                            <!-- Center Large Suit Watermark (Desktop only) -->
                                            <div class="hidden sm:flex absolute inset-0 items-center justify-center pointer-events-none opacity-20 {{ $node['card']['color'] === 'red' ? 'text-red-500' : 'text-slate-800' }}">
                                                <span class="text-xl md:text-2xl font-bold leading-none">{!! $node['card']['symbol_html'] !!}</span>
                                            </div>

                                            <!-- Bottom Inverted Rank & Suit (Desktop only) -->
                                            <div class="hidden sm:flex flex-col items-center leading-none rotate-180 {{ $node['card']['color'] === 'red' ? 'text-red-600' : 'text-slate-950' }}">
                                                <span class="text-[10px] sm:text-xs md:text-sm font-black font-mono leading-tight">{{ $node['card']['rank_label'] }}</span>
                                                <span class="text-[9px] sm:text-[11px] md:text-xs leading-none">{!! $node['card']['symbol_html'] !!}</span>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Face-Down Card (Covered Shingle) -->
                                        <div 
                                            @click="triggerCoveredShake({{ $index }})"
                                            class="tripeaks-card-back cursor-pointer active:scale-95"
                                            title="Masih tertindih! Bersihkan kartu di bawahnya terlebih dahulu"
                                        ></div>
                                    @endif
                                @else
                                    <!-- Removed Card Empty Slot Placeholder -->
                                    <div class="w-full h-full rounded-md border border-emerald-500/10 opacity-15"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- BOTTOM DOCK: UNIFIED HIGH-ROLLER CASINO CONTROL DOCK -->
                    <div class="mt-2 pt-2 border-t border-emerald-600/30 flex items-center justify-between gap-1.5 sm:gap-4 z-20">
                        
                        <!-- Left: Stock Pile (Cadangan) -->
                        <div class="flex items-center gap-1.5 sm:gap-3">
                            <div 
                                @if($stockCount > 0 && $gameState === 'playing')
                                    wire:click="drawFromStock"
                                    class="cursor-pointer hover:scale-105 active:scale-95 transition"
                                @endif
                                class="relative w-10 h-14 sm:w-14 sm:h-20 rounded-lg sm:rounded-xl select-none shrink-0"
                                title="{{ $stockCount > 0 ? 'Klik untuk membuka kartu cadangan (Penalti -150 Skor & Reset Combo)' : 'Tumpukan cadangan kosong' }}"
                            >
                                @if($stockCount > 0)
                                    <!-- 3D Card Stack Layer -->
                                    <div class="absolute inset-0 bg-blue-950 rounded-lg sm:rounded-xl border border-blue-600 translate-x-0.5 translate-y-0.5 opacity-70"></div>
                                    <div class="tripeaks-card-back w-full h-full flex flex-col items-center justify-center shadow-md">
                                        <span class="text-white text-xs sm:text-sm font-black font-mono z-10">{{ $stockCount }}</span>
                                        <span class="text-[6px] sm:text-[8px] text-sky-300 font-bold uppercase tracking-wider z-10">Sisa</span>
                                    </div>
                                @else
                                    <div class="w-full h-full rounded-lg sm:rounded-xl border border-dashed border-emerald-500/40 flex items-center justify-center text-center p-0.5 text-[9px] text-emerald-300 font-bold">
                                        Habis
                                    </div>
                                @endif
                            </div>

                            <div>
                                <span class="text-[8px] sm:text-[10px] uppercase font-bold text-emerald-300/80 block leading-tight">Cadangan</span>
                                @if($stockCount > 0 && $gameState === 'playing')
                                    <button 
                                        type="button" 
                                        wire:click="drawFromStock"
                                        class="mt-0.5 px-2 py-0.5 sm:px-3 sm:py-1 rounded-lg bg-emerald-800/90 hover:bg-emerald-700 active:scale-95 text-emerald-100 border border-emerald-500/40 text-[10px] sm:text-xs font-bold transition cursor-pointer flex items-center gap-1 shadow-sm whitespace-nowrap"
                                        title="Tarik kartu cadangan (Penalti -150 Skor & Reset Combo)"
                                    >
                                        <span>🃏 Tarik</span>
                                        <span class="text-[8px] sm:text-[9px] text-amber-300 font-mono font-bold bg-black/40 px-1 rounded">(-150)</span>
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400 font-mono">0</span>
                                @endif
                            </div>
                        </div>

                        <!-- Center: Target Match Indicator (Unified Mobile & Desktop) -->
                        @if($currentWaste && $gameState === 'playing')
                            <div class="bg-slate-950/90 border border-amber-400/50 px-2 sm:px-4 py-1 sm:py-1.5 rounded-xl sm:rounded-2xl flex flex-col sm:flex-row items-center gap-0.5 sm:gap-2 shadow-md">
                                <span class="text-[8px] sm:text-[10px] uppercase tracking-wider text-amber-300 font-bold flex items-center gap-1 leading-none">
                                    <span>🎯</span> <span class="hidden xs:inline">Cari</span> ±1:
                                </span>
                                <div class="flex items-center gap-1 font-mono font-black text-xs sm:text-sm">
                                    <span class="px-1.5 sm:px-2 py-0.5 rounded-md sm:rounded-lg bg-amber-400 text-slate-950 shadow-sm">{{ $targetRanks[0] ?? '-' }}</span>
                                    <span class="text-slate-400 font-normal text-[10px]">atau</span>
                                    <span class="px-1.5 sm:px-2 py-0.5 rounded-md sm:rounded-lg bg-amber-400 text-slate-950 shadow-sm">{{ $targetRanks[1] ?? '-' }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Right: Active Waste Pile (Kartu Umpan) -->
                        <div class="flex items-center gap-1.5 sm:gap-3">
                            <div class="text-right hidden xs:block">
                                <span class="text-[8px] sm:text-[10px] uppercase font-bold text-amber-300/80 block leading-tight">Umpan</span>
                                <span class="text-[10px] sm:text-xs text-slate-200 font-bold truncate max-w-[70px] sm:max-w-none block">{{ $currentWaste['title'] ?? '-' }}</span>
                            </div>

                            <div id="tripeaks-waste-target" class="relative w-10 h-14 sm:w-14 sm:h-20 rounded-lg sm:rounded-xl select-none shrink-0">
                                @if($currentWaste)
                                    <div class="tripeaks-card-face w-full h-full shadow-lg border-2 border-amber-400/80">
                                        <!-- Top Rank & Suit -->
                                        <div class="flex flex-col items-center leading-none {{ $currentWaste['color'] === 'red' ? 'text-red-600' : 'text-slate-950' }}">
                                            <span class="text-xs sm:text-sm font-black font-mono leading-tight">{{ $currentWaste['rank_label'] }}</span>
                                            <span class="text-[11px] sm:text-xs leading-none mt-0.5">{!! $currentWaste['symbol_html'] !!}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full h-full rounded-lg sm:rounded-xl border border-slate-700 bg-slate-900/50"></div>
                                @endif
                            </div>
                        </div>

                    </div>
                @endif

            </div>

            <!-- Action Toast / Feedback Bar -->
            @if($lastActionMessage)
                <div class="bg-slate-900/90 border border-slate-800 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl flex items-center justify-between text-xs sm:text-sm">
                    <span class="text-slate-200 font-medium truncate mr-2">{!! $lastActionMessage !!}</span>
                    @if($lastPointsEarned > 0)
                        <span class="font-mono font-black text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-lg border border-amber-500/20 shrink-0">
                            +{{ number_format($lastPointsEarned) }} Poin
                        </span>
                    @endif
                </div>
            @endif

        </div>

        <!-- 2. LEADERBOARD PANEL (4 COLS) -->
        <div class="lg:col-span-4 flex flex-col space-y-3 sm:space-y-4">
            
            <!-- User Status Card -->
            @if(Auth::check() && $userStats)
                <div class="bg-slate-900/90 rounded-2xl p-3.5 sm:p-4 border border-amber-500/30 shadow-lg relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-300 flex items-center justify-center font-bold text-slate-950 text-sm sm:text-base shadow shrink-0">
                                🎖️
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                                <span class="text-[9px] sm:text-[10px] text-emerald-400 font-mono font-semibold">Statistik Tri-Peaks</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-800 text-center font-mono">
                        <div class="bg-slate-950/70 p-1.5 sm:p-2 rounded-xl border border-slate-800">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">High Score</span>
                            <span class="text-xs sm:text-sm font-black text-emerald-400">{{ number_format($userStats['high_score']) }}</span>
                        </div>
                        <div class="bg-slate-950/70 p-1.5 sm:p-2 rounded-xl border border-slate-800">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">Max Combo</span>
                            <span class="text-xs sm:text-sm font-black text-amber-400">{{ $userStats['max_combo'] }}x</span>
                        </div>
                        <div class="bg-slate-950/70 p-1.5 sm:p-2 rounded-xl border border-slate-800">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">Menang</span>
                            <span class="text-xs sm:text-sm font-black text-sky-400">{{ $userStats['total_victories'] }}x</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Top 10 Tri-Peaks Leaderboard -->
            <div class="bg-slate-900/90 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-800 shadow-xl flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="text-lg sm:text-xl">🏆</span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-white font-serif-display uppercase tracking-wide">
                                Jawara Tri-Peaks
                            </h3>
                            <p class="text-[9px] sm:text-[10px] text-slate-400">Peringkat penakluk piramida kartu</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="loadLeaderboardData" 
                        class="text-xs text-amber-400 hover:text-amber-300 p-1 rounded-lg hover:bg-slate-800 transition cursor-pointer"
                        title="Segarkan Leaderboard"
                    >
                        🔄
                    </button>
                </div>

                @if(empty($leaderboard))
                    <div class="py-8 text-center text-slate-500 text-xs">
                        Belum ada rekor tersimpan. Jadilah penakluk pertama!
                    </div>
                @else
                    <div class="space-y-1.5 overflow-y-auto max-h-60 sm:max-h-80 pr-1">
                        @foreach($leaderboard as $player)
                            <div class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $player['is_current_user'] ? 'bg-amber-500/15 border border-amber-500/40 text-amber-200' : 'bg-slate-950/60 hover:bg-slate-950 border border-slate-800/80 text-slate-300' }}">
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
                                            <span>Combo: <strong class="text-amber-400 font-mono">{{ $player['max_combo'] }}x</strong></span>
                                            <span>•</span>
                                            <span>Menang: <strong class="text-sky-400 font-mono">{{ $player['total_victories'] }}x</strong></span>
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

    <!-- MODAL: VICTORY CELEBRATION -->
    @if($gameState === 'victory')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md">
            <div class="bg-gradient-to-b from-slate-900 to-slate-950 border-2 border-amber-400 rounded-3xl p-6 sm:p-8 max-w-md w-full text-center space-y-4 shadow-2xl relative overflow-hidden animate-bounce-short">
                <div class="text-5xl animate-bounce">👑</div>
                <div class="space-y-1">
                    <h3 class="text-2xl font-black text-amber-300 font-serif-display uppercase">
                        TRI-PEAKS DITAKLUKKAN!
                    </h3>
                    <p class="text-xs text-slate-300">
                        Luar biasa! Kamu berhasil membersihkan ketiga puncak piramida dengan sempurna.
                    </p>
                </div>

                <div class="bg-slate-950/80 rounded-2xl p-4 border border-amber-500/30 text-center font-mono space-y-3">
                    <div>
                        <span class="text-xs uppercase text-slate-400 font-sans block">Total Skor Akhir</span>
                        <span class="text-3xl font-black text-amber-400">{{ number_format($score) }}</span>
                    </div>

                    <!-- Breakdown Bonus Kasino -->
                    <div class="grid grid-cols-3 gap-1.5 text-[10px] text-slate-300 pt-2 border-t border-slate-800 font-sans">
                        <div class="bg-slate-900/90 p-2 rounded-xl border border-slate-800">
                            <span class="text-slate-400 block text-[9px] uppercase">Piramida</span>
                            <span class="font-bold text-amber-300 font-mono">+5,000</span>
                        </div>
                        <div class="bg-slate-900/90 p-2 rounded-xl border border-slate-800">
                            <span class="text-slate-400 block text-[9px] uppercase">Cadangan ({{ $stockCount }})</span>
                            <span class="font-bold text-emerald-400 font-mono">+{{ number_format($stockCount * 250) }}</span>
                        </div>
                        <div class="bg-slate-900/90 p-2 rounded-xl border border-slate-800">
                            <span class="text-slate-400 block text-[9px] uppercase">Waktu ({{ $finalRemainingSeconds }}s)</span>
                            <span class="font-bold text-sky-400 font-mono">+{{ number_format($timeBonusEarned) }}</span>
                        </div>
                    </div>

                    @if($isNewHighScore)
                        <span class="block mt-1 text-xs text-emerald-400 font-bold uppercase tracking-wider animate-pulse">
                            🔥 Rekor Skor Baru Kamu!
                        </span>
                    @endif
                </div>

                <button 
                    type="button" 
                    wire:click="startGame"
                    class="btn-tripeaks-gold w-full py-3.5 rounded-2xl text-slate-950 font-black text-sm uppercase tracking-wider cursor-pointer shadow-lg hover:scale-105 transition"
                >
                    🔄 MAIN LAGI SEKARANG
                </button>
            </div>
        </div>
    @endif

    <!-- MODAL: GAME OVER -->
    @if($gameState === 'game_over')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md">
            <div class="bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-700 rounded-3xl p-6 sm:p-8 max-w-md w-full text-center space-y-4 shadow-2xl relative">
                <div class="text-4xl">🏁</div>
                <div class="space-y-1">
                    <h3 class="text-xl font-black text-white font-serif-display uppercase">
                        PERMAINAN SELESAI
                    </h3>
                    <p class="text-xs text-amber-300 font-medium">
                        {{ $lastActionMessage }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-2 font-mono">
                    <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800">
                        <span class="text-[10px] uppercase text-slate-400 font-sans block">Skor</span>
                        <span class="text-base font-black text-amber-400">{{ number_format($score) }}</span>
                    </div>
                    <div class="bg-slate-950/70 p-3 rounded-xl border border-slate-800">
                        <span class="text-[10px] uppercase text-slate-400 font-sans block">Max Combo</span>
                        <span class="text-base font-black text-emerald-400">{{ $maxComboThisSession }}x</span>
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <button 
                        type="button" 
                        wire:click="startGame"
                        class="btn-tripeaks-gold flex-1 py-3 rounded-2xl text-slate-950 font-black text-xs uppercase tracking-wider cursor-pointer shadow-md hover:scale-105 transition"
                    >
                        🔄 COBA LAGI
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- HIGH PERFORMANCE CANVAS PARTICLE & AUDIO SYNTHESIZER ENGINE -->
<script>
    function tripeaksEngine() {
        return {
            showRules: false,
            soundEnabled: localStorage.getItem('tripeaks_sound_enabled') !== 'false',
            audioCtx: null,
            particles: [],
            animFrameId: null,
            timeLeft: 80,
            timerInterval: null,

            initEngine() {
                this.setupCanvas();
                this.initListeners();
                @if($gameState === 'playing')
                    this.startTimer();
                @endif
            },

            formatTime(seconds) {
                const s = Math.max(0, parseInt(seconds) || 0);
                const mins = Math.floor(s / 60);
                const secs = s % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            },

            startTimer() {
                this.stopTimer();
                this.timeLeft = 80;
                this.timerInterval = setInterval(() => {
                    if (this.timeLeft > 0) {
                        this.timeLeft--;
                        if (this.timeLeft <= 10 && this.timeLeft > 0) {
                            this.playEmergencyTick();
                        }
                        if (this.timeLeft === 0) {
                            this.stopTimer();
                            if (window.Livewire) {
                                @this.timeUp();
                            }
                        }
                    }
                }, 1000);
            },

            stopTimer() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
            },

            setupCanvas() {
                const canvas = document.getElementById('tripeaks-fx-canvas');
                if (!canvas) return;
                const resize = () => {
                    const rect = canvas.parentElement.getBoundingClientRect();
                    canvas.width = rect.width;
                    canvas.height = rect.height;
                };
                resize();
                window.addEventListener('resize', resize);
                this.loopParticles();
            },

            initListeners() {
                // Game Started: reset & start 80s countdown timer
                window.addEventListener('tripeaks-started', () => {
                    this.startTimer();
                });

                // Card Picked: Floating toast + sound + haptic + +2s time bonus
                window.addEventListener('tripeaks-card-picked', (e) => {
                    const idx = e.detail.index;
                    const combo = e.detail.combo || 1;
                    const newlyUncovered = e.detail.newlyUncovered || [];

                    // Add +2s time attack bonus per card cleared (max 99s)
                    this.timeLeft = Math.min(this.timeLeft + 2, 99);
                    this.pulseTimerBonus();

                    this.playPickSound(combo);
                    this.triggerHaptic(15);
                    this.spawnFloatingScore(idx, combo);
                    this.spawnBurstParticlesAtNode(idx, combo);

                    // Animate newly uncovered cards with 3D Flip
                    newlyUncovered.forEach(cIdx => {
                        const node = document.getElementById(`tripeaks-card-node-${cIdx}`);
                        if (node) {
                            node.classList.add('card-flip-reveal');
                            setTimeout(() => node.classList.remove('card-flip-reveal'), 600);
                        }
                    });

                    // Pop combo badge in header
                    const comboBox = document.getElementById('tripeaks-combo-box');
                    if (comboBox && combo > 1) {
                        comboBox.style.transform = 'scale(1.15)';
                        setTimeout(() => comboBox.style.transform = 'scale(1)', 200);
                    }
                });

                // Invalid card tap shake
                window.addEventListener('tripeaks-invalid-pick', (e) => {
                    this.triggerInvalidShake(e.detail.index);
                });

                // Stock draw: penalty feedback
                window.addEventListener('tripeaks-stock-drawn', (e) => {
                    this.playDrawSound();
                    this.triggerHaptic([40, 60, 40]);
                    const penalty = (e.detail && e.detail.penalty) ? e.detail.penalty : 150;
                    this.pulseScoreDeduction(penalty);
                    this.spawnFloatingDrawPenalty(penalty);
                });

                // Peak Cleared celebration
                window.addEventListener('tripeaks-peak-cleared', (e) => {
                    this.playPeakChord();
                    this.triggerHaptic([30, 60, 30]);
                    this.launchPeakFireworks(e.detail.peak);
                });

                // Victory celebration
                window.addEventListener('tripeaks-victory', () => {
                    this.stopTimer();
                    this.playVictoryFanfare();
                    this.triggerHaptic([50, 100, 50, 100, 150]);
                    this.launchVictoryConfetti();
                });

                // Game Over sound
                window.addEventListener('tripeaks-game-over', () => {
                    this.stopTimer();
                    this.playGameOverSound();
                });
            },

            triggerHaptic(pattern) {
                if (navigator.vibrate) {
                    try { navigator.vibrate(pattern); } catch (err) {}
                }
            },

            triggerCoveredShake(index) {
                const node = document.getElementById(`tripeaks-card-node-${index}`);
                const board = document.getElementById('tripeaks-felt-board');
                if (node) {
                    node.classList.add('card-shake');
                    setTimeout(() => node.classList.remove('card-shake'), 400);
                }
                if (node && board) {
                    const nodeRect = node.getBoundingClientRect();
                    const boardRect = board.getBoundingClientRect();
                    const toast = document.createElement('div');
                    toast.className = 'floating-toast text-sky-300 text-[10px] sm:text-xs font-black pointer-events-none px-2 py-0.5 rounded-full bg-slate-950/90 border border-sky-400/50 shadow-md whitespace-nowrap';
                    toast.innerHTML = `🔒 Tertindih kartu di bawahnya!`;
                    const left = nodeRect.left - boardRect.left + (nodeRect.width / 2);
                    const top = nodeRect.top - boardRect.top - 10;
                    toast.style.left = `${left}px`;
                    toast.style.top = `${top}px`;
                    board.appendChild(toast);
                    setTimeout(() => toast.remove(), 1000);
                }
                this.playThudSound();
                this.triggerHaptic(20);
            },

            triggerInvalidShake(index) {
                const node = document.getElementById(`tripeaks-card-node-${index}`);
                const board = document.getElementById('tripeaks-felt-board');
                if (node) {
                    node.classList.add('card-shake');
                    setTimeout(() => node.classList.remove('card-shake'), 400);
                }
                if (node && board) {
                    const nodeRect = node.getBoundingClientRect();
                    const boardRect = board.getBoundingClientRect();
                    const toast = document.createElement('div');
                    toast.className = 'floating-toast text-red-300 text-[10px] sm:text-xs font-black pointer-events-none px-2.5 py-0.5 rounded-full bg-slate-950/90 border border-red-500/50 shadow-md whitespace-nowrap';
                    toast.innerHTML = `❌ Bukan ±1 dari Umpan!`;
                    const left = nodeRect.left - boardRect.left + (nodeRect.width / 2);
                    const top = nodeRect.top - boardRect.top - 10;
                    toast.style.left = `${left}px`;
                    toast.style.top = `${top}px`;
                    board.appendChild(toast);
                    setTimeout(() => toast.remove(), 1000);
                }

                // Guide eyes to waste pile card at bottom
                const waste = document.getElementById('tripeaks-waste-target');
                if (waste) {
                    waste.style.transform = 'scale(1.12)';
                    waste.style.boxShadow = '0 0 20px 4px rgba(251, 191, 36, 0.9)';
                    setTimeout(() => {
                        waste.style.transform = 'scale(1)';
                        waste.style.boxShadow = '';
                    }, 400);
                }

                this.playThudSound();
                this.triggerHaptic(35);
            },

            spawnFloatingScore(index, combo) {
                const node = document.getElementById(`tripeaks-card-node-${index}`);
                const board = document.getElementById('tripeaks-felt-board');
                if (!node || !board) return;

                const nodeRect = node.getBoundingClientRect();
                const boardRect = board.getBoundingClientRect();

                const toast = document.createElement('div');
                toast.className = 'floating-toast text-amber-300 text-xs sm:text-sm font-black pointer-events-none flex items-center gap-1.5 z-50';
                toast.innerHTML = combo > 1 
                    ? `<span>+${combo * 100}</span> <span class="text-amber-400 font-extrabold text-xs">🔥x${combo}</span> <span class="text-xs font-mono font-black text-slate-950 bg-emerald-400 px-2 py-0.5 rounded-full shadow-[0_0_12px_#10b981] border border-white flex items-center gap-0.5 leading-none ml-1">+2s ⏱️</span>` 
                    : `<span>+100</span> <span class="text-xs font-mono font-black text-slate-950 bg-emerald-400 px-2 py-0.5 rounded-full shadow-[0_0_12px_#10b981] border border-white flex items-center gap-0.5 leading-none ml-1">+2s ⏱️</span>`;

                const left = nodeRect.left - boardRect.left + (nodeRect.width / 2);
                const top = nodeRect.top - boardRect.top;

                toast.style.left = `${left}px`;
                toast.style.top = `${top}px`;

                board.appendChild(toast);
                setTimeout(() => toast.remove(), 900);
            },

            pulseTimerBonus() {
                const timerBadge = document.getElementById('tripeaks-timer-badge');
                const clockText = document.getElementById('tripeaks-timer-clock-text');
                if (timerBadge) {
                    timerBadge.classList.remove('timer-bonus-surge');
                    void timerBadge.offsetWidth;
                    timerBadge.classList.add('timer-bonus-surge');
                    setTimeout(() => timerBadge.classList.remove('timer-bonus-surge'), 500);

                    // Flash clock text in bright emerald
                    if (clockText) {
                        clockText.style.color = '#34d399';
                        clockText.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            clockText.style.color = '';
                            clockText.style.transform = '';
                        }, 400);
                    }

                    // Spawn prominent +2s floating pill above timer badge (z-index 100, unclipped)
                    const pill = document.createElement('div');
                    pill.className = 'floating-time-pill text-xs text-slate-950 bg-emerald-400 border border-white font-black px-2.5 py-0.5 rounded-full shadow-[0_0_15px_#10b981] flex items-center gap-1';
                    pill.innerHTML = `+2s ⏱️`;
                    timerBadge.appendChild(pill);
                    setTimeout(() => pill.remove(), 800);
                }
                this.playTimeBonusSound();
            },

            pulseScoreDeduction(penalty) {
                const scoreBox = document.getElementById('tripeaks-score-box');
                if (scoreBox) {
                    scoreBox.classList.remove('score-deduct-surge');
                    void scoreBox.offsetWidth;
                    scoreBox.classList.add('score-deduct-surge');
                    setTimeout(() => scoreBox.classList.remove('score-deduct-surge'), 550);

                    // Spawn mini -150 floating pill right above score box
                    const pill = document.createElement('div');
                    pill.className = 'floating-penalty-pill text-[10px] sm:text-xs text-red-400 bg-red-950/95 border border-red-500/80 px-2 py-0.5 rounded-full shadow-lg';
                    pill.innerHTML = `-${penalty}`;
                    scoreBox.appendChild(pill);
                    setTimeout(() => pill.remove(), 700);
                }
                this.playPenaltySound();
            },

            spawnFloatingDrawPenalty(penalty) {
                const board = document.getElementById('tripeaks-felt-board');
                if (!board) return;

                const toast = document.createElement('div');
                toast.className = 'floating-penalty-toast text-red-400 font-mono text-xs sm:text-sm font-black pointer-events-none px-3.5 py-1.5 rounded-2xl bg-slate-950/95 border-2 border-red-500/80 shadow-[0_0_20px_rgba(239,68,68,0.6)] whitespace-nowrap flex items-center gap-1.5';
                toast.innerHTML = `<span>💸</span> <span>-${penalty} Poin</span> <span class="text-[9px] sm:text-[10px] text-red-300 font-sans font-bold bg-red-950/90 px-1.5 py-0.5 rounded border border-red-500/40">Streak Reset!</span>`;
                toast.style.left = '50%';
                toast.style.bottom = '85px';

                board.appendChild(toast);
                setTimeout(() => toast.remove(), 1000);
            },

            spawnBurstParticlesAtNode(index, combo) {
                const node = document.getElementById(`tripeaks-card-node-${index}`);
                const canvas = document.getElementById('tripeaks-fx-canvas');
                if (!node || !canvas) return;

                const nodeRect = node.getBoundingClientRect();
                const canvasRect = canvas.getBoundingClientRect();
                const x = nodeRect.left - canvasRect.left + (nodeRect.width / 2);
                const y = nodeRect.top - canvasRect.top + (nodeRect.height / 2);

                const count = Math.min(10 + combo * 3, 30);
                const colors = ['#f59e0b', '#fbbf24', '#fef08a', '#10b981', '#ffffff'];

                for (let i = 0; i < count; i++) {
                    const angle = Math.random() * Math.PI * 2;
                    const speed = 2 + Math.random() * 4;
                    this.particles.push({
                        x, y,
                        vx: Math.cos(angle) * speed,
                        vy: Math.sin(angle) * speed - 1,
                        size: 3 + Math.random() * 3,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        alpha: 1,
                        decay: 0.02 + Math.random() * 0.03,
                        gravity: 0.1
                    });
                }
            },

            launchPeakFireworks(peakIndex) {
                const canvas = document.getElementById('tripeaks-fx-canvas');
                if (!canvas) return;

                // Peak 0 = left (25%), Peak 1 = center (50%), Peak 2 = right (75%)
                const posX = peakIndex === 0 ? canvas.width * 0.25 : (peakIndex === 1 ? canvas.width * 0.5 : canvas.width * 0.75);
                const posY = canvas.height * 0.15;
                const colors = ['#fbbf24', '#f59e0b', '#38bdf8', '#ffffff', '#ec4899'];

                for (let i = 0; i < 45; i++) {
                    const angle = Math.random() * Math.PI * 2;
                    const speed = 3 + Math.random() * 7;
                    this.particles.push({
                        x: posX, y: posY,
                        vx: Math.cos(angle) * speed,
                        vy: Math.sin(angle) * speed,
                        size: 4 + Math.random() * 4,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        alpha: 1,
                        decay: 0.015,
                        gravity: 0.08
                    });
                }
            },

            launchVictoryConfetti() {
                const canvas = document.getElementById('tripeaks-fx-canvas');
                if (!canvas) return;

                const colors = ['#fbbf24', '#f59e0b', '#10b981', '#38bdf8', '#ec4899', '#ffffff'];
                for (let i = 0; i < 90; i++) {
                    this.particles.push({
                        x: Math.random() * canvas.width,
                        y: -10 - Math.random() * 50,
                        vx: (Math.random() - 0.5) * 4,
                        vy: 2 + Math.random() * 5,
                        size: 5 + Math.random() * 5,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        alpha: 1,
                        decay: 0.008,
                        gravity: 0.04
                    });
                }
            },

            loopParticles() {
                const canvas = document.getElementById('tripeaks-fx-canvas');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    for (let i = this.particles.length - 1; i >= 0; i--) {
                        const p = this.particles[i];
                        p.x += p.vx;
                        p.y += p.vy;
                        p.vy += p.gravity;
                        p.alpha -= p.decay;

                        if (p.alpha <= 0) {
                            this.particles.splice(i, 1);
                            continue;
                        }

                        ctx.save();
                        ctx.globalAlpha = p.alpha;
                        ctx.fillStyle = p.color;
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.restore();
                    }
                }
                requestAnimationFrame(() => this.loopParticles());
            },

            getAudioContext() {
                if (!this.audioCtx) {
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (AudioContextClass) {
                        this.audioCtx = new AudioContextClass();
                    }
                }
                if (this.audioCtx && this.audioCtx.state === 'suspended') {
                    this.audioCtx.resume();
                }
                return this.audioCtx;
            },

            toggleSound() {
                this.soundEnabled = !this.soundEnabled;
                localStorage.setItem('tripeaks_sound_enabled', this.soundEnabled ? 'true' : 'false');
            },

            playPickSound(combo) {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                const baseFreq = 440;
                const freq = baseFreq * Math.pow(1.08, Math.min(combo, 16));

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(freq * 1.5, ctx.currentTime + 0.12);

                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.14);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            },

            playThudSound() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(140, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(50, ctx.currentTime + 0.09);

                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.11);
            },

            playDrawSound() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(320, ctx.currentTime);
                osc.frequency.linearRampToValueAtTime(500, ctx.currentTime + 0.08);

                gain.gain.setValueAtTime(0.18, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.11);
            },

            playPeakChord() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.06));

                    gain.gain.setValueAtTime(0.22, ctx.currentTime + (idx * 0.06));
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (idx * 0.06) + 0.35);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(ctx.currentTime + (idx * 0.06));
                    osc.stop(ctx.currentTime + (idx * 0.06) + 0.36);
                });
            },

            playVictoryFanfare() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const fanfare = [523.25, 659.25, 783.99, 1046.50, 1318.51];
                fanfare.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.1));

                    gain.gain.setValueAtTime(0.25, ctx.currentTime + (idx * 0.1));
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (idx * 0.1) + 0.5);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(ctx.currentTime + (idx * 0.1));
                    osc.stop(ctx.currentTime + (idx * 0.1) + 0.52);
                });
            },

            playGameOverSound() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const notes = [400, 350, 300, 250];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.12));

                    gain.gain.setValueAtTime(0.18, ctx.currentTime + (idx * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (idx * 0.12) + 0.25);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(ctx.currentTime + (idx * 0.12));
                    osc.stop(ctx.currentTime + (idx * 0.12) + 0.26);
                });
            },

            playTimeBonusSound() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(700, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1400, ctx.currentTime + 0.1);

                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.13);
            },

            playPenaltySound() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                // Two-tone descending buzz for casino loss
                [180, 110].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + (idx * 0.08));
                    osc.frequency.exponentialRampToValueAtTime(freq * 0.7, ctx.currentTime + (idx * 0.08) + 0.12);

                    gain.gain.setValueAtTime(0.18, ctx.currentTime + (idx * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (idx * 0.08) + 0.14);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(ctx.currentTime + (idx * 0.08));
                    osc.stop(ctx.currentTime + (idx * 0.08) + 0.15);
                });
            },

            playEmergencyTick() {
                if (!this.soundEnabled) return;
                const ctx = this.getAudioContext();
                if (!ctx) return;

                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(400, ctx.currentTime + 0.04);

                gain.gain.setValueAtTime(0.22, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.06);
            }
        };
    }
</script>
</div>
