<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.manage-settings';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Pengaturan Website';
    protected static ?string $navigationGroup = 'Konten Website';
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

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Kontak')->schema([
                Forms\Components\TextInput::make('company_name')->label('Nama Perusahaan'),
                Forms\Components\TextInput::make('tagline')->label('Tagline'),
                Forms\Components\TextInput::make('email')->label('Email')->email(),
                Forms\Components\TextInput::make('phone')->label('Telepon'),
                Forms\Components\TextInput::make('whatsapp')->label('WhatsApp'),
                Forms\Components\Textarea::make('address')->label('Alamat')->rows(3),
            ])->columns(2),
            Forms\Components\Section::make('Media Sosial')->schema([
                Forms\Components\TextInput::make('instagram')->label('Instagram URL'),
                Forms\Components\TextInput::make('linkedin')->label('LinkedIn URL'),
                Forms\Components\TextInput::make('youtube')->label('YouTube URL'),
            ])->columns(3),
            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title'),
                Forms\Components\Textarea::make('meta_description')->label('Meta Description')->rows(3),
            ])->columns(1),
        ])->statePath('data');
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
