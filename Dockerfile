FROM php:8.3-apache

WORKDIR /app

# Install dependencies
RUN apt-get update && apt-get install -y \
    composer \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . .

# Install PHP dependencies and build Laravel app
RUN bash scripts/render-build.sh

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/app/Http

# Configure Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 8000

# Start Laravel
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public"]
