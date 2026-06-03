<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prerequisites — Knowledge Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 720px;">
    <h1 class="h4 text-danger mb-3">Application prerequisites not met</h1>
    <p class="text-muted">The application is installed but cannot start until the items below are resolved.</p>
    <ul class="list-group mb-4">
        @foreach ($checks as $check)
            <li class="list-group-item d-flex justify-content-between align-items-start gap-3">
                <span>{{ $check['label'] }}</span>
                <span class="badge bg-{{ $check['ok'] ? 'success' : 'danger' }}">{{ $check['message'] }}</span>
            </li>
        @endforeach
    </ul>
    <p class="small text-muted mb-0">Fix the failed checks, then refresh this page. If settings are missing and the installer is locked, configure the active row in the admin settings area or contact your system administrator.</p>
</div>
</body>
</html>
