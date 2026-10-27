<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class TripeaksLeaderboard extends Model
{
    use HasFactory;

    protected $table = 'game_tripeaks_leaderboards';

    protected $fillable = [
        'user_id',
        'high_score',
        'max_combo',
        'total_games',
        'total_cleared_peaks',
        'total_victories',
        'last_played_at',
    ];

    protected function casts(): array
    {
        return [
            'high_score'          => 'integer',
            'max_combo'           => 'integer',
            'total_games'         => 'integer',
            'total_cleared_peaks' => 'integer',
            'total_victories'     => 'integer',
            'last_played_at'      => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat hasil pertandingan Tri-Peaks user.
     * Mengembalikan array status rekor baru.
     */
    public static function recordScore(User $user, int $score, int $combo, int $clearedPeaks, bool $isVictory): array
    {
        try {
            $record = static::firstOrNew(['user_id' => $user->id]);

            $isNewHighScore = $score > ($record->high_score ?? 0);
            $isNewMaxCombo = $combo > ($record->max_combo ?? 0);

            $record->high_score = max($record->high_score ?? 0, $score);
            $record->max_combo = max($record->max_combo ?? 0, $combo);
            $record->total_games = ($record->total_games ?? 0) + 1;
            $record->total_cleared_peaks = ($record->total_cleared_peaks ?? 0) + $clearedPeaks;
            if ($isVictory) {
                $record->total_victories = ($record->total_victories ?? 0) + 1;
            }
            $record->last_played_at = now();
            $record->save();

            return [
                'isNewHighScore' => $isNewHighScore,
                'isNewMaxCombo'  => $isNewMaxCombo,
                'record'         => $record,
            ];
        } catch (\Throwable $e) {
            Log::error('TripeaksLeaderboard::recordScore error: ' . $e->getMessage());
            return [
                'isNewHighScore' => false,
                'isNewMaxCombo'  => false,
                'record'         => null,
            ];
        }
    }

    /**
     * Ambil 10 skor tertinggi Tri-Peaks.
     */
    public static function getTopPlayers(int $limit = 10)
    {
        return static::with(['user.roles'])
            ->orderByDesc('high_score')
            ->orderByDesc('max_combo')
            ->limit($limit)
            ->get();
    }
}
