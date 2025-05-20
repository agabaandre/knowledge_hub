@extends('layouts.app')

@section('styles')
<style>
    body {
        background: #f4f6f9;
    }

    .section-heading {
        font-size: 1.25rem;
        font-weight: 600;
        color: #911C39; /* Primary Red */
        margin-bottom: 1rem;
    }

    .card-md {
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .badge-au {
        background-color: #119A48;
        color: #fff;
        font-size: 0.85rem;
        border-radius: 50px;
        padding: 0.35rem 0.75rem;
        margin-right: 0.5rem;
    }

    .badge-secondary {
        background-color: #7A7A7A;
        color: #fff;
    }

    .btn-au {
        background-color: #119A48;
        color: #fff;
    }

    .btn-au:hover {
        background-color: #0e7a3a;
        color: #fff;
    }

    .comment-box {
        background: #f1f1f1;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .metadata-list label {
        font-weight: 500;
        color: #5F5F5F;
    }

    .metadata-list span {
        display: block;
        margin-bottom: 0.75rem;
    }
</style>
@endsection

@section('content')

@php $likes = count($publication->favourited); @endphp

<!-- Header Section -->
<section class="py-5" style="background: #fff;">
    <div class="container">
        <div class="row align-items-center">

            <!-- Image -->
            <div class="col-md-3 mb-3 text-center">
                <img src="{{ $publication->image_url }}" class="img-fluid shadow rounded" alt="Cover Image">
            </div>

            <!-- Metadata -->
            <div class="col-md-6">
                <h3 class="font-weight-bold mb-2">{{ $publication->title }}</h3>
                <p class="text-muted">{{ $publication->theme->description ?? '' }}</p>

                <div class="d-flex flex-wrap mb-3">
                    <span class="badge badge-au">
                        {{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}
                    </span>
                    @if($likes)
                        <span class="badge badge-secondary">
                            <i class="lni lni-heart"></i> {{ $likes }} like{{ $likes > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>

                <button onclick="summarise({{ $publication->id }})" class="btn btn-au btn-sm">
                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                </button>
            </div>

            <!-- Actions -->
            <div class="col-md-3 text-md-right mt-3 mt-md-0">
                @if ($publication->publication)
                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success btn-block mb-2">
                        <i class="fa fa-eye"></i> Browse Resource
                    </a>
                @endif

                @auth
                    <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-block mb-2">
                        <i class="fa fa-plus"></i> Submit Version
                    </a>
                    <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-block">
                        <i class="fa fa-file"></i> Submit Summary
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="card-md">
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
                    <div class="metadata-list">
                        <label>Source</label><span>{{ $publication->author->name }}</span>
                        <label>Visits</label><span>{{ $publication->visits }}</span>
                        <label>Likes</label><span>{{ $likes }}</span>
                        <label>Category</label><span>{{ @$publication->data_category->category_name }}</span>
                        <label>Sub Category</label><span>{{ $publication->sub_category->category_name ?? '' }}</span>
                        <label>Theme</label><span>{!! $publication->theme->description ?? '' !!}</span>
                        <label>Sub-Theme</label><span>{!! nl2br($publication->sub_theme->description ?? '') !!}</span>
                        <label>Associated Authors</label><span>{{ $publication->associated_authors ?? 'N/A' }}</span>
                    </div>

                    @include('common.favourites_btn')

                    <h5 class="section-heading">Rating</h5>
                    @include('partials.general.rating')

                    <h5 class="section-heading">Share This Resource</h5>
                    {{ share_buttons(url('records/resource') . '?id=' . $publication->id) }}
                </div>

                <!-- Comments -->
                <div class="card-md">
                    <h5 class="section-heading">Comments ({{ count($publication->comments) }})</h5>
                    @foreach ($publication->comments as $comment)
                        @if ($comment->status === 'approved')
                            <div class="comment-box">
                                <strong>{{ $comment->user->name ?? 'Anonymous' }}</strong>
                                <small class="text-muted d-block">{{ time_ago($comment->created_at) }}</small>
                                <p class="mb-0">{{ nl2br($comment->comment) }}</p>
                            </div>
                        @endif
                    @endforeach

                    @auth
                        <form action="{{ url('records/comment') }}" method="post">
                            @csrf
                            <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                            <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                            <div class="form-group">
                                <label>Your comment</label>
                                <textarea name="comment" class="form-control" rows="3" required>{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-au btn-sm">Submit Comment</button>
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
                    <div class="card-md">
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

                @if (count($publication->versioning) || $publication->parent_id)
                    <div class="card-md">
                        <h5 class="section-heading">Versions</h5>
                        <ul class="list-group">
                            @foreach ($publication->versioning as $version)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $version->id }}">
                                        Version {{ $version->version_no }}
                                    </a>
                                </li>
                            @endforeach
                            @if ($publication->parent_id)
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
                    <div class="card-md">
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
