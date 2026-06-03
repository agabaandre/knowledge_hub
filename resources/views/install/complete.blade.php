@extends('install.layout')

@section('title', 'Complete')

@section('content')
    <div class="text-center py-3">
        <div class="display-6 text-success mb-3">&#10003;</div>
        <h2 class="h5 mb-2">Installation complete</h2>
        <p class="text-muted">Knowledge Hub is ready. The web installer has been <strong>locked</strong> in site settings and cannot be run again.</p>
        <p class="text-muted small">Sign in with your administrator account. To change mail settings later, edit <code>.env</code> or use your server configuration.</p>
        <a href="{{ url('/') }}" class="btn btn-success">Go to site</a>
    </div>
@endsection
