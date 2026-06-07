@extends('install.layout')

@section('title', 'Storage')

@section('content')
    <div class="step-badge text-muted mb-2">Step 3 of 9</div>
    <h2 class="h5 mb-3">File &amp; backup storage</h2>
    <p class="text-muted small">
        Publication and forum uploads are stored <strong>outside</strong> the application tree so they survive container rebuilds and deployments.
        @if(($runtime ?? 'local') === 'docker')
            Mount a host volume at <code>/var/khubdata</code> in your compose file (see example below).
        @elseif(($runtime ?? 'local') === 'windows')
            Recommended host path on Windows: <code>{{ $defaults['files_root'] }}</code>
        @else
            Recommended host path: <code>{{ $defaults['files_root'] }}</code>
        @endif
        SQL backups are kept separately at <code>{{ $defaults['sql_backup_root'] }}</code>.
        This hub's permanent storage ID is <code>{{ $siteStorageId }}</code> (from <code>APP_URL</code> domain and subfolder).
    </p>

    @if(($runtime ?? 'local') === 'docker')
        <pre class="small bg-light border rounded p-2 mb-3">volumes:
  - ./khubdata:/var/khubdata</pre>
    @endif

    <form method="post" action="{{ route('install.storage.store') }}" class="row g-3" id="install-storage-form">
        @csrf
        <div class="col-12">
            <label class="form-label">Files driver</label>
            <select name="files_driver" id="filesDriver" class="form-select">
                @foreach($drivers as $key => $label)
                    <option value="{{ $key }}" @selected(old('files_driver', 'internal') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div id="driverSetupNote" class="col-12 alert alert-secondary small py-2 mb-0" style="display:none;"></div>
        <div id="driverPackageNote" class="col-12 alert alert-warning small py-2 mb-0" style="display:none;"></div>

        <div class="col-12" id="internalFilesRoot">
            <label class="form-label">Host files root</label>
            <input type="text" name="local_files_root" class="form-control" value="{{ old('local_files_root', $defaults['files_root']) }}">
            <div class="form-text">Must be writable by the web server user. Not inside <code>storage/</code> or the git checkout. The installer will link <code>public/storage</code> to this folder automatically.</div>
        </div>
        <div class="col-12">
            <label class="form-label">SQL backup root (always on host)</label>
            <input type="text" name="sql_backup_root" class="form-control" value="{{ old('sql_backup_root', $defaults['sql_backup_root']) }}" required>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="auto_sql_backup" value="0">
                <input class="form-check-input" type="checkbox" name="auto_sql_backup" value="1" id="autoSqlBackup" checked>
                <label class="form-check-label" for="autoSqlBackup">Enable automatic daily SQL backups</label>
            </div>
        </div>

        <div id="cloudFields" class="col-12 border rounded p-3" style="display:none;">
            <p class="small text-muted mb-2">Cloud credentials can be refined later under <strong>Settings → Storage Management</strong>. Enter only what you need for your provider.</p>
            <div class="row g-2">
                <div class="col-md-6 cloud-field" data-drivers="s3,gcs,azure,sharepoint,sftp">
                    <label class="form-label">Root prefix</label>
                    <input type="text" name="cloud_root_prefix" class="form-control" value="{{ old('cloud_root_prefix', 'khub') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="s3">
                    <label class="form-label">S3 bucket</label>
                    <input type="text" name="cloud_bucket" class="form-control" value="{{ old('cloud_bucket') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="s3">
                    <label class="form-label">S3 region</label>
                    <input type="text" name="cloud_region" class="form-control" value="{{ old('cloud_region') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="gcs">
                    <label class="form-label">GCS project ID</label>
                    <input type="text" name="gcs_project_id" class="form-control" value="{{ old('gcs_project_id') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="gcs">
                    <label class="form-label">GCS bucket</label>
                    <input type="text" name="gcs_bucket" class="form-control" value="{{ old('gcs_bucket') }}">
                </div>
                <div class="col-12 cloud-field" data-drivers="gcs">
                    <label class="form-label">GCS key file path</label>
                    <input type="text" name="gcs_key_file_path" class="form-control" value="{{ old('gcs_key_file_path') }}" placeholder="/etc/khub/gcs-key.json">
                </div>
                <div class="col-12 cloud-field" data-drivers="azure">
                    <label class="form-label">Azure connection string</label>
                    <input type="password" name="cloud_connection_string" class="form-control" value="">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="azure">
                    <label class="form-label">Azure container</label>
                    <input type="text" name="cloud_container" class="form-control" value="{{ old('cloud_container') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="sharepoint">
                    <label class="form-label">Tenant ID</label>
                    <input type="text" name="sharepoint_tenant_id" class="form-control" value="{{ old('sharepoint_tenant_id') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="sharepoint">
                    <label class="form-label">Client ID</label>
                    <input type="text" name="sharepoint_client_id" class="form-control" value="{{ old('sharepoint_client_id') }}">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="sharepoint">
                    <label class="form-label">Site hostname</label>
                    <input type="text" name="sharepoint_site_hostname" class="form-control" value="{{ old('sharepoint_site_hostname') }}" placeholder="contoso.sharepoint.com">
                </div>
                <div class="col-md-6 cloud-field" data-drivers="sharepoint">
                    <label class="form-label">Site path</label>
                    <input type="text" name="sharepoint_site_path" class="form-control" value="{{ old('sharepoint_site_path') }}" placeholder="sites/KnowledgeHub">
                </div>
            </div>
        </div>

        <div class="col-12 d-flex gap-2">
            <a href="{{ route('install.database') }}" class="btn btn-outline-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save storage &amp; continue</button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
(function () {
    var driver = document.getElementById('filesDriver');
    var internalFilesRoot = document.getElementById('internalFilesRoot');
    var cloudFields = document.getElementById('cloudFields');
    var driverSetup = @json($driverSetup);
    var driverPackages = @json($driverPackages);

    function toggleStorageFields() {
        var val = driver.value;
        var isInternal = val === 'internal';
        internalFilesRoot.style.display = isInternal ? '' : 'none';
        cloudFields.style.display = isInternal ? 'none' : '';
        var filesInput = internalFilesRoot.querySelector('input[name="local_files_root"]');
        if (filesInput) {
            filesInput.required = isInternal;
        }
        document.querySelectorAll('.cloud-field').forEach(function (el) {
            var drivers = (el.getAttribute('data-drivers') || '').split(',');
            el.style.display = !isInternal && drivers.indexOf(val) !== -1 ? '' : 'none';
        });
        var setupEl = document.getElementById('driverSetupNote');
        var pkgEl = document.getElementById('driverPackageNote');
        if (!isInternal && driverSetup[val]) {
            setupEl.style.display = '';
            setupEl.textContent = driverSetup[val];
        } else {
            setupEl.style.display = 'none';
        }
        if (!isInternal && driverPackages[val]) {
            pkgEl.style.display = '';
            pkgEl.innerHTML = '<strong>Server requirement:</strong> <code>' + driverPackages[val] + '</code>';
        } else {
            pkgEl.style.display = 'none';
        }
    }

    driver.addEventListener('change', toggleStorageFields);
    toggleStorageFields();
})();
</script>
@endsection
