<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aktivitas Terakhir
        </x-slot>

        <div class="space-y-4">
            @forelse ($this->getActivities() as $activity)
                <div class="flex items-start space-x-3">
                    <div class="mt-0.5">
                        <x-dynamic-component :component="$activity['icon']" class="w-5 h-5 {{ $activity['color'] }}" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $activity['message'] }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $activity['time']->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-sm text-gray-500 dark:text-gray-400 italic">
                    Belum ada aktivitas.
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
