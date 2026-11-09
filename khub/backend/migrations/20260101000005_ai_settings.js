/** Per-site AI configuration: one JSON document, edited from the admin portal. */
export const up = async (knex) => {
  if (await knex.schema.hasTable('ai_settings')) return;
  await knex.schema.createTable('ai_settings', (table) => {
    table.increments('id');
    table.json('data').nullable();
    table.integer('updated_by').unsigned().nullable();
    table.timestamp('created_at').nullable();
    table.timestamp('updated_at').nullable();
  });
};

export const down = async (knex) => {
  await knex.schema.dropTableIfExists('ai_settings');
};
