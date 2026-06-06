@extends('install.layout')

@section('title', 'Database')

@section('content')
    <div class="step-badge text-muted mb-2">Step 2 of 6</div>
    <h2 class="h5 mb-3">Database connection</h2>
    <p class="text-muted small">
        @if(($runtime ?? 'local') === 'docker')
            Use the MySQL service name <code>mysql</code> when running via Docker Compose.
        @else
            Use your local MySQL/MariaDB host (usually <code>127.0.0.1</code>). Create an empty database named <code>{{ $defaults['database'] }}</code> first if it does not exist.
        @endif
        @if($hasExistingTables ?? false)
            Existing tables were detected in this database. You can skip migrations to keep the current schema and data.
        @else
            Migrations, roles, permissions, and baseline data are applied automatically unless you choose to skip them below.
        @endif
    </p>

    <form method="post" action="{{ route('install.database.store') }}" class="row g-3">
        @csrf
        <div class="col-md-8">
            <label class="form-label">Host</label>
            <input type="text" name="db_host" class="form-control" value="{{ old('db_host', $defaults['host']) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Port</label>
            <input type="number" name="db_port" class="form-control" value="{{ old('db_port', $defaults['port']) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Database name</label>
            <input type="text" name="db_database" class="form-control" value="{{ old('db_database', $defaults['database']) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="db_username" class="form-control" value="{{ old('db_username', $defaults['username']) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="password" name="db_password" class="form-control" value="{{ old('db_password', $defaults['password']) }}">
        </div>

        @if($hasExistingTables ?? false)
            <div class="col-12">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="skip_migrations"
                        id="skip_migrations"
                        value="1"
                        @checked(old('skip_migrations', $hasExistingData ?? false))
                    >
                    <label class="form-check-label" for="skip_migrations">
                        Skip migrations (use existing database schema and data)
                    </label>
                </div>
                <div class="form-text">
                    Leave this checked when reconnecting an already-populated database. Uncheck only for a fresh install on an empty database.
                </div>
            </div>
        @endif

        <div class="col-12 d-flex gap-2">
            <a href="{{ route('install.index') }}" class="btn btn-outline-secondary">Back</a>
            <button type="submit" class="btn btn-success">
                {{ ($hasExistingTables ?? false) ? 'Save connection and continue' : 'Run migrations' }}
            </button>
        </div>
    </form>
@endsection
