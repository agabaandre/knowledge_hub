@extends('install.layout')

@section('title', 'Admin account')

@section('content')
    <div class="step-badge text-muted mb-2">Step 8 of 8</div>
    <h2 class="h5 mb-3">Administrator account</h2>
    <p class="text-muted small">This user receives the <strong>Admin</strong> role with full access. Completing this step locks the installer permanently.</p>

    <form method="post" action="{{ route('install.admin.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
        </div>
        <div class="col-12">
            <label class="form-label">Application URL (optional)</label>
            <input type="url" name="app_url" class="form-control" value="{{ old('app_url', url('/')) }}" placeholder="http://localhost:8080/">
        </div>
        <div class="col-12 d-flex gap-2">
            <a href="{{ route('install.mail') }}" class="btn btn-outline-secondary">Back</a>
            <button type="submit" class="btn btn-success">Finish installation</button>
        </div>
    </form>
@endsection
