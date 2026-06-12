<?php

namespace App\Filament\Pages;

use App\Filament\BuilderBlocks;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class HomepageEditor extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Halaman Utama';
    protected static ?string $title = 'Edit Halaman Utama';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.homepage-editor';

    public ?array $data = [];

    public function mount(): void
    {
        // For standard array/json settings, retrieve with default fallback
        $sections = Setting::getJson('homepage_sections', []);

        $this->form->fill([
            'sections' => $sections,
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Builder::make('sections')
                    ->label('Bagian Halaman')
                    ->blocks(BuilderBlocks::get())
                    ->reorderable()
                    ->collapsible()
                    ->collapsed()
                    ->addActionLabel('+ Tambah Bagian Baru')
                    ->blockNumbers(false)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('👁️ Pratinjau')
                ->icon('heroicon-o-eye')
                ->url(url('/?preview=true')) // temporary preview URL logic
                ->openUrlInNewTab()
                ->color('info'),

            Action::make('save')
                ->label('💾 Simpan')
                ->icon('heroicon-o-check')
                ->action('save')
                ->color('success'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        Setting::setJson('homepage_sections', $state['sections'] ?? []);

        Notification::make()
            ->success()
            ->title('Berhasil disimpan')
            ->body('Halaman utama telah diperbarui.')
            ->send();
    }
}
