#!/usr/bin/env bash
set -euo pipefail

#
# Knowledge Hub – Docker production setup (single command)
#
# Usage:
#   chmod +x set-production.sh && ./set-production.sh [--rebuild]
#
# This script:
#   1. Prompts for super-admin credentials and site settings
#   2. Generates a production .env file
#   3. Builds and starts all containers
#   4. Runs migrations and installs the application
#

REBUILD=false

for arg in "$@"; do
    case "$arg" in
        --rebuild)
            REBUILD=true
            ;;
        -h|--help)
            echo "Usage: ./set-production.sh [--rebuild]"
            echo "  --rebuild   Force full image rebuild (pull latest base images, no cache)."
            exit 0
            ;;
        *)
            echo "Unknown option: $arg" >&2
            echo "Usage: ./set-production.sh [--rebuild]" >&2
            exit 1
            ;;
    esac
done

BOLD="\033[1m"
GREEN="\033[0;32m"
CYAN="\033[0;36m"
RED="\033[0;31m"
RESET="\033[0m"

echo -e "${CYAN}${BOLD}"
echo "╔══════════════════════════════════════════════════════╗"
echo "║     Knowledge Hub – Production Setup                ║"
echo "╚══════════════════════════════════════════════════════╝"
echo -e "${RESET}"

# ─── Collect super-admin credentials ──────────────────────────────────────────

read -rp "$(echo -e "${BOLD}Super Admin Email:${RESET} ")" ADMIN_EMAIL

if [[ ! "$ADMIN_EMAIL" =~ ^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$ ]]; then
    echo -e "${RED}Error: Invalid email address.${RESET}" >&2
    exit 1
fi

while true; do
    read -srp "$(echo -e "${BOLD}Super Admin Password (min 8 chars):${RESET} ")" ADMIN_PASSWORD
    echo
    if [ ${#ADMIN_PASSWORD} -lt 8 ]; then
        echo -e "${RED}Password must be at least 8 characters. Try again.${RESET}"
        continue
    fi
    read -srp "$(echo -e "${BOLD}Confirm password:${RESET} ")" ADMIN_PASSWORD_CONFIRM
    echo
    if [ "$ADMIN_PASSWORD" != "$ADMIN_PASSWORD_CONFIRM" ]; then
        echo -e "${RED}Passwords do not match. Try again.${RESET}"
        continue
    fi
    break
done

read -rp "$(echo -e "${BOLD}Admin First Name [Admin]:${RESET} ")" ADMIN_FIRST_NAME
ADMIN_FIRST_NAME="${ADMIN_FIRST_NAME:-Admin}"

read -rp "$(echo -e "${BOLD}Admin Last Name [User]:${RESET} ")" ADMIN_LAST_NAME
ADMIN_LAST_NAME="${ADMIN_LAST_NAME:-User}"

# ─── Collect site settings ────────────────────────────────────────────────────

read -rp "$(echo -e "${BOLD}Site Name [Knowledge Hub]:${RESET} ")" SITE_NAME
SITE_NAME="${SITE_NAME:-Knowledge Hub}"

read -rp "$(echo -e "${BOLD}Domain / APP_URL [http://localhost:8081]:${RESET} ")" APP_URL
APP_URL="${APP_URL:-http://localhost:8081}"

read -rp "$(echo -e "${BOLD}HTTP Port [8081]:${RESET} ")" APP_PORT
APP_PORT="${APP_PORT:-8081}"

prompt_bool() {
    local label="$1"
    local default_value="$2"
    local input
    while true; do
        read -rp "$(echo -e "${BOLD}${label} [${default_value}]:${RESET} ")" input
        input="${input:-$default_value}"
        input="$(echo "$input" | tr '[:upper:]' '[:lower:]')"
        case "$input" in
            true|false)
                echo "$input"
                return 0
                ;;
            *)
                echo -e "${RED}Please enter true or false.${RESET}"
                ;;
        esac
    done
}

STATES_ENABLED="$(prompt_bool "STATES_ENABLED" "false")"
ADMIN_UNITS_ENABLED="$(prompt_bool "ADMIN_UNITS_ENABLED" "true")"

# ─── Generate secure passwords ───────────────────────────────────────────────

generate_password() {
    openssl rand -base64 24 | tr -d '=/+' | head -c 32
}

DB_PASSWORD="$(generate_password)"
MEILISEARCH_KEY="$(generate_password)"
APP_KEY="$(openssl rand -base64 32)"

# ─── Write .env ───────────────────────────────────────────────────────────────

ENV_FILE="$(dirname "$0")/.env"

if [ -f "$ENV_FILE" ]; then
    cp "$ENV_FILE" "${ENV_FILE}.bak.$(date +%Y%m%d%H%M%S)"
    echo -e "${CYAN}Existing .env backed up.${RESET}"
fi

cat > "$ENV_FILE" <<EOF
APP_NAME="${SITE_NAME}"
APP_ENV=production
APP_KEY=base64:${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL}
APP_INSTALLED=false
APP_PORT=${APP_PORT}
RUN_MIGRATIONS=false
WAIT_FOR_MEILISEARCH=false
GENERATE_APP_KEY_ON_BOOT=false
STATES_ENABLED=${STATES_ENABLED}
ADMIN_UNITS_ENABLED=${ADMIN_UNITS_ENABLED}

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=knowledge_hub
DB_USERNAME=root
DB_PASSWORD=${DB_PASSWORD}

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

SCOUT_DRIVER=meilisearch
SCOUT_QUEUE=true
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=${MEILISEARCH_KEY}

MAIL_MAILER=log
MAIL_FROM_ADDRESS=${ADMIN_EMAIL}
MAIL_FROM_NAME="${SITE_NAME}"
EOF

echo -e "${GREEN}✓ .env written${RESET}"

# ─── Build and start containers ───────────────────────────────────────────────

echo -e "\n${CYAN}Building and starting Docker containers...${RESET}"
docker compose down --remove-orphans 2>/dev/null || true
if [ "$REBUILD" = "true" ]; then
    echo -e "${CYAN}Rebuild mode enabled: pulling latest base images and building without cache...${RESET}"
    docker compose build --pull --no-cache
else
    docker compose build
fi
docker compose up -d

echo -e "${CYAN}Waiting for services to become healthy...${RESET}"

wait_for_container() {
    local container="$1"
    local max_attempts="${2:-60}"
    local i=0
    while [ $i -lt $max_attempts ]; do
        status="$(docker inspect --format='{{.State.Health.Status}}' "$container" 2>/dev/null || echo "starting")"
        if [ "$status" = "healthy" ]; then
            return 0
        fi
        sleep 2
        i=$((i + 1))
    done
    echo -e "${RED}Error: $container did not become healthy within $((max_attempts * 2))s${RESET}" >&2
    return 1
}

wait_for_container "khub_mysql" 30
echo -e "${GREEN}✓ MySQL ready${RESET}"

wait_for_container "khub_app" 60
echo -e "${GREEN}✓ Application ready${RESET}"

# ─── Run installer ────────────────────────────────────────────────────────────

echo -e "\n${CYAN}Running Knowledge Hub installer...${RESET}"
docker compose exec -T app php artisan khub:install \
    --email="$ADMIN_EMAIL" \
    --password="$ADMIN_PASSWORD" \
    --first-name="$ADMIN_FIRST_NAME" \
    --last-name="$ADMIN_LAST_NAME" \
    --site-name="$SITE_NAME" \
    --mail-driver=log \
    --storage-driver=internal

docker compose exec -T app php artisan khub:mark-installed 2>/dev/null || true
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

# ─── Done ─────────────────────────────────────────────────────────────────────

echo -e "\n${GREEN}${BOLD}╔══════════════════════════════════════════════════════╗${RESET}"
echo -e "${GREEN}${BOLD}║  Installation complete!                              ║${RESET}"
echo -e "${GREEN}${BOLD}╠══════════════════════════════════════════════════════╣${RESET}"
echo -e "${GREEN}${BOLD}║${RESET}  URL:   ${APP_URL}"
echo -e "${GREEN}${BOLD}║${RESET}  Admin: ${ADMIN_EMAIL}"
echo -e "${GREEN}${BOLD}║${RESET}  Pass:  (as entered)"
echo -e "${GREEN}${BOLD}╚══════════════════════════════════════════════════════╝${RESET}"
echo
echo -e "  To view logs:     ${CYAN}docker compose logs -f app${RESET}"
echo -e "  To stop:          ${CYAN}docker compose down${RESET}"
echo -e "  To restart:       ${CYAN}docker compose up -d${RESET}"
echo
