@extends(admin_layout())

@section('styles')
    <style>
        .access-denied-card {
            border: 1px solid #e2e8f0;
            border-radius: 0;
            margin-bottom: 1.5rem;
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
            max-width: 640px;
            margin: 0 auto;
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
@endsection

@section('content')
    @include('errors.partials.access_denied_content')
@endsection
