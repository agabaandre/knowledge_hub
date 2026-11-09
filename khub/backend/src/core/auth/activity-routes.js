import { Router } from 'express';
import path from 'node:path';
import multer from 'multer';
import { publicationPath, forumPath, slugify, validatePublishSubmission, DEFAULT_REQUIRED_FIELDS } from '@khub/shared';
import { asyncHandler, HttpError } from '../http/errors.js';
import { requireAuth } from './middleware.js';
import { HubStorage } from '../storage/hub-storage.js';
import { now, toBool } from '../db/helpers.js';
import { PublicationsRepository } from '../../modules/publications/repository.js';
import { sanitizeRichText } from '../http/sanitize-html.js';

/**
 * The signed-in user's content, mirroring the original account menu:
 *   GET  /api/account/publications   my submissions with review status
 *   GET  /api/account/forums         my forum threads with review status
 *   GET  /api/account/favourites     publications I favourited
 *   POST /api/account/publish        submit a resource (goes to the approvals inbox)
 */
const FILE_TYPES = {
  '.pdf': 'application/pdf',
  '.doc': 'application/msword',
  '.docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  '.ppt': 'application/vnd.ms-powerpoint',
  '.pptx': 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  '.xls': 'application/vnd.ms-excel',
  '.xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  '.csv': 'text/csv',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
};
const upload = multer({ storage: multer.memoryStorage(), limits: { fileSize: 25 * 1024 * 1024 } });

const statusOf = (row) => (row.is_approved ? 'approved' : row.is_rejected ? 'rejected' : 'pending');

export const activityRouter = () => {
  const router = Router();
  router.use(requireAuth);

  router.get(
    '/publications',
    asyncHandler(async (req, res) => {
      const rows = await req.db('publication')
        .where('user_id', req.user.id)
        .where('is_version', false)
        .select('id', 'slug', 'title', 'created_at', 'is_approved', 'is_rejected', 'rejected_reason', 'visits')
        .orderBy('created_at', 'desc')
        .limit(100);
      res.json({ data: rows.map((row) => ({ id: row.id, title: row.title, createdAt: row.created_at, visits: row.visits, status: statusOf(row), rejectedReason: row.rejected_reason, path: publicationPath(row) })) });
    }),
  );

  router.get(
    '/forums',
    asyncHandler(async (req, res) => {
      const rows = await req.db('forums')
        .where('created_by', req.user.id)
        .select('id', 'slug', 'forum_title as title', 'created_at', 'is_approved', 'is_rejected', 'rejected_reason', 'views')
        .orderBy('created_at', 'desc')
        .limit(100);
      res.json({ data: rows.map((row) => ({ id: row.id, title: row.title, createdAt: row.created_at, views: row.views, status: statusOf(row), rejectedReason: row.rejected_reason, path: forumPath(row) })) });
    }),
  );

  router.get(
    '/favourites',
    asyncHandler(async (req, res) => {
      const rows = await req.db('favourites')
        .join('publication', 'publication.id', 'favourites.publication_id')
        .leftJoin('author', 'author.id', 'publication.author_id')
        .where('favourites.user_id', req.user.id)
        .select('publication.id', 'publication.slug', 'publication.title', 'publication.created_at', 'author.name as author_name')
        .orderBy('favourites.id', 'desc')
        .limit(100);
      res.json({ data: rows.map((row) => ({ id: row.id, title: row.title, createdAt: row.created_at, author: row.author_name, status: 'favourite', path: publicationPath(row) })) });
    }),
  );

  router.get(
    '/publish-config',
    asyncHandler(async (req, res) => {
      const settings = await req.settingsService.forSite(req.site);
      let stored = {};
      try { stored = JSON.parse(settings.publication_required_fields ?? '{}') ?? {}; } catch { stored = {}; }
      const [licenses, tags, regions] = await Promise.all([
        req.db('licenses').where('is_active', true).orderBy('sort_order').select('id', 'name'),
        req.db('tags').orderBy('tag_text').select('id', 'tag_text as name'),
        req.db('regions').orderBy('region_name').select('id', 'region_name as name'),
      ]);
      res.json({
        requiredFields: { ...DEFAULT_REQUIRED_FIELDS, ...stored },
        minWords: Number(settings.publication_min_words ?? 150),
        isAdmin: Boolean(req.user.access?.isAdmin),
        licenses,
        tags,
        regions: [{ id: 'all', name: 'All (every member state)' }, ...regions],
      });
    }),
  );

  router.post(
    '/publish',
    upload.fields([{ name: 'file', maxCount: 1 }, { name: 'cover', maxCount: 1 }]),
    asyncHandler(async (req, res) => {
      const parseList = (value) => {
        if (Array.isArray(value)) return value;
        if (value === undefined || value === '') return [];
        try { const parsed = JSON.parse(value); return Array.isArray(parsed) ? parsed : [parsed]; } catch { return [value]; }
      };
      const truthy = (value) => value === true || value === 'true' || value === '1' || value === 'on';
      const data = {
        ...req.body,
        tags: parseList(req.body.tags).map(Number),
        countries: parseList(req.body.countries).map(Number),
        rccs: parseList(req.body.rccs),
        is_embedded: truthy(req.body.is_embedded),
        is_default: truthy(req.body.is_default),
        admin_only: truthy(req.body.admin_only),
        show_disclaimer: req.body.show_disclaimer === undefined ? true : truthy(req.body.show_disclaimer),
        is_journal_article: truthy(req.body.is_journal_article),
      };

      const settings = await req.settingsService.forSite(req.site);

      // New version of an existing resource (Laravel model: is_version + parent_id + version_no).
      // Classification is inherited from the root, so missing fields default from it.
      let versionParent = null;
      if (req.body.original_id) {
        if (!toBool(settings.enable_version_submission ?? true)) throw new HttpError(422, 'Version submissions are turned off on this hub.');
        const original = await req.db('publication').where({ id: Number(req.body.original_id) }).first();
        if (!original) throw new HttpError(422, 'The resource you are submitting a version of no longer exists.');
        versionParent = original.is_version && original.parent_id
          ? await req.db('publication').where({ id: original.parent_id }).first() ?? original
          : original;
        data.title = data.title || versionParent.title;
        data.sub_theme = data.sub_theme || versionParent.sub_thematic_area_id;
        data.data_category_id = data.data_category_id || versionParent.data_category_id;
        data.category_id = data.category_id || versionParent.publication_catgory_id;
        data.associated_authors = data.associated_authors || versionParent.associated_authors;
        data.author_affiliation = data.author_affiliation || versionParent.author_affiliation;
        data.publisher = data.publisher || versionParent.publisher;
        data.license_id = data.license_id || versionParent.license_id;
        if (!data.theme && data.sub_theme) {
          const subTheme = await req.db('sub_thematic_area').where({ id: data.sub_theme }).first('thematic_area_id');
          data.theme = subTheme?.thematic_area_id ?? '';
        }
        if (data.tags.length === 0) {
          const parentTags = await req.db('publication_tags').where({ publication_id: versionParent.id }).select('tag_id');
          data.tags = parentTags.map((row) => row.tag_id);
        }
        if (data.countries.length === 0) {
          const parentCountries = await req.db('publication_countries').where({ publication_id: versionParent.id }).select('country_id');
          data.countries = parentCountries.map((row) => row.country_id);
          if (data.countries.length === 0) data.rccs = ['all'];
        }
      }

      let requiredFields = {};
      try { requiredFields = JSON.parse(settings.publication_required_fields ?? '{}') ?? {}; } catch { requiredFields = {}; }

      const mainFile = req.files?.file?.[0] ?? null;
      const result = validatePublishSubmission(data, {
        requiredFields,
        minWords: Number(settings.publication_min_words ?? 150),
        isAdmin: Boolean(req.user.access?.isAdmin),
        hasFile: Boolean(mainFile),
      });
      if (!result.valid) {
        throw new HttpError(422, 'Validation failed', Object.entries(result.errors).map(([path_, message]) => ({ path: path_, message })));
      }

      const storage = new HubStorage(req.site);
      const storeUpload = async (uploaded, prefix, types) => {
        const extension = path.extname(uploaded.originalname ?? '').toLowerCase();
        if (!(extension in types) || uploaded.mimetype !== types[extension]) {
          throw new HttpError(422, `File type not allowed. Use: ${Object.keys(types).join(', ')}`);
        }
        const filename = `${prefix}-${req.user.id}-${Date.now()}${extension}`;
        await storage.write(`uploads/publications/${filename}`, uploaded.buffer);
        return filename;
      };
      const storedFile = mainFile ? await storeUpload(mainFile, 'pub', FILE_TYPES) : null;
      const coverFile = req.files?.cover?.[0]
        ? await storeUpload(req.files.cover[0], 'cover', { '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.webp': 'image/webp' })
        : null;

      // Submitter's author record (or the admin-selected source), created on first submission.
      let authorId = req.user.author_id;
      if (!authorId) {
        const [insertedAuthor] = await req.db('author').insert({ name: req.user.name, is_organsiation: 'No', created_at: now(), updated_at: now() }).returning('id');
        authorId = typeof insertedAuthor === 'object' ? insertedAuthor.id : insertedAuthor;
        await req.db('users').where({ id: req.user.id }).update({ author_id: authorId });
      }

      const baseSlug = slugify(data.title);
      const clash = await req.db('publication').where('slug', baseSlug).first('id');
      const isAdmin = Boolean(req.user.access?.isAdmin);
      const [insertedPub] = await req.db('publication').insert({
        author_id: authorId,
        user_id: req.user.id,
        title: data.title,
        slug: clash ? `${baseSlug}-${Date.now().toString(36)}` : baseSlug,
        description: sanitizeRichText(data.description),
        year_published: data.year_published ? Number(data.year_published) : null,
        sub_thematic_area_id: data.sub_theme ? Number(data.sub_theme) : 0,
        data_category_id: data.data_category_id ? Number(data.data_category_id) : null,
        publication_catgory_id: data.category_id ? Number(data.category_id) : 0,
        publication: storedFile,
        cover: coverFile,
        citation_link: data.link || null,
        associated_authors: data.associated_authors || null,
        author_affiliation: data.author_affiliation || null,
        publisher: data.publisher || null,
        funder: data.funder || null,
        doi: data.doi || null,
        issn: data.issn || null,
        isbn: data.isbn || null,
        license_id: data.license_id ? Number(data.license_id) : null,
        copyright_info: sanitizeRichText(data.copyright_info) || null,
        journal_name: data.is_journal_article ? data.journal_name || null : null,
        journal_volume: data.is_journal_article ? data.journal_volume || null : null,
        journal_issue: data.is_journal_article ? data.journal_issue || null : null,
        journal_pages: data.is_journal_article ? data.journal_pages || null : null,
        is_embedded: data.is_embedded,
        is_default_in_category: isAdmin ? data.is_default : false,
        is_admin_only_access: isAdmin ? data.admin_only : false,
        show_disclaimer: data.show_disclaimer,
        geographical_coverage_id: 0,
        is_active: 'Active',
        is_approved: false,
        is_rejected: false,
        is_version: Boolean(versionParent),
        parent_id: versionParent ? versionParent.id : null,
        version_no: versionParent ? await new PublicationsRepository(req.db).nextVersionNo(versionParent.id) : 1,
        visits: 0,
        created_at: now(),
        updated_at: now(),
      }).returning('id');
      const id = typeof insertedPub === 'object' ? insertedPub.id : insertedPub;

      if (data.tags.length) {
        await req.db('publication_tags').insert(data.tags.map((tagId) => ({ publication_id: id, tag_id: tagId })));
      }
      const regionsAll = data.rccs.some((value) => String(value).toLowerCase() === 'all');
      const countryIds = regionsAll ? [] : data.countries;
      const fallbackCountry = !regionsAll && countryIds.length === 0 && req.user.country_id ? [req.user.country_id] : [];
      for (const countryId of [...new Set([...countryIds, ...fallbackCountry])]) {
        await req.db('publication_countries').insert({ publication_id: id, country_id: countryId, created_at: now(), updated_at: now() });
      }

      res.status(201).json({ ok: true, id, status: 'pending', message: 'Thank you — your resource was submitted and is awaiting review.' });
    }),
  );

  return router;
};
