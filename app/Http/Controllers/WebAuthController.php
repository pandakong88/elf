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
        if (!$user->hasRole('super-admin') && !$user->hasRole('manajemen')) {
            if ($user->person?->gender) {
                $genderScope = $user->person->gender;
            } elseif ($user->hasRole('bendahara-putra')) {
                $genderScope = 'L';
            } elseif ($user->hasRole('bendahara-putri')) {
                $genderScope = 'P';
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

        $dawahList = [
            [
                'number'      => 1,
                'category'    => 'Amanah & Kejujuran',
                'badge'       => 'QS. An-Nisa: 58 📖',
                'arab'        => 'إِنَّ ٱللَّهَ يَأْمُرُكُمْ أَن تُؤَدُّوا۟ ٱلْأَمَٰنَٰتِ إِلَىٰٓ أَهْلِهَا',
                'translation' => 'Sesungguhnya Allah menyuruh kamu menyampaikan amanat kepada yang berhak menerimanya...',
                'hikmah'      => 'Sesungguhnya orang-orang yang menepati janji dan menjaga amanah dalam mengelola keuangan serta pendidikan santri, Allah akan membukakan baginya pintu keberkahan yang tak terduga-duga.'
            ],
            [
                'number'      => 2,
                'category'    => 'Menuntut Ilmu & Keikhlasan',
                'badge'       => 'HR. Muslim 📚',
                'arab'        => 'مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا سَهَّلَ اللَّهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ',
                'translation' => 'Barangsiapa menempuh jalan untuk menuntut ilmu, maka Allah akan mudahkan baginya jalan menuju Surga.',
                'hikmah'      => 'Sesungguhnya orang-orang yang ikhlas mendidik dan mengabdi untuk santri, setiap lelah dan peluhnya dicatat sebagai pahala jariyah yang tak pernah terputus.'
            ],
            [
                'number'      => 3,
                'category'    => 'Keberkahan Rezeki & Infak',
                'badge'       => 'QS. Al-Baqarah: 261 💰',
                'arab'        => 'مَّثَلُ ٱلَّذِينَ يُنفِقُونَ أَمْوَٰلَهُمْ فِى سَبِيلِ ٱللَّهِ كَمَثَلِ حَبَّةٍ أَنۢبَتَتْ سَبْعَ سَنَابِلَ',
                'translation' => 'Perumpamaan orang yang menginfakkan hartanya di jalan Allah seperti sebutir biji yang menumbuhkan tujuh tangkai...',
                'hikmah'      => 'Sesungguhnya orang-orang yang mengelola infak & syahriah santri dengan jujur dan efisien, setiap rupiahnya menjadi benteng pertolongan Allah dari segala mara bahaya.'
            ],
            [
                'number'      => 4,
                'category'    => 'Persaudaraan & Ukhuwah',
                'badge'       => 'HR. Bukhari & Muslim 💚',
                'arab'        => 'الْمُؤْمِنُ لِلْمُؤْمِنِ كَالْبُنْيَانِ يَشُدُّ بَعْضُهُ بَعْضًا',
                'translation' => 'Orang mukmin dengan mukmin lainnya bagaikan satu bangunan yang saling menguatkan satu sama lain.',
                'hikmah'      => 'Sesungguhnya orang-orang yang merawat persaudaraan dan tidak mudah berburuk sangka sesama pengurus pondok, hatinya akan selalu dilapangkan dan diterangi oleh Allah.'
            ],
            [
                'number'      => 5,
                'category'    => 'Syukur & Kecukupan Rezeki',
                'badge'       => 'QS. Ibrahim: 7 🤲',
                'arab'        => 'لَئِن شَكَرْتُمْ لَأَزِيدَنَّكُمْ ۖ وَلَئِن كَفَرْتُمْ إِنَّ عَذَابِى لَشَدِيدٌ',
                'translation' => 'Sesungguhnya jika kamu bersyukur, pasti Kami akan menambah (nikmat) kepadamu...',
                'hikmah'      => 'Sesungguhnya orang-orang yang senantiasa bersyukur atas sedikit maupun banyaknya rezeki hari ini, Allah akan cukupkan semua kebutuhan hidup dan keluarganya.'
            ],
            [
                'number'      => 6,
                'category'    => 'Kesabaran & Ketenangan Jiwa',
                'badge'       => 'QS. Ar-Ra\'d: 28 🕊️',
                'arab'        => 'ٱلَّذِينَ ءَامَنُوا۟ وَتَطْمَئِنُّ قُلُوبُهُم بِذِكْرِ ٱللَّهِ ۗ أَلَا بِذِكْرِ ٱللَّهِ تَطْمَئِنُّ ٱلْقُلُوبُ',
                'translation' => 'Ingatlah, hanya dengan mengingat Allah hati menjadi tenteram.',
                'hikmah'      => 'Sesungguhnya orang-orang yang menghadapi setiap ujian tugas dan amanah pesantren dengan sabar dan zikir, Allah akan menganugerahkan ketenangan jiwa yang hakiki.'
            ]
        ];

        $khodamList = [
            [
                'title'       => 'Lele Berkepala Ikan',
                'icon'        => '🐟',
                'badge'       => 'Kebocoran Aura: 99%',
                'description' => 'Khodam absurd legendaris! Fisiknya lele tapi kepalanya tetap ikan. Membawa kemampuan bernafas di air dan tetap santai walau dikejar deadline tugas pesantren.'
            ],
            [
                'title'       => 'Channa Limbata',
                'icon'        => '🐟⚡',
                'badge'       => 'Tingkat Keganasan: 97%',
                'description' => 'Khodam ikan galak bergaris oranye-biru! Memiliki insting teritorial tinggi, siap pasang badan pas ada yang mau serobot antrean mandi di asrama.'
            ],
            [
                'title'       => 'Nyi Blorong',
                'icon'        => '🐍✨',
                'badge'       => 'Keanggunan Mistis: 95%',
                'description' => 'Khodam bersisik emas mistis! Membawa keahlian menjaga kerapihan sarung dan memancarkan aroma wewangian mistis nan elegan.'
            ],
            [
                'title'       => 'Ultraman Ribut',
                'icon'        => '🦸‍♂️',
                'badge'       => 'Tingkat Kerepotan: 100%',
                'description' => 'Khodam penyelamat bumi dari Silat Malaysia! Siap bikin ribut suasana asrama tiap kali ada yang ketahuan sembunyiin sandal Swallow.'
            ],
            [
                'title'       => 'BoBoiBoy Air',
                'icon'        => '💧',
                'badge'       => 'Tingkat Kesegaran: 98%',
                'description' => 'Khodam elemen air yang tenang & adem! Mampu mendinginkan pikiran ustadz pas ngadapi santri yang susah dibangunin subuh.'
            ],
            [
                'title'       => 'Adudu',
                'icon'        => '👽',
                'badge'       => 'Tingkat Kejahilan: 91%',
                'description' => 'Khodam kepala kotak hijau pencari Kakao! Selalu punya rencana jahil di pondok tapi selalu gagal kena tepukan sajadah musyrif.'
            ],
            [
                'title'       => 'Tai Lung',
                'icon'        => '🐆',
                'badge'       => 'Tingkat Kekecewaan: 96%',
                'description' => 'Khodam macan tutul ahli kungfu! Kecewa berat karena Gulungan Rahasia ternyata isinya lembar tagihan syahriah yang belum lunas.'
            ],
            [
                'title'       => 'Master Shifu',
                'icon'        => '🐼',
                'badge'       => 'Kebijaksanaan: 100%',
                'description' => 'Khodam guru kungfu bertelinga lebar! Menjelaskan kedamaian batin (Inner Peace) sambil menikmati seruputan kopi hangat di gazebo pondok.'
            ],
            [
                'title'       => 'Plankton',
                'icon'        => '🦠',
                'badge'       => 'Tingkat Ambisi: 99%',
                'description' => 'Khodam mikroba hijau berambisi tinggi! Selalu berusaha mencuri resep rahasia bumbu katering majek tapi selalu tertangkap nampan.'
            ]
        ];

        $titlesList = [
            'Sang Maestro Penyeduh Kopi Subuh & Penjaga Lembar Setoran',
            'Sang Pendekar Harakat Gundul & Penakluk Antrean Wudhu',
            'Sang Sultan Katering Majek Porsi Double & Kolektor Peci Miring',
            'Sang Maharaja Hafalan Alfiyah & Penengah Musyawarah',
            'Sang Pengawal Garis Depan Sholat Berjamaah & Benteng Asrama',
            'Sang Ahli Logistik Pondok & Panglima Sarung Lipat Rapi',
            'Sang Penjinak Server ERP & Pemanggil Keberkahan Keuangan'
        ];

        $slotSatireQuotes = [
            [
                'title' => 'Cara Mengubah Dosa Menjadi Saldo DANA',
                'quote' => 'TAUBAT NASUHA & SEDEKAH, bukan depo di situs judi online slot rungkad! 😂 Rezeki berkah dari Allah itu nyata, judol itu fiktif!'
            ],
            [
                'title' => 'Bocoran Jam & Pola Gacor Hari Ini',
                'quote' => 'Jam Gacor: Subuh (04:15 WIB). Pola Gacor: 2 Rakaat Subuh + Zikir -> Auto Berkah Tanpa Depo! 🤲'
            ],
            [
                'title' => 'Statistik Situs Judi Online Asli',
                'quote' => 'Rungkad 99.9%, Hutang Pinjol 100%, Penyesalan 1000%. Mending uangnya buat bayar syahriah & jajan santri! 💸'
            ],
            [
                'title' => 'Reaksi Admin Slot Olympus',
                'quote' => 'Admin Olympus Menangis melihat Anda main slot di Dev Corner karena Modalnya Rp 0,- dan dosa Anda tidak bertambah! ⚡'
            ],
            [
                'title' => 'RTP (Return to Pahala) Hari Ini',
                'quote' => 'RTP Hari Ini: 1000% Barokah. Tidak perlu WD (Withdraw), Pahala langsung dicatat Malaikat Rakib! 📖'
            ]
        ];

        return view('dashboard', compact(
            'user',
            'roles',
            'permissions',
            'organizationIds',
            'stats',
            'calendarData',
            'importantEvents',
            'dawahList',
            'khodamList',
            'titlesList',
            'slotSatireQuotes'
        ));
    }
}
