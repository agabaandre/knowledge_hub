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
    ],

    /*
    | Tables included in SQL backups (order respects foreign keys on restore).
    */
    'backup_tables' => [
        'users',
        'author',
        'thematic_area',
        'sub_thematic_area',
        'data_category',
        'publication_categories',
        'publication_sub_categories',
        'regions',
        'country',
        'community_of_practices',
        'community_of_practice_members',
        'community_of_practice_tags',
        'community_invitations',
        'publication',
        'publication_attachments',
        'publication_tags',
        'publication_comments',
        'publication_summaries',
        'publication_community_of_practices',
        'publication_countries',
        'geographical_scope_publication',
        'publication_approval_logs',
        'forums',
        'forum_comments',
        'forum_tags',
        'forum_community_of_practices',
        'forum_approval_logs',
    ],

    'backup_schedule_time' => '01:30',

];
