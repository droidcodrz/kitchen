#!/usr/bin/env bash
#
# Deploy script - run this after pulling new code.
#
#   ./deploy.sh
#
# Safe to run repeatedly. Stops at the first failure rather than carrying on
# and leaving the application half-updated.
#
# This is only the deploy step. The queue worker and the scheduler are server
# services that must already be set up - see docs/SERVER-SETUP.md. Running
# this script does not start them.

set -euo pipefail

cd "$(dirname "$0")"

echo "==> Pulling latest code"
git pull

echo "==> Installing PHP dependencies"
# vendor/ is not in git, so a fresh checkout has none. Without it artisan
# cannot even start: "Failed to open stream: vendor/autoload.php".
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Building frontend assets"
# public/build is not in git either. Without it every page returns 500,
# because the layout asks Vite for a manifest that isn't there. This is the
# CSS/JS equivalent of composer install and is just as mandatory.
npm ci
npm run build

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Clearing caches"
# Compiled Blade templates persist between deploys. Without clearing them the
# old markup keeps rendering and the deploy looks like it did nothing.
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo "==> Restarting queue workers"
# A running worker holds the old code in memory and will never pick up what
# was just deployed. Skipping this is the most common reason a fix appears
# not to have worked.
php artisan queue:restart

echo
echo "Deploy complete."
echo
echo "Check the background services are alive:"
echo "  php artisan queue:monitor default      # queued work should not pile up"
echo "  php artisan schedule:list              # scheduled tasks and next run times"
