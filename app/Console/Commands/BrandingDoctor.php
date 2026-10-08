<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Checks everything the name and logo depend on and says which part is
 * broken.
 *
 * Written because the branding failing looks the same from the outside
 * whatever the cause - a missing table, a stale cache, a route that was
 * cached before the route existed, a file that never reached the disk. Each
 * needs a different fix and none of them show up in the logs.
 */
class BrandingDoctor extends Command
{
    protected $signature = 'branding:doctor {--repair : Create the settings table if it is missing}';

    protected $description = 'Check why the application name or logo is not showing';

    private array $problems = [];

    public function handle(): int
    {
        $this->newLine();
        $this->line('<options=bold>Branding check</>');
        $this->newLine();

        if ($this->option('repair')) {
            return $this->repair();
        }

        $this->checkEnvironment();
        $this->checkTable();
        $this->checkSettings();
        $this->checkCache();
        $this->checkLogoFile();
        $this->checkRoutes();
        $this->checkCachedArtefacts();

        $this->newLine();

        if ($this->problems === []) {
            $this->info('No problems found. Name and logo should be showing.');
            $this->line('If they are still not, open the logo URL directly in the browser');
            $this->line('and note the error: ' . (AppSetting::logoUrl() ?? '(no logo uploaded)'));

            return self::SUCCESS;
        }

        $this->error('Found ' . count($this->problems) . ' problem(s):');
        foreach ($this->problems as $i => $problem) {
            $this->newLine();
            $this->line('  <fg=red>' . ($i + 1) . '.</> ' . $problem['what']);
            $this->line('     <fg=yellow>Fix:</> ' . $problem['fix']);
        }
        $this->newLine();

        return self::FAILURE;
    }

    /**
     * Creates the settings table when it is missing.
     *
     * Only ever adds the one table. It clears that migration's record first,
     * because a record left behind by a table that no longer exists is what
     * makes migrate report nothing to do - and it touches no other migration,
     * so nothing else in the database is disturbed.
     */
    private function repair(): int
    {
        if (Schema::hasTable('app_settings')) {
            $this->info('Nothing to repair: the app_settings table already exists.');

            return self::SUCCESS;
        }

        $this->line(' Settings table is missing. Creating it.');

        DB::table('migrations')
            ->where('migration', '2026_10_08_000001_create_app_settings_table')
            ->delete();

        $this->call('migrate', [
            '--path' => 'database/migrations/2026_10_08_000001_create_app_settings_table.php',
            '--force' => true,
        ]);

        if (!Schema::hasTable('app_settings')) {
            $this->error('The table still does not exist. Run "php artisan migrate" and send the full output.');

            return self::FAILURE;
        }

        $this->call('optimize:clear');
        $this->newLine();
        $this->info('Done. Run "php artisan branding:doctor" to confirm.');

        return self::SUCCESS;
    }

    private function problem(string $what, string $fix): void
    {
        $this->problems[] = ['what' => $what, 'fix' => $fix];
    }

    private function row(string $label, string $value, bool $ok = true): void
    {
        $mark = $ok ? '<fg=green>ok</>' : '<fg=red>!!</>';
        $this->line(sprintf('  %s  %-26s %s', $mark, $label, $value));
    }

    private function checkEnvironment(): void
    {
        $this->line(' <options=bold>Environment</>');
        $this->row('database connection', (string) config('database.default'));
        $this->row('database name', (string) config('database.connections.' . config('database.default') . '.database'));
        $this->row('cache store', (string) config('cache.default'));
        $this->row('APP_URL', (string) config('app.url'));

        $hasFileinfo = extension_loaded('fileinfo');
        $this->row('fileinfo extension', $hasFileinfo ? 'loaded' : 'NOT loaded', $hasFileinfo);
        $this->newLine();
    }

    private function checkTable(): void
    {
        $this->line(' <options=bold>Table</>');

        if (!Schema::hasTable('app_settings')) {
            $this->row('app_settings table', 'MISSING', false);

            $recorded = DB::table('migrations')
                ->where('migration', '2026_10_08_000001_create_app_settings_table')
                ->exists();

            $this->row('migration recorded', $recorded ? 'yes - but table is gone' : 'no', !$recorded);

            if ($recorded) {
                // The nastiest version of this: migrate reports "Nothing to
                // migrate" and exits cleanly, because as far as it is concerned
                // the work is done. The record has to go before it will run
                // again.
                $this->problem(
                    'The migration is recorded as already run, but the table is not there - so "php artisan migrate" reports nothing to do and never creates it.',
                    'php artisan branding:doctor --repair'
                );
            } else {
                $this->problem(
                    'The app_settings table does not exist, so nothing can be saved.',
                    'php artisan migrate --path=database/migrations/2026_10_08_000001_create_app_settings_table.php --force'
                );
            }

            $this->newLine();

            return;
        }

        $this->row('app_settings table', 'exists');

        $recorded = DB::table('migrations')
            ->where('migration', '2026_10_08_000001_create_app_settings_table')
            ->exists();
        $this->row('migration recorded', $recorded ? 'yes' : 'no (table made another way)', true);
        $this->newLine();
    }

    private function checkSettings(): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        $this->line(' <options=bold>Saved settings</>');

        $rows = DB::table('app_settings')->pluck('value', 'key');

        if ($rows->isEmpty()) {
            $this->row('rows in table', '0 - nothing saved yet', false);
            $this->problem(
                'Nothing has been saved. The Branding form has not been submitted, or the save failed.',
                'Sign in as an administrator, go to Settings, fill in Branding and press Save Branding.'
            );
            $this->newLine();

            return;
        }

        foreach ($rows as $key => $value) {
            $this->row($key, $value === null ? '(empty)' : (string) $value);
        }
        $this->newLine();
    }

    private function checkCache(): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        $this->line(' <options=bold>Cache</>');

        $fromDatabase = DB::table('app_settings')
            ->where('key', AppSetting::KEY_APP_NAME)
            ->value('value');
        $throughModel = AppSetting::appName();

        $expected = ($fromDatabase === null || $fromDatabase === '')
            ? AppSetting::DEFAULT_APP_NAME
            : $fromDatabase;

        $matches = $throughModel === $expected;

        $this->row('name in database', $fromDatabase === null ? '(none)' : (string) $fromDatabase);
        $this->row('name the app reads', $throughModel, $matches);

        if (!$matches) {
            $this->problem(
                'The application is reading a stale cached name. The database says "' . $expected . '" but the application shows "' . $throughModel . '".',
                'php artisan optimize:clear'
            );
        }
        $this->newLine();
    }

    private function checkLogoFile(): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        $this->line(' <options=bold>Logo file</>');

        $path = DB::table('app_settings')->where('key', AppSetting::KEY_LOGO_PATH)->value('value');

        if ($path === null || $path === '') {
            $this->row('logo', 'none uploaded - the built-in icon is used');
            $this->newLine();

            return;
        }

        $disk = Storage::disk(AppSetting::logoDisk());
        $exists = $disk->exists($path);

        $this->row('stored path', (string) $path);
        $this->row('file on disk', $exists ? 'found' : 'MISSING', $exists);

        if (!$exists) {
            $this->problem(
                'A logo is recorded but its file is not on this machine at storage/app/' . AppSetting::logoDisk() . '/' . $path . '. It was probably uploaded on a different machine.',
                'Upload the logo again from Settings on this machine.'
            );
            $this->newLine();

            return;
        }

        $size = $disk->size($path);
        $this->row('file size', number_format($size) . ' bytes', $size > 0);

        if ($size === 0) {
            $this->problem(
                'The logo file is empty, so the browser has nothing to draw.',
                'Upload the logo again from Settings.'
            );
        }

        $this->row('served as', AppSetting::logoMimeType());
        $this->row('logo URL', (string) AppSetting::logoUrl());
        $this->newLine();
    }

    private function checkRoutes(): void
    {
        $this->line(' <options=bold>Routes</>');

        foreach (['branding.logo', 'settings.update-branding'] as $name) {
            $exists = Route::has($name);
            $this->row($name, $exists ? 'registered' : 'MISSING', $exists);

            if (!$exists) {
                $this->problem(
                    'The route ' . $name . ' is not registered. Routes were most likely cached before this feature was pulled.',
                    'php artisan optimize:clear'
                );
            }
        }
        $this->newLine();
    }

    private function checkCachedArtefacts(): void
    {
        $this->line(' <options=bold>Cached files</>');

        $caches = [
            'routes' => base_path('bootstrap/cache/routes-v7.php'),
            'config' => base_path('bootstrap/cache/config.php'),
        ];

        foreach ($caches as $label => $file) {
            $cached = file_exists($file);
            $this->row($label . ' cached', $cached ? 'yes' : 'no');

            if ($cached) {
                $this->problem(
                    ucfirst($label) . ' is cached. If it was cached before this feature was pulled, the application is still running the old ' . $label . '.',
                    'php artisan optimize:clear'
                );
            }
        }
        $this->newLine();
    }
}
