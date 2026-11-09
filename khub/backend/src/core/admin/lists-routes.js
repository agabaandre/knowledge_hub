import { Router } from 'express';
import { z } from 'zod';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { now } from '../db/helpers.js';
import { LookupsRepository } from '../../modules/publications/lookups-repository.js';

/**
 * Dropdown Lists — the taxonomy tables behind the portal's selects. One
 * generic, allowlisted editor covers them (the production admin has a page
 * per list; same operations).
 */
const LISTS = {
  'thematic-areas': { table: 'thematic_area', label: 'Thematic Areas', columns: { name: 'description', extra: 'icon' }, extraLabel: 'Icon (fa-…)' },
  'sub-themes': { table: 'sub_thematic_area', label: 'Sub Themes', columns: { name: 'description', parent: 'thematic_area_id' }, parentList: 'thematic-areas' },
  'data-categories': { table: 'data_categories', label: 'Categories', columns: { name: 'category_name', flag: 'show_on_menu' }, flagLabel: 'Show on menu' },
  'file-categories': { table: 'publication_categories', label: 'Resource & Asset Types', columns: { name: 'category_name' } },
  'sub-categories': { table: 'publication_sub_categories', label: 'Sub Categories', columns: { name: 'category_name', parent: 'publication_category_id' }, parentList: 'file-categories' },
  'file-types': { table: 'file_type', label: 'File Types', columns: { name: 'name', extra: 'icon' }, extraLabel: 'Icon (fa-…)', noTimestamps: true },
  tags: { table: 'tags', label: 'Tags / Health Topics', columns: { name: 'tag_text', desc: 'overview', flag: 'is_health_emergency' }, flagLabel: 'Health emergency', noTimestamps: true },
  'geographical-coverage': { table: 'geographical_scope', label: 'Geographical Coverage', columns: { name: 'name', extra: 'description' }, extraLabel: 'Description' },
  regions: { table: 'regions', label: 'Regions', columns: { name: 'region_name' }, noTimestamps: true },
  countries: { table: 'country', label: 'Member States', columns: { name: 'name', extra: 'iso_code', parent: 'region_id' }, extraLabel: 'ISO code', parentList: 'regions', noTimestamps: true, insertDefaults: { national: '', longitude: 0, latitude: 0, population: 0, color: '' } },
  licenses: { table: 'licenses', label: 'Licenses', columns: { name: 'name', extra: 'url' }, extraLabel: 'URL' },
  'static-links': { table: 'static_links', label: 'Static Links', columns: { name: 'title', extra: 'link', flag: 'open_in_new_tab' }, extraLabel: 'URL', flagLabel: 'Open in new tab', requireExtra: true, orderColumn: 'order' },
  faqs: { table: 'faqs', label: 'FAQs', columns: { name: 'question', extra: 'answer' }, extraLabel: 'Answer', extraType: 'textarea', requireExtra: true },
  quotes: { table: 'quotes', label: 'Quotes', columns: { name: 'quote', extra: 'link_url', flag: 'is_active' }, extraLabel: 'Link URL', flagLabel: 'Active', noTimestamps: true },
};

const itemSchema = z.object({
  name: z.string().trim().min(1).max(1000),
  extra: z.string().trim().max(10000).optional().default(''),
  desc: z.string().trim().max(10000).optional().default(''),
  flag: z.boolean().optional(),
  parent_id: z.coerce.number().int().positive().nullable().optional(),
});

const configOf = (key) => {
  const config = LISTS[key];
  if (!config) throw new HttpError(404, `Unknown list "${key}". Available: ${Object.keys(LISTS).join(', ')}`);
  return config;
};

const serialize = (config, row) => ({
  id: row.id,
  name: row[config.columns.name],
  extra: config.columns.extra ? (row[config.columns.extra] ?? '') : undefined,
  desc: config.columns.desc ? (row[config.columns.desc] ?? '') : undefined,
  flag: config.columns.flag ? Boolean(row[config.columns.flag]) : undefined,
  parentId: config.columns.parent ? row[config.columns.parent] : undefined,
});

export const listsRoutes = () => {
  const router = Router();

  router.get(
    '/lists',
    asyncHandler(async (req, res) => {
      const lists = [];
      for (const [key, config] of Object.entries(LISTS)) {
        const count = await req.db(config.table).count({ total: '*' }).first();
        lists.push({ key, label: config.label, count: Number(count.total), hasExtra: Boolean(config.columns.extra), extraLabel: config.extraLabel, extraType: config.extraType ?? 'input', hasDesc: Boolean(config.columns.desc), descLabel: config.descLabel ?? 'Description', hasFlag: Boolean(config.columns.flag), flagLabel: config.flagLabel, parentList: config.parentList });
      }
      res.json({ lists });
    }),
  );

  router.get(
    '/lists/:key',
    asyncHandler(async (req, res) => {
      const config = configOf(req.params.key);
      const rows = await req.db(config.table).orderBy(config.columns.name).limit(500);
      res.json({ items: rows.map((row) => serialize(config, row)) });
    }),
  );

  // CSV export of one list (optionally filtered by ?term=…).
  router.get(
    '/lists/:key/export',
    asyncHandler(async (req, res) => {
      const config = configOf(req.params.key);
      const term = String(req.query.term ?? '').trim();
      const query = req.db(config.table).orderBy(config.columns.name);
      if (term) query.whereILike(config.columns.name, `%${term}%`);
      const rows = await query;
      const parents = config.parentList
        ? Object.fromEntries((await req.db(LISTS[config.parentList].table).select('id', LISTS[config.parentList].columns.name))
            .map((row) => [row.id, row[LISTS[config.parentList].columns.name]]))
        : {};

      const header = ['ID', 'Name'];
      if (config.columns.extra) header.push(config.extraLabel ?? 'Extra');
      if (config.columns.desc) header.push(config.descLabel ?? 'Description');
      if (config.parentList) header.push('Parent');
      if (config.columns.flag) header.push(config.flagLabel ?? 'Flag');

      const cell = (value) => {
        const text = String(value ?? '');
        return /[",\n]/.test(text) ? `"${text.replace(/"/g, '""')}"` : text;
      };
      const lines = rows.map((row) => {
        const item = serialize(config, row);
        const cells = [item.id, item.name];
        if (config.columns.extra) cells.push(item.extra);
        if (config.columns.desc) cells.push(item.desc);
        if (config.parentList) cells.push(parents[item.parentId] ?? '');
        if (config.columns.flag) cells.push(item.flag ? 'yes' : 'no');
        return cells.map(cell).join(',');
      });
      res.setHeader('Content-Type', 'text/csv; charset=utf-8');
      res.setHeader('Content-Disposition', `attachment; filename="${req.params.key}-${new Date().toISOString().slice(0, 10)}.csv"`);
      res.send(['\uFEFF' + header.join(','), ...lines].join('\n'));
    }),
  );

  router.post(
    '/lists/:key',
    requirePermission('admin.lists.edit'),
    validate(itemSchema),
    asyncHandler(async (req, res) => {
      const config = configOf(req.params.key);
      const body = req.body;
      if (config.requireExtra && !body.extra) throw new HttpError(422, `${config.extraLabel} is required for ${config.label}.`);
      const payload = { [config.columns.name]: body.name };
      if (config.columns.extra && body.extra) payload[config.columns.extra] = body.extra;
      if (config.columns.desc) payload[config.columns.desc] = body.desc || null;
      if (config.columns.flag && body.flag !== undefined) payload[config.columns.flag] = body.flag;
      if (config.columns.parent) {
        if (!body.parent_id) throw new HttpError(422, 'A parent is required for this list.');
        payload[config.columns.parent] = body.parent_id;
      }
      if (!config.noTimestamps) Object.assign(payload, { created_at: now(), updated_at: now() });
      if (config.insertDefaults) Object.assign(payload, config.insertDefaults, payload);
      if (config.orderColumn) {
        const max = await req.db(config.table).max({ max: config.orderColumn }).first();
        payload[config.orderColumn] = Number(max?.max ?? 0) + 1;
      }
      let inserted;
      try {
        [inserted] = await req.db(config.table).insert(payload).returning('id');
      } catch (error) {
        // Legacy Laravel tables sometimes lack an auto-increment id; retry with an explicit one.
        if (!/'?id'?/i.test(String(error.message))) throw error;
        const max = await req.db(config.table).max({ max: 'id' }).first();
        payload.id = Number(max?.max ?? 0) + 1;
        await req.db(config.table).insert(payload);
        inserted = payload.id;
      }
      LookupsRepository.forget(req.site);
      res.status(201).json({ id: typeof inserted === 'object' ? inserted.id : inserted });
    }),
  );

  router.put(
    '/lists/:key/:id',
    requirePermission('admin.lists.edit'),
    validate(itemSchema),
    asyncHandler(async (req, res) => {
      const config = configOf(req.params.key);
      const body = req.body;
      if (config.requireExtra && !body.extra) throw new HttpError(422, `${config.extraLabel} is required for ${config.label}.`);
      const payload = { [config.columns.name]: body.name };
      if (config.columns.extra) payload[config.columns.extra] = body.extra || null;
      if (config.columns.desc) payload[config.columns.desc] = body.desc || null;
      if (config.columns.flag && body.flag !== undefined) payload[config.columns.flag] = body.flag;
      if (config.columns.parent && body.parent_id) payload[config.columns.parent] = body.parent_id;
      if (!config.noTimestamps) payload.updated_at = now();
      const changed = await req.db(config.table).where({ id: Number(req.params.id) }).update(payload);
      if (!changed) throw new HttpError(404, 'Item not found.');
      LookupsRepository.forget(req.site);
      res.json({ ok: true });
    }),
  );

  router.delete(
    '/lists/:key/:id',
    requirePermission('admin.lists.edit'),
    asyncHandler(async (req, res) => {
      const config = configOf(req.params.key);
      await req.db(config.table).where({ id: Number(req.params.id) }).del();
      LookupsRepository.forget(req.site);
      res.status(204).end();
    }),
  );

  return router;
};
