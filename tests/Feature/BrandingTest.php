<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The settings are cached per request and in the cache store, and both
     * outlive a single test in the same process. Without this, settings left
     * by one test answer the next one's lookups.
     */
    protected function setUp(): void
    {
        parent::setUp();

        AppSetting::flushCache();
    }

    private function admin(): User
    {
        $role = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function nonAdmin(): User
    {
        $role = Role::create(['name' => 'Worker', 'slug' => 'worker']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_default_name_is_used_when_nothing_is_set(): void
    {
        $this->assertSame(AppSetting::DEFAULT_APP_NAME, AppSetting::appName());
    }

    public function test_admin_can_change_the_application_name(): void
    {
        $this->actingAs($this->admin())
            ->post(route('settings.update-branding'), ['app_name' => 'Acme Kitchens'])
            ->assertRedirect();

        $this->assertSame('Acme Kitchens', AppSetting::appName());
    }

    public function test_the_name_shows_on_the_login_page(): void
    {
        AppSetting::put(AppSetting::KEY_APP_NAME, 'Acme Kitchens');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Acme Kitchens', false);
    }

    public function test_clearing_the_name_restores_the_default(): void
    {
        AppSetting::put(AppSetting::KEY_APP_NAME, 'Acme Kitchens');

        $this->actingAs($this->admin())
            ->post(route('settings.update-branding'), ['app_name' => '']);

        $this->assertSame(AppSetting::DEFAULT_APP_NAME, AppSetting::appName());
    }

    public function test_a_non_admin_cannot_change_the_branding(): void
    {
        $this->actingAs($this->nonAdmin())
            ->post(route('settings.update-branding'), ['app_name' => 'Hijacked'])
            ->assertForbidden();

        $this->assertSame(AppSetting::DEFAULT_APP_NAME, AppSetting::appName());
    }

    public function test_a_guest_cannot_change_the_branding(): void
    {
        $this->post(route('settings.update-branding'), ['app_name' => 'Hijacked'])
            ->assertRedirect(route('login'));

        $this->assertSame(AppSetting::DEFAULT_APP_NAME, AppSetting::appName());
    }

    public function test_admin_can_upload_a_logo_and_it_is_stored_on_the_private_disk(): void
    {
        Storage::fake('private');

        $this->actingAs($this->admin())
            ->post(route('settings.update-branding'), [
                'logo' => UploadedFile::fake()->image('brand.png', 300, 300),
            ])
            ->assertRedirect();

        $path = AppSetting::get(AppSetting::KEY_LOGO_PATH);

        $this->assertNotNull($path);
        $this->assertStringStartsWith('branding/', $path);
        Storage::disk('private')->assertExists($path);
        $this->assertTrue(AppSetting::hasLogo());
    }

    public function test_the_logo_is_served_without_signing_in(): void
    {
        Storage::fake('private');

        $this->actingAs($this->admin())->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('brand.png', 300, 300),
        ]);

        // The login page shows the logo, so this has to work for a guest.
        auth()->logout();

        $this->get(route('branding.logo'))->assertOk();
    }

    public function test_the_logo_route_404s_when_no_logo_is_set(): void
    {
        $this->get(route('branding.logo'))->assertNotFound();
    }

    public function test_replacing_the_logo_deletes_the_previous_file(): void
    {
        Storage::fake('private');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('first.png', 300, 300),
        ]);
        $first = AppSetting::get(AppSetting::KEY_LOGO_PATH);

        $this->actingAs($admin)->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('second.png', 300, 300),
        ]);
        $second = AppSetting::get(AppSetting::KEY_LOGO_PATH);

        $this->assertNotSame($first, $second);
        Storage::disk('private')->assertMissing($first);
        Storage::disk('private')->assertExists($second);
    }

    public function test_removing_the_logo_clears_it(): void
    {
        Storage::fake('private');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('brand.png', 300, 300),
        ]);
        $path = AppSetting::get(AppSetting::KEY_LOGO_PATH);

        $this->actingAs($admin)->post(route('settings.update-branding'), ['remove_logo' => '1']);

        Storage::disk('private')->assertMissing($path);
        $this->assertFalse(AppSetting::hasLogo());
        $this->assertNull(AppSetting::logoUrl());
    }

    public function test_a_non_image_file_is_rejected(): void
    {
        Storage::fake('private');

        $this->actingAs($this->admin())
            ->post(route('settings.update-branding'), [
                'logo' => UploadedFile::fake()->create('invoice.pdf', 40, 'application/pdf'),
            ])
            ->assertSessionHasErrors('logo');

        $this->assertFalse(AppSetting::hasLogo());
    }

    public function test_an_svg_is_rejected(): void
    {
        Storage::fake('private');

        // Left out of the allowed types on purpose: an SVG can carry script and
        // the logo is rendered on a page every user loads.
        $this->actingAs($this->admin())
            ->post(route('settings.update-branding'), [
                'logo' => UploadedFile::fake()->create('brand.svg', 10, 'image/svg+xml'),
            ])
            ->assertSessionHasErrors('logo');

        $this->assertFalse(AppSetting::hasLogo());
    }

    public function test_an_overlong_name_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('settings.update-branding'), ['app_name' => str_repeat('a', 61)])
            ->assertSessionHasErrors('app_name');

        $this->assertSame(AppSetting::DEFAULT_APP_NAME, AppSetting::appName());
    }

    public function test_the_logo_url_changes_when_the_logo_is_replaced(): void
    {
        Storage::fake('private');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('first.png', 300, 300),
        ]);
        $firstUrl = AppSetting::logoUrl();

        AppSetting::put(AppSetting::KEY_LOGO_VERSION, 'changed');

        $this->assertNotSame($firstUrl, AppSetting::logoUrl());
    }

    public function test_a_missing_table_is_survived_without_being_cached(): void
    {
        // What happens on a server where the migration has not run yet: the
        // page must still render on the default name rather than failing on
        // the missing table.
        Schema::drop('app_settings');

        $this->assertSame(AppSetting::DEFAULT_APP_NAME, AppSetting::appName());
        $this->get(route('login'))->assertOk();

        // And the fallback must not have been cached. Caching it would leave
        // every page on the default name for the full hour after the migration
        // finally ran, which looks exactly like the setting being broken.
        Schema::create('app_settings', function ($table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Inserted straight through the query builder on purpose. Going via
        // AppSetting::put would fire the model's saved event and clear the
        // cache itself, which is exactly the thing under test here - the row
        // has to appear without anything inviting the cache to refresh.
        DB::table('app_settings')->insert([
            'key' => AppSetting::KEY_APP_NAME,
            'value' => 'Acme Kitchens',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertSame('Acme Kitchens', AppSetting::appName());
    }

    public function test_a_get_on_the_branding_url_lands_on_settings(): void
    {
        // A refresh or a back button after a save requests this with GET, which
        // answered with an exception page before.
        $this->actingAs($this->admin())
            ->get('/settings/branding')
            ->assertRedirect(route('settings.index'));
    }

    public function test_the_logo_is_served_with_an_image_content_type(): void
    {
        Storage::fake('private');

        $this->actingAs($this->admin())->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('brand.png', 300, 300),
        ]);

        // The application sends X-Content-Type-Options: nosniff, so a wrong or
        // missing type makes the browser refuse to render the image at all - it
        // shows as a broken icon with nothing in the logs to say why.
        $this->get(route('branding.logo'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_the_content_type_does_not_depend_on_a_stored_value(): void
    {
        Storage::fake('private');

        $this->actingAs($this->admin())->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('brand.png', 300, 300),
        ]);

        // A logo saved before the type was being recorded, and anything that
        // loses the value later, still has to serve as an image.
        AppSetting::put(AppSetting::KEY_LOGO_MIME, null);

        $this->get(route('branding.logo'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_a_spoofed_content_type_is_not_served_back(): void
    {
        Storage::fake('private');

        $this->actingAs($this->admin())->post(route('settings.update-branding'), [
            'logo' => UploadedFile::fake()->image('brand.png', 300, 300),
        ]);

        // The recorded type comes from the browser, so a crafted request could
        // put anything there. It must never come back out as something a
        // browser would treat as a document.
        AppSetting::put(AppSetting::KEY_LOGO_MIME, 'text/html');

        $this->get(route('branding.logo'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }
}
