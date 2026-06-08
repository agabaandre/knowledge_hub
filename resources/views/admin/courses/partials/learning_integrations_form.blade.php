@if(!empty($moodleFields) || !empty($frappeFields) || !empty($openEdxFields))
    <div class="alert alert-light border mb-4">
        <i class="fa fa-info-circle me-2" style="color: var(--la-primary);"></i>
        Configure Moodle, Frappe LMS, and/or Open edX. Values are loaded from <code>.env</code> by default; the database stores only fields you change here.
        Run <code>php artisan learning:fetch-courses</code> after saving.
    </div>
@else
    <div class="alert alert-warning">
        Learning platform columns are missing from the database. Run <code>php artisan migrate</code> on this server.
    </div>
@endif

@if(!empty($moodleFields))
    <div class="learning-section-card">
        <div class="section-head">
            <h4><span class="platform-icon"><i class="fa fa-book"></i></span>Moodle</h4>
            <p>Web service connection for course sync into the hub.</p>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="moodle_api_url">Web service URL</label>
                    <input type="url" name="moodle_api_url" id="moodle_api_url" class="form-control"
                           value="{{ $moodleFields['moodle_api_url']['form_value'] ?? '' }}"
                           placeholder="{{ $moodleFields['moodle_api_url']['value'] ?? 'https://example.org/elearning/webservice/rest/server.php' }}">
                    <small class="text-muted">Env: <code>MOODLE_API_URL</code></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="moodle_base_url">Learner site URL</label>
                    <input type="url" name="moodle_base_url" id="moodle_base_url" class="form-control"
                           value="{{ $moodleFields['moodle_base_url']['form_value'] ?? '' }}"
                           placeholder="{{ $moodleFields['moodle_base_url']['value'] ?? 'https://example.org/elearning' }}">
                    <small class="text-muted">Env: <code>MOODLE_URL</code></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="moodle_api_token">Web service token</label>
                    <input type="password" name="moodle_api_token" id="moodle_api_token" class="form-control" autocomplete="new-password"
                           placeholder="{{ !empty($moodleFields['moodle_api_token']['form_value']) ? '••••••••' : 'Paste token to set or update' }}">
                    <small class="text-muted">Leave blank to keep current. Env: <code>MOODLE_API_TOKEN</code></small>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <input type="hidden" name="moodle_sync_enabled" value="0">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" class="form-check-input" id="moodle_sync_enabled" name="moodle_sync_enabled" value="1"
                               @checked($moodleFields['moodle_sync_enabled']['form_value'] ?? true)>
                        <label class="form-check-label" for="moodle_sync_enabled">Enable automatic Moodle course sync</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(!empty($frappeFields))
    <div class="learning-section-card">
        <div class="section-head">
            <h4><span class="platform-icon"><i class="fa fa-server"></i></span>Frappe LMS</h4>
            <p>Token auth against the <a href="https://docs.frappe.io/framework/user/en/api/rest" target="_blank" rel="noopener">Frappe REST API</a>.</p>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="frappe_base_url">Site base URL</label>
                    <input type="url" name="frappe_base_url" id="frappe_base_url" class="form-control"
                           value="{{ $frappeFields['frappe_base_url']['form_value'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="frappe_course_doctype">Course DocType</label>
                    <input type="text" name="frappe_course_doctype" id="frappe_course_doctype" class="form-control"
                           value="{{ $frappeFields['frappe_course_doctype']['form_value'] ?? 'LMS Course' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="frappe_api_key">API key</label>
                    <input type="text" name="frappe_api_key" id="frappe_api_key" class="form-control"
                           value="{{ $frappeFields['frappe_api_key']['form_value'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="frappe_api_secret">API secret</label>
                    <input type="password" name="frappe_api_secret" id="frappe_api_secret" class="form-control" autocomplete="new-password"
                           placeholder="Leave blank to keep current secret">
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="frappe_sync_enabled" value="0">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="frappe_sync_enabled" name="frappe_sync_enabled" value="1"
                               @checked($frappeFields['frappe_sync_enabled']['form_value'] ?? false)>
                        <label class="form-check-label" for="frappe_sync_enabled">Enable Frappe LMS sync</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(!empty($openEdxFields))
    <div class="learning-section-card">
        <div class="section-head">
            <h4><span class="platform-icon"><i class="fa fa-university"></i></span>Open edX</h4>
            <p>OAuth2 client credentials per the <a href="https://docs.openedx.org/projects/edx-platform/en/latest/how-tos/use_the_api.html" target="_blank" rel="noopener">Open edX REST API guide</a>.</p>
        </div>
        <div class="section-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="openedx_lms_url">LMS URL</label>
                    <input type="url" name="openedx_lms_url" id="openedx_lms_url" class="form-control"
                           value="{{ $openEdxFields['openedx_lms_url']['form_value'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="openedx_token_url">Token URL (optional)</label>
                    <input type="url" name="openedx_token_url" id="openedx_token_url" class="form-control"
                           value="{{ $openEdxFields['openedx_token_url']['form_value'] ?? '' }}"
                           placeholder="Defaults to {LMS URL}/oauth2/access_token">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="openedx_client_id">Client ID</label>
                    <input type="text" name="openedx_client_id" id="openedx_client_id" class="form-control"
                           value="{{ $openEdxFields['openedx_client_id']['form_value'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="openedx_client_secret">Client secret</label>
                    <input type="password" name="openedx_client_secret" id="openedx_client_secret" class="form-control" autocomplete="new-password"
                           placeholder="Leave blank to keep current secret">
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="openedx_sync_enabled" value="0">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="openedx_sync_enabled" name="openedx_sync_enabled" value="1"
                               @checked($openEdxFields['openedx_sync_enabled']['form_value'] ?? false)>
                        <label class="form-check-label" for="openedx_sync_enabled">Enable Open edX sync</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
