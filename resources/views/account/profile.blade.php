@extends('layouts.plain')

@section('title', 'My account')

@section('styles')
<style>
    .account-profile-page {
        --ap-green: {{ settings()->au_corporate_green ?? '#119A48' }};
        background: #f4f5f7;
        padding: 2rem 0;
    }
    .account-profile-shell {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .account-profile-shell > .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f7f4 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }
    .account-profile-shell .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .account-profile-shell__subtitle {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0.25rem 0 0;
    }
    .account-profile-shell > .card-body,
    .account-profile-shell > .card-footer {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    .account-profile-photo {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
    }
    .account-profile-photo__preview {
        width: 112px;
        height: 112px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 4px solid #fff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.12);
        background: #e2e8f0 center/cover no-repeat;
    }
    .account-profile-photo__preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .account-profile-photo__actions .btn {
        border-radius: 999px;
        font-weight: 600;
    }
    .account-profile-section-title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
        margin: 0 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eef2f6;
    }
    .account-profile-field {
        margin-bottom: 1.1rem;
    }
    .account-profile-field .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .account-profile-field .form-control,
    .account-profile-field .select2-container {
        border-radius: 8px;
    }
    .account-profile-field .form-text,
    .account-profile-field small.text-muted {
        font-size: 0.78rem;
    }
    @media (max-width: 767.98px) {
        .account-profile-page.row {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
    }
</style>
@endsection

@section('content')
<section class="account-profile-page">
    <div class="container">
        @include('account.profile-data')
    </div>
</section>
@endsection

@section('scripts')
    @include('common.select2')
@endsection
