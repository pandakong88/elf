<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Elvith.id</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased min-h-screen flex items-center justify-center p-3 sm:p-4 md:p-6">

    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 bg-slate-950/80 border border-slate-800/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 backdrop-blur-xl shadow-2xl my-auto">
        
        <!-- Brand & Info Panel (Left) -->
        <div class="md:col-span-5 flex flex-col justify-between p-6 sm:p-8 rounded-2xl bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-950 text-white shadow-xl border border-emerald-500/20 overflow-hidden relative group">
            <!-- Decorative blur shapes -->
            <div class="absolute -top-12 -right-12 w-36 sm:w-40 h-36 sm:h-40 bg-emerald-500/15 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute -bottom-16 -left-16 w-48 sm:w-52 h-48 sm:h-52 bg-teal-500/15 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <img src="/images/logo-alfithroh.png" alt="Logo Al-Fithroh" class="h-10 w-auto object-contain drop-shadow">
                        <div class="flex flex-col">
                            <span class="font-extrabold text-sm tracking-tight text-white uppercase leading-none font-serif-display">Al-Fithroh</span>
                            <span class="text-[9px] font-bold text-emerald-400 tracking-wider uppercase mt-0.5">Jejeran Bantul</span>
                        </div>
                    </div>
                    <a href="{{ url('/') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 active:bg-white/30 backdrop-blur-md text-white text-xs font-bold rounded-xl transition-all border border-white/20 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Landing Page</span>
                    </a>
                </div>

                {{-- Banner Kaligrafi Arab --}}
                <div class="py-3 flex flex-col items-center justify-center">
                    <img src="/images/calligraphy-alfithroh.png" alt="Kaligrafi Al-Fithroh" class="max-h-36 sm:max-h-40 w-auto object-contain drop-shadow-[0_10px_25px_rgba(16,185,129,0.3)] hover:scale-105 transition-transform duration-500">
                </div>

                <div class="space-y-1 text-center sm:text-left">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight text-white font-serif-display">Elvith.id</h2>
                    <p class="text-slate-300 text-xs sm:text-sm font-light leading-relaxed">
                        Sistem informasi terintegrasi untuk asrama, kepengurusan, perizinan, dan tata tertib santri Pondok Pesantren Al-Fithroh Jejeran Bantul.
                    </p>
                </div>
            </div>

            <div class="relative z-10 mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] text-slate-400 font-medium">Elvith.id v1.0 — System Ready</span>
                </div>
            </div>
        </div>

        <!-- Login Form Panel (Right) -->
        <div class="md:col-span-7 flex flex-col justify-center p-1 sm:p-2 md:p-4">
            <div class="flex items-center justify-between mb-4 md:mb-6 pb-3 md:pb-0 border-b md:border-b-0 border-slate-800/60">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-white">Selamat Datang Kembali</h3>
                    <p class="text-slate-400 text-xs sm:text-sm mt-0.5 sm:mt-1">Silakan masuk menggunakan kredensial terdaftar.</p>
                </div>
                <a href="{{ url('/portal-wali') }}" class="md:hidden inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold rounded-xl transition-all shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Portal Wali</span>
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="loginForm" action="{{ url('/login') }}" method="POST" class="space-y-3.5 sm:space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" required placeholder="name@elvith.id" 
                           class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-sm">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="••••••••" 
                               class="w-full px-3.5 py-2.5 sm:px-4 sm:py-3 pr-11 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-sm">
                        <button type="button" onclick="togglePasswordVisibility()" 
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 p-1 focus:outline-none transition-colors" 
                                title="Tampilkan / Sembunyikan Kata Sandi">
                            <svg id="eyeIconShow" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeIconHide" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 012.122-.363c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 flex items-center justify-center gap-2 mt-5 sm:mt-6 text-sm">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            @if($devModeEnabled === '1')
                <!-- Developer Quick Login Panel -->
                <div class="mt-5 sm:mt-6 pt-4 sm:pt-5 border-t border-slate-800/80">
                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-md uppercase">DEV MODE</span>
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Quick Switcher</h4>
                        </div>
                        <span class="text-[10px] text-slate-500 font-medium">Password dev: <code class="text-emerald-400 font-mono">{{ $devPassword }}</code></span>
                    </div>

                <!-- Brief Explanation Box -->
                <div class="mb-3 p-2.5 sm:p-3 bg-slate-900/60 border border-slate-800/80 rounded-2xl flex items-start gap-2">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-light">
                        <strong class="text-slate-300 font-semibold">Pengujian Hak Akses:</strong> Klik role card di bawah untuk memilih akun pengurus & menguji otorisasi modul.
                    </p>
                </div>

                {{-- 4 Role Group Cards (Mobile Responsive Grid) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5 mb-3">
                    @foreach($roleGroups as $key => $rg)
                        <button type="button" onclick="openRoleUsersModal('{{ $key }}')"
                                class="p-2.5 sm:p-3 bg-gradient-to-b {{ $rg['bg_color'] }} border {{ $rg['border_color'] }} rounded-2xl text-left transition-all shadow-md hover:shadow-lg group relative overflow-hidden flex flex-col justify-between min-h-[5.5rem] sm:h-24">
                            <div class="flex items-center justify-between w-full">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-900/80 border border-slate-700/50 flex items-center justify-center shadow-sm shrink-0">
                                    @if(($rg['icon_type'] ?? '') === 'shield')
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @elseif(($rg['icon_type'] ?? '') === 'building')
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    @elseif(($rg['icon_type'] ?? '') === 'banknotes')
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                                <span class="px-1.5 sm:px-2 py-0.5 text-[8px] sm:text-[9px] font-extrabold rounded-full border {{ $rg['badge_color'] }}">
                                    {{ count($rg['users']) }} Akun
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="block text-[11px] sm:text-xs font-extrabold text-slate-100 group-hover:text-emerald-400 transition-colors leading-tight truncate">{{ $rg['title'] }}</span>
                                <span class="block text-[9px] sm:text-[10px] text-slate-400 truncate mt-0.5 font-light">{{ $rg['subtitle'] }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Dropdown Select Pengurus --}}
                @if(isset($devUsers) && count($devUsers) > 0)
                    <div class="mt-2.5 p-2.5 sm:p-3 bg-slate-900/90 border border-slate-800 rounded-2xl">
                        <label for="devUserSelect" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="truncate">Daftar Pengurus / Staff:</span>
                        </label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <select id="devUserSelect" 
                                    class="w-full sm:flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 truncate">
                                <option value="">-- Pilih Nama Pengurus / Staff --</option>
                                @foreach($devUsers as $u)
                                    <option value="{{ $u['email'] }}">
                                        {{ $u['name'] }} — {{ $u['roles'] }} ({{ $u['gender'] }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="loginSelectedUser()"
                                    class="w-full sm:w-auto px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shrink-0 flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span>Login</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Selection for Role Accounts -->
    <div id="roleUsersModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden items-center justify-center p-3 sm:p-4">
        <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-800">
                <div>
                    <h3 id="modalRoleTitle" class="text-base font-extrabold text-white flex items-center gap-2">
                        <!-- Icon & Title dynamically inserted -->
                    </h3>
                    <p id="modalRoleSubtitle" class="text-xs text-slate-400 mt-0.5 font-light">
                        <!-- Subtitle dynamically inserted -->
                    </p>
                </div>
                <button type="button" onclick="closeRoleUsersModal()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                    ✕
                </button>
            </div>

            <!-- Modal Users List -->
            <div id="modalRoleUsersList" class="mt-4 space-y-2.5 max-h-72 sm:max-h-80 overflow-y-auto pr-1">
                <!-- User list dynamically inserted -->
            </div>

            <!-- Modal Footer -->
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-end text-[11px] text-slate-500">
                <button type="button" onclick="closeRoleUsersModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        const roleGroupsData = @json($roleGroups ?? []);
        const devPassword = @json($devPassword ?? '');

        function openRoleUsersModal(groupKey) {
            const group = roleGroupsData[groupKey];
            if (!group) return;

            document.getElementById('modalRoleTitle').innerText = group.title;
            document.getElementById('modalRoleSubtitle').innerText = 'Pilih salah satu akun ' + group.title + ' untuk masuk ke sistem:';

            const listContainer = document.getElementById('modalRoleUsersList');
            listContainer.innerHTML = '';

            if (!group.users || group.users.length === 0) {
                listContainer.innerHTML = `<div class="p-6 text-center text-xs text-slate-500 font-medium">Tidak ada akun terdaftar untuk role ini.</div>`;
            } else {
                group.users.forEach(u => {
                    const isPutra = u.gender === 'Putra';
                    const userCard = document.createElement('div');
                    userCard.className = 'p-3 bg-slate-950/80 border border-slate-800/80 hover:border-emerald-500/50 rounded-2xl flex items-center justify-between transition-all group/item';
                    userCard.innerHTML = `
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            <div class="w-9 h-9 rounded-xl ${isPutra ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-pink-500/10 text-pink-400 border-pink-500/20'} border flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-100 group-hover/item:text-emerald-400 transition-colors flex items-center gap-1.5 truncate">
                                    <span class="truncate">${u.name}</span>
                                    <span class="px-1.5 py-0.5 text-[8px] rounded font-mono font-semibold shrink-0 ${isPutra ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-pink-500/10 text-pink-400 border border-pink-500/20'}">${u.gender}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">${u.email}</div>
                            </div>
                        </div>
                        <button type="button" onclick="quickLogin('${u.email}', '${devPassword}')"
                                class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-emerald-500/20 flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Masuk</span>
                        </button>
                    `;
                    listContainer.appendChild(userCard);
                });
            }

            document.getElementById('roleUsersModal').classList.remove('hidden');
            document.getElementById('roleUsersModal').classList.add('flex');
        }

        function closeRoleUsersModal() {
            document.getElementById('roleUsersModal').classList.add('hidden');
            document.getElementById('roleUsersModal').classList.remove('flex');
        }

        function quickLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password || devPassword;
            document.getElementById('loginForm').submit();
        }

        function loginSelectedUser() {
            const select = document.getElementById('devUserSelect');
            const email = select ? select.value : '';
            if (!email) {
                alert('Silakan pilih salah satu musyrif/pengurus dari daftar terlebih dahulu.');
                return;
            }
            quickLogin(email, devPassword);
        }
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeShow = document.getElementById('eyeIconShow');
            const eyeHide = document.getElementById('eyeIconHide');

            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeShow.classList.add('hidden');
                eyeHide.classList.remove('hidden');
            } else {
                pwdInput.type = 'password';
                eyeShow.classList.remove('hidden');
                eyeHide.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
