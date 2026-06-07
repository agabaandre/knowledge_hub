@extends('install.layout')

@section('title', 'Mail')

@section('content')
    <div class="step-badge text-muted mb-2">Step 7 of 9</div>
    <h2 class="h5 mb-3">Mail configuration</h2>
    <p class="text-muted small">
        Choose how the hub sends system email (password reset, notifications, reminders).
        Settings are saved to the database and can be changed later under <strong>Admin → Configure → Email</strong>.
        Use <strong>Log only</strong> for local/Docker testing.
    </p>

    <form method="post" action="{{ route('install.mail.store') }}" class="row g-3" id="install-mail-form">
        @csrf
        <div class="col-12">
            <label class="form-label">Mail driver</label>
            <select name="mail_mailer" id="mail_mailer" class="form-select">
                <option value="exchange" @selected(old('mail_mailer', $defaults['mail_mailer']) === 'exchange')>Microsoft Exchange (default)</option>
                <option value="smtp" @selected(old('mail_mailer', $defaults['mail_mailer']) === 'smtp')>SMTP</option>
                <option value="log" @selected(old('mail_mailer', $defaults['mail_mailer']) === 'log')>Log only (development — emails written to logs)</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">From address</label>
            <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $defaults['mail_from_address']) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">From name</label>
            <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $defaults['mail_from_name']) }}" required>
        </div>

        <div id="exchange-fields" class="row g-3">
            <div class="col-12">
                <h3 class="h6 text-muted mb-0">Microsoft Exchange / Graph API</h3>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tenant ID</label>
                <input type="text" name="exchange_tenant_id" class="form-control" value="{{ old('exchange_tenant_id', $defaults['exchange_tenant_id']) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Client ID</label>
                <input type="text" name="exchange_client_id" class="form-control" value="{{ old('exchange_client_id', $defaults['exchange_client_id']) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Client secret</label>
                <input type="password" name="exchange_client_secret" class="form-control" value="{{ old('exchange_client_secret', $defaults['exchange_client_secret']) }}" autocomplete="new-password">
            </div>
            <div class="col-md-6">
                <label class="form-label">Auth method</label>
                <select name="exchange_auth_method" class="form-select">
                    <option value="client_credentials" @selected(old('exchange_auth_method', $defaults['exchange_auth_method']) === 'client_credentials')>Client credentials (recommended)</option>
                    <option value="authorization_code" @selected(old('exchange_auth_method', $defaults['exchange_auth_method']) === 'authorization_code')>Authorization code</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Redirect URI</label>
                <input type="text" name="exchange_redirect_uri" class="form-control"
                       value="{{ old('exchange_redirect_uri', $defaults['exchange_redirect_uri']) }}"
                       placeholder="{{ url('/auth/microsoft/callback') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Scope</label>
                <input type="text" name="exchange_scope" class="form-control"
                       value="{{ old('exchange_scope', $defaults['exchange_scope']) }}"
                       placeholder="https://graph.microsoft.com/.default">
            </div>
        </div>

        <div id="smtp-fields" class="row g-3">
            <div class="col-12">
                <h3 class="h6 text-muted mb-0">SMTP server</h3>
            </div>
            <div class="col-md-8">
                <label class="form-label">SMTP host</label>
                <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', $defaults['mail_host']) }}" placeholder="smtp.example.com">
            </div>
            <div class="col-md-4">
                <label class="form-label">Port</label>
                <input type="number" name="mail_port" class="form-control" value="{{ old('mail_port', $defaults['mail_port']) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Username</label>
                <input type="text" name="mail_username" class="form-control" value="{{ old('mail_username', $defaults['mail_username']) }}" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="mail_password" class="form-control" value="{{ old('mail_password', $defaults['mail_password']) }}" autocomplete="new-password">
            </div>
            <div class="col-md-6">
                <label class="form-label">Encryption</label>
                <select name="mail_encryption" class="form-select">
                    <option value="tls" @selected(old('mail_encryption', $defaults['mail_encryption']) === 'tls')>TLS</option>
                    <option value="ssl" @selected(old('mail_encryption', $defaults['mail_encryption']) === 'ssl')>SSL</option>
                    <option value="none" @selected(old('mail_encryption', $defaults['mail_encryption']) === 'none')>None</option>
                </select>
            </div>
        </div>

        <div class="col-md-6" id="test-email-wrap">
            <label class="form-label">Send test email to (optional)</label>
            <input type="email" name="test_email" id="test_email" class="form-control" value="{{ old('test_email') }}" placeholder="you@example.com">
            <div class="form-text">Leave blank to only verify the connection.</div>
        </div>
        <div class="col-12" id="test-mail-wrap">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="testMailBtn">
                Test mail configuration
            </button>
            <span id="testMailResult" class="small ms-2 text-muted"></span>
        </div>

        <div class="col-12 d-flex gap-2">
            <a href="{{ route('install.moodle') }}" class="btn btn-outline-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save mail settings &amp; continue</button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
(function () {
    var driver = document.getElementById('mail_mailer');
    var smtp = document.getElementById('smtp-fields');
    var exchange = document.getElementById('exchange-fields');
    var testWrap = document.getElementById('test-mail-wrap');
    var testEmailWrap = document.getElementById('test-email-wrap');

    function toggle() {
        var value = driver.value;
        smtp.style.display = value === 'smtp' ? '' : 'none';
        exchange.style.display = value === 'exchange' ? '' : 'none';
        var showTest = value !== 'log';
        testWrap.style.display = showTest ? '' : 'none';
        testEmailWrap.style.display = showTest ? '' : 'none';
    }

    driver.addEventListener('change', toggle);
    toggle();

    var btn = document.getElementById('testMailBtn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        var result = document.getElementById('testMailResult');
        result.textContent = 'Testing…';
        result.className = 'small ms-2 text-muted';

        var form = document.getElementById('install-mail-form');
        var body = new FormData(form);

        fetch('{{ route('install.mail.test') }}', {
            method: 'POST',
            body: body,
            headers: { 'Accept': 'application/json' }
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data.ok) {
                result.textContent = data.message || 'Mail configuration is valid.';
                result.className = 'small ms-2 text-success';
            } else {
                result.textContent = data.error || 'Test failed';
                result.className = 'small ms-2 text-danger';
            }
        }).catch(function () {
            result.textContent = 'Request failed';
            result.className = 'small ms-2 text-danger';
        });
    });
})();
</script>
@endsection
