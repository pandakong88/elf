<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminVirtualPet extends Model
{
    protected $table = 'admin_virtual_pets';

    protected $guarded = ['id'];

    protected $casts = [
        'inventory' => 'array',
        'wardrobe' => 'array',
        'equipped_outfit' => 'array',
        'recent_rewards' => 'array',
        'is_sleeping' => 'boolean',
        'last_fed_at' => 'datetime',
        'last_cleaned_at' => 'datetime',
        'last_slept_at' => 'datetime',
        'last_played_at' => 'datetime',
        'last_decay_calculated_at' => 'datetime',
        'last_daily_bonus_at' => 'date',
    ];

    public const PET_TYPES = [
        'kibo' => [
            'key' => 'kibo',
            'name' => 'Kibo',
            'species' => 'Kucing Oranye',
            'emoji' => '🐱',
            'color' => 'amber',
            'description' => 'Kucing oranye ramah & cerdik yang suka nongkrong santai.',
            'greeting' => 'Meoong! Semangat ya, jangan lupa rehat dan ngopi!',
        ],
        'bunbun' => [
            'key' => 'bunbun',
            'name' => 'Bunbun',
            'species' => 'Kelinci Cerdik',
            'emoji' => '🐰',
            'color' => 'pink',
            'description' => 'Kelinci putih ceria yang sangat aktif dan gemar baca buku.',
            'greeting' => 'Hai! Sudah baca bab buku hari ini belum?',
        ],
        'pansan' => [
            'key' => 'pansan',
            'name' => 'Pansan',
            'species' => 'Panda Pemalas',
            'emoji' => '🐼',
            'color' => 'emerald',
            'description' => 'Panda lucu dan santai pecinta camilan lezat dan istirahat yang tenang.',
            'greeting' => 'Hoaaam... Mau ngemil donat atau ngopi santai dulu nih?',
        ],
        'bambu' => [
            'key' => 'pansan',
            'name' => 'Pansan',
            'species' => 'Panda Pemalas',
            'emoji' => '🐼',
            'color' => 'emerald',
            'description' => 'Panda lucu dan santai pecinta camilan lezat dan istirahat yang tenang.',
            'greeting' => 'Hoaaam... Mau ngemil donat atau ngopi santai dulu nih?',
        ],
        'pingo' => [
            'key' => 'pingo',
            'name' => 'Pingo',
            'species' => 'Penguin Disiplin',
            'emoji' => '🐧',
            'color' => 'sky',
            'description' => 'Penguin polos yang tertib, rajin piket, dan tepat waktu.',
            'greeting' => 'Meja bersih, jadwal rapi, kerjaan pasti beres!',
        ],
    ];

    public const FOOD_ITEMS = [
        'coffee' => [
            'key' => 'coffee',
            'name' => 'Kopi Susu Hangat',
            'emoji' => '☕',
            'price' => 15,
            'hunger' => 10,
            'happiness' => 20,
            'xp' => 10,
            'energy' => 30,
        ],
        'donut' => [
            'key' => 'donut',
            'name' => 'Donat Cokelat',
            'emoji' => '🍩',
            'price' => 15,
            'hunger' => 25,
            'happiness' => 15,
            'xp' => 8,
            'energy' => 10,
        ],
        'apple' => [
            'key' => 'apple',
            'name' => 'Apel Segar',
            'emoji' => '🍎',
            'price' => 10,
            'hunger' => 20,
            'happiness' => 8,
            'xp' => 5,
            'energy' => 5,
        ],
        'nasgor' => [
            'key' => 'nasgor',
            'name' => 'Nasi Goreng Spesial',
            'emoji' => '🍛',
            'price' => 35,
            'hunger' => 50,
            'happiness' => 25,
            'xp' => 20,
            'energy' => 20,
        ],
        // Backward-compatible fallback keys
        'kurma' => [
            'key' => 'kurma',
            'name' => 'Camilan Manis',
            'emoji' => '🍪',
            'price' => 15,
            'hunger' => 25,
            'happiness' => 12,
            'xp' => 8,
            'energy' => 10,
        ],
        'milk' => [
            'key' => 'milk',
            'name' => 'Susu Kotak Segar',
            'emoji' => '🥛',
            'price' => 15,
            'hunger' => 20,
            'happiness' => 15,
            'xp' => 8,
            'energy' => 15,
        ],
        'kebuli' => [
            'key' => 'kebuli',
            'name' => 'Makan Siang Komplit',
            'emoji' => '🍱',
            'price' => 35,
            'hunger' => 50,
            'happiness' => 25,
            'xp' => 20,
            'energy' => 20,
        ],
    ];

    /**
     * Katalog Pakaian Santri & Kosmetik Absurd (Pet Wardrobe)
     * Kategori: 'head', 'eyes', 'body', 'prop'
     * Tier: 'cheap' (murah), 'mid' (sedang), 'sultan' (mahal), 'insane' (mahal ga ngotak)
     */
    public const OUTFIT_ITEMS = [
        // --- 1. KEPALA (HEADWEAR) ---
        'peci_hitam' => [
            'id' => 'peci_hitam',
            'name' => 'Peci Hitam Gus Santri',
            'category' => 'head',
            'tier' => 'cheap',
            'price' => 30,
            'emoji' => '🧢',
            'badge' => 'Santri Merakyat',
            'description' => 'Peci beludru hitam khas santri nusantara, adem dan berwibawa.',
        ],
        'kopiah_putih' => [
            'id' => 'kopiah_putih',
            'name' => 'Kopiah Putih Habib',
            'category' => 'head',
            'tier' => 'mid',
            'price' => 150,
            'emoji' => '🤍',
            'badge' => 'Santri Rajin',
            'description' => 'Kopiah rajut putih bersih, membuat aura pet makin kharismatik.',
        ],
        'sorban_hijau' => [
            'id' => 'sorban_hijau',
            'name' => 'Sorban Hijau Syaikhuna',
            'category' => 'head',
            'tier' => 'mid',
            'price' => 450,
            'emoji' => '🟢',
            'badge' => 'Gus Senior',
            'description' => 'Lilitan sorban hijau emerald dengan rumbai sutra anggun.',
        ],
        'blangkon_solo' => [
            'id' => 'blangkon_solo',
            'name' => 'Blangkon Keraton Solo',
            'category' => 'head',
            'tier' => 'mid',
            'price' => 500,
            'emoji' => '👑',
            'badge' => 'Ningrat Santun',
            'description' => 'Blangkon batik klasik dengan mondolan di belakang, santun dan ningrat.',
        ],
        'helm_bogo' => [
            'id' => 'helm_bogo',
            'name' => 'Helm Bogo Retro Santriwati',
            'category' => 'head',
            'tier' => 'sultan',
            'price' => 750,
            'emoji' => '🛵',
            'badge' => 'Beat Karbu Rider',
            'description' => 'Helm bogo kaca cembung untuk pet yang suka naik motor beat karbu keliling pondok.',
        ],
        'mahkota_emas' => [
            'id' => 'mahkota_emas',
            'name' => 'Mahkota Sultan Berlian 24K',
            'category' => 'head',
            'tier' => 'insane',
            'price' => 15000,
            'emoji' => '👑',
            'badge' => '🔥 MAHAL GA NGOTAK',
            'description' => 'Mahkota emas murni bertabur permata berlian, membuat silau seluruh admin di leaderboard!',
        ],

        // --- 2. MATA & WAJAH (EYES & FACE) ---
        'masker_santri' => [
            'id' => 'masker_santri',
            'name' => 'Masker Piket Polos',
            'category' => 'eyes',
            'tier' => 'cheap',
            'price' => 20,
            'emoji' => '😷',
            'badge' => 'Anti Debu',
            'description' => 'Masker anti-debu saat piket sapu halaman asrama santri.',
        ],
        'kacamata_bulat' => [
            'id' => 'kacamata_bulat',
            'name' => 'Kacamata Kitab Kuning',
            'category' => 'eyes',
            'tier' => 'mid',
            'price' => 120,
            'emoji' => '👓',
            'badge' => 'Kutu Buku',
            'description' => 'Kacamata vintage kutu buku, menambah kecerdasan visual 100%.',
        ],
        'kacamata_thug' => [
            'id' => 'kacamata_thug',
            'name' => 'Kacamata Pixel Thug Life 8-Bit',
            'category' => 'eyes',
            'tier' => 'mid',
            'price' => 350,
            'emoji' => '🕶️',
            'badge' => 'Meme Legend',
            'description' => 'Kacamata pixel hitam melayang ala meme legendaris. Deal with it, Bos!',
        ],
        'monocle_victoria' => [
            'id' => 'monocle_victoria',
            'name' => 'Monocle Emas Bangsawan',
            'category' => 'eyes',
            'tier' => 'sultan',
            'price' => 1200,
            'emoji' => '🧐',
            'badge' => 'Bangsawan',
            'description' => 'Kaca mata sebelah berantai emas, khusus admin yang memeriksa database dengan elegan.',
        ],
        'kacamata_cyberpunk' => [
            'id' => 'kacamata_cyberpunk',
            'name' => 'Kacamata Las Cyberpunk Hologram',
            'category' => 'eyes',
            'tier' => 'insane',
            'price' => 12000,
            'emoji' => '🥽',
            'badge' => '🔥 MAHAL GA NGOTAK',
            'description' => 'Kacamata visor laser neon masa depan, serasa admin tahun 2099.',
        ],

        // --- 3. BADAN & PAKAIAN (BODY CLOTHING) ---
        'sarung_wadimor' => [
            'id' => 'sarung_wadimor',
            'name' => 'Sarung Wadimor Kotak-Kotak',
            'category' => 'body',
            'tier' => 'cheap',
            'price' => 50,
            'emoji' => '🟩',
            'badge' => 'Santri Sejati',
            'description' => 'Sarung tenun kotak-kotak andalan santri sehari-hari, adem dan anti gerah.',
        ],
        'baju_koko' => [
            'id' => 'baju_koko',
            'name' => 'Baju Koko Bordir Gus',
            'category' => 'body',
            'tier' => 'mid',
            'price' => 200,
            'emoji' => '🥋',
            'badge' => 'Imam Jamaah',
            'description' => 'Baju koko katun bordir rapi, siap imam salat berjamaah.',
        ],
        'jubah_saudi' => [
            'id' => 'jubah_saudi',
            'name' => 'Jubah Gamis Putih Saudi',
            'category' => 'body',
            'tier' => 'sultan',
            'price' => 1800,
            'emoji' => '🥻',
            'badge' => 'Aroma Gaharu',
            'description' => 'Jubah putih terusan mewah berkerah kaku dengan aroma gaharu mewah.',
        ],
        'sarung_bhs' => [
            'id' => 'sarung_bhs',
            'name' => 'Sarung BHS Sutra Masterpiece',
            'category' => 'body',
            'tier' => 'sultan',
            'price' => 2500,
            'emoji' => '✨',
            'badge' => 'Sultan Pondok',
            'description' => 'Sarung tenun sutra damask legendaris yang harganya bikin dompet bergetar.',
        ],
        'jas_tuxedo' => [
            'id' => 'jas_tuxedo',
            'name' => 'Jas Tuxedo Agen Rahasia 007',
            'category' => 'body',
            'tier' => 'sultan',
            'price' => 3500,
            'emoji' => '🤵',
            'badge' => 'Rapat Yayasan',
            'description' => 'Jas formal berdasi kupu-kupu hitam, cocok saat pet menghadiri rapat pleno yayasan.',
        ],
        'jubah_kaisar' => [
            'id' => 'jubah_kaisar',
            'name' => 'Jubah Emas Kaisar Galaksi',
            'category' => 'body',
            'tier' => 'insane',
            'price' => 25000,
            'emoji' => '🪐',
            'badge' => '🔥 MAHAL GA NGOTAK',
            'description' => 'Jubah emas berkilau efek partikel bintang kosmik. Siapa pun yang melihat pet ini langsung sungkem.',
        ],

        // --- 4. AKSESORIS TANGAN & BELAKANG (PROPS) ---
        'sapu_lidi' => [
            'id' => 'sapu_lidi',
            'name' => 'Sapu Lidi Kebersihan',
            'category' => 'prop',
            'tier' => 'cheap',
            'price' => 25,
            'emoji' => '🧹',
            'badge' => 'Juara Piket',
            'description' => 'Sapu lidi legendaris untuk piket pagi dan mendisiplinkan santri telat.',
        ],
        'tasbih_digital' => [
            'id' => 'tasbih_digital',
            'name' => 'Tasbih Digital LED Glow',
            'category' => 'prop',
            'tier' => 'mid',
            'price' => 180,
            'emoji' => '📿',
            'badge' => 'Dzikir 24 Jam',
            'description' => 'Tasbih digital dengan layar mini hijau, klik dzikir auto nambah pahala.',
        ],
        'gitar_akustik' => [
            'id' => 'gitar_akustik',
            'name' => 'Gitar Akustik Hindia',
            'category' => 'prop',
            'tier' => 'mid',
            'price' => 300,
            'emoji' => '🎸',
            'badge' => 'Indie Santri',
            'description' => 'Gitar akustik kayu di punggung, pet kamu siap bernyanyi Evaluasi.',
        ],
        'termos_kopi' => [
            'id' => 'termos_kopi',
            'name' => 'Termos Kopi Stainless Rapat',
            'category' => 'prop',
            'tier' => 'mid',
            'price' => 400,
            'emoji' => '🫖',
            'badge' => 'Admin Lembur',
            'description' => 'Termos kopi panas tahan 24 jam, bahan bakar utama admin saat lembur.',
        ],
        'lightsaber_hijau' => [
            'id' => 'lightsaber_hijau',
            'name' => 'Lightsaber Hijau Kyai Jedi',
            'category' => 'prop',
            'tier' => 'sultan',
            'price' => 5000,
            'emoji' => '⚔️',
            'badge' => 'Jedi Santri',
            'description' => 'Pedang energi hijau pendar neon, pet kamu resmi diangkat jadi Jedi Santri.',
        ],
        'rubicon_emas' => [
            'id' => 'rubicon_emas',
            'name' => 'Miniatur Rubicon Emas Sultan',
            'category' => 'prop',
            'tier' => 'insane',
            'price' => 50000,
            'emoji' => '🚙',
            'badge' => '🔥 RAJA FLEXING',
            'description' => 'Mobil jeep Rubicon mini warna emas pelat nomor B 1 BOS parkir di samping pet. Flexing level maksimal!',
        ],
    ];

    /**
     * Relasi ke User (Admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cek apakah pet sudah memiliki pakaian tertentu di lemarinya.
     */
    public function hasOutfit(string $key): bool
    {
        return in_array($key, $this->wardrobe ?? []);
    }

    /**
     * Cek apakah pakaian tertentu sedang dipakai oleh pet.
     */
    public function isEquipped(string $key): bool
    {
        return in_array($key, array_values($this->equipped_outfit ?? []));
    }

    /**
     * Ambil item yang sedang dipakai di kategori tertentu.
     */
    public function getEquippedItem(string $category): ?string
    {
        return ($this->equipped_outfit ?? [])[$category] ?? null;
    }

    /**
     * Beli pakaian dari katalog lemari.
     */
    public function buyOutfit(string $key): array
    {
        if (!isset(self::OUTFIT_ITEMS[$key])) {
            return ['success' => false, 'message' => 'Item pakaian tidak ditemukan.'];
        }

        if ($this->hasOutfit($key)) {
            return ['success' => false, 'message' => 'Kamu sudah memiliki pakaian ini di lemarimu!'];
        }

        $item = self::OUTFIT_ITEMS[$key];
        if ($this->coins < $item['price']) {
            $kurang = number_format($item['price'] - $this->coins);
            return ['success' => false, 'message' => "Koinmu belum cukup! Kurang {$kurang} Koin lagi."];
        }

        $this->coins -= $item['price'];
        $wardrobe = $this->wardrobe ?? [];
        $wardrobe[] = $key;
        $this->wardrobe = array_values(array_unique($wardrobe));

        // Otomatis equip setelah beli
        $equipped = $this->equipped_outfit ?? [];
        $equipped[$item['category']] = $key;
        $this->equipped_outfit = $equipped;

        $this->happiness = min(100, $this->happiness + 15);
        $this->save();

        return [
            'success' => true,
            'message' => "Hore! Kamu berhasil membeli & mengenakan {$item['name']}! (+15% Senang)",
            'item' => $item,
        ];
    }

    /**
     * Kenakan (Equip) pakaian yang sudah dimiliki.
     */
    public function equipOutfit(string $key): array
    {
        if (!isset(self::OUTFIT_ITEMS[$key])) {
            return ['success' => false, 'message' => 'Item pakaian tidak ditemukan.'];
        }

        if (!$this->hasOutfit($key)) {
            return ['success' => false, 'message' => 'Kamu belum memiliki pakaian ini, beli dulu di katalog ya!'];
        }

        $item = self::OUTFIT_ITEMS[$key];
        $equipped = $this->equipped_outfit ?? [];
        $equipped[$item['category']] = $key;
        $this->equipped_outfit = $equipped;
        $this->save();

        return [
            'success' => true,
            'message' => "{$item['name']} sekarang dipakai oleh {$this->name}!",
            'item' => $item,
        ];
    }

    /**
     * Lepas (Unequip) pakaian di kategori tertentu.
     */
    public function unequipOutfit(string $keyOrCategory): array
    {
        $equipped = $this->equipped_outfit ?? [];

        // 1. Cek jika keyOrCategory adalah nama kategori (head, eyes, body, prop)
        if (isset($equipped[$keyOrCategory])) {
            $itemKey = $equipped[$keyOrCategory];
            unset($equipped[$keyOrCategory]);
            $this->equipped_outfit = $equipped;
            $this->save();
            $itemName = self::OUTFIT_ITEMS[$itemKey]['name'] ?? 'Pakaian';
            return ['success' => true, 'message' => "{$itemName} telah dilepas."];
        }

        // 2. Cek jika keyOrCategory adalah id/key item (contoh: peci_hitam)
        if (isset(self::OUTFIT_ITEMS[$keyOrCategory])) {
            $cat = self::OUTFIT_ITEMS[$keyOrCategory]['category'];
            if (($equipped[$cat] ?? null) === $keyOrCategory) {
                unset($equipped[$cat]);
                $this->equipped_outfit = $equipped;
                $this->save();
                $itemName = self::OUTFIT_ITEMS[$keyOrCategory]['name'] ?? 'Pakaian';
                return ['success' => true, 'message' => "{$itemName} telah dilepas."];
            }
        }

        return ['success' => false, 'message' => 'Pakaian tidak sedang dipakai.'];
    }

    /**
     * Hitung peluruhan kebutuhan seiring berjalannya waktu (Decay over time).
     * Pet tidak pernah mati, hanya statusnya menurun agar admin termotivasi merawatnya.
     */
    public function applyDecay(): self
    {
        $now = Carbon::now();
        $lastChecked = $this->last_decay_calculated_at ?? $this->updated_at ?? $now;
        
        $minutesPassed = max(0, $lastChecked->diffInMinutes($now));

        if ($minutesPassed >= 15) {
            $hoursPassed = $minutesPassed / 60;

            // Pengurangan rasa kenyang: ~4% per jam (min 10)
            $hungerLoss = (int) round($hoursPassed * 4);
            $this->hunger = max(10, min(100, $this->hunger - $hungerLoss));

            // Pengurangan kebersihan: ~3% per jam (min 15)
            $hygieneLoss = (int) round($hoursPassed * 3);
            $this->hygiene = max(15, min(100, $this->hygiene - $hygieneLoss));

            // Energi
            if ($this->is_sleeping) {
                // Saat tidur, energi pulih ~25% per jam
                $energyGain = (int) round($hoursPassed * 25);
                $this->energy = min(100, $this->energy + $energyGain);
                if ($this->energy >= 100) {
                    $this->is_sleeping = false; // Bangun otomatis jika baterai penuh
                }
            } else {
                // Saat bangun, energi turun ~4% per jam (min 10)
                $energyLoss = (int) round($hoursPassed * 4);
                $this->energy = max(10, min(100, $this->energy - $energyLoss));
            }

            // Kebahagiaan terpengaruh kenyang & kebersihan
            $happinessDecay = (int) round($hoursPassed * 3);
            if ($this->hunger < 30 || $this->hygiene < 30) {
                $happinessDecay = (int) round($hoursPassed * 6);
            }
            $this->happiness = max(15, min(100, $this->happiness - $happinessDecay));

            $this->last_decay_calculated_at = $now;
            $this->save();
        }

        return $this;
    }

    /**
     * Beri makan pet menggunakan item dari inventori.
     */
    public function feed(string $foodKey): array
    {
        if ($this->is_sleeping) {
            return ['success' => false, 'message' => "{$this->name} sedang tidur lelap! Bangunkan dulu untuk memberinya makan."];
        }

        if (!isset(self::FOOD_ITEMS[$foodKey])) {
            return ['success' => false, 'message' => 'Makanan tidak valid!'];
        }

        $food = self::FOOD_ITEMS[$foodKey];
        $inv = $this->inventory ?? [];
        $currentStock = $inv[$foodKey] ?? 0;

        if ($currentStock <= 0) {
            return ['success' => false, 'message' => "Stok {$food['name']} habis! Beli di Toko Camilan terlebih dahulu."];
        }

        if ($this->hunger >= 100) {
            return ['success' => false, 'message' => "{$this->name} sudah sangat kenyang dan tidak bisa makan lagi!"];
        }

        // Kurangi stok
        $inv[$foodKey] = $currentStock - 1;
        $this->inventory = $inv;

        // Tambah status
        $this->hunger = min(100, $this->hunger + $food['hunger']);
        $this->happiness = min(100, $this->happiness + $food['happiness']);
        if (!empty($food['energy'])) {
            $this->energy = min(100, $this->energy + $food['energy']);
        }
        $this->last_fed_at = Carbon::now();
        $this->last_decay_calculated_at = Carbon::now();

        $this->addXp($food['xp']);
        $this->save();

        return [
            'success' => true,
            'message' => "Nyam nyam! {$this->name} menikmati {$food['name']} (+{$food['hunger']}% Kenyang, +{$food['xp']} XP)!"
        ];
    }

    /**
     * Beli makanan di toko menggunakan koin.
     */
    public function buyFood(string $foodKey): array
    {
        if (!isset(self::FOOD_ITEMS[$foodKey])) {
            return ['success' => false, 'message' => 'Item tidak ditemukan di toko!'];
        }

        $food = self::FOOD_ITEMS[$foodKey];
        if ($this->coins < $food['price']) {
            return ['success' => false, 'message' => "Koin tidak cukup! Harga {$food['name']} adalah {$food['price']} koin."];
        }

        $this->coins -= $food['price'];
        $inv = $this->inventory ?? [];
        $inv[$foodKey] = ($inv[$foodKey] ?? 0) + 1;
        $this->inventory = $inv;
        $this->save();

        return [
            'success' => true,
            'message' => "Berhasil membeli 1 {$food['name']} seharga {$food['price']} koin!"
        ];
    }

    /**
     * Mandikan pet.
     */
    public function clean(): array
    {
        if ($this->is_sleeping) {
            return ['success' => false, 'message' => "{$this->name} sedang tidur! Bangunkan dulu sebelum dimandikan."];
        }

        if ($this->hygiene >= 98) {
            return ['success' => false, 'message' => "{$this->name} sudah sangat wangi dan bersih kinclong!"];
        }

        $this->hygiene = 100;
        $this->happiness = min(100, $this->happiness + 10);
        $this->last_cleaned_at = Carbon::now();
        $this->last_decay_calculated_at = Carbon::now();

        $this->addXp(6);
        $this->save();

        return [
            'success' => true,
            'message' => "Byuurr! {$this->name} sekarang wangi, segar, dan bersih (+100% Bersih, +6 XP)!"
        ];
    }

    /**
     * Nyalakan/Matikan lampu tidur.
     */
    public function toggleSleep(): array
    {
        if ($this->is_sleeping) {
            $this->is_sleeping = false;
            $this->last_decay_calculated_at = Carbon::now();
            $this->save();
            return [
                'success' => true,
                'message' => "Lampu dinyalakan! {$this->name} sudah bangun dengan ceria!"
            ];
        } else {
            $this->is_sleeping = true;
            $this->last_slept_at = Carbon::now();
            $this->last_decay_calculated_at = Carbon::now();
            $this->save();
            return [
                'success' => true,
                'message' => "Lampu dipadamkan. Ssst... {$this->name} sedang tidur untuk memulihkan energi."
            ];
        }
    }

    /**
     * Interaksi santai / tos bareng pet (tap/click interaction).
     */
    public function petInteraction(): array
    {
        if ($this->is_sleeping) {
            return ['success' => false, 'message' => "{$this->name} sedang tidur nyenyak."];
        }

        $this->happiness = min(100, $this->happiness + 8);
        $this->last_played_at = Carbon::now();
        $this->last_decay_calculated_at = Carbon::now();
        $this->addXp(3);
        $this->save();

        return [
            'success' => true,
            'message' => "Tos! {$this->name} senang diajak main dan siap kerja bareng kamu (+8% Senang, +3 XP)!"
        ];
    }

    /**
     * Berikan reward koin dan XP dari aksi kerja sistem (Presensi, Perizinan, Tagihan, dsb).
     */
    public function addReward(int $coins, int $xp, string $reason): void
    {
        $this->coins += $coins;
        $this->addXp($xp);

        $rewards = $this->recent_rewards ?? [];
        array_unshift($rewards, [
            'reason' => $reason,
            'coins' => $coins,
            'xp' => $xp,
            'time' => Carbon::now()->toIso8601String(),
        ]);

        // Simpan 5 riwayat reward terbaru saja
        $this->recent_rewards = array_slice($rewards, 0, 5);
        $this->save();
    }

    /**
     * Naikkan XP & level up jika mencapai ambang batas level.
     */
    public function addXp(int $amount): void
    {
        $this->xp += $amount;
        $xpNeeded = $this->xpForNextLevel();

        while ($this->xp >= $xpNeeded) {
            $this->xp -= $xpNeeded;
            $this->level++;
            $this->coins += 35; // Bonus koin naik level
            $this->happiness = 100;
            $xpNeeded = $this->xpForNextLevel();
        }
    }

    /**
     * Hitung XP yang dibutuhkan untuk level berikutnya.
     */
    public function xpForNextLevel(): int
    {
        return $this->level * 75;
    }

    /**
     * Klaim bonus login harian jika belum diambil hari ini.
     */
    public function claimDailyBonus(): ?array
    {
        $today = Carbon::today()->toDateString();
        if ($this->last_daily_bonus_at === $today) {
            return null;
        }

        $this->last_daily_bonus_at = $today;
        $bonusCoins = 50;
        $bonusXp = 25;
        $this->addReward($bonusCoins, $bonusXp, 'Bonus Login Harian');

        return [
            'coins' => $bonusCoins,
            'xp' => $bonusXp,
        ];
    }

    /**
     * Dapatkan metadata info maskot.
     */
    public function getMetaAttribute(): array
    {
        return self::PET_TYPES[$this->pet_type] ?? self::PET_TYPES['kibo'];
    }

    /**
     * Gelar / Pangkat pet berdasarkan levelnya.
     */
    public function getRankTitleAttribute(): string
    {
        return match (true) {
            $this->level >= 30 => 'Sahabat Legendaris 👑',
            $this->level >= 20 => 'Master Produktif 🌟',
            $this->level >= 12 => 'Kutu Buku Teladan 📚',
            $this->level >= 6 => 'Partner Piket & Ngopi ☕',
            $this->level >= 3 => 'Sahabat Ceria 🌿',
            default => 'Sahabat Baru 🌱',
        };
    }

    /**
     * Status mood pet saat ini untuk ekspresi visual.
     */
    public function getMoodStatusAttribute(): string
    {
        if ($this->is_sleeping) {
            return 'sleeping';
        }
        if ($this->hunger <= 25) {
            return 'hungry';
        }
        if ($this->hygiene <= 25) {
            return 'dirty';
        }
        if ($this->energy <= 25) {
            return 'tired';
        }
        if ($this->happiness >= 80) {
            return 'super_happy';
        }
        if ($this->happiness <= 35) {
            return 'sad';
        }
        return 'happy';
    }
}
