<?php

namespace App\Modules\Core\Http\Controllers;

use App\Models\User;
use App\Modules\Core\Http\Resources\UserResource;
use App\Modules\Core\Services\UserService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * GET /api/v1/core/users
     * Butuh permission: manage-users
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('manage-users');

        $users = User::with(['person', 'roles'])
                     ->when($request->filled('active'), fn($q) => $q->where('is_active', $request->boolean('active')))
                     ->paginate($request->integer('per_page', 15));

        return UserResource::collection($users);
    }

    /**
     * POST /api/v1/core/users
     * Buat user baru dan hubungkan ke person yang sudah ada.
     * Butuh permission: manage-users
     */
    public function store(Request $request): UserResource|JsonResponse
    {
        $this->authorize('manage-users');

        $validated = $request->validate([
            'person_id' => 'required|uuid|exists:persons,id',
            'email'     => 'required|email|max:255',
            'username'  => 'nullable|string|max:50|unique:users,username',
            'password'  => 'required|string|min:8|confirmed',
            'roles'     => 'nullable|array',
            'roles.*'   => 'string|exists:roles,name',
        ]);

        try {
            $user = $this->userService->create($validated, $validated['person_id']);

            // Assign roles jika diberikan
            if (! empty($validated['roles'])) {
                foreach ($validated['roles'] as $role) {
                    $this->userService->assignRole($user, $role);
                }
            }
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new UserResource($user->load(['person', 'roles']));
    }

    /**
     * GET /api/v1/core/users/{user}
     */
    public function show(User $user): UserResource
    {
        $this->authorize('manage-users');

        $user->load(['person', 'roles']);

        return new UserResource($user);
    }

    /**
     * PUT /api/v1/core/users/{user}
     */
    public function update(Request $request, User $user): UserResource|JsonResponse
    {
        $this->authorize('manage-users');

        $validated = $request->validate([
            'username'  => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        try {
            if (! empty($validated['password'])) {
                $this->userService->updatePassword($user, $validated['password']);
                unset($validated['password'], $validated['password_confirmation']);
            }

            $user->update(array_filter($validated, fn($v) => $v !== null));
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new UserResource($user->fresh()->load(['person', 'roles']));
    }

    /**
     * DELETE /api/v1/core/users/{user}
     * Soft deactivate — BUKAN hard delete.
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('manage-users');

        // Tidak boleh deactivate diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak dapat menonaktifkan akun sendiri.'], 422);
        }

        $this->userService->deactivate($user);

        return response()->json(['message' => "User '{$user->email}' berhasil dinonaktifkan."]);
    }

    /**
     * POST /api/v1/core/users/{user}/roles
     * Assign role ke user.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        try {
            $this->userService->assignRole($user, $validated['role']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => "Role '{$validated['role']}' berhasil diberikan ke {$user->email}."]);
    }

    /**
     * DELETE /api/v1/core/users/{user}/roles
     * Cabut role dari user.
     */
    public function revokeRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('manage-roles');

        $validated = $request->validate([
            'role' => 'required|string',
        ]);

        try {
            $this->userService->revokeRole($user, $validated['role']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => "Role '{$validated['role']}' berhasil dicabut dari {$user->email}."]);
    }
}
