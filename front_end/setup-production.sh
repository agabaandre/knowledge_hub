#!/usr/bin/env bash
#
# Knowledge Hub Next.js frontend — production deploy (beside Laravel at /knowledge_hub/front_end/).
#
# Same idea as staff-portal/setup-production.sh: build static files Apache can serve
# without keeping Node running.
#
#   cd front_end
#   ./setup-production.sh
#
# Re-deploy after git pull:
#   ./setup-production.sh
#
# Options:
#   --skip-build   Skip npm install / npm run build (publish existing out/)
#   -h, --help     Show help
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

SKIP_BUILD=0

usage() {
    sed -n '2,18p' "$0" | sed 's/^# \{0,1\}//'
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --skip-build) SKIP_BUILD=1 ;;
        -h|--help) usage; exit 0 ;;
        *) echo "Unknown option: $1" >&2; usage >&2; exit 1 ;;
    esac
    shift
done

log() { printf '==> %s\n' "$*"; }
warn() { printf 'warning: %s\n' "$*" >&2; }
die() { printf 'error: %s\n' "$*" >&2; exit 1; }

command -v npm >/dev/null 2>&1 || die "npm not found"
command -v curl >/dev/null 2>&1 || die "curl not found"

export NEXT_PUBLIC_BASE_PATH="${NEXT_PUBLIC_BASE_PATH:-/knowledge_hub/front_end}"
SMOKE_URL="${KHUB_FRONT_END_URL:-http://localhost/knowledge_hub/front_end/}"

if [[ "$SKIP_BUILD" -eq 0 ]]; then
    log "Installing frontend dependencies"
    mkdir -p "$ROOT/.npm-cache"
    # Build needs typescript / eslint-config-next (devDependencies).
    # NODE_ENV=production would skip them and break `npm run build`.
    export NODE_ENV=development
    if [[ -f package-lock.json ]]; then
        if ! npm ci --include=dev --cache "$ROOT/.npm-cache" --legacy-peer-deps; then
            warn "npm ci failed (stale lock?) — running npm install --include=dev"
            npm install --include=dev --cache "$ROOT/.npm-cache" --legacy-peer-deps
        fi
    else
        npm install --include=dev --cache "$ROOT/.npm-cache" --legacy-peer-deps
    fi
    log "Building static export (NEXT_PUBLIC_BASE_PATH=${NEXT_PUBLIC_BASE_PATH})"
    NODE_ENV=production npm run build
    [[ -f "$ROOT/out/index.html" ]] || die "Frontend build failed — missing out/index.html"
else
    log "Skipping frontend build (--skip-build)"
    [[ -f "$ROOT/out/index.html" ]] || die "Missing out/index.html — run without --skip-build"
fi

chmod +x "$ROOT/scripts/publish-static.sh"
"$ROOT/scripts/publish-static.sh" "$ROOT/out"

log "Smoke test ${SMOKE_URL}"
code="$(curl -sS -o /tmp/khub-front-end-smoke.html -w '%{http_code}' -L --max-time 20 "$SMOKE_URL" || true)"
if [[ "$code" == "200" ]]; then
    printf '    frontend OK: %s\n' "$SMOKE_URL"
else
    warn "frontend returned HTTP ${code:-000} for ${SMOKE_URL}"
    warn "If Apache still ProxyPass-es this path, reload httpd after pulling apache-front-end.conf"
fi

echo ""
echo "Knowledge Hub frontend production setup complete."
echo "  URL: ${SMOKE_URL}"
echo "  Files: ${ROOT}/public-spa/"
echo ""
echo "Apache serves published files via front-end-proxy.php — Node does not need to keep running."
echo "To use next dev again:  rm -rf public-spa && npm run dev"
echo ""
echo "Re-deploy after git pull:  ./setup-production.sh"
echo "Skip the Next build:       ./setup-production.sh --skip-build"
