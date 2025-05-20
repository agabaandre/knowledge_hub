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
@php
    $image_link = $publication->image_url;
@endphp

<!-- ======================= Publication Header ======================== -->
<div class="bg-light rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png') }}); background-repeat:repeat-x; background-size:contain;">
    <div class="container">
        @include('layouts.partials.alerts')

        <div class="row">
            <div class="col-12">
                <!-- Display PDF Canvas -->
                <canvas id="pdfCanvas"></canvas>

                <div class="jbd-01 d-flex justify-content-between align-items-start flex-wrap">
                    <div class="jbd-flex d-flex">
                        <!-- Thumbnail -->
                        <div class="jbd-01-thumb">
                            <img src="{{ $image_link }}" class="img-fluid" width="250" alt="" />
                        </div>

                        <!-- Title & Metadata -->
                        <div class="jbd-01-caption pl-3">
                            <h4 class="mb-0 ft-medium fs-md">{!! $publication->title !!}</h4>
                            <div class="jbl_location mb-3">
                                <span>{!! $publication->theme->description ?? '' !!}</span>
                            </div>
                            <div class="jbl_info01">
                                <span class="px-2 py-1 ft-medium text-light theme-bg rounded mr-2">
                                    {{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}
                                </span>
                                @if(count($publication->favourited) > 0)
                                    <span class="px-2 py-1 ft-medium text-light bg-dark rounded">
                                        <i class="lni lni-heart mr-1"></i>
                                        {{ count($publication->favourited) }} User{{ count($publication->favourited) > 1 ? 's' : '' }} liked this
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <a onclick="summarise({{ $publication->id }})" class="btn btn-md btn-success rounded fs-sm ft-medium text-white">
                                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="jbd-01-right text-right">
                        @if (!empty($publication->publication))
                            <a href="{{ $publication->publication }}" target="_blank" class="btn btn-sm rounded btn-outline-success fs-sm ft-medium mb-2" style="width:180px;">
                                <i class="fa fa-eye"></i> Browse Resource
                            </a>
                        @endif

                        @auth
                            <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-danger rounded fs-sm ft-medium mb-2" style="width:180px;">
                                <i class="fa fa-plus"></i> Submit a Version
                            </a>

                            <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-danger rounded fs-sm ft-medium" style="width:180px;">
                                <i class="fa fa-file"></i> Submit a Summary
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================ Publication Details Section ============================ -->
<section class="py-5">
    <div class="container">
        <div class="row">
            @php
                $col = (count($publication->summaries) > 0 || $publication->has_attachments || $publication->parent_id > 0) ? '7' : '12';
            @endphp

            <!-- Main Content -->
            <div class="col-xl-{{ $col }} col-lg-{{ $col }}">
                @if ($publication->is_embedded)
                    <div class="responsive-iframe-container mb-3">
                        <iframe src="{{ $publication->publication }}" frameborder="0" allowfullscreen></iframe>
                    </div>
                @endif

                <div class="rounded mb-4">
                    <div class="jbd-01 pr-3">
                        @if ($publication->is_video)
                            <iframe width="650" height="400" src="{{ $publication->publication }}"></iframe>
                        @endif

                        <h5 class="ft-medium fs-md mt-3">Description</h5>
                        <p>{!! $publication->description !!}</p>

                        <h5 class="ft-medium fs-md text-success mt-4">Resource Details</h5>
                        <div class="other-details">
                            <div class="details ft-medium"><label class="text-muted">Source</label> <span>{{ $publication->author->name }}</span></div>
                            <div class="details ft-medium"><label class="text-muted">No. of Visits</label> <span class="theme-bg text-light rounded px-2 py-1">{{ $publication->visits }} Visits</span></div>
                            <div class="details ft-medium"><label class="text-muted">Likes</label> <span class="theme-bg text-light rounded px-2 py-1">{{ count($publication->favourited) }} Likes</span></div>
                            <div class="details ft-medium"><label class="text-muted">Category</label> <span>{{ @$publication->data_category->category_name }}</span></div>
                            <div class="details ft-medium"><label class="text-muted">Sub Category</label> <span>{{ $publication->sub_category->category_name ?? '' }}</span></div>
                            <div class="details ft-medium"><label class="text-muted">Theme</label> <span>{!! $publication->theme->description ?? '' !!}</span></div>
                            <div class="details ft-medium"><label class="text-muted">Sub-Theme</label> <span>{!! nl2br($publication->sub_theme->description ?? '') !!}</span></div>
                            <div class="details ft-medium"><label class="text-muted">Associated Authors</label> <span>{{ $publication->associated_authors ?? 'N/A' }}</span></div>
                            <div class="details ft-medium mt-3">@include('common.favourites_btn')</div>
                        </div>
                    </div>

                    <div class="row container mt-4">
                        <div class="col-lg-12">
                            <h5 class="text-bold text-success">Rating</h5>
                            @include('partials.general.rating')
                        </div>
                    </div>

                    <div class="jbd-02 pt-4 container">
                        <h5 class="text-bold text-success">Share on:</h5>
                        <div class="row">
                            {{ share_buttons(url('records/resource') . '?id=' . $publication->id) }}
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="article_detail_wrapss single_article_wrap format-standard">
                    <div class="comment-area">
                        <h3 class="comments-title">{{ count($publication->comments) }} Comments</h3>
                        <ul class="comment-list">
                            @foreach ($publication->comments as $comment)
                                @if ($comment->status == 'approved')
                                    <li class="article_comments_wrap">
                                        <article>
                                            <div class="comment-details app-comment">
                                                <div class="comment-meta">
                                                    <div class="comment-left-meta">
                                                        <h4 class="author-name">{{ $comment->user->name ?? 'Anonymous' }}</h4>
                                                        <div class="comment-date">{{ time_ago($comment->created_at) }}</div>
                                                    </div>
                                                </div>
                                                <div class="comment-text">
                                                    <p>{{ nl2br($comment->comment) }}</p>
                                                </div>
                                            </div>
                                        </article>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            @if (count($publication->summaries) > 0 || $publication->has_attachments || $publication->parent_id > 0)
                <div class="col-xl-5 col-lg-5">
                    @if ($publication->has_attachments)
                        <h5>Attachments</h5>
                        <ul class="list-group mb-3">
                            @foreach ($publication->attachments as $i => $pub_file)
                                <li class="list-group-item">
                                    <a href="{{ $pub_file->file }}" target="_blank" class="fs-sm ft-medium">
                                        <i class="fa fa-download"></i> {{ $pub_file->description ?? 'View Attachment ' . ($i+1) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="jb-apply-form bg-white shadow rounded py-3 px-4 box-static">
                        @if(count($publication->versioning) > 0)
                            <h4 class="ft-medium fs-md mb-3">Resource Versions</h4>
                            <ul class="list-group mb-3">
                                @foreach ($publication->versioning as $version)
                                    <li><a href="{{ url('records/resource') }}?id={{ $version->id }}">Version {{ $version->version_no }}</a></li>
                                @endforeach
                            </ul>
                        @elseif($publication->parent_id > 0)
                            <h5><a class="btn btn-sm btn-outline-success rounded" href="{{ url('records/resource') }}?id={{ $publication->parent_id }}">
                                <i class="fa fa-link"></i> Original Version
                            </a></h5>
                        @endif

                        @if(count($publication->summaries) > 0)
                            <h6 class="ft-medium fs-sm mb-3">Summaries and Abstracts</h6>
                            <ul class="list-group mb-3">
                                @foreach ($publication->summaries as $summary)
                                    @if ($comment->is_approved == 1)
                                        <li><a href="{{ url('records/shortened') }}?id={{ $summary->id }}">
                                            {{ truncate($summary->title, 100) }} by {{ $summary->author->name ?? "" }}
                                        </a></li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        <!-- Comment Form -->
                        @auth
                            <h4 class="ft-medium fs-md mb-3">Got something to say about this resource?</h4>
                            <form action="{{ url('records/comment') }}" method="post" class="_apply_form_form">
                                @csrf
                                <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                                <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                                <div class="form-group">
                                    <label class="text-success mb-1 ft-medium">Your comment</label>
                                    <textarea name="comment" class="form-control" placeholder="Your comment" required>{{ old('comment') }}</textarea>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Submit Comment</button>
                                </div>
                            </form>
                        @else
                            <div class="form-group">
                                <label class="text-success mb-1 ft-medium">Your comment</label>
                                <textarea class="form-control" placeholder="Login to comment" disabled></textarea>
                            </div>
                            <div class="form-group mt-4">
                                <a href="{{ url('/login') }}" class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Login to Comment</a>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

@include('common.ai-summary')
@endsection
