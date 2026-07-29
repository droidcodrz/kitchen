#!/bin/bash

echo "========================================"
echo "Kitchen Manufacturing API Documentation"
echo "========================================"
echo ""
echo "Choose an option:"
echo ""
echo "1. Open Swagger UI in browser (Recommended)"
echo "2. Start Swagger UI Server (requires npm)"
echo "3. Open in Swagger Editor online"
echo "4. View README"
echo ""
read -p "Enter your choice (1-4): " choice

case $choice in
    1)
        echo ""
        echo "Opening Swagger UI in your default browser..."
        if command -v xdg-open > /dev/null; then
            xdg-open swagger-ui.html
        elif command -v open > /dev/null; then
            open swagger-ui.html
        else
            echo "Please open swagger-ui.html in your browser manually"
        fi
        echo ""
        echo "Note: You may need to serve this via a local server if CORS errors occur."
        echo "Run option 2 to start a local server."
        ;;
    2)
        echo ""
        echo "Installing swagger-ui-watcher..."
        npm install -g swagger-ui-watcher
        echo ""
        echo "Starting Swagger UI server..."
        echo "The API documentation will open in your browser at http://localhost:8000"
        swagger-ui-watcher openapi.yaml
        ;;
    3)
        echo ""
        echo "Opening Swagger Editor..."
        echo "Please copy the contents of openapi.yaml and paste into the editor."
        if command -v xdg-open > /dev/null; then
            xdg-open https://editor.swagger.io/
        elif command -v open > /dev/null; then
            open https://editor.swagger.io/
        fi
        cat openapi.yaml
        ;;
    4)
        echo ""
        echo "Opening API Documentation README..."
        if command -v xdg-open > /dev/null; then
            xdg-open API_DOCUMENTATION.md
        elif command -v open > /dev/null; then
            open API_DOCUMENTATION.md
        else
            cat API_DOCUMENTATION.md
        fi
        ;;
    *)
        echo "Invalid choice. Please run the script again."
        exit 1
        ;;
esac
