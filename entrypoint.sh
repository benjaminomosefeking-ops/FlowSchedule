#!/bin/sh
set -e

# Limpiar cualquier caché antigua que se haya quedado pegada en la imagen
php artisan optimize:clear

# Generar las cachés con las variables de entorno reales de Render
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones (opcional, pero recomendado si usas Supabase)
php artisan migrate --force

# Iniciar el servidor (asegúrate de que este sea el comando que usa tu Dockerfile)
exec php-fpm