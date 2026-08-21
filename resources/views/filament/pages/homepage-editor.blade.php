<x-filament-panels::page>
    @php
        $activeLanguages = \App\Models\Language::getActive();
        $currentLang = \App\Models\Language::resolve($activeLocale) ?? \App\Models\Language::getDefault();
    @endphp
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Bahasa Konten:</span>
            <div class="inline-flex flex-wrap gap-1 rounded-lg p-1 bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700" data-testid="editor-language-selector">
                @foreach($activeLanguages as $lang)
                    <button type="button"
                        wire:click="switchLanguage('{{ $lang->code }}')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all inline-flex items-center gap-1.5 {{ $activeLocale === $lang->code ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        data-testid="editor-lang-btn-{{ $lang->code }}">
                        <span>{{ $lang->native_name }} ({{ strtoupper($lang->code) }})</span>
                        @if($lang->is_default)
                            <span class="text-[10px] bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300 px-1 py-0.2 rounded font-normal">Default</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400">
            Sedang mengedit: <span class="font-bold text-primary-600 dark:text-primary-400">{{ $currentLang->native_name }} ({{ strtoupper($currentLang->code) }})</span>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
