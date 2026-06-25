@php $hide_search = true; @endphp
@extends('layouts.app')

@section('styles')
@include('content_requests.partials.message_thread_styles')
@endsection

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <h1 class="h4 mb-2">Content request discussion</h1>
    <p class="text-muted small mb-4">
        @if($contentRequest->referral_type === 'user')
            Referred to an individual on the hub.
        @else
            Referred to community: <strong>{{ $contentRequest->referredToCommunity->community_name ?? '—' }}</strong>.
        @endif
        Messages here are visible to the requester on their tracking page and emailed to them when you post.
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($contentRequest->isProcessed())
        <div class="alert alert-success">
            <i class="fa fa-check-circle mr-1"></i>This request is <strong>processed</strong> in the hub
            @if($contentRequest->processed_at)
                ({{ $contentRequest->processed_at->format('M j, Y') }}
                @if($contentRequest->processedBy) by {{ $contentRequest->processedBy->name }}@endif
                ).
            @endif
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5">{{ $contentRequest->subject }}</h2>
            <div class="small border-top pt-3 mt-2" style="max-height: 220px; overflow-y: auto;">
                {!! cleanHtmlContent($contentRequest->description) !!}
            </div>
        </div>
    </div>

    @if(!$contentRequest->isProcessed() && $contentRequest->userMayMarkReferralAsProcessed(auth()->user()))
    <div class="card mb-4 border-success" id="mark-processed">
        <div class="card-header bg-success text-white">
            <strong><i class="fa fa-check-circle mr-1"></i>Mark request as processed</strong>
        </div>
        <div class="card-body">
            <p class="text-muted small">Provide links to resources for the requester (same as hub admin processing). They receive an email with this information.</p>
            <form method="post" action="{{ route('content-request.referral.mark-processed', $contentRequest) }}">
                @csrf
                <div class="form-group">
                    <label for="content_links_mp"><strong>Content links</strong> <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content_links') is-invalid @enderror" id="content_links_mp" name="content_links" rows="6" required placeholder="Links or references, one per line or separated by commas…">{{ old('content_links') }}</textarea>
                    @error('content_links')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="admin_comments_mp">Comments for the requester (optional)</label>
                    <textarea class="form-control @error('admin_comments') is-invalid @enderror" id="admin_comments_mp" name="admin_comments" rows="3" maxlength="1000" placeholder="Optional note">{{ old('admin_comments') }}</textarea>
                    @error('admin_comments')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-check mr-1"></i>Mark processed &amp; email requester
                </button>
            </form>
        </div>
    </div>
    @endif

    <h2 class="h5 mb-3">Thread</h2>
    @include('content_requests.partials.message_thread', ['contentRequest' => $contentRequest])

    <div class="card">
        <div class="card-header"><strong>Post a message</strong></div>
        <div class="card-body">
            <form method="post" action="{{ route('content-request.referral.discuss.message', $contentRequest) }}">
                @csrf
                <div class="form-group">
                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="6" required placeholder="Share an update for the requester…">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Post &amp; email requester</button>
            </form>
        </div>
    </div>
</div>
@endsection
