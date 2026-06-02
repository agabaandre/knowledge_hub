#!/usr/bin/env bash
#
# Fix Laravel storage and bootstrap/cache permissions for the Knowledge Hub.
#
# Usage (from anywhere):
#   ./scripts/fix-storage-permissions.sh --no-chown
#   sudo ./scripts/fix-storage-permissions.sh                    # auto-detect web user
#   sudo ./scripts/fix-storage-permissions.sh --web-user=www-data   # Linux (Apache/PHP-FPM)
#   sudo ./scripts/fix-storage-permissions.sh --web-user=_www --web-group=_www   # macOS
#
# Options:
#   --web-user=USER   Web server user (default: auto-detect; macOS uses _www, Linux www-data)
#   --web-group=GRP   Web server group (default: primary group of web user)
#   --no-chown        Only chmod; do not change ownership (safe for local dev without sudo)
#   --dry-run         Print commands without running them
#   -h, --help        Show help

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

WEB_USER=""
WEB_GROUP=""
DO_CHOWN=1
DRY_RUN=0

usage() {
    sed -n '2,12p' "$0" | sed 's/^# \{0,1\}//'
    exit "${1:-0}"
}

log() {
    printf '%s\n' "$*"
}

run() {
    if [[ "$DRY_RUN" -eq 1 ]]; then
        printf '[dry-run] %s\n' "$*"
    else
        "$@"
    fi
}

group_exists() {
    local group="$1"
    if grep -q "^${group}:" /etc/group 2>/dev/null; then
        return 0
    fi
    if [[ "$(uname -s)" == "Darwin" ]] && dscl . -read "/Groups/${group}" &>/dev/null; then
        return 0
    fi
    return 1
}

detect_web_user() {
    if [[ -n "$WEB_USER" ]]; then
        return 0
    fi
    local candidates=()
    if [[ "$(uname -s)" == "Darwin" ]]; then
        candidates=(_www www apache nobody)
    else
        candidates=(www-data nginx apache http _www)
    fi
    for candidate in "${candidates[@]}"; do
        if id "$candidate" &>/dev/null; then
            WEB_USER="$candidate"
            return 0
        fi
    done
    WEB_USER="$(whoami)"
    log "Note: No common web-server user found; using current user: ${WEB_USER}"
}

detect_web_group() {
    if [[ -n "$WEB_GROUP" ]]; then
        return 0
    fi
    if id "$WEB_USER" &>/dev/null; then
        WEB_GROUP="$(id -gn "$WEB_USER" 2>/dev/null || echo "$WEB_USER")"
    else
        WEB_GROUP="$WEB_USER"
    fi
}

validate_web_identity() {
    if ! id "$WEB_USER" &>/dev/null; then
        log "Error: user '${WEB_USER}' does not exist on this system."
        if [[ "$(uname -s)" == "Darwin" ]]; then
            log "On macOS, Apache/PHP usually run as _www. Try:"
            log "  sudo $0 --web-user=_www --web-group=_www"
        else
            log "On Linux, try www-data or nginx. Example:"
            log "  sudo $0 --web-user=www-data --web-group=www-data"
        fi
        exit 1
    fi

    if ! group_exists "$WEB_GROUP"; then
        log "Error: group '${WEB_GROUP}' does not exist on this system."
        if [[ "$(uname -s)" == "Darwin" ]]; then
            log "Use the _www group with the _www user:"
            log "  sudo $0 --web-user=_www --web-group=_www"
        else
            log "Example:"
            log "  sudo $0 --web-user=www-data --web-group=www-data"
        fi
        exit 1
    fi
}

ensure_directories() {
    local dirs=(
        "${APP_ROOT}/bootstrap/cache"
        "${APP_ROOT}/storage"
        "${APP_ROOT}/storage/app"
        "${APP_ROOT}/storage/app/public"
        "${APP_ROOT}/storage/framework"
        "${APP_ROOT}/storage/framework/cache"
        "${APP_ROOT}/storage/framework/cache/data"
        "${APP_ROOT}/storage/framework/sessions"
        "${APP_ROOT}/storage/framework/views"
        "${APP_ROOT}/storage/framework/testing"
        "${APP_ROOT}/storage/logs"
    )
    for dir in "${dirs[@]}"; do
        if [[ ! -d "$dir" ]]; then
            log "Creating ${dir}"
            run mkdir -p "$dir"
        fi
    done
}

ensure_storage_link() {
    local link="${APP_ROOT}/public/storage"
    local target="${APP_ROOT}/storage/app/public"
    if [[ ! -e "$link" ]]; then
        log "Creating public/storage → storage/app/public symlink"
        run ln -sfn "$target" "$link"
    fi
}

fix_permissions() {
    local paths=(
        "${APP_ROOT}/storage"
        "${APP_ROOT}/bootstrap/cache"
    )

    detect_web_user
    detect_web_group
    validate_web_identity

    log "Application root: ${APP_ROOT}"

    if [[ "$DO_CHOWN" -eq 1 ]] && [[ "$(id -u)" -eq 0 ]]; then
        log "Setting owner to ${WEB_USER}:${WEB_GROUP}"
        run chown -R "${WEB_USER}:${WEB_GROUP}" "${paths[@]}"
    elif [[ "$DO_CHOWN" -eq 1 ]] && [[ "$WEB_USER" != "$(whoami)" ]]; then
        log "Skipping chown (not root). Run with sudo or use --no-chown for chmod-only."
        log "  Example: sudo $0 --web-user=${WEB_USER}"
    fi

    log "Setting directory permissions (ug+rwx)"
    run find "${paths[@]}" -type d -exec chmod 775 {} +

    log "Setting file permissions (ug+rw)"
    run find "${paths[@]}" -type f -exec chmod 664 {} +

    # Directories remain group-writable for new files when setgid is supported
    run find "${paths[@]}" -type d -exec chmod g+s {} + 2>/dev/null || true
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --web-user=*)
            WEB_USER="${1#*=}"
            ;;
        --web-group=*)
            WEB_GROUP="${1#*=}"
            ;;
        --no-chown)
            DO_CHOWN=0
            ;;
        --dry-run)
            DRY_RUN=1
            ;;
        -h|--help)
            usage 0
            ;;
        *)
            log "Unknown option: $1"
            usage 1
            ;;
    esac
    shift
done

cd "$APP_ROOT"

ensure_directories
ensure_storage_link
fix_permissions

log "Done. If uploads still fail, confirm the PHP/Apache user matches --web-user and SELinux/AppArmor is not blocking writes."
