@include('errors.partials.resolve_error_message')
@php
    $notFoundMessage = $resolvedErrorMessage;
    if ($notFoundMessage === '' || $notFoundMessage === 'Not Found' || $notFoundMessage === null) {
        $notFoundMessage = 'The page you are looking for could not be found.';
    }
@endphp

<div class="page-header">
    <h1 class="page-title">Page Not Found</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Page Not Found</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card error-page-card">
            <div class="card-header">
                <h3 class="card-title mb-0">404 — Page Not Found</h3>
            </div>
            <div class="card-body error-page-body">
                <div class="error-page-icon" aria-hidden="true">
                    <i class="fa fa-search"></i>
                </div>
                <p class="error-page-message">{{ $notFoundMessage }}</p>
                <p class="error-page-hint text-muted">
                    The link may be broken or the page may have been moved. Try returning home or
                    <a href="mailto:{{ settings()->support_email ?? 'support@africacdc.org' }}">contact support</a> if you need help.
                </p>
                <div class="error-page-actions">
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
