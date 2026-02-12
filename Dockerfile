FROM dunglas/frankenphp:latest-php8.4

# Install mysqli extension
RUN install-php-extensions mysqli pdo_mysql

# Copy application files
COPY . /app

# Set working directory
WORKDIR /app

# Expose port
EXPOSE 8080

# Start FrankenPHP
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
