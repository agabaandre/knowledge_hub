@extends('admin.layouts.tabular')

@section('styles')
    @include('common.table')
    <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
        .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
        .af-card-body{padding:16px}
        .stat-chip{display:inline-block;padding:6px 10px;border-radius:999px;font-size:.85rem;margin-right:8px}
        .stat-chip.total{background:#e2e8f0;color:#0f172a}
        .stat-chip.approved{background:#dcfce7;color:#166534}
        .stat-chip.pending{background:#fef9c3;color:#854d0e}
        .stat-chip.rejected{background:#fee2e2;color:#991b1b}
        .table thead th{background:#f8fafc;border-bottom:1px solid #e2e8f0}
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $community->community_name }}</h1>
    </div>

    <div class="row">
        <div class="card col-lg-12 af-card">
            <div class="af-card-header d-flex align-items-center justify-content-between">
                <div>
                    <strong>Members</strong>
                    <div class="text-muted" style="font-size:.9rem;">Manage membership for this community</div>
                </div>
                <div>
                    <span class="stat-chip total">Total: {{ $totalMembers }}</span>
                    <span class="stat-chip approved">Approved: {{ $approvedCount }}</span>
                    <span class="stat-chip pending">Pending: {{ $pendingCount }}</span>
                    <span class="stat-chip rejected">Rejected: {{ $rejectedCount }}</span>
                </div>
            </div>
            <div class="af-card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="af-card mb-3">
                            <div class="af-card-header"><strong>Recent Publications</strong></div>
                            <div class="af-card-body">
                                @if(isset($publications) && count($publications))
                                    <ul class="list-unstyled mb-0">
                                        @foreach($publications as $pub)
                                            <li class="mb-2">
                                                <a href="{{ url('records/resource') }}?id={{ $pub->id }}">{!! truncate($pub->title, 80) !!}</a>
                                                <div class="text-muted" style="font-size:.85rem;">{{ time_ago($pub->created_at) }}</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted">No publications yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="af-card mb-3">
                            <div class="af-card-header"><strong>Recent Forums</strong></div>
                            <div class="af-card-body">
                                @if(isset($forums) && count($forums))
                                    <ul class="list-unstyled mb-0">
                                        @foreach($forums as $f)
                                            <li class="mb-2">
                                                <a href="{{ url('forums/thread') }}?id={{ $f->id }}">{!! truncate($f->forum_title, 80) !!}</a>
                                                <div class="text-muted" style="font-size:.85rem;">by {{ $f->user->name ?? 'Unknown' }} · {{ time_ago($f->created_at) }}</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted">No forum threads yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <table id="members-table" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th style="width:220px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membership as $member)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $member->user->name }}</td>
                                <td>{{ $member->user->email }}</td>
                                <td>
                                    @if ($member->is_approved == 1)
                                        <span class="badge badge-success">Approved</span>
                                    @elseif ($member->is_approved == 2)
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($member->is_approved == 1)
                                        <button class="btn btn-outline-danger btn-sm"
                                            onclick="showModal({{ $member->id }}, 'reject')"><i class="fa fa-times mr-1"></i>Remove</button>
                                    @elseif ($member->is_approved == 2)
                                        <button class="btn btn-outline-success btn-sm"
                                            onclick="showModal({{ $member->id }}, 'approve')"><i class="fa fa-undo mr-1"></i>Reconsider</button>
                                    @else
                                        <button class="btn btn-outline-success btn-sm mr-1"
                                            onclick="showModal({{ $member->id }}, 'approve')"><i class="fa fa-check mr-1"></i>Approve</button>
                                        <button class="btn btn-outline-danger btn-sm"
                                            onclick="showModal({{ $member->id }}, 'reject')"><i class="fa fa-times mr-1"></i>Reject</button>
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
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script>
        // Initialize DataTable for members table
        $(function(){
            var table = $('#members-table').DataTable({
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [4] } // Disable sorting on Actions column
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search members by name or email...",
                    lengthMenu: "Show _MENU_ members per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ members",
                    infoEmpty: "No members available",
                    infoFiltered: "(filtered from _MAX_ total members)",
                    zeroRecords: "No matching members found"
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                drawCallback: function(){
                    // Reinitialize any tooltips or other interactive elements after table redraw
                }
            });
            
            // Customize search input styling
            $('.dataTables_filter input').addClass('form-control').css({
                'width': '300px',
                'display': 'inline-block',
                'margin-left': '10px'
            });
            
            // Add search icon to DataTables filter
            $('.dataTables_filter').prepend('<i class="fa fa-search" style="margin-right: 5px; color: #6c757d;"></i>');
        });
        
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
