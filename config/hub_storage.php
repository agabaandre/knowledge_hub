<?php

return [

    /*
    | Host data root. Each hub instance gets a subdirectory:
    |   /var/khubdata/{site-id}/files
    |   /var/khubdata/{site-id}/backups/sql
    |
    | site-id is derived from APP_URL (domain + path, e.g. khub.com/andrew → khub-com-andrew)
    | or set explicitly with HUB_SITE_ID in .env. In Docker, mount ./khubdata:/var/khubdata.
    */
    'host_data_root' => '/var/khubdata',
    'host_data_root_windows' => 'C:\\khubdata',

    'drivers' => [
        'internal' => 'Internal (application or host path)',
        's3' => 'Amazon S3 (or S3-compatible)',
        'gcs' => 'Google Cloud Storage',
        'azure' => 'Azure Blob Storage',
        'sharepoint' => 'Microsoft SharePoint (organisational)',
        'sftp' => 'SFTP',
    ],

    /*
    | Optional Composer packages per external driver (install on the app server).
    */
    'driver_packages' => [
        's3' => 'composer require league/flysystem-aws-s3-v3 "^1.0"',
        'gcs' => 'composer require google/cloud-storage superbalist/flysystem-google-storage "^7.2"',
        'azure' => 'Built-in (Azure Blob REST via Guzzle). No extra Composer packages required.',
        'sharepoint' => 'Built-in (Microsoft Graph via Guzzle). Register an Azure AD app with Sites.ReadWrite.All application permission.',
        'sftp' => 'Built-in (phpseclib ^3 via Laravel Passport). No extra Composer packages required.',
    ],

    /*
    | Setup notes shown in admin when a driver is selected.
    */
    'driver_setup' => [
        's3' => 'Create an IAM user or role with s3:PutObject, s3:GetObject, s3:DeleteObject on the bucket. For MinIO or other S3-compatible stores, set the custom endpoint.',
        'gcs' => 'Create a service account with Storage Object Admin on the bucket. Upload the JSON key to the server (outside the web root) and enter its path below. Bucket names are globally unique.',
        'azure' => 'Create a Storage Account and container. Use the connection string from Azure Portal → Access keys, or account name + key. Prefix maps to a virtual folder inside the container.',
        'sharepoint' => 'In Azure AD: App registration → client secret → API permissions → Microsoft Graph application permissions: Sites.ReadWrite.All (admin consent). Provide site hostname (e.g. contoso.sharepoint.com) and site path (e.g. sites/KnowledgeHub), or the Graph site ID. Drive ID is optional; the default document library is resolved automatically.',
        'sftp' => 'Use SSH key or password authentication. Root prefix is the remote directory for hub uploads.',
    ],

    'content_prefixes' => [
        'publications' => 'uploads/publications',
        'publication_summaries' => 'uploads/publications/summaries',
        'forums' => 'uploads/forums',
        'forum_attachments' => 'uploads/forum',
        'users' => 'uploads/users',
    ],

    /*
    | KPI / OWID indicator tables (API-synced). Excluded from SQL backup — re-fetch from OWID instead.
    */
    'backup_kpi_tables' => [
        'kpi',
        'kpi_narrations',
        'kpi_sync_runs',
        'subject_areas',
        'data',
    ],

    /*
    | Tables included in SQL backups (grouped for admin UI; order within each group respects FKs).
    | Flat list is built by HubDatabaseBackupService::backupTables().
    */
    'backup_table_groups' => [
        'Site & configuration' => [
            'setting' => 'Site settings',
            'setting_key_groups' => 'Setting key groups',
            'site_languages' => 'Site languages',
            'theme_settings' => 'Theme settings',
            'hub_storage_settings' => 'Storage management settings',
            'federated_knowledge_hubs' => 'Federated knowledge hubs registry',
            'licenses' => 'Licenses',
            'static_links' => 'Static links',
            'rss_feeds' => 'RSS feeds',
        ],
        'Access control' => [
            'permissions' => 'Permissions',
            'roles' => 'Roles',
            'role_has_permissions' => 'Role permissions',
            'access_levels' => 'Access levels',
            'user_access_groups' => 'User access groups',
            'user_access_groupings' => 'User access group memberships',
            'publication_access_groups' => 'Publication access groups',
        ],
        'Users & authors' => [
            'users' => 'Users',
            'author' => 'Authors',
            'user_roles' => 'User roles (legacy)',
            'model_has_roles' => 'User role assignments',
            'model_has_permissions' => 'User permission assignments',
            'user_preferences' => 'User preferences',
            'job_titles' => 'Job titles',
            'isco_classifications' => 'ISCO classifications',
        ],
        'Badges & recognition' => [
            'badge_types' => 'Badge types',
            'user_lifetime_badges' => 'Lifetime contributor badges',
            'user_community_monthly_contributions' => 'Community monthly contributions',
            'user_badges' => 'User badges (legacy)',
        ],
        'Taxonomy & geography' => [
            'thematic_area' => 'Thematic areas',
            'sub_thematic_area' => 'Sub-thematic areas',
            'tags' => 'Tags / health topics',
            'data_category' => 'Data categories',
            'data_sub_categories' => 'Data sub-categories',
            'publication_categories' => 'Publication categories',
            'publication_sub_categories' => 'Publication sub-categories',
            'data_category_publication_category' => 'Data category ↔ publication category',
            'regions' => 'Regions',
            'country' => 'Countries',
            'world_countries' => 'World countries reference',
            'administrative_units' => 'Administrative units',
            'geographical_scope' => 'Geographical scopes',
        ],
        'Communities of practice' => [
            'community_of_practices' => 'Communities',
            'community_of_practice_members' => 'Community members',
            'community_of_practice_tags' => 'Community tags',
            'community_invitations' => 'Community invitations',
        ],
        'Publications' => [
            'publication' => 'Publications',
            'publication_attachments' => 'Publication attachments',
            'publication_tags' => 'Publication tags',
            'publication_comments' => 'Publication comments',
            'publication_summaries' => 'Publication summaries',
            'publication_community_of_practices' => 'Publication ↔ communities',
            'publication_countries' => 'Publication ↔ countries',
            'geographical_scope_publication' => 'Publication geographical scope',
            'publication_approval_logs' => 'Publication approval logs',
            'publication_views' => 'Publication view counts',
            'custom_attachments' => 'Custom attachments (forums, etc.)',
        ],
        'Forums' => [
            'forums' => 'Forum threads',
            'forum_comments' => 'Forum comments',
            'forum_tags' => 'Forum tags',
            'forum_community_of_practices' => 'Forum ↔ communities',
            'forum_approval_logs' => 'Forum approval logs',
            'forum_likes' => 'Forum likes',
            'forum_comment_likes' => 'Forum comment likes',
            'forum_subscriptions' => 'Forum subscriptions',
            'forum_engagements' => 'Forum engagements',
        ],
        'Content requests & events' => [
            'content_requests' => 'Content requests',
            'content_request_referral_targets' => 'Content request referral targets',
            'content_request_referral_messages' => 'Content request referral messages',
            'events' => 'Events',
            'event_tags' => 'Event tags',
            'facts' => 'Facts',
        ],
        'Courses, tools & assets' => [
            'course_categories' => 'Course categories',
            'courses' => 'Courses',
            'tool_categories' => 'Tool categories',
            'tools' => 'Tools',
            'expert_types' => 'Expert types',
            'data_records' => 'Data records',
        ],
        'Other' => [
            'subscribes' => 'Mailing list subscribers',
            'oauth_clients' => 'OAuth API clients (Passport)',
        ],
    ],

    'backup_schedule_time' => '01:30',

    /*
    | Offsite SQL backup upload (weekly). Separate from publication file storage.
    */
    'offsite_backup_drivers' => [
        's3' => 'Amazon S3 (or S3-compatible)',
        'gcs' => 'Google Cloud Storage',
        'azure' => 'Azure Blob Storage',
        'sftp' => 'SFTP',
        'ftp' => 'FTP / FTPS',
    ],

    'offsite_backup_schedule_day' => 0,
    'offsite_backup_schedule_time' => '02:15',

    /*
    | Staff portal ecosystem (sibling repo). Managed from Storage Management UI.
    */
    'staff_ecosystem' => [
        'enabled' => filter_var(env('HUB_STAFF_STORAGE_ENABLED', true), FILTER_VALIDATE_BOOL),
        'repo_root' => rtrim(env('STAFF_REPO_ROOT', '/opt/homebrew/var/www/staff'), '/\\'),
        'host_data_root' => env('STAFF_HOST_DATA_ROOT', '/var/staffdata'),
        'host_data_root_windows' => env('STAFF_HOST_DATA_ROOT_WINDOWS', 'C:\\staffdata'),
        'site_id' => env('STAFF_SITE_ID', ''),
        'base_url' => env('STAFF_BASE_URL', 'http://localhost/staff'),
        'backup_root' => env('STAFF_FILES_BACKUP_ROOT', ''),
        'backup_retention_days' => (int) env('STAFF_FILES_BACKUP_RETENTION_DAYS', 30),

        'modules' => [
            'ci' => [
                'label' => 'CodeIgniter (legacy staff)',
                'legacy_relative' => 'uploads',
                'host_subdir' => 'ci',
                'env_root' => 'STAFF_PORTAL_UPLOADS_ROOT',
                'migrate_script' => 'scripts/storage/migrate-ci-uploads.sh',
            ],
            'apm' => [
                'label' => 'APM',
                'legacy_relative' => 'apm/storage/app/public',
                'host_subdir' => 'apm',
                'env_root' => 'STAFF_APM_FILES_ROOT',
                'migrate_script' => 'scripts/storage/migrate-apm-uploads.sh',
            ],
            'helpdesk' => [
                'label' => 'Helpdesk',
                'legacy_relative' => 'helpdesk/backend/storage/app/public',
                'host_subdir' => 'helpdesk',
                'env_root' => 'STAFF_HELPDESK_FILES_ROOT',
                'migrate_script' => 'scripts/storage/migrate-helpdesk-uploads.sh',
            ],
            'staff-portal' => [
                'label' => 'Staff Portal (Laravel)',
                'legacy_relative' => 'staff-portal/storage/app/public',
                'host_subdir' => 'staff-portal',
                'env_root' => 'STAFF_PORTAL_MODULE_FILES_ROOT',
                'migrate_script' => 'scripts/storage/migrate-staff-portal-uploads.sh',
            ],
        ],
    ],

];
