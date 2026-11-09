import nodemailer from 'nodemailer';
import { TtlCache, MINUTE } from '../cache.js';

const healthCache = new TtlCache();

/** Drivers this mailer can actually send through (SMTP family). */
const SMTP_DRIVERS = new Set(['smtp', 'zoho']);
const ZOHO_DEFAULTS = { host: 'smtp.zoho.com', port: 587, encryption: 'tls' };

const transportFor = (settings) => {
  const driver = String(settings.email_driver ?? '').toLowerCase();
  if (!SMTP_DRIVERS.has(driver)) return null;
  const host = settings.mail_host || (driver === 'zoho' ? ZOHO_DEFAULTS.host : '');
  if (!host) return null;
  const port = Number(settings.mail_port) || (driver === 'zoho' ? ZOHO_DEFAULTS.port : 587);
  const encryption = settings.mail_encryption || (driver === 'zoho' ? ZOHO_DEFAULTS.encryption : 'tls');
  return nodemailer.createTransport({
    host,
    port,
    secure: encryption === 'ssl',
    ignoreTLS: encryption === 'none',
    auth: settings.mail_username ? { user: settings.mail_username, pass: settings.mail_password ?? '' } : undefined,
    connectionTimeout: 8000,
    greetingTimeout: 8000,
  });
};

/**
 * Outgoing mail on top of the Configure → Email settings. `isWorking()` is the
 * gate callers use: it means a transport is configured AND the server answered
 * a live connection check (cached for five minutes).
 */
export class Mailer {
  constructor(site, settingsService) {
    this.site = site;
    this.settingsService = settingsService;
  }

  async #settings() {
    return this.settingsService.forSite(this.site);
  }

  async isWorking() {
    return healthCache.remember(`mail:${this.site.id}`, 5 * MINUTE, async () => {
      const transport = transportFor(await this.#settings());
      if (!transport) return false;
      try {
        await transport.verify();
        return true;
      } catch {
        return false;
      }
    });
  }

  async send({ to, subject, html }) {
    const settings = await this.#settings();
    const transport = transportFor(settings);
    if (!transport) throw new Error('No working mail service is configured.');
    const fromAddress = settings.mail_from_address || settings.mail_username || `no-reply@${new URL(this.site.publicUrl).hostname}`;
    await transport.sendMail({
      from: `"${settings.mail_from_name || settings.site_name || 'Knowledge Hub'}" <${fromAddress}>`,
      to,
      subject,
      html,
    });
  }

  /** Call after mail settings change so the next check re-tests the server. */
  static forgetHealth(site) {
    healthCache.forget(`mail:${site.id}`);
  }
}
