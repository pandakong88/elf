<?php

namespace App\Livewire\Dashboard;

use App\Models\TripeaksLeaderboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TripeaksArena extends Component
{
    // Status Permainan: 'idle', 'playing', 'victory', 'game_over'
    #[Locked]
    public string $gameState = 'idle';

    // State Tableau 28 Kartu
    #[Locked]
    public array $tableau = [];

    // Tumpukan Cadangan (Stock) & Kartu Umpan (Waste)
    #[Locked]
    public array $stock = [];

    #[Locked]
    public array $waste = [];

    // Statistik & Skor Game Saat Ini
    #[Locked]
    public int $score = 0;

    #[Locked]
    public int $combo = 0;

    #[Locked]
    public int $maxComboThisSession = 0;

    #[Locked]
    public int $clearedPeaksCount = 0;

    #[Locked]
    public array $peaksCleared = [0 => false, 1 => false, 2 => false];

    #[Locked]
    public int $lastPointsEarned = 0;

    #[Locked]
    public string $lastActionMessage = '';

    #[Locked]
    public bool $isNewHighScore = false;

    #[Locked]
    public bool $isNewMaxCombo = false;

    #[Locked]
    public int $movesCount = 0;

    #[Locked]
    public int $finalRemainingSeconds = 0;

    #[Locked]
    public int $timeBonusEarned = 0;

    // Leaderboard & Personal Best
    public $leaderboard = [];
    public ?array $userStats = null;

    /**
     * Relasi penutup/bloker tiap kartu di piramida (Card Index => [Array of Blocker Indices])
     */
    protected array $blockerMap = [
        // Row 0 (Peaks) - ditimpa oleh Row 1
        0 => [3, 4],
        1 => [5, 6],
        2 => [7, 8],

        // Row 1 - ditimpa oleh Row 2
        3 => [9, 10],
        4 => [10, 11],
        5 => [12, 13],
        6 => [13, 14],
        7 => [15, 16],
        8 => [16, 17],

        // Row 2 - ditimpa oleh Row 3 (Base)
        9  => [18, 19],
        10 => [19, 20],
        11 => [20, 21],
        12 => [21, 22],
        13 => [22, 23],
        14 => [23, 24],
        15 => [24, 25],
        16 => [25, 26],
        17 => [26, 27],

        // Row 3 (Base 18..27) - Tidak ditimpa kartu manapun
        18 => [], 19 => [], 20 => [], 21 => [], 22 => [],
        23 => [], 24 => [], 25 => [], 26 => [], 27 => [],
    ];

    public function mount()
    {
        $this->loadLeaderboardData();
    }

    /**
     * Muat data leaderboard Tri-Peaks dan statistik pemain saat ini.
     */
    public function loadLeaderboardData()
    {
        $topPlayers = TripeaksLeaderboard::getTopPlayers(10);

        $this->leaderboard = $topPlayers->map(function ($row, $index) {
            $user = $row->user;
            $roles = $user ? $user->roles->pluck('name')->toArray() : [];
            $roleLabel = count($roles) > 0 ? ucfirst($roles[0]) : 'Admin';

            return [
                'rank'                => $index + 1,
                'user_id'             => $row->user_id,
                'name'                => $user ? $user->name : 'Anonim',
                'role'                => $roleLabel,
                'high_score'          => $row->high_score,
                'max_combo'           => $row->max_combo,
                'total_games'         => $row->total_games,
                'total_cleared_peaks' => $row->total_cleared_peaks,
                'total_victories'     => $row->total_victories,
                'last_played'         => $row->last_played_at ? $row->last_played_at->diffForHumans() : '-',
                'is_current_user'     => Auth::check() && Auth::id() === $row->user_id,
            ];
        })->toArray();

        if (Auth::check()) {
            $myRecord = TripeaksLeaderboard::where('user_id', Auth::id())->first();
            if ($myRecord) {
                $this->userStats = [
                    'high_score'          => $myRecord->high_score,
                    'max_combo'           => $myRecord->max_combo,
                    'total_games'         => $myRecord->total_games,
                    'total_cleared_peaks' => $myRecord->total_cleared_peaks,
                    'total_victories'     => $myRecord->total_victories,
                ];
            }
        }
    }

    /**
     * Mulai permainan baru Tri-Peaks Solitaire.
     */
    public function startGame()
    {
        $deck = $this->generateFullDeck();

        // 1. Ambil 28 kartu pertama untuk menyusun Piramida Tri-Peaks
        $this->tableau = [];
        for ($i = 0; $i < 28; $i++) {
            $card = array_pop($deck);
            $row = $this->getRowByIndex($i);
            // Row 3 (dasar, index 18..27) langsung terbuka, sisanya tertutup
            $isFaceUp = ($i >= 18);

            $this->tableau[$i] = [
                'index'       => $i,
                'card'        => $card,
                'is_removed'  => false,
                'is_face_up'  => $isFaceUp,
                'row'         => $row,
            ];
        }

        // 2. Ambil 1 kartu dari sisa dek untuk menjadi Kartu Umpan awal (Waste Pile)
        $initialWaste = array_pop($deck);
        $this->waste = [$initialWaste];

        // 3. Pangkas Tumpukan Cadangan jadi 14 kartu (Menantang & presisi!)
        $this->stock = array_slice($deck, 0, 14);

        // Reset skor, combo, & timer
        $this->score = 0;
        $this->combo = 0;
        $this->maxComboThisSession = 0;
        $this->clearedPeaksCount = 0;
        $this->peaksCleared = [0 => false, 1 => false, 2 => false];
        $this->lastPointsEarned = 0;
        $this->lastActionMessage = 'Permainan dimulai! Waktu berjalan 80 detik. Cari kartu selisih ±1.';
        $this->isNewHighScore = false;
        $this->isNewMaxCombo = false;
        $this->movesCount = 0;
        $this->finalRemainingSeconds = 0;
        $this->timeBonusEarned = 0;
        $this->gameState = 'playing';

        $this->dispatch('tripeaks-started');
    }

    /**
     * Menentukan baris/tingkat piramida berdasarkan indeks kartu.
     */
    protected function getRowByIndex(int $index): int
    {
        if ($index <= 2) return 0;   // Peaks (3 cards)
        if ($index <= 8) return 1;   // Level 2 (6 cards)
        if ($index <= 17) return 2;  // Level 3 (9 cards)
        return 3;                    // Base (10 cards)
    }

    /**
     * Memeriksa apakah kartu tidak tertutup oleh kartu di bawahnya.
     */
    protected function isCardUncovered(int $index): bool
    {
        $blockers = $this->blockerMap[$index] ?? [];
        foreach ($blockers as $bIndex) {
            // Jika kartu penutupnya belum diambil, maka kartu ini masih tertutup
            if (isset($this->tableau[$bIndex]) && !$this->tableau[$bIndex]['is_removed']) {
                return false;
            }
        }
        return true;
    }

    /**
     * Memeriksa apakah nilai dua kartu selisih 1 (termasuk King dan As saling terhubung).
     */
    public function isAdjacent(int $rank1, int $rank2): bool
    {
        if (abs($rank1 - $rank2) === 1) {
            return true;
        }
        // King (13) terhubung dengan As (1)
        if (($rank1 === 1 && $rank2 === 13) || ($rank1 === 13 && $rank2 === 1)) {
            return true;
        }
        return false;
    }

    /**
     * Pemain mengklik salah satu kartu di piramida.
     */
    public function pickCard(int $index, int $remainingSeconds = 0)
    {
        if ($this->gameState !== 'playing') {
            return;
        }

        if (!isset($this->tableau[$index])) {
            return;
        }

        $node = $this->tableau[$index];
        if ($node['is_removed'] || !$node['is_face_up']) {
            return;
        }

        $currentWaste = end($this->waste);
        if (!$currentWaste) {
            return;
        }

        $cardRank = (int) $node['card']['rank'];
        $wasteRank = (int) $currentWaste['rank'];

        // Verifikasi aturan selisih 1 angka
        if (!$this->isAdjacent($cardRank, $wasteRank)) {
            $this->dispatch('tripeaks-invalid-pick', ['index' => $index]);
            return;
        }

        // --- Langkah Valid: Ambil Kartu! ---
        $this->movesCount++;
        $this->tableau[$index]['is_removed'] = true;
        $this->waste[] = $node['card'];

        // Tambah combo berantai
        $this->combo++;
        $this->maxComboThisSession = max($this->maxComboThisSession, $this->combo);

        // Kalkulasi Poin Combo: 100 * combo
        $stepPoints = 100 * $this->combo;
        $this->score += $stepPoints;
        $this->lastPointsEarned = $stepPoints;
        $this->lastActionMessage = "🔥 COMBO x{$this->combo}! (+{$stepPoints} Poin)";

        // Periksa apakah kartu yang diambil adalah salah satu dari 3 Puncak (Index 0, 1, 2)
        if (in_array($index, [0, 1, 2]) && !$this->peaksCleared[$index]) {
            $this->peaksCleared[$index] = true;
            $this->clearedPeaksCount++;
            $peakBonus = 1500;
            $this->score += $peakBonus;
            $this->lastPointsEarned += $peakBonus;
            $peakName = ($index === 0 ? 'Puncak Kiri' : ($index === 1 ? 'Puncak Tengah' : 'Puncak Kanan'));
            $this->lastActionMessage = "⭐ {$peakName} BERSIH! (+{$peakBonus} Bonus Puncak!)";
            $this->dispatch('tripeaks-peak-cleared', ['peak' => $index]);
        }

        // Buka kartu-kartu di atasnya yang kini telah bebas dari penutup
        $newlyUncovered = [];
        for ($i = 0; $i < 28; $i++) {
            if (!$this->tableau[$i]['is_removed'] && !$this->tableau[$i]['is_face_up']) {
                if ($this->isCardUncovered($i)) {
                    $this->tableau[$i]['is_face_up'] = true;
                    $newlyUncovered[] = $i;
                }
            }
        }

        $this->dispatch('tripeaks-card-picked', [
            'index'          => $index,
            'combo'          => $this->combo,
            'newlyUncovered' => $newlyUncovered,
            'addSeconds'     => 2,
        ]);

        // Periksa apakah seluruh 28 kartu telah bersih (TRI-PEAKS VICTORY)
        if ($this->checkVictory()) {
            $this->handleVictory($remainingSeconds);
            return;
        }

        // Periksa apakah pemain macet tanpa kartu cadangan
        $this->checkStuckCondition();
    }

    /**
     * Pemain menarik kartu baru dari Tumpukan Cadangan (Stock Pile).
     * Menerapkan Penalti Kasino: -150 Poin & Reset Combo ke 0!
     */
    public function drawFromStock()
    {
        if ($this->gameState !== 'playing') {
            return;
        }

        if (empty($this->stock)) {
            $this->checkStuckCondition();
            return;
        }

        $newCard = array_pop($this->stock);
        $this->waste[] = $newCard;

        // Reset combo berantai & Terapkan Penalti Kasino (-150 poin)
        $this->combo = 0;
        $penalty = 150;
        $this->score = max(0, $this->score - $penalty);
        $this->lastPointsEarned = -$penalty;
        $this->lastActionMessage = "💸 Buka Cadangan: -{$penalty} Poin & Combo Reset!";

        $this->dispatch('tripeaks-stock-drawn', [
            'stockCount' => count($this->stock),
            'wasteCard'  => $newCard,
            'penalty'    => $penalty,
        ]);

        $this->checkStuckCondition();
    }

    /**
     * Waktu permainan 80 detik habis (Time Attack Timeout).
     */
    public function timeUp()
    {
        if ($this->gameState !== 'playing') {
            return;
        }

        $this->gameState = 'game_over';
        $this->lastActionMessage = '⏰ WAKTU HABIS! Waktu permainan 80 detik telah berakhir.';
        $this->saveLeaderboardScore(false);

        $this->dispatch('tripeaks-game-over', [
            'score'    => $this->score,
            'maxCombo' => $this->maxComboThisSession,
            'reason'   => 'timeout',
        ]);
    }

    /**
     * Memeriksa apakah semua 28 kartu di piramida telah bersih.
     */
    protected function checkVictory(): bool
    {
        foreach ($this->tableau as $node) {
            if (!$node['is_removed']) {
                return false;
            }
        }
        return true;
    }

    /**
     * Menangani kemenangan sempurna (Tri-Peaks Cleared).
     */
    protected function handleVictory(int $remainingSeconds = 0)
    {
        $victoryBonus = 5000;
        $unusedStockBonus = count($this->stock) * 250;
        $validRemainingSeconds = max(0, min(120, $remainingSeconds));
        $this->finalRemainingSeconds = $validRemainingSeconds;
        $this->timeBonusEarned = $validRemainingSeconds * 100;

        $totalBonus = $victoryBonus + $unusedStockBonus + $this->timeBonusEarned;

        $this->score += $totalBonus;
        $this->gameState = 'victory';
        $this->lastActionMessage = "👑 TRI-PEAKS DITAKLUKKAN! Bonus Kemenangan +{$totalBonus} Poin (Waktu: +{$this->timeBonusEarned})!";

        $this->saveLeaderboardScore(true);

        $this->dispatch('tripeaks-victory', [
            'score'      => $this->score,
            'bonus'      => $totalBonus,
            'timeBonus'  => $this->timeBonusEarned,
            'stockBonus' => $unusedStockBonus,
        ]);
    }

    /**
     * Memeriksa apakah tidak ada langkah yang bisa diambil dan cadangan sudah habis.
     */
    protected function checkStuckCondition()
    {
        if (!empty($this->stock)) {
            return; // Masih ada kartu cadangan, belum macet total
        }

        $currentWaste = end($this->waste);
        if (!$currentWaste) {
            return;
        }
        $wasteRank = (int) $currentWaste['rank'];

        // Cek apakah ada kartu terbuka di piramida yang selisih 1
        foreach ($this->tableau as $node) {
            if (!$node['is_removed'] && $node['is_face_up']) {
                if ($this->isAdjacent((int) $node['card']['rank'], $wasteRank)) {
                    return; // Masih ada setidaknya 1 langkah valid!
                }
            }
        }

        // Macet total & tumpukan cadangan habis -> Game Over!
        $this->gameState = 'game_over';
        $this->lastActionMessage = 'Cadangan habis & tidak ada kartu selisih ±1. Permainan selesai!';

        $this->saveLeaderboardScore(false);

        $this->dispatch('tripeaks-game-over', [
            'score'    => $this->score,
            'maxCombo' => $this->maxComboThisSession,
        ]);
    }

    /**
     * Simpan skor ke database leaderboard.
     */
    protected function saveLeaderboardScore(bool $isVictory)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $result = TripeaksLeaderboard::recordScore(
                $user,
                $this->score,
                $this->maxComboThisSession,
                $this->clearedPeaksCount,
                $isVictory
            );

            $this->isNewHighScore = $result['isNewHighScore'];
            $this->isNewMaxCombo = $result['isNewMaxCombo'];
            $this->loadLeaderboardData();
        }
    }

    /**
     * Menghasilkan Dek 52 Kartu Lengkap & Acak (CSPRNG).
     */
    protected function generateFullDeck(): array
    {
        $suits = [
            'spades'   => ['name' => 'Sekop', 'color' => 'black', 'symbol' => '♠', 'html' => '&spades;'],
            'hearts'   => ['name' => 'Hati',  'color' => 'red',   'symbol' => '♥', 'html' => '&hearts;'],
            'clubs'    => ['name' => 'Keriting', 'color' => 'black', 'symbol' => '♣', 'html' => '&clubs;'],
            'diamonds' => ['name' => 'Wajik', 'color' => 'red',   'symbol' => '♦', 'html' => '&diams;'],
        ];

        $ranks = [
            1  => 'A',
            2  => '2',
            3  => '3',
            4  => '4',
            5  => '5',
            6  => '6',
            7  => '7',
            8  => '8',
            9  => '9',
            10 => '10',
            11 => 'J',
            12 => 'Q',
            13 => 'K',
        ];

        $deck = [];
        $id = 1;
        foreach ($suits as $suitKey => $suitMeta) {
            foreach ($ranks as $rankNum => $rankLabel) {
                $rankTitle = match($rankNum) {
                    1       => 'As',
                    11      => 'Jack',
                    12      => 'Queen',
                    13      => 'King',
                    default => (string) $rankNum,
                };

                $deck[] = [
                    'id'          => $id++,
                    'suit'        => $suitKey,
                    'suit_name'   => $suitMeta['name'],
                    'rank'        => $rankNum,
                    'rank_label'  => $rankLabel,
                    'title'       => "{$rankTitle} {$suitMeta['name']}",
                    'color'       => $suitMeta['color'],
                    'symbol'      => $suitMeta['symbol'],
                    'symbol_html' => $suitMeta['html'],
                ];
            }
        }

        // Fisher-Yates CSPRNG Shuffle
        for ($i = count($deck) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $tmp = $deck[$i];
            $deck[$i] = $deck[$j];
            $deck[$j] = $tmp;
        }

        return $deck;
    }

    protected array $cardCoordinates = [
        // Row 0 (Peaks)
        0 => ['x' => 1.5, 'row' => 0],
        1 => ['x' => 4.5, 'row' => 0],
        2 => ['x' => 7.5, 'row' => 0],
        // Row 1
        3 => ['x' => 1.0, 'row' => 1],
        4 => ['x' => 2.0, 'row' => 1],
        5 => ['x' => 4.0, 'row' => 1],
        6 => ['x' => 5.0, 'row' => 1],
        7 => ['x' => 7.0, 'row' => 1],
        8 => ['x' => 8.0, 'row' => 1],
        // Row 2
        9  => ['x' => 0.5, 'row' => 2],
        10 => ['x' => 1.5, 'row' => 2],
        11 => ['x' => 2.5, 'row' => 2],
        12 => ['x' => 3.5, 'row' => 2],
        13 => ['x' => 4.5, 'row' => 2],
        14 => ['x' => 5.5, 'row' => 2],
        15 => ['x' => 6.5, 'row' => 2],
        16 => ['x' => 7.5, 'row' => 2],
        17 => ['x' => 8.5, 'row' => 2],
        // Row 3 (Base)
        18 => ['x' => 0.0, 'row' => 3],
        19 => ['x' => 1.0, 'row' => 3],
        20 => ['x' => 2.0, 'row' => 3],
        21 => ['x' => 3.0, 'row' => 3],
        22 => ['x' => 4.0, 'row' => 3],
        23 => ['x' => 5.0, 'row' => 3],
        24 => ['x' => 6.0, 'row' => 3],
        25 => ['x' => 7.0, 'row' => 3],
        26 => ['x' => 8.0, 'row' => 3],
        27 => ['x' => 9.0, 'row' => 3],
    ];

    public function render()
    {
        $currentWaste = end($this->waste) ?: null;
        $playableIndices = [];
        $targetRanks = [];

        if ($currentWaste && $this->gameState === 'playing') {
            $wRank = (int) $currentWaste['rank'];

            // Hitung target angka yang cocok (selisih 1)
            $rankLabels = [
                1  => 'A', 2  => '2', 3  => '3', 4  => '4', 5  => '5',
                6  => '6', 7  => '7', 8  => '8', 9  => '9', 10 => '10',
                11 => 'J', 12 => 'Q', 13 => 'K',
            ];
            $lowRank = ($wRank === 1) ? 13 : $wRank - 1;
            $highRank = ($wRank === 13) ? 1 : $wRank + 1;
            $targetRanks = [$rankLabels[$lowRank], $rankLabels[$highRank]];

            foreach ($this->tableau as $index => $node) {
                if (!$node['is_removed'] && $node['is_face_up']) {
                    if ($this->isAdjacent((int) $node['card']['rank'], $wRank)) {
                        $playableIndices[] = $index;
                    }
                }
            }
        }

        // Bantuan visual cahaya kartu hanya aktif di 3 langkah awal permainan
        // Setelah langkah ke-3, cahaya padam agar pemain tertantang mencari kartu sendiri!
        $showGlowHelp = ($this->movesCount < 3);

        return view('livewire.dashboard.tripeaks-arena', [
            'currentWaste'    => $currentWaste,
            'stockCount'      => count($this->stock),
            'playableIndices' => $playableIndices,
            'targetRanks'     => $targetRanks,
            'cardCoordinates' => $this->cardCoordinates,
            'showGlowHelp'          => $showGlowHelp,
            'movesCount'            => $this->movesCount,
            'finalRemainingSeconds' => $this->finalRemainingSeconds,
            'timeBonusEarned'       => $this->timeBonusEarned,
        ]);
    }
}
