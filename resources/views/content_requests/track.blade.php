@php
    $hide_search = true;
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0e7a3a';
    $auGold = settings()->au_gold ?? '#B4A269';
    $forumIds = $contentRequest->referralForumIds();
    $isProcessed = $contentRequest->isProcessed();
@endphp
@extends('layouts.app')

@section('styles')
@include('content_requests.partials.message_thread_styles')
<style>
    .cr-track-page {
        background: #f1f5f9;
        padding-bottom: 3rem;
    }

    .cr-track-hero {
        background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 55%, #0d5c2e 100%);
        color: #fff;
        padding: 2.5rem 0 2rem;
        position: relative;
        overflow: hidden;
    }

    .cr-track-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 85% 20%, rgba(255,255,255,0.12) 0%, transparent 45%),
            radial-gradient(circle at 10% 80%, rgba(180,162,105,0.25) 0%, transparent 40%);
        pointer-events: none;
    }

    .cr-track-hero__inner {
        position: relative;
        z-index: 1;
        max-width: 920px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .cr-track-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.9);
        margin-bottom: 0.75rem;
    }

    .cr-track-hero h1 {
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 700;
        margin: 0 0 0.75rem;
        line-height: 1.25;
    }

    .cr-track-hero__lead {
        margin: 0;
        color: rgba(255,255,255,0.92);
        font-size: 0.975rem;
        max-width: 42rem;
        line-height: 1.6;
    }

    .cr-track-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .cr-track-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 600;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.22);
        color: #fff;
    }

    .cr-track-pill--secure i { color: {{ $auGold }}; }

    .cr-track-pill--status-open { background: rgba(255,193,7,0.2); border-color: rgba(255,193,7,0.45); }
    .cr-track-pill--status-done { background: rgba(40,167,69,0.25); border-color: rgba(40,167,69,0.5); }

    .cr-track-shell {
        max-width: 920px;
        margin: -1.25rem auto 0;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }

    .cr-track-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .cr-track-card__header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .cr-track-card__header i {
        color: {{ $primaryColor }};
        font-size: 1.05rem;
    }

    .cr-track-card__body {
        padding: 1.25rem 1.5rem 1.5rem;
    }

    .cr-track-request-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.75rem;
        line-height: 1.35;
    }

    .cr-track-request-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 1.5rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    .cr-track-request-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .cr-track-request-meta i { color: {{ $primaryColor }}; }

    .cr-track-request-body {
        color: #334155;
        line-height: 1.75;
        border-top: 1px solid #edf2f7;
        padding-top: 1rem;
    }

    .cr-track-request-body.rich-text-content p:last-child { margin-bottom: 0; }

    .cr-track-forum-card {
        border: 2px solid {{ $primaryColor }};
        background: linear-gradient(135deg, rgba(17,154,72,0.04) 0%, rgba(180,162,105,0.08) 100%);
    }

    .cr-track-forum-card .cr-track-card__body p {
        color: #475569;
        font-size: 0.9375rem;
        line-height: 1.65;
        margin-bottom: 1rem;
    }

    .cr-track-forum-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .cr-track-forum-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 8px;
        background: {{ $primaryColor }};
        color: #fff !important;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        box-shadow: 0 2px 8px rgba(17,154,72,0.25);
    }

    .cr-track-forum-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(17,154,72,0.35);
        color: #fff !important;
        text-decoration: none;
    }

    .cr-track-section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.35rem;
    }

    .cr-track-section-lead {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.55;
    }

    .cr-track-compose .note-editor {
        border-radius: 8px;
        border-color: #e2e8f0;
    }

    .cr-track-compose .note-toolbar {
        background: #f8fafc;
        border-bottom-color: #e2e8f0;
        border-radius: 8px 8px 0 0;
    }

    .cr-track-compose-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1rem;
    }

    .cr-track-send-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.35rem;
        border: none;
        border-radius: 8px;
        background: {{ $primaryColor }};
        color: #fff;
        font-weight: 600;
        font-size: 0.9375rem;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
        box-shadow: 0 2px 8px rgba(17,154,72,0.2);
    }

    .cr-track-send-btn:hover {
        background: {{ $secondaryColor }};
        transform: translateY(-1px);
        color: #fff;
    }

    .cr-track-alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 8px rgba(15,23,42,0.06);
        margin-bottom: 1.25rem;
    }

    @media (max-width: 768px) {
        .cr-track-hero { padding: 2rem 0 1.5rem; }
        .cr-track-card__body { padding: 1rem; }
        .cr-track-message__time { margin-left: 0; width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="cr-track-page">
    <header class="cr-track-hero">
        <div class="cr-track-hero__inner">
            <div class="cr-track-hero__eyebrow">
                <i class="fa fa-inbox"></i> Content request tracking
            </div>
            <h1>Your request updates</h1>
            <p class="cr-track-hero__lead">
                Follow private notes from the Knowledge Hub team and join the community forum discussion when available.
                This page uses a secure link — please do not share the URL.
            </p>
            <div class="cr-track-hero__meta">
                <span class="cr-track-pill cr-track-pill--secure">
                    <i class="fa fa-lock"></i> Private link
                </span>
                @if($isProcessed)
                    <span class="cr-track-pill cr-track-pill--status-done">
                        <i class="fa fa-check-circle"></i> Processed
                    </span>
                @else
                    <span class="cr-track-pill cr-track-pill--status-open">
                        <i class="fa fa-clock-o"></i> In progress
                    </span>
                @endif
                @if($contentRequest->country)
                    <span class="cr-track-pill">
                        <i class="fa fa-globe"></i> {{ $contentRequest->country->name }}
                    </span>
                @endif
            </div>
        </div>
    </header>

    <div class="cr-track-shell">
        @if(session('success'))
            <div class="alert alert-success cr-track-alert mt-3">{{ session('success') }}</div>
        @endif

        <div class="cr-track-card">
            <div class="cr-track-card__header">
                <i class="fa fa-file-text-o"></i> Your request
            </div>
            <div class="cr-track-card__body">
                <h2 class="cr-track-request-title">{{ $contentRequest->subject }}</h2>
                <div class="cr-track-request-meta">
                    @if($contentRequest->country)
                        <span><i class="fa fa-map-marker"></i> {{ $contentRequest->country->name }}</span>
                    @endif
                    @if($contentRequest->created_at)
                        <span><i class="fa fa-calendar"></i> Submitted {{ $contentRequest->created_at->format('M j, Y') }}</span>
                    @endif
                    <span><i class="fa fa-tag"></i> {{ $contentRequest->processingMethodLabel() }}</span>
                </div>
                <div class="cr-track-request-body rich-text-content">
                    {!! cleanHtmlContent($contentRequest->description) !!}
                </div>
            </div>
        </div>

        @if($forumIds->isNotEmpty())
        <div class="cr-track-card cr-track-forum-card">
            <div class="cr-track-card__header">
                <i class="fa fa-comments"></i> Community forum discussion
            </div>
            <div class="cr-track-card__body">
                <p>
                    Experts are discussing your request in {{ $forumIds->count() === 1 ? 'a dedicated thread' : 'dedicated threads' }}
                    within {{ $forumIds->count() === 1 ? 'a community' : 'the listed communities' }}.
                    Sign in to read comments, follow the conversation, and see how the community is responding.
                </p>
                <div class="cr-track-forum-links">
                    @foreach($forumIds as $fid)
                        <a href="{{ forum_thread_url($fid) }}" class="cr-track-forum-btn" target="_blank" rel="noopener">
                            <i class="fa fa-external-link"></i>
                            Open forum thread{{ $forumIds->count() > 1 ? ' #' . $loop->iteration : '' }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="cr-track-card">
            <div class="cr-track-card__header">
                <i class="fa fa-envelope-o"></i> Private notes &amp; updates
            </div>
            <div class="cr-track-card__body">
                <h2 class="cr-track-section-title">Message thread</h2>
                <p class="cr-track-section-lead">
                    Messages here are visible only on this tracking page and to assigned hub participants.
                    For the wider community conversation, use the forum link above.
                </p>

                @include('content_requests.partials.message_thread', ['contentRequest' => $contentRequest])

                <div class="cr-track-compose mt-4 pt-3 border-top">
                    <h3 class="cr-track-section-title" style="font-size:1rem;">Add a message</h3>
                    <p class="cr-track-section-lead mb-3">Format your message with bold, lists, and links. The team will be notified by email.</p>
                    <form method="post" action="{{ route('content-request.track.message', ['token' => $token]) }}" id="cr-track-message-form">
                        @csrf
                        <div class="form-group mb-0">
                            <textarea name="body"
                                      id="cr_track_message_body"
                                      class="form-control @error('body') is-invalid @enderror"
                                      rows="5"
                                      required
                                      placeholder="Your message to the team…">{!! old('body') !!}</textarea>
                            @error('body')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="cr-track-compose-actions">
                            <button type="submit" class="cr-track-send-btn">
                                <i class="fa fa-paper-plane"></i> Send message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('partials.general.summernote')
<script>
$(document).ready(function() {
    var $ta = $('#cr_track_message_body');
    if ($ta.length && !$ta.next('.note-editor').length) {
        $ta.summernote({
            placeholder: 'Your message to the team…',
            tabsize: 2,
            height: 160,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ],
            callbacks: {
                onImageUpload: function() { /* images disabled on track messages */ }
            }
        });
    }

    $('#cr-track-message-form').on('submit', function() {
        if ($ta.length && $ta.data('summernote')) {
            $ta.val($ta.summernote('code'));
        }
    });
});
</script>
@endsection
