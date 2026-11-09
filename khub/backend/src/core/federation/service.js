import { slugify } from '@khub/shared';
import { now } from '../db/helpers.js';
import { sanitizeRichText } from '../http/sanitize-html.js';

/** Portable record exchanged between hubs — intentionally small and self-contained. */
export const portablePublication = (site, row) => ({
  remoteId: String(row.id),
  title: row.title,
  description: row.description,
  yearPublished: row.year_published,
  link: row.citation_link || `${site.publicUrl}/records/resource/${row.slug ?? row.id}`,
  authors: row.associated_authors || row.author_name || '',
  publisher: row.publisher,
  doi: row.doi,
  updatedAt: row.updated_at,
});

const FETCH_TIMEOUT_MS = 20000;

const remoteFetch = async (hub, pathname, options = {}) => {
  const response = await fetch(`${hub.base_url.replace(/\/+$/, '')}${pathname}`, {
    ...options,
    headers: {
      authorization: `Bearer ${hub.api_token}`,
      'content-type': 'application/json',
      ...options.headers,
    },
    signal: AbortSignal.timeout(FETCH_TIMEOUT_MS),
  });
  const payload = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(payload.error ?? `Remote hub answered ${response.status}`);
  return payload;
};

/**
 * Federation: pull approved content from partner hubs and push ours to them.
 * Imported records are regular (pending-free, already-reviewed) publications
 * tagged with their origin, upserted by (federated_source, remote id) so
 * repeated syncs update instead of duplicating.
 */
export class FederationService {
  constructor(site) {
    this.site = site;
    this.db = site.db;
  }

  /** Local records worth sharing: approved, active, public, not themselves imports. */
  exportQuery() {
    return this.db('publication')
      .leftJoin('author', 'author.id', 'publication.author_id')
      .where({ 'publication.is_approved': true, 'publication.is_active': 'Active', 'publication.is_version': false, 'publication.is_admin_only_access': false })
      .whereNull('publication.deleted_at')
      .whereNull('publication.federated_source')
      .select('publication.*', 'author.name as author_name');
  }

  async testConnection(hub) {
    return remoteFetch(hub, '/api/federation/manifest');
  }

  /** A per-source author record so imported items credit their origin hub. */
  async #sourceAuthorId(hub) {
    const name = `Federated: ${new URL(hub.base_url).host}`;
    const existing = await this.db('author').where({ name }).first('id');
    if (existing) return existing.id;
    const [inserted] = await this.db('author').insert({ name, is_organsiation: 'Yes', created_at: now(), updated_at: now() }).returning('id');
    return typeof inserted === 'object' ? inserted.id : inserted;
  }

  async upsertImported(hub, record) {
    const existing = await this.db('publication')
      .where({ federated_source: hub.base_url, federated_remote_id: record.remoteId })
      .first('id');
    const common = {
      title: String(record.title ?? '').slice(0, 500),
      description: sanitizeRichText(record.description ?? ''),
      year_published: record.yearPublished ?? null,
      citation_link: record.link,
      associated_authors: record.authors || null,
      publisher: record.publisher || null,
      doi: record.doi || null,
      updated_at: now(),
    };
    if (existing) {
      await this.db('publication').where({ id: existing.id }).update(common);
      return 'updated';
    }
    const baseSlug = slugify(common.title);
    const clash = await this.db('publication').where('slug', baseSlug).first('id');
    await this.db('publication').insert({
      ...common,
      author_id: await this.#sourceAuthorId(hub),
      slug: clash ? `${baseSlug}-${Date.now().toString(36)}` : baseSlug,
      federated_source: hub.base_url,
      federated_remote_id: record.remoteId,
      sub_thematic_area_id: 0,
      publication_catgory_id: 0,
      geographical_coverage_id: 0,
      is_active: 'Active',
      is_approved: true, // already reviewed on the origin hub
      is_rejected: false,
      is_version: false,
      visits: 0,
      created_at: now(),
    });
    return 'created';
  }

  /** Pull the partner's shared records into this hub. */
  async pull(hub) {
    const payload = await remoteFetch(hub, '/api/federation/publications?per_page=100');
    let created = 0;
    let updated = 0;
    for (const record of payload.data ?? []) {
      if (!record.remoteId || !record.title) continue;
      const result = await this.upsertImported(hub, record);
      if (result === 'created') created += 1;
      else updated += 1;
    }
    return { pulled: payload.data?.length ?? 0, created, updated };
  }

  /** Push this hub's shared records to the partner. */
  async push(hub) {
    const rows = await this.exportQuery().orderBy('publication.updated_at', 'desc').limit(100);
    const payload = await remoteFetch(hub, '/api/federation/import', {
      method: 'POST',
      body: JSON.stringify({ source: this.site.publicUrl, publications: rows.map((row) => portablePublication(this.site, row)) }),
    });
    return { pushed: rows.length, ...payload };
  }

  async sync(hub) {
    const result = {};
    if (hub.direction !== 'push') {
      const pull = await this.pull(hub);
      Object.assign(result, { pulled: pull.pulled, pulledNew: pull.created, pulledUpdated: pull.updated });
    }
    if (hub.direction !== 'pull') {
      const push = await this.push(hub);
      Object.assign(result, { pushed: push.pushed, pushAcceptedNew: push.created ?? 0, pushAcceptedUpdated: push.updated ?? 0 });
    }
    return result;
  }
}
