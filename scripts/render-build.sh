#!/usr/bin/env bash
set -euo pipefail

APP_DIR="app"

if [ ! -d "$APP_DIR" ]; then
  composer create-project laravel/laravel "$APP_DIR" --no-interaction --prefer-dist
fi

cp scaffold/app/Http/Controllers/DashboardController.php "$APP_DIR/app/Http/Controllers/DashboardController.php"
cp scaffold/routes/web.php "$APP_DIR/routes/web.php"
cp scaffold/routes/api.php "$APP_DIR/routes/api.php"
cp scaffold/resources/views/dashboard.blade.php "$APP_DIR/resources/views/dashboard.blade.php"

cd "$APP_DIR"
php artisan key:generate --force
