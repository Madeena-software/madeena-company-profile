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
    public string $activeLocale = 'id';

    public function mount(): void
    {
        $requestedLocale = request()->query('lang', 'id');
        $this->loadLanguageState($requestedLocale);
    }

    public function updatedActiveLocale($value): void
    {
        $this->switchLanguage($value);
    }

    public function switchLanguage(string $locale): void
    {
        $this->loadLanguageState($locale);
    }

    public function setLanguage(string $locale): void
    {
        $this->switchLanguage($locale);
    }

    public function loadLanguageState(string $locale): void
    {
        $this->activeLocale = Setting::normalizeLocale($locale);
        $sections = Setting::getHomepageSections($this->activeLocale, true);

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
                ->url(fn () => Setting::normalizeLocale($this->activeLocale ?? ($this->data['locale'] ?? 'id')) === 'en' ? url('/en?preview=true') : url('/?preview=true'))
                ->openUrlInNewTab()
                ->color('info'),

            Action::make('save')
                ->label('💾 Simpan Draft')
                ->icon('heroicon-o-check')
                ->action('save')
                ->color('warning'),

            Action::make('publish_to_prod')
                ->label('🚀 Update Prod')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Update Website Live')
                ->modalDescription(new \Illuminate\Support\HtmlString('Apakah Anda yakin ingin menerapkan perubahan draft ini ke website utama? Pengunjung akan langsung melihat perubahan ini.'))
                ->action(function () {
                    $this->publish();
                }),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $locale = Setting::normalizeLocale($this->activeLocale ?? ($state['locale'] ?? 'id'));
        $this->activeLocale = $locale;
        $draftKey = Setting::homepageDraftKey($locale);

        $sections = array_values($state['sections'] ?? []);
        Setting::setJson($draftKey, $sections);

        $langLabel = $locale === 'en' ? 'English' : 'Indonesia';
        Notification::make()
            ->success()
            ->title("Draft ({$langLabel}) Berhasil Disimpan")
            ->body("Perubahan bahasa {$langLabel} telah disimpan sebagai draft. Gunakan Pratinjau untuk melihat perubahan.")
            ->send();
    }

    public function publish(): void
    {
        $state = $this->form->getState();
        $locale = Setting::normalizeLocale($this->activeLocale ?? ($state['locale'] ?? 'id'));
        $this->activeLocale = $locale;
        $draftKey = Setting::homepageDraftKey($locale);
        $publishedKey = Setting::homepagePublishedKey($locale);

        $draft = Setting::getJson($draftKey, null);
        
        if (is_null($draft)) {
            $draft = Setting::getJson($publishedKey, []);
        }

        $sections = array_values($draft ?? []);
        Setting::setJson($publishedKey, $sections);

        $langLabel = $locale === 'en' ? 'English' : 'Indonesia';
        Notification::make()
            ->success()
            ->title("Berhasil Diupdate ({$langLabel})")
            ->body("Halaman utama ({$langLabel}) telah berhasil diterapkan ke website live.")
            ->send();
    }

    public static function getNavigation(bool $useDraft = false, string $language = 'id'): array
    {
        $locale = Setting::normalizeLocale($language);
        $sections = Setting::getHomepageSections($locale, $useDraft);
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
