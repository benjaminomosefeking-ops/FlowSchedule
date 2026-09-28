#!/bin/sh
set -e

echo "==> Esperando a la base de datos..."
# Espera hasta 30s a que la BD acepte conexiones
for i in $(seq 1 30); do
    php -r "
        try {
            \$dsn = getenv('DB_URL');
            if (!\$dsn) { exit(0); }
            \$p = parse_url(\$dsn);
            \$c = new PDO(
                \"pgsql:host={\$p['host']};port={\$p['port']};dbname=\" . ltrim(\$p['path'], '/'),
                \$p['user'],
                \$p['pass'],
                [PDO::ATTR_TIMEOUT => 3]
            );
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " && break || sleep 1
done

echo "==> Limpiando cachés previas..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "==> Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Arrancando nginx + php-fpm (supervisor)..."
envsubst '${PORT}' < /etc/nginx/sites-available/app.conf > /tmp/nginx.conf
mv /tmp/nginx.conf /etc/nginx/sites-available/app.conf
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf