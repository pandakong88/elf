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
     * Relasi ke User (Admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
