import path from 'node:path';
import { Router } from 'express';
import { z } from 'zod';
import multer from 'multer';
import { asyncHandler, HttpError } from '../http/errors.js';
import { validate } from '../http/validate.js';
import { requirePermission } from '../auth/middleware.js';
import { HubStorage, mediaUrl } from '../storage/hub-storage.js';
import { Mailer } from '../mail/mailer.js';

const color = z.string().trim().regex(/^#[0-9a-fA-F]{3,8}$/).or(z.literal(''));
const text = (max) => z.string().trim().max(max);
const toggle = z.boolean();

/**
 * Fields the admin Configure page may edit, mirroring the production System
 * Configuration tabs. Secrets (mail password, OAuth client secrets, API keys)
 * deliberately stay out of this surface.
 */
const SETTING_SHAPE = {
  // General
  site_name: text(100).min(2).optional(),
  title: text(255).optional(),
  slogan: text(100).optional(),
  site_description: text(2000).optional(),
  seo_keywords: text(1000).optional(),
  timezone: text(64).optional(),
  use_seo_friendly_urls: toggle.optional(),
  // Branding
  logo: text(255).optional(),
  favicon: text(255).optional(),
  logo_scale: text(10).optional(),
  header_logo_inverse: toggle.optional(),
  footer_logo_inverse: toggle.optional(),
  spotlight_banner: text(255).optional(),
  banner_text: text(500).optional(),
  spotlight_overlay_color: color.optional(),
  spotlight_overlay_opacity: text(10).optional(),
  // Frontend
  footer_style: text(50).optional(),
  preloader_enabled: toggle.optional(),
  preloader_text: text(100).optional(),
  show_publication_card_file_type_badge: toggle.optional(),
  theme_cards_per_row: text(5).optional(),
  content_disclaimer: text(1000).optional(),
  // Colors & Typography
  primary_color: color.optional(),
  secondary_color: color.optional(),
  gradient_start_color: color.optional(),
  gradient_end_color: color.optional(),
  default_font_color: color.optional(),
  primary_text_color: color.optional(),
  primary_font: text(100).optional(),
  front_body_font_size: text(10).optional(),
  // Navigation
  nav_style: text(50).optional(),
  menu_icons_enabled: toggle.optional(),
  nav_font_size: text(10).optional(),
  nav_font_weight: text(10).optional(),
  nav_link_color: color.optional(),
  nav_link_hover_color: color.optional(),
  nav_link_active_color: color.optional(),
  // Homepage
  show_featured: toggle.optional(),
  show_events: toggle.optional(),
  show_top_searches: toggle.optional(),
  show_quotes: toggle.optional(),
  show_health_themes: toggle.optional(),
  show_tags: toggle.optional(),
  show_quiz: toggle.optional(),
  section_title_health_themes: text(255).optional(),
  section_title_top_searches: text(255).optional(),
  section_title_recommended: text(255).optional(),
  section_title_flagship_initiatives: text(255).optional(),
  spotlight_animation: text(50).optional(),
  spotlight_animation_speed: text(10).optional(),
  // Search
  enable_ai_search: toggle.optional(),
  search_show_forums: toggle.optional(),
  search_show_communities: toggle.optional(),
  search_pagination_mode: text(30).optional(),
  // Contact
  address: text(500).optional(),
  email: text(50).optional(),
  phone: text(20).optional(),
  facebook: text(191).optional(),
  twitter: text(191).optional(),
  linkedin: text(191).optional(),
  researchgate: text(191).optional(),
  // Email — same driver catalogue as the Laravel app (app/Support/EmailDrivers.php)
  email_driver: z.enum(['http', 'exchange', 'smtp', 'zoho', 'sendgrid', 'mailgun', 'postmark', 'mailjet', 'log']).optional(),
  mail_from_name: text(100).optional(),
  mail_from_address: text(100).optional(),
  mail_host: text(191).optional(),
  mail_port: text(10).optional(),
  mail_username: text(191).optional(),
  mail_password: text(191).optional(),
  mail_encryption: text(10).optional(),
  exchange_tenant_id: text(191).optional(),
  exchange_client_id: text(191).optional(),
  exchange_client_secret: text(191).optional(),
  exchange_redirect_uri: text(255).optional(),
  exchange_scope: text(255).optional(),
  exchange_auth_method: text(50).optional(),
  mail_http_base_url: text(255).optional(),
  mail_http_client_id: text(191).optional(),
  mail_http_client_secret: text(191).optional(),
  mail_api_key: text(255).optional(),
  mail_api_secret: text(255).optional(),
  mail_api_domain: text(191).optional(),
  mail_api_region: text(10).optional(),
  mail_api_base_url: text(255).optional(),
  mail_api_message_stream: text(100).optional(),
  // Social login
  enable_google_login: toggle.optional(),
  enable_microsoft_login: toggle.optional(),
  enable_linkedin_login: toggle.optional(),
  allow_email_password_accounts_social_login: toggle.optional(),
  google_client_id: text(191).optional(),
  google_client_secret: text(191).optional(),
  microsoft_client_id: text(191).optional(),
  microsoft_client_secret: text(191).optional(),
  microsoft_tenant_id: text(191).optional(),
  linkedin_client_id: text(191).optional(),
  linkedin_client_secret: text(191).optional(),
  // Advanced
  auto_approve_comments: toggle.optional(),
  auto_approve_publications: toggle.optional(),
  enable_version_submission: toggle.optional(),
  publication_min_words: text(10).optional(),
  block_disposable_email_registration: toggle.optional(),
  blocked_email_domains: text(2000).optional(),
  analytics_script: text(5000).optional(),
  trash_retention_days: text(5).regex(/^\d*$/).optional(),
  federation_api_token: text(255).optional(),
};

const settingsSchema = z.object(SETTING_SHAPE).partial().refine((value) => Object.keys(value).length > 0, 'Nothing to update');

export const EDITABLE_SETTING_KEYS = Object.keys(SETTING_SHAPE);

/** Secrets are editable from Configure but never echoed back: reads are masked. */
export const SECRET_SETTING_KEYS = [
  'federation_api_token', 'mail_password', 'exchange_client_secret', 'mail_http_client_secret', 'mail_api_key', 'mail_api_secret',
  'google_client_secret', 'microsoft_client_secret', 'linkedin_client_secret',
];
export const SECRET_MASK = '••••••••';

const GALLERY_TYPES = { '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.gif': 'image/gif', '.webp': 'image/webp', '.svg': 'image/svg+xml', '.ico': 'image/x-icon' };
const upload = multer({ storage: multer.memoryStorage(), limits: { fileSize: 4 * 1024 * 1024 } });

/** GET/PUT /api/admin/settings + the branding config gallery (uploads/config). */
export const settingsRoutes = ({ settings }) => {
  const router = Router();

  router.get(
    '/settings',
    asyncHandler(async (req, res) => {
      const siteSettings = await settings.forSite(req.site);
      const payload = Object.fromEntries(EDITABLE_SETTING_KEYS.map((key) => [key, siteSettings[key] ?? '']));
      for (const key of SECRET_SETTING_KEYS) {
        if (payload[key]) payload[key] = SECRET_MASK;
      }
      res.json({ settings: payload, editable: EDITABLE_SETTING_KEYS, secrets: SECRET_SETTING_KEYS });
    }),
  );

  router.put(
    '/settings',
    validate(settingsSchema),
    asyncHandler(async (req, res) => {
      const updates = { ...req.body };
      // A masked secret means "unchanged" — never write the mask to the database.
      for (const key of SECRET_SETTING_KEYS) {
        if (updates[key] === SECRET_MASK) delete updates[key];
      }
      if (Object.keys(updates).length === 0) return res.json({ ok: true, updated: [] });
      // Branding files are stored as bare filenames (Laravel convention).
      for (const key of ['logo', 'favicon', 'spotlight_banner']) {
        if (key in updates && updates[key]) updates[key] = String(updates[key]).split('/').pop();
      }
      const row = await req.db('setting').where('status', 'active').first() ?? await req.db('setting').first();
      if (row) await req.db('setting').where({ id: row.id }).update(updates);
      else await req.db('setting').insert({ timezone: 'Africa/Addis_Ababa', status: 'active', seo_keywords: '', site_description: '', ...updates });
      settings.forget(req.site);
      Mailer.forgetHealth(req.site); // mail settings may have changed — re-test on next use
      res.json({ ok: true, updated: Object.keys(updates) });
    }),
  );

  // Live check of the configured mail service (used by registration verification too).
  router.post(
    '/settings/mail-test',
    requirePermission('admin.settings.edit'),
    asyncHandler(async (req, res) => {
      const mailer = new Mailer(req.site, settings);
      Mailer.forgetHealth(req.site);
      const working = await mailer.isWorking();
      if (working && req.body?.to) {
        await mailer.send({ to: String(req.body.to), subject: 'Knowledge Hub mail test', html: '<p>The mail service is configured and working.</p>' });
        return res.json({ working: true, sent: true });
      }
      res.json({ working });
    }),
  );

  // Config gallery: the files Branding pickers choose from.
  router.get(
    '/settings/gallery',
    asyncHandler(async (req, res) => {
      const names = await new HubStorage(req.site).list('uploads/config');
      const files = names
        .filter((name) => path.extname(name).toLowerCase() in GALLERY_TYPES)
        .map((name) => ({ name, url: mediaUrl(req.site, `uploads/config/${name}`) }));
      res.json({ files });
    }),
  );

  router.post(
    '/settings/gallery',
    requirePermission('admin.settings.edit'),
    upload.single('file'),
    asyncHandler(async (req, res) => {
      if (!req.file) throw new HttpError(422, 'Attach an image as the "file" field.');
      const extension = path.extname(req.file.originalname ?? '').toLowerCase();
      if (!(extension in GALLERY_TYPES)) throw new HttpError(422, 'Images only: PNG, JPG, GIF, WebP, SVG or ICO.');
      const name = `cfg-${Date.now()}${extension}`;
      await new HubStorage(req.site).write(`uploads/config/${name}`, req.file.buffer);
      res.status(201).json({ name, url: mediaUrl(req.site, `uploads/config/${name}`) });
    }),
  );

  return router;
};
