FROM php:8.3-cli AS builder

WORKDIR /app

# Install dependencies for building
RUN apt-get update -qq && apt-get install -y -qq \
    git \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite zip

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Build Laravel app first (script will create /app/app and copy scaffolding)
RUN bash scripts/render-build.sh

# Move into the Laravel application directory to install dependencies
WORKDIR /app/app

# Install PHP dependencies inside Laravel folder
RUN composer install --no-dev --optimize-autoloader

# switch back to root for any further actions
WORKDIR /app

# Production stage
FROM php:8.3-cli

WORKDIR /app

# Install runtime dependencies (including build deps for extensions)
RUN apt-get update -qq && apt-get install -y -qq \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    pkg-config \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite zip

# Copy built app from builder
COPY --from=builder /app /app

# Set working directory to the actual Laravel root created by the build script
WORKDIR /app/app

# Ensure directories exist then set permissions
RUN mkdir -p /app/app/storage /app/app/bootstrap/cache \
    && chown -R nobody:nogroup /app/app/storage /app/app/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Laravel using correct public path
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/app/public"]
