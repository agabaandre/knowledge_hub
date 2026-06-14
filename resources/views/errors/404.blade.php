@extends('layouts.plain', ['hide_search' => true])

@section('title', '404 — Page Not Found')

@section('styles')
    @include('errors.partials.error_page_front_styles')
@endsection

@section('content')
    <div class="front-error-page">
        <div class="container">
            @include('errors.partials.resolve_error_message')
            @php
                $notFoundMessage = $resolvedErrorMessage;
                if ($notFoundMessage === '' || $notFoundMessage === 'Not Found' || $notFoundMessage === null) {
                    $notFoundMessage = 'The page you are looking for could not be found.';
                }
            @endphp
            <div class="card front-error-card">
                <div class="card-header">
                    <h1 class="card-title">404 — Page Not Found</h1>
                </div>
                <div class="card-body">
                    <div class="front-error-icon" aria-hidden="true">
                        <i class="fa fa-search"></i>
                    </div>
                    <p class="front-error-message">{{ $notFoundMessage }}</p>
                    <p class="front-error-hint">
                        The link may be broken or the page may have been moved. Try returning home or
                        <a href="mailto:{{ settings()->support_email ?? 'support@africacdc.org' }}">contact support</a> if you need help.
                    </p>
                    <div class="front-error-actions">
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fa fa-home me-1"></i> Go to Homepage
                        </a>
                        @auth
                            <a href="{{ route('account.profile') }}" class="btn btn-outline-primary">
                                <i class="fa fa-user me-1"></i> My Account
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fa fa-sign-in-alt me-1"></i> Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
