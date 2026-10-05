#!/bin/sh
set -e

# Substitute $PORT into Nginx config (Render assigns dynamic PORT, default to 8080)
PORT_NUM=${PORT:-8080}
sed -i "s/LISTEN_PORT/$PORT_NUM/g" /etc/nginx/http.d/default.conf

echo "Starting application on port $PORT_NUM..."

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache Laravel configurations for optimal production performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Supervisor (which starts both PHP-FPM and Nginx)
exec /usr/bin/supervisord -c /etc/supervisord.conf
