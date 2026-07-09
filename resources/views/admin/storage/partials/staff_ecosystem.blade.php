@if($staffEcosystemEnabled && $staffEcosystem)
<section class="storage-section" id="storage-section-staff">
    <h2 class="storage-section-title"><i class="fa fa-users"></i> Staff portal ecosystem</h2>
    <p class="text-muted small mb-4">
        Uploads for CodeIgniter staff, APM, Helpdesk, and staff-portal live outside the git repo under
        <code>{{ $staffEcosystem['data_root'] }}</code>.
        Repo: <code>{{ $staffEcosystem['repo_root'] }}</code>
    </p>

    <div class="storage-panel mb-4">
        <div class="storage-panel-header d-flex justify-content-between align-items-center">
            <h3>Module storage</h3>
            <form method="post" action="{{ route('admin.storage.staff-migrate') }}" class="d-inline"
                  onsubmit="return confirm('Migrate all staff modules to the host data root?');">
                @csrf
                <input type="hidden" name="module" value="all">
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fa fa-cloud-upload"></i> Migrate all
                </button>
            </form>
        </div>
        <div class="storage-panel-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Module</th>
                            <th>Legacy path</th>
                            <th>Host path</th>
                            <th>Legacy</th>
                            <th>Host</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($staffEcosystem['modules'] as $module)
                            <tr>
                                <td><strong>{{ $module['label'] ?? $module['key'] }}</strong><br><code>{{ $module['env_var'] ?? '' }}</code></td>
                                <td><span class="storage-path">{{ $module['legacy_path'] ?? '' }}</span></td>
                                <td><span class="storage-path">{{ $module['host_path'] ?? '' }}</span></td>
                                <td>{{ number_format((int) ($module['legacy_files'] ?? 0)) }} files<br>{{ number_format((int) ($module['legacy_bytes'] ?? 0) / 1024 / 1024, 1) }} MB</td>
                                <td>{{ number_format((int) ($module['host_files'] ?? 0)) }} files<br>{{ number_format((int) ($module['host_bytes'] ?? 0) / 1024 / 1024, 1) }} MB</td>
                                <td>
                                    @if($module['needs_migration'] ?? false)
                                        <span class="badge bg-warning text-dark">Needs migration</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="post" action="{{ route('admin.storage.staff-migrate') }}" class="d-inline"
                                          onsubmit="return confirm('Copy {{ $module['label'] ?? $module['key'] }} uploads to host path?');">
                                        @csrf
                                        <input type="hidden" name="module" value="{{ $module['key'] }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Migrate</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="storage-panel mb-4">
        <div class="storage-panel-header d-flex justify-content-between align-items-center">
            <h3>File backups</h3>
            <form method="post" action="{{ route('admin.storage.staff-backup') }}"
                  onsubmit="return confirm('Create a file backup of all staff modules?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fa fa-archive"></i> Run file backup
                </button>
            </form>
        </div>
        <div class="storage-panel-body">
            <p class="text-muted small mb-3">
                Backups are stored under <code>{{ $staffEcosystem['backup_root'] }}</code>.
                Retention: {{ (int) config('hub_storage.staff_ecosystem.backup_retention_days', 30) }} days.
            </p>
            @if(count($staffEcosystem['backups'] ?? []) === 0)
                <p class="mb-0 text-muted">No file backups yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Folder</th><th>Created</th><th>Size</th><th>Path</th></tr>
                        </thead>
                        <tbody class="small">
                            @foreach($staffEcosystem['backups'] as $backup)
                                <tr>
                                    <td><code>{{ $backup['folder'] }}</code></td>
                                    <td>{{ $backup['created_at'] }}</td>
                                    <td>{{ number_format((int) ($backup['bytes'] ?? 0) / 1024 / 1024, 1) }} MB</td>
                                    <td><span class="storage-path">{{ $backup['path'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="storage-dev-grid">
        <div class="storage-dev-block">
            <h5>Path layout</h5>
            <pre>/var/staffdata/{{ $staffEcosystem['site_id'] }}/
├── ci/            ← STAFF_PORTAL_UPLOADS_ROOT
├── apm/           ← STAFF_APM_FILES_ROOT
├── helpdesk/      ← STAFF_HELPDESK_FILES_ROOT
├── staff-portal/  ← STAFF_PORTAL_MODULE_FILES_ROOT
└── backups/files/</pre>
        </div>
        <div class="storage-dev-block">
            <h5>Shell scripts (staff repo)</h5>
            <ul class="small mb-0">
                <li><code>scripts/storage/fix-staff-storage-permissions.sh</code></li>
                <li><code>scripts/storage/migrate-ci-uploads.sh</code></li>
                <li><code>scripts/storage/migrate-apm-uploads.sh</code></li>
                <li><code>scripts/storage/migrate-helpdesk-uploads.sh</code></li>
                <li><code>scripts/storage/migrate-staff-portal-uploads.sh</code></li>
                <li><code>scripts/storage/migrate-all.sh</code></li>
            </ul>
        </div>
        <div class="storage-dev-block">
            <h5>Environment</h5>
            <ul class="small mb-0">
                <li><code>STAFF_DATA_ROOT</code></li>
                <li><code>STAFF_SITE_ID</code></li>
                <li><code>STAFF_REPO_ROOT</code> (Knowledge Hub)</li>
                <li><code>STAFF_PORTAL_UPLOADS_ROOT</code></li>
                <li><code>STAFF_APM_FILES_ROOT</code></li>
                <li><code>STAFF_HELPDESK_FILES_ROOT</code></li>
            </ul>
        </div>
    </div>
</section>
@endif
