#!/usr/bin/env bash
# Move a Knowledge Hub database and its uploads from a host or VM install into the Docker stack.
#
#   Source (pick one):
#     SRC_KIND=mysql  SRC_HOST=10.0.0.5 SRC_PORT=3306 SRC_DB=knowledge_hub SRC_USER=root SRC_PASSWORD=...
#     SRC_KIND=pg     SRC_HOST=10.0.0.5 SRC_PORT=5432 SRC_DB=khub        SRC_USER=postgres SRC_PASSWORD=...
#     SRC_CONTAINER=khub_mysql   (dump from a container instead of a host client; MySQL only)
#   Uploads:  SRC_DATA=/var/khubdata  (the host folder that holds {site-id}/files)
#   Target:   the docker compose stack in this folder (mysql or postgres service)
#
#   scripts/move-to-docker.sh --dry-run          # show the plan only
#   scripts/move-to-docker.sh                    # dump → restore → copy uploads → migrate → verify
#
# Re-running is safe: the restore drops and recreates the target database.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="${ENV_FILE:-$ROOT/.env}"
[ -f "$ENV_FILE" ] && { set -a; . "$ENV_FILE"; set +a; }

DRY_RUN=0; [ "${1:-}" = "--dry-run" ] && DRY_RUN=1
WORK="${WORK_DIR:-$(mktemp -d)}"
mkdir -p "$WORK"
SRC_KIND="${SRC_KIND:?SRC_KIND must be mysql or pg}"
SRC_DATA="${SRC_DATA:-}"
DEST_DATA="$ROOT/${KHUBDATA_DIR:-khubdata}"
COMPOSE=(docker compose -f "$ROOT/docker-compose.yml")
COMPOSE_CMD="$(printf '%q ' "${COMPOSE[@]}")"  # same command, quoted for bash -c strings

log()  { printf '\033[1;32m[move]\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m[warn]\033[0m %s\n' "$*"; }
die()  { printf '\033[1;31m[fail]\033[0m %s\n' "$*" >&2; exit 1; }
run()  { if [ "$DRY_RUN" = 1 ]; then echo "  would run: $*"; else "$@"; fi; }

# Database client tools: use the local binary when installed, otherwise run it
# from the official image, so a bare host or VM needs no clients installed.
# Inside a container 127.0.0.1 means the container itself, so point it at the host.
docker_host() { case "$1" in 127.0.0.1|localhost) echo host.docker.internal ;; *) echo "$1" ;; esac; }
client() { # client <tool> <image>
  if command -v "$1" >/dev/null 2>&1; then echo "$1"
  else echo "docker run --rm -i -e MYSQL_PWD -e PGPASSWORD --add-host=host.docker.internal:host-gateway $2 $1"; fi
}
src_host() { # host as seen by the dump tool
  local host="${SRC_HOST:-127.0.0.1}"
  command -v "$1" >/dev/null 2>&1 && echo "$host" || docker_host "$host"
}

# ---- 1. dump the source ----------------------------------------------------
dump_source() {
  case "$SRC_KIND" in
    mysql)
      log "dumping MySQL ${SRC_DB} (${SRC_CONTAINER:-host ${SRC_HOST:-127.0.0.1}})"
      local flags=(--single-transaction --routines --triggers --events --no-tablespaces --default-character-set=utf8mb4)
      if [ -n "${SRC_CONTAINER:-}" ]; then
        run bash -c "docker exec -e MYSQL_PWD='$SRC_PASSWORD' '$SRC_CONTAINER' mysqldump -u'${SRC_USER:-root}' ${flags[*]} '$SRC_DB' > '$WORK/source.sql'"
      else
        run bash -c "export MYSQL_PWD='$SRC_PASSWORD'; $(client mysqldump mysql:8.4) -h'$(src_host mysqldump)' -P'${SRC_PORT:-3306}' -u'${SRC_USER:-root}' ${flags[*]} '$SRC_DB' > '$WORK/source.sql'"
      fi ;;
    pg)
      log "dumping PostgreSQL ${SRC_DB} from ${SRC_HOST:-127.0.0.1}"
      run bash -c "export PGPASSWORD='$SRC_PASSWORD'; $(client pg_dump postgres:16-alpine) -h '$(src_host pg_dump)' -p '${SRC_PORT:-5432}' -U '${SRC_USER:-postgres}' -Fc --no-owner --no-privileges '$SRC_DB' > '$WORK/source.dump'" ;;
    *) die "SRC_KIND must be mysql or pg" ;;
  esac
}

# ---- 2. restore into the docker database ------------------------------------
restore_target() {
  case "$SRC_KIND" in
    mysql)
      [ "$DRY_RUN" = 1 ] || "${COMPOSE[@]}" up -d mysql >/dev/null
      log "restoring into docker MySQL (database knowledge_hub)"
      run bash -c "$COMPOSE_CMD exec -T mysql sh -c 'mysql -uroot -p\"\$MYSQL_ROOT_PASSWORD\" -e \"DROP DATABASE IF EXISTS knowledge_hub; CREATE DATABASE knowledge_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\" && mysql -uroot -p\"\$MYSQL_ROOT_PASSWORD\" knowledge_hub' < '$WORK/source.sql'" ;;
    pg)
      [ "$DRY_RUN" = 1 ] || "${COMPOSE[@]}" up -d postgres >/dev/null
      log "restoring into docker PostgreSQL (database khub)"
      run bash -c "$COMPOSE_CMD exec -T postgres sh -c 'dropdb -U postgres --if-exists khub && createdb -U postgres khub'"
      run bash -c "$COMPOSE_CMD exec -T postgres pg_restore -U postgres -d khub --no-owner --no-privileges < '$WORK/source.dump'" ;;
  esac
}

# ---- 3. copy uploads (rsync keeps existing files, never deletes) -----------
copy_uploads() {
  if [ -z "$SRC_DATA" ]; then warn "SRC_DATA not set: uploads not copied"; return; fi
  [ -d "$SRC_DATA" ] || die "SRC_DATA $SRC_DATA does not exist"
  log "copying uploads $SRC_DATA → $DEST_DATA (no deletes)"
  run mkdir -p "$DEST_DATA"
  # Plain -a: macOS ships openrsync, which lacks GNU-only flags like --info=progress2.
  run rsync -a "$SRC_DATA/" "$DEST_DATA/"
}

# ---- 4. adopt the schema (Node migrations skip tables that already exist) --
migrate_target() {
  log "running Node migrations on the restored database"
  run "${COMPOSE[@]}" run --rm --no-deps -e DB_CLIENT=$([ "$SRC_KIND" = mysql ] && echo mysql2 || echo pg) \
      -e DB_HOST=$([ "$SRC_KIND" = mysql ] && echo mysql || echo postgres) \
      -e DB_PORT=$([ "$SRC_KIND" = mysql ] && echo 3306 || echo 5432) \
      -e DB_DATABASE=$([ "$SRC_KIND" = mysql ] && echo knowledge_hub || echo khub) \
      -e DB_USERNAME=$([ "$SRC_KIND" = mysql ] && echo root || echo postgres) \
      -e DB_PASSWORD="$([ "$SRC_KIND" = mysql ] && echo "${MYSQL_ROOT_PASSWORD:-}" || echo "${POSTGRES_PASSWORD:-}")" \
      app-$([ "$SRC_KIND" = mysql ] && echo mysql || echo pg) node backend/scripts/migrate.js
}

# ---- 5. verify row counts source vs target ----------------------------------
verify() {
  [ "$DRY_RUN" = 1 ] && { echo "  would verify row counts"; return; }
  log "verifying row counts"
  local src_client; src_client=$([ "$SRC_KIND" = mysql ] && echo mysql2 || echo pg)
  SOURCE_DB_CLIENT="$src_client" SOURCE_DB_HOST="${SRC_VERIFY_HOST:-127.0.0.1}" SOURCE_DB_PORT="${SRC_VERIFY_PORT:-${SRC_PORT:-3306}}" \
  SOURCE_DB_DATABASE="$SRC_DB" SOURCE_DB_USERNAME="${SRC_USER:-root}" SOURCE_DB_PASSWORD="$SRC_PASSWORD" \
  DB_CLIENT="$src_client" DB_HOST="${TARGET_HOST:-127.0.0.1}" \
  DB_PORT="$([ "$SRC_KIND" = mysql ] && echo "${MYSQL_PORT_FORWARD:-3320}" || echo "${PG_PORT_FORWARD:-5440}")" \
  DB_DATABASE="$([ "$SRC_KIND" = mysql ] && echo knowledge_hub || echo khub)" \
  DB_USERNAME="$([ "$SRC_KIND" = mysql ] && echo root || echo postgres)" \
  DB_PASSWORD="$([ "$SRC_KIND" = mysql ] && echo "$MYSQL_ROOT_PASSWORD" || echo "$POSTGRES_PASSWORD")" \
  HUB_DATA_ROOT="$DEST_DATA" SITES_DIR="$ROOT/sites" \
    node "$ROOT/backend/scripts/verify-counts.js"
}

log "plan: source=$SRC_KIND ${SRC_HOST:-container:${SRC_CONTAINER:-}} db=$SRC_DB uploads=${SRC_DATA:-none} → docker stack in $ROOT (work dir $WORK)"
dump_source
restore_target
copy_uploads
migrate_target
verify
log "done. Start the apps with: docker compose up -d app-mysql app-pg"
