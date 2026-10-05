<div>
<style>
    /* ========================================================
       ELVITH ROYALE LUXURY 3D & CASINO FELT STYLING
       ======================================================== */
    .elvith-card-shell {
        position: relative;
        width: 100%;
        max-width: 100%;
        aspect-ratio: 5 / 7.2;
        border-radius: 0.95rem;
        filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.6));
    }
    @media (min-width: 640px) {
        .elvith-card-shell {
            border-radius: 1.25rem;
            filter: drop-shadow(0 14px 26px rgba(0, 0, 0, 0.65));
        }
    }
    .elvith-3d-scene {
        perspective: 1200px;
        -webkit-perspective: 1200px;
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
        border-radius: 0.95rem;
        overflow: hidden;
    }
    @media (min-width: 640px) {
        .elvith-card-face {
            border-radius: 1.25rem;
        }
    }
    .elvith-card-back {
        transform: rotateY(180deg);
        -webkit-transform: rotateY(180deg);
    }

    /* Luxury Bicycle Card Surface */
    .card-luxury-surface {
        border-radius: 0.95rem;
        background: linear-gradient(155deg, #ffffff 0%, #fafafb 50%, #f1f1f5 100%);
        border: 1.5px solid rgba(203, 213, 225, 0.85);
        box-shadow: 
            inset 0 0 0 1px rgba(255, 255, 255, 0.95),
            inset 0 0 12px rgba(0, 0, 0, 0.03),
            0 8px 18px -3px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }
    @media (min-width: 640px) {
        .card-luxury-surface {
            border-radius: 1.25rem;
            box-shadow: 
                inset 0 0 0 1px rgba(255, 255, 255, 0.95),
                inset 0 0 16px rgba(0, 0, 0, 0.03),
                0 12px 28px -4px rgba(0, 0, 0, 0.5);
        }
    }

    /* Pinstripe Inner Frame like Classic Playing Cards */
    .card-inner-frame {
        position: absolute;
        inset: 3.5px;
        border: 1px solid rgba(203, 213, 225, 0.7);
        border-radius: 0.75rem;
        pointer-events: none;
    }
    @media (min-width: 640px) {
        .card-inner-frame {
            inset: 6px;
            border-radius: 0.95rem;
        }
    }
    .card-inner-frame::after {
        content: '';
        position: absolute;
        inset: 1.5px;
        border: 1px dashed rgba(203, 213, 225, 0.45);
        border-radius: 0.65rem;
    }
    @media (min-width: 640px) {
        .card-inner-frame::after {
            inset: 2px;
            border-radius: 0.8rem;
        }
    }

    /* Velvet Felt Mat Table */
    .table-felt-mat {
        background: radial-gradient(circle at 50% 38%, #065f46 0%, #064e3b 45%, #022c22 80%, #011a13 100%);
        box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.85), 0 20px 40px rgba(0, 0, 0, 0.6);
        border: 2px solid rgba(245, 158, 11, 0.4);
    }

    /* 3D Shiny Gold VS Medallion */
    .vs-medallion {
        background: radial-gradient(circle at 35% 35%, #fef08a 0%, #f59e0b 55%, #b45309 100%);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.55), inset 0 1px 2px rgba(255,255,255,0.7), inset 0 -2px 3px rgba(0,0,0,0.3);
        border: 1.5px solid #fef3c7;
    }

    /* 3D Arcade Buttons */
    .btn-3d-green {
        background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        box-shadow: 0 5px 0 #047857, 0 10px 20px rgba(16, 185, 129, 0.4);
        border-top: 1px solid rgba(255, 255, 255, 0.4);
        transition: all 0.12s ease;
    }
    .btn-3d-green:active {
        transform: translateY(4px);
        box-shadow: 0 1px 0 #047857, 0 4px 8px rgba(16, 185, 129, 0.2);
    }

    .btn-3d-red {
        background: linear-gradient(180deg, #f43f5e 0%, #e11d48 100%);
        box-shadow: 0 5px 0 #be123c, 0 10px 20px rgba(244, 63, 94, 0.4);
        border-top: 1px solid rgba(255, 255, 255, 0.4);
        transition: all 0.12s ease;
    }
    .btn-3d-red:active {
        transform: translateY(4px);
        box-shadow: 0 1px 0 #be123c, 0 4px 8px rgba(244, 63, 94, 0.2);
    }

    .btn-3d-gold {
        background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 60%, #d97706 100%);
        box-shadow: 0 5px 0 #b45309, 0 10px 24px rgba(245, 158, 11, 0.5);
        border-top: 1px solid rgba(255, 255, 255, 0.55);
        transition: all 0.12s ease;
    }
    .btn-3d-gold:active {
        transform: translateY(4px);
        box-shadow: 0 1px 0 #b45309, 0 4px 10px rgba(245, 158, 11, 0.25);
    }

    /* Shimmer Sweep Animation on Card Back */
    @keyframes shimmer-sweep {
        0% { transform: translateX(-150%) rotate(25deg); }
        100% { transform: translateX(250%) rotate(25deg); }
    }
    .shimmer-effect::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(90deg, transparent 30%, rgba(255, 255, 255, 0.22) 50%, transparent 70%);
        transform: rotate(25deg);
        animation: shimmer-sweep 3.5s infinite;
        pointer-events: none;
    }

    /* Futuristic X-Ray Cyber Scanning Animations */
    @keyframes xray-scanner {
        0% { top: 0%; opacity: 0.85; }
        50% { top: 96%; opacity: 1; }
        100% { top: 0%; opacity: 0.85; }
    }
    .xray-scan-beam {
        position: absolute;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, #38bdf8 20%, #ffffff 50%, #38bdf8 80%, transparent);
        box-shadow: 0 0 16px 4px #06b6d4, 0 0 32px 8px rgba(6, 182, 212, 0.6);
        animation: xray-scanner 1.2s ease-in-out infinite;
        z-index: 35;
        pointer-events: none;
    }
    .xray-grid-overlay {
        background: 
            linear-gradient(rgba(6, 182, 212, 0.15) 1px, transparent 1px),
            linear-gradient(90deg, rgba(6, 182, 212, 0.15) 1px, transparent 1px);
        background-size: 14px 14px;
        pointer-events: none;
    }

    /* Dramatic Shield Shockwave & Impact Shake */
    @keyframes shield-impact {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        15% { transform: translate(-10px, -4px) rotate(-2deg); }
        30% { transform: translate(10px, 3px) rotate(2deg); }
        45% { transform: translate(-8px, 2px) rotate(-1.5deg); }
        60% { transform: translate(6px, -2px) rotate(1deg); }
        75% { transform: translate(-4px, 1px) rotate(-0.5deg); }
        90% { transform: translate(2px, 0px) rotate(0.2deg); }
    }
    .animate-shield-impact {
        animation: shield-impact 0.45s cubic-bezier(.36,.07,.19,.97) both;
    }

    /* Electric Spark Crackle on Shattered Shield */
    @keyframes electric-crackle {
        0%, 100% { opacity: 0.4; transform: scale(1); }
        25% { opacity: 1; transform: scale(1.15) rotate(4deg); }
        50% { opacity: 0.7; transform: scale(0.92) rotate(-4deg); }
        75% { opacity: 1; transform: scale(1.1) rotate(2deg); }
    }
    .animate-electric {
        animation: electric-crackle 0.25s infinite;
    }
</style>

<div x-data="{
    audioCtx: null,
    soundEnabled: true,
    bgmEnabled: false,
    bgmInterval: null,
    bgmGainNode: null,
    bgmVinylSource: null,
    bgmStep: 0,
    bgmBpm: 72,
    isFlipped: false,
    isPeeking: false,
    peekTimeout: null,
    isShieldShaking: false,
    revealingState: null, // 'won', 'tie', 'lost', 'shield'
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

    playShieldBreak() {
        if (!this.soundEnabled) return;
        this.initAudio();
        if (!this.audioCtx) return;

        const ctx = this.audioCtx;
        const now = ctx.currentTime;

        // 1. Heavy Shield Impact Boom (Bass hit)
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(240, now);
        osc.frequency.exponentialRampToValueAtTime(45, now + 0.35);
        gain.gain.setValueAtTime(0.55, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.36);

        // 2. Crystal / Electric Barrier Shatter (High sparkle burst)
        [1100, 1480, 2090, 2790, 3500].forEach((freq, idx) => {
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'sine';
            o.frequency.setValueAtTime(freq, now + idx * 0.03);
            o.frequency.exponentialRampToValueAtTime(freq * 0.5, now + idx * 0.03 + 0.18);
            g.gain.setValueAtTime(0.22, now + idx * 0.03);
            g.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.03 + 0.32);
            o.connect(g);
            g.connect(ctx.destination);
            o.start(now + idx * 0.03);
            o.stop(now + idx * 0.03 + 0.34);
        });
    },

    playXRayScan() {
        if (!this.soundEnabled) return;
        this.initAudio();
        if (!this.audioCtx) return;

        const ctx = this.audioCtx;
        const now = ctx.currentTime;

        // Futuristic cybernetic sweep & high-pitch telemetry beeps
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(450, now);
        osc.frequency.linearRampToValueAtTime(1500, now + 0.35);
        osc.frequency.linearRampToValueAtTime(800, now + 0.7);
        
        const filter = ctx.createBiquadFilter();
        filter.type = 'bandpass';
        filter.frequency.setValueAtTime(1100, now);
        filter.Q.setValueAtTime(4.5, now);

        gain.gain.setValueAtTime(0.2, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.85);

        osc.connect(filter);
        filter.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + 0.9);

        // Rapid digital telemetry blips
        [1760, 2349, 3135].forEach((freq, i) => {
            const blip = ctx.createOscillator();
            const blipGain = ctx.createGain();
            blip.type = 'sine';
            blip.frequency.setValueAtTime(freq, now + 0.1 + i * 0.1);
            blipGain.gain.setValueAtTime(0.16, now + 0.1 + i * 0.1);
            blipGain.gain.exponentialRampToValueAtTime(0.001, now + 0.1 + i * 0.1 + 0.08);
            blip.connect(blipGain);
            blipGain.connect(ctx.destination);
            blip.start(now + 0.1 + i * 0.1);
            blip.stop(now + 0.1 + i * 0.1 + 0.09);
        });
    },

    playPowerUp() {
        if (!this.soundEnabled) return;
        this.initAudio();
        if (!this.audioCtx) return;

        const ctx = this.audioCtx;
        const now = ctx.currentTime;
        [587.33, 880, 1174.66].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, now + i * 0.07);
            gain.gain.setValueAtTime(0.22, now + i * 0.07);
            gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.07 + 0.22);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now + i * 0.07);
            osc.stop(now + i * 0.07 + 0.25);
        });
    },

    launchShieldShatter() {
        const count = 36;
        const container = document.getElementById('card-arena-container');
        if (!container) return;

        for (let i = 0; i < count; i++) {
            const shard = document.createElement('div');
            shard.className = 'absolute pointer-events-none z-50';
            const colors = ['#06b6d4', '#38bdf8', '#10b981', '#34d399', '#fef08a', '#ffffff'];
            const color = colors[Math.floor(Math.random() * colors.length)];
            const size = Math.floor(Math.random() * 12) + 6;
            
            shard.style.width = `${size}px`;
            shard.style.height = `${size}px`;
            shard.style.backgroundColor = color;
            shard.style.clipPath = 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)'; // diamond crystal shape
            shard.style.left = '50%';
            shard.style.top = '48%';
            shard.style.opacity = '1';
            shard.style.boxShadow = `0 0 12px ${color}`;
            shard.style.transition = 'all 1.1s cubic-bezier(0.1, 0.9, 0.2, 1)';
            
            container.appendChild(shard);

            const angle = Math.random() * Math.PI * 2;
            const distance = Math.random() * 240 + 70;
            const moveX = Math.cos(angle) * distance;
            const moveY = Math.sin(angle) * distance;
            const rot = Math.random() * 720 - 360;

            setTimeout(() => {
                shard.style.transform = `translate(${moveX}px, ${moveY}px) rotate(${rot}deg) scale(0)`;
                shard.style.opacity = '0';
            }, 15);

            setTimeout(() => {
                shard.remove();
            }, 1200);
        }
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
    },

    toggleBgm() {
        this.initAudio();
        if (!this.audioCtx) return;

        this.bgmEnabled = !this.bgmEnabled;
        if (this.bgmEnabled) {
            this.startBgm();
        } else {
            this.stopBgm();
        }
    },

    startBgm() {
        if (!this.audioCtx) return;
        const ctx = this.audioCtx;

        // Master BGM gain node with soft warm volume
        if (!this.bgmGainNode) {
            this.bgmGainNode = ctx.createGain();
            this.bgmGainNode.connect(ctx.destination);
        }
        this.bgmGainNode.gain.cancelScheduledValues(ctx.currentTime);
        this.bgmGainNode.gain.setValueAtTime(0, ctx.currentTime);
        this.bgmGainNode.gain.linearRampToValueAtTime(0.24, ctx.currentTime + 1.2);

        // 1. Subtle Vinyl Crackle & Tape Ambience
        this.startVinylCrackle();

        // 2. Jazz Lounge Chord Progression (Frequencies in Hz)
        // Dm9 -> G13 -> Cmaj9 -> Am9
        const chords = [
            { chord: [146.83, 174.61, 220.00, 261.63, 329.63], bass: 73.42 }, // Dm9
            { chord: [98.00, 174.61, 246.94, 329.63], bass: 49.00 },          // G13
            { chord: [130.81, 164.81, 196.00, 246.94, 293.66], bass: 65.41 }, // Cmaj9
            { chord: [110.00, 196.00, 261.63, 329.63], bass: 55.00 }          // Am9
        ];

        this.bgmStep = 0;
        const beatDuration = 60 / this.bgmBpm; // ~0.833s per beat (4/4 time)
        const stepDuration = beatDuration / 2; // 8th note ~0.416s

        let nextNoteTime = ctx.currentTime + 0.1;
        
        const scheduleLoop = () => {
            if (!this.bgmEnabled) return;

            while (nextNoteTime < ctx.currentTime + 0.5) {
                const currentStep = this.bgmStep % 32; // 32 8th-notes (4 bars x 8)
                const barIndex = Math.floor(currentStep / 8);
                const stepInBar = currentStep % 8;
                const chordData = chords[barIndex];

                // Play Rhodes Piano Chord on downbeat (step 0) and syncopated beat (step 4)
                if (stepInBar === 0) {
                    this.playRhodesChord(chordData.chord, nextNoteTime, beatDuration * 2.2);
                    this.playBassNote(chordData.bass, nextNoteTime, beatDuration * 1.8);
                } else if (stepInBar === 4) {
                    this.playRhodesChord(chordData.chord, nextNoteTime, beatDuration * 1.6, 0.7);
                    this.playBassNote(chordData.bass * 1.5, nextNoteTime, beatDuration * 1.4);
                }

                // Lofi Percussion:
                // Soft Kick on step 0 and step 5
                if (stepInBar === 0 || stepInBar === 5) {
                    this.playLofiKick(nextNoteTime);
                }
                // Soft Snare / Rimshot on step 2 and step 6 (Beat 2 and 4)
                if (stepInBar === 2 || stepInBar === 6) {
                    this.playLofiRim(nextNoteTime);
                }
                // Soft Shaker on every 8th note with subtle swing
                this.playLofiShaker(nextNoteTime, (stepInBar % 2 === 1) ? 0.08 : 0.04);

                // Swing timing on odd eighth notes:
                const swing = (stepInBar % 2 === 0) ? stepDuration * 1.08 : stepDuration * 0.92;
                nextNoteTime += swing;
                this.bgmStep++;
            }
        };

        clearInterval(this.bgmInterval);
        this.bgmInterval = setInterval(scheduleLoop, 120);
        scheduleLoop();
    },

    stopBgm() {
        clearInterval(this.bgmInterval);
        this.bgmInterval = null;

        if (this.bgmGainNode && this.audioCtx) {
            const ctx = this.audioCtx;
            this.bgmGainNode.gain.cancelScheduledValues(ctx.currentTime);
            this.bgmGainNode.gain.linearRampToValueAtTime(0.001, ctx.currentTime + 0.6);
            setTimeout(() => {
                this.stopVinylCrackle();
            }, 650);
        }
    },

    startVinylCrackle() {
        if (!this.audioCtx || this.bgmVinylSource) return;
        const ctx = this.audioCtx;
        
        // Procedural 3-second vinyl crackle buffer
        const bufferSize = ctx.sampleRate * 3;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        
        for (let i = 0; i < bufferSize; i++) {
            // Pinkish noise floor
            let noise = (Math.random() * 2 - 1) * 0.012;
            // Random tiny vinyl dust clicks
            if (Math.random() < 0.0006) {
                noise += (Math.random() * 2 - 1) * 0.32;
            }
            data[i] = noise;
        }

        const source = ctx.createBufferSource();
        source.buffer = buffer;
        source.loop = true;

        const filter = ctx.createBiquadFilter();
        filter.type = 'bandpass';
        filter.frequency.setValueAtTime(1400, ctx.currentTime);
        filter.Q.setValueAtTime(1.2, ctx.currentTime);

        const gain = ctx.createGain();
        gain.gain.setValueAtTime(0.035, ctx.currentTime);

        source.connect(filter);
        filter.connect(gain);
        gain.connect(this.bgmGainNode);

        source.start(0);
        this.bgmVinylSource = source;
    },

    stopVinylCrackle() {
        if (this.bgmVinylSource) {
            try {
                this.bgmVinylSource.stop();
                this.bgmVinylSource.disconnect();
            } catch (e) {}
            this.bgmVinylSource = null;
        }
    },

    playRhodesChord(frequencies, time, duration, volumeScale = 1.0) {
        if (!this.audioCtx || !this.bgmGainNode) return;
        const ctx = this.audioCtx;

        frequencies.forEach((freq, idx) => {
            const osc = ctx.createOscillator();
            const osc2 = ctx.createOscillator();
            const gain = ctx.createGain();
            const filter = ctx.createBiquadFilter();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, time);

            osc2.type = 'triangle';
            osc2.frequency.setValueAtTime(freq * 1.002, time); // Subtle warm detune

            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(1100, time);
            filter.frequency.exponentialRampToValueAtTime(550, time + duration);

            const baseVol = 0.024 * volumeScale;
            gain.gain.setValueAtTime(0.0001, time);
            gain.gain.linearRampToValueAtTime(baseVol, time + 0.035);
            gain.gain.exponentialRampToValueAtTime(baseVol * 0.3, time + duration * 0.5);
            gain.gain.exponentialRampToValueAtTime(0.00001, time + duration);

            osc.connect(gain);
            osc2.connect(gain);
            gain.connect(filter);
            filter.connect(this.bgmGainNode);

            osc.start(time);
            osc2.start(time);
            osc.stop(time + duration + 0.1);
            osc2.stop(time + duration + 0.1);
        });
    },

    playBassNote(freq, time, duration) {
        if (!this.audioCtx || !this.bgmGainNode) return;
        const ctx = this.audioCtx;

        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        const filter = ctx.createBiquadFilter();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, time);

        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(180, time);
        filter.frequency.exponentialRampToValueAtTime(70, time + duration);

        gain.gain.setValueAtTime(0.0001, time);
        gain.gain.linearRampToValueAtTime(0.065, time + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, time + duration);

        osc.connect(filter);
        filter.connect(gain);
        gain.connect(this.bgmGainNode);

        osc.start(time);
        osc.stop(time + duration + 0.05);
    },

    playLofiKick(time) {
        if (!this.audioCtx || !this.bgmGainNode) return;
        const ctx = this.audioCtx;

        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(80, time);
        osc.frequency.exponentialRampToValueAtTime(35, time + 0.09);

        gain.gain.setValueAtTime(0.065, time);
        gain.gain.exponentialRampToValueAtTime(0.0001, time + 0.11);

        osc.connect(gain);
        gain.connect(this.bgmGainNode);

        osc.start(time);
        osc.stop(time + 0.12);
    },

    playLofiRim(time) {
        if (!this.audioCtx || !this.bgmGainNode) return;
        const ctx = this.audioCtx;

        const bufferSize = ctx.sampleRate * 0.04;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
            data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.3));
        }

        const noise = ctx.createBufferSource();
        noise.buffer = buffer;

        const filter = ctx.createBiquadFilter();
        filter.type = 'bandpass';
        filter.frequency.setValueAtTime(2200, time);

        const gain = ctx.createGain();
        gain.gain.setValueAtTime(0.045, time);
        gain.gain.exponentialRampToValueAtTime(0.0001, time + 0.04);

        noise.connect(filter);
        filter.connect(gain);
        gain.connect(this.bgmGainNode);

        noise.start(time);
        noise.stop(time + 0.05);
    },

    playLofiShaker(time, vol = 0.05) {
        if (!this.audioCtx || !this.bgmGainNode) return;
        const ctx = this.audioCtx;

        const bufferSize = ctx.sampleRate * 0.025;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
            data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.25));
        }

        const noise = ctx.createBufferSource();
        noise.buffer = buffer;

        const filter = ctx.createBiquadFilter();
        filter.type = 'highpass';
        filter.frequency.setValueAtTime(5500, time);

        const gain = ctx.createGain();
        gain.gain.setValueAtTime(vol, time);
        gain.gain.exponentialRampToValueAtTime(0.0001, time + 0.025);

        noise.connect(filter);
        filter.connect(gain);
        gain.connect(this.bgmGainNode);

        noise.start(time);
        noise.stop(time + 0.03);
    }
}" 
x-init="
    $wire.on('game-started', () => { 
        isFlipped = false;
        revealingState = null;
        playCardFlip(); 
    });

    $wire.on('power-up-peek-animation', () => {
        playXRayScan();
        playPowerUp();
        triggerHaptic('success');
        
        // 1. Putar kartu misteri untuk membukanya secara fisik (3D Flip Open)
        playCardFlip();
        isFlipped = true;
        isPeeking = true;

        // 2. Berikan waktu 1.6 detik agar pemain melihat kartu dengan mata kepalanya sendiri
        clearTimeout(peekTimeout);
        peekTimeout = setTimeout(() => {
            // 3. Putar kartu kembali ke posisi tertutup (3D Flip Close)
            playCardFlip();
            isFlipped = false;
            setTimeout(() => {
                isPeeking = false;
            }, 650);
        }, 1600);
    });

    $wire.on('power-up-swapped', () => {
        playCardFlip();
        playPowerUp();
        triggerHaptic('success');
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
        const isShield = (data.isShield === true) || ($wire.roundResult === 'shield');
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
        } else if (isShield) {
            revealingState = 'shield';
            roundStatusText = '🛡️ PERISAI MENYERAP SERANGAN!';
            triggerHaptic('error');
            
            // Efek guncangan arena (Screen Shake) & ledakan kristal perisai (Shatter Shards)
            isShieldShaking = true;
            launchShieldShatter();
            setTimeout(() => { isShieldShaking = false; }, 480);
            setTimeout(() => { playShieldBreak(); }, 160);
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
        const duration = isShield ? 1900 : 1500;
        clearTimeout(animationTimeout);
        animationTimeout = setTimeout(() => {
            if (isCorrect || isTie || isShield) {
                isFlipped = false;
                revealingState = null;
                $wire.advanceRound();
            } else {
                $wire.finalizeGameOver();
            }
        }, duration);
    });

    $wire.on('show-game-over-summary', (event) => {
        if (event.isNewHighScore) {
            launchConfetti();
        }
    });
"
@keydown.window="
    if ($event.target.tagName === 'INPUT' || $event.target.tagName === 'TEXTAREA') return;
    if ($wire.gameState === 'playing' && !isFlipped && !isPeeking) {
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
            <!-- 1. SFX Toggle -->
            <button 
                type="button" 
                @click="soundEnabled = !soundEnabled"
                class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-700/80 bg-slate-900/80 hover:bg-slate-800 text-[11px] sm:text-xs font-semibold text-slate-300 transition flex items-center gap-1 shadow-sm select-none cursor-pointer"
                :title="soundEnabled ? 'Matikan Suara SFX' : 'Aktifkan Suara SFX'"
            >
                <span x-text="soundEnabled ? '🔊 SFX' : '🔇 SFX'"></span>
            </button>

            <!-- 2. Synthesized Lofi Casino Jazz BGM Toggle -->
            <button 
                type="button" 
                @click="toggleBgm()"
                class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border transition flex items-center gap-1.5 shadow-sm text-[11px] sm:text-xs font-bold select-none cursor-pointer"
                :class="bgmEnabled ? 'border-amber-400/80 bg-gradient-to-r from-amber-500/25 via-yellow-400/20 to-amber-500/25 text-amber-300 shadow-md shadow-amber-500/25 ring-1 ring-amber-400/50' : 'border-slate-700/80 bg-slate-900/80 hover:bg-slate-800 text-slate-400'"
                :title="bgmEnabled ? 'Matikan Musik Lofi' : 'Putar Musik Lofi Jazz Kasino Santai (Nol Kuota/MP3)'"
            >
                <!-- Animated Equalizer Bars when playing -->
                <div x-show="bgmEnabled" class="flex items-end gap-0.5 h-3 w-3 pointer-events-none" x-cloak>
                    <span class="w-0.5 bg-amber-400 rounded-full animate-pulse h-full"></span>
                    <span class="w-0.5 bg-yellow-300 rounded-full animate-bounce h-2/3"></span>
                    <span class="w-0.5 bg-amber-400 rounded-full animate-pulse h-4/5"></span>
                </div>
                <span x-show="!bgmEnabled">🎵</span>
                <span x-text="bgmEnabled ? 'Lofi: ON' : 'Lofi: OFF'"></span>
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
    <div class="relative z-10 grid grid-cols-1 xl:grid-cols-12 gap-3 sm:gap-6 p-1.5 sm:p-5 lg:p-8">
        
        <!-- CARD TABLE ARENA (8 Cols) - VELVET FELT CASINO MAT -->
        <div class="xl:col-span-8 flex flex-col items-center justify-between rounded-2xl sm:rounded-3xl px-2 py-3 sm:p-6 lg:p-8 table-felt-mat relative overflow-hidden min-h-[440px] sm:min-h-[570px]">
            
            <!-- Table Subtle Pattern / Gold Trim Ring -->
            <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="absolute inset-1 sm:inset-3 rounded-[14px] sm:rounded-[22px] border border-amber-400/25 pointer-events-none"></div>

            <!-- Top Game HUD (Score, Message, Streak) - Mobile Glassmorphism -->
            <div class="w-full flex items-center justify-between gap-1.5 sm:gap-4 z-10 mb-2 sm:mb-4">
                <!-- Streak Meter -->
                <div class="flex items-center gap-1.5 sm:gap-3 bg-slate-950/85 backdrop-blur-md px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl border border-amber-500/35 shadow-lg">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-gradient-to-tr from-amber-600 to-yellow-400 text-slate-950 flex items-center justify-center font-black text-sm sm:text-base shrink-0 shadow-sm">
                        🔥
                    </div>
                    <div>
                        <div class="text-[8px] sm:text-[10px] uppercase font-bold tracking-wider text-slate-400 leading-none">Streak</div>
                        <div class="text-sm sm:text-xl font-black text-amber-300 font-mono leading-none mt-0.5">
                            {{ $streak }}<span class="text-[10px] sm:text-xs text-amber-500 font-bold">x</span>
                        </div>
                    </div>
                </div>

                <!-- Center Status Banner -->
                <div class="flex-1 max-w-sm text-center px-1">
                    @if($gameState === 'playing' || $gameState === 'revealing')
                        <div 
                            class="inline-block px-3 sm:px-4 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-bold border transition duration-300 shadow-md leading-tight"
                            :class="{
                                'bg-emerald-500/30 text-emerald-200 border-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.5)] animate-pulse': revealingState === 'won',
                                'bg-amber-500/30 text-amber-200 border-amber-400 shadow-[0_0_15px_rgba(251,191,36,0.5)] animate-pulse': revealingState === 'tie',
                                'bg-rose-500/30 text-rose-200 border-rose-400 shadow-[0_0_15px_rgba(244,63,94,0.5)] animate-pulse': revealingState === 'lost',
                                'bg-slate-950/80 text-slate-300 border-slate-700/70': !revealingState
                            }"
                        >
                            <span x-text="roundStatusText || '{{ addslashes($resultMessage) }}'"></span>
                        </div>
                    @elseif($gameState === 'game_over')
                        <div class="inline-block px-3 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-extrabold bg-rose-500/30 text-rose-200 border border-rose-500/60 shadow-[0_0_15px_rgba(244,63,94,0.4)] leading-tight">
                            💀 GAME OVER — {{ $resultMessage }}
                        </div>
                    @else
                        <div class="inline-block px-3 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-bold bg-amber-500/15 text-amber-200 border border-amber-500/40 shadow-md">
                            Siap menguji firasat? Klik Mulai!
                        </div>
                    @endif
                </div>

                <!-- Score Board -->
                <div class="flex items-center gap-1.5 sm:gap-3 bg-slate-950/85 backdrop-blur-md px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl border border-emerald-500/35 shadow-lg">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-slate-950 flex items-center justify-center font-black text-sm sm:text-base shrink-0 shadow-sm">
                        💎
                    </div>
                    <div class="text-right">
                        <div class="text-[8px] sm:text-[10px] uppercase font-bold tracking-wider text-slate-400 leading-none">Skor</div>
                        <div class="text-sm sm:text-xl font-black text-emerald-300 font-mono leading-none mt-0.5">
                            {{ number_format($score) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Counting & Deck Status Tracker (Live Remaining Probability) -->
            @if($gameState === 'playing' || $gameState === 'revealing')
                <div class="w-full z-10 flex flex-wrap items-center justify-between gap-1.5 px-3 py-1.5 mb-2 bg-slate-950/75 backdrop-blur-md rounded-xl sm:rounded-2xl border border-amber-500/25 shadow-md text-[10px] sm:text-xs font-mono">
                    <div class="flex items-center gap-2">
                        <span class="text-amber-400 font-bold flex items-center gap-1">
                            <span>🂠</span>
                            <span>Dek:</span>
                        </span>
                        <span class="font-black text-white bg-slate-900 px-1.5 py-0.5 rounded border border-slate-800">
                            {{ $this->deckStats['cards_left'] }} / {{ $this->deckStats['total'] }}
                        </span>
                        <span class="text-slate-400 text-[9px] sm:text-[10px]">
                            (Keluar: {{ $this->deckStats['discarded'] }})
                        </span>
                    </div>

                    <!-- Live Remaining Odds / Card Counting Hints -->
                    <div class="flex items-center gap-2 text-[9px] sm:text-[11px] font-bold">
                        <span class="text-emerald-400 bg-emerald-950/60 px-1.5 py-0.5 rounded border border-emerald-500/30" title="Kartu 2-6 yang tersisa di dek">
                            ▼ 2-6: {{ $this->deckStats['low_left'] }}
                        </span>
                        <span class="text-slate-300 bg-slate-900/80 px-1.5 py-0.5 rounded border border-slate-700/50" title="Kartu 7-10 yang tersisa di dek">
                            • 7-10: {{ $this->deckStats['mid_left'] }}
                        </span>
                        <span class="text-amber-300 bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-500/30" title="Kartu J, Q, K, A yang tersisa di dek">
                            ▲ J-A: {{ $this->deckStats['high_left'] }}
                        </span>
                    </div>
                </div>

                <!-- X-Ray Peek Clue Banner (Muncul saat Power-Up Intip Digunakan) -->
                @if($peekedHint)
                    <div class="w-full z-10 mb-2 px-3 py-1.5 bg-gradient-to-r from-sky-950 via-cyan-900 to-indigo-950 border border-cyan-400/70 rounded-xl shadow-[0_0_20px_rgba(6,182,212,0.45)] flex items-center justify-center gap-2 text-cyan-200 text-xs sm:text-sm font-black animate-pulse">
                        <span class="text-base sm:text-lg">👁️</span>
                        <span>{{ $peekedHint }}</span>
                    </div>
                @endif
            @endif

            <!-- Card Playing Area: LUXURY CASINO CARDS SIDE-BY-SIDE -->
            <div class="relative z-10 flex flex-col items-center justify-center my-auto py-2 sm:py-4 w-full">
                
                @if($gameState === 'idle')
                    <!-- Idle Screen: Ready to Play -->
                    <div class="flex flex-col items-center text-center space-y-4 sm:space-y-6">
                        <div class="relative w-44 h-62 sm:w-56 sm:h-80 group cursor-pointer elvith-3d-scene" wire:click="startGame">
                            <div class="absolute inset-0 translate-x-2 translate-y-2 rounded-2xl bg-slate-900/90 border border-slate-700/50 shadow-xl"></div>
                            <div class="absolute inset-0 translate-x-1 translate-y-1 rounded-2xl bg-slate-800/90 border border-slate-700/60 shadow-xl"></div>
                            
                            <!-- Top Realistic Card Back (Holographic Shimmer) -->
                            <div class="relative w-full h-full rounded-2xl p-2.5 sm:p-3 bg-white shadow-2xl transition transform group-hover:-translate-y-2 group-hover:shadow-[0_25px_50px_rgba(0,0,0,0.7)] flex items-center justify-center border border-slate-300 overflow-hidden shimmer-effect">
                                <div class="w-full h-full rounded-xl bg-gradient-to-br from-blue-950 via-indigo-950 to-slate-950 border-4 border-amber-400/80 relative overflow-hidden flex flex-col items-center justify-center text-amber-300 p-3 sm:p-4 shadow-inner">
                                    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#fbbf24_2px,transparent_2px)] [background-size:12px_12px]"></div>
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-2 border-amber-400/70 flex items-center justify-center relative shadow-lg bg-amber-400/10">
                                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-full border border-amber-400/40 flex items-center justify-center text-2xl sm:text-3xl font-serif">
                                            🂠
                                        </div>
                                    </div>
                                    <span class="mt-3 text-[11px] sm:text-xs font-black uppercase tracking-widest text-amber-200 font-serif-display drop-shadow">
                                        ELVITH DECK
                                    </span>
                                    <span class="text-[8px] sm:text-[9px] text-amber-400/90 font-mono mt-0.5">52 Bicycle Royale</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button 
                                type="button" 
                                wire:click="startGame"
                                class="btn-3d-gold px-7 sm:px-9 py-3 sm:py-3.5 rounded-2xl text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider flex items-center gap-2 cursor-pointer mx-auto touch-manipulation"
                            >
                                <span>🃏 MULAI PERMAINAN</span>
                                <span class="hidden sm:inline px-2 py-0.5 bg-black/15 text-[10px] font-mono rounded">ENTER</span>
                            </button>
                            <p class="text-[11px] text-slate-300 mt-2">Sentuh kartu atau tombol untuk mengocok</p>
                        </div>
                    </div>

                @else
                    <!-- Active Gameplay: TWO CARDS SIDE-BY-SIDE (High-End Casino Look) -->
                    <div 
                        class="relative flex flex-row items-center justify-center gap-1.5 xs:gap-3 sm:gap-6 lg:gap-10 w-full max-w-2xl px-0 sm:px-0 transition-transform"
                        :class="{ 'animate-shield-impact': isShieldShaking }"
                    >
                        
                        <!-- CARD 1: KARTU SAAT INI (FACE UP) -->
                        <div class="flex flex-col items-center flex-1 min-w-0 max-w-[138px] xs:max-w-[165px] sm:max-w-[220px]">
                            <span class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-300 mb-1 sm:mb-2 font-mono flex items-center gap-1 truncate drop-shadow">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0 animate-pulse"></span>
                                <span class="truncate">Kartu Kamu</span>
                            </span>

                            <div class="elvith-card-shell card-luxury-surface p-1.5 xs:p-2.5 sm:p-4 flex flex-col justify-between select-none text-{{ $currentCard['color'] === 'red' ? 'rose-600' : 'slate-900' }}">
                                <!-- Classic Pinstripe Frame -->
                                <div class="card-inner-frame"></div>

                                <!-- Top-Left Corner Index -->
                                <div class="relative z-10 flex flex-col items-start leading-none space-y-0.5">
                                    <span class="text-base xs:text-xl sm:text-3xl font-black font-serif-display drop-shadow-sm">{{ $currentCard['rank_label'] }}</span>
                                    <span class="text-xs xs:text-base sm:text-xl font-black leading-none">{!! $currentCard['symbol_html'] !!}</span>
                                </div>

                                <!-- Center Artwork Motif -->
                                <div class="relative z-10 flex flex-col items-center justify-center my-auto py-0.5 sm:py-1">
                                    @if($currentCard['rank'] === 14)
                                        <!-- Ornate Ace of Spades/Hearts Center -->
                                        <div class="relative flex flex-col items-center justify-center">
                                            <div class="w-9 h-9 xs:w-14 xs:h-14 sm:w-20 sm:h-20 rounded-full border-2 border-current/30 flex items-center justify-center bg-current/5 shadow-inner">
                                                <span class="text-xl xs:text-4xl sm:text-6xl font-serif font-black leading-none drop-shadow-md">
                                                    {!! $currentCard['symbol_html'] !!}
                                                </span>
                                            </div>
                                            <div class="mt-0.5 xs:mt-1 px-1 xs:px-2 py-0.5 rounded-full border border-current/40 text-[6px] xs:text-[7px] sm:text-[8px] font-black uppercase tracking-widest bg-current/10">
                                                ★ ACE (14) ★
                                            </div>
                                        </div>
                                    @elseif($currentCard['rank'] >= 11)
                                        <!-- Court Card Artwork Shield -->
                                        <div class="w-full py-1 xs:py-1.5 px-0.5 xs:px-1 flex flex-col items-center border border-current/30 bg-current/5 rounded-lg xs:rounded-xl shadow-inner">
                                            <div class="text-xl xs:text-3xl sm:text-5xl filter drop-shadow">
                                                @if($currentCard['rank'] === 13) 👑 @elseif($currentCard['rank'] === 12) 👸 @else 🗡️ @endif
                                            </div>
                                            <span class="text-[8px] xs:text-[9px] sm:text-xs font-black uppercase tracking-wider mt-0.5 truncate max-w-full">
                                                {{ $currentCard['title'] }}
                                            </span>
                                            <span class="text-[7px] xs:text-[8px] sm:text-[10px] font-mono opacity-80 font-bold">Nilai: {{ $currentCard['rank'] }}</span>
                                        </div>
                                    @else
                                        <!-- Number Card (2-10): Luxury Royal Seal -->
                                        <div class="relative flex flex-col items-center justify-center">
                                            <div class="w-8 h-8 xs:w-12 xs:h-12 sm:w-18 sm:h-18 rounded-full border border-current/25 flex items-center justify-center bg-current/5 shadow-inner">
                                                <span class="text-lg xs:text-3xl sm:text-5xl font-black leading-none drop-shadow">
                                                    {!! $currentCard['symbol_html'] !!}
                                                </span>
                                            </div>
                                            <div class="mt-0.5 xs:mt-1 flex items-center gap-1 px-1 xs:px-1.5 py-0.5 rounded-md bg-current/10 text-[7px] xs:text-[8px] sm:text-[9px] font-black font-mono">
                                                <span>{{ $currentCard['rank_label'] }}</span>
                                                <span>{!! $currentCard['symbol_html'] !!}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Bottom-Right Corner Index (Inverted) -->
                                <div class="relative z-10 flex flex-col items-end leading-none space-y-0.5 rotate-180">
                                    <span class="text-base xs:text-xl sm:text-3xl font-black font-serif-display drop-shadow-sm">{{ $currentCard['rank_label'] }}</span>
                                    <span class="text-xs xs:text-base sm:text-xl font-black leading-none">{!! $currentCard['symbol_html'] !!}</span>
                                </div>
                            </div>

                            <span class="text-[9px] xs:text-[10px] sm:text-xs font-mono text-amber-200 mt-1 sm:mt-2 font-bold truncate max-w-full text-center drop-shadow">
                                {{ $currentCard['title'] }}
                            </span>
                        </div>

                        <!-- VS / CHOICE INDICATOR (3D Golden Medallion) -->
                        <div class="flex flex-col items-center justify-center my-auto shrink-0 px-0.5 sm:px-1.5 z-10">
                            <div class="vs-medallion w-7 h-7 sm:w-11 sm:h-11 rounded-full flex items-center justify-center font-black text-[9px] sm:text-xs text-slate-950 font-serif tracking-wider shadow-md">
                                VS
                            </div>
                            @if($lastGuess)
                                <div class="mt-1 px-1.5 py-0.5 rounded-full text-[7px] sm:text-[9px] font-black uppercase font-mono tracking-wider shadow-md {{ $lastGuess === 'higher' ? 'bg-emerald-500 text-white border border-emerald-300' : 'bg-rose-500 text-white border border-rose-300' }}">
                                    {{ $lastGuess === 'higher' ? '▲ BESAR' : '▼ KECIL' }}
                                </div>
                            @endif
                        </div>

                        <!-- CARD 2: KARTU BERIKUTNYA DENGAN EFEK 3D FLIP ANIMATION -->
                        <div class="relative flex flex-col items-center flex-1 min-w-0 max-w-[138px] xs:max-w-[165px] sm:max-w-[220px]">
                            <!-- Floating X-Ray Peek Badge over Card 2 -->
                            <div 
                                x-show="isPeeking"
                                x-transition:enter="transition ease-out duration-200 transform"
                                x-transition:enter-start="opacity-0 scale-75 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-75 -translate-y-2"
                                class="absolute -top-4 z-40 px-2.5 py-0.5 rounded-full bg-gradient-to-r from-sky-400 via-cyan-300 to-sky-400 text-slate-950 font-black text-[9px] sm:text-[10px] uppercase tracking-wider shadow-lg shadow-cyan-400/60 pointer-events-none flex items-center gap-1.5 border border-cyan-100 animate-pulse"
                                x-cloak
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-700 animate-ping"></span>
                                <span>👁️ X-RAY SCANNER</span>
                            </div>

                            <span 
                                class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider mb-1 sm:mb-2 font-mono flex items-center gap-1 truncate drop-shadow transition-colors"
                                :class="isPeeking ? 'text-cyan-300' : 'text-amber-300'"
                            >
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="isPeeking ? 'bg-cyan-400 animate-ping' : 'bg-amber-400 animate-ping'"></span>
                                <span class="truncate" x-text="isPeeking ? 'Mengintip...' : 'Berikutnya'"></span>
                            </span>

                            <!-- 3D Card Container with Perspective -->
                            <div class="elvith-card-shell elvith-3d-scene">
                                <!-- Inner Flipper -->
                                <div 
                                    class="elvith-flipper"
                                    :class="{ 'flipped': isFlipped }"
                                >
                                    <!-- FRONT (CARD BACK / FACEDOWN MISTERI DENGAN HOLOGRAPHIC SHIMMER) -->
                                    <div class="elvith-card-face p-1.5 xs:p-2 sm:p-3 bg-white shadow-2xl border border-slate-300 overflow-hidden shimmer-effect">
                                        <div class="w-full h-full rounded-xl bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-950 border-2 sm:border-4 border-amber-400/80 relative overflow-hidden flex flex-col items-center justify-center text-amber-300 p-1.5 xs:p-2 text-center shadow-inner">
                                            <div class="absolute inset-0 opacity-25 bg-[radial-gradient(#fbbf24_2px,transparent_2px)] [background-size:12px_12px]"></div>
                                            <div class="w-8 h-8 xs:w-10 xs:h-10 sm:w-14 sm:h-14 rounded-full border-2 border-amber-400/70 flex items-center justify-center relative shadow-lg bg-amber-400/10">
                                                <span class="text-base xs:text-xl sm:text-2xl font-black animate-pulse">❓</span>
                                            </div>
                                            <span class="mt-1.5 sm:mt-2.5 text-[7px] xs:text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-amber-200 font-serif-display drop-shadow">
                                                MISTERI
                                            </span>
                                            <span class="text-[6px] xs:text-[7px] sm:text-[8px] text-amber-400/90 font-mono mt-0.5">Tebak Dulu!</span>
                                        </div>
                                    </div>

                                    <!-- BACK (REVEALED REAL CARD) [Rotated 180deg] -->
                                    @if($nextCard)
                                        <div 
                                            class="elvith-card-face elvith-card-back card-luxury-surface p-1.5 xs:p-2.5 sm:p-4 flex flex-col justify-between select-none text-{{ $nextCard['color'] === 'red' ? 'rose-600' : 'slate-900' }}"
                                            :class="{
                                                'ring-4 ring-cyan-400 shadow-[0_0_35px_rgba(6,182,212,0.9)]': isPeeking,
                                                'ring-4 ring-emerald-400 shadow-[0_0_35px_rgba(16,185,129,0.7)]': revealingState === 'won',
                                                'ring-4 ring-amber-400 shadow-[0_0_35px_rgba(251,191,36,0.7)]': revealingState === 'tie',
                                                'ring-4 ring-rose-500 shadow-[0_0_35px_rgba(244,63,94,0.7)]': revealingState === 'lost',
                                                'border-slate-300': !revealingState && !isPeeking
                                            }"
                                        >
                                            <!-- Classic Pinstripe Frame -->
                                            <div class="card-inner-frame"></div>

                                            <!-- X-Ray Futuristic Scan Beam & HUD Overlays when Peeking -->
                                            <template x-if="isPeeking">
                                                <div class="absolute inset-0 pointer-events-none z-30 overflow-hidden rounded-[inherit]">
                                                    <!-- Moving Laser Scanner Beam -->
                                                    <div class="xray-scan-beam"></div>
                                                    <!-- Cyber Holographic Grid -->
                                                    <div class="absolute inset-0 xray-grid-overlay opacity-60"></div>
                                                    <!-- 4 Corner HUD Targeting Brackets -->
                                                    <div class="absolute top-1 left-1 w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 border-t-2 border-l-2 border-cyan-400"></div>
                                                    <div class="absolute top-1 right-1 w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 border-t-2 border-r-2 border-cyan-400"></div>
                                                    <div class="absolute bottom-1 left-1 w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 border-b-2 border-l-2 border-cyan-400"></div>
                                                    <div class="absolute bottom-1 right-1 w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 border-b-2 border-r-2 border-cyan-400"></div>
                                                    <!-- Center Radar Pulse Reticle -->
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-full border border-cyan-400/40 animate-ping"></div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Top-Left Corner Index -->
                                            <div class="relative z-10 flex flex-col items-start leading-none space-y-0.5">
                                                <span class="text-base xs:text-xl sm:text-3xl font-black font-serif-display drop-shadow-sm">{{ $nextCard['rank_label'] }}</span>
                                                <span class="text-xs xs:text-base sm:text-xl font-black leading-none">{!! $nextCard['symbol_html'] !!}</span>
                                            </div>

                                            <!-- Center Artwork Motif -->
                                            <div class="relative z-10 flex flex-col items-center justify-center my-auto py-0.5 sm:py-1">
                                                @if($nextCard['rank'] === 14)
                                                    <div class="relative flex flex-col items-center justify-center">
                                                        <div class="w-9 h-9 xs:w-14 xs:h-14 sm:w-20 sm:h-20 rounded-full border-2 border-current/30 flex items-center justify-center bg-current/5 shadow-inner">
                                                            <span class="text-xl xs:text-4xl sm:text-6xl font-serif font-black leading-none drop-shadow-md">
                                                                {!! $nextCard['symbol_html'] !!}
                                                            </span>
                                                        </div>
                                                        <div class="mt-0.5 xs:mt-1 px-1 xs:px-2 py-0.5 rounded-full border border-current/40 text-[6px] xs:text-[7px] sm:text-[8px] font-black uppercase tracking-widest bg-current/10">
                                                            ★ ACE (14) ★
                                                        </div>
                                                    </div>
                                                @elseif($nextCard['rank'] >= 11)
                                                    <div class="w-full py-1 xs:py-1.5 px-0.5 xs:px-1 flex flex-col items-center border border-current/30 bg-current/5 rounded-lg xs:rounded-xl shadow-inner">
                                                        <div class="text-xl xs:text-3xl sm:text-5xl filter drop-shadow">
                                                            @if($nextCard['rank'] === 13) 👑 @elseif($nextCard['rank'] === 12) 👸 @else 🗡️ @endif
                                                        </div>
                                                        <span class="text-[8px] xs:text-[9px] sm:text-xs font-black uppercase tracking-wider mt-0.5 truncate max-w-full">
                                                            {{ $nextCard['title'] }}
                                                        </span>
                                                        <span class="text-[7px] xs:text-[8px] sm:text-[10px] font-mono opacity-80 font-bold">Nilai: {{ $nextCard['rank'] }}</span>
                                                    </div>
                                                @else
                                                    <div class="relative flex flex-col items-center justify-center">
                                                        <div class="w-8 h-8 xs:w-12 xs:h-12 sm:w-18 sm:h-18 rounded-full border border-current/25 flex items-center justify-center bg-current/5 shadow-inner">
                                                            <span class="text-lg xs:text-3xl sm:text-5xl font-black leading-none drop-shadow">
                                                                {!! $nextCard['symbol_html'] !!}
                                                            </span>
                                                        </div>
                                                        <div class="mt-0.5 xs:mt-1 flex items-center gap-1 px-1 xs:px-1.5 py-0.5 rounded-md bg-current/10 text-[7px] xs:text-[8px] sm:text-[9px] font-black font-mono">
                                                            <span>{{ $nextCard['rank_label'] }}</span>
                                                            <span>{!! $nextCard['symbol_html'] !!}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Bottom-Right Corner Index (Inverted) -->
                                            <div class="relative z-10 flex flex-col items-end leading-none space-y-0.5 rotate-180">
                                                <span class="text-base xs:text-xl sm:text-3xl font-black font-serif-display drop-shadow-sm">{{ $nextCard['rank_label'] }}</span>
                                                <span class="text-xs xs:text-base sm:text-xl font-black leading-none">{!! $nextCard['symbol_html'] !!}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <span 
                                class="text-[9px] xs:text-[10px] sm:text-xs font-mono mt-1 sm:mt-2 font-bold truncate max-w-full text-center drop-shadow transition-colors" 
                                :class="isPeeking ? 'text-cyan-300 font-black animate-pulse' : 'text-amber-200'"
                                x-text="isFlipped ? '{{ $nextCard['title'] ?? '' }}' : 'Tertutup 🔒'"
                            ></span>
                        </div>

                        <!-- Dramatic Floating Result Overlay in Front of Cards -->
                        <div 
                            x-show="revealingState"
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="opacity-0 scale-50 -translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200 transform"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-75 translate-y-2"
                            class="absolute inset-0 z-30 flex items-center justify-center pointer-events-none p-2"
                            x-cloak
                        >
                            <!-- 1. Shield Saved Notification with Epic Barrier Breakdown -->
                            <template x-if="revealingState === 'shield'">
                                <div class="relative flex flex-col items-center justify-center">
                                    <!-- Giant Hexagonal Forcefield Bubble in Background -->
                                    <div class="absolute -inset-10 sm:-inset-14 flex items-center justify-center pointer-events-none">
                                        <div class="w-48 h-48 sm:w-64 sm:h-64 rounded-full border-4 border-cyan-400/60 bg-cyan-500/10 backdrop-blur-sm animate-ping"></div>
                                    </div>

                                    <!-- Dramatic Shield Shatter Modal -->
                                    <div class="relative bg-gradient-to-b from-slate-950/95 via-sky-950/95 to-slate-950/95 border-2 border-cyan-400 rounded-2xl px-5 py-3.5 sm:px-7 sm:py-4 shadow-[0_0_50px_rgba(6,182,212,0.95)] flex flex-col items-center text-center max-w-[270px] xs:max-w-xs sm:max-w-sm backdrop-blur-md">
                                        
                                        <!-- Cracked Shield Animation Icon -->
                                        <div class="relative w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center mb-1">
                                            <span class="text-3xl sm:text-5xl filter drop-shadow">🛡️</span>
                                            <!-- Electric Shock Crackles -->
                                            <div class="absolute -top-1 -right-1 text-sm sm:text-base animate-electric">⚡</div>
                                            <div class="absolute -bottom-1 -left-1 text-sm sm:text-base animate-electric">💥</div>
                                        </div>

                                        <span class="text-xs sm:text-sm font-black uppercase tracking-widest text-cyan-300 drop-shadow flex items-center gap-1">
                                            <span>⚡</span>
                                            <span>PERISAI MENYERAP SERANGAN!</span>
                                            <span>⚡</span>
                                        </span>

                                        <span class="text-[10px] sm:text-xs text-white font-bold leading-tight mt-1">
                                            Tebakan Meleset Tapi Kamu Selamat!
                                        </span>

                                        <div class="mt-2 flex items-center gap-1.5 sm:gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/25 text-emerald-300 text-[9px] sm:text-[10px] font-black font-mono border border-emerald-400/40 shadow-sm">
                                                ✨ Streak Aman ({{ $streak }}x)
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-200 text-[8px] sm:text-[9px] font-mono border border-cyan-400/40">
                                                Defended 100%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- 2. Won Notification -->
                            <template x-if="revealingState === 'won'">
                                <div class="bg-gradient-to-b from-slate-950/95 via-emerald-950/95 to-slate-950/95 border-2 border-emerald-400 rounded-2xl px-5 py-2.5 sm:px-7 sm:py-3.5 shadow-[0_0_35px_rgba(16,185,129,0.85)] flex flex-col items-center text-center max-w-[240px] xs:max-w-xs sm:max-w-sm backdrop-blur-md">
                                    <span class="text-2xl sm:text-3xl filter drop-shadow">✨</span>
                                    <span class="mt-0.5 text-xs sm:text-sm font-black uppercase tracking-widest text-emerald-300 drop-shadow">
                                        TEBAKAN TEPAT!
                                    </span>
                                    <span class="text-[10px] sm:text-xs text-emerald-100 font-mono mt-0.5">
                                        Combo Streak Lanjut!
                                    </span>
                                </div>
                            </template>

                            <!-- 3. Tie Notification -->
                            <template x-if="revealingState === 'tie'">
                                <div class="bg-gradient-to-b from-slate-950/95 via-amber-950/95 to-slate-950/95 border-2 border-amber-400 rounded-2xl px-5 py-2.5 sm:px-7 sm:py-3.5 shadow-[0_0_35px_rgba(251,191,36,0.85)] flex flex-col items-center text-center max-w-[240px] xs:max-w-xs sm:max-w-sm backdrop-blur-md">
                                    <span class="text-2xl sm:text-3xl filter drop-shadow">🤝</span>
                                    <span class="mt-0.5 text-xs sm:text-sm font-black uppercase tracking-widest text-amber-300 drop-shadow">
                                        HASIL SERI!
                                    </span>
                                    <span class="text-[10px] sm:text-xs text-amber-100 font-mono mt-0.5">
                                        Kartu Kembar • Streak Aman
                                    </span>
                                </div>
                            </template>

                            <!-- 4. Lost Notification -->
                            <template x-if="revealingState === 'lost'">
                                <div class="bg-gradient-to-b from-slate-950/95 via-rose-950/95 to-slate-950/95 border-2 border-rose-500 rounded-2xl px-5 py-2.5 sm:px-7 sm:py-3.5 shadow-[0_0_35px_rgba(244,63,94,0.85)] flex flex-col items-center text-center max-w-[240px] xs:max-w-xs sm:max-w-sm backdrop-blur-md">
                                    <span class="text-2xl sm:text-3xl filter drop-shadow">💀</span>
                                    <span class="mt-0.5 text-xs sm:text-sm font-black uppercase tracking-widest text-rose-300 drop-shadow">
                                        TEBAKAN SALAH!
                                    </span>
                                    <span class="text-[10px] sm:text-xs text-rose-200/90 font-mono mt-0.5">
                                        Permainan Berakhir
                                    </span>
                                </div>
                            </template>
                        </div>

                    </div>
                @endif

            </div>

            <!-- Bottom Controller Action Bar: POWER-UPS & 3D ARCADE BUTTONS -->
            <div class="w-full z-10 pt-2.5 sm:pt-4 border-t border-emerald-800/40 space-y-2 sm:space-y-3">
                @if($gameState === 'playing' || $gameState === 'revealing')
                    
                    <!-- 3 Power-Up Dock (1x Pakai per Permainan) -->
                    <div class="flex items-center justify-center gap-1.5 xs:gap-2 max-w-lg mx-auto w-full px-0.5">
                        
                        <!-- 1. Power-Up: Intip Kartu (X-Ray Peek) -->
                        <button 
                            type="button" 
                            wire:click="usePowerUpPeek" 
                            :disabled="isFlipped || isPeeking || {{ $powerUpPeekUsed ? 'true' : 'false' }}"
                            class="flex-1 py-1.5 px-2 rounded-xl text-[10px] sm:text-xs font-bold transition flex items-center justify-center gap-1 border select-none touch-manipulation {{ $powerUpPeekUsed ? 'bg-slate-900/60 text-slate-500 border-slate-800 cursor-not-allowed opacity-50' : 'bg-gradient-to-r from-sky-600 via-cyan-500 to-sky-600 hover:from-sky-500 hover:to-cyan-400 active:scale-95 text-white border-cyan-300/40 shadow-md shadow-cyan-500/25 cursor-pointer hover:shadow-cyan-400/40' }}"
                            title="Intip nilai kartu berikutnya sebelum menebak (1x)"
                        >
                            <span class="text-xs sm:text-sm {{ !$powerUpPeekUsed ? 'animate-pulse' : '' }}">👁️</span>
                            <span class="truncate">{{ $powerUpPeekUsed ? 'Intip (Habis)' : 'Intip (X-Ray)' }}</span>
                        </button>

                        <!-- 2. Power-Up: Perisai Nyawa (Shield Status Indicator) -->
                        <div 
                            class="flex-1 py-1.5 px-2 rounded-xl text-[10px] sm:text-xs font-bold flex items-center justify-center gap-1 border select-none transition-all {{ $powerUpShieldActive ? 'bg-gradient-to-r from-emerald-950 via-teal-900 to-emerald-950 text-emerald-300 border-emerald-400/60 shadow-lg shadow-emerald-500/30 ring-1 ring-emerald-400/50' : 'bg-slate-900/60 text-slate-500 border-slate-800 opacity-50' }}"
                            title="Melindungi 1x dari Game Over jika salah menebak (Otomatis)"
                        >
                            <span class="text-xs sm:text-sm {{ $powerUpShieldActive ? 'animate-bounce' : '' }}">🛡️</span>
                            <span class="truncate">{{ $powerUpShieldActive ? 'Perisai Aktif' : 'Perisai Pecah' }}</span>
                        </div>

                        <!-- 3. Power-Up: Tukar Kartu (Swap Deck) -->
                        <button 
                            type="button" 
                            wire:click="usePowerUpSwap" 
                            :disabled="isFlipped || isPeeking || {{ $powerUpSwapUsed ? 'true' : 'false' }}"
                            class="flex-1 py-1.5 px-2 rounded-xl text-[10px] sm:text-xs font-black transition flex items-center justify-center gap-1 border select-none touch-manipulation {{ $powerUpSwapUsed ? 'bg-slate-900/60 text-slate-500 border-slate-800 cursor-not-allowed opacity-50' : 'bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 active:scale-95 text-slate-950 border-amber-300/60 shadow-md shadow-amber-500/25 cursor-pointer' }}"
                            title="Tukar kartu saat ini jika posisinya nanggung (1x)"
                        >
                            <span class="text-xs sm:text-sm">🔄</span>
                            <span class="truncate">{{ $powerUpSwapUsed ? 'Tukar (Habis)' : 'Tukar Kartu' }}</span>
                        </button>

                    </div>

                    <div class="flex flex-row items-center justify-center gap-2.5 sm:gap-4 max-w-xl mx-auto w-full px-0.5 sm:px-0">
                        
                        <!-- Button LEBIH BESAR (3D Arcade Green) -->
                        <button 
                            type="button" 
                            wire:click="guess('higher')" 
                            :disabled="isFlipped || isPeeking"
                            class="btn-3d-green flex-1 w-full py-3.5 sm:py-4 px-3 sm:px-6 rounded-xl sm:rounded-2xl text-white font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center sm:justify-between gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none touch-manipulation"
                        >
                            <span class="flex items-center gap-1.5 sm:gap-2">
                                <span class="text-lg sm:text-2xl drop-shadow">▲</span>
                                <span class="drop-shadow">LEBIH BESAR</span>
                            </span>
                            <span class="hidden sm:inline px-2 py-0.5 bg-black/20 rounded-md text-[10px] font-mono">W / ↑</span>
                        </button>

                        <!-- Button LEBIH KECIL (3D Arcade Red) -->
                        <button 
                            type="button" 
                            wire:click="guess('lower')" 
                            :disabled="isFlipped || isPeeking"
                            class="btn-3d-red flex-1 w-full py-3.5 sm:py-4 px-3 sm:px-6 rounded-xl sm:rounded-2xl text-white font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center sm:justify-between gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none touch-manipulation"
                        >
                            <span class="flex items-center gap-1.5 sm:gap-2">
                                <span class="text-lg sm:text-2xl drop-shadow">▼</span>
                                <span class="drop-shadow">LEBIH KECIL</span>
                            </span>
                            <span class="hidden sm:inline px-2 py-0.5 bg-black/20 rounded-md text-[10px] font-mono">S / ↓</span>
                        </button>

                    </div>
                @elseif($gameState === 'game_over')
                    <!-- Game Over Actions (3D Gold Arcade Button) -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-4 max-w-md mx-auto w-full px-1">
                        <button 
                            type="button" 
                            wire:click="startGame"
                            class="btn-3d-gold w-full py-3.5 px-6 rounded-xl sm:rounded-2xl text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer touch-manipulation animate-pulse"
                        >
                            <span>🔄 MAIN LAGI SEKARANG</span>
                            <span class="hidden sm:inline px-2 py-0.5 bg-black/15 rounded-md text-[10px] font-mono">ENTER</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="resetGame"
                            class="w-full sm:w-auto py-2.5 sm:py-3.5 px-5 rounded-xl sm:rounded-2xl bg-slate-900/90 hover:bg-slate-800 text-slate-300 font-bold text-xs uppercase tracking-wider border border-slate-700 transition cursor-pointer touch-manipulation"
                        >
                            Beranda Game
                        </button>
                    </div>
                @else
                    <div class="text-center text-[11px] sm:text-xs text-slate-300">
                        Tekan tombol <strong class="text-amber-300">Mulai Permainan</strong> di atas untuk bertarung di leaderboard!
                    </div>
                @endif
            </div>

        </div>

        <!-- LEADERBOARD PANEL (4 Cols) -->
        <div id="leaderboard-section" class="xl:col-span-4 flex flex-col space-y-4">
            
            <!-- User Status Card -->
            @if(Auth::check())
                <div class="bg-slate-950/85 backdrop-blur-md rounded-2xl p-3.5 sm:p-4 border border-amber-500/25 shadow-lg relative overflow-hidden">
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
                        <div class="bg-slate-900/70 p-1.5 sm:p-2 rounded-xl border border-slate-800">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">High Score</span>
                            <span class="text-xs sm:text-sm font-black text-emerald-400">{{ number_format($userStats['high_score'] ?? 0) }}</span>
                        </div>
                        <div class="bg-slate-900/70 p-1.5 sm:p-2 rounded-xl border border-slate-800">
                            <span class="text-[8px] sm:text-[9px] uppercase text-slate-400 block font-sans">Max Streak</span>
                            <span class="text-xs sm:text-sm font-black text-amber-400">{{ $userStats['max_streak'] ?? 0 }}x</span>
                        </div>
                        <div class="bg-slate-900/70 p-1.5 sm:p-2 rounded-xl border border-slate-800">
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
