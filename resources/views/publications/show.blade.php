@extends('layouts.app')

@section('styles')
<style>
    .hero-section {
        background-image: url('{{ asset('frontend/img/dots.png') }}');
        background-repeat: repeat-x;
        background-size: contain;
        padding: 3rem 0;
    }

    .resource-card {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    }

    .meta-badge {
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.35rem 0.75rem;
        margin-right: 0.5rem;
        border-radius: 20px;
    }

    .meta-badge.green {
        background-color: #119A48;
        color: white;
    }

    .meta-badge.dark {
        background-color: #333;
        color: white;
    }

    .resource-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .section-heading {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #119A48;
    }

    .comment-box {
        background: #f9f9f9;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')

@php $likes = count($publication->favourited); @endphp

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">

            <!-- Cover Image -->
            <div class="col-md-3 text-center mb-4 mb-md-0">
                <img src="{{ $publication->image_url }}" class="img-fluid shadow-sm rounded" alt="Resource cover">
            </div>

            <!-- Main Metadata -->
            <div class="col-md-6">
                <h3 class="font-weight-bold mb-2">{!! $publication->title !!}</h3>
                <p class="text-muted mb-3">{{ $publication->theme->description ?? '' }}</p>

                <div class="d-flex flex-wrap align-items-center mb-3">
                    <span class="meta-badge green">
                        {{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}
                    </span>
                    @if ($likes)
                        <span class="meta-badge dark">
                            <i class="lni lni-heart"></i> {{ $likes }} like{{ $likes > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>

                <button onclick="summarise({{ $publication->id }})" class="btn btn-success btn-sm">
                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                </button>
            </div>

            <!-- CTA Buttons -->
            <div class="col-md-3 resource-actions">
                @if (!empty($publication->publication))
                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success">
                        <i class="fa fa-eye"></i> Browse Resource
                    </a>
                @endif

                @auth
                    <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger">
                        <i class="fa fa-plus"></i> Submit Version
                    </a>
                    <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger">
                        <i class="fa fa-file"></i> Submit Summary
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row">

            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="resource-card mb-4">

                    @if ($publication->is_embedded)
                        <div class="responsive-iframe-container mb-4">
                            <iframe src="{{ $publication->publication }}" allowfullscreen></iframe>
                        </div>
                    @elseif ($publication->is_video)
                        <div class="mb-4">
                            <iframe width="100%" height="400" src="{{ $publication->publication }}"></iframe>
                        </div>
                    @endif

                    <h5 class="section-heading">Description</h5>
                    <p>{!! $publication->description !!}</p>

                    <h5 class="section-heading">Resource Details</h5>
                    <ul class="list-unstyled">
                        <li><strong>Source:</strong> {{ $publication->author->name }}</li>
                        <li><strong>No. of Visits:</strong> {{ $publication->visits }}</li>
                        <li><strong>Likes:</strong> {{ $likes }}</li>
                        <li><strong>Category:</strong> {{ @$publication->data_category->category_name }}</li>
                        <li><strong>Sub Category:</strong> {{ $publication->sub_category->category_name ?? '' }}</li>
                        <li><strong>Theme:</strong> {!! $publication->theme->description ?? '' !!}</li>
                        <li><strong>Sub-Theme:</strong> {!! nl2br($publication->sub_theme->description ?? '') !!}</li>
                        <li><strong>Associated Authors:</strong> {{ $publication->associated_authors ?? 'N/A' }}</li>
                    </ul>

                    @include('common.favourites_btn')

                    <h5 class="section-heading">Rating</h5>
                    @include('partials.general.rating')

                    <h5 class="section-heading">Share This Resource</h5>
                    {{ share_buttons(url('records/resource') . '?id=' . $publication->id) }}
                </div>

                <!-- Comments -->
                <div class="resource-card">
                    <h5 class="section-heading">Comments ({{ count($publication->comments) }})</h5>

                    @foreach ($publication->comments as $comment)
                        @if ($comment->status === 'approved')
                            <div class="comment-box">
                                <h6 class="mb-1">{{ $comment->user->name ?? 'Anonymous' }}
                                    <small class="text-muted"> • {{ time_ago($comment->created_at) }}</small>
                                </h6>
                                <p class="mb-0">{{ nl2br($comment->comment) }}</p>
                            </div>
                        @endif
                    @endforeach

                    <!-- Comment Form -->
                    @auth
                        <form action="{{ url('records/comment') }}" method="post">
                            @csrf
                            <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                            <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                            <div class="form-group">
                                <label>Your comment</label>
                                <textarea name="comment" class="form-control" required rows="3">{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Submit Comment</button>
                        </form>
                    @else
                        <p class="text-muted">Login to comment.</p>
                        <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                    @endauth
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                @if ($publication->has_attachments)
                    <div class="resource-card mb-4">
                        <h5 class="section-heading">Attachments</h5>
                        <ul class="list-group">
                            @foreach ($publication->attachments as $index => $pub_file)
                                <li class="list-group-item">
                                    <a href="{{ $pub_file->file }}" target="_blank">
                                        <i class="fa fa-download"></i> {{ $pub_file->description ?? 'Attachment ' . ($index + 1) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (count($publication->versioning) || $publication->parent_id > 0)
                    <div class="resource-card mb-4">
                        <h5 class="section-heading">Versions</h5>
                        <ul class="list-group">
                            @foreach ($publication->versioning as $version)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $version->id }}">
                                        Version {{ $version->version_no }}
                                    </a>
                                </li>
                            @endforeach
                            @if ($publication->parent_id > 0)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->parent_id }}">
                                        <i class="fa fa-link"></i> Original Version
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                @if (count($publication->summaries))
                    <div class="resource-card">
                        <h5 class="section-heading">Summaries & Abstracts</h5>
                        <ul class="list-group">
                            @foreach ($publication->summaries as $summary)
                                @if ($comment->is_approved == 1)
                                    <li class="list-group-item">
                                        <a href="{{ url('records/shortened') }}?id={{ $summary->id }}">
                                            {{ truncate($summary->title, 100) }} by {{ $summary->author->name ?? '' }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('common.ai-summary')
@endsection
