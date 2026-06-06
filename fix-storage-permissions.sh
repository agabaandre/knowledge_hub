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

FILES_ROOT="${HUB_FILES_ROOT:-/var/khubdata/files}"
SQL_ROOT="${HUB_SQL_BACKUP_ROOT:-/var/khubdata/backups/sql}"

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

echo "Done. Reload the site and clear compiled views if needed:"
echo "  php artisan view:clear"
