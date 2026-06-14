@extends('layouts.plain', ['hide_search' => true])

@section('title', '403 — Access Denied')

@section('styles')
    @include('errors.partials.error_page_front_styles')
@endsection

@section('content')
    <div class="front-error-page">
        <div class="container">
            @include('errors.partials.resolve_error_message')
            @php
                $deniedMessage = $resolvedErrorMessage;
                if ($deniedMessage === '' || $deniedMessage === 'Forbidden' || $deniedMessage === null) {
                    $deniedMessage = 'You do not have permission to access this area.';
                }
            @endphp
            <div class="card front-error-card">
                <div class="card-header">
                    <h1 class="card-title">403 — Access Denied</h1>
                </div>
                <div class="card-body">
                    <div class="front-error-icon" aria-hidden="true">
                        <i class="fa fa-shield-alt"></i>
                    </div>
                    <p class="front-error-message">{{ $deniedMessage }}</p>
                    <p class="front-error-hint">
                        If you believe this is an error, contact your administrator or
                        <a href="mailto:{{ settings()->support_email ?? 'support@africacdc.org' }}">support</a>.
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
