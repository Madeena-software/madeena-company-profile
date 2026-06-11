<x-filament-panels::page.simple>
    @if (session('sso_silent_failed'))
        <div class="mb-6 p-4 text-sm text-yellow-800 rounded-xl bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-900" role="alert">
            <span class="font-semibold block mb-1">Otomatis login gagal.</span> Silakan klik tombol di bawah ini untuk masuk.
        </div>
    @elseif (session('sso_manual_login'))
        <div class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-gray-800 dark:text-green-300 border border-green-200 dark:border-green-900" role="alert">
            <span class="font-semibold block mb-1">Berhasil keluar.</span> Anda telah keluar dari sesi lokal.
        </div>
    @endif

    <div class="flex flex-col items-center justify-center pt-2 pb-4 w-full">
        <x-filament::button
            tag="a"
            href="{{ route('sso.redirect') }}"
            class="w-full"
            size="lg"
            color="primary"
            icon="heroicon-o-arrow-right-on-rectangle"
        >
            Masuk dengan Madeena SSO
        </x-filament::button>
    </div>
</x-filament-panels::page.simple>
