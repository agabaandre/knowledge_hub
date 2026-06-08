<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $integrationColumns = [
        'email_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'exchange_tenant_id',
        'exchange_client_id',
        'exchange_client_secret',
        'exchange_redirect_uri',
        'exchange_scope',
        'exchange_auth_method',
        'microsoft_client_id',
        'microsoft_client_secret',
        'microsoft_redirect_uri',
        'microsoft_tenant_id',
        'google_client_id',
        'google_client_secret',
        'google_redirect_uri',
        'linkedin_client_id',
        'linkedin_client_secret',
        'linkedin_redirect_uri',
        'moodle_api_url',
        'moodle_api_token',
        'moodle_base_url',
        'moodle_sync_enabled',
        'frappe_base_url',
        'frappe_api_key',
        'frappe_api_secret',
        'frappe_course_doctype',
        'frappe_sync_enabled',
        'openedx_lms_url',
        'openedx_client_id',
        'openedx_client_secret',
        'openedx_token_url',
        'openedx_sync_enabled',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('setting')) {
            return;
        }

        $updates = [];
        foreach ($this->integrationColumns as $column) {
            if (Schema::hasColumn('setting', $column)) {
                $updates[$column] = null;
            }
        }

        if ($updates !== []) {
            DB::table('setting')->update($updates);
        }

        if (Schema::hasColumn('setting', 'sso_use_database_credentials')) {
            DB::table('setting')->update(['sso_use_database_credentials' => false]);
        }
    }

    public function down(): void
    {
        // No-op: cleared overrides are not restored automatically.
    }
};
