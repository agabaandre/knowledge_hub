@if(!empty($ssoFields))
<div class="tab-pane fade" id="social-login" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-sign-in-alt"></i>
        Social Login
    </div>
    <p class="text-muted small mb-3">
        Configure Microsoft, Google, and LinkedIn sign-in. Credentials are saved separately from general settings — use <strong>Save social login settings</strong> at the bottom of this tab.
    </p>
    @include('admin.settings.partials.sso_credentials_form', ['ssoFields' => $ssoFields, 'standalone' => true])
</div>
@endif
