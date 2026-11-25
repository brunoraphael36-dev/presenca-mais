#!/bin/bash

echo "=== DEBUGGING SCRIPT ==="
echo "Date: $(date)"
echo "User: $(whoami)"
echo "Working directory: $(pwd)"

echo ""
echo "=== PHP VERSION ==="
php -v

echo ""
echo "=== ENVIRONMENT VARIABLES ==="
env | grep -E "(YII_|PHP_)" | sort

echo ""
echo "=== .ENV FILE ==="
if [ -f /var/www/html/.env ]; then
    echo "--- .env file exists ---"
    head -10 /var/www/html/.env
else
    echo "ERROR: .env file not found!"
fi

echo ""
echo "=== DIRECTORY STRUCTURE ==="
ls -la /var/www/html/

echo ""
echo "=== WEB DIRECTORY ==="
ls -la /var/www/html/web/

echo ""
echo "=== COMPOSER AUTOLOAD ==="
if [ -f /var/www/html/vendor/autoload.php ]; then
    echo "Composer autoload exists"
else
    echo "ERROR: Composer autoload not found!"
fi

echo ""
echo "=== TEST PHP SYNTAX ==="
php -l /var/www/html/web/index.php

echo ""
echo "=== TEST PHP EXECUTION ==="
cd /var/www/html
php -r "
try {
    require('vendor/autoload.php');
    echo 'Autoload: OK\n';
    
    if (file_exists('.env')) {
        (new \Symfony\Component\Dotenv\Dotenv())->load('.env');
        echo 'Dotenv: OK\n';
        echo 'YII_ENV: ' . getenv('YII_ENV') . '\n';
        echo 'YII_DEBUG: ' . getenv('YII_DEBUG') . '\n';
    } else {
        echo 'ERROR: .env file not found\n';
    }
    
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . '\n';
    echo 'File: ' . \$e->getFile() . ':' . \$e->getLine() . '\n';
}
"

echo ""
echo "=== APACHE CONFIG TEST ==="
apache2ctl configtest

echo ""
echo "=== START APACHE FOR TESTING ==="
echo "Starting Apache in background..."
apache2-foreground &
APACHE_PID=$!

sleep 3

echo ""
echo "=== TEST HTTP REQUEST ==="
curl -v http://localhost/ 2>&1 | head -20

echo ""
echo "=== TEST PHP INFO ==="
curl -s http://localhost/test.php

echo ""
echo "=== APACHE ERROR LOG ==="
if [ -f /var/log/apache2/error.log ]; then
    tail -20 /var/log/apache2/error.log
else
    echo "No Apache error log found"
fi

echo ""
echo "=== PHP ERROR LOG ==="
if [ -f /var/log/php_errors.log ]; then
    tail -20 /var/log/php_errors.log
else
    echo "No PHP error log found"
fi

echo ""
echo "=== KILL APACHE ==="
kill $APACHE_PID 2>/dev/null

echo ""
echo "=== DEBUG COMPLETE ==="
