import { isKnownModule, isCoreModule } from '@khub/shared';
import { toBool, now } from '../db/helpers.js';
import { HttpError } from '../http/errors.js';

const TABLE = 'module_settings';

/** Admin overrides of module toggles, persisted per site database. */
export class ModuleSettingsRepository {
  constructor(db) {
    this.db = db;
  }

  /** @returns {Promise<Record<string, boolean>>} */
  async all() {
    const hasTable = await this.db.schema.hasTable(TABLE);
    if (!hasTable) return {};
    const rows = await this.db(TABLE).select('module', 'enabled');
    return Object.fromEntries(rows.map((row) => [row.module, toBool(row.enabled)]));
  }

  async set(module, enabled, userId = null) {
    if (!isKnownModule(module)) throw new HttpError(422, `Unknown module "${module}"`);
    if (isCoreModule(module) && !enabled) throw new HttpError(422, `Module "${module}" is a core module and cannot be disabled`);

    const existing = await this.db(TABLE).where({ module }).first();
    const payload = { enabled, updated_by: userId, updated_at: now() };
    if (existing) await this.db(TABLE).where({ module }).update(payload);
    else await this.db(TABLE).insert({ module, ...payload, created_at: now() });
  }
}
