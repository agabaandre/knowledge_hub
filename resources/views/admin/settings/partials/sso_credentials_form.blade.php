@php
    $ssoFields = $ssoFields ?? [];
    $compact = !empty($compact);
@endphp

@if(!empty($ssoFields))
    @if(!$compact)
        <hr class="my-3">
        <p class="text-muted small mb-3">
            Social login credentials are managed here (same approach as email settings). Values saved below are stored in the database and take effect immediately; matching keys in <code>.env</code> are cleared on save so the database remains the source of truth.
        </p>
    @endif

    <div class="{{ $compact ? 'col-12' : '' }}">
        <div class="card mb-3">
            <div class="card-header py-2">
                <strong><i class="lni lni-microsoft me-1" style="color:#00a1f1;"></i>Microsoft</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="microsoft_client_id">Client ID</label>
                        <input type="text" name="microsoft_client_id" id="microsoft_client_id" class="form-control"
                               value="{{ old('microsoft_client_id', $ssoFields['microsoft_client_id']['form_value'] ?? '') }}">
                        <small class="text-muted">Env fallback: <code>MICROSOFT_CLIENT_ID</code> or <code>EXCHANGE_CLIENT_ID</code></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="microsoft_client_secret">Client secret</label>
                        <input type="password" name="microsoft_client_secret" id="microsoft_client_secret" class="form-control" autocomplete="new-password"
                               placeholder="{{ !empty($ssoFields['microsoft_client_secret']['value']) ? '••••••••' : 'Paste secret to set or update' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="microsoft_redirect_uri">Redirect URI</label>
                        <input type="text" name="microsoft_redirect_uri" id="microsoft_redirect_uri" class="form-control"
                               value="{{ old('microsoft_redirect_uri', $ssoFields['microsoft_redirect_uri']['form_value'] ?? '') }}"
                               placeholder="{{ $ssoFields['microsoft_redirect_uri']['value'] ?? url('/auth/microsoft/callback') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="microsoft_tenant_id">Tenant ID</label>
                        <input type="text" name="microsoft_tenant_id" id="microsoft_tenant_id" class="form-control"
                               value="{{ old('microsoft_tenant_id', $ssoFields['microsoft_tenant_id']['form_value'] ?? 'common') }}">
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="enable_microsoft_login" value="0">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_microsoft_login" name="enable_microsoft_login" value="1"
                                   @checked(old('enable_microsoft_login', $ssoFields['enable_microsoft_login']['form_value'] ?? true))>
                            <label class="form-check-label" for="enable_microsoft_login">Enable Microsoft login</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header py-2">
                <strong><i class="lni lni-google me-1" style="color:#db4437;"></i>Google</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="google_client_id">Client ID</label>
                        <input type="text" name="google_client_id" id="google_client_id" class="form-control"
                               value="{{ old('google_client_id', $ssoFields['google_client_id']['form_value'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="google_client_secret">Client secret</label>
                        <input type="password" name="google_client_secret" id="google_client_secret" class="form-control" autocomplete="new-password"
                               placeholder="{{ !empty($ssoFields['google_client_secret']['value']) ? '••••••••' : 'Paste secret to set or update' }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="google_redirect_uri">Redirect URI</label>
                        <input type="text" name="google_redirect_uri" id="google_redirect_uri" class="form-control"
                               value="{{ old('google_redirect_uri', $ssoFields['google_redirect_uri']['form_value'] ?? '') }}"
                               placeholder="{{ $ssoFields['google_redirect_uri']['value'] ?? url('/auth/google/callback') }}">
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="enable_google_login" value="0">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_google_login" name="enable_google_login" value="1"
                                   @checked(old('enable_google_login', $ssoFields['enable_google_login']['form_value'] ?? true))>
                            <label class="form-check-label" for="enable_google_login">Enable Google login</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header py-2">
                <strong><i class="fab fa-linkedin me-1" style="color:#0077b5;"></i>LinkedIn</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="linkedin_client_id">Client ID</label>
                        <input type="text" name="linkedin_client_id" id="linkedin_client_id" class="form-control"
                               value="{{ old('linkedin_client_id', $ssoFields['linkedin_client_id']['form_value'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="linkedin_client_secret">Client secret</label>
                        <input type="password" name="linkedin_client_secret" id="linkedin_client_secret" class="form-control" autocomplete="new-password"
                               placeholder="{{ !empty($ssoFields['linkedin_client_secret']['value']) ? '••••••••' : 'Paste secret to set or update' }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="linkedin_redirect_uri">Redirect URI</label>
                        <input type="text" name="linkedin_redirect_uri" id="linkedin_redirect_uri" class="form-control"
                               value="{{ old('linkedin_redirect_uri', $ssoFields['linkedin_redirect_uri']['form_value'] ?? '') }}"
                               placeholder="{{ $ssoFields['linkedin_redirect_uri']['value'] ?? url('/auth/linkedin/callback') }}">
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="enable_linkedin_login" value="0">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_linkedin_login" name="enable_linkedin_login" value="1"
                                   @checked(old('enable_linkedin_login', $ssoFields['enable_linkedin_login']['form_value'] ?? true))>
                            <label class="form-check-label" for="enable_linkedin_login">Enable LinkedIn login</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
