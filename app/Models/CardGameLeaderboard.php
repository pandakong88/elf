<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class CardGameLeaderboard extends Model
{
    use HasFactory;

    protected $table = 'game_card_leaderboards';

    protected $fillable = [
        'user_id',
        'high_score',
        'max_streak',
        'total_games',
        'total_correct_guesses',
        'last_played_at',
    ];

    protected function casts(): array
    {
        return [
            'high_score'            => 'integer',
            'max_streak'            => 'integer',
            'total_games'           => 'integer',
            'total_correct_guesses' => 'integer',
            'last_played_at'        => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat hasil pertandingan user.
     * Mengembalikan array berisi status apakah rekor baru berhasil dipecahkan.
     */
    public static function recordScore(User $user, int $score, int $streak, int $correctGuesses): array
    {
        try {
            $record = static::firstOrNew(['user_id' => $user->id]);

            $isNewHighScore = $score > ($record->high_score ?? 0);
            $isNewMaxStreak = $streak > ($record->max_streak ?? 0);

            $record->high_score = max($record->high_score ?? 0, $score);
            $record->max_streak = max($record->max_streak ?? 0, $streak);
            $record->total_games = ($record->total_games ?? 0) + 1;
            $record->total_correct_guesses = ($record->total_correct_guesses ?? 0) + $correctGuesses;
            $record->last_played_at = now();
            $record->save();

            return [
                'success'           => true,
                'is_new_high_score' => $isNewHighScore && $score > 0,
                'is_new_max_streak' => $isNewMaxStreak && $streak > 0,
                'record'            => $record,
            ];
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat skor game kartu ke database: ' . $e->getMessage());
            return [
                'success'           => false,
                'is_new_high_score' => false,
                'is_new_max_streak' => false,
                'record'            => null,
            ];
        }
    }

    /**
     * Ambil daftar leaderboard teratas.
     */
    public static function getTopPlayers(int $limit = 10)
    {
        try {
            return static::with(['user.person', 'user.roles'])
                ->orderByDesc('high_score')
                ->orderByDesc('max_streak')
                ->take($limit)
                ->get();
        } catch (\Throwable $e) {
            Log::warning('Gagal mengambil leaderboard game kartu: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Ambil rekor user saat ini.
     */
    public static function getUserRecord(?string $userId)
    {
        if (!$userId) {
            return null;
        }

        try {
            return static::where('user_id', $userId)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Ambil peringkat rank user saat ini.
     */
    public static function getUserRank(?string $userId, int $userHighScore): string|int
    {
        if (!$userId) {
            return '-';
        }

        try {
            return static::where('high_score', '>', $userHighScore)->count() + 1;
        } catch (\Throwable $e) {
            return '-';
        }
    }
}

