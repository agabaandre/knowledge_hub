#!/usr/bin/env bash
# Link public/storage to the hub files root (macOS and Linux).
# On Windows use link-hub-storage.bat or link-hub-storage.ps1 instead.
#
# Usage (from project root):
#   ./link-hub-storage.sh
#
# Cross-platform equivalent:
#   php artisan hub:link-storage

set -euo pipefail
cd "$(dirname "$0")"

if [[ "$(uname -s)" == MINGW* ]] || [[ "$(uname -s)" == MSYS* ]] || [[ "$(uname -s)" == CYGWIN* ]]; then
  echo "On Windows, run: link-hub-storage.bat or php artisan hub:link-storage" >&2
  exit 1
fi

if [[ ! -f artisan ]]; then
  echo "error: run this script from the Knowledge Hub project root (artisan not found)." >&2
  exit 1
fi

if ! command -v php >/dev/null 2>&1; then
  echo "error: php is not on PATH." >&2
  exit 1
fi

if [[ ! -f vendor/autoload.php ]]; then
  echo "error: run composer install first (vendor/autoload.php missing)." >&2
  exit 1
fi

php artisan hub:link-storage
