import { addColumnIfMissing } from '../src/core/db/schema-guard.js';

/** Mail gateway columns the newer Laravel app uses for HTTP + transactional API drivers. */
export const up = async (knex) => {
  const columns = [
    ['mail_http_base_url', (t) => t.string('mail_http_base_url', 255).nullable()],
    ['mail_http_client_id', (t) => t.string('mail_http_client_id', 191).nullable()],
    ['mail_http_client_secret', (t) => t.string('mail_http_client_secret', 191).nullable()],
    ['mail_api_key', (t) => t.string('mail_api_key', 255).nullable()],
    ['mail_api_secret', (t) => t.string('mail_api_secret', 255).nullable()],
    ['mail_api_domain', (t) => t.string('mail_api_domain', 191).nullable()],
    ['mail_api_region', (t) => t.string('mail_api_region', 10).nullable()],
    ['mail_api_base_url', (t) => t.string('mail_api_base_url', 255).nullable()],
    ['mail_api_message_stream', (t) => t.string('mail_api_message_stream', 100).nullable()],
  ];
  for (const [column, build] of columns) {
    await addColumnIfMissing(knex, 'setting', column, (table) => build(table));
  }
};

export const down = async () => {};
