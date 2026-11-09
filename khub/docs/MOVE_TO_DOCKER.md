# Moving a host or VM install into the Docker stack

This moves a running Knowledge Hub database and its uploads from a host or VM install (MySQL or PostgreSQL) into the Node stack in `docker-compose.yml`. It works for both the Laravel schema and the Node schema, because the Node migrations skip tables that already exist.

## Before you start

1. Stop writes on the source: put the Laravel site in maintenance mode, or accept that anything written after the dump is lost.
2. Copy `.env.docker.example` to `.env` and set `APP_SECRET`, `MYSQL_ROOT_PASSWORD`, `POSTGRES_PASSWORD`.
3. Start the database containers: `docker compose up -d mysql postgres`.

## Run the move

Source on a host or VM (MySQL):

```bash
SRC_KIND=mysql SRC_HOST=10.0.0.5 SRC_PORT=3306 SRC_DB=knowledge_hub \
SRC_USER=root SRC_PASSWORD='…' SRC_DATA=/var/khubdata \
scripts/move-to-docker.sh --dry-run      # check the plan first
SRC_KIND=mysql SRC_HOST=10.0.0.5 SRC_PORT=3306 SRC_DB=knowledge_hub \
SRC_USER=root SRC_PASSWORD='…' SRC_DATA=/var/khubdata \
scripts/move-to-docker.sh
```

Source is a container (the current Laravel MySQL in `khub_mysql`):

```bash
SRC_KIND=mysql SRC_CONTAINER=khub_mysql SRC_DB=knowledge_hub SRC_USER=root \
SRC_PASSWORD='…' SRC_PORT=3308 SRC_DATA=/path/to/khubdata scripts/move-to-docker.sh
```

Source is PostgreSQL: use `SRC_KIND=pg`, `SRC_PORT=5432`, `SRC_DB=khub`.

**No client tools needed on the machine running the script.** If `mysqldump` or `pg_dump` is not installed, the script runs it from the official `mysql:8.4` or `postgres:16-alpine` image over TCP (`127.0.0.1`/`localhost` is rewritten to `host.docker.internal`). For a VM source, set `SRC_HOST` to the VM's IP and make sure its database port is reachable from this machine.

## What the script does

1. **Dump** the source (`mysqldump --single-transaction --routines --triggers --events` or `pg_dump -Fc`).
2. **Restore** into the Docker database. The target database is dropped and recreated, so re-running is safe.
3. **Copy uploads** from `SRC_DATA` to `KHUBDATA_DIR` (default `khubdata/`, mounted into the app containers at `/var/khubdata`). `rsync -a` never deletes. For a VM source, copy the folder to this machine first (`rsync -a vm:/var/khubdata/ ./khubdata-from-vm/`) and point `SRC_DATA` at it.
4. **Migrate** with the Node migrations inside the app image. Existing tables are left alone; only missing Node tables such as `module_settings` are added.
5. **Verify** row counts for every known table, source against target. The script exits non-zero on any mismatch.

## Start the apps

```bash
docker compose up -d --build app-mysql app-pg
open http://localhost:4001   # MySQL-backed app
open http://localhost:4002   # PostgreSQL-backed app
```

Check `/admin/metrics` (admin login) and a few resource pages before pointing DNS or the reverse proxy at the containers.

## Rollback

The source is not modified. To roll back, point traffic at the host or VM install again. The Docker database can be discarded with `docker compose down -v`, which removes the named volumes.

## Limits

- Cloud storage drivers (S3, Azure, GCS, SharePoint, SFTP) are not copied; only the internal host-path uploads are.
- Migration tables not yet ported to Node (KPI, federation, courses, AI chat, and others) are copied as raw tables but not served by the new API.

## Smoke test after the move

```bash
npm run smoke -- --base=http://localhost:4001 --email=<admin email> --password=<admin password>
npm run smoke -- --base=http://localhost:4002 --email=<admin email> --password=<admin password>
```

Laravel passwords keep working (`$2y$` bcrypt hashes are accepted), so use an existing admin account.
