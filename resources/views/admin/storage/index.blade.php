@extends(admin_layout())

@php
    $driverLabel = $drivers[$settings->files_driver] ?? $settings->files_driver;
    $linkStatusOk = $publicStorageLinkOk && $settings->files_driver === 'internal';
    $migrationRunning = $settings->migration_status === 'running';
    $purgeBytesMb = ($legacyPurgePreview['bytes'] ?? 0) > 0
        ? number_format(($legacyPurgePreview['bytes'] ?? 0) / 1048576, 1)
        : '0';
@endphp

@section('styles')
<style>
    .storage-shell {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .storage-hero {
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #0d7a38 100%);
        color: #fff;
        padding: 1.75rem 2rem;
    }

    .storage-hero h1 {
        font-size: 1.65rem;
        font-weight: 600;
        margin: 0 0 0.35rem;
        color: #fff;
    }

    .storage-hero p {
        margin: 0;
        opacity: 0.92;
        max-width: 52rem;
    }

    .storage-kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1.25rem 2rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .storage-kpi {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.9rem 1rem;
        min-height: 88px;
    }

    .storage-kpi-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .storage-kpi-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.35;
    }

    .storage-kpi-meta {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .storage-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        padding: 0 1.5rem;
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
    }

    .storage-tab {
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 500;
        font-size: 0.9rem;
        padding: 0.85rem 1.1rem;
        border-bottom: 3px solid transparent;
        transition: color 0.2s, background 0.2s, border-color 0.2s;
    }

    .storage-tab:hover {
        color: var(--theme-color-primary, #119A48);
        background: rgba(17, 154, 72, 0.06);
    }

    .storage-tab.active {
        color: var(--theme-color-primary, #119A48);
        background: #fff;
        border-bottom-color: var(--theme-color-primary, #119A48);
    }

    .storage-body {
        padding: 1.75rem 2rem 2rem;
    }

    .storage-section {
        display: none;
    }

    .storage-section.active {
        display: block;
    }

    .storage-section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .storage-section-title i {
        color: var(--theme-color-primary, #119A48);
    }

    .storage-panel {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        margin-bottom: 1.25rem;
    }

    .storage-panel-header {
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid #e2e8f0;
        background: #fafbfc;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .storage-panel-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }

    .storage-panel-body {
        padding: 1.15rem;
    }

    .storage-path {
        display: block;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.78rem;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.45rem 0.6rem;
        color: #334155;
        word-break: break-all;
    }

    .storage-status-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .storage-status-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .storage-status-list li:last-child {
        border-bottom: none;
    }

    .storage-status-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.85rem;
    }

    .storage-status-icon.ok { background: #dcfce7; color: #15803d; }
    .storage-status-icon.warn { background: #fef3c7; color: #b45309; }
    .storage-status-icon.bad { background: #fee2e2; color: #b91c1c; }
    .storage-status-icon.info { background: #e0f2fe; color: #0369a1; }

    .storage-action-card {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 1.15rem;
        margin-bottom: 1rem;
        background: #fff;
    }

    .storage-action-card.warning { border-color: #fcd34d; background: #fffbeb; }
    .storage-action-card.success { border-color: #86efac; background: #f0fdf4; }
    .storage-action-card.neutral { background: #f8fafc; }

    .storage-action-card h4 {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: #1e293b;
    }

    .storage-step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        background: var(--theme-color-primary, #119A48);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        margin-right: 0.35rem;
    }

    .storage-dev-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .storage-dev-block {
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        font-size: 0.82rem;
    }

    .storage-dev-block h5 {
        color: #94a3b8;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 0.65rem;
    }

    .storage-dev-block code,
    .storage-dev-block pre {
        color: #a5f3fc;
        background: transparent;
        border: none;
        padding: 0;
        margin: 0;
        font-size: 0.8rem;
        white-space: pre-wrap;
        word-break: break-all;
    }

    .storage-dev-block ul {
        margin: 0;
        padding-left: 1.1rem;
    }

    .storage-dev-block li {
        margin-bottom: 0.35rem;
    }

    .storage-browse-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.85rem;
    }

    .storage-browse-list {
        max-height: 360px;
        overflow: auto;
    }

    .storage-browse-list .list-group-item {
        border-left: none;
        border-right: none;
        cursor: default;
        font-size: 0.88rem;
    }

    .storage-browse-list .list-group-item-action {
        cursor: pointer;
    }

    .storage-form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
        margin-top: 1rem;
    }

    @media (max-width: 767px) {
        .storage-hero,
        .storage-kpi-row,
        .storage-body {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .storage-tabs {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .storage-tab {
            padding: 0.65rem 0.75rem;
            font-size: 0.82rem;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header mb-3">
    <div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">{{ __('admin_nav.settings') }}</a></li>
            <li class="breadcrumb-item active">{{ __('admin_nav.storage_management') }}</li>
        </ol>
    </div>
</div>

@if(session('alert-success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('alert-success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
@endif
@if(session('alert-danger'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('alert-danger') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
@endif

@if($isUsingLegacyUploadFallback)
    <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="fa fa-exclamation-triangle mt-1"></i>
        <div>
            <strong>Legacy storage in use.</strong> Uploads are still under <code>{{ $legacyFilesRoot }}</code> while the host path is empty.
            Open the <a href="#" class="storage-tab-link" data-storage-tab="migration">Migration</a> tab to copy files to <code>{{ $configuredFilesRoot }}</code>.
        </div>
    </div>
@elseif(! $publicStorageLinkOk && $settings->files_driver === 'internal')
    <div class="alert alert-danger d-flex align-items-start gap-2">
        <i class="fa fa-unlink mt-1"></i>
        <div>
            <strong>public/storage is not linked correctly.</strong> Active root: <code>{{ $filesRoot }}</code>.
            See the <a href="#" class="storage-tab-link" data-storage-tab="server">Server setup</a> tab to run <code>php artisan hub:link-storage</code>.
        </div>
    </div>
@endif

<div class="storage-shell">
    <div class="storage-hero">
        <h1>{{ __('admin_nav.storage_management') }}</h1>
        <p>Manage where publications, forum attachments, and SQL backups are stored. Files stay outside the application deploy tree so updates do not affect uploads.</p>
    </div>

    <div class="storage-kpi-row">
        <div class="storage-kpi">
            <div class="storage-kpi-label">Files driver</div>
            <div class="storage-kpi-value">{{ $driverLabel }}</div>
            <div class="storage-kpi-meta">Site ID: <code>{{ $siteStorageId }}</code></div>
        </div>
        <div class="storage-kpi">
            <div class="storage-kpi-label">Active files root</div>
            <div class="storage-kpi-value text-truncate" title="{{ $filesRoot }}">{{ basename($filesRoot) ?: $filesRoot }}</div>
            <div class="storage-kpi-meta text-truncate" title="{{ $filesRoot }}">{{ $filesRoot }}</div>
        </div>
        <div class="storage-kpi">
            <div class="storage-kpi-label">Public URL link</div>
            <div class="storage-kpi-value">
                @if($settings->files_driver !== 'internal')
                    <span class="badge bg-info">External driver</span>
                @elseif($linkStatusOk)
                    <span class="badge bg-success">Linked</span>
                @else
                    <span class="badge bg-danger">Needs fix</span>
                @endif
            </div>
            <div class="storage-kpi-meta">public/storage → files root</div>
        </div>
        <div class="storage-kpi">
            <div class="storage-kpi-label">Migration</div>
            <div class="storage-kpi-value">
                @if($migrationRunning)
                    <span class="badge bg-warning text-dark">Running</span>
                @elseif($settings->migration_status === 'completed')
                    <span class="badge bg-success">Completed</span>
                @else
                    <span class="badge bg-secondary">Idle</span>
                @endif
            </div>
            <div class="storage-kpi-meta">
                @if($settings->migration_message)
                    {{ Str::limit($settings->migration_message, 48) }}
                @else
                    No recent migration activity
                @endif
            </div>
        </div>
    </div>

    <nav class="storage-tabs" role="tablist" aria-label="Storage management sections">
        <button type="button" class="storage-tab active" data-storage-tab="overview">Overview</button>
        <button type="button" class="storage-tab" data-storage-tab="config">Configuration</button>
        <button type="button" class="storage-tab" data-storage-tab="migration">Migration</button>
        <button type="button" class="storage-tab" data-storage-tab="backups">SQL backups</button>
        <button type="button" class="storage-tab" data-storage-tab="browse">Browse files</button>
        <button type="button" class="storage-tab" data-storage-tab="server">Server setup</button>
    </nav>

    <div class="storage-body">
        {{-- Overview --}}
        <section class="storage-section active" id="storage-section-overview">
            <h2 class="storage-section-title"><i class="fa fa-chart-pie"></i> System status</h2>

            <ul class="storage-status-list storage-panel mb-4">
                <li>
                    <span class="storage-status-icon {{ $settings->files_driver === 'internal' ? 'ok' : 'info' }}"><i class="fa fa-hdd"></i></span>
                    <div>
                        <strong>File storage</strong>
                        <div class="text-muted small">{{ $driverLabel }}</div>
                        @if($settings->files_driver === 'internal')
                            <span class="storage-path mt-1">{{ $filesRoot }}</span>
                        @endif
                    </div>
                </li>
                <li>
                    <span class="storage-status-icon {{ $isUsingLegacyUploadFallback ? 'warn' : 'ok' }}"><i class="fa fa-folder-open"></i></span>
                    <div>
                        <strong>Upload location</strong>
                        <div class="text-muted small">
                            @if($isUsingLegacyUploadFallback)
                                Serving from legacy path until host migration completes.
                            @elseif($configuredFilesRoot !== $filesRoot)
                                Configured host path differs from active root — check migration status.
                            @else
                                Uploads are on the configured host path.
                            @endif
                        </div>
                        @if($configuredFilesRoot !== $legacyFilesRoot)
                            <span class="storage-path mt-1">{{ $configuredFilesRoot }}</span>
                        @endif
                    </div>
                </li>
                <li>
                    <span class="storage-status-icon {{ $linkStatusOk || $settings->files_driver !== 'internal' ? 'ok' : 'bad' }}"><i class="fa fa-link"></i></span>
                    <div>
                        <strong>Browser URLs</strong>
                        <div class="text-muted small">
                            @if($linkStatusOk)
                                <code>/storage/…</code> URLs resolve via public/storage symlink.
                            @elseif($settings->files_driver === 'internal')
                                Symlink needs repair — files may still work via <code>/hub-media/…</code>.
                            @else
                                External driver serves via configured cloud URLs.
                            @endif
                        </div>
                    </div>
                </li>
                <li>
                    <span class="storage-status-icon info"><i class="fa fa-database"></i></span>
                    <div>
                        <strong>SQL backups</strong>
                        <div class="text-muted small">
                            @if($settings->auto_sql_backup)
                                Daily incremental backup enabled (retain {{ $settings->sql_backup_retention_days }} days).
                            @else
                                Automatic backups are off.
                            @endif
                            @if($settings->last_sql_backup_at)
                                Last run: {{ $settings->last_sql_backup_at->format('M j, Y H:i') }}.
                            @endif
                        </div>
                        <span class="storage-path mt-1">{{ $sqlBackupRoot }}</span>
                    </div>
                </li>
            </ul>

            <h2 class="storage-section-title"><i class="fa fa-bolt"></i> Quick actions</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="storage-action-card neutral h-100">
                        <h4><i class="fa fa-sliders-h text-primary me-1"></i> Change driver or paths</h4>
                        <p class="small text-muted mb-2">Switch between internal host storage and cloud providers.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary storage-tab-link" data-storage-tab="config">Open configuration</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="storage-action-card neutral h-100">
                        <h4><i class="fa fa-exchange-alt text-warning me-1"></i> Move or copy files</h4>
                        <p class="small text-muted mb-2">Host-path migration, legacy cleanup, or cloud upload.</p>
                        <button type="button" class="btn btn-sm btn-outline-warning storage-tab-link" data-storage-tab="migration">Open migration</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="storage-action-card neutral h-100">
                        <h4><i class="fa fa-life-ring text-success me-1"></i> Backup or restore database</h4>
                        <p class="small text-muted mb-2">Incremental or full SQL dumps to a separate folder.</p>
                        <button type="button" class="btn btn-sm btn-outline-success storage-tab-link" data-storage-tab="backups">Open backups</button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Configuration --}}
        <section class="storage-section" id="storage-section-config">
            <h2 class="storage-section-title"><i class="fa fa-cog"></i> Storage configuration</h2>
            <div class="storage-panel">
                <div class="storage-panel-body">
                    <form method="post" action="{{ route('admin.storage.update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label fw-semibold">Files driver</label>
                                <select name="files_driver" id="filesDriver" class="form-control">
                                    @foreach($drivers as $key => $label)
                                        <option value="{{ $key }}" {{ old('files_driver', $settings->files_driver) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Internal storage keeps files on this server. Cloud drivers copy uploads to S3, Azure, GCS, SharePoint, or SFTP.</small>
                            </div>
                            <div class="col-lg-6 mb-3" id="internalRootGroup">
                                <label class="form-label fw-semibold">Host files root</label>
                                <input type="text" name="local_files_root" class="form-control" value="{{ old('local_files_root', $settings->local_files_root) }}" placeholder="{{ $recommended['files'] }}">
                                <small class="text-muted">Recommended: <code>{{ $recommended['files'] }}</code> — outside the git/deploy tree.</small>
                            </div>
                        </div>

                        <div id="driverSetupNote" class="alert alert-secondary small py-2 mb-3" style="display:none;"></div>
                        <div id="driverPackageNote" class="alert alert-warning small py-2 mb-3" style="display:none;"></div>

                        <div id="cloudConfigGroup" class="storage-panel mb-3" style="display:none;">
                            <div class="storage-panel-header">
                                <h3>Cloud credentials</h3>
                            </div>
                            <div class="storage-panel-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3,gcs,azure,sharepoint,sftp"><label class="form-label">Root prefix / folder</label><input type="text" name="cloud_root_prefix" class="form-control" value="{{ old('cloud_root_prefix', $settings->cloud_config['root_prefix'] ?? 'khub') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Access key</label><input type="text" name="cloud_key" class="form-control" value="{{ old('cloud_key', $settings->cloud_config['key'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Secret key</label><input type="password" name="cloud_secret" class="form-control" value=""></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Region</label><input type="text" name="cloud_region" class="form-control" value="{{ old('cloud_region', $settings->cloud_config['region'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Bucket</label><input type="text" name="cloud_bucket" class="form-control" value="{{ old('cloud_bucket', $settings->cloud_config['bucket'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Endpoint (optional)</label><input type="text" name="cloud_endpoint" class="form-control" value="{{ old('cloud_endpoint', $settings->cloud_config['endpoint'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="s3,gcs,azure"><label class="form-label">Public URL (optional)</label><input type="text" name="cloud_url" class="form-control" value="{{ old('cloud_url', $settings->cloud_config['url'] ?? '') }}"></div>
                                    <div class="col-md-12 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">GCS project ID</label><input type="text" name="gcs_project_id" class="form-control" value="{{ old('gcs_project_id', $settings->cloud_config['project_id'] ?? '') }}" placeholder="my-gcp-project"></div>
                                    <div class="col-md-12 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">Service account key file path</label><input type="text" name="gcs_key_file_path" class="form-control" value="{{ old('gcs_key_file_path', $settings->cloud_config['key_file_path'] ?? '') }}" placeholder="/etc/khub/gcs-service-account.json"><small class="text-muted">JSON key on the server, readable by the web/queue user.</small></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">GCS bucket</label><input type="text" name="gcs_bucket" class="form-control" value="{{ old('gcs_bucket', $settings->cloud_config['bucket'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">Storage API URI (optional)</label><input type="text" name="gcs_storage_api_uri" class="form-control" value="{{ old('gcs_storage_api_uri', $settings->cloud_config['storage_api_uri'] ?? '') }}" placeholder="https://storage.googleapis.com"></div>
                                    <div class="col-md-12 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Connection string (recommended)</label><input type="password" name="cloud_connection_string" class="form-control" value="" placeholder="DefaultEndpointsProtocol=https;AccountName=…"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Azure account name</label><input type="text" name="cloud_account_name" class="form-control" value="{{ old('cloud_account_name', $settings->cloud_config['account_name'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Azure account key</label><input type="password" name="cloud_account_key" class="form-control" value=""></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Container</label><input type="text" name="cloud_container" class="form-control" value="{{ old('cloud_container', $settings->cloud_config['container'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP host</label><input type="text" name="cloud_host" class="form-control" value="{{ old('cloud_host', $settings->cloud_config['host'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP port</label><input type="number" name="cloud_port" class="form-control" value="{{ old('cloud_port', $settings->cloud_config['port'] ?? 22) }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP username</label><input type="text" name="cloud_username" class="form-control" value="{{ old('cloud_username', $settings->cloud_config['username'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP password</label><input type="password" name="cloud_password" class="form-control" value=""><small class="text-muted">Leave blank when using a private key.</small></div>
                                    <div class="col-md-12 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SSH private key path</label><input type="text" name="cloud_private_key" class="form-control" value="{{ old('cloud_private_key', $settings->cloud_config['private_key'] ?? '') }}" placeholder="/etc/khub/keys/sftp_id_rsa"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">Key passphrase (optional)</label><input type="password" name="cloud_passphrase" class="form-control" value=""></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Tenant ID</label><input type="text" name="sharepoint_tenant_id" class="form-control" value="{{ old('sharepoint_tenant_id', $settings->cloud_config['tenant_id'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Client ID</label><input type="text" name="sharepoint_client_id" class="form-control" value="{{ old('sharepoint_client_id', $settings->cloud_config['client_id'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Client secret</label><input type="password" name="sharepoint_client_secret" class="form-control" value=""></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Site hostname</label><input type="text" name="sharepoint_site_hostname" class="form-control" value="{{ old('sharepoint_site_hostname', $settings->cloud_config['site_hostname'] ?? '') }}" placeholder="contoso.sharepoint.com"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Site path</label><input type="text" name="sharepoint_site_path" class="form-control" value="{{ old('sharepoint_site_path', $settings->cloud_config['site_path'] ?? '') }}" placeholder="sites/KnowledgeHub"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Site ID (optional)</label><input type="text" name="sharepoint_site_id" class="form-control" value="{{ old('sharepoint_site_id', $settings->cloud_config['site_id'] ?? '') }}"></div>
                                    <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Drive ID (optional)</label><input type="text" name="sharepoint_drive_id" class="form-control" value="{{ old('sharepoint_drive_id', $settings->cloud_config['drive_id'] ?? '') }}"></div>
                                </div>
                            </div>
                        </div>

                        <div class="storage-panel mb-0">
                            <div class="storage-panel-header"><h3>SQL backup settings</h3></div>
                            <div class="storage-panel-body">
                                <div class="row">
                                    <div class="col-lg-8 mb-3">
                                        <label class="form-label fw-semibold">SQL backup root</label>
                                        <input type="text" name="sql_backup_root" class="form-control" value="{{ old('sql_backup_root', $settings->sql_backup_root) }}" placeholder="{{ $recommended['sql_backups'] }}">
                                        <small class="text-muted">Separate from publication files. Default: <code>{{ $recommended['sql_backups'] }}</code></small>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fw-semibold">Retain backups (days)</label>
                                        <input type="number" name="sql_backup_retention_days" class="form-control" min="1" max="3650" value="{{ old('sql_backup_retention_days', $settings->sql_backup_retention_days) }}">
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="auto_sql_backup" value="0">
                                    <input class="form-check-input" type="checkbox" name="auto_sql_backup" value="1" id="autoSqlBackup" {{ old('auto_sql_backup', $settings->auto_sql_backup) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autoSqlBackup">Run automatic daily incremental SQL backup</label>
                                </div>
                            </div>
                        </div>

                        <div class="storage-form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save settings</button>
                            <button type="button" class="btn btn-outline-secondary" id="testConnectionBtn"><i class="fa fa-plug me-1"></i> Test connection</button>
                            <span id="testConnectionResult" class="small"></span>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- Migration --}}
        <section class="storage-section" id="storage-section-migration">
            <h2 class="storage-section-title"><i class="fa fa-route"></i> File migration</h2>
            <p class="text-muted small mb-4">Follow these steps in order when moving from legacy in-app storage to a host path, then optionally to cloud storage.</p>

            @if($needsLegacyToHostMigration)
            <div class="storage-action-card warning">
                <h4><span class="storage-step-badge">1</span> Migrate to host files root</h4>
                <p class="small text-muted mb-2">
                    Copies publication and forum uploads from legacy <code>storage/app/public</code> to
                    <code>{{ $configuredFilesRoot }}</code>. Originals are kept until you verify the copy.
                </p>
                <p class="small mb-2"><strong>Source:</strong> <span class="storage-path d-inline-block">{{ $legacyFilesRoot }}</span></p>
                @if($migrationRunning && $settings->files_driver === 'internal')
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" id="hostMigrationProgressBar" style="width:0%"></div>
                    </div>
                    <p class="small mb-2" id="hostMigrationProgressLabel">Migration in progress…</p>
                @elseif($settings->migration_message && $settings->files_driver === 'internal')
                    <p class="small text-muted mb-2">{{ $settings->migration_message }}</p>
                @endif
                <div class="d-flex flex-wrap gap-2">
                    <form method="post" action="{{ route('admin.storage.migrate-host') }}" class="d-inline" onsubmit="return confirm('Copy all uploads to the host files root?');">
                        @csrf
                        <input type="hidden" name="mode" value="queue">
                        <button type="submit" class="btn btn-warning btn-sm" {{ $migrationRunning ? 'disabled' : '' }}><i class="fa fa-clock me-1"></i> Queue migration</button>
                    </form>
                    <form method="post" action="{{ route('admin.storage.migrate-host') }}" class="d-inline" onsubmit="return confirm('Run host migration now? This may take a long time.');">
                        @csrf
                        <input type="hidden" name="mode" value="sync">
                        <button type="submit" class="btn btn-outline-warning btn-sm" {{ $migrationRunning ? 'disabled' : '' }}><i class="fa fa-play me-1"></i> Run now</button>
                    </form>
                </div>
            </div>
            @else
            <div class="storage-action-card success mb-3">
                <h4><span class="storage-step-badge">1</span> Host path migration</h4>
                <p class="small mb-0 text-muted">No legacy-to-host copy is pending. Uploads are on the configured host path or legacy storage is not in use.</p>
            </div>
            @endif

            @if($legacyPurgePreview['can_purge'] ?? false)
            <div class="storage-action-card success">
                <h4><span class="storage-step-badge">2</span> Remove legacy copies</h4>
                <p class="small text-muted mb-2">
                    After verifying files on <code>{{ $legacyPurgePreview['host_root'] }}</code>, delete verified duplicates under
                    <code>{{ $legacyPurgePreview['legacy_root'] }}</code> to free disk space.
                </p>
                <p class="small mb-3">
                    <strong>{{ number_format($legacyPurgePreview['verified']) }}</strong> verified file(s) ·
                    <strong>~{{ $purgeBytesMb }} MB</strong> reclaimable
                    @if(($legacyPurgePreview['skipped'] ?? 0) > 0)
                        · <span class="text-danger">{{ number_format($legacyPurgePreview['skipped']) }} not verified (purge blocked)</span>
                    @endif
                </p>
                <form method="post" action="{{ route('admin.storage.purge-legacy') }}" class="d-inline" onsubmit="return confirm('Delete verified legacy upload copies? This cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm" {{ $canPurgeLegacyInternalStorage ? '' : 'disabled' }}><i class="fa fa-trash-alt me-1"></i> Remove legacy copies</button>
                </form>
            </div>
            @elseif(! $needsLegacyToHostMigration && ($legacyPurgePreview['total'] ?? 0) === 0)
            <div class="storage-action-card neutral">
                <h4><span class="storage-step-badge">2</span> Remove legacy copies</h4>
                <p class="small text-muted mb-0">No legacy upload duplicates detected under <code>{{ $legacyFilesRoot }}</code>.</p>
            </div>
            @endif

            <div class="storage-action-card neutral mt-3">
                <h4><span class="storage-step-badge">{{ ($needsLegacyToHostMigration || ($legacyPurgePreview['can_purge'] ?? false)) ? '3' : '2' }}</span> Migrate to cloud / external storage</h4>
                <p class="small text-muted mb-2">After switching the files driver in Configuration, copy existing uploads to S3, Azure, GCS, SharePoint, or SFTP.</p>
                @if($migrationRunning && $settings->files_driver !== 'internal')
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="migrationProgressBar" style="width:0%"></div>
                    </div>
                    <p class="small mb-2" id="migrationProgressLabel">Migration in progress…</p>
                @elseif($settings->migration_message && $settings->files_driver !== 'internal')
                    <p class="small text-muted mb-2">{{ $settings->migration_message }}</p>
                @endif
                <div class="d-flex flex-wrap gap-2">
                    <form method="post" action="{{ route('admin.storage.migrate') }}" class="d-inline" onsubmit="return confirm('Start file migration to external storage?');">
                        @csrf
                        <input type="hidden" name="mode" value="queue">
                        <button type="submit" class="btn btn-warning btn-sm" {{ $settings->files_driver === 'internal' ? 'disabled' : '' }}>Queue migration</button>
                    </form>
                    <form method="post" action="{{ route('admin.storage.migrate') }}" class="d-inline" onsubmit="return confirm('Run migration synchronously? This may take a long time.');">
                        @csrf
                        <input type="hidden" name="mode" value="sync">
                        <button type="submit" class="btn btn-outline-warning btn-sm" {{ $settings->files_driver === 'internal' ? 'disabled' : '' }}>Run now</button>
                    </form>
                </div>
                @if($settings->files_driver === 'internal')
                    <p class="small text-muted mt-2 mb-0">Select a cloud driver under Configuration and save before running this step.</p>
                @endif
            </div>
        </section>

        {{-- Backups --}}
        <section class="storage-section" id="storage-section-backups">
            <h2 class="storage-section-title"><i class="fa fa-database"></i> SQL backups &amp; restore</h2>
            <div class="row g-3">
                <div class="col-lg-5">
                    <div class="storage-panel h-100">
                        <div class="storage-panel-header"><h3>Run backup</h3></div>
                        <div class="storage-panel-body">
                            <p class="small text-muted">Backup root: <span class="storage-path">{{ $sqlBackupRoot }}</span></p>
                            @if($settings->last_sql_backup_at)
                                <p class="small">Last backup: <strong>{{ $settings->last_sql_backup_at->format('Y-m-d H:i') }}</strong></p>
                            @endif
                            <form method="post" action="{{ route('admin.storage.backup') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="incremental" value="1">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-sync me-1"></i> Incremental</button>
                            </form>
                            <form method="post" action="{{ route('admin.storage.backup') }}" class="d-inline ms-1">
                                @csrf
                                <input type="hidden" name="incremental" value="0">
                                <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fa fa-copy me-1"></i> Full backup</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="storage-panel">
                        <div class="storage-panel-header"><h3>Restore from backup</h3></div>
                        <div class="storage-panel-body">
                            <form method="post" action="{{ route('admin.storage.restore') }}" id="restoreBackupForm">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Backup folder</label>
                                    <select name="backup_path" id="restoreBackupPath" class="form-control" required>
                                        <option value="">Select backup…</option>
                                        @foreach($backups as $backup)
                                            <option value="{{ $backup['path'] }}">{{ $backup['type'] }} / {{ $backup['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold mb-0">Tables to restore</label>
                                        <span class="small">
                                            <a href="#" id="restoreSelectAll">All</a> ·
                                            <a href="#" id="restoreSelectNone">None</a> ·
                                            <a href="#" id="restoreSelectInBackup">In backup only</a>
                                        </span>
                                    </div>
                                    <div class="border rounded p-2" style="max-height:220px;overflow:auto;" id="restoreTableList">
                                        @foreach($backupTableGroups as $group => $tables)
                                            <div class="small fw-bold text-muted mt-1 mb-1">{{ $group }}</div>
                                            @foreach($tables as $table => $label)
                                                <div class="form-check restore-table-row" data-table="{{ $table }}">
                                                    <input class="form-check-input restore-table-cb" type="checkbox" name="restore_tables[]" value="{{ $table }}" id="restore_{{ $table }}" checked>
                                                    <label class="form-check-label small" for="restore_{{ $table }}">{{ $label }} <code class="text-muted">{{ $table }}</code></label>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                    <p class="small text-muted mb-0 mt-1">KPI / OWID tables are not backed up: <code>{{ implode('</code>, <code>', $kpiExcludedTables) }}</code></p>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="hidden" name="only_empty_tables" value="0">
                                    <input class="form-check-input" type="checkbox" name="only_empty_tables" value="1" id="onlyEmptyTables" checked>
                                    <label class="form-check-label" for="onlyEmptyTables">Only restore into empty tables (recommended)</label>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm" id="restoreSubmitBtn"><i class="fa fa-undo me-1"></i> Restore selected tables</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Browse --}}
        <section class="storage-section" id="storage-section-browse">
            <h2 class="storage-section-title"><i class="fa fa-folder-tree"></i> Browse uploaded files</h2>
            <div class="storage-panel">
                <div class="storage-browse-toolbar">
                    <span class="text-muted">Content area</span>
                    <select id="browseArea" class="form-control form-control-sm" style="width:auto; min-width: 180px;">
                        @foreach($contentAreas as $key => $prefix)
                            <option value="{{ $key }}">{{ str_replace('_', ' ', ucfirst($key)) }} ({{ $prefix }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="storage-browse-toolbar border-top-0">
                    <span class="text-muted"><i class="fa fa-folder-open me-1"></i> Path</span>
                    <code id="browsePath" class="small">/</code>
                </div>
                <ul class="list-group list-group-flush storage-browse-list" id="browseList"></ul>
            </div>
            <p class="small text-muted mt-2 mb-0">Active files root: <code>{{ $filesRoot }}</code></p>
        </section>

        {{-- Server setup (developer) --}}
        <section class="storage-section" id="storage-section-server">
            <h2 class="storage-section-title"><i class="fa fa-terminal"></i> Server setup &amp; developer reference</h2>
            <p class="text-muted small mb-4">Commands and paths for DevOps. End users normally only need the Overview and Configuration tabs.</p>

            <div class="storage-dev-grid mb-4">
                <div class="storage-dev-block">
                    <h5>Path layout</h5>
                    <pre>/var/khubdata/{{ $siteStorageId }}/
├── files/          ← uploads (public/storage symlink)
└── backups/sql/    ← SQL dumps</pre>
                </div>
                <div class="storage-dev-block">
                    <h5>Environment (.env)</h5>
                    <ul>
                        <li><code>HUB_SITE_ID</code> — override site storage ID</li>
                        <li><code>HUB_FILES_ROOT</code> — full files path override</li>
                        <li><code>HUB_SQL_BACKUP_ROOT</code> — SQL backup path override</li>
                    </ul>
                </div>
                <div class="storage-dev-block">
                    <h5>Docker volume</h5>
                    <pre>volumes:
  - ./khubdata:/var/khubdata</pre>
                </div>
            </div>

            <div class="storage-panel mb-4">
                <div class="storage-panel-header"><h3>Artisan commands</h3></div>
                <div class="storage-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Command</th><th>Purpose</th></tr>
                            </thead>
                            <tbody class="small">
                                <tr><td><code>php artisan hub:link-storage</code></td><td>Link public/storage to the active files root</td></tr>
                                <tr><td><code>php artisan hub:recover-storage</code></td><td>Diagnose symlink and legacy path issues</td></tr>
                                <tr><td><code>php artisan hub:recover-storage --sync-legacy</code></td><td>Merge old storage/uploads into app/public</td></tr>
                                <tr><td><code>php artisan hub:migrate-storage-to-host</code></td><td>Copy legacy uploads to host files root</td></tr>
                                <tr><td><code>php artisan hub:purge-legacy-storage --dry-run</code></td><td>Preview legacy copy removal</td></tr>
                                <tr><td><code>php artisan hub:purge-legacy-storage</code></td><td>Delete verified legacy copies</td></tr>
                                <tr><td><code>./fix-storage-permissions.sh</code></td><td>Create dirs, fix permissions, link storage</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="storage-panel mb-4">
                <div class="storage-panel-header"><h3>Composer packages (cloud drivers)</h3></div>
                <div class="storage-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Driver</th><th>Server requirement</th></tr>
                            </thead>
                            <tbody class="small">
                                @foreach($driverPackages as $driverKey => $package)
                                    <tr>
                                        <td>{{ $drivers[$driverKey] ?? $driverKey }}</td>
                                        <td><code>{{ $package }}</code></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="storage-panel">
                <div class="storage-panel-header"><h3>Current paths on this server</h3></div>
                <div class="storage-panel-body">
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Active files root</div>
                            <span class="storage-path">{{ $filesRoot }}</span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Configured host path</div>
                            <span class="storage-path">{{ $configuredFilesRoot }}</span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted mb-1">Legacy app/public</div>
                            <span class="storage-path">{{ $legacyFilesRoot }}</span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted mb-1">SQL backup root</div>
                            <span class="storage-path">{{ $sqlBackupRoot }}</span>
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Full documentation: <code>docs/deployment/STORAGE.md</code> in the repository.</p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function activateStorageTab(tabId) {
        document.querySelectorAll('.storage-tab').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-storage-tab') === tabId);
        });
        document.querySelectorAll('.storage-section').forEach(function (section) {
            section.classList.toggle('active', section.id === 'storage-section-' + tabId);
        });
        if (tabId === 'browse') {
            loadBrowse(browsePath || '');
        }
    }

    document.querySelectorAll('.storage-tab, .storage-tab-link').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            var tab = el.getAttribute('data-storage-tab');
            if (tab) {
                activateStorageTab(tab);
                if (history.replaceState) {
                    history.replaceState(null, '', '#storage-' + tab);
                }
            }
        });
    });

    var hash = (window.location.hash || '').replace(/^#storage-/, '');
    if (hash && document.getElementById('storage-section-' + hash)) {
        activateStorageTab(hash);
    }

    var driver = document.getElementById('filesDriver');
    var cloudGroup = document.getElementById('cloudConfigGroup');
    var internalGroup = document.getElementById('internalRootGroup');
    var driverSetup = @json($driverSetup);
    var driverPackages = @json($driverPackages);

    function toggleDriverFields() {
        var val = driver.value;
        internalGroup.style.display = val === 'internal' ? '' : 'none';
        cloudGroup.style.display = val === 'internal' ? 'none' : 'block';
        document.querySelectorAll('.cloud-field').forEach(function (el) {
            var drivers = (el.getAttribute('data-drivers') || '').split(',');
            el.style.display = drivers.indexOf(val) !== -1 ? '' : 'none';
        });
        var setupEl = document.getElementById('driverSetupNote');
        var pkgEl = document.getElementById('driverPackageNote');
        if (val !== 'internal' && driverSetup[val]) {
            setupEl.style.display = '';
            setupEl.textContent = driverSetup[val];
        } else {
            setupEl.style.display = 'none';
        }
        if (val !== 'internal' && driverPackages[val]) {
            pkgEl.style.display = '';
            pkgEl.innerHTML = '<strong>Server requirement:</strong> <code>' + driverPackages[val] + '</code> — see Server setup tab for all drivers.';
        } else {
            pkgEl.style.display = 'none';
        }
    }
    driver.addEventListener('change', toggleDriverFields);
    toggleDriverFields();

    document.getElementById('testConnectionBtn').addEventListener('click', function () {
        var result = document.getElementById('testConnectionResult');
        result.textContent = 'Testing…';
        fetch('{{ route('admin.storage.test') }}', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
        }).then(function (r) { return r.json(); }).then(function (data) {
            result.textContent = data.message || '';
            result.className = 'small text-' + (data.status === 'ok' ? 'success' : (data.status === 'warning' ? 'warning' : 'danger'));
        });
    });

    function formatBytes(bytes) {
        if (!bytes || bytes < 1024) return (bytes || 0) + ' B';
        var units = ['KB', 'MB', 'GB'];
        var v = bytes;
        for (var i = 0; i < units.length; i++) {
            v /= 1024;
            if (v < 1024) return v.toFixed(v >= 10 ? 0 : 1) + ' ' + units[i];
        }
        return (v / 1024).toFixed(1) + ' TB';
    }

    var browsePath = '';
    function loadBrowse(subPath) {
        var area = document.getElementById('browseArea').value;
        fetch('{{ route('admin.storage.browse') }}?area=' + encodeURIComponent(area) + '&path=' + encodeURIComponent(subPath || ''))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                browsePath = subPath || '';
                document.getElementById('browsePath').textContent = '/' + (data.path || '');
                var list = document.getElementById('browseList');
                list.innerHTML = '';
                if (!data.items || !data.items.length) {
                    var empty = document.createElement('li');
                    empty.className = 'list-group-item text-muted text-center py-4';
                    empty.textContent = 'This folder is empty.';
                    list.appendChild(empty);
                    return;
                }
                if (browsePath) {
                    var up = document.createElement('li');
                    up.className = 'list-group-item list-group-item-action';
                    up.innerHTML = '<i class="fa fa-level-up-alt me-2 text-muted"></i><span>Parent folder</span>';
                    up.addEventListener('click', function () {
                        var parts = browsePath.split('/').filter(Boolean);
                        parts.pop();
                        loadBrowse(parts.join('/'));
                    });
                    list.appendChild(up);
                }
                (data.items || []).forEach(function (item) {
                    var li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center' + (item.type === 'dir' ? ' list-group-item-action' : '');
                    li.innerHTML = '<span><i class="fa fa-' + (item.type === 'dir' ? 'folder text-warning' : 'file text-secondary') + ' me-2"></i>' + item.name + '</span>'
                        + (item.size ? '<span class="badge bg-light text-dark">' + formatBytes(item.size) + '</span>' : '');
                    if (item.type === 'dir') {
                        li.addEventListener('click', function () { loadBrowse(item.path); });
                    }
                    list.appendChild(li);
                });
            });
    }
    document.getElementById('browseArea').addEventListener('change', function () { loadBrowse(''); });

    var backupTablesInDir = [];
    var restorePath = document.getElementById('restoreBackupPath');
    function setRestoreCheckboxes(checked) {
        document.querySelectorAll('.restore-table-cb').forEach(function (cb) { cb.checked = checked; });
    }
    function highlightBackupAvailability() {
        document.querySelectorAll('.restore-table-row').forEach(function (row) {
            var table = row.getAttribute('data-table');
            var inBackup = backupTablesInDir.indexOf(table) !== -1;
            row.style.opacity = restorePath.value && !inBackup ? '0.45' : '1';
        });
    }
    function loadBackupTables() {
        var path = restorePath.value;
        backupTablesInDir = [];
        if (!path) {
            highlightBackupAvailability();
            return;
        }
        fetch('{{ route('admin.storage.backup-tables') }}?path=' + encodeURIComponent(path))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                backupTablesInDir = data.tables || [];
                highlightBackupAvailability();
            });
    }
    if (restorePath) {
        restorePath.addEventListener('change', loadBackupTables);
    }
    document.getElementById('restoreSelectAll').addEventListener('click', function (e) {
        e.preventDefault();
        setRestoreCheckboxes(true);
    });
    document.getElementById('restoreSelectNone').addEventListener('click', function (e) {
        e.preventDefault();
        setRestoreCheckboxes(false);
    });
    document.getElementById('restoreSelectInBackup').addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelectorAll('.restore-table-cb').forEach(function (cb) {
            cb.checked = backupTablesInDir.indexOf(cb.value) !== -1;
        });
    });
    document.getElementById('restoreBackupForm').addEventListener('submit', function (e) {
        var selected = document.querySelectorAll('.restore-table-cb:checked').length;
        if (selected === 0) {
            e.preventDefault();
            alert('Select at least one table to restore.');
            return false;
        }
        return confirm('Restore ' + selected + ' selected table(s) from this backup?');
    });

    @if($migrationRunning)
    function pollMigration(barId, labelId) {
        var bar = document.getElementById(barId);
        var label = document.getElementById(labelId);
        if (!bar || !label) return;
        fetch('{{ route('admin.storage.migration-status') }}')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var total = parseInt(data.total || 0, 10);
                var done = parseInt(data.done || 0, 10);
                var pct = total > 0 ? Math.round((done / total) * 100) : 0;
                bar.style.width = pct + '%';
                label.textContent = done + ' / ' + total + ' files' + (data.message ? ' — ' + data.message : '');
                if (data.status === 'running') setTimeout(function () { pollMigration(barId, labelId); }, 2000);
                else if (data.status === 'completed') setTimeout(function () { window.location.reload(); }, 1500);
            });
    }
    pollMigration('hostMigrationProgressBar', 'hostMigrationProgressLabel');
    pollMigration('migrationProgressBar', 'migrationProgressLabel');
    @endif
})();
</script>
@endsection
