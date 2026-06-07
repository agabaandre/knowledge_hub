@extends('install.layout')

@section('title', 'SSO')

@section('content')
    <div class="step-badge text-muted mb-2">Step 8 of 9</div>
    <h2 class="h5 mb-3">Social sign-in (SSO)</h2>
    <p class="text-muted small">
        Optionally configure Microsoft, Google, and LinkedIn login. Settings are saved to the database
        and override matching <code>.env</code> values. You can skip this step and configure later under
        <strong>Admin → Configure</strong>.
    </p>

    @if(empty($fields))
        <div class="alert alert-warning">
            SSO columns are missing from the database. Run <code>php artisan migrate</code>, or continue to create the admin account.
        </div>
        <a href="{{ route('install.admin') }}" class="btn btn-success">Continue without SSO</a>
    @else
        <form method="post" action="{{ route('install.sso.store') }}" class="row g-3">
            @csrf
            @include('admin.settings.partials.sso_credentials_form', ['ssoFields' => $fields, 'compact' => true])

            <div class="col-12 d-flex gap-2">
                <a href="{{ route('install.mail') }}" class="btn btn-outline-secondary">Back</a>
                <a href="{{ route('install.admin') }}" class="btn btn-outline-secondary">Skip</a>
                <button type="submit" class="btn btn-success">Save SSO settings &amp; continue</button>
            </div>
        </form>
    @endif
@endsection
