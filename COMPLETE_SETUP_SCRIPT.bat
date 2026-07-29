@echo off
echo ========================================
echo Complete Setup Script for Custom Fields
echo ========================================
echo.

echo Step 1: Running migrations...
php artisan migrate
echo.

echo Step 2: Seeding basic data...
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=PermissionsSeeder
php artisan db:seed --class=PermissionRoleSeeder
php artisan db:seed --class=AdminUserSeeder
echo.

echo Step 3: Seeding categories and custom fields...
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=DropdownOptionSeeder
php artisan db:seed --class=CustomFieldDefinitionSeeder
echo.

echo Step 4: Seeding other data...
php artisan db:seed --class=VendorSeeder
php artisan db:seed --class=StorageLocationSeeder
php artisan db:seed --class=ClientSeeder
echo.

echo Step 5: Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Now run: php artisan serve
echo Then open: http://localhost:8000
echo Login: admin@kitchen.com / password
echo.
pause
