@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('admin_nav.storage_management') }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">{{ __('admin_nav.settings') }}</a></li>
            <li class="breadcrumb-item active">{{ __('admin_nav.storage_management') }}</li>
        </ol>
    </div>
</div>

@if(session('alert-success'))
    <div class="alert alert-success">{{ session('alert-success') }}</div>
@endif
@if(session('alert-danger'))
    <div class="alert alert-danger">{{ session('alert-danger') }}</div>
@endif

<div class="alert alert-info">
    <strong>Site storage ID:</strong> <code>{{ $siteStorageId }}</code>
    — paths are isolated under <code>/var/khubdata/{{ $siteStorageId }}/</code> so multiple hubs on one server or Docker host do not share files.
    Override with <code>HUB_SITE_ID</code> in <code>.env</code> if needed.
</div>

<div class="alert alert-secondary">
    <strong>Recommendation:</strong> Keep <em>internal</em> files on a host path outside the application and container tree
    (Linux: <code>{{ $recommended['files'] }}</code>, Windows: <code>{{ $recommended['files'] }}</code>).
    SQL backups stay separate at <code>{{ $recommended['sql_backups'] }}</code>.
    In Docker, mount a volume at <code>/var/khubdata</code>. <code>public/storage</code> is symlinked to your files root for URL compatibility.
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title mb-0">File storage</h3></div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.storage.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Files driver</label>
                        <select name="files_driver" id="filesDriver" class="form-control">
                            @foreach($drivers as $key => $label)
                                <option value="{{ $key }}" {{ old('files_driver', $settings->files_driver) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="internalRootGroup">
                        <label class="form-label">Host files root (internal)</label>
                        <input type="text" name="local_files_root" class="form-control" value="{{ old('local_files_root', $settings->local_files_root) }}" placeholder="{{ $recommended['files'] }}">
                        <small class="text-muted">Defaults to <code>{{ $recommended['files'] }}</code> when empty. Uploads should not live inside the container or git deploy tree.</small>
                    </div>
                    <div id="driverSetupNote" class="alert alert-secondary small py-2 mb-3" style="display:none;"></div>
                    <div id="driverPackageNote" class="alert alert-warning small py-2 mb-3" style="display:none;"></div>
                    <div id="cloudConfigGroup" class="border rounded p-3 mb-3" style="display:none;">
                        <h6 class="fw-bold">Cloud / external credentials</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3,gcs,azure,sharepoint,sftp"><label class="form-label">Root prefix / folder</label><input type="text" name="cloud_root_prefix" class="form-control" value="{{ old('cloud_root_prefix', $settings->cloud_config['root_prefix'] ?? 'khub') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Access key</label><input type="text" name="cloud_key" class="form-control" value="{{ old('cloud_key', $settings->cloud_config['key'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Secret key</label><input type="password" name="cloud_secret" class="form-control" value=""></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Region</label><input type="text" name="cloud_region" class="form-control" value="{{ old('cloud_region', $settings->cloud_config['region'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Bucket</label><input type="text" name="cloud_bucket" class="form-control" value="{{ old('cloud_bucket', $settings->cloud_config['bucket'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3"><label class="form-label">Endpoint (optional)</label><input type="text" name="cloud_endpoint" class="form-control" value="{{ old('cloud_endpoint', $settings->cloud_config['endpoint'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="s3,gcs,azure"><label class="form-label">Public URL (optional)</label><input type="text" name="cloud_url" class="form-control" value="{{ old('cloud_url', $settings->cloud_config['url'] ?? '') }}"></div>
                            <div class="col-md-12 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">GCS project ID</label><input type="text" name="gcs_project_id" class="form-control" value="{{ old('gcs_project_id', $settings->cloud_config['project_id'] ?? '') }}" placeholder="my-gcp-project"></div>
                            <div class="col-md-12 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">Service account key file path</label><input type="text" name="gcs_key_file_path" class="form-control" value="{{ old('gcs_key_file_path', $settings->cloud_config['key_file_path'] ?? '') }}" placeholder="/etc/khub/gcs-service-account.json"><small class="text-muted">JSON key on the server, readable by the web/queue user. Do not store the key in the database.</small></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">GCS bucket</label><input type="text" name="gcs_bucket" class="form-control" value="{{ old('gcs_bucket', $settings->cloud_config['bucket'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="gcs"><label class="form-label">Storage API URI (optional)</label><input type="text" name="gcs_storage_api_uri" class="form-control" value="{{ old('gcs_storage_api_uri', $settings->cloud_config['storage_api_uri'] ?? '') }}" placeholder="https://storage.googleapis.com"></div>
                            <div class="col-md-12 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Connection string (recommended)</label><input type="password" name="cloud_connection_string" class="form-control" value="" placeholder="DefaultEndpointsProtocol=https;AccountName=…"><small class="text-muted">From Azure Portal → Storage account → Access keys. Leave blank to keep existing or use account name + key below.</small></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Azure account name</label><input type="text" name="cloud_account_name" class="form-control" value="{{ old('cloud_account_name', $settings->cloud_config['account_name'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Azure account key</label><input type="password" name="cloud_account_key" class="form-control" value=""></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="azure"><label class="form-label">Container</label><input type="text" name="cloud_container" class="form-control" value="{{ old('cloud_container', $settings->cloud_config['container'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP host</label><input type="text" name="cloud_host" class="form-control" value="{{ old('cloud_host', $settings->cloud_config['host'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP port</label><input type="number" name="cloud_port" class="form-control" value="{{ old('cloud_port', $settings->cloud_config['port'] ?? 22) }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP username</label><input type="text" name="cloud_username" class="form-control" value="{{ old('cloud_username', $settings->cloud_config['username'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SFTP password</label><input type="password" name="cloud_password" class="form-control" value=""><small class="text-muted">Leave blank when using a private key.</small></div>
                            <div class="col-md-12 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">SSH private key path</label><input type="text" name="cloud_private_key" class="form-control" value="{{ old('cloud_private_key', $settings->cloud_config['private_key'] ?? '') }}" placeholder="/etc/khub/keys/sftp_id_rsa"><small class="text-muted">Path on the server, or paste PEM content. Uses built-in phpseclib — do not install <code>league/flysystem-sftp</code>.</small></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sftp"><label class="form-label">Key passphrase (optional)</label><input type="password" name="cloud_passphrase" class="form-control" value=""></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Tenant ID</label><input type="text" name="sharepoint_tenant_id" class="form-control" value="{{ old('sharepoint_tenant_id', $settings->cloud_config['tenant_id'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Client ID</label><input type="text" name="sharepoint_client_id" class="form-control" value="{{ old('sharepoint_client_id', $settings->cloud_config['client_id'] ?? '') }}"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Client secret</label><input type="password" name="sharepoint_client_secret" class="form-control" value=""></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Site hostname</label><input type="text" name="sharepoint_site_hostname" class="form-control" value="{{ old('sharepoint_site_hostname', $settings->cloud_config['site_hostname'] ?? '') }}" placeholder="contoso.sharepoint.com"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Site path</label><input type="text" name="sharepoint_site_path" class="form-control" value="{{ old('sharepoint_site_path', $settings->cloud_config['site_path'] ?? '') }}" placeholder="sites/KnowledgeHub"></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Site ID (optional)</label><input type="text" name="sharepoint_site_id" class="form-control" value="{{ old('sharepoint_site_id', $settings->cloud_config['site_id'] ?? '') }}"><small class="text-muted">Graph site ID; alternative to hostname + path.</small></div>
                            <div class="col-md-6 mb-2 cloud-field" data-drivers="sharepoint"><label class="form-label">Drive ID (optional)</label><input type="text" name="sharepoint_drive_id" class="form-control" value="{{ old('sharepoint_drive_id', $settings->cloud_config['drive_id'] ?? '') }}"><small class="text-muted">Defaults to the site document library if empty.</small></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SQL backup root (separate from files)</label>
                        <input type="text" name="sql_backup_root" class="form-control" value="{{ old('sql_backup_root', $settings->sql_backup_root) }}" placeholder="{{ $recommended['sql_backups'] }}">
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="auto_sql_backup" value="0">
                        <input class="form-check-input" type="checkbox" name="auto_sql_backup" value="1" id="autoSqlBackup" {{ old('auto_sql_backup', $settings->auto_sql_backup) ? 'checked' : '' }}>
                        <label class="form-check-label" for="autoSqlBackup">Automatic daily incremental SQL backup</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Retain SQL backups (days)</label>
                        <input type="number" name="sql_backup_retention_days" class="form-control" min="1" max="3650" value="{{ old('sql_backup_retention_days', $settings->sql_backup_retention_days) }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Save storage settings</button>
                    <button type="button" class="btn btn-outline-secondary" id="testConnectionBtn">Test connection</button>
                    <span id="testConnectionResult" class="ms-2 small"></span>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Migrate files to external storage</h3>
            </div>
            <div class="card-body">
                <p class="text-muted small">Copies existing publication and forum uploads from internal storage to the configured external driver. Internal copies are kept until you verify the migration.</p>
                @if($settings->migration_status === 'running')
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="migrationProgressBar" style="width:0%"></div>
                    </div>
                    <p class="small mb-2" id="migrationProgressLabel">Migration in progress…</p>
                @elseif($settings->migration_message)
                    <p class="small text-muted">{{ $settings->migration_message }}</p>
                @endif
                <form method="post" action="{{ route('admin.storage.migrate') }}" class="d-inline" onsubmit="return confirm('Start file migration to external storage?');">
                    @csrf
                    <input type="hidden" name="mode" value="queue">
                    <button type="submit" class="btn btn-warning" {{ $settings->files_driver === 'internal' ? 'disabled' : '' }}>Queue migration</button>
                </form>
                <form method="post" action="{{ route('admin.storage.migrate') }}" class="d-inline ms-2" onsubmit="return confirm('Run migration synchronously? This may take a long time.');">
                    @csrf
                    <input type="hidden" name="mode" value="sync">
                    <button type="submit" class="btn btn-outline-warning" {{ $settings->files_driver === 'internal' ? 'disabled' : '' }}>Run now</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title mb-0">SQL backups &amp; restore</h3></div>
            <div class="card-body">
                <p class="small text-muted mb-2">Current files root: <code>{{ $filesRoot }}</code></p>
                <p class="small text-muted mb-3">SQL backup root: <code>{{ $sqlBackupRoot }}</code></p>
                @if($settings->last_sql_backup_at)
                    <p class="small">Last backup: {{ $settings->last_sql_backup_at->format('Y-m-d H:i') }}</p>
                @endif
                <form method="post" action="{{ route('admin.storage.backup') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="incremental" value="1">
                    <button type="submit" class="btn btn-sm btn-primary">Incremental backup</button>
                </form>
                <form method="post" action="{{ route('admin.storage.backup') }}" class="d-inline ms-1">
                    @csrf
                    <input type="hidden" name="incremental" value="0">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Full backup</button>
                </form>
                <hr>
                <h6 class="fw-bold">Restore from backup</h6>
                <form method="post" action="{{ route('admin.storage.restore') }}" id="restoreBackupForm">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Backup folder</label>
                        <select name="backup_path" id="restoreBackupPath" class="form-control" required>
                            <option value="">Select backup…</option>
                            @foreach($backups as $backup)
                                <option value="{{ $backup['path'] }}">{{ $backup['type'] }} / {{ $backup['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label mb-0">Tables to restore</label>
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
                        <p class="small text-muted mb-0 mt-1">KPI / OWID tables are <strong>not</strong> backed up (re-sync from OWID): <code>{{ implode('</code>, <code>', $kpiExcludedTables) }}</code></p>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="only_empty_tables" value="0">
                        <input class="form-check-input" type="checkbox" name="only_empty_tables" value="1" id="onlyEmptyTables" checked>
                        <label class="form-check-label" for="onlyEmptyTables">Only restore into empty tables (recommended)</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-danger" id="restoreSubmitBtn">Restore selected tables</button>
                </form>
                <p class="small text-muted mt-2 mb-0">Backups include site settings, roles, badges, taxonomy, publications, forums, communities, and more. Select only the tables you need when restoring.</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">File browser</h3>
                <select id="browseArea" class="form-control form-control-sm" style="width:auto;">
                    @foreach($contentAreas as $key => $prefix)
                        <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-body p-0">
                <div class="p-2 border-bottom small text-muted" id="browsePath">/</div>
                <ul class="list-group list-group-flush" id="browseList" style="max-height:320px;overflow:auto;"></ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
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
            pkgEl.innerHTML = '<strong>Server requirement:</strong> <code>' + driverPackages[val] + '</code>';
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
            result.className = 'ms-2 small text-' + (data.status === 'ok' ? 'success' : (data.status === 'warning' ? 'warning' : 'danger'));
        });
    });

    var browsePath = '';
    function loadBrowse(subPath) {
        var area = document.getElementById('browseArea').value;
        fetch('{{ route('admin.storage.browse') }}?area=' + encodeURIComponent(area) + '&path=' + encodeURIComponent(subPath || ''))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                browsePath = subPath || '';
                document.getElementById('browsePath').textContent = data.path || '/';
                var list = document.getElementById('browseList');
                list.innerHTML = '';
                if (browsePath) {
                    var up = document.createElement('li');
                    up.className = 'list-group-item list-group-item-action';
                    up.innerHTML = '<i class="fa fa-level-up-alt me-1"></i> ..';
                    up.addEventListener('click', function () {
                        var parts = browsePath.split('/').filter(Boolean);
                        parts.pop();
                        loadBrowse(parts.join('/'));
                    });
                    list.appendChild(up);
                }
                (data.items || []).forEach(function (item) {
                    var li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action d-flex justify-content-between';
                    li.innerHTML = '<span><i class="fa fa-' + (item.type === 'dir' ? 'folder' : 'file') + ' me-1"></i> ' + item.name + '</span>'
                        + (item.size ? '<span class="text-muted small">' + item.size + ' B</span>' : '');
                    if (item.type === 'dir') {
                        li.addEventListener('click', function () { loadBrowse(item.path); });
                    }
                    list.appendChild(li);
                });
            });
    }
    document.getElementById('browseArea').addEventListener('change', function () { loadBrowse(''); });
    loadBrowse('');

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

    @if($settings->migration_status === 'running')
    function pollMigration() {
        fetch('{{ route('admin.storage.migration-status') }}')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var total = parseInt(data.total || 0, 10);
                var done = parseInt(data.done || 0, 10);
                var pct = total > 0 ? Math.round((done / total) * 100) : 0;
                document.getElementById('migrationProgressBar').style.width = pct + '%';
                document.getElementById('migrationProgressLabel').textContent = done + ' / ' + total + ' files';
                if (data.status === 'running') setTimeout(pollMigration, 2000);
            });
    }
    pollMigration();
    @endif
})();
</script>
@endsection
