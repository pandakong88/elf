<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Core\Models\LandingPageContent;
use App\Modules\Core\Models\Person;
use App\Modules\Keuangan\Models\Bill;
use App\Modules\Keuangan\Models\BillPayment;
use App\Modules\Kepengasuhan\Models\Perizinan;
use App\Modules\Kepengasuhan\Models\Violation;
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
                'subtitle'    => 'Pengasuh & Pimpinan Pondok',
                'icon'        => '',
                'icon_type'   => 'crown',
                'bg_color'    => 'from-amber-950/60 to-slate-900',
                'border_color'=> 'border-amber-500/40 hover:border-amber-400',
                'badge_color' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('manajemen', $u['roles_array']) || in_array('pengasuh', $u['roles_array']))->values(),
            ],
            'bendahara_pondok' => [
                'id'          => 'bendahara_pondok',
                'title'       => 'Bendahara Pondok',
                'subtitle'    => 'Keuangan Pusat & SPP',
                'icon'        => '',
                'icon_type'   => 'cashier',
                'bg_color'    => 'from-blue-950/60 to-slate-900',
                'border_color'=> 'border-blue-500/40 hover:border-blue-400',
                'badge_color' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('bendahara-pondok', $u['roles_array']))->values(),
            ],
            'bendahara_unit' => [
                'id'          => 'bendahara_unit',
                'title'       => 'Bendahara Unit / Madin',
                'subtitle'    => 'Keuangan Unit Putra / Putri',
                'icon'        => '',
                'icon_type'   => 'unit',
                'bg_color'    => 'from-purple-950/60 to-slate-900',
                'border_color'=> 'border-purple-500/40 hover:border-purple-400',
                'badge_color' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('bendahara-putra', $u['roles_array']) || in_array('bendahara-putri', $u['roles_array']) || in_array('bendahara-unit', $u['roles_array']))->values(),
            ],
            'musyrif' => [
                'id'          => 'musyrif',
                'title'       => 'Musyrif & Ustadz',
                'subtitle'    => 'Pengasuhan Asrama & KBM',
                'icon'        => '',
                'icon_type'   => 'users',
                'bg_color'    => 'from-teal-950/60 to-slate-900',
                'border_color'=> 'border-teal-500/40 hover:border-teal-400',
                'badge_color' => 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                'users'       => $allUsers->filter(fn($u) => in_array('musyrif', $u['roles_array']) || in_array('guru', $u['roles_array']))->values(),
            ],
        ];

        $cmsContent     = LandingPageContent::getContent();
        $devModeEnabled = (string) ($cmsContent['dev_quick_switcher_enabled'] ?? '1');
        $devPassword    = (string) ($cmsContent['dev_quick_switcher_password'] ?? 'rahasia123');

        return view('auth.login', compact('devUsers', 'roleGroups', 'cmsContent', 'devModeEnabled', 'devPassword'));
    }

    /**
     * Proses autentikasi login via form.
     */
    public function login(Request $request)
    {
        $loginInput = $request->input('email') ?? $request->input('login') ?? $request->input('username');

        if (empty($loginInput)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau username wajib diisi.'],
            ]);
        }

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($fieldType, $loginInput)->first();

        if (! $user || ! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun tidak ditemukan atau dalam status nonaktif.'],
            ]);
        }

        if (! Auth::attempt([$fieldType => $loginInput, 'password' => $request->password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => ['Kata sandi yang Anda masukkan salah.'],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Dev Fast Switch Login (Khusus Lingkungan Dev/Demo).
     */
    public function devLogin(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);

        if (! $user->is_active) {
            return back()->withErrors(['login' => 'Akun tersebut sedang nonaktif.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
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

        // Detect Gender Scope
        $genderScope = null;
        $isCentral = $user->hasAnyRole([
            'super-admin',
            'admin',
            'manajemen',
            'pengasuh',
            'bendahara',
            'bendahara-pondok',
            'bendahara-pusat',
            'bendahara-unit',
            'admin-data',
        ]);
        if (!$isCentral) {
            if ($user->hasRole(['bendahara-putra', 'lurah-putra'])) {
                $genderScope = 'L';
            } elseif ($user->hasRole(['bendahara-putri', 'lurah-putri'])) {
                $genderScope = 'P';
            } elseif ($user->person?->gender) {
                $genderScope = $user->person->gender;
            }
        }

        // Date Context & Hijri Calculation
        $now = now();
        $gregorianDate = $now->locale('id')->translatedFormat('l, d F Y');

        $hijriFullDate = '21 Safar 1448 H';
        if (class_exists('IntlDateFormatter')) {
            try {
                $hijriFormatter = new \IntlDateFormatter(
                    'id_ID@calendar=islamic-civil',
                    \IntlDateFormatter::FULL,
                    \IntlDateFormatter::NONE,
                    'Asia/Jakarta',
                    \IntlDateFormatter::TRADITIONAL
                );
                $hijriFullDate = $hijriFormatter->format($now->timestamp);
            } catch (\Throwable $e) {
                // Fallback
            }
        }

        preg_match('/(?:[A-Za-z]+,\s+)?(\d+)\s+([A-Za-z]+)\s+(\d+)/', $hijriFullDate, $matches);
        $hijriDay   = $matches[1] ?? '21';
        $hijriMonth = $matches[2] ?? 'Safar';
        $hijriYear  = $matches[3] ?? '1448';

        // Monthly calendar grid data
        $startOfMonth   = $now->copy()->startOfMonth();
        $daysInMonth    = $now->daysInMonth;
        $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sun, 6 = Sat

        $calendarDays = [];
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $calendarDays[] = null;
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = $startOfMonth->copy()->day($d);
            $hDayNum = '';
            if (isset($hijriFormatter)) {
                try {
                    $hDateStr = $hijriFormatter->format($date->timestamp);
                    preg_match('/(?:[A-Za-z]+,\s+)?(\d+)/', $hDateStr, $hMatch);
                    $hDayNum = $hMatch[1] ?? '';
                } catch (\Throwable $e) {
                    $hDayNum = '';
                }
            }

            $calendarDays[] = [
                'day'       => $d,
                'hijri_day' => $hDayNum,
                'is_today'  => $date->isToday(),
            ];
        }

        $stats = [
            'gregorian_date' => $gregorianDate,
            'hijri_date'     => "{$hijriDay} {$hijriMonth} {$hijriYear} H",
            'hijri_month'    => "{$hijriMonth} {$hijriYear} H",
            'gender_scope'   => $genderScope,
        ];

        $calendarData = [
            'masehi_month_name' => $now->locale('id')->translatedFormat('F Y'),
            'hijri_month_name'  => "{$hijriMonth} {$hijriYear} H",
            'calendar_days'     => $calendarDays,
            'today_masehi'      => $gregorianDate,
            'today_hijri'       => "{$hijriDay} {$hijriMonth} {$hijriYear} H",
        ];

        $importantEvents = [
            [
                'title'    => 'Hari Kemerdekaan RI',
                'date'     => '17 Agustus 2026',
                'badge'    => 'Hari Libur Nasional 🇮🇩',
                'category' => 'nasional',
                'icon'     => '🇮🇩',
            ],
            [
                'title'    => 'Hari Santri Nasional',
                'date'     => '22 Oktober 2026',
                'badge'    => 'Peringatan Resmi 🕌',
                'category' => 'santri',
                'icon'     => '🕌',
            ],
            [
                'title'    => 'Maulid Nabi Muhammad SAW',
                'date'     => '25 Agustus 2026 / 12 Rabiul Awal 1448 H',
                'badge'    => 'PHBI 🌟',
                'category' => 'islamic',
                'icon'     => '🌟',
            ],
            [
                'title'    => 'Hari Asyura (10 Muharram)',
                'date'     => '25 Juli 2026 / 10 Muharram 1448 H',
                'badge'    => 'Sunnah Puasa & Yatim 💛',
                'category' => 'islamic',
                'icon'     => '💛',
            ],
            [
                'title'    => 'Awal Ramadhan 1448 H',
                'date'     => '08 Februari 2027 / 1 Ramadhan 1448 H',
                'badge'    => 'Ibadah Wajib 🌙',
                'category' => 'islamic',
                'icon'     => '🌙',
            ],
            [
                'title'    => 'Hari Raya Idul Fitri 1448 H',
                'date'     => '10 Maret 2027 / 1 Syawal 1448 H',
                'badge'    => 'Hari Raya 🎉',
                'category' => 'islamic',
                'icon'     => '🎉',
            ],
        ];

        return view('dashboard', compact(
            'user',
            'roles',
            'permissions',
            'organizationIds',
            'stats',
            'calendarData',
            'importantEvents'
        ));
    }
}
