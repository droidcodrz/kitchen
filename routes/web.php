<?php

use App\Http\Controllers\Admin\AlertConfigurationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DropdownOptionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StorageLocationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectAttachmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\Admin\CustomFieldDefinitionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects
    Route::get('projects/check-name', [ProjectController::class, 'checkName'])
    ->name('projects.check-name');
    Route::resource('projects', ProjectController::class);
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])
        ->name('projects.update-status');

    // Project Attachments
    Route::post('projects/{project}/attachments', [ProjectAttachmentController::class, 'store'])
        ->name('projects.attachments.store');
    Route::get('projects/{project}/attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])
        ->name('projects.attachments.download');
    Route::delete('projects/{project}/attachments/{attachment}', [ProjectAttachmentController::class, 'destroy'])
        ->name('projects.attachments.destroy');

    // Products
    Route::get('products/custom-fields/get', [ProductController::class, 'getCustomFields'])
        ->name('products.custom-fields.get');
    Route::post('categories/quick-add', [ProductController::class, 'quickAddCategory'])
        ->name('categories.quick-add');
    Route::resource('products', ProductController::class);

    // Product Folders
    Route::resource('product-folders', ProductFolderController::class)
        ->except(['create', 'show']);
    Route::post('product-folders/move-product', [ProductFolderController::class, 'moveProduct'])
        ->name('product-folders.move-product');

        Route::get('inventory/custom-fields/get', [InventoryItemController::class, 'getCustomFields'])
    ->name('inventory.custom-fields.get');

    // Inventory
    Route::resource('inventory', InventoryItemController::class)
        ->parameters(['inventory' => 'inventory_item']);
    Route::post('inventory/{inventory_item}/adjust-stock', [InventoryItemController::class, 'adjustStock'])
        ->name('inventory.adjust-stock');

    // Teams
    Route::resource('teams', TeamController::class);

    // Team Members
    Route::post('teams/{team}/members', [TeamMemberController::class, 'store'])
        ->name('teams.members.store');
    Route::delete('teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])
        ->name('teams.members.destroy');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');

    // Calendar
    Route::get('calendar', [CalendarController::class, 'index'])
        ->name('calendar.index');

    // Calendar Events (API-style JSON endpoints)
    Route::prefix('api')->group(function () {
        Route::get('calendar-events', [CalendarEventController::class, 'index'])
            ->name('api.calendar-events.index');
        Route::post('calendar-events', [CalendarEventController::class, 'store'])
            ->name('api.calendar-events.store');
        Route::put('calendar-events/{calendarEvent}', [CalendarEventController::class, 'update'])
            ->name('api.calendar-events.update');
        Route::delete('calendar-events/{calendarEvent}', [CalendarEventController::class, 'destroy'])
            ->name('api.calendar-events.destroy');
    });

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])
        ->name('settings.index');
    Route::patch('settings/theme', [SettingsController::class, 'updateTheme'])
        ->name('settings.update-theme');

    // Trash / Waste Bin
    Route::get('trash', [TrashController::class, 'index'])
        ->name('trash.index');
    Route::post('trash/restore/{type}/{id}', [TrashController::class, 'restore'])
        ->name('trash.restore');
    Route::delete('trash/force-destroy/{type}/{id}', [TrashController::class, 'forceDestroy'])
        ->name('trash.force-destroy');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // Users
        Route::resource('users', UserController::class);

        // Roles (index and show only)
        Route::resource('roles', RoleController::class)->only(['index', 'show']);

        // Vendors
        Route::resource('vendors', VendorController::class);

        // Categories
        Route::resource('categories', CategoryController::class);

        // Clients
        Route::resource('clients', ClientController::class);

        // Storage Locations
        Route::resource('storage-locations', StorageLocationController::class);

        // Dropdown Options
        Route::get('dropdown-options', [DropdownOptionController::class, 'index'])->name('dropdown-options.index');
        Route::post('dropdown-options', [DropdownOptionController::class, 'store'])->name('dropdown-options.store');
        Route::patch('dropdown-options/{dropdownOption}', [DropdownOptionController::class, 'update'])->name('dropdown-options.update');
        Route::delete('dropdown-options/{dropdownOption}', [DropdownOptionController::class, 'destroy'])->name('dropdown-options.destroy');

        // Alert Configurations
        Route::get('alert-configurations', [AlertConfigurationController::class, 'index'])
            ->name('alert-configurations.index');
        Route::patch('alert-configurations/{alertConfiguration}', [AlertConfigurationController::class, 'update'])
            ->name('alert-configurations.update');

        Route::resource('custom-field-definitions', CustomFieldDefinitionController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
