@echo off
echo ========================================
echo Swagger Integration Verification
echo ========================================
echo.

echo [1/3] Checking if api-docs files exist...
if exist "storage\api-docs\api-docs.yaml" (
    echo   [OK] api-docs.yaml found
) else (
    echo   [ERROR] api-docs.yaml not found
)

if exist "storage\api-docs\api-docs.json" (
    echo   [OK] api-docs.json found
) else (
    echo   [ERROR] api-docs.json not found
)

echo.
echo [2/3] Checking if L5-Swagger is installed...
php artisan | findstr "l5-swagger" >nul
if %errorlevel% equ 0 (
    echo   [OK] L5-Swagger package installed
) else (
    echo   [ERROR] L5-Swagger not found
)

echo.
echo [3/3] Checking routes...
php artisan route:list | findstr "api/documentation" >nul
if %errorlevel% equ 0 (
    echo   [OK] Swagger routes registered
) else (
    echo   [ERROR] Routes not found
)

echo.
echo ========================================
echo.
echo Your Swagger documentation is ready!
echo.
echo To access it:
echo   1. Start your server: php artisan serve
echo   2. Open browser: http://localhost:8000/api/documentation
echo.
echo ========================================
pause
