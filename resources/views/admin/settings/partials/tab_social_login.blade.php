@if(!empty($ssoFields))
<div class="tab-pane fade" id="social-login" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-sign-in-alt"></i>
        {{ __('admin_configure.tab_social_login') }}
    </div>
    <p class="text-muted small mb-3">{{ __('admin_configure.social_login_intro') }}</p>
    @include('admin.settings.partials.sso_credentials_form', ['ssoFields' => $ssoFields, 'standalone' => true])
</div>
@endif
