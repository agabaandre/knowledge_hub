@extends('layouts.app')

@section('styles')
<style>
    .responsive-iframe-container {
        position: relative;
        width: 100%;
        padding-top: 56.25%;
        overflow: hidden;
    }

    .responsive-iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
</style>
@endsection

@section('content')
<div class="bg-light py-4" style="background-image: url('{{ asset('frontend/img/dots.png') }}'); background-repeat: repeat-x;">
    <div class="container">
        @include('layouts.partials.alerts')
        <div class="card shadow-sm border-0 p-4 mb-4">
            <div class="row">
                <!-- Cover Image -->
                <div class="col-md-3 mb-3">
                    <img src="{{ $publication->image_url }}" alt="Publication Cover" class="img-fluid rounded">
                </div>
                <!-- Publication Info -->
                <div class="col-md-6">
                    <h4 class="mb-2 font-weight-bold">{!! $publication->title !!}</h4>
                    <p class="mb-1 text-muted">{!! $publication->theme->description ?? '' !!}</p>
                    <div class="mb-2">
                        <span class="badge badge-success px-3 py-1">{{ $publication->visits }} Visits</span>
                        <span class="badge badge-primary px-3 py-1">{{ count($publication->favourited) }} Likes</span>
                        @if (count($publication->favourited) > 0)
                            <div class="mt-2 text-muted">
                                <i class="lni lni-heart text-danger"></i> {{ count($publication->favourited) }}
                                user{{ count($publication->favourited) > 1 ? 's' : '' }} liked this
                            </div>
                        @endif
                    </div>
                    <p class="mb-1">
                        <strong>Version Info: </strong>
                        {{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}
                    </p>
                    <a onclick="summarise({{ $publication->id }})" class="btn btn-sm btn-success mt-3 text-white">
                        <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                    </a>
                    @if (!empty($publication->publication))
                        <button class="btn btn-sm btn-outline-success mt-2" data-toggle="modal" data-target="#pdfModal"
                            onclick="setIframeSrc('{{ $publication->publication }}')">
                            <i class="fa fa-eye"></i> Browse Resource
                        </button>
                    @endif
                </div>
                <!-- Actions -->
                <div class="col-md-3 text-md-right mt-3 mt-md-0">
                    @auth
                        <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-sm mb-2">
                            <i class="fa fa-plus"></i> Submit a Version
                        </a>
                        <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-file"></i> Submit a Summary
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-7">
                @if ($publication->is_embedded)
                    <div class="responsive-iframe-container mb-4">
                        <iframe src="{{ $publication->publication }}" frameborder="0" allowfullscreen></iframe>
                    </div>
                @elseif ($publication->is_video)
                    <div class="embed-responsive embed-responsive-16by9 mb-4">
                        <iframe class="embed-responsive-item" src="{{ $publication->publication }}" allowfullscreen></iframe>
                    </div>
                @endif
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">Description</h5>
                        <p class="card-text">{!! $publication->description !!}</p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">Resource Details</h5>
                        <ul class="list-unstyled">
                            <li><strong>Source:</strong> {{ $publication->author->name ?? 'N/A' }}</li>
                            <li><strong>Category:</strong> {{ $publication->data_category->category_name ?? 'N/A' }}</li>
                            <li><strong>Sub Category:</strong> {{ $publication->sub_category->category_name ?? 'N/A' }}</li>
                            <li><strong>Theme:</strong> {!! $publication->theme->description ?? 'N/A' !!}</li>
                            <li><strong>Sub-Theme:</strong> {!! nl2br($publication->sub_theme->description ?? 'N/A') !!}</li>
                            <li><strong>Associated Authors:</strong> {{ $publication->associated_authors ?? 'N/A' }}</li>
                        </ul>
                        <div class="mt-3">
                            @php $row = $publication; @endphp
                            @include('common.favourites_btn')
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">Rating</h5>
                        @include('partials.general.rating')
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">Share on:</h5>
                        <div class="d-flex">
                            {!! share_buttons(url('records/resource') . '?id=' . $publication->id) !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sidebar -->
            <div class="col-lg-5">
                @if ($publication->has_attachments)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Attachments</h5>
                            <ul class="list-group list-group-flush">
                                @foreach ($publication->attachments as $index => $attachment)
                                    <li class="list-group-item">
                                        <a href="{{ $attachment->file }}" target="_blank">
                                            <i class="fa fa-download"></i> {{ $attachment->description ?? 'Attachment ' . ($index + 1) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if (count($publication->versioning) > 0 || $publication->parent_id > 0)
                    <div class="card mb-4">
                        <div class="card-body">
                            @if (count($publication->versioning) > 0)
                                <h5 class="card-title">Resource Versions</h5>
                                <ul class="list-group list-group-flush">
                                    @foreach ($publication->versioning as $version)
                                        <li class="list-group-item">
                                            <a href="{{ url('records/resource') }}?id={{ $version->id }}">Version {{ $version->version_no }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif ($publication->parent_id > 0)
                                <h5 class="card-title">Original Version</h5>
                                <a href="{{ url('records/resource') }}?id={{ $publication->parent_id }}" class="btn btn-outline-success btn-sm">
                                    <i class="fa fa-link"></i> View Original
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
                @if (count($publication->summaries) > 0)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Summaries and Abstracts</h5>
                            <ul class="list-group list-group-flush">
                                @foreach ($publication->summaries as $summary)
                                    @if ($summary->is_approved)
                                        <li class="list-group-item">
                                            <a href="{{ url('records/shortened') }}?id={{ $summary->id }}">
                                                {{ truncate($summary->title, 100) }} by {{ $summary->author->name ?? 'N/A' }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Leave a Comment</h5>
                        @auth
                            <form action="{{ url('records/comment') }}" method="post">
                                @csrf
                                <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                                <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                                <div class="form-group">
                                    <label for="comment">Your Comment</label>
                                    <textarea name="comment" class="form-control" rows="4" required>{{ old('comment') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Submit Comment</button>
                            </form>
                        @else
                            <p>Please <a href="{{ url('/login') }}">login</a> to leave a comment.</p>
                        @endauth
                    </div>
               
::contentReference[oaicite:1]{index=1}
 
