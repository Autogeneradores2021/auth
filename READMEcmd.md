# Limpiar caché de rutas
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Ver todas las rutas registradas
php artisan route:list

# Filtrar solo rutas de API
php artisan route:list --path=api
