@php
    $offsite = $settings->offsite_backup_config ?? [];
    $offsiteDrivers = config('hub_storage.offsite_backup_drivers', []);
    $offsiteDay = (int) config('hub_storage.offsite_backup_schedule_day', 0);
    $offsiteTime = config('hub_storage.offsite_backup_schedule_time', '02:15');
    $weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
@endphp

<div class="storage-panel mt-3 mb-0" id="offsiteBackupPanel">
    <div class="storage-panel-header">
        <h3>Offsite SQL backup</h3>
    </div>
    <div class="storage-panel-body">
        <p class="small text-muted mb-3">
            Copy a zipped full SQL backup (plus <code>.env</code> snapshot) to remote storage each week
            ({{ $weekdays[$offsiteDay] ?? 'Sunday' }} at {{ $offsiteTime }}).
            This is separate from publication file storage and from local SQL backup folders.
        </p>

        @if(!empty($offsite['last_upload_at']))
            <div class="alert alert-{{ ($offsite['last_upload_status'] ?? '') === 'ok' ? 'success' : 'warning' }} py-2 small mb-3">
                <strong>Last offsite upload:</strong>
                {{ \Carbon\Carbon::parse($offsite['last_upload_at'])->format('M j, Y H:i') }}
                @if(!empty($offsite['last_upload_path']))
                    · <code>{{ $offsite['last_upload_path'] }}</code>
                @endif
                @if(!empty($offsite['last_upload_message']))
                    <div class="mt-1 mb-0">{{ $offsite['last_upload_message'] }}</div>
                @endif
            </div>
        @endif

        <div class="form-check mb-3">
            <input type="hidden" name="offsite_backup_enabled" value="0">
            <input class="form-check-input" type="checkbox" name="offsite_backup_enabled" value="1" id="offsiteBackupEnabled" {{ old('offsite_backup_enabled', !empty($offsite['enabled'])) ? 'checked' : '' }}>
            <label class="form-check-label" for="offsiteBackupEnabled">Enable weekly offsite SQL backup upload</label>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <label class="form-label fw-semibold" for="offsiteBackupDriver">Remote storage driver</label>
                <select name="offsite_backup_driver" id="offsiteBackupDriver" class="form-control">
                    <option value="">— Select driver —</option>
                    @foreach($offsiteDrivers as $key => $label)
                        <option value="{{ $key }}" {{ old('offsite_backup_driver', $offsite['driver'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6 mb-3">
                <label class="form-label fw-semibold">Remote folder prefix</label>
                <input type="text" name="offsite_remote_prefix" class="form-control" value="{{ old('offsite_remote_prefix', $offsite['remote_prefix'] ?? 'sql-backups') }}" placeholder="sql-backups">
                <small class="text-muted">Scoped with site ID, e.g. <code>sql-backups/{{ $siteStorageId ?? 'site-id' }}/full/…</code></small>
            </div>
        </div>

        <div id="offsiteDriverFields">
            <div class="row">
                <div class="col-md-6 mb-2 offsite-field" data-drivers="s3"><label class="form-label">Access key</label><input type="text" name="offsite_key" class="form-control" value="{{ old('offsite_key', $offsite['key'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="s3"><label class="form-label">Secret key</label><input type="password" name="offsite_secret" class="form-control" value=""></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="s3"><label class="form-label">Region</label><input type="text" name="offsite_region" class="form-control" value="{{ old('offsite_region', $offsite['region'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="s3"><label class="form-label">Bucket</label><input type="text" name="offsite_bucket" class="form-control" value="{{ old('offsite_bucket', $offsite['bucket'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="s3"><label class="form-label">Endpoint (optional)</label><input type="text" name="offsite_endpoint" class="form-control" value="{{ old('offsite_endpoint', $offsite['endpoint'] ?? '') }}"></div>

                <div class="col-md-12 mb-2 offsite-field" data-drivers="gcs"><label class="form-label">GCS project ID</label><input type="text" name="offsite_gcs_project_id" class="form-control" value="{{ old('offsite_gcs_project_id', $offsite['project_id'] ?? '') }}"></div>
                <div class="col-md-12 mb-2 offsite-field" data-drivers="gcs"><label class="form-label">Service account key file path</label><input type="text" name="offsite_gcs_key_file_path" class="form-control" value="{{ old('offsite_gcs_key_file_path', $offsite['key_file_path'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="gcs"><label class="form-label">GCS bucket</label><input type="text" name="offsite_gcs_bucket" class="form-control" value="{{ old('offsite_gcs_bucket', $offsite['bucket'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="gcs"><label class="form-label">Storage API URI (optional)</label><input type="text" name="offsite_gcs_storage_api_uri" class="form-control" value="{{ old('offsite_gcs_storage_api_uri', $offsite['storage_api_uri'] ?? '') }}"></div>

                <div class="col-md-12 mb-2 offsite-field" data-drivers="azure"><label class="form-label">Connection string</label><input type="password" name="offsite_connection_string" class="form-control" value="" placeholder="Leave blank to keep existing"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="azure"><label class="form-label">Account name</label><input type="text" name="offsite_account_name" class="form-control" value="{{ old('offsite_account_name', $offsite['account_name'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="azure"><label class="form-label">Account key</label><input type="password" name="offsite_account_key" class="form-control" value=""></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="azure"><label class="form-label">Container</label><input type="text" name="offsite_container" class="form-control" value="{{ old('offsite_container', $offsite['container'] ?? '') }}"></div>

                <div class="col-md-6 mb-2 offsite-field" data-drivers="sftp,ftp"><label class="form-label">Host</label><input type="text" name="offsite_host" class="form-control" value="{{ old('offsite_host', $offsite['host'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="sftp,ftp"><label class="form-label">Port</label><input type="number" name="offsite_port" class="form-control" value="{{ old('offsite_port', $offsite['port'] ?? '') }}" placeholder="22 / 21"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="sftp,ftp"><label class="form-label">Username</label><input type="text" name="offsite_username" class="form-control" value="{{ old('offsite_username', $offsite['username'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="sftp,ftp"><label class="form-label">Password</label><input type="password" name="offsite_password" class="form-control" value=""><small class="text-muted">Leave blank when using an SSH key (SFTP).</small></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="sftp,ftp"><label class="form-label">Remote root directory</label><input type="text" name="offsite_remote_root" class="form-control" value="{{ old('offsite_remote_root', $offsite['remote_root'] ?? '/khub-backups') }}"></div>
                <div class="col-md-12 mb-2 offsite-field" data-drivers="sftp"><label class="form-label">SSH private key path</label><input type="text" name="offsite_private_key" class="form-control" value="{{ old('offsite_private_key', $offsite['private_key'] ?? '') }}"></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="sftp"><label class="form-label">Key passphrase (optional)</label><input type="password" name="offsite_passphrase" class="form-control" value=""></div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="ftp">
                    <div class="form-check mt-4">
                        <input type="hidden" name="offsite_ftp_ssl" value="0">
                        <input class="form-check-input" type="checkbox" name="offsite_ftp_ssl" value="1" id="offsiteFtpSsl" {{ old('offsite_ftp_ssl', !empty($offsite['ssl'])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="offsiteFtpSsl">Use FTPS (explicit SSL)</label>
                    </div>
                </div>
                <div class="col-md-6 mb-2 offsite-field" data-drivers="ftp">
                    <div class="form-check mt-4">
                        <input type="hidden" name="offsite_ftp_passive" value="0">
                        <input class="form-check-input" type="checkbox" name="offsite_ftp_passive" value="1" id="offsiteFtpPassive" {{ old('offsite_ftp_passive', ($offsite['passive'] ?? true) ? true : false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="offsiteFtpPassive">Passive mode</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="testOffsiteBackupBtn"><i class="fa fa-plug me-1"></i> Test offsite connection</button>
            <span id="testOffsiteBackupResult" class="small"></span>
            <span class="small text-muted">Save settings first, then test.</span>
        </div>
    </div>
</div>
