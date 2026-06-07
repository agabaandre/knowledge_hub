@extends('install.layout')

@section('title', 'Requirements')

@section('content')
    <div class="step-badge text-muted mb-2">Step 1 of 8</div>
    <h2 class="h5 mb-3">Server prerequisites</h2>
    <p class="text-muted small mb-3">
        Detected environment: <strong>{{ $requirements['runtime'] === 'docker' ? 'Docker / container stack' : 'Local server (Apache/Nginx + PHP)' }}</strong>.
        @if ($requirements['runtime'] === 'docker')
            Database host defaults to <code>mysql</code> on the next step.
        @else
            Database host defaults to <code>127.0.0.1</code> on the next step.
        @endif
    </p>

    <ul class="list-group mb-4">
        @foreach ($requirements['checks'] as $check)
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3">
                <span>
                    {{ $check['label'] }}
                    @if (empty($check['required']))
                        <span class="badge bg-secondary ms-1">optional</span>
                    @endif
                </span>
                <span class="badge bg-{{ $check['ok'] ? 'success' : ($check['required'] ?? true ? 'danger' : 'warning') }}">{{ $check['message'] }}</span>
            </li>
        @endforeach
    </ul>

    @if ($requirements['ok'])
        <a href="{{ route('install.database') }}" class="btn btn-success">Continue to database setup</a>
    @else
        <p class="text-danger mb-0">Fix the required failed checks above, then refresh this page.</p>
    @endif
@endsection
