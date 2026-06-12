<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Pengaturan Website';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'contact_info' => Setting::getJson('contact_info', []),
            'social_media' => Setting::getJson('social_media', []),
            'seo' => Setting::getJson('seo', []),
            'nav_custom_links' => Setting::getJson('nav_custom_links', []),
            'branding' => Setting::getJson('branding', []),
            'whatsapp_button' => Setting::getJson('whatsapp_button', []),
        ]);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                Section::make('📞 Informasi Kontak')
                    ->description('Informasi kontak yang ditampilkan di seluruh website.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('contact_info.email')->label('Email')->email()
                            ->placeholder('madeenajog@gmail.com'),
                        TextInput::make('contact_info.phone')->label('Telepon')
                            ->placeholder('+62 821 3811 4011'),
                        TextInput::make('contact_info.whatsapp')->label('WhatsApp')
                            ->placeholder('+62 857 2830 4141')
                            ->helperText('Nomor WhatsApp dengan kode negara.'),
                        Textarea::make('contact_info.address')->label('Alamat')->rows(2)
                            ->placeholder('Jl. Lowanu No. 68-72, Yogyakarta'),
                    ])->columns(2),

                Section::make('🌐 Media Sosial')
                    ->description('Link media sosial perusahaan.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('social_media.instagram')->label('Instagram URL')
                            ->placeholder('https://instagram.com/madeena'),
                        TextInput::make('social_media.linkedin')->label('LinkedIn URL'),
                        TextInput::make('social_media.youtube')->label('YouTube URL'),
                    ])->columns(3),

                Section::make('🔍 SEO')
                    ->description('Pengaturan mesin pencari (Google, dll).')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo.meta_title')->label('Judul Website (Meta Title)')
                            ->placeholder('PT Madeena Karya Indonesia')
                            ->helperText('Judul yang muncul di tab browser dan hasil pencarian Google.'),
                        Textarea::make('seo.meta_description')->label('Deskripsi Website (Meta Description)')
                            ->rows(3)
                            ->placeholder('Produsen alat Digital Direct Radiography (DDR) buatan Indonesia.')
                            ->helperText('Deskripsi singkat yang muncul di hasil pencarian Google. Maks 160 karakter.'),
                    ])->columns(1),

                Section::make('🔗 Navigasi Tambahan')
                    ->description('Link navigasi tambahan di header website.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('nav_custom_links')
                            ->label('Link Kustom')
                            ->schema([
                                TextInput::make('label')->label('Label')->required(),
                                TextInput::make('url')->label('URL')->required(),
                            ])
                            ->addActionLabel('+ Tambah Link')
                            ->helperText('Akan muncul di navigasi atas website.'),
                    ]),

                Section::make('🎨 Pengaturan Tampilan')
                    ->description('Logo, warna, dan font website.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        FileUpload::make('branding.logo')->label('Logo Website')
                            ->image()->disk('public')->directory('branding')
                            ->helperText('Unggah logo perusahaan. Akan ditampilkan di header dan footer.'),
                        TextInput::make('branding.primary_color')->label('Warna Utama')
                            ->type('color')->default('#1a365d')
                            ->helperText('Warna utama website (header, tombol, dll).'),
                        TextInput::make('branding.secondary_color')->label('Warna Sekunder')
                            ->type('color')->default('#2dd4bf')
                            ->helperText('Warna aksen website (highlight, link, dll).'),
                        Select::make('branding.font_family')->label('Font Website')
                            ->options([
                                'Inter' => 'Inter (Modern)',
                                'Noto Sans' => 'Noto Sans (Clean)',
                                'Roboto' => 'Roboto (Classic)',
                                'Outfit' => 'Outfit (Trendy)',
                                'Poppins' => 'Poppins (Friendly)',
                            ])->default('Inter')
                            ->helperText('Pilih font untuk seluruh website.'),
                    ])->columns(2),

                Section::make('💬 Tombol WhatsApp Melayang')
                    ->description('Tombol chat WhatsApp di pojok kanan bawah website.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('whatsapp_button.enabled')->label('Aktifkan Tombol WhatsApp')
                            ->default(true)
                            ->helperText('Tampilkan tombol chat WhatsApp di pojok kanan bawah.'),
                        TextInput::make('whatsapp_button.number')->label('Nomor WhatsApp')
                            ->placeholder('+62 857 2830 4141')
                            ->helperText('Nomor yang akan dihubungi saat tombol diklik.'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
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

        Setting::setJson('contact_info', $state['contact_info'] ?? []);
        Setting::setJson('social_media', $state['social_media'] ?? []);
        Setting::setJson('seo', $state['seo'] ?? []);
        Setting::setJson('nav_custom_links', $state['nav_custom_links'] ?? []);
        Setting::setJson('branding', $state['branding'] ?? []);
        Setting::setJson('whatsapp_button', $state['whatsapp_button'] ?? []);

        Notification::make()
            ->success()
            ->title('Berhasil disimpan')
            ->body('Pengaturan website telah diperbarui.')
            ->send();
    }
}
