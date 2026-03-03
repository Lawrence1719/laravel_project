FROM php:8.3-cli AS builder

WORKDIR /app

# Install dependencies for building
RUN apt-get update -qq && apt-get install -y -qq \
    git \
    sqlite3 \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Move into the Laravel application directory
WORKDIR /app/app

# Install PHP dependencies inside Laravel folder
RUN composer install --no-dev --optimize-autoloader

# switch back to root app folder and build Laravel
WORKDIR /app

# Build Laravel app (script expects to run from repo root)
RUN bash scripts/render-build.sh

# Production stage
FROM php:8.3-cli

WORKDIR /app

# Install runtime dependencies (including build deps for extensions)
RUN apt-get update -qq && apt-get install -y -qq \
    sqlite3 \
    libsqlite3-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Copy built app from builder
COPY --from=builder /app /app

# Set permissions
RUN chown -R nobody:nogroup /app/storage /app/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Laravel
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public"]
