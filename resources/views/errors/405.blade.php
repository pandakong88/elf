<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Metode Tidak Diizinkan (405) - Elvith</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-slate-200 shadow-xl text-center space-y-6">
        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Permintaan Tidak Valid</h1>
            <p class="text-sm text-slate-500 leading-relaxed">
                Tindakan ini tidak dapat diproses secara langsung melalui alamat tautan (405). Silakan kembali ke halaman sebelumnya.
            </p>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row gap-3">
            <button onclick="window.history.back()" class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition">
                Kembali
            </button>
            <a href="/" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl text-xs transition shadow-md shadow-emerald-600/20">
                Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
