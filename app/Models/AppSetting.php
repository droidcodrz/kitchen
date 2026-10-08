<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    public const KEY_APP_NAME = 'app_name';
    public const KEY_LOGO_PATH = 'logo_path';
    public const KEY_LOGO_DISK = 'logo_disk';
    public const KEY_LOGO_VERSION = 'logo_version';

    /**
     * Shown wherever no name has been set. Also what the name reverts to when
     * the field is cleared, so the application is never left nameless.
     */
    public const DEFAULT_APP_NAME = 'Kitchen Manufacturing';

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCached());
        static::deleted(fn () => static::forgetCached());
    }

    private static function forgetCached(): void
    {
        static::$cached = null;
        Cache::forget('app_settings:all');
    }

    /** @var array<string, string|null>|null */
    private static ?array $cached = null;

    /**
     * Every setting in one array. The branding is read on every page render -
     * the title, the sidebar and the login screen all want it - so this is
     * fetched once rather than a query per lookup.
     *
     * @return array<string, string|null>
     */
    public static function all_settings(): array
    {
        return static::$cached ??= Cache::remember('app_settings:all', 3600, function () {
            // The table is created by a migration; a request served before that
            // migration runs must still render rather than fail on a missing
            // table, so fall back to an empty set.
            try {
                return static::query()->pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::all_settings()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * The name to display. Never blank: an empty stored value means the user
     * cleared the field, which reverts to the default rather than showing
     * nothing at all.
     */
    public static function appName(): string
    {
        return static::get(static::KEY_APP_NAME, static::DEFAULT_APP_NAME);
    }

    /**
     * Whether a logo has been uploaded. The file is checked as well as the
     * stored path: a path left behind by a file that is gone would otherwise
     * render a broken image in place of the logo.
     */
    public static function hasLogo(): bool
    {
        $path = static::get(static::KEY_LOGO_PATH);

        if ($path === null) {
            return false;
        }

        return \Illuminate\Support\Facades\Storage::disk(static::logoDisk())->exists($path);
    }

    public static function logoDisk(): string
    {
        return static::get(static::KEY_LOGO_DISK, 'private');
    }

    /**
     * Carries the upload time so a replaced logo is not served from the
     * browser's cache under the same URL as the one it replaced. The version
     * is stored as a setting of its own rather than read from the row's
     * timestamp, so this stays inside the one cached lookup.
     */
    public static function logoUrl(): ?string
    {
        if (!static::hasLogo()) {
            return null;
        }

        return route('branding.logo', ['v' => static::get(static::KEY_LOGO_VERSION, '1')]);
    }
}
