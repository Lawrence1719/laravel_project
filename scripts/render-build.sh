#!/usr/bin/env bash
set -euo pipefail

APP_DIR="app"

if [ ! -d "$APP_DIR" ]; then
  composer create-project laravel/laravel "$APP_DIR" --no-interaction --prefer-dist
fi

# make sure dependencies are installed (composer create-project may not run in Docker build context)
if [ -f "$APP_DIR/composer.json" ] && [ ! -d "$APP_DIR/vendor" ]; then
  (cd "$APP_DIR" && composer install --no-dev --optimize-autoloader)
fi

cp scaffold/app/Http/Controllers/DashboardController.php "$APP_DIR/app/Http/Controllers/DashboardController.php"
cp scaffold/app/Http/Controllers/AuthController.php "$APP_DIR/app/Http/Controllers/AuthController.php"
cp scaffold/routes/web.php "$APP_DIR/routes/web.php"
cp scaffold/routes/api.php "$APP_DIR/routes/api.php"
cp scaffold/resources/views/dashboard.blade.php "$APP_DIR/resources/views/dashboard.blade.php"
cp scaffold/resources/views/auth.blade.php "$APP_DIR/resources/views/auth.blade.php"

cd "$APP_DIR"
# copy example env if missing (create-project doesn't always do this inside Docker)
if [ ! -f .env ]; then
  cp .env.example .env
fi
php artisan key:generate --force
php artisan migrate --force
