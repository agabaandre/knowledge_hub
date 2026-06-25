@extends(admin_layout('tabular'))

@section('styles')
    @include('common.table')
    <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
        .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
        .af-card-body{padding:16px}
        .stat-chip{display:inline-block;padding:6px 10px;border-radius:999px;font-size:.85rem;margin-right:8px;border:none}
        /* Traffic-light states: use Bootstrap semantic colors (success/warning/danger/primary/secondary) */
        .stat-chip.total{background:var(--bs-secondary,#6c757d);color:#fff}
        .stat-chip.approved{background:var(--bs-success,#198754);color:#fff}
        .stat-chip.pending{background:var(--bs-warning,#ffc107);color:#212529}
        .stat-chip.rejected{background:var(--bs-danger,#dc3545);color:#fff}
        .stat-chip.sent{background:var(--bs-primary,#0d6efd);color:#fff}
        .stat-chip.responded{background:var(--bs-success,#198754);color:#fff}
        .stat-chip.expired{background:var(--bs-danger,#dc3545);color:#fff}
        .table thead th{background:#f8fafc;border-bottom:1px solid #e2e8f0}
        .nav-tabs .nav-link{color:#64748b;border:none;border-bottom:2px solid transparent}
        .nav-tabs .nav-link.active{color:#119A48;border-bottom-color:#119A48;font-weight:600}
        .nav-tabs .nav-link:hover{color:#119A48;border-bottom-color:#e2e8f0}
        /* Traffic-light badges in tables: use Bootstrap semantic colors */
        #members-table .badge-success, #invitations-table .badge-success { background-color: var(--bs-success, #198754) !important; color: #fff !important; }
        #members-table .badge-warning, #invitations-table .badge-warning { background-color: var(--bs-warning, #ffc107) !important; color: #212529 !important; }
        #members-table .badge-danger, #invitations-table .badge-danger { background-color: var(--bs-danger, #dc3545) !important; color: #fff !important; }
        #invitations-table .badge-info { background-color: var(--bs-info, #0dcaf0) !important; color: #fff !important; }
        /* Checkbox column alignment in members table */
        #members-table thead th:first-child,
        #members-table tbody td:first-child { vertical-align: middle !important; text-align: center; }
        #members-table .form-check-input.member-pending-cb,
        #members-table #select-all-pending { margin: 0; vertical-align: middle; }
        #members-table tbody td { vertical-align: middle !important; }
    </style>
@endsection

@section('content')
    @if(session('alert'))
        <div class="alert alert-{{ session('alert_class', 'info') }} alert-dismissible fade show" role="alert">
            {{ session('alert') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    <div class="page-header">
        <h1 class="page-title">{{ $community->community_name }}</h1>
    </div>

    <div class="row">
        <div class="card col-lg-12 af-card">
            <div class="af-card-header d-flex align-items-center justify-content-between">
                <div>
                    <strong>Community Management</strong>
                    <div class="text-muted" style="font-size:.9rem;">Manage members and invitations for this community</div>
                </div>
                <div>
                    <span class="stat-chip total">Total Members: {{ $totalMembers }}</span>
                    <span class="stat-chip approved">Approved: {{ $approvedCount }}</span>
                    <span class="stat-chip pending">Pending: {{ $pendingCount }}</span>
                </div>
            </div>
            <div class="af-card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="communityTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="members-tab" data-toggle="tab" href="#members" role="tab" aria-controls="members" aria-selected="true">
                            Members
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="invitations-tab" data-toggle="tab" href="#invitations" role="tab" aria-controls="invitations" aria-selected="false">
                            Invitations
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="communityTabsContent">
                    <!-- Members Tab -->
                    <div class="tab-pane fade show active" id="members" role="tabpanel" aria-labelledby="members-tab">
                        <div class="mb-3">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMemberModal">
                                <i class="fa fa-user-plus mr-1"></i>Add member
                            </button>
                        </div>
                        @if($pendingCount > 0)
                        @can('moderate_cop_participants')
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="text-muted small">Select participants to approve or reject:</span>
                            <button type="button" class="btn btn-success btn-sm" id="bulk-approve-btn" disabled>
                                <i class="fa fa-check mr-1"></i>Approve selected
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" id="bulk-reject-btn" disabled>
                                <i class="fa fa-times mr-1"></i>Reject selected
                            </button>
                        </div>
                        @endcan
                        @endif
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <div class="af-card mb-3">
                                    <div class="af-card-header"><strong>Recent Publications</strong></div>
                                    <div class="af-card-body">
                                        @if(isset($publications) && count($publications))
                                            <ul class="list-unstyled mb-0">
                                                @foreach($publications as $pub)
                                                    <li class="mb-2">
                                                        <a href="{{ publication_url($pub)}}">{!! truncate($pub->title, 80) !!}</a>
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
                                                        <a href="{{ forum_thread_url($f)}}">{!! truncate($f->forum_title, 80) !!}</a>
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
                                    <th style="width:42px;" class="text-center align-middle">
                                        @if($pendingCount > 0 && auth()->user()?->can('moderate_cop_participants'))
                                        <input type="checkbox" id="select-all-pending" class="form-check-input" title="Select all pending on this page" aria-label="Select all pending">
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </th>
                                    <th style="width:60px;">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Job title</th>
                            <th>Organisation</th>
                                    <th>Status</th>
                                    <th style="width:220px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membership as $member)
                            <tr class="{{ $member->is_approved == 0 ? 'member-row-pending' : '' }}" data-member-id="{{ $member->id }}">
                                        <td class="text-center align-middle">
                                            @if ($member->is_approved == 0 && auth()->user()?->can('moderate_cop_participants'))
                                                <input type="checkbox" class="form-check-input member-pending-cb" value="{{ $member->id }}" data-member-id="{{ $member->id }}" aria-label="Select member">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                <td>{{ $member->user->name }}</td>
                                <td>
                                    @if(!empty($member->user->email))
                                        <a href="mailto:{{ e($member->user->email) }}">{{ $member->user->email }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $phoneRaw = trim((string) ($member->user->phone_number ?? ''));
                                        $phoneHref = $phoneRaw !== '' ? preg_replace('/[^\d+]/', '', $phoneRaw) : '';
                                    @endphp
                                    @if($phoneRaw !== '' && $phoneHref !== '')
                                        <a href="tel:{{ e($phoneHref) }}">{{ $phoneRaw }}</a>
                                    @elseif($phoneRaw !== '')
                                        <span>{{ $phoneRaw }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ trim((string) ($member->user->job_title ?? '')) !== '' ? $member->user->job_title : '—' }}</td>
                                <td>{{ trim((string) ($member->user->organization_name ?? '')) !== '' ? $member->user->organization_name : '—' }}</td>
                                <td>
                                    @if(($member->is_admin ?? false) && $member->is_approved == 1)
                                        <span class="badge badge-primary mr-1">Admin</span>
                                    @endif
                                    @if ($member->is_approved == 1)
                                        <span class="badge badge-success">Approved</span>
                                        @if(($member->is_active ?? true) == false)
                                            <span class="badge badge-danger ml-1">Inactive</span>
                                        @endif
                                    @elseif ($member->is_approved == 2)
                                        <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($member->is_approved == 1)
                                                @if(($member->is_admin ?? false))
                                                    <button type="button" class="btn btn-outline-secondary btn-sm mr-1 js-member-action" data-member-id="{{ $member->id }}" data-action="remove_admin"><i class="fa fa-user-times mr-1"></i>Remove admin</button>
                                                @else
                                                    <button type="button" class="btn btn-outline-primary btn-sm mr-1 js-member-action" data-member-id="{{ $member->id }}" data-action="make_admin"><i class="fa fa-user-secret mr-1"></i>Make admin</button>
                                                @endif
                                                @if(($member->is_active ?? true))
                                                    <button type="button" class="btn btn-outline-warning btn-sm mr-1 js-member-action" data-member-id="{{ $member->id }}" data-action="deactivate"><i class="fa fa-pause mr-1"></i>Mark inactive</button>
                                                @else
                                                    <button type="button" class="btn btn-outline-success btn-sm mr-1 js-member-action" data-member-id="{{ $member->id }}" data-action="activate"><i class="fa fa-play mr-1"></i>Mark active</button>
                                                @endif
                                                @can('moderate_cop_participants')
                                                <button type="button" class="btn btn-outline-danger btn-sm js-member-action" data-member-id="{{ $member->id }}" data-action="reject"><i class="fa fa-times mr-1"></i>Remove</button>
                                                @endcan
                                            @elseif ($member->is_approved == 2)
                                                @can('moderate_cop_participants')
                                                <button type="button" class="btn btn-outline-success btn-sm mr-1 js-member-action" data-member-id="{{ $member->id }}" data-action="approve"><i class="fa fa-undo mr-1"></i>Reconsider</button>
                                                @endcan
                                                <button type="button" class="btn btn-outline-danger btn-sm js-member-action" data-member-id="{{ $member->id }}" data-action="delete" title="Permanently remove this rejected request"><i class="fa fa-trash mr-1"></i>Delete</button>
                                            @else
                                                @can('moderate_cop_participants')
                                                <button type="button" class="btn btn-outline-success btn-sm mr-1 js-member-action" data-member-id="{{ $member->id }}" data-action="approve"><i class="fa fa-check mr-1"></i>Approve</button>
                                                <button type="button" class="btn btn-outline-danger btn-sm js-member-action" data-member-id="{{ $member->id }}" data-action="reject"><i class="fa fa-times mr-1"></i>Reject</button>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Invitations Tab -->
                    <div class="tab-pane fade" id="invitations" role="tabpanel" aria-labelledby="invitations-tab">
                        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sendInvitationModal">
                                <i class="fa fa-envelope mr-1"></i>Send Invitation
                            </button>
                            <button class="btn btn-outline-primary btn-sm" type="button" data-toggle="modal" data-target="#importCsvModal">
                                <i class="fa fa-upload mr-1"></i>Import from CSV
                            </button>
                        </div>
                        
                        @php
                            $sentCount = $invitations->whereNull('responded_at')->where('expires_at', '>', now())->count();
                            $respondedCount = $invitations->whereNotNull('responded_at')->count();
                            $expiredCount = $invitations->whereNull('responded_at')->where('expires_at', '<=', now())->count();
                        @endphp
                        
                        <div class="mb-3">
                            <span class="stat-chip sent">Sent: {{ $sentCount }}</span>
                            <span class="stat-chip responded">Responded: {{ $respondedCount }}</span>
                            <span class="stat-chip expired">Expired: {{ $expiredCount }}</span>
                        </div>

                        <table id="invitations-table" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Email</th>
                                    <th>Invited By</th>
                                    <th>Sent Date</th>
                                    <th>Expires At</th>
                                    <th>Status</th>
                                    <th>Responded At</th>
                                    <th style="width:160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invitations as $invitation)
                                    <tr data-invitation-id="{{ $invitation->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if(!empty($invitation->email))
                                                <a href="mailto:{{ e($invitation->email) }}">{{ $invitation->email }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $invitation->inviter->name ?? 'Unknown' }}</td>
                                        <td>{{ $invitation->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $invitation->expires_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($invitation->responded_at)
                                                <span class="badge badge-success">Responded</span>
                                            @elseif($invitation->expires_at->isPast())
                                                <span class="badge badge-danger">Expired</span>
                                            @else
                                                <span class="badge badge-info">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($invitation->responded_at)
                                                {{ $invitation->responded_at->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$invitation->responded_at)
                                                <button type="button" class="btn btn-outline-primary btn-sm js-resend-invitation" data-invitation-id="{{ $invitation->id }}" data-community-id="{{ $community->id }}" data-resend-url="{{ route('admin.commsofpractice.resendInvitation') }}" title="Resend invitation">
                                                    <i class="fa fa-redo mr-1"></i>Resend
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger btn-sm js-delete-invitation" data-invitation-id="{{ $invitation->id }}" data-community-id="{{ $community->id }}" data-delete-url="{{ route('admin.commsofpractice.deleteInvitation') }}" title="Delete invitation">
                                                <i class="fa fa-trash mr-1"></i>Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="approvalModalText">Are you sure you want to <span id="actionType"></span> this member?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Invitation Modal -->
    <div class="modal fade" id="sendInvitationModal" tabindex="-1" role="dialog" aria-labelledby="sendInvitationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendInvitationModalLabel">Send Invitation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="sendInvitationForm" method="POST" action="{{ route('admin.commsofpractice.sendInvitation') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="invitationEmail">Email Address(es)</label>
                            <textarea class="form-control" id="invitationEmail" name="email" rows="3" required placeholder="Enter one or more email addresses, separated by commas"></textarea>
                            <small class="form-text text-muted">Enter multiple emails separated by commas. Existing members and pending invitations for this community are skipped. Invitations expire in 7 days.</small>
                            <small class="form-text text-muted d-block">Maximum 5 users per request.</small>
                        </div>
                        <input type="hidden" name="community_id" value="{{ $community->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="sendInvitationSubmitBtn">Send Invitation(s)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import from CSV Modal -->
    <div class="modal fade" id="importCsvModal" tabindex="-1" role="dialog" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importCsvModalLabel">Import Invitations from CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importCsvForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="csvFile">CSV File</label>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <input type="file" class="form-control-file" id="csvFile" name="csv_file" accept=".csv,.txt" required>
                                <a href="#" class="btn btn-outline-secondary btn-sm" id="downloadCsvTemplate" title="Download a template with one example row">
                                    <i class="fa fa-download mr-1"></i>Download CSV template
                                </a>
                            </div>
                            <small class="form-text text-muted">Upload a CSV with an "email" column or use the first column for emails. Already existing participants (members or pending invitations) for each community are skipped.</small>
                        </div>
                        <div class="form-group">
                            <label>Send invitations to</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeThis" value="this" checked>
                                <label class="form-check-label" for="scopeThis">This community only ({{ $community->community_name }})</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeAll" value="all">
                                <label class="form-check-label" for="scopeAll">All communities</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="scope" id="scopeSelected" value="selected">
                                <label class="form-check-label" for="scopeSelected">Selected communities</label>
                            </div>
                        </div>
                        <div class="form-group ml-4" id="selectedCommunitiesWrap" style="display:none;">
                            <label class="small text-muted">Select communities</label>
                            <select name="community_ids[]" id="selectedCommunities" class="form-control" multiple size="8">
                                @foreach($allCommunities ?? [] as $c)
                                    <option value="{{ $c->id }}" {{ $c->id == $community->id ? 'selected' : '' }}>{{ $c->community_name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple.</small>
                        </div>
                        <input type="hidden" name="community_id" value="{{ $community->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="importCsvSubmitBtn">
                            <i class="fa fa-upload mr-1"></i>Import and Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add member</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="addMemberForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>User email</label>
                            <input type="email" class="form-control" name="user_email" required placeholder="user@example.com">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="addMemberIsAdmin" name="is_admin" value="1">
                            <label class="form-check-label" for="addMemberIsAdmin">Make community admin</label>
                        </div>
                        <input type="hidden" name="community_id" value="{{ $community->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addMemberSubmitBtn">Add member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script>
        (function() {
            var communityId = {{ $community->id }};
            var allPendingIds = [{{ $membership->where('is_approved', 0)->pluck('id')->join(',') }}];
            var bulkSelectAll = false;
            var memberId;
            var action;

            function showModal(id, actionType) {
                memberId = id;
                action = actionType;
                jQuery('#actionType').text(actionType);
                if (actionType === 'delete') {
                    jQuery('#approvalModalText').html('Permanently remove this rejected request? This cannot be undone.');
                } else {
                    jQuery('#approvalModalText').html('Are you sure you want to <span id="actionType">' + actionType + '</span> this member?');
                }
                jQuery('#confirmAction').off('click').on('click', confirmSingleAction);
                jQuery('#approvalModal').modal('show');
            }

            function confirmSingleAction() {
                var url = action === 'delete' ? '{{ route('admin.commsofpractice.deleteMember') }}' : '{{ route('admin.commsofpractice.memberAction') }}';
                var data = {
                    _token: '{{ csrf_token() }}',
                    member_id: memberId,
                    community_id: communityId
                };
                if (action !== 'delete') data.action = action;
                jQuery.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        jQuery('#approvalModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.');
                    }
                });
            }

            function getSelectedMemberIds() {
                if (bulkSelectAll) return allPendingIds.slice();
                var ids = [];
                jQuery('.member-pending-cb:checked').each(function() {
                    ids.push(parseInt(jQuery(this).val(), 10));
                });
                return ids;
            }

            function updateBulkButtons() {
                var n = getSelectedMemberIds().length;
                jQuery('#bulk-approve-btn, #bulk-reject-btn').prop('disabled', n === 0);
            }

            function doBulkAction(actionType) {
                var ids = getSelectedMemberIds();
                if (ids.length === 0) {
                    alert('Please select at least one pending member.');
                    return;
                }
                var msg = 'Are you sure you want to ' + actionType + ' ' + ids.length + ' selected member(s)?';
                jQuery('#approvalModalText').text(msg);
                jQuery('#actionType').text(actionType);
                jQuery('#confirmAction').off('click').on('click', function() {
                    jQuery.ajax({
                        url: '{{ route('admin.commsofpractice.memberAction') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            member_ids: ids,
                            action: actionType,
                            community_id: communityId
                        },
                        success: function(response) {
                            jQuery('#approvalModal').modal('hide');
                            alert(response.message || 'Done.');
                            location.reload();
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.');
                        }
                    });
                });
                jQuery('#approvalModal').modal('show');
            }

            jQuery(function() {
                // Confirm button for single member action
                jQuery('#confirmAction').on('click', confirmSingleAction);

                // Inline Approve/Reject/Remove/Delete buttons
                jQuery(document).on('click', '.js-member-action', function(e) {
                    e.preventDefault();
                    var id = jQuery(this).data('member-id');
                    var actionType = jQuery(this).data('action');
                    if (id && actionType) showModal(id, actionType);
                });

                // Row checkbox: update bulk buttons state
                jQuery(document).on('change', '.member-pending-cb', function() {
                    bulkSelectAll = false;
                    updateBulkButtons();
                });

                // Select-all checkbox: check/uncheck all row checkboxes and update bulk buttons
                jQuery(document).on('change', '#select-all-pending', function() {
                    bulkSelectAll = this.checked;
                    jQuery('#members-table .member-pending-cb').each(function() {
                        this.checked = bulkSelectAll;
                    });
                    updateBulkButtons();
                });

                // Bulk Approve / Reject buttons
                jQuery(document).on('click', '#bulk-approve-btn', function(e) { e.preventDefault(); doBulkAction('approve'); });
                jQuery(document).on('click', '#bulk-reject-btn', function(e) { e.preventDefault(); doBulkAction('reject'); });
            });

            // Resend invitation (delegated; use data attributes for URL so it works from any context)
            jQuery(document).on('click', '.js-resend-invitation', function(e) {
                e.preventDefault();
                var btn = jQuery(this);
                var invitationId = btn.data('invitation-id');
                var commId = btn.data('community-id') || communityId;
                var url = btn.data('resend-url') || '{{ route('admin.commsofpractice.resendInvitation') }}';
                if (!invitationId) { alert('Invalid invitation.'); return; }
                if (!confirm('Resend this invitation? A new link will be sent and the previous link will no longer work.')) return;
                btn.prop('disabled', true);
                jQuery.ajax({
                    url: url,
                    method: 'POST',
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    data: { _token: '{{ csrf_token() }}', invitation_id: invitationId, community_id: commId },
                    success: function(response) { alert(response.message || 'Invitation resent.'); location.reload(); },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.status === 419 ? 'Session expired. Please refresh and try again.' : 'Failed to resend.');
                        alert(msg);
                        btn.prop('disabled', false);
                    }
                });
            });

            // Delete invitation
            jQuery(document).on('click', '.js-delete-invitation', function(e) {
                e.preventDefault();
                var btn = jQuery(this);
                var invitationId = btn.data('invitation-id');
                var commId = btn.data('community-id') || communityId;
                var url = btn.data('delete-url') || '{{ route('admin.commsofpractice.deleteInvitation') }}';
                if (!invitationId) { alert('Invalid invitation.'); return; }
                if (!confirm('Delete this invitation? This cannot be undone.')) return;
                btn.prop('disabled', true);
                jQuery.ajax({
                    url: url,
                    method: 'POST',
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    data: { _token: '{{ csrf_token() }}', invitation_id: invitationId, community_id: commId },
                    success: function(response) { alert(response.message || 'Invitation deleted.'); location.reload(); },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr.status === 419 ? 'Session expired. Please refresh and try again.' : 'Failed to delete.');
                        alert(msg);
                        btn.prop('disabled', false);
                    }
                });
            });
        })();

        jQuery(function() {
            // Initialize DataTable for invitations table only (members table left as plain HTML for checkboxes/buttons)
            if (jQuery('#invitations-table').length) {
                jQuery('#invitations-table').DataTable({
                    pageLength: 15,
                    lengthMenu: [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
                    order: [[3, 'desc']],
                    columnDefs: [{ orderable: false, targets: 7 }],
                    language: { search: "", searchPlaceholder: "Search invitations by email...", lengthMenu: "Show _MENU_ invitations per page", info: "Showing _START_ to _END_ of _TOTAL_ invitations", infoEmpty: "No invitations available", infoFiltered: "(filtered from _MAX_ total)", zeroRecords: "No matching invitations found" },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
                });
                jQuery('.dataTables_filter input').addClass('form-control').css({ width: '300px', display: 'inline-block', marginLeft: '10px' });
                jQuery('.dataTables_filter').prepend('<i class="fa fa-search" style="margin-right: 5px; color: #6c757d;"></i>');
            }

            // Pre-fill invitation form and auto-open modal when URL has email
            var params = new URLSearchParams(window.location.search);
            var emailParam = params.get('email');
            var communityIdParam = params.get('community_id');
            if (emailParam && emailParam.trim() !== '') {
                jQuery('#invitationEmail').val(emailParam.trim().replace(/%2C/gi, ', '));
                if (communityIdParam) {
                    jQuery('#sendInvitationForm').find('input[name="community_id"]').val(communityIdParam);
                }
                jQuery('#sendInvitationModal').modal('show');
            }

            // Handle send invitation form (AJAX)
            jQuery('#sendInvitationForm').on('submit', function(e) {
                e.preventDefault();
                var $form = jQuery(this);
                var $btn = $form.find('#sendInvitationSubmitBtn');
                var communityId = $form.find('input[name="community_id"]').val();
                var emailVal = jQuery('#invitationEmail').val().trim();
                if (!communityId || !emailVal) {
                    alert('Please enter at least one email address.');
                    return;
                }
                $btn.prop('disabled', true).text('Sending...');
                var formData = new FormData($form[0]);
                formData.set('email', emailVal);
                jQuery.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $form.find('input[name="_token"]').val(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        jQuery('#sendInvitationModal').modal('hide');
                        $form[0].reset();
                        $form.find('input[name="community_id"]').val('{{ $community->id }}');
                        alert(response.message || 'Invitation(s) sent.');
                        location.reload();
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : (xhr.status === 419 ? 'Session expired. Please refresh the page.' : 'An error occurred.');
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join(' ');
                        alert(message);
                    },
                    complete: function() { $btn.prop('disabled', false).text('Send Invitation(s)'); }
                });
            });

            jQuery('#addMemberForm').on('submit', function(e) {
                e.preventDefault();
                var $form = jQuery(this);
                var $btn = jQuery('#addMemberSubmitBtn');
                $btn.prop('disabled', true).text('Adding...');
                jQuery.ajax({
                    url: '{{ route('admin.commsofpractice.addMember') }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    data: $form.serialize(),
                    success: function(response) {
                        alert(response.message || 'Member added.');
                        location.reload();
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to add member.';
                        alert(message);
                    },
                    complete: function() { $btn.prop('disabled', false).text('Add member'); }
                });
            });

            jQuery('#downloadCsvTemplate').on('click', function(e) {
                e.preventDefault();
                var blob = new Blob(['email\nexample@email.com'], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'invitation_emails_template.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            });

            jQuery('input[name="scope"]').on('change', function() {
                jQuery('#selectedCommunitiesWrap').toggle(jQuery(this).val() === 'selected');
            });

            jQuery('#importCsvForm').on('submit', function(e) {
                e.preventDefault();
                var fd = new FormData(this);
                fd.append('_token', '{{ csrf_token() }}');
                fd.append('scope', jQuery('input[name="scope"]:checked').val());
                fd.append('community_id', '{{ $community->id }}');
                if (jQuery('input[name="scope"]:checked').val() === 'selected') {
                    jQuery('#selectedCommunities option:selected').each(function() { fd.append('community_ids[]', jQuery(this).val()); });
                }
                var $btn = jQuery('#importCsvSubmitBtn');
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Sending...');
                jQuery.ajax({
                    url: '{{ route('admin.commsofpractice.bulkInvite') }}',
                    method: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        jQuery('#importCsvModal').modal('hide');
                        jQuery('#importCsvForm')[0].reset();
                        jQuery('#selectedCommunitiesWrap').hide();
                        alert(response.message || 'Done.');
                        location.reload();
                    },
                    error: function(xhr) { alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Import failed.'); },
                    complete: function() { $btn.prop('disabled', false).html('<i class="fa fa-upload mr-1"></i>Import and Send'); }
                });
            });
        });
    </script>
@endsection
