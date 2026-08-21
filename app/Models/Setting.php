<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public const SUPPORTED_LOCALES = ['id', 'en'];
    public const DEFAULT_LOCALE = 'id';

    protected $fillable = ['key', 'value', 'group'];

    public static function normalizeLocale(?string $locale): string
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : self::DEFAULT_LOCALE;
    }

    public static function homepagePublishedKey(?string $locale = 'id'): string
    {
        $norm = static::normalizeLocale($locale);

        return $norm === 'en' ? 'homepage_sections_en' : 'homepage_sections';
    }

    public static function homepageDraftKey(?string $locale = 'id'): string
    {
        $norm = static::normalizeLocale($locale);

        return $norm === 'en' ? 'homepage_sections_en_draft' : 'homepage_sections_draft';
    }

    public static function getHomepageSections(?string $locale = 'id', bool $useDraft = false): array
    {
        $norm = static::normalizeLocale($locale);
        $draftKey = static::homepageDraftKey($norm);
        $publishedKey = static::homepagePublishedKey($norm);

        if ($useDraft) {
            return static::getJson($draftKey, static::getJson($publishedKey, [])) ?? [];
        }

        return static::getJson($publishedKey, []) ?? [];
    }

    public static function get(string $key, $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function getJson(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? json_decode($setting->value, true) : $default;
    }

    public static function setJson(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
    }
}
