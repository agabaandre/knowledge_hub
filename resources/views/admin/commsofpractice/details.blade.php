@extends('admin.layouts.tabular')

@section('styles')
    @include('common.table')
    <style>
        .stats-card {
            background-color: #f8f9fa;
            /* Light gray background */
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .stats-card p {
            margin: 0;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $community->community_name }} Members</h1>
    </div>

    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header text-left">
                <h3 class="card-title">Members of {{ $community->community_name }}</h3>
                <div class="card-subtitle stats-card">
                    <p>Total Members: <span class="badge badge-primary">{{ $totalMembers }}</span></p>
                    <p>Approved Members: <span class="badge badge-success">{{ $approvedCount }}</span></p>
                    <p>Pending Members: <span class="badge badge-warning">{{ $pendingCount }}</span></p>
                    <p>Rejected Members: <span class="badge badge-danger">{{ $rejectedCount }}</span></p>
                </div>
            </div>
            <div class="card-body text-left">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membership as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>{{ $member->user->name }}</td>
                                <td>{{ $member->user->email }}</td>
                                <td>
                                    @if ($member->is_approved == 1)
                                        <span class="badge badge-success">Approved</span>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="showModal({{ $member->id }}, 'reject')">Remove</button>
                                    @elseif ($member->is_approved == 2)
                                        <span class="badge badge-danger">Rejected</span>
                                        <button class="btn btn-success btn-sm"
                                            onclick="showModal({{ $member->id }}, 'approve')">Reconsider</button>
                                    @else
                                        <button class="btn btn-success btn-sm"
                                            onclick="showModal({{ $member->id }}, 'approve')">Approve</button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="showModal({{ $member->id }}, 'reject')">Reject</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <span id="actionType"></span> this member?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let memberId;
        let action;

        function showModal(id, actionType) {
            memberId = id;
            action = actionType;
            $('#actionType').text(actionType);
            $('#approvalModal').modal('show');
        }

        $('#confirmAction').on('click', function() {
            $.ajax({
                url: '{{ route('admin.commsofpractice.memberAction') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    member_id: memberId,
                    action: action
                },
                success: function(response) {
                    $('#approvalModal').modal('hide');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
@endsection
