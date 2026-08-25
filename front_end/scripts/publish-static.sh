#!/usr/bin/env bash
# Publish Next.js static export to public-spa/ so Apache can serve it without Node.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT="${1:-$ROOT/out}"

if [[ ! -f "$OUT/index.html" ]]; then
  echo "error: missing $OUT/index.html — run: cd front_end && npm run build" >&2
  exit 1
fi

echo "==> Removing old published frontend files"
rm -rf "$ROOT/public-spa"

echo "==> Copying $OUT → public-spa/"
mkdir -p "$ROOT/public-spa"
cp -a "$OUT/." "$ROOT/public-spa/"

if [[ -d "$ROOT/public-spa/_next" ]]; then
  cat > "$ROOT/public-spa/_next/.htaccess" <<'EOF'
# Serve Next hashed assets as static files only.
<IfModule mod_rewrite.c>
    RewriteEngine Off
</IfModule>
EOF
fi

if [[ -d "$ROOT/public-spa/assets" ]]; then
  cat > "$ROOT/public-spa/assets/.htaccess" <<'EOF'
# Serve theme assets as static files only.
<IfModule mod_rewrite.c>
    RewriteEngine Off
</IfModule>
EOF
fi

chmod -R a+rX "$ROOT/public-spa"

html_count="$(find "$ROOT/public-spa" -type f -name '*.html' | wc -l | tr -d ' ')"
echo "    index.html → $ROOT/public-spa/index.html"
echo "    html pages → $html_count"
echo "==> Done. Hard-refresh the browser (Ctrl+Shift+R)."
