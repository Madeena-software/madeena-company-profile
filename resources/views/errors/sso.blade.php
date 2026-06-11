<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Gagal Autentikasi' }} - Madeena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-100 flex items-center justify-center min-h-screen p-4 relative overflow-hidden">
    <!-- background glow -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-500/10 rounded-full blur-[100px]"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px]"></div>

    <div class="w-full max-w-md bg-slate-900/60 backdrop-blur-xl border border-slate-800 p-8 rounded-2xl shadow-2xl relative z-10 text-center">
        <!-- Icon -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-rose-500/10 text-rose-500 border border-rose-500/20 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 7.5h.008v.008H12v-.008Z" />
            </svg>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-white mb-3 tracking-tight">
            {{ $title }}
        </h1>

        <!-- Message -->
        <p class="text-slate-400 text-sm leading-relaxed mb-8">
            {{ $message }}
        </p>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="/admin/login" class="block w-full py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-semibold text-sm transition-all duration-200 shadow-lg shadow-teal-500/20">
                Kembali ke Login
            </a>
            <a href="/" class="block w-full py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-sm transition-all duration-200 border border-slate-700/50">
                Kembali ke Beranda
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-xs text-slate-500 border-t border-slate-800/80 pt-6">
            &copy; {{ date('Y') }} PT Madeena Karya Indonesia.
        </div>
    </div>
</body>
</html>
