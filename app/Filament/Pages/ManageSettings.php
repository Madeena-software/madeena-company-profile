<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ManageSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.manage-settings';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan Website';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    protected function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Kontak')->schema([
                TextInput::make('company_name')->label('Nama Perusahaan'),
                TextInput::make('tagline')->label('Tagline'),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('phone')->label('Telepon'),
                TextInput::make('whatsapp')->label('WhatsApp'),
                Textarea::make('address')->label('Alamat')->rows(3),
            ])->columns(2),
            Section::make('Media Sosial')->schema([
                TextInput::make('instagram')->label('Instagram URL'),
                TextInput::make('linkedin')->label('LinkedIn URL'),
                TextInput::make('youtube')->label('YouTube URL'),
            ])->columns(3),
            Section::make('SEO')->schema([
                TextInput::make('meta_title')->label('Meta Title'),
                Textarea::make('meta_description')->label('Meta Description')->rows(3),
            ])->columns(1),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Notification::make()->title('Pengaturan berhasil disimpan.')->success()->send();
    }
}
