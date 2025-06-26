@extends('layouts.app')

@section('styles')
    <style>
        .theme-text {
            color: {{ settings()->primary_color }};
        }

        .community-card {
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            text-align: center;
        }

        .community-card:hover {
            transform: translateY(-5px);
        }

        .community-card h4 {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .community-card p {
            margin: 0;
            color: #555;
            text-align: left;
        }

        .join-btn,
        .leave-btn,
        .forum-btn,
        .publication-btn {
            font-size: 16px;
            padding: 10px;
            border: 1px solid {{ settings()->primary_color }};
            color: {{ settings()->primary_color }};
            background-color: transparent;
            transition: background-color 0.2s, color 0.2s;
        }

        .join-btn:hover,
        .leave-btn:hover,
        .forum-btn:hover,
        .publication-btn:hover {
            background-color: {{ settings()->primary_color }};
            color: #fff;
        }

        .page-title {
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        .community-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .community-stats p {
            position: relative;
            padding: 0 10px;
            font-size: 12px;
            text-align: center;
        }

        .community-stats p:nth-child(2)::before,
        .community-stats p:nth-child(2)::after {
            content: '';
            position: absolute;
            top: 0;
            height: 100%;
            border-left: 1px dotted #ccc;
            border-right: 1px dotted #ccc;
        }

        .community-stats p:nth-child(2)::before {
            left: 0;
        }

        .community-stats p:nth-child(2)::after {
            right: 0;
        }

        .community-stats i {
            margin-right: 5px;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="page-title pt-3 pb-3">
            <h3>Communities of Practice</h3>
            <h6>Join a community of practice to connect with peers, share knowledge, and participate in discussions.</h6>
        </div>
        <div class="row">
            @foreach ($communities as $community)
                <div class="col-md-12 col-sm-12 col-lg-4">
                    <div class="community-card">
                        <h4>{{ $community->community_name }}</h4>
                        <p>{!! $community->description ?? '<br>' !!}</p>
                        <div class="community-stats">
                            <p><i class="fa fa-users theme-text"></i> {{ $community->members_count }} Members</p>
                            <p><i class="fa fa-comments theme-text"></i> {{ $community->forums_count }} Forums
                            </p>
                            <p><i class="fa fa-book theme-text"></i> {{ $community->publications_count }}
                                Resources</p>
                        </div>
                        @if (Auth::check())
                            @if (!$community->user_joined && !$community->user_pending_approval)
                                <button class="mt-2 btn btn-sm join-btn col-lg-12" data-community-id="{{ $community->id }}">
                                    Join Community
                                </button>
                            @elseif ($community->user_pending_approval)
                                <button class="mt-2 btn btn-sm btn-warning col-lg-12">
                                    Request Pending Approval
                                </button>
                            @else
                                <div class="btn-group" role="group" aria-label="Community Actions">
                                    <a href="{{ url('/records') }}?community_id={{ $community->id }}"
                                        class="btn btn-sm publication-btn">
                                        Publications
                                    </a>
                                    <a href="{{ url('/forums') }}?community_id={{ $community->id }}"
                                        class="btn btn-sm forum-btn">
                                        Forums
                                    </a>
                                    <button class="btn btn-sm leave-btn" data-community-id="{{ $community->id }}">
                                        Leave Community
                                    </button>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="mt-2 btn btn-sm join-btn">
                                Login to Join
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-12">
                {{ $communities->links() }}
            </div>
        </div>
    </div>

    <!-- Join Modal -->
    <div class="modal fade" id="joinModal" tabindex="-1" role="dialog" aria-labelledby="joinModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joinModalLabel">Join Community</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to join this community?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmJoin">Join</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Modal -->
    <div class="modal fade" id="leaveModal" tabindex="-1" role="dialog" aria-labelledby="leaveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveModalLabel">Leave Community</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to leave this community?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLeave">Leave</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let communityId;

        $('.join-btn').on('click', function() {
            communityId = $(this).data('community-id');
            @if (Auth::check())
                $('#joinModal').modal('show');
            @else
                window.location.href = '/login';
            @endif
        });

        $('#confirmJoin').on('click', function() {
            $.ajax({
                url: '{{ route('community.join') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    community_id: communityId
                },
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        $('#joinModal').modal('hide');
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        $('.leave-btn').on('click', function() {
            communityId = $(this).data('community-id');
            $('#leaveModal').modal('show');
        });

        $('#confirmLeave').on('click', function() {
            $.ajax({
                url: '{{ route('community.leave') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    community_id: communityId
                },
                success: function(response) {
                    $('#leaveModal').modal('hide');
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
@endsection
