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

        .section-title {
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .details label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6c757d;
        }

        .details span {
            display: block;
            font-size: 0.95rem;
            color: #212529;
        }

        .comment-box {
            border-top: 1px solid #ddd;
            padding-top: 1.5rem;
        }
    </style>
@endsection

@section('content')

@php $image_link = $publication->image_url; @endphp

<!-- Hero Section -->
<section class="py-5 bg-light" style="background-image: url('{{ asset('frontend/img/dots.png') }}'); background-repeat:repeat-x; background-size:contain;">
    <div class="container">
        @include('layouts.partials.alerts')

        <div class="row">
            <div class="col-lg-3 mb-4 text-center">
                <img src="{{ $image_link }}" class="img-fluid rounded shadow-sm" alt="cover image">
            </div>

            <div class="col-lg-6 mb-4">
                <h3 class="font-weight-bold">{!! $publication->title !!}</h3>
                <p class="text-muted mb-1">{!! $publication->theme->description ?? '' !!}</p>
                <div class="mb-2">
                    <span class="badge badge-success">{{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}</span>
                    @if(count($publication->favourited))
                        <span class="badge badge-dark">
                            <i class="lni lni-heart"></i> {{ count($publication->favourited) }} like{{ count($publication->favourited) > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                <a onclick="summarise({{ $publication->id }})" class="btn btn-success btn-sm mt-2">
                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                </a>
            </div>

            <div class="col-lg-3 text-lg-right">
                @if (!empty($publication->publication))
                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success btn-sm mb-2 d-block">
                        <i class="fa fa-eye"></i> Browse Resource
                    </a>
                @endif

                @auth
                    <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-sm mb-2 d-block">
                        <i class="fa fa-plus"></i> Submit Version
                    </a>
                    <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-sm d-block">
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
            @php
                $mainCol = (count($publication->summaries) > 0 || $publication->has_attachments || $publication->parent_id > 0) ? 8 : 12;
            @endphp

            <div class="col-lg-{{ $mainCol }}">
                @if ($publication->is_embedded)
                    <div class="responsive-iframe-container mb-4">
                        <iframe src="{{ $publication->publication }}" allowfullscreen></iframe>
                    </div>
                @endif

                @if ($publication->is_video)
                    <div class="mb-4">
                        <iframe width="100%" height="400" src="{{ $publication->publication }}"></iframe>
                    </div>
                @endif

                <h5 class="section-title">Description</h5>
                <p>{!! $publication->description !!}</p>

                <h5 class="section-title text-success">Resource Details</h5>
                <div class="mb-4">
                    <div class="details mb-2"><label>Source</label><span>{{ $publication->author->name }}</span></div>
                    <div class="details mb-2"><label>No. of Visits</label><span>{{ $publication->visits }}</span></div>
                    <div class="details mb-2"><label>Likes</label><span>{{ count($publication->favourited) }}</span></div>
                    <div class="details mb-2"><label>Category</label><span>{{ @$publication->data_category->category_name }}</span></div>
                    <div class="details mb-2"><label>Sub Category</label><span>{{ $publication->sub_category->category_name ?? '' }}</span></div>
                    <div class="details mb-2"><label>Theme</label><span>{!! $publication->theme->description ?? '' !!}</span></div>
                    <div class="details mb-2"><label>Sub-Theme</label><span>{!! nl2br($publication->sub_theme->description ?? '') !!}</span></div>
                    <div class="details mb-2"><label>Associated Authors</label><span>{{ $publication->associated_authors ?? 'N/A' }}</span></div>
                    @include('common.favourites_btn')
                </div>

                <h5 class="section-title text-success">Rating</h5>
                @include('partials.general.rating')

                <h5 class="section-title text-success">Share This Resource</h5>
                <div class="mb-4">
                    {{ share_buttons(url('records/resource') . '?id=' . $publication->id) }}
                </div>

                <!-- Comments -->
                <div class="comment-box">
                    <h5 class="section-title">Comments ({{ count($publication->comments) }})</h5>
                    @foreach ($publication->comments as $comment)
                        @if ($comment->status == 'approved')
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="mb-1">{{ $comment->user->name ?? 'Anonymous' }} <small class="text-muted">• {{ time_ago($comment->created_at) }}</small></h6>
                                <p class="mb-0">{{ nl2br($comment->comment) }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            @if ($mainCol < 12)
                <div class="col-lg-4 mt-4 mt-lg-0">
                    @if ($publication->has_attachments)
                        <h5 class="section-title">Attachments</h5>
                        <ul class="list-group mb-4">
                            @foreach ($publication->attachments as $i => $pub_file)
                                <li class="list-group-item">
                                    <a href="{{ $pub_file->file }}" target="_blank">
                                        <i class="fa fa-download mr-1"></i> {{ $pub_file->description ?? 'View Attachment ' . ($i+1) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(count($publication->versioning) || $publication->parent_id)
                        <h5 class="section-title">Versions</h5>
                        <ul class="list-group mb-4">
                            @foreach ($publication->versioning as $version)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $version->id }}">
                                        Version {{ $version->version_no }}
                                    </a>
                                </li>
                            @endforeach
                            @if($publication->parent_id > 0)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->parent_id }}">
                                        <i class="fa fa-link"></i> Original Version
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif

                    @if(count($publication->summaries))
                        <h5 class="section-title">Summaries</h5>
                        <ul class="list-group mb-4">
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
                    @endif

                    <!-- Comment Form -->
                    <div class="bg-white rounded shadow p-3">
                        @auth
                            <h5 class="section-title">Leave a Comment</h5>
                            <form action="{{ url('records/comment') }}" method="post">
                                @csrf
                                <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                                <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                                <div class="form-group">
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Your comment" required>{{ old('comment') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm btn-block">Submit Comment</button>
                            </form>
                        @else
                            <p class="text-muted">Login to post a comment</p>
                            <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm btn-block">Login</a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

@include('common.ai-summary')
@endsection
