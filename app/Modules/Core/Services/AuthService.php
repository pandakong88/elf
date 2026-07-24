<?php

namespace App\Modules\Core\Services;

use App\Models\User;
use DomainException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Login dengan email dan password.
     * Throw DomainException jika credentials salah atau akun tidak aktif.
     *
     * @return array{ token: string, token_type: string, expires_in: int, user: User }
     */
    public function login(string $email, string $password): array
    {
        $token = auth('api')->attempt(['email' => $email, 'password' => $password]);

        if (! $token) {
            throw new DomainException('Email atau password salah.');
        }

        /** @var User $user */
        $user = auth('api')->user();

        if (! $user->is_active) {
            auth('api')->logout();
            throw new DomainException('Akun ini telah dinonaktifkan. Hubungi administrator.');
        }

        $user->load(['person', 'roles']);

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user'       => $user,
        ];
    }

    /**
     * Logout user — invalidate token saat ini.
     */
    public function logout(): void
    {
        auth('api')->logout();
    }

    /**
     * Refresh token — kembalikan token baru.
     *
     * @return array{ token: string, token_type: string, expires_in: int }
     */
    public function refresh(): array
    {
        $token = auth('api')->refresh();

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    /**
     * Ambil user yang sedang login dengan relasi lengkap.
     */
    public function me(): User
    {
        /** @var User $user */
        $user = auth('api')->user();
        $user->load(['person', 'roles']);

        return $user;
    }
}
