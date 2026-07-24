<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ELF System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl grid md:grid-cols-12 gap-8 bg-slate-950/40 border border-slate-800 rounded-3xl p-6 md:p-8 backdrop-blur-xl shadow-2xl">
        <!-- Brand & Info Panel (Left) -->
        <div class="md:col-span-5 flex flex-col justify-between p-6 md:p-8 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg overflow-hidden relative group">
            <!-- Decorative blur shapes -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute -bottom-16 -left-16 w-52 h-52 bg-emerald-600/30 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center font-bold text-white shadow-inner text-xl mb-6">E</div>
                <h2 class="text-3xl font-extrabold tracking-tight leading-tight">Educational & Lodge Framework</h2>
                <p class="text-emerald-100/90 text-sm mt-3 font-light leading-relaxed">
                    Sistem informasi terintegrasi untuk asrama, kepengurusan, perizinan, dan tata tertib santri di Pondok Pesantren Al-Fithroh.
                </p>
            </div>

            <div class="relative z-10 mt-12 pt-6 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                    <span class="text-xs text-emerald-100 font-medium">Versi 1.0.0 (Foundation UI)</span>
                </div>
            </div>
        </div>

        <!-- Login Form Panel (Right) -->
        <div class="md:col-span-7 flex flex-col justify-center p-2 md:p-4">
            <div class="mb-6">
                <h3 class="text-2xl font-bold tracking-tight text-white">Selamat Datang Kembali</h3>
                <p class="text-slate-400 text-sm mt-1">Silakan masuk menggunakan kredensial terdaftar.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="loginForm" action="{{ url('/login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                    <input type="email" name="email" id="email" required placeholder="name@elvith.id" 
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-sm">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="••••••••" 
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-sm">
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 flex items-center justify-center gap-2 mt-6">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <!-- Developer Quick Login Panel -->
            <div class="mt-6 pt-5 border-t border-slate-800/80">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-md uppercase">DEV MODE</span>
                        <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Quick Switcher Login</h4>
                    </div>
                    <span class="text-[10px] text-slate-500 font-medium">Password dev: <code class="text-emerald-400 font-mono">rahasia123</code></span>
                </div>

                <!-- Brief Explanation Box -->
                <div class="mb-3.5 p-3 bg-slate-900/60 border border-slate-800/80 rounded-2xl flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[11px] text-slate-400 leading-relaxed font-light">
                        <strong class="text-slate-300 font-semibold">Pengujian Hak Akses:</strong> Klik salah satu grup role di bawah untuk memilih akun pengurus dan mensimulasikan otorisasi modul (seperti tagihan, setoran, atau katering) tanpa mengetik password secara manual.
                    </p>
                </div>

                {{-- 4 Role Group Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 mb-3">
                    @foreach($roleGroups as $key => $rg)
                        <button type="button" onclick="openRoleUsersModal('{{ $key }}')"
                                class="p-3 bg-gradient-to-b {{ $rg['bg_color'] }} border {{ $rg['border_color'] }} rounded-2xl text-left transition-all shadow-md hover:shadow-lg group relative overflow-hidden flex flex-col justify-between h-24">
                            <div class="flex items-center justify-between w-full">
                                <div class="w-8 h-8 rounded-xl bg-slate-900/80 border border-slate-700/50 flex items-center justify-center shadow-sm">
                                    @if(($rg['icon_type'] ?? '') === 'shield')
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @elseif(($rg['icon_type'] ?? '') === 'building')
                                        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    @elseif(($rg['icon_type'] ?? '') === 'banknotes')
                                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full border {{ $rg['badge_color'] }}">
                                    {{ count($rg['users']) }} Akun
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-100 group-hover:text-emerald-400 transition-colors">{{ $rg['title'] }}</span>
                                <span class="block text-[10px] text-slate-400 truncate mt-0.5 font-light">{{ $rg['subtitle'] }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Opsi Dropdown Pengurus / Staff Lainnya --}}
                @if(isset($devUsers) && count($devUsers) > 0)
                    <div class="mt-3 p-3 bg-slate-900/90 border border-slate-800 rounded-2xl">
                        <label for="devUserSelect" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Pilih Nama Musyrif / Guru / Staff Lainnya:</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <select id="devUserSelect" 
                                    class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                                <option value="">-- Pilih Nama Pengurus / Staff --</option>
                                @foreach($devUsers as $u)
                                    <option value="{{ $u['email'] }}">
                                        {{ $u['name'] }} — Role: {{ $u['roles'] }} ({{ $u['gender'] }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="loginSelectedUser()"
                                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shrink-0 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span>Login</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Selection for Role Accounts -->
    <div id="roleUsersModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden items-center justify-center p-4">
        <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl relative overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
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
            <div id="modalRoleUsersList" class="mt-4 space-y-2.5 max-h-80 overflow-y-auto pr-1">
                <!-- User list dynamically inserted -->
            </div>

            <!-- Modal Footer -->
            <div class="mt-5 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                <span>Password diisi otomatis: <code class="text-emerald-400 font-mono">rahasia123</code></span>
                <button type="button" onclick="closeRoleUsersModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <script>
        const roleGroupsData = @json($roleGroups ?? []);

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
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl ${isPutra ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-pink-500/10 text-pink-400 border-pink-500/20'} border flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-100 group-hover/item:text-emerald-400 transition-colors flex items-center gap-2 truncate">
                                    <span class="truncate">${u.name}</span>
                                    <span class="px-1.5 py-0.5 text-[9px] rounded font-mono font-semibold shrink-0 ${isPutra ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-pink-500/10 text-pink-400 border border-pink-500/20'}">${u.gender}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate">${u.email}</div>
                            </div>
                        </div>
                        <button type="button" onclick="quickLogin('${u.email}', 'rahasia123')"
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
            document.getElementById('password').value = password;
            document.getElementById('loginForm').submit();
        }

        function loginSelectedUser() {
            const select = document.getElementById('devUserSelect');
            const email = select ? select.value : '';
            if (!email) {
                alert('Silakan pilih salah satu musyrif/pengurus dari daftar terlebih dahulu.');
                return;
            }
            quickLogin(email, 'rahasia123');
        }
    </script>
</body>
</html>
