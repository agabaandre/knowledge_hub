@if(!empty($moodleFields) || !empty($frappeFields) || !empty($openEdxFields))
    <p class="text-muted mb-4">
        Configure Moodle, Frappe LMS, and/or Open edX. Enable sync for each platform you use.
        Database values <strong>override</strong> matching <code>.env</code> settings.
        Run <code>php artisan learning:fetch-courses</code> after saving.
    </p>
@else
    <div class="alert alert-warning">
        Learning platform columns are missing from the database. Run <code>php artisan migrate</code> on this server.
    </div>
@endif

@if(!empty($moodleFields))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-book me-2"></i>Moodle</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="moodle_api_url">Web service URL</label>
                        <input type="url" name="moodle_api_url" id="moodle_api_url" class="form-control"
                               value="{{ $moodleFields['moodle_api_url']['form_value'] ?? '' }}"
                               placeholder="{{ $moodleFields['moodle_api_url']['value'] ?? 'https://example.org/elearning/webservice/rest/server.php' }}">
                        <small class="form-text text-muted">Env key: <code>MOODLE_API_URL</code></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="moodle_base_url">Learner site URL</label>
                        <input type="url" name="moodle_base_url" id="moodle_base_url" class="form-control"
                               value="{{ $moodleFields['moodle_base_url']['form_value'] ?? '' }}"
                               placeholder="{{ $moodleFields['moodle_base_url']['value'] ?? 'https://example.org/elearning' }}">
                        <small class="form-text text-muted">Env key: <code>MOODLE_URL</code></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="moodle_api_token">Web service token</label>
                        <input type="password" name="moodle_api_token" id="moodle_api_token" class="form-control" autocomplete="new-password"
                               placeholder="{{ !empty($moodleFields['moodle_api_token']['form_value']) ? '••••••••' : 'Paste token to set or update' }}">
                        <small class="form-text text-muted">Leave blank to keep the current token. Env key: <code>MOODLE_API_TOKEN</code></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <input type="hidden" name="moodle_sync_enabled" value="0">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="moodle_sync_enabled" name="moodle_sync_enabled" value="1"
                                   @checked((bool) ($moodleFields['moodle_sync_enabled']['form_value'] ?? $moodleFields['moodle_sync_enabled']['value'] ?? true))>
                            <label class="form-check-label" for="moodle_sync_enabled">Enable automatic Moodle course sync</label>
                        </div>
                        <small class="form-text text-muted">Env key: <code>MOODLE_SYNC_ENABLED</code></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(!empty($frappeFields))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-server me-2"></i>Frappe LMS</h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Token auth against the <a href="https://docs.frappe.io/framework/user/en/api/rest" target="_blank" rel="noopener">Frappe REST API</a>.
            </p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="frappe_base_url">Site base URL</label>
                        <input type="url" name="frappe_base_url" id="frappe_base_url" class="form-control"
                               value="{{ $frappeFields['frappe_base_url']['form_value'] ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="frappe_course_doctype">Course DocType</label>
                        <input type="text" name="frappe_course_doctype" id="frappe_course_doctype" class="form-control"
                               value="{{ $frappeFields['frappe_course_doctype']['form_value'] ?? 'LMS Course' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="frappe_api_key">API key</label>
                        <input type="text" name="frappe_api_key" id="frappe_api_key" class="form-control"
                               value="{{ $frappeFields['frappe_api_key']['form_value'] ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="frappe_api_secret">API secret</label>
                        <input type="password" name="frappe_api_secret" id="frappe_api_secret" class="form-control" autocomplete="new-password"
                               placeholder="Leave blank to keep current secret">
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="frappe_sync_enabled" value="0">
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="frappe_sync_enabled" name="frappe_sync_enabled" value="1"
                               @checked((bool) ($frappeFields['frappe_sync_enabled']['form_value'] ?? false))>
                        <label class="form-check-label" for="frappe_sync_enabled">Enable Frappe LMS sync</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(!empty($openEdxFields))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-university me-2"></i>Open edX</h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                OAuth2 client credentials per the <a href="https://docs.openedx.org/projects/edx-platform/en/latest/how-tos/use_the_api.html" target="_blank" rel="noopener">Open edX REST API guide</a>.
            </p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="openedx_lms_url">LMS URL</label>
                        <input type="url" name="openedx_lms_url" id="openedx_lms_url" class="form-control"
                               value="{{ $openEdxFields['openedx_lms_url']['form_value'] ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="openedx_token_url">Token URL (optional)</label>
                        <input type="url" name="openedx_token_url" id="openedx_token_url" class="form-control"
                               value="{{ $openEdxFields['openedx_token_url']['form_value'] ?? '' }}"
                               placeholder="Defaults to {LMS URL}/oauth2/access_token">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="openedx_client_id">Client ID</label>
                        <input type="text" name="openedx_client_id" id="openedx_client_id" class="form-control"
                               value="{{ $openEdxFields['openedx_client_id']['form_value'] ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="openedx_client_secret">Client secret</label>
                        <input type="password" name="openedx_client_secret" id="openedx_client_secret" class="form-control" autocomplete="new-password"
                               placeholder="Leave blank to keep current secret">
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="openedx_sync_enabled" value="0">
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="openedx_sync_enabled" name="openedx_sync_enabled" value="1"
                               @checked((bool) ($openEdxFields['openedx_sync_enabled']['form_value'] ?? false))>
                        <label class="form-check-label" for="openedx_sync_enabled">Enable Open edX sync</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
