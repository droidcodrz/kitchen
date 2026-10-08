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
    public const KEY_LOGO_MIME = 'logo_mime';

    /**
     * The only types a logo may be served as. The value is recorded from the
     * upload, so it is checked against this rather than trusted: a browser
     * sends that header and a crafted request can say anything.
     */
    public const ALLOWED_LOGO_MIMES = ['image/png', 'image/jpeg', 'image/webp'];

    /**
     * Shown wherever no name has been set. Also what the name reverts to when
     * the field is cleared, so the application is never left nameless.
     */
    public const DEFAULT_APP_NAME = 'Kitchen Manufacturing';

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    /**
     * Drops both layers of caching - the per-request copy and the stored one.
     *
     * Public because the settings can change without a model event to notice
     * it: a row written straight through the query builder, a seeder, or a
     * database restored underneath a running application. Also what keeps one
     * test's settings from leaking into the next.
     */
    public static function flushCache(): void
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
        if (static::$cached !== null) {
            return static::$cached;
        }

        $cached = Cache::get('app_settings:all');

        if (is_array($cached)) {
            return static::$cached = $cached;
        }

        try {
            $values = static::query()->pluck('value', 'key')->all();
        } catch (\Throwable $e) {
            // The table is created by a migration, and a request served before
            // that migration runs must still render rather than fail on the
            // missing table.
            //
            // Returned without being cached, deliberately. Caching the empty
            // fallback would keep every page showing the default name for the
            // full hour after the migration finally ran, which looks exactly
            // like the setting not working.
            return [];
        }

        Cache::put('app_settings:all', $values, 3600);

        return static::$cached = $values;
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
     *
     * Absolute, which during a request means built from the address the
     * browser actually asked for. That matters where the application is not
     * served from the root of the domain: a relative path is produced by
     * stripping that root, so it comes out without the directory the
     * application sits in and points at an address that does not exist. It was
     * relative for a while and did exactly that.
     */
    public static function logoUrl(): ?string
    {
        if (!static::hasLogo()) {
            return null;
        }

        return route('branding.logo', ['v' => static::get(static::KEY_LOGO_VERSION, '1')]);
    }

    /**
     * The type the logo is served as.
     *
     * Recorded at upload rather than detected when it is served. Detection
     * needs the fileinfo extension, and where that is missing the type comes
     * out wrong or empty - which, with the nosniff header this application
     * sends, makes the browser refuse to render the image at all. It shows as
     * a broken icon, with nothing in the logs to say why.
     *
     * Falls back to the file extension for a logo uploaded before the type
     * was being stored, and to PNG if even that is gone.
     */
    public static function logoMimeType(): string
    {
        $stored = static::get(static::KEY_LOGO_MIME);

        if ($stored !== null && in_array($stored, static::ALLOWED_LOGO_MIMES, true)) {
            return $stored;
        }

        $extension = strtolower(pathinfo((string) static::get(static::KEY_LOGO_PATH), PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }
}
