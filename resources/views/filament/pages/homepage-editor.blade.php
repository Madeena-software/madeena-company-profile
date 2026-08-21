<x-filament-panels::page>
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Bahasa Konten:</span>
            <div class="inline-flex rounded-lg p-1 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                <button type="button"
                    wire:click="switchLanguage('id')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $activeLocale === 'id' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    🇮🇩 Indonesia
                </button>
                <button type="button"
                    wire:click="switchLanguage('en')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $activeLocale === 'en' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                    🇬🇧 English
                </button>
            </div>
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400">
            Sedang mengedit: <span class="font-bold text-primary-600 dark:text-primary-400">{{ $activeLocale === 'en' ? 'English (EN)' : 'Indonesia (ID)' }}</span>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
