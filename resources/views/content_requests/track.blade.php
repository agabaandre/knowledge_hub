@php $hide_search = true; @endphp
@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <h1 class="h4 mb-3">Content request — updates</h1>
    <p class="text-muted small">You are viewing this thread with a private link. Do not share the URL.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($contentRequest->referral_forum_id)
    <div class="card mb-4 border-primary" style="border-width: 2px;">
        <div class="card-body">
            <h2 class="h6 card-title"><i class="fa fa-comments mr-2"></i>Community forum discussion</h2>
            <p class="small mb-2">Experts are discussing your request in a thread scoped to the community. Sign in to read all comments and follow the conversation in one place.</p>
            <a href="{{ url('forums/thread?id='.$contentRequest->referral_forum_id) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener">Open forum thread</a>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5">{{ $contentRequest->subject }}</h2>
            @if($contentRequest->country)
                <p class="small text-muted mb-2"><strong>Country:</strong> {{ $contentRequest->country->name }}</p>
            @endif
            <div class="small border-top pt-3 mt-2" style="max-height: 200px; overflow-y: auto;">
                {!! cleanHtmlContent($contentRequest->description) !!}
            </div>
        </div>
    </div>

    <h2 class="h5 mb-3">Private notes &amp; updates</h2>
    <p class="text-muted small">Messages here are separate from the community forum. Use the forum link above for the main discussion.</p>
    @include('content_requests.partials.message_thread', ['contentRequest' => $contentRequest])

    <div class="card">
        <div class="card-header"><strong>Add a message</strong></div>
        <div class="card-body">
            <form method="post" action="{{ route('content-request.track.message', ['token' => $token]) }}">
                @csrf
                <div class="form-group">
                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="5" required placeholder="Your message to the team…">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</div>
@endsection
