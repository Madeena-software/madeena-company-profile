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

    protected static function isAdminUser(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user instanceof \App\Models\User && $user->isAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAdminUser();
    }

    public static function canAccess(): bool
    {
        return static::isAdminUser();
    }

    public function getMaxContentWidth(): \Filament\Support\Enums\Width | string | null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    protected string $view = 'filament.pages.homepage-editor';

    public ?array $data = [];

    public function mount(): void
    {
        // For standard array/json settings, retrieve with default fallback
        $sections = Setting::getJson('homepage_sections_draft', Setting::getJson('homepage_sections', []));

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
                    ->blockPickerColumns(3)
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
                ->label('💾 Simpan Draft')
                ->icon('heroicon-o-check')
                ->action('save')
                ->color('warning'),

            Action::make('publish')
                ->label('🚀 Update Prod')
                ->icon('heroicon-o-rocket-launch')
                ->action('publish')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Update Website Live')
                ->modalDescription(new \Illuminate\Support\HtmlString('Apakah Anda yakin ingin menerapkan perubahan draft ini ke website utama? Pengunjung akan langsung melihat perubahan ini.')),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        Setting::setJson('homepage_sections_draft', $state['sections'] ?? []);

        Notification::make()
            ->success()
            ->title('Draft Berhasil Disimpan')
            ->body('Perubahan Anda telah disimpan sebagai draft. Gunakan Pratinjau untuk melihat perubahan.')
            ->send();
    }

    public function publish(): void
    {
        $draft = Setting::getJson('homepage_sections_draft', null);
        
        if (is_null($draft)) {
            $draft = Setting::getJson('homepage_sections', []);
        }

        Setting::setJson('homepage_sections', $draft);

        Notification::make()
            ->success()
            ->title('Berhasil Diupdate')
            ->body('Halaman utama telah berhasil diterapkan ke website live.')
            ->send();
    }

    public static function getNavigation(bool $useDraft = false): array
    {
        $sections = $useDraft ? Setting::getJson('homepage_sections_draft', Setting::getJson('homepage_sections', [])) : Setting::getJson('homepage_sections', []);
        $navItems = [];

        foreach ($sections as $section) {
            if (! empty($section['data']['show_in_nav'])) {
                $sectionId = $section['data']['section_id'] ?? '';
                $navItems[] = [
                    'label'       => $section['data']['nav_label'] ?? ucfirst($section['type']),
                    'anchor'      => $sectionId ? '#' . $sectionId : null,
                    'is_external' => false,
                ];
            }
        }

        // Append custom links from settings
        $customLinks = Setting::getJson('nav_custom_links', []);
        foreach ($customLinks as $link) {
            $navItems[] = [
                'label'       => $link['label'] ?? '',
                'url'         => $link['url'] ?? '#',
                'is_external' => true,
            ];
        }

        return $navItems;
    }
}
