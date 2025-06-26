@extends('admin.layouts.main')

@section('styles')
@include('common.table')

<style>
    #forum-list .list-group-item:nth-child(even) {
        background-color: #FCFCFC;
    }

    #forum-list .list-group-item {
        border-bottom: 1px solid #BCBCBC;
        transition: background-color 0.8s ease;
    }

    #forum-list .list-group-item:hover {
        background-color: #F5F3F5;
        border-bottom-color: #000000;
    }

    #forum-list .list-group-item h5 {
        display: block;
        font-weight: 600;
        font-size: 28px;
        line-height: 1.2;
        margin: 5px 0 0;
    }

    .comment-wrapper {
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .comment-username {
        font-weight: bold;
    }

    .comment-time {
        color: #999;
    }

    .comment-content {
        margin-top: 5px;
    }

    .comment-actions {
        margin-top: 10px;
    }
</style>

@endsection

@section('content')
<div class="row">
    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h3 class="card-title float-left mt-2 mb-2">{{ $title ?? 'Moderate Forums' }}</h3>
            <hr>
        </div>
        <!-- Card Header With Form Filters -->
        <div class="card-header">
            <form class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <!-- Filter By Title -->
                        <div class="form-group">
                            <label for="title">Forum Title</label>
                            <input type="text" name="title" id="filterTitle" class="form-control" placeholder="Filter by title" value="{{ @$search->title ?? ''}}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <!-- Filter By Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" name="description" id="filterDescription" class="form-control" placeholder="Filter by description" value="{{ @$search->description ?? ''}}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <!-- Filter By Status (Pending, Approved, Rejected)   -->
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="filterStatus" class="form-control">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <!-- Filter By Date range -->
                        <div class="form-group">
                            <label for="title">Date Range</label>
                            <div class="input-group">
                                <input class="form-control" type="text" name="dates" id="filterDates" class="form-control" placeholder="Date" value="{{ @$search->date_range ?? ''}}">
                            </div>
                        </div>
                    </div>



                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <!-- Export Button -->
                        <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                        <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                        <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>

                    </div>


                </div>
            </form>
        </div>
        <div class="card-body text-left">
            <div id="forum-list" class="list-group">
                @foreach($forums as $forum)
                <div class="list-group-item d-flex flex-column">
                    <div class="row">
                        <div class="col-md-9">
                            <h5 class="mb-1">{{ $forum->forum_title }}</h5>
                            <div class="description-short" id="forumDescriptionShort{{ $forum->id }}">
                                <p class="mb-1">{{ Str::limit($forum->forum_description, 100) }}</p>
                            </div>
                            <div class="description-full collapse" id="forumDescriptionFull{{ $forum->id }}">
                                <p class="mb-1">{{ cleanHtmlContent($forum->forum_description) }}</p>
                            </div>
                            @if (str_word_count(cleanHtmlContent($forum->forum_description)) > 20)
                            <a href="#" class="read-more" data-forum-id="{{ $forum->id }}">Read More</a>
                            @endif
                            <p class="my-2 text-secondary"><strong>Author: {{ $forum->user->name }}</strong></p>
                        </div>
                        <div class="col-md-3 text-md-right">
                            <p class="mb-1">Published: {{ $forum->published_date }}</p>
                            <p class="mb-1">Comments: {{ count($forum->comments) }}</p>
                            <p class="mb-1">Status: {{ $forum->status }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <a href="#" data-toggle="collapse" data-target="#collapseComments{{ $forum->id }}" aria-expanded="false" aria-controls="collapseComments{{ $forum->id }}">
                                Show Latest Comments
                            </a>
                            <div class="collapse" id="collapseComments{{ $forum->id }}">
                                <div class="card card-body mt-3">
                                    @foreach($forum->comments as $comment)
                                    <div class="comment-wrapper">
                                        <div class="comment-header">
                                            <span class="comment-username">{{ $comment->user->name }}</span>
                                            <span class="comment-time">{{ $comment->created_at }}</span>
                                        </div>
                                        <div class="comment-content">
                                            <p>{{ Str::limit($comment->comment, 100) }}</p>
                                        </div>
                                        <div class="comment-actions">
                                            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editCommentModal{{ $comment->id }}">Edit</button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-auto">
                        <div class="col-md-12 text-right">
                            <div class="btn-group" role="group" aria-label="Actions">
                                <a class="btn btn-sm btn-success" href="{{ route('forums.approve', $forum->id) }}">
                                    <i class="fas fa-check"></i>
                                    <span>Approve</span>
                                </a>
                                <a class="btn btn-sm btn-danger" href="{{ route('forums.decline', $forum->id) }}">
                                    <i class="fas fa-times"></i>
                                    <span>Decline</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>


            <script>
                $(document).ready(function() {
                    $('.read-more').on('click', function(e) {
                        e.preventDefault();
                        var forumId = $(this).data('forum-id');
                        $('#forumDescriptionShort' + forumId).toggleClass('collapse');
                        $('#forumDescriptionFull' + forumId).toggleClass('collapse');
                        // Change text of button to 'Read Less' if class is not 'collapse'
                        if ($('#forumDescriptionFull' + forumId).hasClass('collapse')) {
                            $(this).text('Read More');
                        } else {
                            $(this).text('Read Less');
                        }
                    });
                });
            </script>



        </div>
        <!-- Card Footer with pagination -->
        <div class="card-footer">
            {{ $forums->links() }}
        </div>
    </div>
    @endsection



    @section('scripts')


    <script type="text/javascript">
        $(document).ready(function() {
            $('#filterButton').click(function() {
                $('#filterForm').submit();

            });

            $('#reset').click(function() {
                $('#filterForm').trigger('reset');
            })

            // Date range picker
            $('#filterDates').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },

            })


        });
    </script>
    @endsection
