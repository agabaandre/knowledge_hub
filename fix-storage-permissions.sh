#!/usr/bin/env bash
# Reset Laravel storage/bootstrap cache ownership and permissions (macOS + Linux).
# Run from project root: ./fix-storage-permissions.sh
# You will be prompted for your sudo password when chown needs elevated access.

set -euo pipefail
cd "$(dirname "$0")"

OWNER="${SUDO_USER:-$USER}"
if [[ -z "$OWNER" || "$OWNER" == "root" ]]; then
  OWNER="$(id -un)"
fi

if [[ "$(uname -s)" == "Darwin" ]]; then
  GROUP="${KHUB_STORAGE_GROUP:-staff}"
else
  GROUP="${KHUB_STORAGE_GROUP:-www-data}"
fi

# Site ID defaults from APP_URL (domain + subfolder). Override: export HUB_SITE_ID=my-hub-id
SITE_ID="${HUB_SITE_ID:-local}"
if command -v php >/dev/null 2>&1 && [[ -f artisan ]]; then
  SITE_ID="$(php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo app(\App\Services\HubStorageService::class)->siteStorageId();" 2>/dev/null || echo "$SITE_ID")"
fi
FILES_ROOT="${HUB_FILES_ROOT:-/var/khubdata/${SITE_ID}/files}"
SQL_ROOT="${HUB_SQL_BACKUP_ROOT:-/var/khubdata/${SITE_ID}/backups/sql}"

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  public/uploads \
  "${FILES_ROOT}" \
  "${SQL_ROOT}"

echo "Using owner: ${OWNER}:${GROUP}"
sudo chown -R "${OWNER}:${GROUP}" storage bootstrap/cache public/uploads
chmod -R ug+rwX,o+rwx storage bootstrap/cache public/uploads

if [[ -d /var/khubdata ]]; then
  echo "Setting permissions on host data paths (${FILES_ROOT}, ${SQL_ROOT})…"
  sudo chown -R "${OWNER}:${GROUP}" /var/khubdata
  chmod -R ug+rwX /var/khubdata
fi

if [[ -f artisan ]] && command -v php >/dev/null 2>&1; then
  echo "Recovering hub storage (symlink + legacy path detection)…"
  php artisan hub:recover-storage || php artisan hub:link-storage || ./link-hub-storage.sh
fi

echo "Done. Reload the site and clear compiled views if needed:"
echo "  php artisan view:clear"
