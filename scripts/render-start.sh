#!/usr/bin/env bash
set -euo pipefail

cd app
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
