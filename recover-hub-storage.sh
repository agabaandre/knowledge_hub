#!/usr/bin/env bash
# Production-safe storage recovery (same flow as local fix).
# Run from project root on the server after deploying the latest code.
#
#   ./recover-hub-storage.sh              # diagnose + fix public/storage symlink
#   ./recover-hub-storage.sh --sync-legacy  # also merge storage/uploads → app/public
#   ./recover-hub-storage.sh --migrate-host # sync + copy to /var/khubdata/{site-id}/files
#
# Cross-platform:
#   php artisan hub:recover-storage
#   php artisan hub:recover-storage --sync-legacy
#   php artisan hub:recover-storage --sync-legacy --migrate-host

set -euo pipefail
cd "$(dirname "$0")"

if [[ ! -f artisan ]]; then
  echo "error: run from the Knowledge Hub project root." >&2
  exit 1
fi

ARGS=()
for arg in "$@"; do
  case "$arg" in
    --sync-legacy) ARGS+=(--sync-legacy) ;;
    --migrate-host) ARGS+=(--migrate-host) ;;
    -h|--help)
      sed -n '2,12p' "$0"
      exit 0
      ;;
    *)
      echo "unknown option: $arg (use --sync-legacy and/or --migrate-host)" >&2
      exit 1
      ;;
  esac
done

php artisan hub:recover-storage "${ARGS[@]}"
php artisan config:clear
php artisan view:clear

echo "Done. Verify a publication file URL in the browser."
