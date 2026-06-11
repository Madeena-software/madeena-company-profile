<div class="flex flex-col items-center justify-center p-8 text-center space-y-4">
    <!-- Spinner / Glow -->
    <div class="relative w-16 h-16">
        <div class="absolute inset-0 rounded-full border-4 border-teal-500/20"></div>
        <div class="absolute inset-0 rounded-full border-4 border-t-teal-500 animate-spin"></div>
    </div>
    
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
        Menghubungkan ke SSO...
    </h2>
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Anda sedang dialihkan ke sistem autentikasi pusat.
    </p>
    
    <a href="{{ route('sso.redirect') }}" class="mt-4 text-sm text-teal-600 dark:text-teal-400 font-medium hover:underline">
        Klik di sini jika Anda tidak dialihkan otomatis.
    </a>
</div>
