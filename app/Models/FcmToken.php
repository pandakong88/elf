<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcmToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_info',
        'last_active_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Save or update a token for a user (upsert by token value).
     */
    public static function saveToken(string|int $userId, string $token, ?string $deviceInfo = null): void
    {
        static::updateOrCreate(
            ['token' => $token],
            [
                'user_id'        => (string) $userId,
                'device_info'    => $deviceInfo,
                'last_active_at' => now(),
            ]
        );

        // Clean up tokens older than 60 days for this user
        static::where('user_id', $userId)
            ->where('last_active_at', '<', now()->subDays(60))
            ->delete();
    }

    /**
     * Get all active tokens for a specific user.
     */
    public static function tokensForUser(string|int $userId): array
    {
        return static::where('user_id', $userId)
            ->where('last_active_at', '>', now()->subDays(60))
            ->pluck('token')
            ->toArray();
    }
}