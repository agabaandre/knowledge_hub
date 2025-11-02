@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
    <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #119A48;
        }
        .stat-card .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Mailing List</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mailing List</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Subscribers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value" style="color: #22c55e;">{{ $stats['subscribed'] }}</div>
            <div class="stat-label">Active Subscribers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value" style="color: #3b82f6;">{{ $stats['users_count'] }}</div>
            <div class="stat-label">From User Accounts</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value" style="color: #f59e0b;">{{ $stats['subscribes_count'] }}</div>
            <div class="stat-label">Direct Subscribes</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Subscribers</h3>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSubscriberModal">
                            <i class="fa fa-plus"></i> Add Subscriber
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#sendEmailModal">
                            <i class="fa fa-envelope"></i> Send Email
                        </button>
                        <a href="{{ route('admin.mailing_list.export', request()->all()) }}" class="btn btn-info btn-sm">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('admin.mailing_list.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by email or name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="subscribed" {{ request('status') == 'subscribed' ? 'selected' : '' }}>Subscribed</option>
                                <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.mailing_list.index') }}" class="btn btn-secondary btn-block">Clear</a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Actions -->
                <form id="bulkActionForm" method="POST" action="{{ route('admin.mailing_list.bulkAction') }}" class="mb-3">
                    @csrf
                    <div class="d-flex align-items-center">
                        <select name="action" class="form-control mr-2" style="width: auto;" required>
                            <option value="">Bulk Actions</option>
                            <option value="subscribe">Subscribe Selected</option>
                            <option value="unsubscribed">Unsubscribe Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Apply</button>
                    </div>
                </form>

                <!-- Subscribers Table -->
                <div class="table-responsive">
                    <table id="subscribers-table" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>#</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Subscribed Date</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscribers as $subscriber)
                            <tr>
                                <td>
                                    @if($subscriber->type === 'subscribe')
                                        <input type="checkbox" name="ids[]" value="{{ $subscriber->original_id ?? $subscriber->id }}" class="subscriber-checkbox">
                                    @else
                                        <input type="checkbox" disabled class="subscriber-checkbox" title="User subscribers cannot be bulk edited">
                                    @endif
                                </td>
                                <td>{{ $loop->iteration + ($subscribers->currentPage() - 1) * $subscribers->perPage() }}</td>
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $subscriber->status == 'subscribed' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($subscriber->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $subscriber->type === 'user' ? 'info' : 'warning' }}">
                                        {{ $subscriber->type === 'user' ? 'User Account' : 'Direct Subscribe' }}
                                    </span>
                                </td>
                                <td>{{ $subscriber->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($subscriber->type === 'subscribe')
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editSubscriber('{{ $subscriber->original_id ?? $subscriber->id }}', '{{ $subscriber->email }}', '{{ $subscriber->name ?? '' }}', '{{ $subscriber->status }}')">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.mailing_list.destroy', $subscriber->original_id ?? $subscriber->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subscriber?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size: 0.875rem;">
                                            <i class="fa fa-info-circle" title="User account subscribers can be managed from their profile"></i>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $subscribers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Subscriber Modal -->
<div class="modal fade" id="addSubscriberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Subscriber</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.mailing_list.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subscriber</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subscriber Modal -->
<div class="modal fade" id="editSubscriberModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subscriber</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editSubscriberForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="subscribed">Subscribed</option>
                            <option value="unsubscribed">Unsubscribed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Subscriber</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email to Subscribers</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.mailing_list.sendEmail') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Recipients <span class="text-danger">*</span></label>
                        <select name="recipients" class="form-control" required>
                            <option value="subscribed">Subscribed Only ({{ $stats['subscribed'] }} subscribers - includes {{ $stats['users_count'] }} users + {{ $stats['subscribes_count'] - ($stats['unsubscribed'] ?? 0) }} direct)</option>
                            <option value="all">All Subscribers ({{ $stats['total'] }} total - includes {{ $stats['users_count'] }} users + {{ $stats['subscribes_count'] }} direct)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="email_message" class="form-control summernote" rows="10" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
@include('partials.general.summernote')
<script>
    $(function() {
        // Initialize DataTable
        $('#subscribers-table').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 7] }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search subscribers...",
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            paging: false, // Disable DataTables pagination since we're using Laravel pagination
            info: false
        });

        // Select all checkbox
        $('#selectAll').on('change', function() {
            $('.subscriber-checkbox').prop('checked', this.checked);
        });

        // Bulk action form submission
        $('#bulkActionForm').on('submit', function(e) {
            if (!$('input[name="ids[]"]:checked').length) {
                e.preventDefault();
                alert('Please select at least one subscriber.');
                return false;
            }
        });
    });

    function editSubscriber(id, email, name, status) {
        // Extract numeric ID if it's in format 'subscribe_X' or 'user_X', or just use the ID if it's already numeric
        var numericId = id;
        if (typeof id === 'string' && id.includes('_')) {
            numericId = id.split('_')[1];
        } else if (typeof id === 'string' && id.startsWith('subscribe_')) {
            numericId = id.replace('subscribe_', '');
        } else if (typeof id === 'string' && id.startsWith('user_')) {
            // User accounts can't be edited here, but handle it gracefully
            alert('User account subscribers cannot be edited here. Please manage them from the user profile.');
            return;
        }
        $('#editSubscriberForm').attr('action', '{{ url("admin/mailing_list") }}/' + numericId);
        $('#edit_email').val(email);
        $('#edit_name').val(name);
        $('#edit_status').val(status);
        $('#editSubscriberModal').modal('show');
    }
</script>
@endsection

