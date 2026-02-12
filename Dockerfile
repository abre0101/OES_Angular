FROM php:8.2-fpm

# Install mysqli and other required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install nginx and gettext for envsubst
RUN apt-get update && apt-get install -y nginx gettext-base && rm -rf /var/lib/apt/lists/*

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create start script
COPY <<'EOF' /start.sh
#!/bin/bash
set -e

PORT=${PORT:-8080}
echo "Configuring Nginx on port $PORT..."

cat > /etc/nginx/sites-available/default <<NGINX
server {
    listen $PORT;
    server_name _;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINX

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
EOF

RUN chmod +x /start.sh

# Expose port
EXPOSE 8080

CMD ["/start.sh"]
