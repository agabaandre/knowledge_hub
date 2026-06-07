@extends('install.layout')

@section('title', 'Central Knowledge Hub')

@section('content')
    <div class="step-badge text-muted mb-2">Step 5 of 9</div>
    <h2 class="h5 mb-3">Connect to central Knowledge Hub</h2>
    <p class="text-muted small">
        Country and regional hubs can pull <strong>branding</strong> (colors, theme, mobile app settings) and
        <strong>lookup metadata</strong> (health themes, tags, publication categories, licenses, key links)
        from the continental central hub during installation.
    </p>

    <form method="post" action="{{ route('install.central.store') }}" class="row g-3" id="centralHubForm">
        @csrf
        <div class="col-12">
            <label class="form-label">Central hub base URL</label>
            <input type="url" name="central_hub_url" id="centralHubUrl" class="form-control" required
                   value="{{ old('central_hub_url', $defaults['central_hub_url']) }}"
                   placeholder="https://khub.africacdc.org">
            <div class="form-text">The continental hub that exposes <code>/api/federation/lookup/*</code> endpoints.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Registration token (optional)</label>
            <input type="text" name="central_hub_api_token" id="centralHubToken" class="form-control"
                   value="{{ old('central_hub_api_token', $defaults['central_hub_api_token']) }}"
                   placeholder="Parent hub federation API token — exchanged for refreshable OAuth tokens">
        </div>

        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="import_branding" value="1" id="importBranding" checked>
                <label class="form-check-label" for="importBranding">Import theme &amp; branding settings</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="import_metadata" value="1" id="importMetadata" checked>
                <label class="form-check-label" for="importMetadata">Import publication &amp; forum lookup tables</label>
            </div>
        </div>

        <div class="col-12">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="testCentralBtn">
                Test connection
            </button>
            <span id="testCentralResult" class="small ms-2 text-muted"></span>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 justify-content-between">
            <a href="{{ route('install.site') }}" class="btn btn-link px-0">Back</a>
            <div class="d-flex gap-2">
                <button type="submit" name="skip_central" value="1" class="btn btn-outline-secondary">Skip for now</button>
                <button type="submit" class="btn btn-success">Connect &amp; import</button>
            </div>
        </div>
    </form>

    <div class="mt-4 p-3 bg-light rounded small">
        <strong>Endpoints used</strong>
        <ul class="mb-0 mt-2">
            <li><code>GET /api/federation/manifest</code></li>
            <li><code>GET /api/federation/lookup/settings</code> — branding for web &amp; mobile (<code>/api/lookup/settings</code>)</li>
            <li><code>GET /api/federation/lookup/metadata</code> — themes, tags, categories, licenses, static links</li>
        </ul>
    </div>
@endsection

@section('scripts')
<script>
(function () {
    var btn = document.getElementById('testCentralBtn');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var result = document.getElementById('testCentralResult');
        result.textContent = 'Testing…';
        result.className = 'small ms-2 text-muted';
        var body = new FormData();
        body.append('_token', '{{ csrf_token() }}');
        body.append('central_hub_url', document.getElementById('centralHubUrl').value);
        body.append('central_hub_api_token', document.getElementById('centralHubToken').value);
        fetch('{{ route('install.central.test') }}', {
            method: 'POST',
            body: body,
            headers: { 'Accept': 'application/json' }
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data.ok) {
                var name = (data.manifest && (data.manifest.site_name || data.manifest.title)) || 'Central hub';
                result.textContent = 'Connected to ' + name;
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
})();
</script>
@endsection
