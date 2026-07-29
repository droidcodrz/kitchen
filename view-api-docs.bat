@echo off
echo ========================================
echo Kitchen Manufacturing API Documentation
echo ========================================
echo.
echo Choose an option:
echo.
echo 1. Open Swagger UI in browser (Recommended)
echo 2. Start Swagger UI Server (requires npm)
echo 3. Open in Swagger Editor online
echo 4. View README
echo.
set /p choice="Enter your choice (1-4): "

if "%choice%"=="1" (
    echo.
    echo Opening Swagger UI in your default browser...
    start swagger-ui.html
    echo.
    echo Note: You may need to serve this via a local server if CORS errors occur.
    echo Run option 2 to start a local server.
    pause
) else if "%choice%"=="2" (
    echo.
    echo Installing swagger-ui-watcher...
    call npm install -g swagger-ui-watcher
    echo.
    echo Starting Swagger UI server...
    echo The API documentation will open in your browser at http://localhost:8000
    call swagger-ui-watcher openapi.yaml
) else if "%choice%"=="3" (
    echo.
    echo Opening Swagger Editor...
    echo Please copy the contents of openapi.yaml and paste into the editor.
    start https://editor.swagger.io/
    notepad openapi.yaml
    pause
) else if "%choice%"=="4" (
    echo.
    echo Opening API Documentation README...
    start API_DOCUMENTATION.md
    pause
) else (
    echo Invalid choice. Please run the script again.
    pause
)
