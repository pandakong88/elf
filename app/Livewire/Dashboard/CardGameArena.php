<?php

namespace App\Livewire\Dashboard;

use App\Models\CardGameLeaderboard;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardGameArena extends Component
{
    // Status Permainan: 'idle', 'playing', 'revealing', 'game_over'
    public string $gameState = 'idle';
    public string $roundResult = ''; // 'won', 'tie', 'lost'
    public bool $isRoundWon = false;

    // Kartu yang sedang aktif dan kartu tebakan yang akan dibuka
    public ?array $currentCard = null;
    public ?array $nextCard = null;

    // Statistik sesi saat ini
    public int $score = 0;
    public int $streak = 0;
    public int $bestStreakThisSession = 0;
    public int $correctGuessesCount = 0;
    public ?string $lastGuess = null;     // 'higher', 'lower'
    public ?string $lastResult = null;    // 'correct', 'tie', 'wrong'
    public string $resultMessage = '';
    public int $lastPointsEarned = 0;

    // Status Rekor
    public bool $isNewHighScore = false;
    public bool $isNewMaxStreak = false;

    // Dek & Tumpukan Kartu Terbuang (Card Counting)
    public array $deck = [];
    public array $discardPile = [];
    public int $totalInitialCards = 52;

    // Power-Up System (1x pakai per game)
    public bool $powerUpPeekUsed = false;
    public bool $powerUpShieldActive = true;
    public bool $powerUpShieldUsed = false;
    public bool $powerUpSwapUsed = false;
    public ?string $peekedHint = null;
    public bool $shieldTriggeredThisRound = false;

    // Leaderboard & Personal Best
    public $leaderboard = [];
    public ?array $userStats = null;

    public function mount()
    {
        $this->loadLeaderboardData();
    }

    /**
     * Muat data leaderboard dan statistik pemain saat ini.
     */
    public function loadLeaderboardData()
    {
        $topPlayers = CardGameLeaderboard::getTopPlayers(10);
        
        $this->leaderboard = $topPlayers->map(function ($row, $index) {
            $user = $row->user;
            $roles = $user ? $user->roles->pluck('name')->toArray() : [];
            $roleLabel = count($roles) > 0 ? ucfirst($roles[0]) : 'Santri';

            return [
                'rank'                  => $index + 1,
                'user_id'               => $row->user_id,
                'name'                  => $user ? $user->name : 'Anonim',
                'role'                  => $roleLabel,
                'high_score'            => $row->high_score,
                'max_streak'            => $row->max_streak,
                'total_games'           => $row->total_games,
                'total_correct_guesses' => $row->total_correct_guesses,
                'last_played'           => $row->last_played_at ? $row->last_played_at->diffForHumans() : '-',
                'is_current_user'       => Auth::check() && Auth::id() === $row->user_id,
            ];
        })->toArray();

        if (Auth::check()) {
            $userRecord = CardGameLeaderboard::getUserRecord(Auth::id());
            if ($userRecord) {
                $rank = CardGameLeaderboard::getUserRank(Auth::id(), $userRecord->high_score);
                $this->userStats = [
                    'high_score' => $userRecord->high_score,
                    'max_streak' => $userRecord->max_streak,
                    'total_games'=> $userRecord->total_games,
                    'rank'       => $rank,
                ];
            } else {
                $this->userStats = [
                    'high_score' => 0,
                    'max_streak' => 0,
                    'total_games'=> 0,
                    'rank'       => '-',
                ];
            }
        }
    }

    /**
     * Generate 52 kartu standar lengkap (Bicycle Style).
     * Menggunakan randomisasi CSPRNG (Cryptographically Secure).
     * Ace (As) = 14 (Tertinggi), 2 = Terendah.
     */
    protected function generateFullDeck(): array
    {
        $suits = [
            ['name' => 'spades',   'symbol' => '♠', 'color' => 'black', 'label' => 'Sekop',   'symbol_html' => '&spades;'],
            ['name' => 'hearts',   'symbol' => '♥', 'color' => 'red',   'label' => 'Hati',    'symbol_html' => '&hearts;'],
            ['name' => 'diamonds', 'symbol' => '♦', 'color' => 'red',   'label' => 'Wajik',   'symbol_html' => '&diams;'],
            ['name' => 'clubs',    'symbol' => '♣', 'color' => 'black', 'label' => 'Keriting', 'symbol_html' => '&clubs;'],
        ];

        $ranks = [
            2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7',
            8 => '8', 9 => '9', 10 => '10', 11 => 'J', 12 => 'Q', 13 => 'K', 14 => 'A'
        ];

        $deck = [];
        foreach ($suits as $s) {
            foreach ($ranks as $rankValue => $rankLabel) {
                $rankTitle = match ($rankValue) {
                    11 => 'Jack',
                    12 => 'Queen (Ratu)',
                    13 => 'King (Raja)',
                    14 => 'Ace (As Tertinggi)',
                    default => (string)$rankValue,
                };

                $deck[] = [
                    'id'          => $s['name'] . '_' . $rankValue,
                    'suit'        => $s['name'],
                    'symbol'      => $s['symbol'],
                    'symbol_html' => $s['symbol_html'],
                    'color'       => $s['color'],
                    'suit_label'  => $s['label'],
                    'rank'        => $rankValue,
                    'rank_label'  => $rankLabel,
                    'title'       => "{$rankTitle} {$s['label']}",
                ];
            }
        }

        // Kocok acak menggunakan secure shuffle
        for ($i = count($deck) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $tmp = $deck[$i];
            $deck[$i] = $deck[$j];
            $deck[$j] = $tmp;
        }

        return $deck;
    }

    /**
     * Ambil satu kartu acak murni yang berbeda dari kartu saat ini.
     */
    protected function drawRandomCard(?array $excludeCard = null): array
    {
        $deck = $this->generateFullDeck();
        if ($excludeCard) {
            $deck = array_values(array_filter($deck, fn($c) => $c['id'] !== $excludeCard['id']));
        }
        $randomIndex = random_int(0, count($deck) - 1);
        return $deck[$randomIndex];
    }

    /**
     * Mulai Permainan Baru dengan Dek 52 Kartu Terbatas
     */
    public function startGame()
    {
        $this->score = 0;
        $this->streak = 0;
        $this->bestStreakThisSession = 0;
        $this->correctGuessesCount = 0;
        $this->lastGuess = null;
        $this->lastResult = null;
        $this->lastPointsEarned = 0;
        $this->isRoundWon = false;
        $this->roundResult = '';
        $this->isNewHighScore = false;
        $this->isNewMaxStreak = false;
        $this->resultMessage = 'Pilih apakah kartu berikutnya LEBIH BESAR (▲) atau LEBIH KECIL (▼)!';

        // 1. Inisialisasi Dek 52 Kartu Lengkap & Acak
        $this->deck = $this->generateFullDeck();
        $this->discardPile = [];

        // 2. Tarik kartu pertama dari dek (tidak akan muncul lagi)
        $this->currentCard = array_pop($this->deck);
        $this->nextCard = null;

        // 3. Reset Status 3 Power-Ups (1x pakai per sesi)
        $this->powerUpPeekUsed = false;
        $this->powerUpShieldActive = true;
        $this->powerUpShieldUsed = false;
        $this->powerUpSwapUsed = false;
        $this->peekedHint = null;
        $this->shieldTriggeredThisRound = false;

        $this->gameState = 'playing';

        $this->dispatch('game-started');
    }

    /**
     * Power-Up 1: Kartu Intip (X-Ray Peek)
     * Mengintip nilai kartu berikutnya sebelum menebak (1x per game).
     */
    public function usePowerUpPeek()
    {
        if ($this->powerUpPeekUsed || $this->gameState !== 'playing' || empty($this->deck)) {
            return;
        }

        // Kartu yang diintip adalah kartu teratas di dek yang akan ditarik berikutnya
        $peekCard = end($this->deck);
        $this->powerUpPeekUsed = true;
        $this->peekedHint = "X-RAY PEEK: Kartu berikutnya adalah {$peekCard['title']} (Nilai {$peekCard['rank']})";

        $this->dispatch('power-up-peeked', [
            'card' => $peekCard
        ]);
    }

    /**
     * Power-Up 3: Tukar Kartu (Swap Deck)
     * Menukar kartu saat ini jika posisinya nanggung (1x per game).
     */
    public function usePowerUpSwap()
    {
        if ($this->powerUpSwapUsed || $this->gameState !== 'playing' || empty($this->deck)) {
            return;
        }

        $oldCard = $this->currentCard;
        $this->discardPile[] = $oldCard;
        $this->currentCard = array_pop($this->deck);
        $this->powerUpSwapUsed = true;
        $this->peekedHint = null;
        $this->resultMessage = "🔄 KARTU DITUKAR! {$oldCard['title']} diganti dengan {$this->currentCard['title']}.";

        $this->dispatch('power-up-swapped', [
            'newCard' => $this->currentCard
        ]);
    }

    /**
     * Pemain Memilih: 'higher' (Lebih Besar) atau 'lower' (Lebih Kecil)
     */
    public function guess(string $choice)
    {
        if ($this->gameState !== 'playing' || !$this->currentCard) {
            return;
        }

        if (!in_array($choice, ['higher', 'lower'])) {
            return;
        }

        $this->lastGuess = $choice;

        // Ambil kartu berikutnya dari sisa dek 52 kartu (Anti-Duplikat)
        if (empty($this->deck)) {
            // Jika dek 52 kartu habis (streak sangat tinggi), kocok ulang kartu terbuang
            if (!empty($this->discardPile)) {
                $this->deck = $this->discardPile;
                $this->discardPile = [];
                for ($i = count($this->deck) - 1; $i > 0; $i--) {
                    $j = random_int(0, $i);
                    $tmp = $this->deck[$i];
                    $this->deck[$i] = $this->deck[$j];
                    $this->deck[$j] = $tmp;
                }
            } else {
                $this->deck = $this->generateFullDeck();
            }
        }

        $newCard = array_pop($this->deck);
        $this->nextCard = $newCard;

        $currentRank = (int) $this->currentCard['rank'];
        $nextRank = (int) $newCard['rank'];

        // Masuk ke fase revealing (kartu baru dibuka dan diperlihatkan ke pemain terlebih dahulu)
        $this->gameState = 'revealing';

        $isTie = ($nextRank === $currentRank);
        $isCorrect = false;
        $this->shieldTriggeredThisRound = false;

        if ($isTie) {
            // SERI: Nilai kartu sama persis! Pemain TIDAK kalah (Push/Safe bonus)
            $isCorrect = true;
            $this->isRoundWon = true;
            $this->roundResult = 'tie';
            $this->lastResult = 'tie';
            $this->streak++;
            $this->correctGuessesCount++;
            $this->bestStreakThisSession = max($this->bestStreakThisSession, $this->streak);

            $this->lastPointsEarned = 150 * $this->streak;
            $this->score += $this->lastPointsEarned;
            $this->resultMessage = "🤝 SERI! Kartu bernilai sama ({$currentRank} vs {$nextRank}). Antum selamat & streak lanjut! (+{$this->lastPointsEarned})";
        } elseif ($choice === 'higher') {
            $isCorrect = ($nextRank > $currentRank);
        } elseif ($choice === 'lower') {
            $isCorrect = ($nextRank < $currentRank);
        }

        if (!$isTie) {
            if ($isCorrect) {
                $this->isRoundWon = true;
                $this->roundResult = 'won';
                $this->lastResult = 'correct';
                $this->streak++;
                $this->correctGuessesCount++;
                $this->bestStreakThisSession = max($this->bestStreakThisSession, $this->streak);

                $this->lastPointsEarned = 100 * $this->streak;
                $this->score += $this->lastPointsEarned;

                $quotes = [
                    "TEPAT SEKALI! Firasat antum tajam! (+{$this->lastPointsEarned} Poin)",
                    "AURA MUSYRIF MENYALA! (+{$this->lastPointsEarned} Poin)",
                    "JITU! Streak combo {$this->streak}x berturut-turut! (+{$this->lastPointsEarned})",
                    "MANTAP! Kartu berhasil ditebak dengan akurat! (+{$this->lastPointsEarned})",
                ];
                $this->resultMessage = $quotes[array_rand($quotes)];
            } else {
                // Periksa apakah Perisai Nyawa masih aktif!
                if ($this->powerUpShieldActive && !$this->powerUpShieldUsed) {
                    $this->powerUpShieldActive = false;
                    $this->powerUpShieldUsed = true;
                    $this->shieldTriggeredThisRound = true;

                    $this->isRoundWon = true; // Selamat dari eliminasi
                    $this->roundResult = 'shield';
                    $this->lastResult = 'shield';
                    $this->lastPointsEarned = 0;

                    $comparisonText = $choice === 'higher' ? 'tidak lebih besar dari' : 'tidak lebih kecil dari';
                    $this->resultMessage = "🛡️ PERISAI PECAH! Tebakan {$choice} meleset ({$newCard['title']} {$comparisonText} {$this->currentCard['title']}), tapi Perisai melindungimu dari Game Over! Streak {$this->streak}x tetap aman!";
                } else {
                    $this->isRoundWon = false;
                    $this->roundResult = 'lost';
                    $this->lastResult = 'wrong';
                    $this->lastPointsEarned = 0;
                    $comparisonText = $choice === 'higher' ? 'tidak lebih besar dari' : 'tidak lebih kecil dari';
                    $this->resultMessage = "Meleset! {$newCard['title']} (Nilai {$nextRank}) {$comparisonText} {$this->currentCard['title']} (Nilai {$currentRank}).";
                }
            }
        }

        // Kirim event ke frontend untuk memicu animasi 3D Card Flip membuka kartu berikutnya!
        $this->dispatch('start-reveal-animation', [
            'choice'      => $choice,
            'isCorrect'   => $isCorrect,
            'isTie'       => $isTie,
            'isShield'    => $this->shieldTriggeredThisRound,
            'currentRank' => $currentRank,
            'nextRank'    => $nextRank,
            'score'       => $this->score,
            'streak'      => $this->streak,
        ]);
    }

    /**
     * Dipanggil oleh frontend setelah animasi reveal selesai dan pemain MENANG / DILINDUNGI PERISAI.
     * Kartu berikutnya bergeser menjadi kartu saat ini, lalu kartu misteri kembali ditutup.
     */
    public function advanceRound()
    {
        if ($this->gameState !== 'revealing' || !$this->nextCard) {
            return;
        }

        // Kartu lama masuk ke tumpukan kartu terbuang (Discard Pile)
        $this->discardPile[] = $this->currentCard;
        $this->currentCard = $this->nextCard;
        $this->nextCard = null;
        $this->gameState = 'playing';
        $this->roundResult = '';
        $this->isRoundWon = false;
        $this->peekedHint = null;
        $this->shieldTriggeredThisRound = false;
    }

    /**
     * Dipanggil oleh frontend setelah animasi reveal selesai dan pemain KALAH.
     * Menyimpan skor ke database dan beralih ke layar Game Over.
     */
    public function finalizeGameOver()
    {
        $this->gameState = 'game_over';
        $this->roundResult = 'lost';
        $this->isRoundWon = false;

        // Pastikan resultMessage mencerminkan kekalahan:
        if ($this->lastResult !== 'wrong' && $this->nextCard) {
            $currentRank = (int) $this->currentCard['rank'];
            $nextRank = (int) $this->nextCard['rank'];
            $this->resultMessage = "Permainan berakhir! {$this->nextCard['title']} (Nilai {$nextRank}) vs {$this->currentCard['title']} (Nilai {$currentRank}).";
        }

        if (Auth::check()) {
            $saveResult = CardGameLeaderboard::recordScore(
                Auth::user(),
                $this->score,
                $this->bestStreakThisSession,
                $this->correctGuessesCount
            );

            $this->isNewHighScore = $saveResult['is_new_high_score'];
            $this->isNewMaxStreak = $saveResult['is_new_max_streak'];

            $this->loadLeaderboardData();
        }

        $this->dispatch('show-game-over-summary', [
            'score'          => $this->score,
            'streak'         => $this->bestStreakThisSession,
            'isNewHighScore' => $this->isNewHighScore,
        ]);
    }

    /**
     * Hitung statistik kartu yang tersisa di dek (Card Counting).
     */
    public function getDeckStatsProperty(): array
    {
        $cardsLeft = count($this->deck);
        $discarded = count($this->discardPile);
        
        $highLeft = 0; // J, Q, K, A (11 - 14)
        $lowLeft = 0;  // 2 - 6
        $midLeft = 0;  // 7 - 10

        foreach ($this->deck as $c) {
            $r = (int) $c['rank'];
            if ($r >= 11) {
                $highLeft++;
            } elseif ($r <= 6) {
                $lowLeft++;
            } else {
                $midLeft++;
            }
        }

        return [
            'cards_left' => $cardsLeft,
            'discarded'  => $discarded,
            'total'      => $this->totalInitialCards,
            'high_left'  => $highLeft,
            'low_left'   => $lowLeft,
            'mid_left'   => $midLeft,
        ];
    }

    /**
     * Reset Permainan kembali ke awal
     */
    public function resetGame()
    {
        $this->gameState = 'idle';
        $this->score = 0;
        $this->streak = 0;
        $this->currentCard = null;
        $this->nextCard = null;
        $this->lastResult = null;
        $this->lastGuess = null;
        $this->lastPointsEarned = 0;
        $this->isRoundWon = false;
        $this->roundResult = '';
        $this->isNewHighScore = false;
        $this->isNewMaxStreak = false;
        $this->resultMessage = '';
        $this->deck = [];
        $this->discardPile = [];
        $this->powerUpPeekUsed = false;
        $this->powerUpShieldActive = true;
        $this->powerUpShieldUsed = false;
        $this->powerUpSwapUsed = false;
        $this->peekedHint = null;
        $this->shieldTriggeredThisRound = false;
        $this->loadLeaderboardData();
    }

    public function render()
    {
        return view('livewire.dashboard.card-game-arena');
    }
}
