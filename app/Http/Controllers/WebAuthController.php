<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Core\Models\LandingPageContent;
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

        $allUsers = User::with(['roles', 'person'])
            ->where('is_active', true)
            ->get()
            ->map(function ($user) {
                $rolesList = $user->roles->pluck('name')->toArray();
                return [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'roles_array' => $rolesList,
                    'roles'       => implode(', ', $rolesList) ?: 'Tanpa Role',
                    'gender'      => $user->person?->gender ? ($user->person->gender === 'L' ? 'Putra' : 'Putri') : '-',
                ];
            })
            ->sortBy('name')
            ->values();

        $devUsers = $allUsers;

        $roleGroups = [
            'super_admin' => [
                'id'          => 'super_admin',
                'title'       => 'Super Admin',
                'subtitle'    => 'Akses Penuh Sistem',
                'icon'        => '',
                'icon_type'   => 'shield',
                'bg_color'    => 'from-emerald-950/60 to-slate-900',
                'border_color'=> 'border-emerald-500/40 hover:border-emerald-400',
                'badge_color' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('super-admin', $u['roles_array']))->values(),
            ],
            'manajemen' => [
                'id'          => 'manajemen',
                'title'       => 'Manajemen',
                'subtitle'    => 'Pengelola & Otoritas',
                'icon'        => '',
                'icon_type'   => 'building',
                'bg_color'    => 'from-sky-950/60 to-slate-900',
                'border_color'=> 'border-sky-500/40 hover:border-sky-400',
                'badge_color' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('manajemen', $u['roles_array']))->values(),
            ],
            'bendahara_pondok' => [
                'id'          => 'bendahara_pondok',
                'title'       => 'Bendahara Pondok',
                'subtitle'    => 'Keuangan Utama',
                'icon'        => '',
                'icon_type'   => 'banknotes',
                'bg_color'    => 'from-amber-950/60 to-slate-900',
                'border_color'=> 'border-amber-500/40 hover:border-amber-400',
                'badge_color' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('bendahara-pondok', $u['roles_array']))->values(),
            ],
            'bendahara_unit' => [
                'id'          => 'bendahara_unit',
                'title'       => 'Bendahara Pa / Pi',
                'subtitle'    => 'Kasir & Setoran Unit',
                'icon'        => '',
                'icon_type'   => 'credit-card',
                'bg_color'    => 'from-purple-950/60 to-slate-900',
                'border_color'=> 'border-purple-500/40 hover:border-purple-400',
                'badge_color' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('bendahara-putra', $u['roles_array']) || in_array('bendahara-putri', $u['roles_array']) || in_array('bendahara-unit', $u['roles_array']))->values(),
            ],
        ];

        $devModeEnabled = (string)(LandingPageContent::where('key', 'dev_quick_switcher_enabled')->value('value') ?? '1');
        $devPassword    = (string)(LandingPageContent::where('key', 'dev_quick_switcher_password')->value('value') ?? 'rahasia123');

        return view('auth.login', compact('devUsers', 'roleGroups', 'devModeEnabled', 'devPassword'));
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
