@extends('layouts.app')

@section('styles')
    <style>
        .theme-text {
            color: {{ settings()->primary_color ?? '#119A48' }};
        }

        .community-card {
            border: 1px solid #e0e0e0;
            border-radius: 0.25rem;
            margin-bottom: 20px;
            padding: 1.5rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .community-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border-color: {{ settings()->primary_color ?? '#119A48' }};
        }

        .community-card h4 {
            margin-bottom: 0.75rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: #2d3748;
        }

        .community-card p {
            margin: 0 0 1rem 0;
            color: #4a5568;
            text-align: left;
            font-size: 0.9rem;
            line-height: 1.6;
            flex-grow: 1;
        }

        .join-btn,
        .leave-btn,
        .forum-btn,
        .publication-btn {
            font-size: 0.875rem;
            padding: 0.5rem 1.5rem;
            border: 1px solid {{ settings()->primary_color ?? '#119A48' }};
            color: {{ settings()->primary_color ?? '#119A48' }};
            background-color: transparent;
            transition: all 0.3s ease;
            border-radius: 0.25rem;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }

        .join-btn:hover,
        .leave-btn:hover,
        .forum-btn:hover,
        .publication-btn:hover {
            background-color: {{ settings()->primary_color ?? '#119A48' }};
            color: #fff;
            transform: translateY(-1px);
        }

        .page-title {
            margin-bottom: 2rem;
            padding: 2rem 0;
            text-align: center;
        }

        .page-title h3 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .page-title h6 {
            font-size: 1rem;
            color: #718096;
            font-weight: 400;
        }

        .community-stats {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 1rem;
            padding: 0.75rem 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .community-stats p {
            position: relative;
            padding: 0 0.5rem;
            font-size: 0.75rem;
            text-align: center;
            color: #718096;
            margin: 0;
        }

        .community-stats p:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -0.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 60%;
            background-color: #cbd5e0;
        }

        .community-stats i {
            margin-right: 0.25rem;
            color: {{ settings()->primary_color ?? '#119A48' }};
        }

        .btn-group {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
            gap: 0.5rem;
        }

        .btn-group > * {
            margin: 0.25rem;
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
            @forelse ($communities as $community)
                <div class="col-md-12 col-sm-12 col-lg-4 mb-4">
                    <div class="community-card">
                        <h4>
                            @if(request()->routeIs('account.my-communities'))
                                <a href="{{ route('community.detail', $community->id) }}" class="theme-text" style="text-decoration: none; color: inherit;">
                                    {{ $community->community_name }}
                                </a>
                            @else
                                {{ $community->community_name }}
                            @endif
                        </h4>
                        <p>{!! \Illuminate\Support\Str::words(strip_tags($community->description ?? ''), 30, '...') !!}</p>
                        <div class="community-stats">
                            <p><i class="fa fa-users theme-text"></i> {{ $community->members_count }} Members</p>
                            <p><i class="fa fa-comments theme-text"></i> {{ $community->forums_count }} Forums</p>
                            <p><i class="fa fa-book theme-text"></i> {{ $community->publications_count }} Resources</p>
                        </div>
                        @if (Auth::check())
                            @if (!$community->user_joined && !$community->user_pending_approval)
                                <div class="mt-2" style="text-align: center;">
                                    <button class="btn btn-sm join-btn" data-community-id="{{ $community->id }}">
                                        Join Community
                                    </button>
                                </div>
                            @elseif ($community->user_pending_approval)
                                <div class="mt-2" style="text-align: center;">
                                    <button class="btn btn-sm btn-warning" disabled>
                                        Request Pending Approval
                                    </button>
                                </div>
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
                                        Leave
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="mt-2" style="text-align: center;">
                                <a href="{{ route('login') }}" class="btn btn-sm join-btn">
                                    Login to Join
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle mr-2"></i>No communities found.
                    </div>
                </div>
            @endforelse
        </div>
        @if($communities->hasPages())
        <div class="row">
            <div class="col-md-12">
                {{ $communities->links() }}
            </div>
        </div>
        @endif
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
