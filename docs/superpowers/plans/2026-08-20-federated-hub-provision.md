# Federated Hub Provision Implementation Plan

> **For agentic workers:** Implement task-by-task on branch `lara` (no new branch).

**Goal:** Let continental admins provision a country Knowledge Hub at `https://khub.africacdc.org/{slug}` from `/admin/federated-hubs`.

**Architecture:** Background job orchestrates file copy, MySQL DB creation, Apache Alias, target-tree `khub:install`, metadata copy, and `FederatedKnowledgeHub` registration. Privileged credentials live only in continental `.env`.

**Tech Stack:** Laravel jobs, MySQL admin PDO, sudo shell for rsync/Apache, existing `InstallerService` / `FederatedHubLookupService`.

## Global Constraints

- Bare-metal Apache path aliases only (not Docker, not subdomains).
- `STATES_ENABLED=false`, `ADMIN_UNITS_ENABLED=true`, owner country = selected country ID.
- No user/content migration from continental.
- Credentials never shown in UI/logs.

## Files

| Path | Role |
|------|------|
| `config/federation_provision.php` | Env-backed provision settings |
| `database/migrations/*_create_federated_hub_provisions_table.php` | Job status rows |
| `app/Models/FederatedHubProvision.php` | Status model |
| `app/Services/FederationProvision/*` | Filesystem, Database, Apache, EnvWriter, AppInstaller, MetadataCopy, ProvisionService |
| `app/Jobs/ProvisionFederatedHubJob.php` | Async orchestration |
| `app/Http/Controllers/Admin/FederatedHubsController.php` | storeProvision + status |
| `routes/web.php` | Routes |
| `resources/views/admin/federation/index.blade.php` | Provision tab |
| `.env.docker.example` | Document provision keys |
| `tests/Unit/FederationProvision/*` | Slug + Apache snippet tests |

## Tasks

- [ ] Task 1: Config, migration, model
- [ ] Task 2: Provision helper services + orchestrator
- [ ] Task 3: Job, controller, routes, UI
- [ ] Task 4: Env example + unit tests

Spec: `docs/superpowers/specs/2026-08-20-federated-hub-provision-design.md`
