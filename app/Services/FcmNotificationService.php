<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FcmNotificationService
{
    /**
     * Send a push notification to all devices of a specific user.
     */
    public function sendToUser(string|int $userId, string $title, string $body, array $data = [], ?string $clickUrl = null): void
    {
        $tokens = FcmToken::tokensForUser($userId);

        if (empty($tokens)) {
            Log::info("[FCM] No tokens found for user_id: {$userId}");
            return;
        }

        foreach ($tokens as $token) {
            $this->sendViaHttpV1($token, $title, $body, $data, $clickUrl);
        }
    }

    /**
     * Send a push notification to all users in a given role.
     */
    public function sendToRole(string $role, string $title, string $body, array $data = [], ?string $clickUrl = null): void
    {
        try {
            $users = \App\Models\User::role($role)->get();
            foreach ($users as $user) {
                $this->sendToUser($user->id, $title, $body, $data, $clickUrl);
            }
        } catch (\Throwable $e) {
            Log::warning("[FCM] Failed finding users with role {$role}: " . $e->getMessage());
        }
    }

    /**
     * Send to multiple roles (e.g. ['admin', 'bendahara', 'super-admin']).
     * If roles don't match or return 0 tokens, broadcast to all active FCM tokens.
     */
    public function sendToRoles(array $roles, string $title, string $body, array $data = [], ?string $clickUrl = null): void
    {
        $sentUserIds = [];

        try {
            $users = \App\Models\User::whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('name', $roles);
            })->get();

            foreach ($users as $user) {
                $sentUserIds[] = $user->id;
                $this->sendToUser($user->id, $title, $body, $data, $clickUrl);
            }
        } catch (\Throwable $e) {
            Log::warning("[FCM] sendToRoles query error: " . $e->getMessage());
        }

        // Jika tidak ada user dengan role tersebut yang punya token, kirim ke SEMUA token aktif pengurus
        if (empty($sentUserIds)) {
            Log::info("[FCM] No users found with specified roles, broadcasting to all registered FCM tokens");
            $this->broadcastToAll($title, $body, $data, $clickUrl);
        }
    }

    /**
     * Broadcast notification to all registered devices.
     */
    public function broadcastToAll(string $title, string $body, array $data = [], ?string $clickUrl = null): void
    {
        $tokens = FcmToken::where('last_active_at', '>', now()->subDays(60))->pluck('token')->unique();

        foreach ($tokens as $token) {
            $this->sendViaHttpV1($token, $title, $body, $data, $clickUrl);
        }
    }

    /**
     * Internal: Send notification via FCM HTTP v1 API.
     */
    private function sendViaHttpV1(string $token, string $title, string $body, array $data = [], ?string $clickUrl = null): void
    {
        try {
            $projectId = config('services.firebase.project_id');
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::warning("[FCM] Could not get access token — skipping notification");
                return;
            }

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'webpush' => [
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                            'icon'  => '/icons/icon-192x192.png',
                            'badge' => '/icons/icon-72x72.png',
                            'requireInteraction' => true,
                            'tag'   => 'elvith-payment-' . ($data['type'] ?? 'notif'),
                        ],
                        'fcm_options' => [
                            'link' => $clickUrl ?? '/',
                        ],
                    ],
                ],
            ];

            // Google FCM v1 requires 'data' to be a JSON Object (Map), NEVER an empty List []
            if (!empty($data)) {
                $payload['message']['data'] = array_map('strval', $data);
            } else {
                $payload['message']['data'] = ['click_url' => (string) ($clickUrl ?? '/')];
            }

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

            if ($response->failed()) {
                $err = $response->json('error.message', 'Unknown error');
                Log::warning("[FCM] Failed to send to token (masked): " . substr($token, 0, 20) . "... Error: {$err}");

                // If token is invalid/unregistered, delete it
                if ($response->status() === 404 || str_contains($err, 'UNREGISTERED')) {
                    \App\Models\FcmToken::where('token', $token)->delete();
                    Log::info("[FCM] Deleted unregistered token");
                }
            } else {
                Log::info("[FCM] Notification sent successfully");
            }
        } catch (\Throwable $e) {
            Log::error("[FCM] Exception: " . $e->getMessage());
        }
    }

    /**
     * Get OAuth2 access token using service account credentials.
     */
    private function getAccessToken(): ?string
    {
        try {
            $credentialPath = config('services.firebase.credentials_path');

            if (!$credentialPath || !file_exists($credentialPath)) {
                Log::warning("[FCM] Service account credentials not found at: {$credentialPath}");
                return null;
            }

            $credentials = json_decode(file_get_contents($credentialPath), true);

            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ]));

            $header = rtrim(strtr($header, '+/', '-_'), '=');
            $payload = rtrim(strtr($payload, '+/', '-_'), '=');

            $privateKey = $credentials['private_key'];
            openssl_sign("{$header}.{$payload}", $signature, $privateKey, 'SHA256');
            $signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

            $jwt = "{$header}.{$payload}.{$signature}";

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error("[FCM] getAccessToken failed: " . $e->getMessage());
            return null;
        }
    }
}