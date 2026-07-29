@echo off
echo ========================================
echo Checking Custom Fields Implementation
echo ========================================
echo.

echo [1/5] Checking Categories...
php artisan tinker --execute="echo 'Total Categories: ' . App\Models\Category::count() . PHP_EOL; App\Models\Category::where('slug', 'chemicals')->first() ? print('✓ Chemicals category exists' . PHP_EOL) : print('✗ Chemicals NOT found' . PHP_EOL);"
echo.

echo [2/5] Checking Custom Field Definitions...
php artisan tinker --execute="echo 'Total Custom Fields: ' . App\Models\CustomFieldDefinition::count() . PHP_EOL;"
echo.

echo [3/5] Checking Chemicals Category Fields...
php artisan tinker --execute="$cat = App\Models\Category::where('slug', 'chemicals')->first(); if($cat) { $fields = App\Models\CustomFieldDefinition::where('category_id', $cat->id)->where('entity_type', 'product')->get(); echo 'Chemicals has ' . $fields->count() . ' fields:' . PHP_EOL; foreach($fields as $f) { echo '  - ' . $f->field_label . ' [' . $f->field_type . ']' . PHP_EOL; } } else { echo '✗ Category not found!' . PHP_EOL; }"
echo.

echo [4/5] Checking Routes...
php artisan route:list | findstr "custom-fields"
echo.

echo [5/5] Checking Material Types...
php artisan tinker --execute="echo 'Material Types: ' . App\Models\DropdownOption::where('type', 'material_type')->count() . PHP_EOL; echo 'Item Types: ' . App\Models\DropdownOption::where('type', 'item_type')->count() . PHP_EOL;"
echo.

echo ========================================
echo Status Check Complete!
echo ========================================
echo.
echo If all checks passed, custom fields should work.
echo If any check failed, run: COMPLETE_SETUP_SCRIPT.bat
echo.
pause
