<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\SendsToast;
use App\Models\AdminVirtualPet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VirtualPetArena extends Component
{
    use SendsToast;

    public ?AdminVirtualPet $pet = null;

    // Mode: 'arena', 'shop', 'adopt', 'rewards'
    public string $viewMode = 'arena';

    // Form Adopsi / Edit
    public string $selectedPetType = 'kibo';
    public string $customPetName = '';

    // Quick inline name editing state
    public bool $isEditingName = false;
    public string $editNameValue = '';

    // Status Aksi Terakhir untuk Balon Ucapan
    public string $petSpeech = '';
    public string $speechEmotion = 'normal'; // 'normal', 'happy', 'eating', 'sleeping', 'clean'

    // Mini-Game Cepat: Tangkap Buah Berkah
    public bool $isMiniGameOpen = false;
    public int $miniGameScore = 0;

    public function mount(): void
    {
        $this->loadPet();
    }

    /**
     * Memuat atau menginisialisasi pet milik user.
     */
    public function loadPet(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->pet = AdminVirtualPet::where('user_id', $user->id)->first();

        if ($this->pet) {
            // Terapkan peluruhan kebutuhan berkala
            $this->pet->applyDecay();

            // Cek Daily Login Bonus otomatis
            $bonus = $this->pet->claimDailyBonus();
            if ($bonus) {
                $this->toastSuccess(
                    "Kamu mendapatkan bonus login harian: +{$bonus['coins']} Koin & +{$bonus['xp']} XP!",
                    '🎁 Bonus Harian Diklaim!'
                );
                $this->petSpeech = "Alhamdulillah! Bonus login harian sudah masuk ke dompetmu!";
                $this->speechEmotion = 'happy';
            } else {
                $this->petSpeech = $this->pet->meta['greeting'] ?? 'Halo Admin!';
            }

            $this->selectedPetType = $this->pet->pet_type;
            $this->customPetName = $this->pet->name;
        } else {
            // Belum pernah adopsi pet
            $this->viewMode = 'adopt';
            $this->selectedPetType = 'kibo';
            $this->customPetName = 'Kibo';
        }
    }

    /**
     * Pilih tipe maskot dan sesuaikan nama secara otomatis jika masih nama default.
     */
    public function selectPetType(string $type): void
    {
        $this->selectedPetType = $type;
        $defaultNames = ['Kibo', 'Bunbun', 'Bambu', 'Pansan', 'Pingo', ''];
        if (in_array(trim($this->customPetName), $defaultNames)) {
            $this->customPetName = AdminVirtualPet::PET_TYPES[$type]['name'] ?? 'Sahabat';
        }
    }

    /**
     * Simpan proses adopsi atau ubah identitas pet.
     */
    public function completeAdoption(): void
    {
        $this->validate([
            'selectedPetType' => 'required|in:kibo,bunbun,bambu,pansan,pingo',
            'customPetName' => 'required|string|min:2|max:30',
        ]);

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $type = $this->selectedPetType === 'bambu' ? 'pansan' : $this->selectedPetType;
        $name = trim($this->customPetName);
        if (empty($name)) {
            $name = AdminVirtualPet::PET_TYPES[$type]['name'] ?? 'Sahabat';
        }

        if ($this->pet) {
            $this->pet->update([
                'pet_type' => $type,
                'name' => $name,
            ]);
            $this->pet->refresh();
            $this->customPetName = $this->pet->name;
            $this->toastSuccess("Profil sahabat ELF berhasil diperbarui menjadi {$name}!");
        } else {
            $this->pet = AdminVirtualPet::create([
                'user_id' => $user->id,
                'pet_type' => $type,
                'name' => $name,
                'level' => 1,
                'xp' => 0,
                'coins' => 120, // Modal awal pet
                'hunger' => 85,
                'hygiene' => 90,
                'energy' => 95,
                'happiness' => 90,
                'inventory' => [
                    'apple' => 3,
                    'kurma' => 2,
                    'milk' => 1,
                    'kebuli' => 0,
                ],
            ]);
            $this->customPetName = $this->pet->name;
            $this->toastSuccess("Selamat! Kamu berhasil mengadopsi {$name}!", '🎉 Sahabat Baru!');
        }

        $this->isEditingName = false;
        $this->viewMode = 'arena';
        $this->petSpeech = "Senang sekali menjadi sahabatmu, {$user->name}!";
        $this->speechEmotion = 'happy';
    }

    /**
     * Memulai edit nama langsung di header arena.
     */
    public function startEditingName(): void
    {
        $this->editNameValue = $this->pet ? $this->pet->name : '';
        $this->isEditingName = true;
    }

    /**
     * Membatalkan edit nama langsung di header arena.
     */
    public function cancelEditingName(): void
    {
        $this->isEditingName = false;
    }

    /**
     * Menyimpan nama baru pet dari header arena.
     */
    public function savePetName(): void
    {
        $clean = trim($this->editNameValue);
        if (empty($clean) || strlen($clean) < 2 || strlen($clean) > 30) {
            $this->toastWarning('Nama harus antara 2 hingga 30 karakter.');
            return;
        }

        if ($this->pet) {
            $this->pet->update(['name' => $clean]);
            $this->pet->refresh();
            $this->customPetName = $clean;
            $this->editNameValue = $clean;
            $this->isEditingName = false;
            $this->toastSuccess("Nama sahabat berhasil diubah menjadi {$clean}!");
            $this->petSpeech = "Panggilan baruku sekarang adalah {$clean}!";
            $this->speechEmotion = 'happy';
        }
    }

    /**
     * Aktivitas: Ngopi Santai (Minum Kopi Susu).
     */
    public function drinkCoffee(): void
    {
        if (!$this->pet) return;
        if ($this->pet->is_sleeping) {
            $this->toastWarning("{$this->pet->name} sedang tidur! Bangunkan dulu kalau mau ngajak ngopi.");
            return;
        }

        $this->pet->energy = min(100, $this->pet->energy + 25);
        $this->pet->happiness = min(100, $this->pet->happiness + 15);
        $this->pet->addXp(6);
        $this->pet->save();

        $quotes = [
            '☕ Sluuuurp... Ahh, kopi susunya nikmat banget! Mata langsung melek dan siap fokus kerja!',
            '☕ Ngopi dulu biar gak panik! Satu teguk kopi, pikiran langsung tenang.',
            '☕ Aroma kopinya mantap! Energi rasanya langsung terisi penuh!',
        ];

        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'happy';
        $this->toastSuccess("Sluurp! {$this->pet->name} menikmati secangkir kopi (+25% Energi, +15% Bahagia)!", '☕ Waktunya Ngopi!');
    }

    /**
     * Aktivitas: Piket & Beres-Beres Ruangan / Meja Kerja.
     */
    public function doPiket(): void
    {
        if (!$this->pet) return;
        if ($this->pet->is_sleeping) {
            $this->toastWarning("{$this->pet->name} sedang tidur! Jangan diajak piket dulu.");
            return;
        }

        $this->pet->hygiene = 100;
        $this->pet->happiness = min(100, $this->pet->happiness + 10);
        $this->pet->addReward(15, 10, 'Piket & Bersih-Bersih Ruangan');
        $this->pet->save();

        $quotes = [
            '🧹 Selesai piket! Sapu lantai, lap meja, rapiin dokumen... Sekarang ruangan jadi bersih dan kinclong!',
            '🧹 Ruangan rapi bikin kerja makin nyaman dan betah! Dapat bonus koin juga nih!',
            '🧹 Meja kerja sudah bersih mengkilap! Semangat kerja jadi naik berkali-kali lipat.',
        ];

        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'clean';
        $this->toastSuccess("Piket beres! Meja & ruangan sekarang bersih berkilau (+100% Bersih, +15 Koin, +10 XP)!", '🧹 Piket Sukses!');
    }

    /**
     * Aktivitas: Membaca Buku & Novel Santai.
     */
    public function readBook(): void
    {
        if (!$this->pet) return;
        if ($this->pet->is_sleeping) {
            $this->toastWarning("{$this->pet->name} sedang tidur lelap.");
            return;
        }

        $this->pet->happiness = min(100, $this->pet->happiness + 15);
        $this->pet->addXp(8);
        $this->pet->save();

        $quotes = [
            '📚 Membaca buku seru banget! Banyak ilmu baru dan wawasan yang bikin pikiran segar.',
            '📚 "Buku adalah sahabat paling setia yang tidak pernah rewel." Rileks sebentar yuk!',
            '📚 Satu bab buku selesai dibaca! Ide-ide segar langsung bermunculan.',
            '📚 Jangan lupa rehat sejenak dari layar komputer, selingi dengan membaca buku favorit.',
        ];

        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'happy';
        $this->toastSuccess("{$this->pet->name} asyik membaca buku (+15% Bahagia, +8 XP)!", '📚 Waktu Membaca!');
    }

    /**
     * Interaksi klik Poster Motivasi Kerja di dinding.
     */
    public function interactPoster(): void
    {
        if (!$this->pet) return;

        $quotes = [
            '✨ "Kerja fokus, rehat teratur, hasil pasti memuaskan!"',
            '✨ "Semangat hari ini! Sedikit demi sedikit, lama-lama pekerjaan selesai."',
            '✨ "Tetap tenang, senyum, dan jangan lupa bahagia!"',
        ];

        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'happy';
        $this->pet->happiness = min(100, $this->pet->happiness + 5);
        $this->pet->save();
        $this->toastSuccess('Pikiran positif & rileks (+5% Bahagia)!');
    }

    /**
     * Interaksi: Bermain Bola Pantul di Kamar.
     */
    public function playBall(): void
    {
        if (!$this->pet) return;
        if ($this->pet->is_sleeping) {
            $this->toastWarning("{$this->pet->name} sedang tidur lelap!");
            return;
        }

        $this->pet->happiness = min(100, $this->pet->happiness + 6);
        $this->pet->energy = max(10, $this->pet->energy - 2);
        $this->pet->addReward(3, 2, 'Main Bola di Kamar');
        $this->pet->save();

        $quotes = [
            '⚽ Gollll! Sundulan maut yang hebat!',
            '⚽ Hahaha asik banget mantul-mantul bolanya!',
            '⚽ Oper bolanya kesini lagi, seru banget!',
        ];
        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'happy';
    }

    /**
     * Interaksi: Memutar Musik Radio Santai.
     */
    public function toggleMusic(): void
    {
        if (!$this->pet) return;
        if ($this->pet->is_sleeping) {
            $this->toastWarning("{$this->pet->name} sedang tidur lelap!");
            return;
        }

        $this->pet->happiness = min(100, $this->pet->happiness + 10);
        $this->pet->addXp(4);
        $this->pet->save();

        $quotes = [
            '🎵 Asik banget lagunya! Badan jadi otomatis goyang santai.',
            '📻 Musik radio bikin suasana kantor jadi ceria & produktif!',
            '🎶 Irama lofi-nya pas banget menemani waktu kerja kita!',
        ];
        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'happy';
        $this->toastSuccess('Musik radio diputar (+10% Bahagia, +4 XP)!', '📻 Radio Santai');
    }

    /**
     * Interaksi: Siram & Rawat Tanaman Sukulen di Meja.
     */
    public function interactPlant(): void
    {
        if (!$this->pet) return;
        $this->pet->happiness = min(100, $this->pet->happiness + 4);
        $this->pet->addXp(2);
        $this->pet->save();

        $quotes = [
            '🪴 Segarnya tanaman hijau di meja kerja bikin mata adem!',
            '🌸 Tanaman sukulennya mekar cantik! Tanda hari ini bakal berkah.',
        ];
        $this->petSpeech = $quotes[array_rand($quotes)];
        $this->speechEmotion = 'happy';
        $this->toastSuccess('Tanaman meja disiram segar (+4% Bahagia)!', '🪴 Tanaman Meja');
    }

    /**
     * Beri makan pet.
     */
    public function feedPet(string $foodKey): void
    {
        if (!$this->pet) {
            return;
        }

        $result = $this->pet->feed($foodKey);
        if ($result['success']) {
            $this->toastSuccess($result['message']);
            $this->petSpeech = "Nyam! Enak banget makanannya, terima kasih ya!";
            $this->speechEmotion = 'eating';
        } else {
            $this->toastWarning($result['message']);
        }
    }

    /**
     * Beli makanan di toko.
     */
    public function buyItem(string $foodKey): void
    {
        if (!$this->pet) {
            return;
        }

        $result = $this->pet->buyFood($foodKey);
        if ($result['success']) {
            $this->toastSuccess($result['message']);
            $this->petSpeech = "Asyik, persediaan makanan kita bertambah!";
            $this->speechEmotion = 'happy';
        } else {
            $this->toastError($result['message']);
        }
    }

    /**
     * Mandikan pet.
     */
    public function cleanPet(): void
    {
        if (!$this->pet) {
            return;
        }

        $result = $this->pet->clean();
        if ($result['success']) {
            $this->toastSuccess($result['message']);
            $this->petSpeech = "Segar sekali! Sekarang badanku bersih dan wangi!";
            $this->speechEmotion = 'clean';
        } else {
            $this->toastInfo($result['message']);
        }
    }

    /**
     * Tidurkan / Bangunkan pet.
     */
    public function toggleSleepPet(): void
    {
        if (!$this->pet) {
            return;
        }

        $result = $this->pet->toggleSleep();
        $this->toastInfo($result['message']);

        if ($this->pet->is_sleeping) {
            $this->petSpeech = "Zzz... Ssst, aku istirahat sebentar ya...";
            $this->speechEmotion = 'sleeping';
        } else {
            $this->petSpeech = "Hoaaam... Wah, energiku sudah pulih kembali!";
            $this->speechEmotion = 'happy';
        }
    }

    /**
     * Interaksi santai / tos bareng pet secara interaktif.
     */
    public function interactPet(): void
    {
        if (!$this->pet) {
            return;
        }

        $result = $this->pet->petInteraction();
        if ($result['success']) {
            $this->petSpeech = "Tos! Senang banget bisa kerja dan main bareng kamu hari ini!";
            $this->speechEmotion = 'happy';
        } else {
            $this->toastInfo($result['message']);
        }
    }

    /**
     * Ganti tab tampilan arena.
     */
    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    /**
     * Selesaikan mini game tangkap berkah dan berikan reward.
     */
    public function finishMiniGame(int $score): void
    {
        if (!$this->pet) {
            return;
        }

        $earnedCoins = min(40, max(5, (int) round($score * 1.5)));
        $earnedXp = min(30, max(5, (int) round($score * 1.2)));

        $this->pet->addReward($earnedCoins, $earnedXp, 'Mini-Game Tangkap Berkah');
        $this->pet->happiness = min(100, $this->pet->happiness + 15);
        $this->pet->save();

        $this->isMiniGameOpen = false;
        $this->toastSuccess(
            "Permainan seru! Skor: {$score} (+{$earnedCoins} Koin, +{$earnedXp} XP, +15% Senang)!",
            '🎮 Permainan Selesai!'
        );
        $this->petSpeech = "Horeee! Seru banget mainnya, besok kita main lagi ya!";
        $this->speechEmotion = 'happy';
    }

    public function render()
    {
        $uniquePetTypes = collect(AdminVirtualPet::PET_TYPES)
            ->filter(fn($val, $key) => in_array($key, ['kibo', 'bunbun', 'pansan', 'pingo']))
            ->all();

        return view('livewire.dashboard.virtual-pet-arena', [
            'pet' => $this->pet,
            'petTypes' => $uniquePetTypes,
            'foodItems' => AdminVirtualPet::FOOD_ITEMS,
            'isEditingName' => $this->isEditingName,
            'editNameValue' => $this->editNameValue,
            'viewMode' => $this->viewMode,
            'selectedPetType' => $this->selectedPetType,
            'customPetName' => $this->customPetName,
            'petSpeech' => $this->petSpeech,
            'speechEmotion' => $this->speechEmotion,
            'isMiniGameOpen' => $this->isMiniGameOpen,
        ]);
    }
}
