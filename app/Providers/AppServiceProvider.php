<?php

namespace App\Providers;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Policies\InventoryItemPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureRateLimiting();
        $this->registerGates();
        $this->shareUploadLimits();
    }

    /**
     * Expose PHP's real upload ceilings to every view, so client-side file
     * validation rejects what this server would refuse anyway. Without them the
     * form can only guess, and anything over the limit is aborted mid-body by
     * PHP - which reaches the user as a bare connection error, not a message.
     */
    protected function shareUploadLimits(): void
    {
        $toBytes = static function (string $value): int {
            $value = trim($value);

            if ($value === '') {
                return 0;
            }

            $bytes = (int) $value;

            return match (strtolower(substr($value, -1))) {
                'g' => $bytes * 1024 ** 3,
                'm' => $bytes * 1024 ** 2,
                'k' => $bytes * 1024,
                default => $bytes,
            };
        };

        $uploadMax = $toBytes((string) ini_get('upload_max_filesize'));
        $postMax = $toBytes((string) ini_get('post_max_size'));

        // A single file can never exceed the whole-request ceiling either.
        $uploadMax = $postMax > 0 ? min($uploadMax ?: $postMax, $postMax) : $uploadMax;

        $label = static fn (int $bytes): string => $bytes >= 1024 ** 3
            ? round($bytes / 1024 ** 3, 1) . 'GB'
            : round($bytes / 1024 ** 2) . 'MB';

        View::share('uploadMaxBytes', $uploadMax);
        View::share('postMaxBytes', $postMax);
        View::share('uploadMaxLabel', $label($uploadMax));
        View::share('postMaxLabel', $label($postMax));
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(InventoryItem::class, InventoryItemPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }

    /**
     * Configure rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->input('email') . '|' . $request->ip()
            );
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });
    }

    /**
     * Register authorization gates.
     */
    protected function registerGates(): void
    {
        // Allow admin role to bypass all authorization checks
        // EXCEPT for status-based restrictions on projects (update/delete)
        Gate::before(function (User $user, string $ability, array $models = []) {
            if ($user->role?->slug === 'admin') {
                // If checking project update/delete, let the policy handle it
                // to enforce status-based restrictions even for admins
                if (in_array($ability, ['update', 'delete']) && isset($models[0]) && $models[0] instanceof Project) {
                    return null; // Let ProjectPolicy decide based on status
                }

                return true; // Bypass all other checks
            }

            return null;
        });
    }
}
