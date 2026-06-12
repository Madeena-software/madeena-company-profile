<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        {{-- We use the header actions to submit, so no button is strictly needed here unless wanted --}}
    </form>
</x-filament-panels::page>
