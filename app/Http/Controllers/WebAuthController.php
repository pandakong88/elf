<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WebAuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $devUsers = User::with(['roles', 'person'])
            ->where('is_active', true)
            ->get()
            ->map(function ($user) {
                return [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'roles'  => $user->roles->pluck('name')->implode(', ') ?: 'Tanpa Role',
                    'gender' => $user->person?->gender ? ($user->person->gender === 'L' ? 'Putra' : 'Putri') : '-',
                ];
            })
            ->sortBy('name')
            ->values();

        return view('auth.login', compact('devUsers'));
    }

    /**
     * Proses login sesi web.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Cek status is_active sebelum mengizinkan login
        $user = User::where('email', $validated['email'])->first();

        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ]);
    }

    /**
     * Proses logout sesi web.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Halaman Dashboard otorisasi checker.
     */
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['person.roles.organization']);

        // Mengambil seluruh permission miliknya (Spatie)
        $permissions = $user->getAllPermissions()->pluck('name')->values()->toArray();
        $roles = $user->getRoleNames()->values()->toArray();
        $organizationIds = $user->getOrganizationIds();

        return view('dashboard', compact('user', 'roles', 'permissions', 'organizationIds'));
    }
}
