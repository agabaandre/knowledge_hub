@php
    $resolvedErrorMessage = $message ?? null;
    if (isset($exception) && $exception instanceof \Throwable) {
        $resolvedErrorMessage = $exception->getMessage() ?: $resolvedErrorMessage;
    }
    $deniedMessage = $resolvedErrorMessage;
    if ($deniedMessage === '' || $deniedMessage === 'Forbidden' || $deniedMessage === null) {
        $deniedMessage = 'You do not have permission to access this area.';
    }
@endphp

<div class="page-header">
    <h1 class="page-title">Access Denied</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Access Denied</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card access-denied-card">
            <div class="card-header">
                <h3 class="card-title mb-0">403 — Access Denied</h3>
            </div>
            <div class="card-body access-denied-body">
                <div class="access-denied-icon" aria-hidden="true">
                    <i class="fa fa-shield-alt"></i>
                </div>
                <p class="access-denied-message">{{ $deniedMessage }}</p>
                <p class="access-denied-hint text-muted">
                    If you believe this is an error, contact your administrator or
                    <a href="mailto:{{ settings()->support_email ?? 'support@africacdc.org' }}">support</a>.
                </p>
                <div class="access-denied-actions">
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="fa fa-home mr-1"></i> Go to Homepage
                    </a>
                    @auth
                        <a href="{{ route('account.profile') }}" class="btn btn-outline-primary">
                            <i class="fa fa-user mr-1"></i> My Account
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fa fa-sign-in-alt mr-1"></i> Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
