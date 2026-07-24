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
                    <input type="email" name="email" id="email" required placeholder="name@alfithroh.pondok" 
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
                        <span class="px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-md uppercase">⚡ Dev Mode</span>
                        <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Quick Switcher Login</h4>
                    </div>
                    <span class="text-[10px] text-slate-500 font-medium">Auto-fill password: <code class="text-emerald-400 font-mono">rahasia123</code></span>
                </div>

                {{-- Preset Quick Buttons --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 mb-3">
                    <button type="button" onclick="quickLogin('admin@alfithroh.pondok', 'rahasia123')"
                            class="p-2.5 bg-slate-900/80 hover:bg-slate-800/90 active:bg-slate-900 border border-slate-800 hover:border-emerald-500/50 rounded-xl text-left transition-all group">
                        <span class="block text-xs font-bold text-slate-200 group-hover:text-emerald-400">🛡️ Super Admin</span>
                        <span class="block text-[10px] text-slate-500 truncate">admin@alfithroh.pondok</span>
                    </button>

                    <button type="button" onclick="quickLogin('musyrif@alfithroh.pondok', 'rahasia123')"
                            class="p-2.5 bg-slate-900/80 hover:bg-slate-800/90 active:bg-slate-900 border border-slate-800 hover:border-emerald-500/50 rounded-xl text-left transition-all group">
                        <span class="block text-xs font-bold text-slate-200 group-hover:text-emerald-400">👳‍♂️ Musyrif Pa</span>
                        <span class="block text-[10px] text-slate-500 truncate">musyrif@alfithroh.pondok</span>
                    </button>

                    <button type="button" onclick="quickLogin('musyrifah@alfithroh.pondok', 'rahasia123')"
                            class="p-2.5 bg-slate-900/80 hover:bg-slate-800/90 active:bg-slate-900 border border-slate-800 hover:border-emerald-500/50 rounded-xl text-left transition-all group">
                        <span class="block text-xs font-bold text-slate-200 group-hover:text-emerald-400">🧕 Musyrifah Pi</span>
                        <span class="block text-[10px] text-slate-500 truncate">musyrifah@alfithroh.pondok</span>
                    </button>

                    <button type="button" onclick="quickLogin('pengasuh@alfithroh.pondok', 'rahasia123')"
                            class="p-2.5 bg-slate-900/80 hover:bg-slate-800/90 active:bg-slate-900 border border-slate-800 hover:border-emerald-500/50 rounded-xl text-left transition-all group">
                        <span class="block text-xs font-bold text-slate-200 group-hover:text-emerald-400">👑 Pengasuh</span>
                        <span class="block text-[10px] text-slate-500 truncate">pengasuh@alfithroh.pondok</span>
                    </button>
                </div>

                {{-- Pilih Spesifik Musyrif / Staff Dropdown --}}
                @if(isset($devUsers) && count($devUsers) > 0)
                    <div class="mt-3 p-3 bg-slate-900/90 border border-slate-800 rounded-2xl">
                        <label for="devUserSelect" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">
                            👤 Pilih Nama Musyrif / Pengurus Spesifik:
                        </label>
                        <div class="flex items-center gap-2">
                            <select id="devUserSelect" 
                                    class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500">
                                <option value="">-- Pilih Nama Musyrif / Staff --</option>
                                @foreach($devUsers as $u)
                                    <option value="{{ $u['email'] }}">
                                        {{ $u['name'] }} — Role: {{ $u['roles'] }} ({{ $u['gender'] }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="loginSelectedUser()"
                                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shrink-0 flex items-center gap-1.5">
                                <span>⚡ Login</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
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
