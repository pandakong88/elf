<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koneksi Terputus - Elvith</title>
    <link rel="icon" type="image/png" href="/images/logo-alfithroh.png">
    <meta name="theme-color" content="#064e3b">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f8fafc;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-100 p-8 text-center">
        <div class="w-24 h-24 mx-auto mb-6 bg-emerald-50 rounded-2xl p-4 flex items-center justify-center relative">
            <img src="/images/logo-alfithroh.png" alt="Elvith Logo" class="w-16 h-16 object-contain opacity-80">
            <span class="absolute -bottom-2 -right-2 bg-amber-500 text-white rounded-full p-1.5 shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-12.728 0m0 0l2.829-2.829m-2.829 2.829L3 21m2.829-5.657a5 5 0 010-7.072m0 0l2.829 2.829m0 0l2.829 2.829"></path>
                </svg>
            </span>
        </div>

        <h1 class="text-2xl font-black text-slate-800 tracking-tight mb-2">Koneksi Terputus</h1>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Aplikasi <strong>Elvith</strong> membutuhkan koneksi internet untuk mengakses data santri dan transaksi secara aman. Silakan periksa jaringan WiFi atau kuota data Anda.
        </p>

        <div class="space-y-3">
            <button onclick="window.location.reload()" class="w-full py-3 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-xl shadow-lg shadow-emerald-900/20 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin-hover" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Coba Muat Ulang
            </button>
            <a href="/" class="block w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all text-sm">
                Kembali ke Beranda
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-center gap-2 text-xs text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            Elvith by pandakong &bull; Al-Fithroh
        </div>
    </div>
</body>
</html>
