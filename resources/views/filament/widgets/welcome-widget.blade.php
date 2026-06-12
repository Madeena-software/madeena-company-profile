<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Selamat Datang, {{ auth()->user()->name }}! 👋
                </h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">
                    Website: {{ config('app.url', 'madeena.co.id') }}
                </p>
            </div>
            <div>
                <x-filament::button
                    tag="a"
                    href="{{ url('/') }}"
                    target="_blank"
                    icon="heroicon-o-globe-alt"
                >
                    Lihat Website
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
