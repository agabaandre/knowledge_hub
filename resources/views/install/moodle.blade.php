@extends('install.layout')

@section('title', 'Learning Platforms')

@section('content')
    <div class="step-badge text-muted mb-2">Step 6 of 8</div>
    <h2 class="h5 mb-3">Learning platform integrations</h2>
    <p class="text-muted small">
        Connect one or more eLearning platforms. Courses sync into this hub and learners open them on the source platform.
        Settings are saved to the database and <strong>override</strong> matching <code>.env</code> values.
    </p>

    <form method="post" action="{{ route('install.moodle.store') }}" class="row g-3" id="learningForm">
        @csrf

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-3">Moodle</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Web service URL</label>
                            <input type="url" name="moodle_api_url" id="moodleApiUrl" class="form-control"
                                   value="{{ old('moodle_api_url', $defaults['moodle_api_url']) }}"
                                   placeholder="https://example.org/elearning/webservice/rest/server.php">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Web service token</label>
                            <input type="password" name="moodle_api_token" id="moodleApiToken" class="form-control"
                                   value="{{ old('moodle_api_token', $defaults['moodle_api_token']) }}" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Learner site URL</label>
                            <input type="url" name="moodle_base_url" id="moodleBaseUrl" class="form-control"
                                   value="{{ old('moodle_base_url', $defaults['moodle_base_url']) }}"
                                   placeholder="https://example.org/elearning">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="hidden" name="moodle_sync_enabled" value="0">
                                <input class="form-check-input" type="checkbox" name="moodle_sync_enabled" value="1" id="moodleSyncEnabled"
                                       @checked(old('moodle_sync_enabled', $defaults['moodle_sync_enabled']))>
                                <label class="form-check-label" for="moodleSyncEnabled">Enable Moodle sync</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="testMoodleBtn">Test Moodle</button>
                            <span id="testMoodleResult" class="small ms-2 text-muted"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-1">Frappe LMS</h3>
                    <p class="text-muted small mb-3">Uses the <a href="https://docs.frappe.io/framework/user/en/api/rest" target="_blank" rel="noopener">Frappe REST API</a> with token authentication.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Site base URL</label>
                            <input type="url" name="frappe_base_url" id="frappeBaseUrl" class="form-control"
                                   value="{{ old('frappe_base_url', $defaults['frappe_base_url']) }}"
                                   placeholder="https://lms.example.org">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course DocType</label>
                            <input type="text" name="frappe_course_doctype" id="frappeCourseDoctype" class="form-control"
                                   value="{{ old('frappe_course_doctype', $defaults['frappe_course_doctype']) }}"
                                   placeholder="LMS Course">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">API key</label>
                            <input type="text" name="frappe_api_key" id="frappeApiKey" class="form-control"
                                   value="{{ old('frappe_api_key', $defaults['frappe_api_key']) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">API secret</label>
                            <input type="password" name="frappe_api_secret" id="frappeApiSecret" class="form-control"
                                   value="{{ old('frappe_api_secret', $defaults['frappe_api_secret']) }}" autocomplete="new-password">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="frappe_sync_enabled" value="0">
                                <input class="form-check-input" type="checkbox" name="frappe_sync_enabled" value="1" id="frappeSyncEnabled"
                                       @checked(old('frappe_sync_enabled', $defaults['frappe_sync_enabled']))>
                                <label class="form-check-label" for="frappeSyncEnabled">Enable Frappe LMS sync</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="testFrappeBtn">Test Frappe LMS</button>
                            <span id="testFrappeResult" class="small ms-2 text-muted"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-1">Open edX</h3>
                    <p class="text-muted small mb-3">Uses OAuth2 client credentials and JWT per the <a href="https://docs.openedx.org/projects/edx-platform/en/latest/how-tos/use_the_api.html" target="_blank" rel="noopener">Open edX REST API guide</a>.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">LMS URL</label>
                            <input type="url" name="openedx_lms_url" id="openedxLmsUrl" class="form-control"
                                   value="{{ old('openedx_lms_url', $defaults['openedx_lms_url']) }}"
                                   placeholder="https://lms.example.org">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Token URL (optional)</label>
                            <input type="url" name="openedx_token_url" id="openedxTokenUrl" class="form-control"
                                   value="{{ old('openedx_token_url', $defaults['openedx_token_url']) }}"
                                   placeholder="https://lms.example.org/oauth2/access_token">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Client ID</label>
                            <input type="text" name="openedx_client_id" id="openedxClientId" class="form-control"
                                   value="{{ old('openedx_client_id', $defaults['openedx_client_id']) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Client secret</label>
                            <input type="password" name="openedx_client_secret" id="openedxClientSecret" class="form-control"
                                   value="{{ old('openedx_client_secret', $defaults['openedx_client_secret']) }}" autocomplete="new-password">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="openedx_sync_enabled" value="0">
                                <input class="form-check-input" type="checkbox" name="openedx_sync_enabled" value="1" id="openedxSyncEnabled"
                                       @checked(old('openedx_sync_enabled', $defaults['openedx_sync_enabled']))>
                                <label class="form-check-label" for="openedxSyncEnabled">Enable Open edX sync</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="testOpenedxBtn">Test Open edX</button>
                            <span id="testOpenedxResult" class="small ms-2 text-muted"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 justify-content-between">
            <a href="{{ route('install.central') }}" class="btn btn-link px-0">Back</a>
            <div class="d-flex gap-2">
                <button type="submit" name="skip_moodle" value="1" class="btn btn-outline-secondary">Skip for now</button>
                <button type="submit" class="btn btn-success">Save &amp; continue</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
(function () {
    function wireTest(btnId, resultId, url, buildBody) {
        var btn = document.getElementById(btnId);
        if (!btn) return;
        btn.addEventListener('click', function () {
            var result = document.getElementById(resultId);
            result.textContent = 'Testing…';
            result.className = 'small ms-2 text-muted';
            var body = buildBody();
            body.append('_token', '{{ csrf_token() }}');
            fetch(url, {
                method: 'POST',
                body: body,
                headers: { 'Accept': 'application/json' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data.ok) {
                    result.textContent = data.sitename || data.user || ('Connected' + (data.courses != null ? ' (' + data.courses + ' courses)' : ''));
                    result.className = 'small ms-2 text-success';
                } else {
                    result.textContent = data.error || 'Connection failed';
                    result.className = 'small ms-2 text-danger';
                }
            }).catch(function () {
                result.textContent = 'Request failed';
                result.className = 'small ms-2 text-danger';
            });
        });
    }

    wireTest('testMoodleBtn', 'testMoodleResult', '{{ route('install.moodle.test') }}', function () {
        var body = new FormData();
        body.append('moodle_api_url', document.getElementById('moodleApiUrl').value);
        body.append('moodle_api_token', document.getElementById('moodleApiToken').value);
        return body;
    });

    wireTest('testFrappeBtn', 'testFrappeResult', '{{ route('install.moodle.test-frappe') }}', function () {
        var body = new FormData();
        body.append('frappe_base_url', document.getElementById('frappeBaseUrl').value);
        body.append('frappe_api_key', document.getElementById('frappeApiKey').value);
        body.append('frappe_api_secret', document.getElementById('frappeApiSecret').value);
        return body;
    });

    wireTest('testOpenedxBtn', 'testOpenedxResult', '{{ route('install.moodle.test-openedx') }}', function () {
        var body = new FormData();
        body.append('openedx_lms_url', document.getElementById('openedxLmsUrl').value);
        body.append('openedx_client_id', document.getElementById('openedxClientId').value);
        body.append('openedx_client_secret', document.getElementById('openedxClientSecret').value);
        body.append('openedx_token_url', document.getElementById('openedxTokenUrl').value);
        return body;
    });
})();
</script>
@endsection
