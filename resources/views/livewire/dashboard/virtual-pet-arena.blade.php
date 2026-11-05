<div x-data="{
    showFoodDrawer: false,
    petJumping: false,
    petEating: false,
    petDrinking: false,
    petSweeping: false,
    eatingFoodItem: null,
    drinkingAura: false,
    sweepingSparkles: [],
    dustPuffs: [],
    petBathing: false,
    petDancing: false,
    petFlipping: false,
    lampSwinging: false,
    isMusicPlaying: false,
    musicInterval: null,
    radioTracks: [
        {
            title: 'Everything You Are',
            artist: 'Hindia',
            file: '/audio/pet-radio/hindia-everything-you-are.mp3',
            altFiles: [
                'audio/pet-radio/hindia-everything-you-are.mp3',
                '/audio/pet-radio/everything-you-are.mp3'
            ],
            lyrics: [
                { time: 0, text: '🎶 (Petikan gitar akustik mulai mengalun...) 🎸' },
                { time: 13, text: 'Wajahmu kuingat selalu... ✨' },
                { time: 17, text: 'Lupakan hal-hal yang menggangguku 🍃' },
                { time: 21, text: 'Karena hari ini mata kita beradu 🤝' },
                { time: 26, text: 'Kita saling bantu 💖' },
                { time: 30, text: 'Melepas perasaan tinggi ke angkasa 🚀' },
                { time: 35, text: 'Menantang dunia! 🌍' },
                { time: 37, text: 'Merayakan muda~ 🎉' },
                { time: 40, text: '\'Tuk satu jam saja ⏳' },
                { time: 43, text: 'Kita hampir mati dan kau selamatkan aku 🥺' },
                { time: 49, text: 'Dan ku menyelamatkanmu 🤝' },
                { time: 52, text: 'Dan sekarang aku tahu... 💡' },
                { time: 55, text: 'Cerita kita tak jauh berbeda 📖' },
                { time: 60, text: 'Got beat down by the world 🥊' },
                { time: 63, text: 'Sometimes I wanna fold 💔' },
                { time: 66, text: 'Namun suratmu \'kan kuceritakan ke anak-anakku nanti 💌' },
                { time: 73, text: 'Bahwa aku pernah dicintai... 💖' },
                { time: 77, text: 'With everything you are~ 🌟' },
                { time: 81, text: 'Fully as I am! ✨' },
                { time: 85, text: 'With everything you are... 🎶' },
                { time: 96, text: 'Wajahmu yang beragam rupa 👥' },
                { time: 100, text: 'Pastikan ku tak sendirian 🌸' },
                { time: 104, text: 'Jalani derita, kau bawakan kisahmu 🗣️' },
                { time: 109, text: 'Aku mendengarkan 👂' },
                { time: 112, text: 'Oh, kita bergantian... 💫' },
                { time: 115, text: 'Bertukar nestapa 🌧️' },
                { time: 118, text: 'Menawar trauma 🩹' },
                { time: 121, text: 'Datang seadanya 🏡' },
                { time: 123, text: 'Terasku terbuka 🚪' },
                { time: 126, text: 'Kita hampir mati dan kau selamatkan aku 🤝' },
                { time: 132, text: 'Dan ku menyelamatkanmu 💖' },
                { time: 135, text: 'Dan sekarang aku tahu... 💡' },
                { time: 138, text: 'Cerita kita tak jauh berbeda 📖' },
                { time: 144, text: 'Got beat down by the world 🥊' },
                { time: 146, text: 'Sometimes I wanna fold 💔' },
                { time: 149, text: 'Namun suratmu \'kan kuceritakan ke anak-anakku nanti 💌' },
                { time: 157, text: 'Bahwa aku pernah dicintai... 💖' },
                { time: 161, text: 'Seada-adanya 🌿' },
                { time: 164, text: 'Sekurang-kurangnya 🍂' },
                { time: 168, text: 'Walau sulit utarakan hatiku dengan indah 🥀' },
                { time: 173, text: 'Walau jarang ku bernyanyi dengan cara yang indah 🎤' },
                { time: 178, text: 'Tapi tak sekali pun kisahku pernah kau bantah, oh 🥺' },
                { time: 186, text: 'Cerita kita tak jauh berbeda 📖' },
                { time: 191, text: 'Got beat down by the world 🥊' },
                { time: 194, text: 'Sometimes I wanna fold 💔' },
                { time: 197, text: 'Namun suratmu \'kan kuceritakan ke anak-anakku nanti 💌' },
                { time: 204, text: 'Bahwa aku pernah dicintai... 💖' },
                { time: 208, text: 'With everything you are~ 🌟' },
                { time: 213, text: 'Fully as I am! ✨' },
                { time: 217, text: 'With everything you are... 💖' },
                { time: 228, text: '🎶 (Melodi lembut penutup...) 🕊️' }
            ]
        },
        {
            title: 'Evaluasi',
            artist: 'Hindia',
            file: '/audio/pet-radio/hindia-evaluasi.mp3',
            altFiles: [
                'audio/pet-radio/hindia-evaluasi.mp3',
                '/audio/pet-radio/evaluasi.mp3'
            ],
            lyrics: [
                { time: 0, text: 'Yang tak bisa terobati... 🥀' },
                { time: 8, text: 'Biarlah mengering sendiri 🍂' },
                { time: 16, text: 'Menghias tubuh dan... 🍃' },
                { time: 18, text: 'Yang mengevaluasi ragamu... 🪞' },
                { time: 30, text: 'Hanya kau sendiri! 👤' },
                { time: 34, text: 'Mereka tak mampu 🚫' },
                { time: 37, text: 'Melewati yang telah kau lewati 🚶‍♂️' },
                { time: 43, text: 'Tiap berganti hari 🌅' },
                { time: 47, text: 'Rintangan yang kau hadapi 🧗‍♂️' },
                { time: 52, text: 'Masalah yang mengeruh... 🌫️' },
                { time: 56, text: 'Perasaan yang rapuh 💔' },
                { time: 60, text: 'Ini belum separuhnya! ⏳' },
                { time: 64, text: 'Biasa saja... 🍃' },
                { time: 68, text: 'Kamu tak apa! 🤍' },
                { time: 73, text: 'Yang selalu ingin ambil peran 🎭' },
                { time: 77, text: 'Hanya berlomba menjadi lebih sedih dari dirimu 🌧️' },
                { time: 85, text: 'Muak dikesampingkan, disamakan 🤦‍♂️' },
                { time: 91, text: 'Hatimu terluka, sempurna... 🥀' },
                { time: 96, text: 'Masalah yang mengeruh 🌫️' },
                { time: 100, text: 'Perasaan yang rapuh 💔' },
                { time: 104, text: 'Ini belum separuhnya! ⏳' },
                { time: 108, text: 'Biasa saja... 🍃' },
                { time: 112, text: 'Kamu tak apa! 🤍' },
                { time: 116, text: 'Perjalanan yang jauh 🛣️' },
                { time: 120, text: 'Kau bangun untuk bertaruh 🎲' },
                { time: 124, text: 'Hari belum selesai! ☀️' },
                { time: 128, text: 'Biasa saja, kamu tak apa~ ✨' },
                { time: 137, text: 'Bilas muka, gosok gigi, evaluasi! 🪥💧' },
                { time: 141, text: 'Tidur sejenak menemui esok pagi 🛌🌙' },
                { time: 145, text: 'Walau pedih ku bersamamu kali ini 🤝' },
                { time: 149, text: 'Ku masih ingin melihatmu esok hari! 🌅' },
                { time: 153, text: 'Bilas muka, gosok gigi, evaluasi! 🪥💧' },
                { time: 157, text: 'Tidur sejenak menemui esok pagi 🛌🌙' },
                { time: 161, text: 'Walau pedih ku bersamamu kali ini 🤝' },
                { time: 165, text: 'Ku masih ingin melihatmu esok hari! 🌅' },
                { time: 169, text: 'Bilas muka, gosok gigi, evaluasi! 🪥💧' },
                { time: 173, text: 'Tidur sejenak menemui esok pagi 🛌🌙' },
                { time: 177, text: 'Walau pedih ku bersamamu kali ini 🤝' },
                { time: 181, text: 'Ku masih ingin melihatmu esok hari... 💖' },
                { time: 188, text: '🎶 (Alunan synth penutup mengalir damai...) 🕊️' }
            ]
        },
        {
            title: 'Karir Ini Tak Ada Artinya',
            artist: 'Hindia',
            file: '/audio/pet-radio/hindia-karir-ini-tak-ada-artinya.mp3',
            altFiles: [
                'audio/pet-radio/hindia-karir-ini-tak-ada-artinya.mp3',
                '/audio/pet-radio/hindia-berdansalah-karir-ini-tak-ada-artinya.mp3',
                '/audio/pet-radio/hindia-karir-ini-ga-ada-artinya.mp3',
                '/audio/pet-radio/karir-ini-tak-ada-artinya.mp3'
            ],
            lyrics: [
                { time: 0, text: '🎶 (Beat drum pembuka Hindia...) 🥁' },
                { time: 19, text: 'Besok coba kau izin pulang cepat! 🏃‍♂️💨' },
                { time: 23, text: 'Bilang saja siapa sedang sakit... 🤫' },
                { time: 27, text: 'Lusa coba izin terlambat~ ⏰' },
                { time: 30, text: 'Bilang saja kau celaka sedikit 🤭' },
                { time: 34, text: 'Habiskan cutimu sebelum resign! 🌴' },
                { time: 38, text: 'I know you\'re scared, but it\'s totally fine! 💖' },
                { time: 42, text: 'Karena kau bukan mesin assembly line! 🤖❌' },
                { time: 46, text: 'Lihat promo liburan, babe, that\'s a sign! ✈️' },
                { time: 50, text: 'Oh, karena ku tahu... 💡' },
                { time: 53, text: 'Kalimat itu ada di ujung mulutmu! 🗣️' },
                { time: 58, text: 'Oh, karena kau tahu... 💭' },
                { time: 61, text: 'Tak mau lakukan itu seumur hidupmu! 🚫' },
                { time: 65, text: 'Lakukan apa yang kau mau sekarang! 🔥' },
                { time: 69, text: 'Saat hatimu bergerak, jangan kau larang! 💃' },
                { time: 73, text: 'Hidup ini tak ada artinya, maka... 🤷‍♂️' },
                { time: 77, text: 'Kau bebas mengarang maknanya seorang! 🎨' },
                { time: 80, text: 'Lakukan apa yang kau mau sekarang! 🔥' },
                { time: 84, text: 'Saat hatimu bergerak, jangan kau larang! 💃' },
                { time: 88, text: 'Lagipula hidup akan berakhir, maka... ⏳' },
                { time: 92, text: 'Lakukan apa yang kau mau sekarang! 🚀' },
                { time: 96, text: 'Berdansalah, kau raja dunia! 👑🕺' },
                { time: 100, text: 'Karir ini tak ada artinya! 🎸✨' },
                { time: 103, text: 'Berdansalah, kau ratu dunia! 💃👑' },
                { time: 107, text: 'Hidup ini tak ada artinya! 🥳🎉' },
                { time: 111, text: 'Besok coba kau berbohong sedikit 🤫' },
                { time: 115, text: 'Lalu kau melupa saat diungkit 🙈' },
                { time: 119, text: 'Bisa juga kau jujur saja 😇' },
                { time: 123, text: 'Separahnya kau akan makin lega! 🍃' },
                { time: 127, text: 'Oh, karena ku tahu... 💡' },
                { time: 130, text: 'Kalimat itu ada di ujung mulutmu! 🗣️' },
                { time: 134, text: 'Oh, karena kau tahu... 💭' },
                { time: 138, text: 'Utamakanlah dirimu di sisa hidupmu! 💖' },
                { time: 142, text: 'Lakukan apa yang kau mau sekarang! 🔥' },
                { time: 146, text: 'Saat hatimu bergerak, jangan kau larang! 💃' },
                { time: 149, text: 'Hidup ini tak ada artinya, maka... 🤷‍♂️' },
                { time: 153, text: 'Kau bebas mengarang maknanya seorang! 🎨' },
                { time: 157, text: 'Lakukan apa yang kau mau sekarang! 🔥' },
                { time: 161, text: 'Saat hatimu bergerak, jangan kau larang! 💃' },
                { time: 165, text: 'Lagipula hidup akan berakhir, maka... ⏳' },
                { time: 169, text: 'Lakukan apa yang kau mau sekarang! 🚀' },
                { time: 172, text: 'Lakukan apa yang kau mau sekarang! 🔥' },
                { time: 176, text: 'Saat hatimu bergerak, jangan kau larang! 💃' },
                { time: 180, text: 'Hidup ini tak ada artinya, maka... 🤷‍♂️' },
                { time: 184, text: 'Kau bebas mengarang maknanya seorang! 🎨' },
                { time: 188, text: 'Lakukan apa yang kau mau sekarang! 🔥' },
                { time: 192, text: 'Saat hatimu bergerak, jangan kau larang! 💃' },
                { time: 196, text: 'Lagipula hidup akan berakhir, maka... ⏳' },
                { time: 200, text: 'Lakukan apa yang kau mau sekarang! 🚀' },
                { time: 203, text: 'Berdansalah, kau raja dunia! 👑🕺' },
                { time: 207, text: 'Karir ini tak ada artinya! 🎸✨' },
                { time: 211, text: 'Berdansalah, kau ratu dunia! 💃👑' },
                { time: 215, text: 'Hidup ini tak ada artinya! 🥳🎉' },
                { time: 220, text: '🎶 (Outro solo musik & tepuk tangan...) 👏' }
            ]
        },
    ],
    currentTrackIndex: 0,
    showRadioDeck: false,
    currentLyricLine: '',
    petSingingMouthOpen: false,
    radioAudio: null,
    isSleeping: {{ ($pet && $pet->is_sleeping) ? 'true' : 'false' }},
    
    // Physics Bouncy Rubber Ball Playground
    ballEnabled: false,
    ball: {
        x: 78,
        y: 74,
        vx: -1.5,
        vy: -3.5,
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        combo: 0,
    },
    ballAnimId: null,

    // Pointer Tracking & Rub/Tickle Mechanics
    eyeX: 0,
    eyeY: 0,
    isRubbing: false,
    rubCount: 0,
    lastRubTime: 0,
    petTickleCount: 0,
    petWiggleAngle: 0,

    // Particles & Visual FX
    floatingHearts: [],
    bubbles: [],
    crumbs: [],
    floatingTexts: [],
    flyingNotes: [],
    flyingFood: null,
    plantBlooming: false,
    windowEvent: null,

    soundEnabled: localStorage.getItem('elf_pet_sound') !== 'false',
    
    init() {
        this.startBallPhysics();
    },

    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        localStorage.setItem('elf_pet_sound', this.soundEnabled);
    },

    // Universal Floating FX Badge Spawner
    spawnFloatingText(text, color = '#fbbf24', icon = '✨') {
        const id = Date.now() + Math.random();
        this.floatingTexts.push({
            id,
            text,
            color,
            icon,
            left: 50 + (Math.random() * 20 - 10),
            top: 42 + (Math.random() * 12 - 6),
        });
        setTimeout(() => {
            this.floatingTexts = this.floatingTexts.filter(t => t.id !== id);
        }, 1250);
    },

    // Eye tracking: pupils follow mouse/touch movement around the stage!
    handleMouseMove(e) {
        const stage = this.$refs.stageElement;
        if (!stage) return;
        const rect = stage.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        if (!clientX || !clientY) return;

        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height * 0.46;
        const dx = clientX - centerX;
        const dy = clientY - centerY;
        const dist = Math.hypot(dx, dy);
        const maxOffset = 5.5; // pixel offset constraint

        if (dist > 0) {
            this.eyeX = (dx / dist) * Math.min(dist / 22, maxOffset);
            this.eyeY = (dy / dist) * Math.min(dist / 22, maxOffset);
        }
    },

    // Synthetic Web Audio Synthesizer (Zero External Asset Dependency)
    // Synthetic Web Audio Synthesizer (Zero External Asset Dependency)
    playSound(type) {
        if (!this.soundEnabled) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            let maxDuration = 0.3;

            if (type === 'pat') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(450 + (this.petTickleCount * 40), now);
                osc.frequency.exponentialRampToValueAtTime(850 + (this.petTickleCount * 50), now + 0.12);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.14);
                gain.gain.setValueAtTime(0, now + 0.15);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.15);
                maxDuration = 0.25;
            } else if (type === 'giggle') {
                for (let i = 0; i < 3; i++) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(600 + (i * 120), now + (i * 0.07));
                    gain.gain.setValueAtTime(0.2, now + (i * 0.07));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (i * 0.07) + 0.05);
                    gain.gain.setValueAtTime(0, now + (i * 0.07) + 0.06);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.07));
                    osc.stop(now + (i * 0.07) + 0.06);
                }
                maxDuration = 0.35;
            } else if (type === 'eat') {
                for (let i = 0; i < 3; i++) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(280 + (i * 70), now + (i * 0.08));
                    osc.frequency.exponentialRampToValueAtTime(140, now + (i * 0.08) + 0.05);
                    gain.gain.setValueAtTime(0.3, now + (i * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (i * 0.08) + 0.05);
                    gain.gain.setValueAtTime(0, now + (i * 0.08) + 0.06);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.08));
                    osc.stop(now + (i * 0.08) + 0.06);
                }
                maxDuration = 0.35;
            } else if (type === 'bubble') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(450, now);
                osc.frequency.exponentialRampToValueAtTime(900, now + 0.09);
                gain.gain.setValueAtTime(0.22, now);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.1);
                gain.gain.setValueAtTime(0, now + 0.11);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.11);
                maxDuration = 0.2;
            } else if (type === 'coffee') {
                [260, 340, 520, 680].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.1));
                    gain.gain.setValueAtTime(0.22, now + (idx * 0.1));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (idx * 0.1) + 0.09);
                    gain.gain.setValueAtTime(0, now + (idx * 0.1) + 0.1);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (idx * 0.1));
                    osc.stop(now + (idx * 0.1) + 0.1);
                });
                maxDuration = 0.55;
            } else if (type === 'sweep') {
                for (let i = 0; i < 3; i++) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(160 + (i * 30), now + (i * 0.12));
                    osc.frequency.exponentialRampToValueAtTime(50, now + (i * 0.12) + 0.09);
                    gain.gain.setValueAtTime(0.22, now + (i * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (i * 0.12) + 0.09);
                    gain.gain.setValueAtTime(0, now + (i * 0.12) + 0.1);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.12));
                    osc.stop(now + (i * 0.12) + 0.1);
                }
                maxDuration = 0.5;
            } else if (type === 'sparkle') {
                [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.06));
                    gain.gain.setValueAtTime(0.15, now + (idx * 0.06));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (idx * 0.06) + 0.15);
                    gain.gain.setValueAtTime(0, now + (idx * 0.06) + 0.16);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (idx * 0.06));
                    osc.stop(now + (idx * 0.06) + 0.16);
                });
                maxDuration = 0.45;
            } else if (type === 'bounce') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(340, now);
                osc.frequency.exponentialRampToValueAtTime(110, now + 0.1);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.1);
                gain.gain.setValueAtTime(0, now + 0.11);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.11);
                maxDuration = 0.2;
            } else if (type === 'kick') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(150, now);
                osc.frequency.exponentialRampToValueAtTime(55, now + 0.12);
                gain.gain.setValueAtTime(0.35, now);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.12);
                gain.gain.setValueAtTime(0, now + 0.13);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.13);
                maxDuration = 0.25;
            } else if (type === 'cheer') {
                [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.07));
                    gain.gain.setValueAtTime(0.18, now + (idx * 0.07));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (idx * 0.07) + 0.18);
                    gain.gain.setValueAtTime(0, now + (idx * 0.07) + 0.2);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (idx * 0.07));
                    osc.stop(now + (idx * 0.07) + 0.2);
                });
                maxDuration = 0.55;
            } else if (type === 'switch') {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'square';
                osc.frequency.setValueAtTime(180, now);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.04);
                gain.gain.setValueAtTime(0, now + 0.05);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.05);
                maxDuration = 0.15;
            } else if (type === 'coin') {
                [987, 1318].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.08));
                    gain.gain.setValueAtTime(0.22, now + (idx * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (idx * 0.08) + 0.16);
                    gain.gain.setValueAtTime(0, now + (idx * 0.08) + 0.18);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (idx * 0.08));
                    osc.stop(now + (idx * 0.08) + 0.18);
                });
                maxDuration = 0.45;
            }

            // Cleanly close AudioContext after sound finishes to eliminate any audio thread drone / ringing
            setTimeout(() => {
                try { ctx.close(); } catch(e) {}
            }, (maxDuration + 0.1) * 1000);
        } catch (e) {}
    },

    // Bouncy Ball Physics Engine
    startBallPhysics() {
        const loop = () => {
            if (this.ballEnabled && !this.ball.isDragging && !this.isSleeping) {
                this.ball.vy += 0.35; // Gravity
                this.ball.x += this.ball.vx;
                this.ball.y += this.ball.vy;
                this.ball.vx *= 0.992; // Air drag

                // Floor collision (74%)
                if (this.ball.y >= 74) {
                    this.ball.y = 74;
                    if (Math.abs(this.ball.vy) > 1.2) {
                        this.ball.vy = -this.ball.vy * 0.72;
                        this.playSound('bounce');
                    } else {
                        this.ball.vy = 0;
                        this.ball.vx *= 0.92;
                    }
                }

                // Left & Right walls (8% - 92%)
                if (this.ball.x <= 8) {
                    this.ball.x = 8;
                    this.ball.vx = Math.abs(this.ball.vx) * 0.8;
                    this.playSound('bounce');
                } else if (this.ball.x >= 92) {
                    this.ball.x = 92;
                    this.ball.vx = -Math.abs(this.ball.vx) * 0.8;
                    this.playSound('bounce');
                }

                // Ceiling (14%)
                if (this.ball.y <= 14) {
                    this.ball.y = 14;
                    this.ball.vy = Math.abs(this.ball.vy) * 0.7;
                    this.playSound('bounce');
                }

                // Collision with Pet mascot (Center around x: 50%, y: 55%) - ONLY when awake!
                const dx = Math.abs(this.ball.x - 50);
                const dy = Math.abs(this.ball.y - 56);
                if (!this.isSleeping && dx < 12 && dy < 14) {
                    this.petJumping = true;
                    setTimeout(() => this.petJumping = false, 400);
                    this.playSound('kick');
                    
                    this.ball.vy = -(6.5 + Math.random() * 3.5);
                    this.ball.vx = (this.ball.x - 50) * 0.5 + (Math.random() * 3 - 1.5);
                    this.ball.combo++;
                    this.spawnFloatingText(`SUNDUL! x${this.ball.combo} ⚽`, '#38bdf8', '⚽');

                    if (this.ball.combo % 3 === 0) {
                        this.playSound('coin');
                        $wire.playBall();
                        this.spawnFloatingText('+3 Koin & Bahagia! 🌟', '#fbbf24', '🪙');
                    }
                }
            }
            this.ballAnimId = requestAnimationFrame(loop);
        };
        this.ballAnimId = requestAnimationFrame(loop);
    },

    startBallGame() {
        if (this.isSleeping) {
            this.spawnFloatingText('Pet sedang tidur lelap! 💤', '#818cf8', '🌙');
            return;
        }
        this.ballEnabled = true;
        this.ball.x = 76;
        this.ball.y = 52;
        this.ball.vx = -(2.5 + Math.random() * 2);
        this.ball.vy = -(6.5 + Math.random() * 2);
        this.ball.combo = 0;
        this.playSound('kick');
        this.spawnFloatingText('Mulai Main Bola! ⚽', '#38bdf8', '⚽');
    },

    stopBallGame() {
        this.ballEnabled = false;
        this.ball.vx = 0;
        this.ball.vy = 0;
        this.ball.combo = 0;
        this.spawnFloatingText('Bola Disimpan 📦', '#94a3b8', '⏹️');
    },

    kickBall() {
        if (this.isSleeping || !this.ballEnabled) return;
        this.ball.vy = -(8.5 + Math.random() * 3.5);
        this.ball.vx = (Math.random() - 0.5) * 8;
        this.playSound('kick');
        this.spawnFloatingText('Oper Bola! ⚽', '#38bdf8', '⚽');
    },

    startDragBall(e) {
        if (this.isSleeping) return;
        this.ball.isDragging = true;
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        const clientY = e.clientY || (e.touches && e.touches[0].clientY);
        this.ball.dragStartX = clientX;
        this.ball.dragStartY = clientY;
    },

    handleStagePointerMove(e) {
        this.handleMouseMove(e);
        if (this.ball.isDragging && !this.isSleeping) {
            const stage = this.$refs.stageElement;
            if (!stage) return;
            const rect = stage.getBoundingClientRect();
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            if (!clientX || !clientY) return;
            
            const newX = ((clientX - rect.left) / rect.width) * 100;
            const newY = ((clientY - rect.top) / rect.height) * 100;
            this.ball.vx = (newX - this.ball.x) * 0.45;
            this.ball.vy = (newY - this.ball.y) * 0.45;
            this.ball.x = Math.max(8, Math.min(92, newX));
            this.ball.y = Math.max(14, Math.min(74, newY));
        }
    },

    handleStagePointerUp() {
        if (this.ball.isDragging) {
            this.ball.isDragging = false;
            if (!this.isSleeping && Math.hypot(this.ball.vx, this.ball.vy) > 1.2) {
                this.playSound('bounce');
            }
        }
    },

    // Main Bareng & Akrobatik Pet
    startRub(e) {
        if (this.isSleeping) return;
        this.isRubbing = true;
        this.rubCount = 0;
    },

    doRub(e) {
        if (this.isSleeping || !this.isRubbing) return;
        const now = Date.now();
        if (now - this.lastRubTime > 80) {
            this.lastRubTime = now;
            this.rubCount++;
            this.petWiggleAngle = (Math.random() - 0.5) * 16;
            this.playSound('giggle');

            const id = Date.now() + Math.random();
            this.floatingHearts.push({ id, left: 46 + (Math.random() * 16 - 8) });
            setTimeout(() => this.floatingHearts = this.floatingHearts.filter(h => h.id !== id), 1200);

            if (this.rubCount >= 7) {
                this.isRubbing = false;
                this.rubCount = 0;
                this.petFlipping = true;
                this.playSound('cheer');
                this.spawnFloatingText('🎉 AKROBAT SALTO! (+5 Koin)', '#10b981', '⭐');
                $wire.interactPet();
                setTimeout(() => {
                    this.petFlipping = false;
                    this.petWiggleAngle = 0;
                }, 800);
            }
        }
    },

    endRub() {
        this.isRubbing = false;
        this.petWiggleAngle = 0;
    },

    triggerPetTap() {
        if (this.isSleeping) {
            this.spawnFloatingText('Ssst... Sedang Istirahat 💤', '#818cf8', '🌙');
            return;
        }
        this.petJumping = true;
        this.petTickleCount++;
        
        if (this.petTickleCount >= 4) {
            this.playSound('giggle');
            this.petTickleCount = 0;
            this.spawnFloatingText('Tos Dulu! 🐾', '#f59e0b', '🐾');
        } else {
            this.playSound('pat');
            this.spawnFloatingText('Hai Partner! 👋', '#38bdf8', '👋');
        }

        setTimeout(() => this.petJumping = false, 450);

        const id = Date.now();
        this.floatingHearts.push({ id, left: 45 + (Math.random() * 14 - 7) });
        setTimeout(() => {
            this.floatingHearts = this.floatingHearts.filter(h => h.id !== id);
        }, 1300);

        $wire.interactPet();
    },

    // Retro Radio Boombox: Hindia Playlist Player
    toggleRadio() {
        if (this.isSleeping) {
            this.spawnFloatingText('Pet sedang tidur lelap! 💤', '#818cf8', '🌙');
            return;
        }

        if (this.isMusicPlaying) {
            this.stopRadioMusic();
        } else {
            this.startRadioMusic();
        }
    },

    startRadioMusic() {
        const track = this.radioTracks[this.currentTrackIndex];
        this.isMusicPlaying = true;
        this.petDancing = true;
        this.spawnFloatingText(`🎵 ${track.artist} - ${track.title}`, '#c084fc', '📻');
        $wire.toggleMusic();
        
        if (this.musicInterval) clearInterval(this.musicInterval);
        this.musicInterval = setInterval(() => {
            if (!this.isMusicPlaying || this.isSleeping) {
                if (this.isSleeping && this.isMusicPlaying) this.stopRadioMusic();
                return;
            }
            const notes = ['🎵', '🎶', '🎷', '🎸', '✨', '🎹'];
            const id = Date.now() + Math.random();
            this.flyingNotes.push({
                id,
                char: notes[Math.floor(Math.random() * notes.length)],
                left: 76 + (Math.random() * 14 - 7),
                color: ['#c084fc', '#f472b6', '#38bdf8', '#facc15'][Math.floor(Math.random() * 4)]
            });
            setTimeout(() => {
                this.flyingNotes = this.flyingNotes.filter(n => n.id !== id);
            }, 1800);
        }, 850);

        if (this.radioAudio) {
            try {
                this.radioAudio.pause();
                this.radioAudio.currentTime = 0;
                this.radioAudio.src = '';
            } catch(e) {}
        }
        this.radioAudio = new Audio();

        let candidates = [track.file];
        if (track.altFiles && Array.isArray(track.altFiles)) {
            candidates = candidates.concat(track.altFiles);
        }
        let candidateIdx = 0;
        let isHandled = false;

        const tryPlay = () => {
            if (!this.isMusicPlaying || this.isSleeping) return;
            if (candidateIdx >= candidates.length) {
                // Graceful fallback to lofi chime if none of the candidate MP3 files exist yet
                this.playLofiChime();
                return;
            }

            const nextCandidate = () => {
                if (isHandled) return;
                isHandled = true;
                candidateIdx++;
                setTimeout(() => tryPlay(), 20);
            };

            isHandled = false;
            this.radioAudio.src = candidates[candidateIdx];
            this.radioAudio.volume = 0.75;

            this.radioAudio.ontimeupdate = () => {
                this.updateSingingLyrics();
            };

            this.radioAudio.onended = () => {
                if (this.isMusicPlaying && !this.isSleeping) {
                    this.nextRadioTrack();
                }
            };

            this.radioAudio.onerror = (err) => {
                nextCandidate();
            };

            const p = this.radioAudio.play();
            if (p !== undefined) {
                p.catch(e => {
                    nextCandidate();
                });
            }
        };

        tryPlay();
    },

    updateSingingLyrics() {
        if (!this.radioAudio || !this.isMusicPlaying || this.isSleeping) {
            this.currentLyricLine = '';
            this.petSingingMouthOpen = false;
            return;
        }

        const track = this.radioTracks[this.currentTrackIndex];
        const currentTime = this.radioAudio.currentTime || 0;
        
        // Rhythmic mouth toggle: alternate every ~320ms while song is playing
        this.petSingingMouthOpen = Math.floor(currentTime * 3.1) % 2 === 0;

        if (track && track.lyrics && track.lyrics.length > 0) {
            let activeLine = track.lyrics[0].text;
            for (let i = 0; i < track.lyrics.length; i++) {
                if (currentTime >= track.lyrics[i].time) {
                    activeLine = track.lyrics[i].text;
                } else {
                    break;
                }
            }
            this.currentLyricLine = activeLine;
        }
    },

    stopRadioMusic() {
        this.isMusicPlaying = false;
        this.petDancing = false;
        this.currentLyricLine = '';
        this.petSingingMouthOpen = false;
        if (this.radioAudio) {
            try {
                this.radioAudio.pause();
                this.radioAudio.currentTime = 0;
                this.radioAudio.src = '';
            } catch(e) {}
        }
        if (this.musicInterval) {
            clearInterval(this.musicInterval);
            this.musicInterval = null;
        }
        this.spawnFloatingText('Radio Mati 🔇', '#94a3b8', '📻');
    },

    nextRadioTrack() {
        if (this.isSleeping) return;
        this.currentTrackIndex = (this.currentTrackIndex + 1) % this.radioTracks.length;
        if (this.isMusicPlaying) {
            this.startRadioMusic();
        } else {
            const track = this.radioTracks[this.currentTrackIndex];
            this.spawnFloatingText(`Pilih: ${track.title} 📻`, '#c084fc', '⏭️');
        }
    },

    prevRadioTrack() {
        if (this.isSleeping) return;
        this.currentTrackIndex = (this.currentTrackIndex - 1 + this.radioTracks.length) % this.radioTracks.length;
        if (this.isMusicPlaying) {
            this.startRadioMusic();
        } else {
            const track = this.radioTracks[this.currentTrackIndex];
            this.spawnFloatingText(`Pilih: ${track.title} 📻`, '#c084fc', '⏮️');
        }
    },

    playLofiChime() {
        if (!this.soundEnabled) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            const freqs = [523.25, 587.33, 659.25, 783.99, 880.00];
            const freq = freqs[Math.floor(Math.random() * freqs.length)];
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(freq, now);
            gain.gain.setValueAtTime(0.25, now);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.55);
            gain.gain.setValueAtTime(0, now + 0.58);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.58);
            setTimeout(() => { try { ctx.close(); } catch(e){} }, 700);
        } catch(e) {}
    },

    // Interactive Environment Clickables
    interactWindow() {
        this.playSound('pat');
        const isNight = {{ ($pet && $pet->is_sleeping) ? 'true' : 'false' }};
        if (isNight) {
            this.windowEvent = 'star';
            this.spawnFloatingText('🌠 Bintang Jatuh! Malam berkah...', '#818cf8', '✨');
        } else {
            this.windowEvent = 'bird';
            this.spawnFloatingText('🕊️ Burung berkicau riang!', '#34d399', '🌳');
        }
        setTimeout(() => this.windowEvent = null, 2000);
    },

    interactPlant() {
        this.playSound('bubble');
        this.plantBlooming = true;
        this.spawnFloatingText('🌸 Sukulen Mekar Cantik!', '#ec4899', '🪴');
        $wire.interactPlant();
        setTimeout(() => this.plantBlooming = false, 2500);
    },

    triggerDrinking() {
        if (this.isSleeping) {
            this.spawnFloatingText('Pet sedang tidur lelap! 💤', '#818cf8', '🌙');
            return;
        }
        if (this.petDrinking) return;

        this.petDrinking = true;
        this.playSound('coffee');

        setTimeout(() => {
            this.drinkingAura = true;
            this.playSound('cheer');
            this.spawnFloatingText('+25% Energi Terisi Penuh! ☕⚡', '#f59e0b', '⚡');
        }, 750);

        setTimeout(() => {
            this.drinkingAura = false;
            this.petDrinking = false;
        }, 1650);

        $wire.drinkCoffee();
    },

    triggerFeeding(foodKey) {
        if (this.isSleeping) {
            this.spawnFloatingText('Pet sedang tidur lelap! 💤', '#818cf8', '🌙');
            return;
        }
        if (this.petEating) return;

        this.petEating = true;
        this.playSound('eat');
        
        const foodEmojis = {
            coffee: '☕',
            donut: '🍩',
            apple: '🍎',
            nasgor: '🍛',
            milk: '🥛',
            kurma: '🌰',
            kebuli: '🍛',
        };
        const foodItem = foodEmojis[foodKey] || '🍩';
        this.flyingFood = foodItem;
        
        // Arc lands in pet's paws/mouth
        setTimeout(() => {
            this.flyingFood = null;
            this.eatingFoodItem = foodItem;
            this.playSound('eat');
            
            // Multiple food crumb particles burst out
            for (let i = 0; i < 14; i++) {
                const id = Date.now() + i;
                this.crumbs.push({
                    id,
                    left: 44 + (Math.random() * 20 - 10),
                    color: ['#f59e0b', '#ef4444', '#10b981', '#3b82f6', '#ec4899', '#facc15'][Math.floor(Math.random() * 6)]
                });
            }
        }, 500);

        // Chomp finish
        setTimeout(() => {
            this.eatingFoodItem = null;
            this.crumbs = [];
            this.playSound('cheer');
            this.spawnFloatingText('+Kenyang & Bergizi! 😋', '#10b981', '🍽️');
            this.floatingHearts.push({ id: Date.now(), left: 50 });
        }, 1300);

        setTimeout(() => {
            this.petEating = false;
        }, 1600);

        $wire.feedPet(foodKey);
    },

    triggerPiket() {
        if (this.isSleeping) {
            this.spawnFloatingText('Pet sedang tidur lelap! 💤', '#818cf8', '🌙');
            return;
        }
        if (this.petSweeping) return;

        this.petSweeping = true;
        this.playSound('sweep');

        // Spawn dust puffs blowing across
        this.dustPuffs = [
            { id: 1, left: 22, bottom: 25 },
            { id: 2, left: 42, bottom: 30 },
            { id: 3, left: 62, bottom: 22 },
            { id: 4, left: 82, bottom: 28 },
        ];

        // Mid-way: Clean shine wave across room & sparkles pop!
        setTimeout(() => {
            this.playSound('sparkle');
            this.dustPuffs = [];
            this.sweepingSparkles = [
                { id: 1, left: 20, top: 48, icon: '✨' },
                { id: 2, left: 35, top: 68, icon: '🌟' },
                { id: 3, left: 65, top: 45, icon: '✨' },
                { id: 4, left: 80, top: 65, icon: '⭐' },
                { id: 5, left: 50, top: 28, icon: '✨' },
            ];
            this.spawnFloatingText('Kinclong & Bersih! +15 Koin 🧹', '#10b981', '✨');
        }, 850);

        setTimeout(() => {
            this.sweepingSparkles = [];
            this.petSweeping = false;
        }, 1750);

        $wire.doPiket();
    },

    triggerBathing() {
        this.petBathing = true;
        this.playSound('bubble');
        this.spawnFloatingText('Busa Wangi & Kinclong! 🧼', '#38bdf8', '🫧');

        for (let i = 0; i < 16; i++) {
            const id = Date.now() + i;
            this.bubbles.push({
                id,
                left: 30 + (Math.random() * 40),
                size: 16 + Math.random() * 26,
                delay: Math.random() * 0.4
            });
        }
        setTimeout(() => {
            this.bubbles = [];
            this.petBathing = false;
        }, 1800);

        $wire.cleanPet();
    },

    triggerSleepToggle() {
        this.lampSwinging = true;
        setTimeout(() => this.lampSwinging = false, 800);
        this.playSound('switch');
        this.isSleeping = !this.isSleeping;
        if (this.isSleeping) {
            if (this.isMusicPlaying) this.stopRadioMusic();
            if (this.ballEnabled) this.stopBallGame();
            this.spawnFloatingText('Selamat Tidur... 🌙', '#818cf8', '💤');
        } else {
            this.spawnFloatingText('Selamat Pagi Ceria! ☀️', '#f59e0b', '✨');
        }
        $wire.toggleSleepPet();
    }
}" 
@mousemove="handleStagePointerMove($event)"
@touchmove="handleStagePointerMove($event)"
@mouseup="handleStagePointerUp()"
@touchend="handleStagePointerUp()"
class="relative bg-slate-950 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-xl transition-all">

    <style>
        .room-perspective {
            perspective: 800px;
        }
        .room-floor {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 60%, #020617 100%);
            background-image: 
                radial-gradient(ellipse at 50% 10%, rgba(245, 158, 11, 0.15) 0%, transparent 60%),
                repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(255, 255, 255, 0.03) 40px, rgba(255, 255, 255, 0.03) 80px);
        }
        .room-night {
            background: radial-gradient(circle at 75% 20%, #1e1b4b 0%, #0f172a 45%, #020617 100%) !important;
        }
        .carpet-rug {
            background: radial-gradient(ellipse at center, #7f1d1d 0%, #450a0a 70%, #1c0505 100%);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.8), inset 0 0 0 3px #d97706, inset 0 0 15px rgba(0, 0, 0, 0.6);
        }

        @keyframes mascotBreathe {
            0%, 100% { transform: scale(1) translateY(0); }
            50% { transform: scale(1.025, 0.975) translateY(-6px); }
        }
        @keyframes mascotJump {
            0% { transform: scale(1, 1) translateY(0); }
            30% { transform: scale(1.15, 0.85) translateY(4px); }
            60% { transform: scale(0.9, 1.15) translateY(-28px); }
            80% { transform: scale(1.05, 0.95) translateY(-4px); }
            100% { transform: scale(1, 1) translateY(0); }
        }
        @keyframes eyeBlink {
            0%, 94%, 98%, 100% { transform: scaleY(1); }
            96% { transform: scaleY(0.08); }
        }
        @keyframes heartFly {
            0% { opacity: 1; transform: translateY(0px) scale(0.6); }
            50% { opacity: 0.9; transform: translateY(-40px) scale(1.2); }
            100% { opacity: 0; transform: translateY(-90px) scale(1.5); }
        }
        @keyframes bubbleFloat {
            0% { opacity: 0; transform: translateY(20px) scale(0.5); }
            30% { opacity: 0.9; }
            80% { opacity: 0.8; }
            100% { opacity: 0; transform: translateY(-80px) scale(1.3); }
        }
        @keyframes crumbDrop {
            0% { opacity: 1; transform: translateY(0) scale(1); }
            100% { opacity: 0; transform: translateY(35px) scale(0.4); }
        }
        @keyframes sleepZzz {
            0% { opacity: 0; transform: translate(0, 0) scale(0.5); }
            50% { opacity: 0.9; }
            100% { opacity: 0; transform: translate(30px, -45px) scale(1.4); }
        }
        @keyframes windowStarTwinkle {
            0%, 100% { opacity: 0.3; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        @keyframes mascotDance {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-7px) rotate(-6deg) scale(1.03); }
            50% { transform: translateY(0) rotate(0deg); }
            75% { transform: translateY(-7px) rotate(6deg) scale(1.03); }
        }
        @keyframes mascotFlip {
            0% { transform: scale(1) rotate(0deg); }
            40% { transform: scale(1.2) translateY(-35px) rotate(180deg); }
            100% { transform: scale(1) rotate(360deg); }
        }
        @keyframes floatBadge {
            0% { opacity: 0; transform: translate(-50%, 15px) scale(0.7); }
            20% { opacity: 1; transform: translate(-50%, -10px) scale(1.1); }
            75% { opacity: 1; transform: translate(-50%, -30px) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -55px) scale(0.9); }
        }
        @keyframes musicNoteRise {
            0% { opacity: 1; transform: translateY(0) scale(0.8) rotate(0deg); }
            50% { transform: translateY(-35px) scale(1.2) rotate(15deg); }
            100% { opacity: 0; transform: translateY(-70px) scale(1.4) rotate(-15deg); }
        }
        @keyframes flyingFoodArc {
            0% { opacity: 0; transform: translate(-50%, 120px) scale(0.4); }
            35% { opacity: 1; transform: translate(-65%, 20px) scale(1.4); }
            75% { opacity: 1; transform: translate(-50%, -15px) scale(1.1); }
            100% { opacity: 0; transform: translate(-50%, 0px) scale(0.2); }
        }
        @keyframes birdFly {
            0% { transform: translateX(-25px) translateY(10px) scale(0.6); opacity: 0; }
            25% { opacity: 1; }
            75% { opacity: 1; }
            100% { transform: translateX(85px) translateY(-15px) scale(1); opacity: 0; }
        }
        @keyframes starShoot {
            0% { transform: translateX(-20px) translateY(-20px) scale(0.5); opacity: 0; }
            30% { opacity: 1; }
            100% { transform: translateX(65px) translateY(65px) scale(1.3); opacity: 0; }
        }
        @keyframes lampCordSwing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(18deg); }
            50% { transform: rotate(-14deg); }
            75% { transform: rotate(8deg); }
        }

        .mascot-idle {
            animation: mascotBreathe 3.6s ease-in-out infinite;
            transform-origin: bottom center;
        }
        .mascot-jump-anim {
            animation: mascotJump 0.45s ease-out forwards;
            transform-origin: bottom center;
        }
        .mascot-dance {
            animation: mascotDance 0.8s infinite ease-in-out;
            transform-origin: bottom center;
        }
        .mascot-flip {
            animation: mascotFlip 0.75s ease-in-out;
            transform-origin: center;
        }
        .eye-blinker {
            animation: eyeBlink 4.2s infinite;
            transform-origin: center;
        }
        .heart-bubble-anim {
            animation: heartFly 1.3s cubic-bezier(0.15, 0.85, 0.35, 1) forwards;
        }
        .bubble-anim {
            animation: bubbleFloat 1.6s ease-out forwards;
        }
        .crumb-anim {
            animation: crumbDrop 0.8s ease-out forwards;
        }
        .floating-badge {
            animation: floatBadge 1.25s ease-out forwards;
        }
        .music-note-anim {
            animation: musicNoteRise 1.6s ease-out forwards;
        }
        .flying-food-anim {
            animation: flyingFoodArc 0.65s cubic-bezier(0.2, 0.9, 0.4, 1) forwards;
        }
        .bird-fly-anim {
            animation: birdFly 1.8s ease-out forwards;
        }
        .star-shoot-anim {
            animation: starShoot 1.4s ease-out forwards;
        }
        .cord-swing {
            animation: lampCordSwing 0.8s ease-in-out;
            transform-origin: top center;
        }
        .zzz-1 { animation: sleepZzz 2.4s infinite ease-out; }
        .zzz-2 { animation: sleepZzz 2.4s infinite ease-out 0.8s; }
        .zzz-3 { animation: sleepZzz 2.4s infinite ease-out 1.6s; }
        .twinkle-star { animation: windowStarTwinkle 2.5s infinite ease-in-out; }

        @keyframes mascotDrink {
            0% { transform: translateY(0) scale(1); }
            20% { transform: translateY(-4px) rotate(-3deg) scale(1.02); }
            40% { transform: translateY(-8px) rotate(3deg) scale(1.04); }
            60% { transform: translateY(-4px) rotate(-2deg) scale(1.02); }
            80% { transform: translateY(-16px) rotate(0deg) scale(1.12); }
            100% { transform: translateY(0) scale(1); }
        }
        @keyframes mascotChomp {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.08, 0.92) translateY(5px); }
            50% { transform: scale(0.94, 1.06) translateY(-7px); }
            75% { transform: scale(1.05, 0.95) translateY(3px); }
        }
        @keyframes mascotSweep {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateX(-12px) rotate(-8deg); }
            50% { transform: translateX(12px) rotate(8deg); }
            75% { transform: translateY(-12px) scale(1.08); }
        }
        @keyframes cupSip {
            0% { opacity: 0; transform: translate(-50%, 50px) scale(0.4); }
            25% { opacity: 1; transform: translate(-50%, -20px) scale(1.15) rotate(0deg); }
            60% { opacity: 1; transform: translate(-50%, -25px) scale(1.25) rotate(-15deg); }
            85% { opacity: 1; transform: translate(-50%, -15px) scale(1.1) rotate(0deg); }
            100% { opacity: 0; transform: translate(-50%, -45px) scale(0.5); }
        }
        @keyframes steamRise {
            0% { opacity: 0; transform: translateY(0) scale(0.6); }
            50% { opacity: 0.9; transform: translateY(-16px) scale(1.2); }
            100% { opacity: 0; transform: translateY(-36px) scale(1.6); }
        }
        @keyframes energyPulse {
            0% { opacity: 0; transform: scale(0.6); }
            50% { opacity: 0.8; transform: scale(1.2); }
            100% { opacity: 0; transform: scale(1.5); }
        }
        @keyframes foodBite {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
            20% { opacity: 1; transform: translate(-50%, -50%) scale(1.3); }
            45% { transform: translate(-50%, -54px) scale(1.1) rotate(-12deg); }
            70% { transform: translate(-50%, -50px) scale(0.8) rotate(10deg); }
            90% { opacity: 1; transform: translate(-50%, -48px) scale(0.4); }
            100% { opacity: 0; transform: translate(-50%, -45px) scale(0); }
        }
        @keyframes broomSweepAnim {
            0% { opacity: 0; transform: translate(-140px, 40px) rotate(-55deg) scale(0.7); }
            20% { opacity: 1; transform: translate(-70px, 15px) rotate(20deg) scale(1.1); }
            45% { transform: translate(0px, 35px) rotate(-45deg) scale(1.2); }
            70% { transform: translate(70px, 15px) rotate(25deg) scale(1.1); }
            90% { opacity: 1; transform: translate(140px, 35px) rotate(-35deg) scale(1); }
            100% { opacity: 0; transform: translate(180px, 10px) rotate(15deg) scale(0.6); }
        }
        @keyframes dustPuffAnim {
            0% { opacity: 0; transform: translate(0, 0) scale(0.4); }
            40% { opacity: 0.9; transform: translate(-25px, -15px) scale(1.3); }
            100% { opacity: 0; transform: translate(-55px, -35px) scale(1.8); }
        }
        @keyframes roomCleanWave {
            0% { opacity: 0; transform: translateX(-120%) skewX(-25deg); }
            40% { opacity: 0.75; }
            100% { opacity: 0; transform: translateX(220%) skewX(-25deg); }
        }
        @keyframes sparklePopAnim {
            0% { opacity: 0; transform: scale(0.2) rotate(0deg); }
            50% { opacity: 1; transform: scale(1.4) rotate(90deg); }
            100% { opacity: 0; transform: scale(0.6) rotate(180deg); }
        }

        .mascot-drink-anim {
            animation: mascotDrink 1.6s ease-in-out;
            transform-origin: bottom center;
        }
        .mascot-chomp-anim {
            animation: mascotChomp 0.4s ease-in-out 3;
            transform-origin: bottom center;
        }
        .mascot-sweep-anim {
            animation: mascotSweep 1.6s ease-in-out;
            transform-origin: bottom center;
        }
        .cup-sip-anim {
            animation: cupSip 1.6s ease-in-out forwards;
        }
        .steam-rise-anim {
            animation: steamRise 1.2s infinite ease-out;
        }
        .energy-pulse-anim {
            animation: energyPulse 1.2s ease-out forwards;
        }
        .food-bite-anim {
            animation: foodBite 1.2s ease-in-out forwards;
        }
        .broom-sweep-anim {
            animation: broomSweepAnim 1.6s ease-in-out forwards;
        }
        .dust-puff-anim {
            animation: dustPuffAnim 0.9s ease-out forwards;
        }
        .room-shine-anim {
            animation: roomCleanWave 1.4s ease-out forwards;
        }
        .sparkle-pop-anim {
            animation: sparklePopAnim 1s ease-out forwards;
        }
    </style>

    <!-- ========================================================= -->
    <!-- TOP NAVIGATION & COMPACT ARCADE HEADER -->
    <!-- ========================================================= -->
    <div class="p-2.5 sm:p-4 border-b border-slate-800/80 bg-slate-900/80 flex flex-col md:flex-row md:items-center justify-between gap-2.5 sm:gap-3 relative z-30">
        <!-- Top Row on Mobile / Left Section on Desktop -->
        <div class="flex items-center justify-between md:justify-start gap-2.5 sm:gap-3 w-full md:w-auto">
            @if($pet)
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-300 p-0.5 shadow-md sm:shadow-lg shadow-amber-500/20">
                            <div class="w-full h-full bg-slate-900 rounded-[10px] sm:rounded-[14px] flex items-center justify-center text-xl sm:text-2xl">
                                {{ $pet->meta['emoji'] }}
                            </div>
                        </div>
                        <span class="absolute -bottom-1 -right-1 px-1.5 py-0.2 rounded-md bg-amber-500 text-slate-950 text-[8px] sm:text-[9px] font-black uppercase tracking-wider shadow">
                            Lv.{{ $pet->level }}
                        </span>
                    </div>

                    <div class="min-w-0">
                        @if($isEditingName)
                            <div class="flex items-center gap-1 py-0.5">
                                <input 
                                    type="text" 
                                    wire:model="editNameValue" 
                                    wire:keydown.enter="savePetName"
                                    wire:keydown.escape="cancelEditingName"
                                    class="px-2 py-0.5 text-xs sm:text-sm bg-slate-950 border-2 border-amber-500 rounded-lg text-white font-black focus:outline-none w-28 sm:w-48 shadow-inner" 
                                    maxlength="30"
                                    autofocus
                                />
                                <button wire:click="savePetName" type="button" class="px-2 py-0.5 rounded-lg text-xs font-black bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow cursor-pointer">✓</button>
                                <button wire:click="cancelEditingName" type="button" class="px-1.5 py-0.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-400 hover:text-white cursor-pointer">✕</button>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5">
                                <h4 class="text-xs sm:text-base font-black text-white tracking-wide truncate max-w-[130px] sm:max-w-none">
                                    {{ $pet->name }}
                                </h4>
                                <button 
                                    wire:click="startEditingName" 
                                    type="button" 
                                    class="p-0.5 rounded text-slate-400 hover:text-amber-300 text-[10px] sm:text-xs cursor-pointer" 
                                    title="Ubah nama"
                                >
                                    ✏️
                                </button>
                                <span class="hidden sm:inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                    {{ $pet->rank_title }}
                                </span>
                            </div>
                        @endif

                        <!-- XP Gauge -->
                        <div class="flex items-center gap-1.5 mt-0.5">
                            @php
                                $needed = $pet->xpForNextLevel();
                                $pct = $needed > 0 ? min(100, round(($pet->xp / $needed) * 100)) : 100;
                            @endphp
                            <div class="w-20 sm:w-40 h-1.5 sm:h-2 bg-slate-950 rounded-full overflow-hidden border border-slate-700/60 p-0.5">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-300 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-[9px] sm:text-[10px] font-mono text-slate-400 font-bold shrink-0">
                                {{ $pet->xp }}/{{ $needed }} XP
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Coin & Sound right next to avatar row -->
                <div class="flex md:hidden items-center gap-1.5 shrink-0">
                    <div class="flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-amber-500/10 to-yellow-500/10 border border-amber-500/30 text-amber-300 rounded-lg text-[11px] font-black">
                        <span>🪙</span>
                        <span class="font-mono text-xs">{{ number_format($pet->coins) }}</span>
                    </div>
                    <button 
                        @click="toggleSound()" 
                        type="button"
                        class="p-1.5 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 text-xs cursor-pointer active:scale-95"
                    >
                        <span x-text="soundEnabled ? '🔊' : '🔇'"></span>
                    </button>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🐾</span>
                    <div>
                        <h4 class="text-sm font-bold text-white">Adopsi Sahabat ELF</h4>
                        <p class="text-xs text-slate-400">Pilih karakter sahabat pertamamu</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Side: Desktop Coins + Navigation Tabs -->
        <div class="flex items-center gap-2 w-full md:w-auto justify-between md:justify-end">
            @if($pet)
                <!-- Desktop Only Coins & Sound -->
                <div class="hidden md:flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-500/10 to-yellow-500/10 border border-amber-500/30 text-amber-300 rounded-xl text-xs font-black shadow-xs">
                        <span class="text-base animate-pulse">🪙</span>
                        <span class="font-mono text-sm tracking-tight">{{ number_format($pet->coins) }}</span>
                        <span class="text-[10px] text-amber-400/80">Koin</span>
                    </div>
                    <button 
                        @click="toggleSound()" 
                        type="button"
                        class="p-2 rounded-xl bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-300 hover:text-white transition-all text-xs cursor-pointer"
                        :title="soundEnabled ? 'Matikan Suara Mini-Game' : 'Nyalakan Suara Mini-Game'"
                    >
                        <span x-text="soundEnabled ? '🔊' : '🔇'"></span>
                    </button>
                </div>

                <!-- Navigation Tabs: Full width 5-col grid on mobile, inline flex on desktop -->
                <div class="grid grid-cols-5 md:flex items-center gap-1 p-1 bg-slate-950 rounded-xl border border-slate-800 w-full md:w-auto">
                    <button 
                        wire:click="setViewMode('arena')" 
                        type="button"
                        class="px-1.5 sm:px-3 py-1.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider transition-all text-center {{ $viewMode === 'arena' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white' }}"
                    >
                        🏠 <span class="hidden sm:inline">Kamar</span>
                    </button>
                    <button 
                        wire:click="setViewMode('shop')" 
                        type="button"
                        class="px-1.5 sm:px-3 py-1.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider transition-all text-center {{ $viewMode === 'shop' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white' }}"
                    >
                        🛒 <span class="hidden sm:inline">Toko</span>
                    </button>
                    <button 
                        wire:click="setViewMode('leaderboard')" 
                        type="button"
                        class="px-1.5 sm:px-3 py-1.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider transition-all text-center {{ $viewMode === 'leaderboard' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white' }}"
                    >
                        🏆 <span class="hidden sm:inline">Peringkat</span>
                    </button>
                    <button 
                        wire:click="setViewMode('rewards')" 
                        type="button"
                        class="px-1.5 sm:px-3 py-1.5 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider transition-all text-center {{ $viewMode === 'rewards' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white' }}"
                    >
                        📜 <span class="hidden sm:inline">Reward</span>
                    </button>
                    <button 
                        wire:click="setViewMode('adopt')" 
                        type="button"
                        title="Ganti Karakter / Nama"
                        class="px-1.5 sm:px-2.5 py-1.5 rounded-lg text-[10px] sm:text-xs font-bold transition-all text-center {{ $viewMode === 'adopt' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white' }}"
                    >
                        ⚙️ <span class="hidden sm:inline">Karakter</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- VIEW MODE: ARENA (KAMAR PERSPEKTIF POU DENGAN RUANGAN KARTUN) -->
    <!-- ========================================================= -->
    @if($viewMode === 'arena' && $pet)
        <div class="p-3 sm:p-5 space-y-4">
            
            <!-- 4 Modern Arcade Status Bars (Ultra Compact Single-Row on Mobile, Rich Cards on Desktop) -->
            <div class="grid grid-cols-4 gap-1.5 sm:gap-3">
                <!-- 1. Hunger -->
                <div class="p-1.5 sm:p-3 bg-slate-900/90 border border-slate-800 rounded-xl sm:rounded-2xl flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-2.5 shadow-sm text-center sm:text-left">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-sm sm:text-lg shrink-0">
                        🍗
                    </div>
                    <div class="w-full flex-1 min-w-0">
                        <div class="flex items-center justify-between text-xs mb-0.5 sm:mb-1">
                            <span class="hidden sm:inline text-[11px] font-extrabold text-slate-300">Kenyang</span>
                            <span class="mx-auto sm:mx-0 font-mono font-black text-[10px] sm:text-xs {{ $pet->hunger > 50 ? 'text-emerald-400' : ($pet->hunger > 25 ? 'text-amber-400' : 'text-red-400 animate-pulse') }}">
                                {{ $pet->hunger }}%
                            </span>
                        </div>
                        <div class="w-full h-1.5 sm:h-2 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pet->hunger > 50 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($pet->hunger > 25 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $pet->hunger }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- 2. Hygiene -->
                <div class="p-1.5 sm:p-3 bg-slate-900/90 border border-slate-800 rounded-xl sm:rounded-2xl flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-2.5 shadow-sm text-center sm:text-left">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sm sm:text-lg shrink-0">
                        🧼
                    </div>
                    <div class="w-full flex-1 min-w-0">
                        <div class="flex items-center justify-between text-xs mb-0.5 sm:mb-1">
                            <span class="hidden sm:inline text-[11px] font-extrabold text-slate-300">Bersih</span>
                            <span class="mx-auto sm:mx-0 font-mono font-black text-[10px] sm:text-xs {{ $pet->hygiene > 50 ? 'text-sky-400' : ($pet->hygiene > 25 ? 'text-amber-400' : 'text-red-400 animate-pulse') }}">
                                {{ $pet->hygiene }}%
                            </span>
                        </div>
                        <div class="w-full h-1.5 sm:h-2 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pet->hygiene > 50 ? 'bg-gradient-to-r from-sky-500 to-blue-400' : ($pet->hygiene > 25 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $pet->hygiene }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- 3. Energy -->
                <div class="p-1.5 sm:p-3 bg-slate-900/90 border border-slate-800 rounded-xl sm:rounded-2xl flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-2.5 shadow-sm text-center sm:text-left">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-sm sm:text-lg shrink-0">
                        ⚡
                    </div>
                    <div class="w-full flex-1 min-w-0">
                        <div class="flex items-center justify-between text-xs mb-0.5 sm:mb-1">
                            <span class="hidden sm:inline text-[11px] font-extrabold text-slate-300">Energi</span>
                            <span class="mx-auto sm:mx-0 font-mono font-black text-[10px] sm:text-xs {{ $pet->energy > 50 ? 'text-yellow-400' : ($pet->energy > 25 ? 'text-amber-400' : 'text-red-400 animate-pulse') }}">
                                {{ $pet->energy }}%
                            </span>
                        </div>
                        <div class="w-full h-1.5 sm:h-2 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pet->energy > 50 ? 'bg-gradient-to-r from-yellow-500 to-amber-400' : ($pet->energy > 25 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $pet->energy }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- 4. Happiness -->
                <div class="p-1.5 sm:p-3 bg-slate-900/90 border border-slate-800 rounded-xl sm:rounded-2xl flex flex-col sm:flex-row items-center sm:items-center gap-1 sm:gap-2.5 shadow-sm text-center sm:text-left">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center text-sm sm:text-lg shrink-0">
                        💖
                    </div>
                    <div class="w-full flex-1 min-w-0">
                        <div class="flex items-center justify-between text-xs mb-0.5 sm:mb-1">
                            <span class="hidden sm:inline text-[11px] font-extrabold text-slate-300">Bahagia</span>
                            <span class="mx-auto sm:mx-0 font-mono font-black text-[10px] sm:text-xs {{ $pet->happiness > 50 ? 'text-pink-400' : ($pet->happiness > 25 ? 'text-amber-400' : 'text-red-400 animate-pulse') }}">
                                {{ $pet->happiness }}%
                            </span>
                        </div>
                        <div class="w-full h-1.5 sm:h-2 bg-slate-950 rounded-full overflow-hidden p-0.5 border border-slate-800">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pet->happiness > 50 ? 'bg-gradient-to-r from-pink-500 to-rose-400' : ($pet->happiness > 25 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $pet->happiness }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- CARTOON PESANTREN ROOM STAGE (ISOMETRIC / 2.5D BACKGROUND) -->
            <!-- ========================================================= -->
            <div 
                x-ref="stageElement"
                style="height: 400px; min-height: 400px; width: 100%; position: relative;"
                class="rounded-3xl border border-slate-800 overflow-hidden transition-all duration-700 select-none relative {{ $pet->is_sleeping ? 'room-night shadow-inner' : 'room-floor' }}"
            >
                
                <!-- Room Walls & Decorative Interactive Elements -->
                <div class="absolute inset-0">
                    
                    <!-- Modern Arch Window on Wall (Interactive: click to look outside!) -->
                    <div 
                        @click="interactWindow()"
                        style="position: absolute; top: 12px; left: 12px; width: 68px; height: 96px;"
                        class="rounded-t-full border-2 border-slate-700/60 bg-slate-900/80 overflow-hidden shadow-lg flex flex-col items-center justify-center cursor-pointer hover:border-amber-400/60 transition-colors group z-10"
                        title="Jendela Kamar (Klik untuk melihat suasana luar)"
                    >
                        @if($pet->is_sleeping)
                            <!-- Night Sky through window -->
                            <div class="w-full h-full bg-gradient-to-b from-indigo-950 via-slate-900 to-slate-950 relative flex items-center justify-center">
                                <span class="absolute top-3 right-3 sm:top-4 sm:right-4 text-base sm:text-xl group-hover:scale-110 transition-transform">🌙</span>
                                <span class="absolute top-7 left-2.5 sm:top-8 sm:left-3 text-[9px] sm:text-[10px] text-amber-200 twinkle-star">✨</span>
                                <span class="absolute bottom-5 right-4 sm:bottom-6 sm:right-5 text-[7px] sm:text-[8px] text-indigo-300 twinkle-star">★</span>
                                <span class="absolute top-12 left-6 sm:top-14 sm:left-8 text-[8px] sm:text-[9px] text-slate-400 twinkle-star">✦</span>
                                <!-- Falling star on click -->
                                <template x-if="windowEvent === 'star'">
                                    <span class="star-shoot-anim absolute text-base sm:text-lg z-20 text-yellow-300">🌠</span>
                                </template>
                            </div>
                        @else
                            <!-- Morning/Day Sky with sun, clouds, & green tree leaves -->
                            <div class="w-full h-full bg-gradient-to-b from-sky-400/40 via-emerald-500/20 to-slate-900 relative flex items-center justify-center">
                                <span class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 text-base sm:text-lg animate-pulse">☀️</span>
                                <span class="absolute top-5 right-2 text-[10px] sm:text-xs opacity-80">☁️</span>
                                <span class="absolute bottom-1.5 text-emerald-400/60 text-lg sm:text-xl font-bold group-hover:scale-110 transition-transform">🌳</span>
                                <!-- Flying bird on click -->
                                <template x-if="windowEvent === 'bird'">
                                    <span class="bird-fly-anim absolute text-base sm:text-lg z-20">🕊️</span>
                                </template>
                            </div>
                        @endif
                    </div>

                    <!-- Motivational Poster on Wall (Hidden on mobile to keep space clean) -->
                    <div 
                        @click="playSound('coin'); spawnFloatingText('Fokus &amp; Semangat! ✨', '#facc15'); $wire.interactPoster()"
                        class="hidden md:block absolute top-3.5 sm:top-5 left-36 px-2.5 py-1 rounded-xl border border-amber-500/40 bg-slate-900/90 shadow-md text-center cursor-pointer hover:scale-105 hover:border-amber-400 transition-all z-10"
                        title="Poster Motivasi (Klik untuk kata-kata semangat)"
                    >
                        <span class="text-[10px] font-black text-amber-300 tracking-wider font-mono block">
                            ✨ WORK &amp; CHILL
                        </span>
                        <span class="text-[8px] text-slate-400 block -mt-0.5">Stay Positive!</span>
                    </div>

                    <!-- 1. COMPACT RADIO BOOMBOX PILL (Default Minimal View - Takes Zero Clutter) -->
                    <div 
                        x-show="!isSleeping && !showRadioDeck"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        @click="showRadioDeck = true"
                        style="position: absolute; top: 12px; right: 12px;"
                        class="z-20 flex items-center gap-1.5 px-2 sm:px-2.5 py-1 sm:py-1.5 rounded-xl border cursor-pointer transition-all shadow-lg group select-none"
                        :class="isMusicPlaying ? 'bg-purple-950/95 border-purple-400/90 shadow-[0_0_16px_rgba(168,85,247,0.45)] ring-1 ring-purple-400/50 scale-105' : 'bg-slate-900/90 border-slate-700/80 hover:border-purple-400/60 hover:scale-105'"
                        title="Klik untuk membuka Kontrol Lengkap Radio Hindia"
                    >
                        <span class="text-base sm:text-lg filter drop-shadow" :class="isMusicPlaying ? 'animate-bounce' : 'group-hover:scale-110 transition-transform'">📻</span>
                        <div class="text-left">
                            <div class="flex items-center gap-1">
                                <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-wider block" :class="isMusicPlaying ? 'text-purple-300' : 'text-slate-200'">
                                    Radio Hindia
                                </span>
                                <span x-show="isMusicPlaying" class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            </div>
                            <span class="text-[8px] text-slate-400 block -mt-0.5 max-w-[75px] sm:max-w-[100px] truncate" x-text="isMusicPlaying ? ('▶ ' + radioTracks[currentTrackIndex].title) : 'Klik buka kontrol'"></span>
                        </div>
                        <!-- Mini quick-toggle button on compact pill -->
                        <button 
                            @click.stop="toggleRadio()" 
                            type="button" 
                            class="ml-0.5 p-1 rounded-lg transition-colors text-[10px] cursor-pointer active:scale-90"
                            :class="isMusicPlaying ? 'bg-purple-900/60 text-purple-200 hover:bg-purple-800' : 'bg-slate-800 text-slate-400 hover:text-emerald-300 hover:bg-slate-700'"
                            :title="isMusicPlaying ? 'Jeda Musik' : 'Putar Musik Langsung'"
                        >
                            <span x-text="isMusicPlaying ? '⏸️' : '▶️'"></span>
                        </button>
                    </div>

                    <!-- 2. EXPANDED FULL RADIO DECK (Opened on Click, Can be Minimized with ➖ Button) -->
                    <div 
                        x-show="!isSleeping && showRadioDeck"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 scale-90 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-90 -translate-y-2"
                        style="position: absolute; top: 12px; right: 12px;"
                        class="z-40 w-[calc(100%-24px)] max-w-[270px] sm:max-w-[300px] p-2.5 sm:p-3 rounded-2xl border shadow-2xl backdrop-blur-xl bg-slate-950/95 transition-all select-none"
                        :class="isMusicPlaying ? 'border-purple-500/80 shadow-[0_0_30px_rgba(168,85,247,0.4)] ring-1 ring-purple-500/50' : 'border-slate-700/80'"
                    >
                        <!-- Top Header Row: Radio Title, Track Counter & Minimize Button -->
                        <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-800/80">
                            <div class="flex items-center gap-1.5">
                                <span class="text-lg" :class="isMusicPlaying ? 'animate-bounce' : ''">📻</span>
                                <div>
                                    <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-white block">
                                        Radio Hindia
                                    </span>
                                    <span class="text-[8px] text-purple-400 font-mono font-bold" x-text="'Lagu ' + (currentTrackIndex + 1) + '/' + radioTracks.length"></span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1.5">
                                <template x-if="isMusicPlaying">
                                    <span class="flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-500/20 border border-emerald-500/40 text-[8px] sm:text-[9px] font-black text-emerald-300 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> PLAYING
                                    </span>
                                </template>
                                <template x-if="!isMusicPlaying">
                                    <span class="px-1.5 py-0.5 rounded-md bg-slate-800 text-[8px] sm:text-[9px] font-bold text-slate-400">
                                        STANDBY
                                    </span>
                                </template>

                                <!-- Dedicated Minimize / Kecilkan Button -->
                                <button 
                                    @click="showRadioDeck = false" 
                                    type="button" 
                                    class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[9px] sm:text-[10px] font-bold flex items-center gap-1 transition-colors cursor-pointer border border-slate-700 active:scale-95"
                                    title="Kecilkan radio ke ikon minimal"
                                >
                                    <span>➖</span>
                                    <span>Kecilkan</span>
                                </button>
                            </div>
                        </div>

                        <!-- Middle Row: LCD Display showing Song Title & Artist + Equalizer -->
                        <div class="px-2.5 py-2 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-2 mb-2">
                            <div class="min-w-0 flex-1">
                                <div class="text-[11px] sm:text-xs font-black truncate text-white" x-text="radioTracks[currentTrackIndex].title"></div>
                                <div class="text-[9px] text-purple-300/80 truncate flex items-center gap-1 mt-0.5 font-medium">
                                    <span>🎤</span>
                                    <span x-text="radioTracks[currentTrackIndex].artist"></span>
                                </div>
                            </div>
                            <!-- Mini Equalizer bars when playing -->
                            <div x-show="isMusicPlaying" class="flex items-end gap-0.5 h-4 shrink-0">
                                <div class="w-1 bg-purple-400 rounded-full animate-pulse h-3"></div>
                                <div class="w-1 bg-pink-400 rounded-full animate-pulse h-4" style="animation-delay: 150ms"></div>
                                <div class="w-1 bg-sky-400 rounded-full animate-pulse h-2" style="animation-delay: 300ms"></div>
                                <div class="w-1 bg-emerald-400 rounded-full animate-pulse h-3.5" style="animation-delay: 450ms"></div>
                            </div>
                        </div>

                        <!-- Bottom Control Deck: Explicit, Crystal-Clear Large Action Buttons -->
                        <div class="grid grid-cols-4 gap-1 sm:gap-1.5">
                            <!-- 1. Tombol Lagu Sebelumnya (Prev) -->
                            <button 
                                @click="prevRadioTrack()" 
                                type="button" 
                                class="py-1.5 px-1 rounded-xl bg-slate-900 border border-slate-700 hover:border-purple-400 hover:bg-slate-800 text-slate-300 hover:text-white flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                                title="Putar Lagu Sebelumnya (⏮️)"
                            >
                                <span class="text-xs sm:text-sm">⏮️</span>
                                <span class="text-[8px] sm:text-[9px] font-bold leading-none mt-0.5">Prev</span>
                            </button>

                            <!-- 2. Tombol Play / Pause (Hijau / Kuning) -->
                            <button 
                                @click="toggleRadio()" 
                                type="button" 
                                class="py-1.5 px-1 rounded-xl flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95 shadow font-black border"
                                :class="isMusicPlaying ? 'bg-amber-500/20 border-amber-500/60 text-amber-300 hover:bg-amber-500/30' : 'bg-emerald-600/30 border-emerald-500/70 text-emerald-300 hover:bg-emerald-600/40 shadow-[0_0_12px_rgba(16,185,129,0.3)]'"
                                :title="isMusicPlaying ? 'Jeda Lagu Sementara' : 'Putar Musik'"
                            >
                                <span class="text-xs sm:text-sm" x-text="isMusicPlaying ? '⏸️' : '▶️'"></span>
                                <span class="text-[8px] sm:text-[9px] font-black leading-none mt-0.5" x-text="isMusicPlaying ? 'Jeda' : 'Putar'"></span>
                            </button>

                            <!-- 3. Tombol Stop Musik (Merah Tegas) -->
                            <button 
                                @click="stopRadioMusic()" 
                                type="button" 
                                class="py-1.5 px-1 rounded-xl bg-rose-950/50 border border-rose-600/60 hover:bg-rose-900/60 hover:border-rose-400 text-rose-300 flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                                title="Matikan &amp; Hentikan Musik (⏹️)"
                            >
                                <span class="text-xs sm:text-sm">⏹️</span>
                                <span class="text-[8px] sm:text-[9px] font-bold leading-none mt-0.5">Stop</span>
                            </button>

                            <!-- 4. Tombol Lagu Berikutnya (Next) -->
                            <button 
                                @click="nextRadioTrack()" 
                                type="button" 
                                class="py-1.5 px-1 rounded-xl bg-slate-900 border border-slate-700 hover:border-purple-400 hover:bg-slate-800 text-slate-300 hover:text-white flex flex-col items-center justify-center transition-all cursor-pointer active:scale-95"
                                title="Ganti ke Lagu Berikutnya (⏭️)"
                            >
                                <span class="text-xs sm:text-sm">⏭️</span>
                                <span class="text-[8px] sm:text-[9px] font-bold leading-none mt-0.5">Next</span>
                            </button>
                        </div>
                    </div>

                    <!-- Bookshelf with Novels & Self-Help Books -->
                    <div 
                        @click="playSound('pat'); spawnFloatingText('Baca Novel Seru! 📚', '#a855f7'); $wire.readBook()"
                        class="absolute top-28 sm:top-36 right-6 hidden md:flex items-end gap-1 px-3 py-1 bg-amber-950/70 border-b-4 border-amber-900 rounded-b shadow cursor-pointer hover:scale-105 transition-transform z-10"
                        title="Rak Buku &amp; Novel Favorit (Klik untuk membaca buku!)"
                    >
                        <span class="text-xs hover:-translate-y-1 transition-transform">📚</span>
                        <span class="text-sm hover:-translate-y-1 transition-transform">📖</span>
                        <span class="text-xs hover:-translate-y-1 transition-transform">📙</span>
                        <span class="text-sm hover:-translate-y-1 transition-transform">📘</span>
                    </div>

                    <!-- Adorable Mini Succulent Plant in Ceramic Pot on Floor -->
                    <div 
                        @click="interactPlant()"
                        style="position: absolute; bottom: 20px; left: 14px;"
                        class="flex flex-col items-center cursor-pointer hover:scale-115 active:scale-95 transition-all z-20 group p-1"
                        title="Tanaman Sukulen (Klik untuk menyiram &amp; bikin mekar!)"
                    >
                        <span 
                            x-show="plantBlooming"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="scale-0 opacity-0"
                            x-transition:enter-end="scale-125 opacity-100"
                            class="text-base absolute -top-4 text-pink-400 animate-bounce"
                        >🌸</span>
                        <span class="text-2xl sm:text-3xl filter drop-shadow group-hover:rotate-12 transition-transform">🪴</span>
                        <span class="text-[9px] font-black text-emerald-300 bg-slate-900/90 px-1.5 py-0.5 rounded-md border border-slate-700 shadow-sm mt-0.5">Siram</span>
                    </div>

                    <!-- Steaming Warm Coffee Mug on Floor -->
                    <div 
                        @click="triggerDrinking()"
                        style="position: absolute; bottom: 20px; right: 14px;"
                        class="flex flex-col items-center cursor-pointer hover:scale-110 active:scale-95 transition-transform z-20 group p-1"
                        title="Secangkir Kopi Hangat (Klik untuk ngopi santai!)"
                    >
                        <span class="text-xs text-amber-300/80 animate-bounce -mb-1">♨️</span>
                        <span class="text-2xl sm:text-3xl filter drop-shadow">☕</span>
                        <span class="text-[9px] font-black text-amber-300 bg-slate-900/90 px-1.5 py-0.5 rounded-md border border-slate-700 shadow-sm mt-0.5">Ngopi</span>
                    </div>

                    <!-- Hanging Bedside Lamp in Center Ceiling -->
                    <div 
                        @click="triggerSleepToggle()"
                        :class="{ 'cord-swing': lampSwinging }"
                        style="position: absolute; top: 0; left: 50%; transform: translateX(-50%);"
                        class="flex flex-col items-center pointer-events-auto cursor-pointer group z-20"
                        title="Klik tali saklar untuk menyalakan/memadamkan lampu kamar"
                    >
                        <div class="w-0.5 h-6 sm:h-9 bg-slate-600 group-hover:bg-amber-400 transition-colors"></div>
                        <div class="w-10 h-6 sm:w-12 sm:h-7 rounded-b-xl sm:rounded-b-2xl border transition-all flex items-center justify-center {{ $pet->is_sleeping ? 'bg-slate-800 border-slate-700 text-slate-500' : 'bg-gradient-to-b from-amber-300 to-yellow-400 border-yellow-200 text-amber-900 shadow-[0_0_25px_rgba(245,158,11,0.6)]' }}">
                            <span class="text-xs font-bold">{{ $pet->is_sleeping ? '💤' : '💡' }}</span>
                        </div>
                        <div class="w-0.5 h-3 sm:h-4 bg-amber-400/80 animate-bounce"></div>
                    </div>

                    <!-- Persahabatan / Persian Carpet Rug under Pet -->
                    <div 
                        style="position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); width: 220px; height: 56px;"
                        class="carpet-rug rounded-full opacity-85 pointer-events-none z-10"
                    ></div>

                    <!-- Ball Playground Control Center in Room -->
                    <div x-show="!isSleeping" style="position: absolute; top: 118px; left: 14px;" class="z-20">
                        <!-- State 1: Ball NOT Active -> Tombol "Mulai Main Bola" Jelas & Menarik -->
                        <div x-show="!ballEnabled">
                            <button 
                                @click="startBallGame()"
                                type="button" 
                                class="px-2 sm:px-2.5 py-1 rounded-xl text-[10px] sm:text-xs font-black border border-emerald-500/60 bg-slate-900/90 text-emerald-300 hover:bg-emerald-950/90 hover:border-emerald-400 hover:scale-105 transition-all flex items-center gap-1.5 shadow-lg shadow-black/50 cursor-pointer active:scale-95 group"
                                title="Keluarkan bola karet & mulai bermain sundul bola bersama pet"
                            >
                                <span class="text-sm sm:text-base group-hover:rotate-45 transition-transform">⚽</span>
                                <span class="font-extrabold text-white group-hover:text-emerald-300">Main Bola</span>
                            </button>
                        </div>

                        <!-- State 2: Ball IS Active -> Tombol "Berhenti / Simpan Bola" & "Oper Bola" Sangat Jelas -->
                        <div x-show="ballEnabled" class="flex flex-col gap-1.5 bg-slate-950/85 p-1 rounded-2xl border border-slate-700/80 shadow-2xl backdrop-blur-sm">
                            <!-- Tombol Berhenti (Merah Tegas) -->
                            <button 
                                @click="stopBallGame()"
                                type="button" 
                                class="px-2.5 py-1 rounded-xl text-[10px] sm:text-[11px] font-black border border-rose-500/70 bg-rose-950/90 text-rose-200 hover:bg-rose-900 hover:border-rose-400 hover:scale-105 transition-all flex items-center gap-1.5 shadow cursor-pointer active:scale-95"
                                title="Berhenti bermain dan simpan bola ke dalam kotak"
                            >
                                <span class="text-xs">⏹️</span>
                                <span class="font-extrabold">Berhenti</span>
                            </button>

                            <!-- Tombol Tendang / Oper Bola (Biru Aksi) -->
                            <button 
                                @click="kickBall()"
                                type="button" 
                                class="px-2.5 py-1 rounded-xl text-[10px] sm:text-[11px] font-black border border-sky-500/60 bg-sky-950/90 text-sky-200 hover:bg-sky-900 hover:border-sky-400 hover:scale-105 transition-all flex items-center gap-1.5 shadow cursor-pointer active:scale-95"
                                title="Tendang / oper bola ke sahabatmu"
                            >
                                <span class="text-xs">🚀</span>
                                <span>Oper Bola</span>
                            </button>
                        </div>
                    </div>

                    <!-- ========================================================= -->
                    <!-- INTERACTIVE BOUNCY RUBBER BALL WITH REAL PHYSICS -->
                    <!-- ========================================================= -->
                    <div 
                        x-show="!isSleeping && ballEnabled"
                        @pointerdown="startDragBall($event)"
                        @click="kickBall()"
                        class="absolute z-30 cursor-grab active:cursor-grabbing select-none transition-transform filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.6)] hover:scale-110 active:scale-95 touch-none group"
                        :style="`left: ${ball.x}%; top: ${ball.y}%; transform: translate(-50%, -50%) rotate(${ball.x * 6}deg);`"
                        title="Bola Karet Interaktif (Klik / Seret untuk oper, atau klik Berhenti pada tombol di kiri)"
                    >
                        <div class="relative w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-tr from-amber-400 via-rose-500 to-indigo-600 p-0.5 border-2 border-white shadow-xl flex items-center justify-center">
                            <span class="text-xl sm:text-2xl filter drop-shadow">⚽</span>
                            <div class="absolute top-1 left-1.5 w-3 h-1.5 rounded-full bg-white/70 rotate-[-30deg]"></div>
                        </div>
                        <!-- Mini Combo Badge above ball when combo > 0 -->
                        <template x-if="ball.combo > 0">
                            <div class="absolute -top-5 left-1/2 -translate-x-1/2 px-1.5 py-0.2 rounded-md bg-slate-900/90 border border-sky-400 text-[9px] font-black text-sky-300 whitespace-nowrap shadow pointer-events-none">
                                x<span x-text="ball.combo"></span>
                            </div>
                        </template>
                        <div 
                            class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-8 h-2 rounded-full bg-black/40 blur-[1px] pointer-events-none"
                            :style="`opacity: ${Math.max(0.1, 1 - (74 - ball.y) / 40)}; transform: translateX(-50%) scale(${Math.max(0.4, 1 - (74 - ball.y) / 50)});`"
                        ></div>
                    </div>
                </div>

                <!-- Floating Interactive Particles (Hearts, Bubbles, Crumbs) -->
                <!-- 1. Floating Hearts -->
                <template x-for="heart in floatingHearts" :key="heart.id">
                    <div 
                        class="heart-bubble-anim absolute z-40 text-2xl sm:text-3xl pointer-events-none select-none drop-shadow"
                        :style="`left: ${heart.left}%; top: 45%;`"
                    >
                        💖
                    </div>
                </template>

                <!-- 2. Bathing Soap Bubbles -->
                <template x-for="bubble in bubbles" :key="bubble.id">
                    <div 
                        class="bubble-anim absolute z-40 rounded-full border-2 border-sky-300/80 bg-sky-400/20 backdrop-blur-xs pointer-events-none select-none shadow-[0_0_12px_rgba(56,189,248,0.5)] flex items-center justify-center"
                        :style="`left: ${bubble.left}%; top: 50%; width: ${bubble.size}px; height: ${bubble.size}px; animation-delay: ${bubble.delay}s;`"
                    >
                        <span class="w-1 h-1 rounded-full bg-white absolute top-1 left-1 opacity-80"></span>
                    </div>
                </template>

                <!-- 3. Feeding Crumbs -->
                <template x-for="crumb in crumbs" :key="crumb.id">
                    <div 
                        class="crumb-anim absolute z-40 w-2 h-2 rounded-full pointer-events-none select-none"
                        :style="`left: ${crumb.left}%; top: 52%; background-color: ${crumb.color};`"
                    ></div>
                </template>

                <!-- 4. Flying Food Snack to Mouth -->
                <template x-if="flyingFood">
                    <div 
                        class="flying-food-anim absolute z-40 text-4xl sm:text-5xl pointer-events-none select-none left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.8)]"
                        x-text="flyingFood"
                    ></div>
                </template>

                <!-- 4.1 Handheld Eating Snack at mouth while Chewing -->
                <template x-if="eatingFoodItem">
                    <div 
                        class="food-bite-anim absolute z-40 text-4xl sm:text-5xl pointer-events-none select-none left-1/2 top-[55%] filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.8)]"
                        x-text="eatingFoodItem"
                    ></div>
                </template>

                <!-- 4.2 Handheld Coffee Mug & Rising Steam while Drinking -->
                <template x-if="petDrinking">
                    <div class="cup-sip-anim absolute z-40 left-1/2 top-[56%] pointer-events-none select-none flex flex-col items-center">
                        <div class="flex gap-1 text-sm text-amber-300 font-bold steam-rise-anim -mb-1">
                            <span>♨️</span>
                            <span style="animation-delay: 0.25s">♨️</span>
                        </div>
                        <span class="text-4xl sm:text-5xl filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.7)]">☕</span>
                    </div>
                </template>

                <!-- 4.3 Energized Lightning Aura while Drinking -->
                <template x-if="drinkingAura">
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center z-35 overflow-hidden">
                        <div class="w-64 h-64 rounded-full bg-amber-400/20 border-2 border-amber-300/80 shadow-[0_0_60px_rgba(245,158,11,0.9)] energy-pulse-anim"></div>
                        <span class="absolute top-[35%] left-[30%] text-3xl text-amber-300 animate-bounce drop-shadow">⚡</span>
                        <span class="absolute top-[38%] right-[30%] text-3xl text-yellow-300 animate-ping drop-shadow">⚡</span>
                        <span class="absolute bottom-[35%] left-[32%] text-2xl text-amber-400 animate-pulse drop-shadow">✨</span>
                        <span class="absolute bottom-[35%] right-[32%] text-2xl text-yellow-400 animate-bounce drop-shadow">⚡</span>
                    </div>
                </template>

                <!-- 4.4 Sweeping Broom & Dust Puffs & Sparkle Wave during Piket -->
                <template x-if="petSweeping">
                    <div class="absolute inset-0 z-40 pointer-events-none overflow-hidden">
                        <!-- Cartoon Flying Broom -->
                        <div class="absolute bottom-16 left-1/2 broom-sweep-anim flex flex-col items-center">
                            <span class="text-6xl sm:text-7xl filter drop-shadow-2xl select-none">🧹</span>
                        </div>

                        <!-- Dust Puffs -->
                        <template x-for="dust in dustPuffs" :key="dust.id">
                            <div 
                                class="dust-puff-anim absolute text-3xl select-none drop-shadow"
                                :style="`left: ${dust.left}%; bottom: ${dust.bottom}%;`"
                            >
                                💨
                            </div>
                        </template>

                        <!-- Room Clean Shine Wave -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent room-shine-anim pointer-events-none"></div>

                        <!-- Sparkles all over room -->
                        <template x-for="sp in sweepingSparkles" :key="sp.id">
                            <div 
                                class="sparkle-pop-anim absolute text-3xl sm:text-4xl filter drop-shadow"
                                :style="`left: ${sp.left}%; top: ${sp.top}%;`"
                                x-text="sp.icon"
                            ></div>
                        </template>
                    </div>
                </template>

                <!-- 5. Musical Notes from Radio -->
                <template x-for="note in flyingNotes" :key="note.id">
                    <div 
                        class="music-note-anim absolute z-40 text-xl font-black pointer-events-none select-none drop-shadow"
                        :style="`left: ${note.left}%; top: 22%; color: ${note.color};`"
                        x-text="note.char"
                    ></div>
                </template>

                <!-- 6. Universal Floating Reward Badges -->
                <template x-for="text in floatingTexts" :key="text.id">
                    <div 
                        class="floating-badge absolute z-50 pointer-events-none select-none flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/95 border shadow-2xl backdrop-blur-md text-xs sm:text-sm font-black whitespace-nowrap"
                        :style="`left: ${text.left}%; top: ${text.top}%; border-color: ${text.color}; color: ${text.color};`"
                    >
                        <span x-text="text.icon"></span>
                        <span x-text="text.text"></span>
                    </div>
                </template>

                <!-- ========================================================= -->
                <!-- MASCOT CENTERING ANCHOR (ROCK-SOLID CENTERED WRAPPER) -->
                <!-- ========================================================= -->
                <div 
                    style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);"
                    class="z-20 flex flex-col items-center justify-center select-none touch-none"
                >
                    <!-- Floating Speech Bubble (Positioned directly over Pet's head) -->
                    <div class="relative -mb-2 z-30 pointer-events-none max-w-[190px] sm:max-w-xs text-center whitespace-normal">
                        <template x-if="isMusicPlaying && !isSleeping && currentLyricLine">
                            <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1 bg-gradient-to-r from-purple-950/95 via-indigo-950/95 to-slate-900/95 border-2 border-purple-400/80 rounded-xl shadow-[0_0_20px_rgba(168,85,247,0.5)] backdrop-blur-md">
                                <span class="text-xs animate-bounce">🎤</span>
                                <div class="text-left min-w-0">
                                    <span class="text-[7px] font-black text-purple-300 uppercase tracking-wider block">
                                        {{ $pet->name }} ikut nyanyi:
                                    </span>
                                    <p class="text-[10px] sm:text-xs font-black text-white italic drop-shadow truncate max-w-[140px] sm:max-w-xs" x-text="currentLyricLine"></p>
                                </div>
                                <span class="text-xs text-yellow-300 animate-pulse">🎶</span>
                            </div>
                        </template>
                        <template x-if="!isMusicPlaying || isSleeping || !currentLyricLine">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-900/95 border border-slate-700/80 rounded-xl shadow-xl backdrop-blur-md">
                                <span class="text-xs">
                                    @if($pet->is_sleeping) 💤
                                    @elseif($pet->hunger <= 25) 🥺
                                    @elseif($pet->hygiene <= 25) 🫧
                                    @else ✨
                                    @endif
                                </span>
                                <p class="text-[10px] sm:text-xs font-bold text-slate-100 leading-tight">
                                    {{ $petSpeech ?: $pet->meta['greeting'] }}
                                </p>
                            </div>
                        </template>
                        <div class="w-2 h-2 bg-slate-900 border-r border-b border-slate-700/80 rotate-45 mx-auto -mt-1 shadow" :class="(isMusicPlaying && !isSleeping && currentLyricLine) ? 'border-purple-400 bg-purple-950' : ''"></div>
                    </div>

                    <!-- Interactive Animated Mascot Body (Holds animations without breaking centering) -->
                    <div 
                        @click="triggerPetTap()"
                        @pointerdown="startRub($event)"
                        @pointermove="doRub($event)"
                        @pointerup="endRub()"
                        @pointerleave="endRub()"
                        :class="{ 
                            'mascot-jump-anim': petJumping, 
                            'mascot-dance': petDancing && !petJumping, 
                            'mascot-flip': petFlipping, 
                            'mascot-drink-anim': petDrinking,
                            'mascot-chomp-anim': petEating,
                            'mascot-sweep-anim': petSweeping,
                            'mascot-idle': !petJumping && !petDancing && !petFlipping && !petDrinking && !petEating && !petSweeping
                        }"
                        :style="petWiggleAngle ? `transform: rotate(${petWiggleAngle}deg) scale(1.08);` : ''"
                        class="cursor-pointer select-none transition-transform duration-150 flex flex-col items-center justify-center group"
                        title="Ajak bercanda, tos bareng, atau oper bola ke sahabatmu!"
                    >
                        <!-- Sleeping ZZZ Effect -->
                        @if($pet->is_sleeping)
                            <div class="absolute -top-6 right-6 text-indigo-300 font-black text-sm zzz-1 select-none pointer-events-none drop-shadow">Z</div>
                            <div class="absolute -top-10 right-12 text-indigo-200 font-black text-base zzz-2 select-none pointer-events-none drop-shadow">Z</div>
                            <div class="absolute -top-14 right-18 text-white font-black text-xl zzz-3 select-none pointer-events-none drop-shadow">Z</div>
                        @endif

                        <!-- DETAILED VECTOR MASCOT RENDERING (SVG ARTWORK) -->
                        <div class="w-36 h-36 sm:w-52 sm:h-52 relative flex items-center justify-center filter drop-shadow-[0_12px_24px_rgba(0,0,0,0.6)] group-hover:scale-105 transition-transform duration-200">
                        
                        <!-- Cartoon Handheld Karaoke Microphone & Note Sparkles -->
                        <template x-if="isMusicPlaying && !isSleeping">
                            <div class="absolute -bottom-1 right-1 sm:right-3 z-30 animate-bounce filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.8)] pointer-events-none select-none">
                                <span class="text-3xl sm:text-4xl">🎤</span>
                                <span class="absolute -top-3 -right-2 text-xs sm:text-sm text-yellow-300 animate-ping">✨</span>
                            </div>
                        </template>
                        @if($pet->pet_type === 'kibo')
                            <!-- ============================================= -->
                            <!-- 🐱 KIBO (KUCING ORANYE SANTRI BERPECI EMERALD) -->
                            <!-- ============================================= -->
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <defs>
                                    <linearGradient id="kiboFur" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#fb923c" />
                                        <stop offset="60%" stop-color="#f97316" />
                                        <stop offset="100%" stop-color="#ea580c" />
                                    </linearGradient>
                                    <linearGradient id="kiboBelly" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#fef3c7" />
                                        <stop offset="100%" stop-color="#fde68a" />
                                    </linearGradient>
                                    <linearGradient id="peciGreen" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#065f46" />
                                        <stop offset="70%" stop-color="#047857" />
                                        <stop offset="100%" stop-color="#064e3b" />
                                    </linearGradient>
                                </defs>

                                <!-- Tail -->
                                <path d="M 150 145 C 180 140 185 105 170 95 C 160 88 152 98 158 110 C 163 120 152 135 142 140" fill="none" stroke="url(#kiboFur)" stroke-width="12" stroke-linecap="round" />

                                <!-- Body -->
                                <ellipse cx="100" cy="135" rx="55" ry="45" fill="url(#kiboFur)" />
                                <!-- Belly -->
                                <ellipse cx="100" cy="142" rx="35" ry="30" fill="url(#kiboBelly)" />

                                <!-- Ears -->
                                <polygon points="55,80 40,30 80,60" fill="url(#kiboFur)" />
                                <polygon points="56,74 46,38 74,60" fill="#f472b6" />
                                
                                <polygon points="145,80 160,30 120,60" fill="url(#kiboFur)" />
                                <polygon points="144,74 154,38 126,60" fill="#f472b6" />

                                <!-- Head -->
                                <ellipse cx="100" cy="95" rx="52" ry="44" fill="url(#kiboFur)" />

                                <!-- Tiger/Tabby Stripes -->
                                <path d="M 94 56 L 100 68 L 106 56" fill="none" stroke="#c2410c" stroke-width="3" stroke-linecap="round" />
                                <path d="M 86 62 L 92 70" fill="none" stroke="#c2410c" stroke-width="2.5" stroke-linecap="round" />
                                <path d="M 114 62 L 108 70" fill="none" stroke="#c2410c" stroke-width="2.5" stroke-linecap="round" />

                                <!-- Cute Red Collar with Gold Bell -->
                                <g transform="translate(0, 5)">
                                    <path d="M 70 138 Q 100 148 130 138" stroke="#ef4444" stroke-width="6" fill="none" stroke-linecap="round" />
                                    <circle cx="100" cy="144" r="5" fill="#facc15" stroke="#ca8a04" stroke-width="1.5" />
                                    <circle cx="100" cy="144" r="1.5" fill="#713f12" />
                                </g>

                                <!-- Cheeks Blush -->
                                <ellipse cx="65" cy="108" rx="8" ry="5" fill="#f43f5e" opacity="0.45" />
                                <ellipse cx="135" cy="108" rx="8" ry="5" fill="#f43f5e" opacity="0.45" />

                                <!-- Eyes with Interactive Pupil Tracking -->
                                @if($pet->is_sleeping)
                                    <path d="M 68 95 Q 78 103 88 95" fill="none" stroke="#0f172a" stroke-width="3.5" stroke-linecap="round" />
                                    <path d="M 112 95 Q 122 103 132 95" fill="none" stroke="#0f172a" stroke-width="3.5" stroke-linecap="round" />
                                @elseif($pet->mood_status === 'sad' || $pet->hunger <= 25)
                                    <ellipse cx="78" cy="96" rx="9" ry="11" fill="#0f172a" />
                                    <circle cx="76" cy="93" r="3.5" fill="#ffffff" />
                                    <ellipse cx="122" cy="96" rx="9" ry="11" fill="#0f172a" />
                                    <circle cx="120" cy="93" r="3.5" fill="#ffffff" />
                                    <circle cx="70" cy="106" r="2.5" fill="#38bdf8" />
                                @else
                                    <g class="eye-blinker">
                                        <!-- Left Eye Socket -->
                                        <ellipse cx="78" cy="94" rx="9.5" ry="12" fill="#ffffff" stroke="#cbd5e1" stroke-width="0.8" />
                                        <!-- Left Pupil (Tracking Pointer) -->
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <ellipse cx="78" cy="94" rx="7.5" ry="10" fill="#0f172a" />
                                            <circle cx="76" cy="91" r="3" fill="#ffffff" />
                                            <circle cx="81" cy="97" r="1.5" fill="#ffffff" />
                                        </g>

                                        <!-- Right Eye Socket -->
                                        <ellipse cx="122" cy="94" rx="9.5" ry="12" fill="#ffffff" stroke="#cbd5e1" stroke-width="0.8" />
                                        <!-- Right Pupil (Tracking Pointer) -->
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <ellipse cx="122" cy="94" rx="7.5" ry="10" fill="#0f172a" />
                                            <circle cx="120" cy="91" r="3" fill="#ffffff" />
                                            <circle cx="125" cy="97" r="1.5" fill="#ffffff" />
                                        </g>
                                    </g>
                                @endif

                                <!-- Nose -->
                                <polygon points="96,104 104,104 100,108" fill="#f472b6" />

                                <!-- Mouth -->
                                <template x-if="petEating || petDrinking || (isMusicPlaying && !isSleeping && petSingingMouthOpen)">
                                    <ellipse cx="100" cy="116" rx="9" ry="7" fill="#be123c" />
                                </template>
                                <template x-if="!petEating && !petDrinking && (!isMusicPlaying || isSleeping || !petSingingMouthOpen)">
                                    <path d="M 94 109 Q 100 115 106 109" fill="none" stroke="#0f172a" stroke-width="2.5" stroke-linecap="round" />
                                </template>

                                <!-- Whiskers -->
                                <line x1="50" y1="102" x2="30" y2="100" stroke="#78350f" stroke-width="2" stroke-linecap="round" />
                                <line x1="50" y1="108" x2="32" y2="112" stroke="#78350f" stroke-width="2" stroke-linecap="round" />
                                <line x1="150" y1="102" x2="170" y2="100" stroke="#78350f" stroke-width="2" stroke-linecap="round" />
                                <line x1="150" y1="108" x2="168" y2="112" stroke="#78350f" stroke-width="2" stroke-linecap="round" />

                                <!-- Paws -->
                                <ellipse cx="80" cy="165" rx="14" ry="10" fill="url(#kiboBelly)" stroke="#ea580c" stroke-width="1.5" />
                                <ellipse cx="120" cy="165" rx="14" ry="10" fill="url(#kiboBelly)" stroke="#ea580c" stroke-width="1.5" />
                            </svg>

                        @elseif($pet->pet_type === 'bunbun')
                            <!-- ============================================= -->
                            <!-- 🐰 BUNBUN (KELINCI CERDIK DENGAN KACAMATA) -->
                            <!-- ============================================= -->
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <defs>
                                    <linearGradient id="bunFur" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#ffffff" />
                                        <stop offset="70%" stop-color="#f8fafc" />
                                        <stop offset="100%" stop-color="#e2e8f0" />
                                    </linearGradient>
                                </defs>

                                <!-- Long Ears with Slight Twitch -->
                                <g>
                                    <ellipse cx="70" cy="40" rx="14" ry="36" fill="url(#bunFur)" transform="rotate(-8 70 40)" stroke="#cbd5e1" stroke-width="1.5" />
                                    <ellipse cx="70" cy="40" rx="7" ry="26" fill="#fbcfe8" transform="rotate(-8 70 40)" />
                                    
                                    <ellipse cx="130" cy="40" rx="14" ry="36" fill="url(#bunFur)" transform="rotate(8 130 40)" stroke="#cbd5e1" stroke-width="1.5" />
                                    <ellipse cx="130" cy="40" rx="7" ry="26" fill="#fbcfe8" transform="rotate(8 130 40)" />
                                </g>

                                <!-- Body -->
                                <ellipse cx="100" cy="138" rx="52" ry="42" fill="url(#bunFur)" stroke="#cbd5e1" stroke-width="1.5" />
                                
                                <!-- Head -->
                                <ellipse cx="100" cy="96" rx="50" ry="42" fill="url(#bunFur)" stroke="#cbd5e1" stroke-width="1.5" />

                                <!-- Blush -->
                                <ellipse cx="66" cy="110" rx="9" ry="6" fill="#fda4af" opacity="0.6" />
                                <ellipse cx="134" cy="110" rx="9" ry="6" fill="#fda4af" opacity="0.6" />

                                <!-- Glasses (Cerdik Style) -->
                                <g stroke="#3b82f6" stroke-width="3" fill="none">
                                    <circle cx="76" cy="94" r="14" />
                                    <circle cx="124" cy="94" r="14" />
                                    <line x1="90" y1="94" x2="110" y2="94" />
                                </g>

                                <!-- Eyes with Pointer Tracking -->
                                @if($pet->is_sleeping)
                                    <path d="M 70 95 Q 76 101 82 95" fill="none" stroke="#0f172a" stroke-width="3" stroke-linecap="round" />
                                    <path d="M 118 95 Q 124 101 130 95" fill="none" stroke="#0f172a" stroke-width="3" stroke-linecap="round" />
                                @else
                                    <g class="eye-blinker">
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <ellipse cx="76" cy="94" rx="6" ry="8" fill="#0f172a" />
                                            <circle cx="74" cy="92" r="2.5" fill="#ffffff" />
                                        </g>
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <ellipse cx="124" cy="94" rx="6" ry="8" fill="#0f172a" />
                                            <circle cx="122" cy="92" r="2.5" fill="#ffffff" />
                                        </g>
                                    </g>
                                @endif

                                <!-- Nose & Bunny Teeth -->
                                <polygon points="97,105 103,105 100,108" fill="#f43f5e" />
                                <template x-if="petEating || petDrinking || (isMusicPlaying && !isSleeping && petSingingMouthOpen)">
                                    <ellipse cx="100" cy="115" rx="8" ry="6" fill="#be123c" />
                                </template>
                                <template x-if="!petEating && !petDrinking && (!isMusicPlaying || isSleeping || !petSingingMouthOpen)">
                                    <g>
                                        <path d="M 96 109 Q 100 113 104 109" fill="none" stroke="#0f172a" stroke-width="2" />
                                        <rect x="98" y="111" width="4" height="4" fill="#ffffff" stroke="#cbd5e1" stroke-width="0.8" rx="0.5" />
                                    </g>
                                </template>

                                <!-- Paws -->
                                <ellipse cx="80" cy="165" rx="14" ry="10" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5" />
                                <ellipse cx="120" cy="165" rx="14" ry="10" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5" />
                            </svg>

                        @elseif($pet->pet_type === 'pansan' || $pet->pet_type === 'bambu')
                            <!-- ============================================= -->
                            <!-- 🐼 PANSAN (PANDA SANTAI & IMUT) -->
                            <!-- ============================================= -->
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <!-- Black Ears -->
                                <circle cx="56" cy="62" r="18" fill="#1e293b" />
                                <circle cx="144" cy="62" r="18" fill="#1e293b" />

                                <!-- Body -->
                                <ellipse cx="100" cy="138" rx="58" ry="46" fill="#f8fafc" />
                                <!-- Black Arms/Shoulder Band -->
                                <path d="M 44 125 C 44 110, 156 110, 156 125 C 156 145, 135 155, 100 155 C 65 155, 44 145, 44 125 Z" fill="#1e293b" />
                                <!-- White Chubby Tummy -->
                                <ellipse cx="100" cy="144" rx="32" ry="26" fill="#ffffff" />

                                <!-- Head -->
                                <ellipse cx="100" cy="96" rx="53" ry="45" fill="#ffffff" />

                                <!-- Black Eye Patches -->
                                <ellipse cx="74" cy="94" rx="17" ry="15" fill="#1e293b" transform="rotate(-15 74 94)" />
                                <ellipse cx="126" cy="94" rx="17" ry="15" fill="#1e293b" transform="rotate(15 126 94)" />

                                <!-- Eyes inside patches with Pointer Tracking -->
                                @if($pet->is_sleeping)
                                    <path d="M 68 94 Q 74 99 80 94" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" />
                                    <path d="M 120 94 Q 126 99 132 94" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" />
                                @else
                                    <g class="eye-blinker">
                                        <ellipse cx="74" cy="94" rx="7" ry="9" fill="#ffffff" />
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <circle cx="75" cy="94" r="4.5" fill="#0f172a" />
                                            <circle cx="73" cy="92" r="1.5" fill="#ffffff" />
                                        </g>

                                        <ellipse cx="126" cy="94" rx="7" ry="9" fill="#ffffff" />
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <circle cx="125" cy="94" r="4.5" fill="#0f172a" />
                                            <circle cx="123" cy="92" r="1.5" fill="#ffffff" />
                                        </g>
                                    </g>
                                @endif

                                <!-- Snout & Content Lazy Smile -->
                                <ellipse cx="100" cy="106" rx="15" ry="11" fill="#f1f5f9" />
                                <ellipse cx="100" cy="104" rx="6.5" ry="4.5" fill="#0f172a" />
                                <template x-if="petEating || petDrinking || (isMusicPlaying && !isSleeping && petSingingMouthOpen)">
                                    <ellipse cx="100" cy="114" rx="8" ry="6" fill="#be123c" />
                                </template>
                                <template x-if="!petEating && !petDrinking && (!isMusicPlaying || isSleeping || !petSingingMouthOpen)">
                                    <path d="M 95 109 Q 100 114 105 109" fill="none" stroke="#0f172a" stroke-width="2.2" />
                                </template>

                                <!-- Bamboo Shoot / Leaf in Head -->
                                <g transform="translate(132, 54) rotate(25)">
                                    <path d="M 0 0 C 12 -5, 20 5, 24 16 C 14 14, 5 8, 0 0 Z" fill="#22c55e" stroke="#15803d" stroke-width="1.5" />
                                </g>

                                <!-- Paws -->
                                <circle cx="74" cy="166" r="15" fill="#1e293b" />
                                <circle cx="126" cy="166" r="15" fill="#1e293b" />
                            </svg>

                        @else
                            <!-- ============================================= -->
                            <!-- 🐧 PINGO (PENGUIN DISIPLIN & POLOS) -->
                            <!-- ============================================= -->
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <defs>
                                    <linearGradient id="pingoBody" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#1e293b" />
                                        <stop offset="100%" stop-color="#0f172a" />
                                    </linearGradient>
                                </defs>

                                <!-- Flippers/Wings -->
                                <ellipse cx="48" cy="125" rx="12" ry="32" fill="#0f172a" transform="rotate(18 48 125)" />
                                <ellipse cx="152" cy="125" rx="12" ry="32" fill="#0f172a" transform="rotate(-18 152 125)" />

                                <!-- Body -->
                                <ellipse cx="100" cy="125" rx="52" ry="54" fill="url(#pingoBody)" />
                                
                                <!-- White Belly -->
                                <ellipse cx="100" cy="132" rx="36" ry="42" fill="#ffffff" />

                                <!-- Cute Feather Tuft on Head -->
                                <path d="M 98 48 Q 100 38 106 36 Q 104 44 100 48" fill="#1e293b" />
                                <path d="M 95 49 Q 93 39 88 38 Q 93 45 97 49" fill="#1e293b" />

                                <!-- Cheeks -->
                                <ellipse cx="68" cy="110" rx="7" ry="5" fill="#f43f5e" opacity="0.5" />
                                <ellipse cx="132" cy="110" rx="7" ry="5" fill="#f43f5e" opacity="0.5" />

                                <!-- Eyes with Pointer Tracking -->
                                @if($pet->is_sleeping)
                                    <path d="M 72 95 Q 80 101 88 95" fill="none" stroke="#0f172a" stroke-width="3" stroke-linecap="round" />
                                    <path d="M 112 95 Q 120 101 128 95" fill="none" stroke="#0f172a" stroke-width="3" stroke-linecap="round" />
                                @else
                                    <g class="eye-blinker">
                                        <!-- Left Eye Socket -->
                                        <ellipse cx="80" cy="94" rx="9" ry="12" fill="#ffffff" stroke="#cbd5e1" stroke-width="0.8" />
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <ellipse cx="80" cy="94" rx="7" ry="9.5" fill="#0f172a" />
                                            <circle cx="78" cy="91" r="3" fill="#ffffff" />
                                        </g>

                                        <!-- Right Eye Socket -->
                                        <ellipse cx="120" cy="94" rx="9" ry="12" fill="#ffffff" stroke="#cbd5e1" stroke-width="0.8" />
                                        <g :style="`transform: translate(${eyeX}px, ${eyeY}px);`">
                                            <ellipse cx="120" cy="94" rx="7" ry="9.5" fill="#0f172a" />
                                            <circle cx="118" cy="91" r="3" fill="#ffffff" />
                                        </g>
                                    </g>
                                @endif

                                <!-- Beak -->
                                <template x-if="petEating || petDrinking || (isMusicPlaying && !isSleeping && petSingingMouthOpen)">
                                    <g>
                                        <polygon points="88,103 112,103 100,113" fill="#f59e0b" stroke="#d97706" stroke-width="1.5" />
                                        <ellipse cx="100" cy="114" rx="7" ry="5" fill="#be123c" />
                                        <polygon points="92,115 108,115 100,123" fill="#d97706" />
                                    </g>
                                </template>
                                <template x-if="!petEating && !petDrinking && (!isMusicPlaying || isSleeping || !petSingingMouthOpen)">
                                    <polygon points="90,105 110,105 100,118" fill="#f59e0b" stroke="#d97706" stroke-width="1.5" />
                                </template>

                                <!-- Little Santri Bow/Tie -->
                                <polygon points="94,124 100,128 94,132" fill="#059669" />
                                <polygon points="106,124 100,128 106,132" fill="#059669" />
                                <circle cx="100" cy="128" r="2.5" fill="#fbbf24" />

                                <!-- Feet -->
                                <ellipse cx="78" cy="174" rx="16" ry="8" fill="#f59e0b" stroke="#d97706" stroke-width="1.5" />
                                <ellipse cx="122" cy="174" rx="16" ry="8" fill="#f59e0b" stroke="#d97706" stroke-width="1.5" />
                            </svg>
                        @endif

                        @php
                            $eqHead = $pet->getEquippedItem('head');
                            $eqEyes = $pet->getEquippedItem('eyes');
                            $eqBody = $pet->getEquippedItem('body');
                            $eqProp = $pet->getEquippedItem('prop');
                        @endphp

                        <!-- MODULAR SVG OUTFIT & COSMETIC OVERLAY -->
                        @if($eqHead || $eqEyes || $eqBody || $eqProp)
                            <svg viewBox="0 0 200 200" class="absolute inset-0 w-full h-full pointer-events-none z-30 select-none">
                                <defs>
                                    <linearGradient id="crownGold" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#fef08a" />
                                        <stop offset="50%" stop-color="#facc15" />
                                        <stop offset="100%" stop-color="#ca8a04" />
                                    </linearGradient>
                                    <linearGradient id="kaisarGold" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#fef9c3" />
                                        <stop offset="40%" stop-color="#fbbf24" />
                                        <stop offset="100%" stop-color="#b45309" />
                                    </linearGradient>
                                </defs>

                                <!-- 1. BODY LAYER -->
                                @if($eqBody === 'sarung_wadimor')
                                    <g>
                                        <path d="M 68 135 C 68 135 70 178 70 178 C 70 182 130 182 130 178 C 130 178 132 135 132 135 Z" fill="#047857" stroke="#064e3b" stroke-width="1.5" />
                                        <line x1="70" y1="150" x2="130" y2="150" stroke="#f59e0b" stroke-width="1.5" />
                                        <line x1="70" y1="164" x2="130" y2="164" stroke="#f59e0b" stroke-width="1.5" />
                                        <line x1="90" y1="135" x2="90" y2="180" stroke="#f59e0b" stroke-width="1.5" />
                                        <line x1="110" y1="135" x2="110" y2="180" stroke="#f59e0b" stroke-width="1.5" />
                                    </g>
                                @elseif($eqBody === 'baju_koko')
                                    <g>
                                        <path d="M 72 125 L 64 145 L 80 150 L 80 172 L 120 172 L 120 150 L 136 145 L 128 125 Z" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5" />
                                        <rect x="97" y="125" width="6" height="32" fill="#e2e8f0" />
                                        <circle cx="100" cy="132" r="1.5" fill="#0f172a" />
                                        <circle cx="100" cy="142" r="1.5" fill="#0f172a" />
                                        <circle cx="100" cy="152" r="1.5" fill="#0f172a" />
                                    </g>
                                @elseif($eqBody === 'jubah_arab')
                                    <g>
                                        <path d="M 68 124 L 56 160 L 76 178 L 124 178 L 144 160 L 132 124 Z" fill="#0f172a" stroke="#1e293b" stroke-width="1.5" />
                                        <path d="M 88 124 L 100 148 L 112 124" fill="none" stroke="#eab308" stroke-width="2.5" />
                                        <line x1="100" y1="148" x2="100" y2="178" stroke="#eab308" stroke-width="2" />
                                    </g>
                                @elseif($eqBody === 'sarung_bhs')
                                    <g>
                                        <path d="M 68 135 C 68 135 70 178 70 178 C 70 182 130 182 130 178 C 130 178 132 135 132 135 Z" fill="#581c87" stroke="#3b0764" stroke-width="1.5" />
                                        <polygon points="100,148 108,156 100,164 92,156" fill="#facc15" stroke="#b45309" stroke-width="1" />
                                        <polygon points="80,148 88,156 80,164 72,156" fill="#facc15" stroke="#b45309" stroke-width="1" />
                                        <polygon points="120,148 128,156 120,164 112,156" fill="#facc15" stroke="#b45309" stroke-width="1" />
                                    </g>
                                @elseif($eqBody === 'jas_tuxedo')
                                    <g>
                                        <path d="M 68 125 L 58 158 L 78 176 L 122 176 L 142 158 L 132 125 Z" fill="#09090b" stroke="#27272a" stroke-width="1.5" />
                                        <polygon points="90,125 110,125 100,146" fill="#ffffff" />
                                        <polygon points="95,128 100,131 95,134" fill="#ef4444" />
                                        <polygon points="105,128 100,131 105,134" fill="#ef4444" />
                                        <circle cx="100" cy="131" r="1.5" fill="#b91c1c" />
                                    </g>
                                @elseif($eqBody === 'jubah_kaisar')
                                    <g>
                                        <path d="M 62 122 L 50 168 L 72 182 L 128 182 L 150 168 L 138 122 Z" fill="url(#kaisarGold)" stroke="#f59e0b" stroke-width="2" />
                                        <circle cx="100" cy="138" r="4" fill="#38bdf8" stroke="#ffffff" stroke-width="1.5" />
                                        <line x1="80" y1="145" x2="120" y2="145" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                                        <polygon points="100,154 104,162 100,170 96,162" fill="#ec4899" />
                                    </g>
                                @endif

                                <!-- 2. EYES / WAJAH LAYER -->
                                @if($eqEyes === 'masker_santri')
                                    <g>
                                        <rect x="74" y="96" width="52" height="24" rx="6" fill="#38bdf8" stroke="#0284c7" stroke-width="1.5" opacity="0.95" />
                                        <line x1="78" y1="104" x2="122" y2="104" stroke="#bae6fd" stroke-width="1" />
                                        <line x1="78" y1="112" x2="122" y2="112" stroke="#bae6fd" stroke-width="1" />
                                        <path d="M 74 100 Q 64 96 68 112" fill="none" stroke="#e2e8f0" stroke-width="1.5" />
                                        <path d="M 126 100 Q 136 96 132 112" fill="none" stroke="#e2e8f0" stroke-width="1.5" />
                                    </g>
                                @elseif($eqEyes === 'kacamata_kitab')
                                    <g>
                                        <circle cx="82" cy="92" r="13" fill="none" stroke="#78350f" stroke-width="2.5" />
                                        <circle cx="118" cy="92" r="13" fill="none" stroke="#78350f" stroke-width="2.5" />
                                        <ellipse cx="80" cy="89" rx="7" ry="4" fill="#ffffff" opacity="0.35" transform="rotate(-20 80 89)" />
                                        <ellipse cx="116" cy="89" rx="7" ry="4" fill="#ffffff" opacity="0.35" transform="rotate(-20 116 89)" />
                                        <path d="M 95 90 Q 100 87 105 90" fill="none" stroke="#78350f" stroke-width="2" />
                                    </g>
                                @elseif($eqEyes === 'kacamata_thug')
                                    <g>
                                        <path d="M 66 86 H 97 V 98 H 89 V 102 H 74 V 98 H 66 Z" fill="#000000" />
                                        <path d="M 103 86 H 134 V 98 H 126 V 102 H 111 V 98 H 103 Z" fill="#000000" />
                                        <rect x="97" y="88" width="6" height="4" fill="#000000" />
                                        <rect x="70" y="89" width="3" height="3" fill="#ffffff" />
                                        <rect x="73" y="92" width="3" height="3" fill="#ffffff" />
                                        <rect x="107" y="89" width="3" height="3" fill="#ffffff" />
                                        <rect x="110" y="92" width="3" height="3" fill="#ffffff" />
                                    </g>
                                @elseif($eqEyes === 'monocle_emas')
                                    <g>
                                        <circle cx="118" cy="92" r="13" fill="#fef08a" fill-opacity="0.2" stroke="#eab308" stroke-width="2.5" />
                                        <path d="M 131 92 Q 138 115 125 130" fill="none" stroke="#eab308" stroke-width="1.5" stroke-dasharray="2 1" />
                                    </g>
                                @elseif($eqEyes === 'kacamata_cyberpunk')
                                    <g>
                                        <polygon points="62,84 138,84 134,102 100,106 66,102" fill="#06b6d4" opacity="0.85" stroke="#22d3ee" stroke-width="2" />
                                        <line x1="68" y1="92" x2="132" y2="92" stroke="#ffffff" stroke-width="1.5" opacity="0.9" />
                                        <circle cx="76" cy="97" r="1.5" fill="#f43f5e" />
                                        <circle cx="124" cy="97" r="1.5" fill="#a855f7" />
                                    </g>
                                @endif

                                <!-- 3. HEADWEAR LAYER -->
                                @if($eqHead === 'peci_hitam')
                                    <g>
                                        <rect x="70" y="44" width="60" height="26" rx="4" fill="#0f172a" stroke="#334155" stroke-width="1.5" />
                                        <circle cx="100" cy="56" r="3" fill="#f59e0b" />
                                    </g>
                                @elseif($eqHead === 'kopiah_putih')
                                    <g>
                                        <rect x="68" y="44" width="64" height="26" rx="5" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.5" />
                                        <line x1="72" y1="62" x2="128" y2="62" stroke="#eab308" stroke-width="2" stroke-dasharray="3 2" />
                                    </g>
                                @elseif($eqHead === 'sorban_hijau')
                                    <g>
                                        <rect x="64" y="38" width="72" height="30" rx="14" fill="#065f46" stroke="#047857" stroke-width="2" />
                                        <path d="M 68 56 Q 60 85 64 110 Q 70 85 74 62" fill="#047857" opacity="0.9" />
                                        <polygon points="100,44 105,52 100,60 95,52" fill="#10b981" stroke="#34d399" />
                                    </g>
                                @elseif($eqHead === 'blangkon_solo')
                                    <g>
                                        <path d="M 64 64 C 64 42 76 38 100 38 C 124 38 136 42 136 64 Z" fill="#78350f" stroke="#92400e" stroke-width="1.5" />
                                        <ellipse cx="100" cy="62" rx="36" ry="6" fill="#451a03" />
                                    </g>
                                @elseif($eqHead === 'helm_bogo')
                                    <g>
                                        <path d="M 60 70 C 58 35 142 35 140 70 C 140 85 60 85 60 70 Z" fill="#f43f5e" stroke="#be123c" stroke-width="2" />
                                        <ellipse cx="100" cy="68" rx="32" ry="16" fill="#0f172a" opacity="0.85" stroke="#e2e8f0" stroke-width="1" />
                                        <ellipse cx="94" cy="64" rx="12" ry="4" fill="#ffffff" opacity="0.4" transform="rotate(-10 94 64)" />
                                    </g>
                                @elseif($eqHead === 'mahkota_sultan')
                                    <g>
                                        <path d="M 66 64 L 66 42 L 80 54 L 100 34 L 120 54 L 134 42 L 134 64 Z" fill="url(#crownGold)" stroke="#b45309" stroke-width="2" />
                                        <circle cx="100" cy="48" r="3.5" fill="#ef4444" stroke="#991b1b" />
                                        <circle cx="80" cy="56" r="2.5" fill="#10b981" />
                                        <circle cx="120" cy="56" r="2.5" fill="#10b981" />
                                        <rect x="65" y="60" width="70" height="6" rx="2" fill="#facc15" stroke="#ca8a04" />
                                    </g>
                                @endif

                                <!-- 4. PROPS / AKSESORIS LAYER -->
                                @if($eqProp === 'sapu_lidi')
                                    <g>
                                        <line x1="152" y1="80" x2="162" y2="175" stroke="#78350f" stroke-width="3" stroke-linecap="round" />
                                        <path d="M 158 145 L 150 178 L 175 178 L 165 145 Z" fill="#d97706" stroke="#92400e" stroke-width="1" />
                                        <rect x="156" y="146" width="10" height="4" fill="#ef4444" rx="1" />
                                    </g>
                                @elseif($eqProp === 'tasbih_led')
                                    <g>
                                        <rect x="146" y="128" width="16" height="22" rx="4" fill="#1e293b" stroke="#334155" stroke-width="1.5" />
                                        <rect x="149" y="132" width="10" height="7" rx="1" fill="#22c55e" />
                                        <text x="154" y="137" font-size="5" font-family="monospace" font-weight="bold" fill="#052e16" text-anchor="middle">99</text>
                                        <circle cx="154" cy="144" r="3" fill="#94a3b8" stroke="#475569" stroke-width="1" />
                                    </g>
                                @elseif($eqProp === 'gitar_ambyar')
                                    <g>
                                        <ellipse cx="148" cy="145" rx="14" ry="18" fill="#b45309" stroke="#78350f" stroke-width="1.5" transform="rotate(-25 148 145)" />
                                        <circle cx="148" cy="145" r="5" fill="#451a03" />
                                        <line x1="140" y1="130" x2="125" y2="95" stroke="#78350f" stroke-width="4" stroke-linecap="round" />
                                        <line x1="144" y1="140" x2="127" y2="100" stroke="#fef08a" stroke-width="1" />
                                    </g>
                                @elseif($eqProp === 'termos_kopi')
                                    <g>
                                        <rect x="144" y="130" width="18" height="36" rx="4" fill="#94a3b8" stroke="#475569" stroke-width="1.5" />
                                        <rect x="147" y="132" width="4" height="32" fill="#ffffff" opacity="0.5" />
                                        <rect x="146" y="125" width="14" height="6" rx="2" fill="#0f172a" />
                                        <path d="M 153 120 Q 150 114 153 108" fill="none" stroke="#e2e8f0" stroke-width="1.5" stroke-linecap="round" opacity="0.7" />
                                    </g>
                                @elseif($eqProp === 'lightsaber_santri')
                                    <g>
                                        <line x1="156" y1="126" x2="170" y2="68" stroke="#4ade80" stroke-width="6" stroke-linecap="round" filter="drop-shadow(0 0 4px #22c55e)" />
                                        <line x1="156" y1="126" x2="170" y2="68" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" />
                                        <rect x="150" y="128" width="6" height="18" rx="2" fill="#334155" stroke="#0f172a" stroke-width="1" transform="rotate(-15 153 137)" />
                                    </g>
                                @elseif($eqProp === 'rubicon_emas')
                                    <g>
                                        <rect x="136" y="148" width="36" height="18" rx="3" fill="#facc15" stroke="#ca8a04" stroke-width="1.5" />
                                        <polygon points="144,148 150,140 166,140 170,148" fill="#eab308" stroke="#ca8a04" stroke-width="1.5" />
                                        <polygon points="151,142 165,142 163,147 147,147" fill="#0284c7" opacity="0.8" />
                                        <circle cx="144" cy="168" r="6" fill="#1e293b" stroke="#0f172a" stroke-width="1.5" />
                                        <circle cx="144" cy="168" r="2.5" fill="#facc15" />
                                        <circle cx="164" cy="168" r="6" fill="#1e293b" stroke="#0f172a" stroke-width="1.5" />
                                        <circle cx="164" cy="168" r="2.5" fill="#facc15" />
                                        <path d="M 172 136 L 174 140 L 178 142 L 174 144 L 172 148 L 170 144 L 166 142 L 170 140 Z" fill="#ffffff" />
                                    </g>
                                @endif
                            </svg>
                        @endif

                    </div>

                </div>

            </div>

                <!-- Single Bottom Status Notification Pill -->
                <div 
                    style="position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%);"
                    class="z-20 text-[9px] sm:text-[11px] font-bold px-3 py-0.5 rounded-full border shadow-md {{ $pet->is_sleeping ? 'bg-indigo-950/90 border-indigo-700 text-indigo-300' : 'bg-slate-900/90 border-slate-700 text-slate-200' }} whitespace-nowrap pointer-events-none"
                >
                    @if($pet->is_sleeping)
                        🌙 Sedang tidur lelap (Klik lampu untuk bangunkan)
                    @elseif($pet->hunger <= 25)
                        🍗 Perut keroncongan! Suapkan camilan di bawah...
                    @elseif($pet->hygiene <= 25)
                        🧼 Mandi &amp; piket yuk biar bersih kembali!
                    @elseif($pet->energy <= 25)
                        ⚡ Mulai lelah, yuk tidurkan sejenak...
                    @else
                        ✨ Mood senang &bull; Tap pet untuk ajak main!
                    @endif
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- ACTION DOCK: POU-STYLE INTERACTIVE CARE BUTTONS (3-COL ON MOBILE, 6-COL ON DESKTOP) -->
            <!-- ========================================================= -->
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5 sm:gap-2.5">
                <!-- 1. Ngopi Santai Button -->
                <button 
                    @click="triggerDrinking()"
                    type="button" 
                    class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-amber-600/20 to-orange-600/10 border border-amber-500/40 hover:border-amber-400 text-amber-300 font-black text-xs flex flex-col items-center justify-center gap-0.5 sm:gap-1 transition-all hover:scale-[1.02] active:scale-95 shadow-md cursor-pointer"
                >
                    <span class="text-xl sm:text-2xl filter drop-shadow">☕</span>
                    <span class="text-[11px] sm:text-xs font-black">Ngopi</span>
                    <span class="hidden sm:block text-[9px] font-medium text-amber-400/80">Kopi Susu</span>
                </button>

                <!-- 2. Camilan Enak Button -->
                <button 
                    @click="showFoodDrawer = !showFoodDrawer"
                    type="button" 
                    class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-rose-600/20 to-pink-600/10 border border-rose-500/40 hover:border-rose-400 text-rose-300 font-black text-xs flex flex-col items-center justify-center gap-0.5 sm:gap-1 transition-all hover:scale-[1.02] active:scale-95 shadow-md cursor-pointer"
                >
                    <span class="text-xl sm:text-2xl filter drop-shadow">🍩</span>
                    <span class="text-[11px] sm:text-xs font-black">Makan</span>
                    <span class="hidden sm:block text-[9px] font-medium text-rose-400/80">Tas Camilan</span>
                </button>

                <!-- 3. Piket & Rapi-Rapi Button -->
                <button 
                    @click="triggerPiket()"
                    type="button" 
                    class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-teal-600/20 to-emerald-600/10 border border-teal-500/40 hover:border-teal-400 text-teal-300 font-black text-xs flex flex-col items-center justify-center gap-0.5 sm:gap-1 transition-all hover:scale-[1.02] active:scale-95 shadow-md cursor-pointer"
                >
                    <span class="text-xl sm:text-2xl filter drop-shadow">🧹</span>
                    <span class="text-[11px] sm:text-xs font-black">Piket</span>
                    <span class="hidden sm:block text-[9px] font-medium text-teal-400/80">Bersih Meja</span>
                </button>

                <!-- 4. Membaca Buku Button -->
                <button 
                    @click="playSound('pat'); spawnFloatingText('Wawasan Baru! +8 XP 📚', '#a855f7', '📖'); $wire.readBook()"
                    type="button" 
                    class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-violet-600/20 to-purple-600/10 border border-violet-500/40 hover:border-violet-400 text-violet-300 font-black text-xs flex flex-col items-center justify-center gap-0.5 sm:gap-1 transition-all hover:scale-[1.02] active:scale-95 shadow-md cursor-pointer"
                >
                    <span class="text-xl sm:text-2xl filter drop-shadow">📚</span>
                    <span class="text-[11px] sm:text-xs font-black">Baca</span>
                    <span class="hidden sm:block text-[9px] font-medium text-violet-400/80">Novel &amp; Ilmu</span>
                </button>

                <!-- 5. Tidurkan / Bangunkan Button -->
                <button 
                    @click="triggerSleepToggle()"
                    type="button" 
                    class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-indigo-600/20 to-sky-600/10 border border-indigo-500/40 hover:border-indigo-400 text-indigo-300 font-black text-xs flex flex-col items-center justify-center gap-0.5 sm:gap-1 transition-all hover:scale-[1.02] active:scale-95 shadow-md cursor-pointer"
                >
                    <span class="text-xl sm:text-2xl filter drop-shadow">{{ $pet->is_sleeping ? '💡' : '🌙' }}</span>
                    <span class="text-[11px] sm:text-xs font-black">{{ $pet->is_sleeping ? 'Bangun' : 'Tidur' }}</span>
                    <span class="hidden sm:block text-[9px] font-medium text-indigo-400/80">{{ $pet->is_sleeping ? 'Nyalakan' : 'Padamkan' }}</span>
                </button>

                <!-- 6. Mini-Game Arcade Button -->
                <button 
                    @click="$wire.isMiniGameOpen = true"
                    type="button" 
                    class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-600/20 to-teal-600/10 border border-emerald-500/40 hover:border-emerald-400 text-emerald-300 font-black text-xs flex flex-col items-center justify-center gap-0.5 sm:gap-1 transition-all hover:scale-[1.02] active:scale-95 shadow-md cursor-pointer"
                >
                    <span class="text-xl sm:text-2xl filter drop-shadow">🎮</span>
                    <span class="text-[11px] sm:text-xs font-black">Game</span>
                    <span class="hidden sm:block text-[9px] font-medium text-emerald-400/80">Tangkap Koin</span>
                </button>
            </div>

            <!-- ========================================================= -->
            <!-- FOOD INVENTORY DRAWER (POU FOOD SELECTION) -->
            <!-- ========================================================= -->
            <div 
                x-show="showFoodDrawer" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="p-4 sm:p-5 bg-slate-900/95 border border-slate-700/80 rounded-3xl space-y-3 shadow-2xl backdrop-blur-xl"
            >
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <h5 class="text-xs font-black text-white uppercase tracking-wider flex items-center gap-1.5">
                        <span>🎒</span> Tas Camilan &amp; Minuman Sahabat
                    </h5>
                    <button @click="showFoodDrawer = false" type="button" class="text-xs text-slate-400 hover:text-white cursor-pointer font-bold">
                        ✕ Tutup
                    </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    @php $inv = $pet->inventory ?? []; @endphp
                    @foreach($foodItems as $key => $food)
                        @php $stock = $inv[$key] ?? 0; @endphp
                        <div class="p-3 rounded-2xl border {{ $stock > 0 ? 'bg-slate-800/90 border-slate-700' : 'bg-slate-950/60 border-slate-800/50 opacity-60' }} flex flex-col justify-between gap-2 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-3xl filter drop-shadow">{{ $food['emoji'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black {{ $stock > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800 text-slate-500' }}">
                                    x{{ $stock }}
                                </span>
                            </div>

                            <div>
                                <h6 class="text-xs font-black text-white truncate">{{ $food['name'] }}</h6>
                                <p class="text-[10px] text-emerald-400 font-bold">+{{ $food['hunger'] }}% Kenyang &bull; +{{ $food['xp'] }} XP</p>
                            </div>

                            <button 
                                @click="triggerFeeding('{{ $key }}')" 
                                type="button" 
                                {{ $stock <= 0 ? 'disabled' : '' }}
                                class="w-full py-1.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all {{ $stock > 0 ? 'bg-gradient-to-r from-emerald-600 to-teal-500 hover:brightness-110 text-white cursor-pointer active:scale-95 shadow-md' : 'bg-slate-800 text-slate-500 cursor-not-allowed' }}"
                            >
                                {{ $stock > 0 ? 'Suapkan' : 'Habis' }}
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="text-center pt-1 flex items-center justify-center gap-2">
                    <span class="text-xs text-slate-400">Stok habis?</span>
                    <button wire:click="setViewMode('shop')" type="button" class="text-xs text-amber-400 hover:text-amber-300 font-black underline cursor-pointer">
                        Beli di Toko Camilan →
                    </button>
                </div>
            </div>

        </div>
    @endif



    <!-- ========================================================= -->
    <!-- VIEW MODE: TOKO CAMILAN / SHOP -->
    <!-- ========================================================= -->
    @if($viewMode === 'shop' && $pet)
        <div class="p-4 sm:p-6 space-y-5">
            <!-- Shop Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <h4 class="text-base sm:text-lg font-black text-white font-serif-display uppercase tracking-wider flex items-center gap-2">
                        <span>🏪</span> Toko &amp; Lemari Sahabat ELF
                    </h4>
                    <p class="text-xs text-slate-400 mt-0.5">Beli asupan bergizi atau dandani sahabatmu dengan outfit santri &amp; kosmetik absurd!</p>
                </div>
                <div class="flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-500/10 border border-amber-500/30 text-amber-300 rounded-xl text-xs font-black shadow-xs self-start sm:self-auto">
                    <span class="text-base">🪙</span>
                    <span class="font-mono text-sm">{{ number_format($pet->coins) }} Koin</span>
                </div>
            </div>

            <!-- Sub-Tabs Switcher (Camilan vs Lemari Wardrobe) -->
            <div class="flex items-center gap-2 p-1.5 bg-slate-950 rounded-2xl border border-slate-800">
                <button 
                    wire:click="setShopTab('food')" 
                    type="button" 
                    class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-black transition-all flex items-center justify-center gap-2 cursor-pointer {{ $shopTab === 'food' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}"
                >
                    <span class="text-base sm:text-lg">🍞</span>
                    <span>Camilan &amp; Minuman</span>
                </button>
                <button 
                    wire:click="setShopTab('wardrobe')" 
                    type="button" 
                    class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs sm:text-sm font-black transition-all flex items-center justify-center gap-2 cursor-pointer {{ $shopTab === 'wardrobe' ? 'bg-gradient-to-r from-purple-500 to-indigo-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}"
                >
                    <span class="text-base sm:text-lg">👔</span>
                    <span>Lemari Outfit &amp; Kosmetik Absurd</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono bg-pink-500 text-white font-bold animate-pulse">22 Item</span>
                </button>
            </div>

            <!-- TAB 1: CAMILAN & MINUMAN -->
            @if($shopTab === 'food')
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach($foodItems as $key => $food)
                        @php 
                            $invStock = ($pet->inventory ?? [])[$key] ?? 0;
                            $canAfford = $pet->coins >= $food['price'];
                        @endphp
                        <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-2xl flex flex-col justify-between gap-3 hover:border-amber-500/40 transition-all shadow-lg">
                            <div class="flex items-start justify-between">
                                <span class="text-4xl p-2.5 bg-slate-800/80 rounded-2xl shadow-inner">{{ $food['emoji'] }}</span>
                                <span class="text-xs font-mono font-bold text-slate-400 bg-slate-950 px-2 py-1 rounded-lg border border-slate-800">
                                    Punya: <strong class="text-white">{{ $invStock }}</strong>
                                </span>
                            </div>

                            <div>
                                <h5 class="text-sm font-black text-white">{{ $food['name'] }}</h5>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        +{{ $food['hunger'] }}% Kenyang
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-pink-500/10 text-pink-400 border border-pink-500/20">
                                        +{{ $food['happiness'] }}% Senang
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        +{{ $food['xp'] }} XP
                                    </span>
                                </div>
                            </div>

                            <button 
                                @click="playSound('coin'); $wire.buyItem('{{ $key }}')" 
                                type="button"
                                {{ !$canAfford ? 'disabled' : '' }}
                                class="w-full py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 {{ $canAfford ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 hover:brightness-110 active:scale-95 shadow-md cursor-pointer' : 'bg-slate-800 text-slate-500 cursor-not-allowed' }}"
                            >
                                <span>Beli</span>
                                <span>•</span>
                                <span>🪙 {{ $food['price'] }} Koin</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- TAB 2: WARDROBE & KOSMETIK ABSURD -->
            @if($shopTab === 'wardrobe')
                <div class="space-y-4">
                    <!-- Category Filter Buttons -->
                    <div class="flex items-center gap-1.5 p-1 bg-slate-950 rounded-xl border border-slate-800 flex-wrap sm:flex-nowrap">
                        <button 
                            wire:click="setWardrobeCategoryFilter('all')" 
                            type="button" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-black transition-all cursor-pointer {{ $wardrobeCategoryFilter === 'all' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}"
                        >
                            Semua (22)
                        </button>
                        <button 
                            wire:click="setWardrobeCategoryFilter('head')" 
                            type="button" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-black transition-all cursor-pointer {{ $wardrobeCategoryFilter === 'head' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}"
                        >
                            🧢 Kepala (6)
                        </button>
                        <button 
                            wire:click="setWardrobeCategoryFilter('eyes')" 
                            type="button" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-black transition-all cursor-pointer {{ $wardrobeCategoryFilter === 'eyes' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}"
                        >
                            🕶️ Wajah (5)
                        </button>
                        <button 
                            wire:click="setWardrobeCategoryFilter('body')" 
                            type="button" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-black transition-all cursor-pointer {{ $wardrobeCategoryFilter === 'body' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}"
                        >
                            🥋 Pakaian (6)
                        </button>
                        <button 
                            wire:click="setWardrobeCategoryFilter('prop')" 
                            type="button" 
                            class="flex-1 py-1.5 px-3 rounded-lg text-xs font-black transition-all cursor-pointer {{ $wardrobeCategoryFilter === 'prop' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}"
                        >
                            🎒 Aksesoris (5)
                        </button>
                    </div>

                    <!-- Active Equipped Outfit Summary Bar -->
                    @php
                        $activeHead = $pet->getEquippedItem('head');
                        $activeEyes = $pet->getEquippedItem('eyes');
                        $activeBody = $pet->getEquippedItem('body');
                        $activeProp = $pet->getEquippedItem('prop');
                    @endphp
                    <div class="p-3 sm:p-4 bg-slate-950/80 border border-slate-800 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-inner">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🪞</span>
                            <div>
                                <h6 class="text-xs font-black text-white uppercase tracking-wider">Set Outfit Sedang Dipakai:</h6>
                                <p class="text-[10px] text-slate-400">Klik 'Lepas' pada slot untuk melepas kosmetik dari sahabat</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <!-- Slot Head -->
                            <div class="px-2.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                <div class="min-w-0 truncate text-[11px]">
                                    <span class="text-slate-400 text-[10px] block">Kepala:</span>
                                    <strong class="text-slate-200 truncate block">
                                        {{ $activeHead ? ($allOutfits[$activeHead]['emoji'] ?? '') . ' ' . ($allOutfits[$activeHead]['name'] ?? '') : 'Polos' }}
                                    </strong>
                                </div>
                                @if($activeHead)
                                    <button wire:click="toggleEquipOutfit('{{ $activeHead }}')" type="button" class="text-[10px] px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 hover:bg-rose-500/40 border border-rose-500/30 cursor-pointer shrink-0">
                                        Lepas
                                    </button>
                                @endif
                            </div>

                            <!-- Slot Eyes -->
                            <div class="px-2.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                <div class="min-w-0 truncate text-[11px]">
                                    <span class="text-slate-400 text-[10px] block">Wajah:</span>
                                    <strong class="text-slate-200 truncate block">
                                        {{ $activeEyes ? ($allOutfits[$activeEyes]['emoji'] ?? '') . ' ' . ($allOutfits[$activeEyes]['name'] ?? '') : 'Polos' }}
                                    </strong>
                                </div>
                                @if($activeEyes)
                                    <button wire:click="toggleEquipOutfit('{{ $activeEyes }}')" type="button" class="text-[10px] px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 hover:bg-rose-500/40 border border-rose-500/30 cursor-pointer shrink-0">
                                        Lepas
                                    </button>
                                @endif
                            </div>

                            <!-- Slot Body -->
                            <div class="px-2.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                <div class="min-w-0 truncate text-[11px]">
                                    <span class="text-slate-400 text-[10px] block">Pakaian:</span>
                                    <strong class="text-slate-200 truncate block">
                                        {{ $activeBody ? ($allOutfits[$activeBody]['emoji'] ?? '') . ' ' . ($allOutfits[$activeBody]['name'] ?? '') : 'Polos' }}
                                    </strong>
                                </div>
                                @if($activeBody)
                                    <button wire:click="toggleEquipOutfit('{{ $activeBody }}')" type="button" class="text-[10px] px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 hover:bg-rose-500/40 border border-rose-500/30 cursor-pointer shrink-0">
                                        Lepas
                                    </button>
                                @endif
                            </div>

                            <!-- Slot Prop -->
                            <div class="px-2.5 py-1.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                <div class="min-w-0 truncate text-[11px]">
                                    <span class="text-slate-400 text-[10px] block">Aksesoris:</span>
                                    <strong class="text-slate-200 truncate block">
                                        {{ $activeProp ? ($allOutfits[$activeProp]['emoji'] ?? '') . ' ' . ($allOutfits[$activeProp]['name'] ?? '') : 'Polos' }}
                                    </strong>
                                </div>
                                @if($activeProp)
                                    <button wire:click="toggleEquipOutfit('{{ $activeProp }}')" type="button" class="text-[10px] px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 hover:bg-rose-500/40 border border-rose-500/30 cursor-pointer shrink-0">
                                        Lepas
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Outfits Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($outfitItems as $key => $item)
                            @php
                                $isOwned = $pet->hasOutfit($key);
                                $isEquipped = $pet->isEquipped($key);
                                $canAfford = $pet->coins >= $item['price'];
                            @endphp
                            <div class="p-4 bg-slate-900/90 border rounded-2xl flex flex-col justify-between gap-3 transition-all shadow-lg {{ $isEquipped ? 'border-emerald-500/60 ring-2 ring-emerald-500/20' : ($isOwned ? 'border-indigo-500/40' : 'border-slate-800 hover:border-slate-700') }}">
                                <!-- Top: Emoji & Tier Badge -->
                                <div class="flex items-start justify-between">
                                    <span class="text-4xl p-2.5 bg-slate-800/80 rounded-2xl shadow-inner border border-slate-700/50">
                                        {{ $item['emoji'] }}
                                    </span>
                                    <div class="text-right">
                                        @if($item['tier'] === 'cheap')
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                {{ $item['badge'] }}
                                            </span>
                                        @elseif($item['tier'] === 'mid')
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                                {{ $item['badge'] }}
                                            </span>
                                        @elseif($item['tier'] === 'sultan')
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-xs">
                                                ✨ {{ $item['badge'] }}
                                            </span>
                                        @elseif($item['tier'] === 'insane')
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-gradient-to-r from-rose-500/20 to-purple-500/20 text-rose-300 border border-rose-500/40 animate-pulse">
                                                🔥 {{ $item['badge'] }}
                                            </span>
                                        @endif
                                        <div class="text-[9px] text-slate-500 mt-1 uppercase font-semibold">
                                            @if($item['category'] === 'head') 🧢 Kepala
                                            @elseif($item['category'] === 'eyes') 🕶️ Wajah
                                            @elseif($item['category'] === 'body') 🥋 Pakaian
                                            @else 🎒 Aksesoris
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Middle: Title & Lore Description -->
                                <div>
                                    <h5 class="text-sm font-black text-white flex items-center gap-1.5">
                                        <span>{{ $item['name'] }}</span>
                                    </h5>
                                    <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $item['description'] ?? $item['desc'] ?? '' }}
                                    </p>
                                </div>

                                <!-- Bottom: Status & Action Button -->
                                <div>
                                    @if($isEquipped)
                                        <button 
                                            wire:click="toggleEquipOutfit('{{ $key }}')" 
                                            type="button"
                                            class="w-full py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
                                        >
                                            <span>Sedang Dipakai ✅</span>
                                            <span class="text-[10px] font-normal opacity-85">(Klik Lepas)</span>
                                        </button>
                                    @elseif($isOwned)
                                        <button 
                                            wire:click="toggleEquipOutfit('{{ $key }}')" 
                                            type="button"
                                            class="w-full py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-indigo-600 hover:bg-indigo-500 text-white shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
                                        >
                                            <span>👔 Pakai Sekarang</span>
                                        </button>
                                    @else
                                        <!-- Not Owned: Buy Button -->
                                        @if($canAfford)
                                            <button 
                                                @click="playSound('coin'); $wire.buyOutfitItem('{{ $key }}')" 
                                                type="button"
                                                class="w-full py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 hover:brightness-110 active:scale-95 shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                                            >
                                                <span>Beli</span>
                                                <span>•</span>
                                                <span>🪙 {{ number_format($item['price']) }} Koin</span>
                                            </button>
                                        @else
                                            <button 
                                                type="button"
                                                disabled
                                                class="w-full py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-slate-800 text-slate-500 cursor-not-allowed flex items-center justify-center gap-1.5"
                                            >
                                                <span>🪙 {{ number_format($item['price']) }}</span>
                                                <span>•</span>
                                                <span class="text-[10px]">Koin Kurang</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-center pt-2">
                <button wire:click="setViewMode('arena')" type="button" class="px-6 py-2 rounded-xl text-xs font-extrabold bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer">
                    ← Kembali ke Kamar
                </button>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- VIEW MODE: LEADERBOARD / PERINGKAT ADMIN -->
    <!-- ========================================================= -->
    @if($viewMode === 'leaderboard' && $pet)
        <div class="p-4 sm:p-6 space-y-5">
            <!-- Header & Filter Tabs -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <h4 class="text-base sm:text-lg font-black text-white font-serif-display uppercase tracking-wider flex items-center gap-2">
                        <span>🏆</span> Hall of Fame &amp; Klasemen Sahabat ELF
                    </h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Ajang persaingan sehat &amp; kolaborasi para admin pesantren dalam merawat dan menaikkan level sahabat virtual!
                    </p>
                </div>

                <!-- Sort Filter Buttons -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-950 rounded-xl border border-slate-800 shrink-0">
                    <button 
                        wire:click="setLeaderboardSort('level')" 
                        type="button"
                        class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black transition-all flex items-center gap-1 cursor-pointer {{ $leaderboardSort === 'level' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}"
                    >
                        <span>🏆</span>
                        <span>Level &amp; XP</span>
                    </button>
                    <button 
                        wire:click="setLeaderboardSort('coins')" 
                        type="button"
                        class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black transition-all flex items-center gap-1 cursor-pointer {{ $leaderboardSort === 'coins' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}"
                    >
                        <span>🪙</span>
                        <span>Koin</span>
                    </button>
                    <button 
                        wire:click="setLeaderboardSort('happiness')" 
                        type="button"
                        class="px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-black transition-all flex items-center gap-1 cursor-pointer {{ $leaderboardSort === 'happiness' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}"
                    >
                        <span>💖</span>
                        <span>Ceria</span>
                    </button>
                </div>
            </div>

            <!-- Posisi Pet Kamu (My Standing Banner) -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-amber-500/15 via-purple-500/10 to-slate-900 border border-amber-500/40 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black text-center shadow shrink-0">
                        <span class="text-[9px] uppercase tracking-wider block font-bold text-slate-900/80">Peringkat</span>
                        <span class="text-base sm:text-lg font-black leading-none">#{{ $myRank ?? '-' }}</span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-white truncate">{{ $pet->name }}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-300 font-mono font-bold border border-amber-400/30">
                                Lv. {{ $pet->level }}
                            </span>
                            <span class="text-[9px] text-slate-400 hidden sm:inline">&bull; {{ $pet->rank_title }}</span>
                        </div>
                        <p class="text-xs text-amber-200/90 mt-0.5 font-medium">
                            @if($myRank === 1)
                                👑 <strong>Luar biasa!</strong> Pet kamu memimpin takhta klasemen tertinggi para admin!
                            @elseif($aheadPet)
                                @php 
                                    $xpGap = max(1, ($aheadPet->level * 100 + $aheadPet->xp) - ($pet->level * 100 + $pet->xp));
                                @endphp
                                🚀 Selisih <strong class="text-yellow-300 font-mono">{{ number_format($xpGap) }} XP</strong> untuk menyalip <strong>{{ $aheadPet->name }}</strong> (Admin {{ $aheadPet->user->name ?? 'Admin' }})!
                            @else
                                🌱 Terus rawat pet dan selesaikan tugas sistem untuk memuncaki klasemen!
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0">
                    <button wire:click="setViewMode('arena')" type="button" class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 hover:brightness-110 active:scale-95 transition-all shadow cursor-pointer">
                        🎮 Rawat Pet
                    </button>
                </div>
            </div>

            <!-- Top 3 Mini Podium -->
            @if(count($leaderboardPets) >= 1)
                <div class="grid grid-cols-3 gap-2 sm:gap-4 items-end pt-4 sm:pt-6 pb-2 px-1 sm:px-6">
                    <!-- Juara 2 (🥈 Kiri) -->
                    <div class="flex flex-col items-center text-center min-w-0">
                        @if(isset($leaderboardPets[1]))
                            <span class="text-lg sm:text-2xl filter drop-shadow">🥈</span>
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-slate-800 border-2 border-slate-300 flex items-center justify-center text-2xl sm:text-3xl shadow-lg mt-1 group hover:scale-105 transition-transform">
                                {{ $leaderboardPets[1]->meta['emoji'] ?? '🐾' }}
                            </div>
                            <span class="text-xs sm:text-sm font-black text-slate-200 truncate w-full mt-1.5">
                                {{ $leaderboardPets[1]->name }}
                            </span>
                            @if(!empty($leaderboardPets[1]->equipped_outfit))
                                <div class="flex items-center justify-center gap-0.5 text-[10px]">
                                    @foreach($leaderboardPets[1]->equipped_outfit as $eqKey)
                                        @if(isset(\App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]))
                                            <span>{{ \App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]['emoji'] }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            <span class="text-[9px] sm:text-[10px] text-slate-400 truncate w-full">
                                {{ $leaderboardPets[1]->user->name ?? 'Admin' }}
                            </span>
                            <div class="px-2 py-0.5 rounded-md bg-slate-900 border border-slate-700 text-[9px] sm:text-[10px] font-mono font-bold text-slate-300 mt-1">
                                @if($leaderboardSort === 'coins')
                                    🪙 {{ number_format($leaderboardPets[1]->coins) }}
                                @elseif($leaderboardSort === 'happiness')
                                    💖 {{ $leaderboardPets[1]->happiness }}%
                                @else
                                    Lv. {{ $leaderboardPets[1]->level }} ({{ number_format($leaderboardPets[1]->xp) }} XP)
                                @endif
                            </div>
                            <div class="w-full h-18 sm:h-24 bg-gradient-to-t from-slate-900 to-slate-800/90 rounded-t-2xl border-t-4 border-slate-300 flex flex-col items-center justify-center shadow-md mt-2">
                                <span class="text-xs sm:text-sm font-black text-slate-300">#2</span>
                            </div>
                        @else
                            <div class="w-full h-14 sm:h-18 bg-slate-900/40 rounded-t-2xl border-t border-slate-800"></div>
                        @endif
                    </div>

                    <!-- Juara 1 (🥇 Tengah, Tertinggi) -->
                    <div class="flex flex-col items-center text-center min-w-0">
                        <span class="text-2xl sm:text-3xl filter drop-shadow animate-bounce">👑</span>
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-tr from-amber-500/30 to-yellow-400/30 border-3 border-amber-400 ring-4 ring-amber-400/20 flex items-center justify-center text-3xl sm:text-4xl shadow-2xl mt-1 hover:scale-105 transition-transform">
                            {{ $leaderboardPets[0]->meta['emoji'] ?? '🐾' }}
                        </div>
                        <span class="text-xs sm:text-base font-black text-amber-300 truncate w-full mt-1.5 flex items-center justify-center gap-1">
                            <span>{{ $leaderboardPets[0]->name }}</span>
                            <span class="text-xs">✨</span>
                        </span>
                        @if(!empty($leaderboardPets[0]->equipped_outfit))
                            <div class="flex items-center justify-center gap-0.5 text-xs">
                                @foreach($leaderboardPets[0]->equipped_outfit as $eqKey)
                                    @if(isset(\App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]))
                                        <span>{{ \App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]['emoji'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <span class="text-[10px] sm:text-xs text-amber-200/80 font-bold truncate w-full">
                            {{ $leaderboardPets[0]->user->name ?? 'Admin' }}
                        </span>
                        <div class="px-2.5 py-0.5 rounded-md bg-amber-500/20 border border-amber-500/40 text-[10px] sm:text-xs font-mono font-black text-yellow-300 mt-1">
                            @if($leaderboardSort === 'coins')
                                🪙 {{ number_format($leaderboardPets[0]->coins) }}
                            @elseif($leaderboardSort === 'happiness')
                                💖 {{ $leaderboardPets[0]->happiness }}%
                            @else
                                Lv. {{ $leaderboardPets[0]->level }} ({{ number_format($leaderboardPets[0]->xp) }} XP)
                            @endif
                        </div>
                        <div class="w-full h-26 sm:h-34 bg-gradient-to-t from-amber-950 via-amber-900/60 to-amber-600/40 rounded-t-2xl border-t-4 border-yellow-300 flex flex-col items-center justify-center shadow-[0_0_25px_rgba(245,158,11,0.4)] mt-2">
                            <span class="text-xs sm:text-sm font-black text-amber-200">🥇 #1 JUARA</span>
                        </div>
                    </div>

                    <!-- Juara 3 (🥉 Kanan) -->
                    <div class="flex flex-col items-center text-center min-w-0">
                        @if(isset($leaderboardPets[2]))
                            <span class="text-lg sm:text-2xl filter drop-shadow">🥉</span>
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-slate-800 border-2 border-amber-700 flex items-center justify-center text-2xl sm:text-3xl shadow-lg mt-1 group hover:scale-105 transition-transform">
                                {{ $leaderboardPets[2]->meta['emoji'] ?? '🐾' }}
                            </div>
                            <span class="text-xs sm:text-sm font-black text-amber-200/90 truncate w-full mt-1.5">
                                {{ $leaderboardPets[2]->name }}
                            </span>
                            @if(!empty($leaderboardPets[2]->equipped_outfit))
                                <div class="flex items-center justify-center gap-0.5 text-[10px]">
                                    @foreach($leaderboardPets[2]->equipped_outfit as $eqKey)
                                        @if(isset(\App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]))
                                            <span>{{ \App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]['emoji'] }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            <span class="text-[9px] sm:text-[10px] text-slate-400 truncate w-full">
                                {{ $leaderboardPets[2]->user->name ?? 'Admin' }}
                            </span>
                            <div class="px-2 py-0.5 rounded-md bg-slate-900 border border-slate-700 text-[9px] sm:text-[10px] font-mono font-bold text-amber-500 mt-1">
                                @if($leaderboardSort === 'coins')
                                    🪙 {{ number_format($leaderboardPets[2]->coins) }}
                                @elseif($leaderboardSort === 'happiness')
                                    💖 {{ $leaderboardPets[2]->happiness }}%
                                @else
                                    Lv. {{ $leaderboardPets[2]->level }} ({{ number_format($leaderboardPets[2]->xp) }} XP)
                                @endif
                            </div>
                            <div class="w-full h-14 sm:h-18 bg-gradient-to-t from-slate-900 to-slate-800/80 rounded-t-2xl border-t-4 border-amber-700 flex flex-col items-center justify-center shadow-md mt-2">
                                <span class="text-xs sm:text-sm font-black text-amber-700">#3</span>
                            </div>
                        @else
                            <div class="w-full h-12 sm:h-16 bg-slate-900/40 rounded-t-2xl border-t border-slate-800"></div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Daftar Klasemen Lengkap Seluruh Admin -->
            <div class="space-y-2 pt-2">
                <div class="flex items-center justify-between text-xs text-slate-400 px-2 font-bold uppercase tracking-wider">
                    <span>Klasemen Lengkap ({{ count($leaderboardPets) }} Sahabat Terdaftar)</span>
                    <span>Total XP / Koin</span>
                </div>

                @forelse($leaderboardPets as $index => $rankedPet)
                    @php 
                        $isMe = $pet && $rankedPet->id === $pet->id;
                    @endphp
                    <div class="p-3 sm:p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all {{ $isMe ? 'bg-amber-500/10 border-2 border-amber-500/60 shadow-lg ring-1 ring-amber-400/30' : 'bg-slate-900/80 border border-slate-800 hover:border-slate-700' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Rank Number Badge -->
                            <div class="shrink-0">
                                @if($index === 0)
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-yellow-600 text-slate-950 font-black text-xs sm:text-sm flex items-center justify-center shadow">
                                        🥇
                                    </div>
                                @elseif($index === 1)
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-300 to-slate-500 text-slate-950 font-black text-xs sm:text-sm flex items-center justify-center shadow">
                                        🥈
                                    </div>
                                @elseif($index === 2)
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-700 to-amber-900 text-amber-200 font-black text-xs sm:text-sm flex items-center justify-center shadow">
                                        🥉
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 text-slate-400 font-mono font-bold text-xs sm:text-sm flex items-center justify-center">
                                        #{{ $index + 1 }}
                                    </div>
                                @endif
                            </div>

                            <!-- Pet Emoji Avatar -->
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center text-xl sm:text-2xl shrink-0 shadow-inner">
                                {{ $rankedPet->meta['emoji'] ?? '🐾' }}
                            </div>

                            <!-- Pet & Admin Info -->
                            <div class="min-w-0 truncate">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-black text-xs sm:text-sm text-white truncate">{{ $rankedPet->name }}</span>
                                    @if($isMe)
                                        <span class="px-1.5 py-0.2 rounded text-[8px] bg-amber-400/20 text-amber-300 font-mono font-black border border-amber-400/30">
                                            Kamu
                                        </span>
                                    @endif
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-slate-800 text-slate-300 font-semibold border border-slate-700">
                                        {{ $rankedPet->meta['species'] ?? 'Sahabat' }}
                                    </span>
                                    @if(!empty($rankedPet->equipped_outfit))
                                        <div class="flex items-center gap-0.5 px-1.5 py-0.2 rounded bg-indigo-950/60 border border-indigo-700/50 text-[10px]" title="Outfit Terpakai">
                                            @foreach($rankedPet->equipped_outfit as $cat => $eqKey)
                                                @if(isset(\App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]))
                                                    <span>{{ \App\Models\AdminVirtualPet::OUTFIT_ITEMS[$eqKey]['emoji'] }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-[10px] sm:text-xs text-slate-400 flex items-center gap-1.5 mt-0.5 truncate">
                                    <span class="text-slate-300 font-semibold truncate">👤 {{ $rankedPet->user->name ?? 'Admin ELF' }}</span>
                                    <span>&bull;</span>
                                    <span class="text-amber-400/90 font-medium truncate">{{ $rankedPet->rank_title }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats & Interaksi Sosial -->
                        <div class="flex items-center gap-2 justify-between sm:justify-end shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-800">
                            <!-- Stats badges -->
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 sm:px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 text-[10px] sm:text-xs font-mono font-black text-purple-300" title="Level Pet">
                                    Lv. {{ $rankedPet->level }}
                                </span>
                                <span class="px-2 sm:px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 text-[10px] sm:text-xs font-mono font-bold text-yellow-400" title="Total XP">
                                    {{ number_format($rankedPet->xp) }} XP
                                </span>
                                <span class="hidden md:inline-flex px-2 py-1 rounded-xl bg-slate-950 border border-slate-800 text-[10px] sm:text-xs font-mono font-bold text-amber-300" title="Koin">
                                    🪙 {{ number_format($rankedPet->coins) }}
                                </span>
                                <span class="hidden sm:inline-flex px-2 py-1 rounded-xl bg-slate-950 border border-slate-800 text-[10px] sm:text-xs font-bold text-pink-400" title="Tingkat Kebahagiaan">
                                    💖 {{ $rankedPet->happiness }}%
                                </span>
                            </div>

                            <!-- Social Action: Tos Semangat -->
                            @if(!$isMe)
                                <button 
                                    wire:click="cheerPet({{ $rankedPet->id }})" 
                                    type="button" 
                                    class="px-2.5 sm:px-3 py-1.5 rounded-xl text-[10px] sm:text-xs font-black bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/40 hover:border-amber-400 text-amber-300 hover:text-white transition-all hover:scale-105 active:scale-95 shadow cursor-pointer flex items-center gap-1 shrink-0"
                                    title="Kirim Tos Semangat (+5 Koin &amp; +5 XP persahabatan)"
                                >
                                    <span class="animate-pulse">✨</span>
                                    <span class="hidden sm:inline">Tos Semangat</span>
                                    <span class="sm:hidden">Tos</span>
                                </button>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold text-amber-400/90 bg-amber-500/10 rounded-xl border border-amber-500/30">
                                    🌟 Pet Kamu
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-slate-900/60 border border-slate-800 rounded-2xl text-slate-400">
                        <span class="text-4xl block mb-2">🐾</span>
                        <p class="text-sm font-bold text-white">Belum ada admin yang mengadopsi pet.</p>
                        <p class="text-xs mt-1">Jadilah yang pertama merawat sahabat virtual maskot!</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer Return Button -->
            <div class="text-center pt-2">
                <button wire:click="setViewMode('arena')" type="button" class="px-6 py-2 rounded-xl text-xs font-extrabold bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer">
                    ← Kembali ke Kamar
                </button>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- VIEW MODE: REWARDS & RIWAYAT PENCATATAN -->
    <!-- ========================================================= -->
    @if($viewMode === 'rewards' && $pet)
        <div class="p-4 sm:p-6 space-y-5">
            <div class="border-b border-slate-800 pb-4">
                <h4 class="text-base sm:text-lg font-black text-white font-serif-display uppercase tracking-wider flex items-center gap-2">
                    <span>📜</span> Sistem Reward Aksi Kerja Admin
                </h4>
                <p class="text-xs text-slate-400 mt-0.5">Dapatkan koin dan XP otomatis setiap kali kamu bekerja mencatat data di sistem ELF</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl">📝</span>
                    <div>
                        <h6 class="text-xs font-bold text-white">Presensi Kegiatan Santri</h6>
                        <p class="text-[11px] text-amber-300 font-mono font-bold">+15 Koin &bull; +10 XP</p>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-sky-500/10 text-sky-400 rounded-xl">🎫</span>
                    <div>
                        <h6 class="text-xs font-bold text-white">Menerbitkan Perizinan Santri</h6>
                        <p class="text-[11px] text-amber-300 font-mono font-bold">+20 Koin &bull; +15 XP</p>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-red-500/10 text-red-400 rounded-xl">⚖️</span>
                    <div>
                        <h6 class="text-xs font-bold text-white">Mencatat Pelanggaran Disiplin</h6>
                        <p class="text-[11px] text-amber-300 font-mono font-bold">+20 Koin &bull; +15 XP</p>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-amber-500/10 text-amber-400 rounded-xl">💰</span>
                    <div>
                        <h6 class="text-xs font-bold text-white">Mencatat Pembayaran Keuangan</h6>
                        <p class="text-[11px] text-amber-300 font-mono font-bold">+25 Koin &bull; +20 XP</p>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-purple-500/10 text-purple-400 rounded-xl">👤</span>
                    <div>
                        <h6 class="text-xs font-bold text-white">Registrasi Profil Santri Baru</h6>
                        <p class="text-[11px] text-amber-300 font-mono font-bold">+30 Koin &bull; +25 XP</p>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl p-2.5 bg-teal-500/10 text-teal-400 rounded-xl">🎁</span>
                    <div>
                        <h6 class="text-xs font-bold text-white">Daily Login Bonus</h6>
                        <p class="text-[11px] text-amber-300 font-mono font-bold">+50 Koin &bull; +25 XP / Hari</p>
                    </div>
                </div>
            </div>

            <!-- Riwayat Reward Terkini -->
            <div class="mt-4 space-y-2">
                <h5 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Riwayat Reward Terbaru
                </h5>
                @php $recent = $pet->recent_rewards ?? []; @endphp
                @if(count($recent) > 0)
                    <div class="space-y-1.5">
                        @foreach($recent as $r)
                            <div class="p-3 bg-slate-900/90 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-amber-400">✨</span>
                                    <span class="font-bold text-white">{{ $r['reason'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 font-mono">
                                    <span class="text-amber-300 font-black">+{{ $r['coins'] }} Koin</span>
                                    <span class="text-slate-400">+{{ $r['xp'] }} XP</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-slate-900/40 border border-dashed border-slate-800 rounded-xl text-center text-xs text-slate-500">
                        Belum ada riwayat aktivitas tercatat hari ini. Coba catat presensi santri atau perizinan untuk mendapatkan reward koin otomatis!
                    </div>
                @endif
            </div>

            <div class="text-center pt-2">
                <button wire:click="setViewMode('arena')" type="button" class="px-6 py-2 rounded-xl text-xs font-extrabold bg-slate-800 text-slate-300 hover:text-white transition-all cursor-pointer">
                    ← Kembali ke Kamar
                </button>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- VIEW MODE: ADOPSI / GANTI KARAKTER & NAMA -->
    <!-- ========================================================= -->
    @if($viewMode === 'adopt')
        <div class="p-4 sm:p-6 space-y-6">
            <div class="text-center max-w-md mx-auto space-y-1">
                <h4 class="text-lg sm:text-xl font-black text-white font-serif-display uppercase tracking-wider">
                    {{ $pet ? 'Ganti Karakter & Nama Sahabat' : 'Pilih Karakter Sahabat Favoritmu' }}
                </h4>
                <p class="text-xs text-slate-400">
                    Pilih karakter yang paling cocok menemani harimu selama bertugas di ELF Al-Fithroh.
                </p>
            </div>

            <!-- 4 Character Cards with Exact User Titles & Descriptions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($petTypes as $typeKey => $info)
                    <div 
                        wire:click="selectPetType('{{ $typeKey }}')"
                        class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col items-center text-center gap-2.5 {{ $selectedPetType === $typeKey ? 'bg-amber-500/10 border-amber-500 shadow-xl scale-[1.02]' : 'bg-slate-900/90 border-slate-800 hover:border-slate-700' }}"
                    >
                        <div class="text-5xl my-2 p-3 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                            {{ $info['emoji'] }}
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-white">{{ $info['name'] }}</h5>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-amber-300 border border-slate-700 mt-0.5">
                                {{ $info['species'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            {{ $info['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Name Input Field -->
            <div class="max-w-md mx-auto space-y-3 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Beri Nama Sahabatmu:
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.150ms="customPetName" 
                        placeholder="Contoh: Kibo Si Manis" 
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-amber-500 font-bold"
                    />
                    @error('customPetName') 
                        <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button 
                        wire:click="completeAdoption" 
                        type="button" 
                        class="flex-1 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black text-xs uppercase tracking-wider hover:brightness-110 active:scale-95 shadow-lg transition-all cursor-pointer"
                    >
                        {{ $pet ? 'Simpan Perubahan' : 'Adopsi Sekarang' }}
                    </button>
                    @if($pet)
                        <button 
                            wire:click="setViewMode('arena')" 
                            type="button" 
                            class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold cursor-pointer"
                        >
                            Batal
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- MINI-GAME MODAL: TANGKAP BERKAH (ALPINES JS INTERACTIVE GAME) -->
    <!-- ========================================================= -->
    <div 
        x-show="$wire.isMiniGameOpen" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
    >
        <div 
            x-data="{
                score: 0,
                gameRunning: false,
                timeLeft: 20,
                basketX: 50,
                fallingItems: [],
                timerInterval: null,
                spawnInterval: null,
                startGame() {
                    this.score = 0;
                    this.timeLeft = 20;
                    this.gameRunning = true;
                    this.fallingItems = [];

                    this.timerInterval = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            this.endGame();
                        }
                    }, 1000);

                    this.spawnInterval = setInterval(() => {
                        if (!this.gameRunning) return;
                        
                        if (Math.random() < 0.6) {
                            this.fallingItems.push({
                                id: Date.now() + Math.random(),
                                x: 10 + Math.random() * 80,
                                y: 0,
                                emoji: ['🍎', '🌴', '🥛', '🪙'][Math.floor(Math.random() * 4)],
                                speed: 4 + Math.random() * 3
                            });
                        }

                        this.fallingItems.forEach(item => {
                            item.y += item.speed;

                            if (item.y >= 75 && item.y <= 90 && Math.abs(item.x - this.basketX) < 16) {
                                this.score += (item.emoji === '🪙' ? 3 : 1);
                                item.y = 999;
                            }
                        });

                        this.fallingItems = this.fallingItems.filter(item => item.y < 95);
                    }, 50);
                },
                moveBasket(event) {
                    if (!this.gameRunning) return;
                    const rect = event.currentTarget.getBoundingClientRect();
                    const clientX = event.clientX || (event.touches && event.touches[0].clientX);
                    if (clientX) {
                        const relX = ((clientX - rect.left) / rect.width) * 100;
                        this.basketX = Math.max(10, Math.min(90, relX));
                    }
                },
                endGame() {
                    this.gameRunning = false;
                    clearInterval(this.timerInterval);
                    clearInterval(this.spawnInterval);
                    $wire.finishMiniGame(this.score);
                }
            }"
            class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-2xl relative space-y-4"
        >
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🧺</span>
                    <div>
                        <h5 class="text-sm font-black text-white">Tangkap Buah Berkah</h5>
                        <p class="text-[10px] text-slate-400">Geser keranjang untuk menangkap buah &amp; koin jatuh!</p>
                    </div>
                </div>
                <button @click="$wire.isMiniGameOpen = false" type="button" class="text-slate-400 hover:text-white text-xs cursor-pointer">
                    ✕ Tutup
                </button>
            </div>

            <!-- Game HUD -->
            <div class="flex items-center justify-between px-3.5 py-2 bg-slate-950 rounded-xl border border-slate-800 font-mono text-xs">
                <span class="text-amber-400 font-black">Skor: <strong x-text="score"></strong></span>
                <span class="text-slate-300 font-bold">Waktu: <strong x-text="timeLeft"></strong>s</span>
            </div>

            <!-- Game Board Field -->
            <div 
                @mousemove="moveBasket($event)"
                @touchmove="moveBasket($event)"
                class="relative h-64 bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden cursor-crosshair select-none"
            >
                <template x-if="!gameRunning">
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center space-y-3 bg-slate-950/80 z-20">
                        <span class="text-4xl animate-bounce">🍎</span>
                        <p class="text-xs text-slate-300 font-medium max-w-xs">
                            Gerakkan keranjang ke kiri dan kanan untuk mengumpulkan berkah sebanyak mungkin!
                        </p>
                        <button 
                            @click="startGame()" 
                            type="button" 
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg active:scale-95 cursor-pointer"
                        >
                            Mulai Main (20 Detik)
                        </button>
                    </div>
                </template>

                <!-- Falling Items -->
                <template x-for="item in fallingItems" :key="item.id">
                    <div 
                        class="absolute text-2xl pointer-events-none transition-all duration-75"
                        :style="`left: ${item.x}%; top: ${item.y}%; transform: translateX(-50%);`"
                        x-text="item.emoji"
                    ></div>
                </template>

                <!-- Basket (Player) -->
                <div 
                    class="absolute bottom-3 text-3xl pointer-events-none transition-all duration-75"
                    :style="`left: ${basketX}%; transform: translateX(-50%);`"
                >
                    🧺
                </div>
            </div>
        </div>
    </div>

</div>
