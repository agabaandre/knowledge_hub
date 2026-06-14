<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>403 — Access Denied</title>
    <link rel="icon" href="{{ site_favicon_url() }}" type="{{ site_favicon_mime() }}" />
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @include('partials.theming.colors')
    <style>
        body {
            min-height: 100vh;
            background: #f1f5f9;
            font-family: var(--primary-font, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
        }
        .access-denied-header {
            background: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
            color: #fff;
            padding: 0.85rem 1.5rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12);
        }
        .access-denied-header img {
            height: {{ (int) (settings()->logo_scale ?? 80) }}px;
            max-height: 80px;
            width: auto;
            background: #fff;
            border-radius: 2px;
            padding: 2px 6px;
        }
        .access-denied-page {
            max-width: 720px;
            margin: 2.5rem auto;
            padding: 0 1rem 2rem;
        }
        .access-denied-card {
            border: 1px solid #e2e8f0;
            border-radius: 0;
            background: #fff;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
        }
        .access-denied-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }
        .access-denied-card .card-body {
            padding: 2rem 1.5rem;
        }
        .access-denied-body {
            text-align: center;
        }
        .access-denied-icon {
            font-size: 3rem;
            color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
            margin-bottom: 1rem;
        }
        .access-denied-message {
            font-size: 1.05rem;
            color: {{ settings()->au_grey_text ?? '#58595B' }};
            margin-bottom: 0.75rem;
            line-height: 1.6;
        }
        .access-denied-hint {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .access-denied-hint a {
            color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
        }
        .access-denied-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }
        .access-denied-actions .btn-primary {
            background: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
            border-color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
        }
        .access-denied-actions .btn-primary:hover {
            opacity: 0.92;
        }
    </style>
</head>
<body>
    <header class="access-denied-header d-flex align-items-center justify-content-center">
        @if (! empty(settings()->logo))
            <img src="{{ settings()->logo }}" alt="{{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}">
        @else
            <span class="fw-semibold">{{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}</span>
        @endif
    </header>

    <main class="access-denied-page">
        <div class="card access-denied-card">
            <div class="card-header">
                <h1 class="h5 mb-0">403 — Access Denied</h1>
            </div>
            <div class="card-body access-denied-body">
                @php
                    $deniedMessage = $exception?->getMessage() ?: ($message ?? null);
                    if ($deniedMessage === '' || $deniedMessage === 'Forbidden') {
                        $deniedMessage = 'You do not have permission to access this area.';
                    }
                @endphp
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
    </main>
</body>
</html>
