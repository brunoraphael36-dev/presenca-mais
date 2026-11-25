# Use official PHP 8.3 with Apache
FROM php:8.3-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and production tools
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    curl \
    wget \
    vim \
    htop \
    logrotate \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions with optimizations
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pdo pdo_pgsql zip opcache

# Enable Apache modules for production performance and logging
RUN a2enmod rewrite headers deflate expires ssl remoteip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock ./

# SEMPRE instalar com dependências de desenvolvimento
# Isso garante que yii2-debug esteja disponível quando YII_ENV=dev
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Create necessary directories with proper permissions
RUN mkdir -p /var/www/html/runtime /var/www/html/web/assets /web/uploads && \
    chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets /web/uploads && \
    chmod -R 777 /var/www/html/runtime /var/www/html/web/assets /web/uploads

# Configure PHP for production performance and TLS
RUN echo "# Production PHP Configuration" > /usr/local/etc/php/conf.d/production.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "display_errors = Off" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "display_startup_errors = Off" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "max_file_uploads = 20" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "# Redis Session Handler" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "session.save_handler = redis" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "session.save_path = \"tcp://redis-service.#{namespace}#.svc.cluster.local:6379\"" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "# TLS/SSL Configuration - Bypass for development" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "openssl.cafile=" >> /usr/local/etc/php/conf.d/production.ini && \
    echo "curl.cainfo=" >> /usr/local/etc/php/conf.d/production.ini

# Configure OPcache for production performance
RUN echo "# OPcache Configuration" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.enable_cli = 0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption = 256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer = 16" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files = 10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq = 2" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps = 0" >> /usr/local/etc/php/conf.d/opcache.ini

# Configure Apache for high load and detailed HTTP logging
# Create custom Apache configuration for production
RUN echo "# Apache Production Configuration" > /etc/apache2/conf-available/production.conf && \
    echo "# Security headers" >> /etc/apache2/conf-available/production.conf && \
    echo "Header always set X-Content-Type-Options nosniff" >> /etc/apache2/conf-available/production.conf && \
    echo "Header always set X-Frame-Options DENY" >> /etc/apache2/conf-available/production.conf && \
    echo "Header always set X-XSS-Protection \"1; mode=block\"" >> /etc/apache2/conf-available/production.conf && \
    echo "Header always set Strict-Transport-Security \"max-age=63072000; includeSubDomains; preload\"" >> /etc/apache2/conf-available/production.conf && \
    echo "Header always set Referrer-Policy \"strict-origin-when-cross-origin\"" >> /etc/apache2/conf-available/production.conf && \
    echo "" >> /etc/apache2/conf-available/production.conf && \
    echo "# Performance settings" >> /etc/apache2/conf-available/production.conf && \
    echo "KeepAlive On" >> /etc/apache2/conf-available/production.conf && \
    echo "MaxKeepAliveRequests 100" >> /etc/apache2/conf-available/production.conf && \
    echo "KeepAliveTimeout 5" >> /etc/apache2/conf-available/production.conf && \
    echo "" >> /etc/apache2/conf-available/production.conf && \
    echo "# Compression" >> /etc/apache2/conf-available/production.conf && \
    echo "SetOutputFilter DEFLATE" >> /etc/apache2/conf-available/production.conf && \
    echo "SetEnvIfNoCase Request_URI \\.(?:gif|jpe?g|png)$ no-gzip dont-vary" >> /etc/apache2/conf-available/production.conf && \
    echo "SetEnvIfNoCase Request_URI \\.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary" >> /etc/apache2/conf-available/production.conf

# Configure detailed HTTP access logging to stdout
RUN echo "# Custom log format for detailed HTTP monitoring" > /etc/apache2/conf-available/logging.conf && \
    echo "LogFormat \"%h %l %u %t \\\"%r\\\" %>s %O \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\" %D %{X-Forwarded-For}i %{X-Real-IP}i response_time=%D bytes=%O\" combined_with_time" >> /etc/apache2/conf-available/logging.conf && \
    echo "LogFormat \"%{%Y-%m-%d %H:%M:%S}t.%{usec_frac}t [%P] %h %l %u \\\"%r\\\" %>s %O \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\" %D\" detailed" >> /etc/apache2/conf-available/logging.conf && \
    echo "" >> /etc/apache2/conf-available/logging.conf && \
    echo "# Error log configuration" >> /etc/apache2/conf-available/logging.conf && \
    echo "ErrorLogFormat \"[%{cu}t] [%-m:%-l] [pid %P:tid %T] %7F: %E: [client\ %a] %M% ,\ referer\ %{Referer}i\"" >> /etc/apache2/conf-available/logging.conf

# Enable the custom configurations
RUN a2enconf production && a2enconf logging

# Configure Apache document root to point to web directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/web
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure custom Apache virtual host with detailed logging
RUN echo "<VirtualHost *:80>" > /etc/apache2/sites-available/000-default.conf && \
    echo "    ServerAdmin webmaster@localhost" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    DocumentRoot /var/www/html/web" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    ServerName localhost" >> /etc/apache2/sites-available/000-default.conf && \
    echo "" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    # Detailed access logging to stdout" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    CustomLog /proc/1/fd/1 detailed" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    ErrorLog /proc/1/fd/2" >> /etc/apache2/sites-available/000-default.conf && \
    echo "" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    # Directory configuration" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    <Directory /var/www/html/web>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Require all granted" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Options -Indexes +FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf && \
    echo "" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        # PHP security" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        php_flag engine on" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        php_admin_value open_basedir \"/var/www/html:/tmp\"" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    </Directory>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    # Security headers" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Header always set X-Content-Type-Options nosniff" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Header always set X-Frame-Options SAMEORIGIN" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Header always set X-XSS-Protection \"1; mode=block\"" >> /etc/apache2/sites-available/000-default.conf && \
    echo "" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    # Static file caching" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    <LocationMatch \"\\.(css|js|png|jpg|jpeg|gif|ico|svg)$\">" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        ExpiresActive On" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        ExpiresDefault \"access plus 1 month\"" >> /etc/apache2/sites-available/000-default.conf && \
    echo "        Header append Cache-Control \"public\"" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    </LocationMatch>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</VirtualHost>" >> /etc/apache2/sites-available/000-default.conf

# Set Apache configuration for high load production
RUN echo "# Apache MPM Prefork configuration for high load" >> /etc/apache2/apache2.conf && \
    echo "<IfModule mpm_prefork_module>" >> /etc/apache2/apache2.conf && \
    echo "    StartServers 8" >> /etc/apache2/apache2.conf && \
    echo "    MinSpareServers 5" >> /etc/apache2/apache2.conf && \
    echo "    MaxSpareServers 20" >> /etc/apache2/apache2.conf && \
    echo "    ServerLimit 256" >> /etc/apache2/apache2.conf && \
    echo "    MaxRequestWorkers 256" >> /etc/apache2/apache2.conf && \
    echo "    MaxConnectionsPerChild 10000" >> /etc/apache2/apache2.conf && \
    echo "</IfModule>" >> /etc/apache2/apache2.conf && \
    echo "" >> /etc/apache2/apache2.conf && \
    echo "# General Apache optimizations" >> /etc/apache2/apache2.conf && \
    echo "Timeout 300" >> /etc/apache2/apache2.conf && \
    echo "KeepAlive On" >> /etc/apache2/apache2.conf && \
    echo "MaxKeepAliveRequests 100" >> /etc/apache2/apache2.conf && \
    echo "KeepAliveTimeout 5" >> /etc/apache2/apache2.conf && \
    echo "UseCanonicalName Off" >> /etc/apache2/apache2.conf && \
    echo "AccessFileName .htaccess" >> /etc/apache2/apache2.conf && \
    echo "ServerTokens Prod" >> /etc/apache2/apache2.conf && \
    echo "ServerSignature Off" >> /etc/apache2/apache2.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Create startup script for logging configuration
RUN echo "#!/bin/bash" > /usr/local/bin/start-apache.sh && \
    echo "# Configure logging for production monitoring" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Starting Apache with detailed HTTP logging...\"" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Log format: timestamp [pid] client_ip user request status bytes referer user_agent response_time\"" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Monitoring HTTP errors, response times, and traffic patterns\"" >> /usr/local/bin/start-apache.sh && \
    echo "" >> /usr/local/bin/start-apache.sh && \
    echo "# Check application setup" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Checking application configuration...\"" >> /usr/local/bin/start-apache.sh && \
    echo "ls -la /var/www/html/web/ | head -10" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Checking .env file...\"" >> /usr/local/bin/start-apache.sh && \
    echo "if [ -f /var/www/html/.env ]; then" >> /usr/local/bin/start-apache.sh && \
    echo "    echo \".env file exists\"" >> /usr/local/bin/start-apache.sh && \
    echo "    YII_ENV=\$(grep \"YII_ENV=\" /var/www/html/.env | cut -d'=' -f2)" >> /usr/local/bin/start-apache.sh && \
    echo "    echo \"Environment: \$YII_ENV\"" >> /usr/local/bin/start-apache.sh && \
    echo "    if [ \"\$YII_ENV\" = \"dev\" ]; then" >> /usr/local/bin/start-apache.sh && \
    echo "        echo \"Development environment detected - debug modules available\"" >> /usr/local/bin/start-apache.sh && \
    echo "    else" >> /usr/local/bin/start-apache.sh && \
    echo "        echo \"Production environment detected\"" >> /usr/local/bin/start-apache.sh && \
    echo "    fi" >> /usr/local/bin/start-apache.sh && \
    echo "else" >> /usr/local/bin/start-apache.sh && \
    echo "    echo \"WARNING: .env file missing! Creating basic one...\"" >> /usr/local/bin/start-apache.sh && \
    echo "    echo 'YII_ENV=prod' > /var/www/html/.env" >> /usr/local/bin/start-apache.sh && \
    echo "    echo 'YII_DEBUG=false' >> /var/www/html/.env" >> /usr/local/bin/start-apache.sh && \
    echo "fi" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Checking runtime directory...\"" >> /usr/local/bin/start-apache.sh && \
    echo "ls -la /var/www/html/runtime/ || echo \"Runtime directory issue\"" >> /usr/local/bin/start-apache.sh && \
    echo "echo \"Environment variables:\"" >> /usr/local/bin/start-apache.sh && \
    echo "head -5 /var/www/html/.env || echo \"Cannot read .env file\"" >> /usr/local/bin/start-apache.sh && \
    echo "" >> /usr/local/bin/start-apache.sh && \
    echo "# Start Apache in foreground" >> /usr/local/bin/start-apache.sh && \
    echo "exec apache2-foreground" >> /usr/local/bin/start-apache.sh && \
    chmod +x /usr/local/bin/start-apache.sh

# Add a health check endpoint
RUN echo '<?php' > /var/www/html/web/health.php && \
    echo 'header("Content-Type: application/json");' >> /var/www/html/web/health.php && \
    echo 'try {' >> /var/www/html/web/health.php && \
    echo '    echo json_encode([' >> /var/www/html/web/health.php && \
    echo '        "status" => "healthy",' >> /var/www/html/web/health.php && \
    echo '        "timestamp" => date("c"),' >> /var/www/html/web/health.php && \
    echo '        "php_version" => phpversion(),' >> /var/www/html/web/health.php && \
    echo '        "memory_usage" => memory_get_usage(true),' >> /var/www/html/web/health.php && \
    echo '        "opcache_enabled" => function_exists("opcache_get_status") ? opcache_get_status() !== false : false' >> /var/www/html/web/health.php && \
    echo '    ]);' >> /var/www/html/web/health.php && \
    echo '} catch (Exception $e) {' >> /var/www/html/web/health.php && \
    echo '    http_response_code(500);' >> /var/www/html/web/health.php && \
    echo '    echo json_encode(["status" => "error", "message" => $e->getMessage()]);' >> /var/www/html/web/health.php && \
    echo '}' >> /var/www/html/web/health.php

# Add a simple test endpoint for debugging
RUN echo '<?php echo "PHP is working! Version: " . phpversion(); ?>' > /var/www/html/web/test.php

# Enhanced healthcheck with detailed endpoint
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/test.php || exit 1

# Expose port 80
EXPOSE 80

# Start Apache with detailed logging
CMD ["/usr/local/bin/start-apache.sh"]