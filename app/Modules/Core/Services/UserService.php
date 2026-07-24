<?php

namespace App\Modules\Core\Services;

use App\Models\User;
use App\Modules\Core\Models\Person;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserService
{
    /**
     * Buat user account baru dan hubungkan ke person yang sudah ada.
     *
     * @throws DomainException
     */
    public function create(array $data, string $personId): User
    {
        $person = Person::findOrFail($personId);

        // Satu person maksimal satu user account
        $existing = User::where('person_id', $personId)->exists();
        if ($existing) {
            throw new DomainException("Person '{$person->name}' sudah memiliki akun user.");
        }

        // Email harus unik
        if (User::where('email', $data['email'])->exists()) {
            throw new DomainException("Email '{$data['email']}' sudah terdaftar.");
        }

        return DB::transaction(function () use ($data, $person) {
            return User::create([
                'id'        => Str::uuid()->toString(),
                'person_id' => $person->id,
                'name'      => $person->name,     // sync nama dari person
                'username'  => $data['username'] ?? null,
                'email'     => $data['email'],
                'password'  => $data['password'], // akan di-hash via cast
                'is_active' => true,
            ]);
        });
    }

    /**
     * Assign Spatie role ke user.
     * Throw DomainException jika role tidak ada.
     *
     * @throws DomainException
     */
    public function assignRole(User $user, string $roleName): void
    {
        $role = Role::findByName($roleName, 'web');

        if (! $role) {
            throw new DomainException("Role '{$roleName}' tidak ditemukan.");
        }

        $user->assignRole($roleName);

        activity()
            ->performedOn($user)
            ->withProperties(['role' => $roleName])
            ->log("Role '{$roleName}' diberikan ke user '{$user->email}'.");
    }

    /**
     * Cabut Spatie role dari user.
     *
     * @throws DomainException
     */
    public function revokeRole(User $user, string $roleName): void
    {
        if (! $user->hasRole($roleName)) {
            throw new DomainException("User tidak memiliki role '{$roleName}'.");
        }

        $user->removeRole($roleName);

        activity()
            ->performedOn($user)
            ->withProperties(['role' => $roleName])
            ->log("Role '{$roleName}' dicabut dari user '{$user->email}'.");
    }

    /**
     * Nonaktifkan user account (soft deactivate — JANGAN hard delete).
     */
    public function deactivate(User $user): void
    {
        $user->update(['is_active' => false]);

        // Invalidate semua token JWT aktif milik user ini
        // JWT stateless — logout dilakukan sisi client, tapi kita
        // bisa blacklist dengan cara revoke semua token via blacklist jika
        // JWTBlacklist enabled. Untuk sekarang, is_active check di AuthService sudah cukup.
        activity()
            ->performedOn($user)
            ->log("User '{$user->email}' dinonaktifkan.");
    }

    /**
     * Update password user.
     *
     * @throws DomainException
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        if (strlen($newPassword) < 8) {
            throw new DomainException('Password minimal 8 karakter.');
        }

        $user->update(['password' => $newPassword]); // hashed via cast
    }
}
