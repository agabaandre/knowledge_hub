@extends('install.layout')

@section('title', 'Mail')

@section('content')
    <div class="step-badge text-muted mb-2">Step 4 of 5</div>
    <h2 class="h5 mb-3">Mail configuration</h2>
    <p class="text-muted small">These values are written to your <code>.env</code> file. Choose <strong>Log only</strong> for local/Docker testing, or <strong>SMTP</strong> for production mail.</p>

    <form method="post" action="{{ route('install.mail.store') }}" class="row g-3" id="install-mail-form">
        @csrf
        <div class="col-12">
            <label class="form-label">Mail driver</label>
            <select name="mail_mailer" id="mail_mailer" class="form-select">
                <option value="log" @selected(old('mail_mailer', $defaults['mail_mailer']) === 'log')>Log only (development — emails written to logs)</option>
                <option value="smtp" @selected(old('mail_mailer', $defaults['mail_mailer']) === 'smtp')>SMTP</option>
            </select>
        </div>

        <div id="smtp-fields" class="row g-3">
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

        <div class="col-md-6">
            <label class="form-label">From address</label>
            <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $defaults['mail_from_address']) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">From name</label>
            <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $defaults['mail_from_name']) }}" required>
        </div>

        <div class="col-12 d-flex gap-2">
            <a href="{{ route('install.site') }}" class="btn btn-outline-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save mail settings &amp; continue</button>
        </div>
    </form>

    <script>
        (function () {
            var driver = document.getElementById('mail_mailer');
            var smtp = document.getElementById('smtp-fields');
            function toggle() {
                smtp.style.display = driver.value === 'smtp' ? '' : 'none';
            }
            driver.addEventListener('change', toggle);
            toggle();
        })();
    </script>
@endsection
