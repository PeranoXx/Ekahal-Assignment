#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "=========================================="
echo " Starting Ekahal Assessment Setup Script  "
echo "=========================================="

# 1. Environment Configuration Setup
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Ensure FORWARD_DB_PORT is set to avoid conflict on host port 3306
if ! grep -q "FORWARD_DB_PORT" .env; then
    echo "" >> .env
    echo "# Sail Forward Database Port to avoid host conflict" >> .env
    echo "FORWARD_DB_PORT=3309" >> .env
    echo "Added FORWARD_DB_PORT=3309 to .env to prevent local port conflicts."
fi

# 2. Bootstrap Composer dependencies using a temporary Docker container
# This is necessary because Sail is in the vendor/ directory, which doesn't exist on fresh clone.
if [ ! -d vendor ]; then
    echo "Installing Composer dependencies via temporary php-composer container..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs --no-scripts
else
    echo "Composer dependencies already installed (vendor/ exists)."
fi

# 3. Start Laravel Sail in detached mode
echo "Starting Laravel Sail containers..."
./vendor/bin/sail up -d

# 4. Wait for database container to be healthy
echo -n "Waiting for database container to become healthy..."
MAX_ATTEMPTS=60
ATTEMPT=0
while true; do
    # Get database container status
    CONTAINER_ID=$(./vendor/bin/sail ps -q mysql 2>/dev/null || true)
    if [ -n "$CONTAINER_ID" ]; then
        STATUS=$(docker inspect -f '{{.State.Health.Status}}' "$CONTAINER_ID" 2>/dev/null || true)
        if [ "$STATUS" = "healthy" ]; then
            break
        fi
    fi
    
    ATTEMPT=$((ATTEMPT + 1))
    if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
        echo ""
        echo "Error: Database container failed to become healthy within 60 seconds."
        echo "Check container logs using: ./vendor/bin/sail logs mysql"
        exit 1
    fi
    printf "."
    sleep 1
done
echo " Ready!"

# 5. Initialize application settings
echo "Generating application key..."
./vendor/bin/sail artisan key:generate --ansi --no-interaction

echo "Running database migrations..."
./vendor/bin/sail artisan migrate --force

echo "Run migrations and seeding..."
./vendor/bin/sail artisan db:seed --force

# 6. Install npm packages and build frontend assets
echo "Installing npm packages..."
./vendor/bin/sail npm install

echo "Building frontend assets..."
./vendor/bin/sail npm run build

echo "=========================================="
echo " Setup Completed Successfully!           "
echo " The application is now running.          "
echo " APP_URL: http://localhost                "
echo " DB PORT (Forwarded on host): 3309        "
echo "=========================================="
