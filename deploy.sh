#!/usr/bin/env bash
# Actualiza El Billetazo en el servidor. Uso (dentro de la carpeta del proyecto):
#   bash deploy.sh
set -euo pipefail

cd "$(dirname "$0")"
USUARIO_WEB="${USUARIO_WEB:-apache}"   # usuario con el que corre Apache en AlmaLinux

echo "==> Descargando cambios"
ANTES=$(git rev-parse HEAD)
git pull --ff-only
DESPUES=$(git rev-parse HEAD)

cambio() { [ "$ANTES" != "$DESPUES" ] && git diff --name-only "$ANTES" "$DESPUES" | grep -qx "$1"; }

echo "==> Modo mantenimiento"
php artisan down --retry=15 || true
trap 'php artisan up' EXIT

if [ ! -d vendor ] || cambio composer.lock; then
    echo "==> Instalando dependencias de PHP"
    composer install --no-dev --optimize-autoloader --no-interaction
fi

if [ ! -d node_modules ] || cambio package-lock.json; then
    echo "==> Instalando dependencias de Node"
    rm -rf node_modules
    npm ci
fi

echo "==> Compilando estilos y scripts"
npm run build
rm -f public/hot

echo "==> Enlace de archivos públicos (imágenes de publicidad)"
[ -e public/storage ] || php artisan storage:link

echo "==> Base de datos"
php artisan migrate --force

echo "==> Comprobantes antiguos a carpeta privada"
php artisan comprobantes:privatizar

echo "==> Cachés de producción"
php artisan optimize:clear
php artisan optimize

echo "==> Permisos"
chown -R "$USUARIO_WEB":"$USUARIO_WEB" storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

echo "==> Listo: $(git log -1 --format='%h %s')"
