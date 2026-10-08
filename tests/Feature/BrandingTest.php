<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

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
}
