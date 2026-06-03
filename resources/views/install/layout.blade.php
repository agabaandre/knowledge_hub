<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Install') — Knowledge Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; }
        .install-card { max-width: 720px; margin: 2rem auto; }
        .step-badge { font-size: .75rem; letter-spacing: .04em; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="container install-card">
    <div class="text-center mb-4">
        <h1 class="h3 text-success fw-bold">Knowledge Hub Installer</h1>
        <p class="text-muted mb-0">Set up the database and create your administrator account.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
