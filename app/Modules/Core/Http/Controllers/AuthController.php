<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Resources\UserResource;
use App\Modules\Core\Services\AuthService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * POST /api/v1/auth/login
     * Public endpoint — tidak butuh token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->login($validated['email'], $validated['password']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        return response()->json([
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user'       => new UserResource($result['user']),
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     * Protected — butuh Bearer token.
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    /**
     * POST /api/v1/auth/refresh
     * Protected — kembalikan token baru, invalidate token lama.
     */
    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refresh();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak valid atau sudah kadaluarsa.'], 401);
        }

        return response()->json($result);
    }

    /**
     * GET /api/v1/auth/me
     * Protected — kembalikan profil lengkap user dengan permissions.
     */
    public function me(): UserResource
    {
        $user = $this->authService->me();

        // withPermissions = true → sertakan permissions dan organization_ids
        return UserResource::withPermissions($user);
    }
}
