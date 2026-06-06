@extends(admin_layout())

@section('styles')
    @include('common.table')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-3">
                        <i class="fa fa-file-alt mr-2"></i>Content Requests
                    </h3>
                    
                    <!-- Filters (auto-apply) -->
                    <form method="GET" action="{{ route('admin.content-requests.index') }}" id="contentRequestFiltersForm" class="mb-0">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="status" class="form-label small font-weight-bold">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                                    <option value="referred" {{ request('status') == 'referred' ? 'selected' : '' }}>Referred</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="country_id" class="form-label small font-weight-bold">Country</label>
                                <select name="country_id" id="country_id" class="form-control form-control-sm">
                                    <option value="">All Countries</option>
                                    @foreach(\App\Models\Country::orderBy('name')->get() as $country)
                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="search" class="form-label small font-weight-bold">Search</label>
                                <input type="text" 
                                       name="search" 
                                       id="search" 
                                       class="form-control form-control-sm" 
                                       placeholder="Subject, email, description..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label small font-weight-bold">Date From</label>
                                <input type="text" 
                                       name="date_from" 
                                       id="date_from" 
                                       class="form-control form-control-sm datepicker" 
                                       placeholder="Select date"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label small font-weight-bold">Date To</label>
                                <input type="text" 
                                       name="date_to" 
                                       id="date_to" 
                                       class="form-control form-control-sm datepicker" 
                                       placeholder="Select date"
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('admin.content-requests.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-times mr-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="contentRequestsTable" class="table table-bordered table-striped table-hover w-100">
        <thead>
            <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Subject</th>
                                    <th width="25%">Description</th>
                                    <th width="10%">Country</th>
                                    <th width="15%">Email</th>
                                    <th width="12%">Status</th>
                                    <th width="10%">Date</th>
                                    <th width="18%">Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Request Modal -->
<div class="modal fade" id="processRequestModal" tabindex="-1" role="dialog" aria-labelledby="processRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 0.25rem;">
            <div class="modal-header" style="background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%); color: white; border-radius: 0.25rem 0.25rem 0 0;">
                <h5 class="modal-title" id="processRequestModalLabel">
                    <i class="fa fa-check-circle mr-2"></i>Process Content Request
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="processRequestForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info" style="border-radius: 0.25rem;">
                        <i class="fa fa-info-circle mr-2"></i>
                        <strong>Request Details:</strong>
                        <div id="requestDetails" class="mt-2"></div>
                    </div>

                    <div class="form-group">
                        <label for="content_links" class="form-label">
                            <strong>Content Links <span class="text-danger">*</span></strong>
                        </label>
                        <textarea class="form-control" 
                                  id="content_links" 
                                  name="content_links" 
                                  rows="6" 
                                  placeholder="Enter the links to the content/resources that fulfill this request. You can add multiple links, one per line or separated by commas."
                                  required
                                  style="border-radius: 0.25rem;"></textarea>
                        <small class="form-text text-muted">
                            <i class="fa fa-lightbulb mr-1"></i>
                            Provide links to publications, resources, or content that address this request. Each link should be on a new line or separated by commas.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="admin_comments" class="form-label">
                            <strong>Admin Comments (Optional)</strong>
                        </label>
                        <textarea class="form-control" 
                                  id="admin_comments" 
                                  name="admin_comments" 
                                  rows="4" 
                                  placeholder="Add any additional comments or notes for the requester..."
                                  style="border-radius: 0.25rem;"></textarea>
                        <small class="form-text text-muted">
                            <i class="fa fa-info-circle mr-1"></i>
                            Optional: Add any additional information or context for the requester.
                        </small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 0.25rem;">
                        <i class="fa fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" style="border-radius: 0.25rem;">
                        <i class="fa fa-check mr-1"></i>Process & Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Processed Details Modal -->
<div class="modal fade" id="viewProcessedModal" tabindex="-1" role="dialog" aria-labelledby="viewProcessedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 0.25rem;">
            <div class="modal-header" style="background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%); color: white; border-radius: 0.25rem 0.25rem 0 0;">
                <h5 class="modal-title" id="viewProcessedModalLabel">
                    <i class="fa fa-eye mr-2"></i>Processed Request Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>Subject:</strong></label>
                    <p id="viewSubject" class="mb-2"></p>
                </div>
                <div class="form-group">
                    <label><strong>Content Links:</strong></label>
                    <div id="viewLinks" class="border p-3 bg-light" style="border-radius: 0.25rem; white-space: pre-wrap;"></div>
                </div>
                <div class="form-group">
                    <label><strong>Admin Comments:</strong></label>
                    <div id="viewComments" class="border p-3 bg-light" style="border-radius: 0.25rem; white-space: pre-wrap;"></div>
                </div>
                <div class="form-group">
                    <label><strong>Process method:</strong></label>
                    <div id="viewProcessMethod" class="border p-3 bg-light" style="border-radius: 0.25rem; white-space: pre-wrap;"></div>
                </div>
                <div class="form-group">
                    <label><strong>Processed by:</strong></label>
                    <div id="viewProcessedBy" class="border p-3 bg-light" style="border-radius: 0.25rem; white-space: pre-wrap;"></div>
                </div>
                <div class="form-group">
                    <label><strong>Processed at:</strong></label>
                    <div id="viewProcessedAt" class="border p-3 bg-light" style="border-radius: 0.25rem; white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 0.25rem;">
                    <i class="fa fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@can('manage_content_requests')
<!-- Refer to hub user or community -->
<div class="modal fade" id="referRequestModal" tabindex="-1" role="dialog" aria-labelledby="referRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 0.25rem;">
            <div class="modal-header" style="background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%); color: white;">
                <h5 class="modal-title" id="referRequestModalLabel">
                    <i class="fa fa-share mr-2"></i>Refer content request
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="referRequestForm" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="referRequestSubjectSummary"></p>
                    <p class="small">The requester receives a private link. Choose <strong>at least two</strong> assignees in total (any mix of hub users and communities). Each community gets its own forum thread; users use the hub discussion.</p>

                    <div class="form-group">
                        <label for="referred_user_ids" class="font-weight-bold">Hub users <span class="text-muted font-weight-normal">(multi-select)</span></label>
                        <select class="form-control no-select2" name="referred_user_ids[]" id="referred_user_ids" multiple
                                data-placeholder="Search and select hub users…">
                            @foreach($referUsers ?? [] as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} &lt;{{ $u->email }}&gt;</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="referred_community_ids" class="font-weight-bold">Communities of practice <span class="text-muted font-weight-normal">(multi-select)</span></label>
                        <select class="form-control no-select2" name="referred_community_ids[]" id="referred_community_ids" multiple
                                data-placeholder="Search and select communities…">
                            @foreach($referCommunities ?? [] as $c)
                                <option value="{{ $c->id }}">{{ $c->community_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="referral_notes">Instructions / context (optional)</label>
                        <textarea class="form-control" name="referral_notes" id="referral_notes" rows="4" placeholder="What should the assignee or community focus on?"></textarea>
                        <small class="form-text text-muted">Shown in the first thread message and in assignee emails.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane mr-1"></i>Refer &amp; notify
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('scripts')
@include('admin.publications.partials.datatable_assets')
<script>
$(document).ready(function() {
    let filterReloadTimer = null;
    let contentRequestsTable = $('#contentRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 10,
        order: [[6, 'desc']],
        ajax: {
            url: '{{ route('admin.content-requests.index') }}',
            data: function (d) {
                d.datatable = 1;
                d.status = $('#status').val() || '';
                d.country_id = $('#country_id').val() || '';
                d.search = $('#search').val() || '';
                d.date_from = $('#date_from').val() || '';
                d.date_to = $('#date_to').val() || '';
                return d;
            }
        },
        columns: [
            { data: 'index', orderable: false },
            { data: 'subject' },
            { data: 'description', orderable: false },
            { data: 'country', orderable: false },
            { data: 'email' },
            { data: 'status', orderable: false },
            { data: 'date' },
            { data: 'actions', orderable: false }
        ]
    });

    function scheduleReload() {
        clearTimeout(filterReloadTimer);
        filterReloadTimer = setTimeout(function () { contentRequestsTable.ajax.reload(); }, 350);
    }

    $('#contentRequestFiltersForm').on('change', 'select', scheduleReload);
    $('#search').on('input', scheduleReload);
    $('#date_from, #date_to').on('change', scheduleReload);

    // Handle Process button click
    $(document).on('click', '.process-request-btn', function() {
        var requestId = $(this).data('id');
        var subject = $(this).data('subject');
        var description = $(this).data('description');
        
        // Set form action
        $('#processRequestForm').attr('action', '{{ url("admin/content-requests") }}/' + requestId + '/process');
        
        // Set request details
        $('#requestDetails').html(
            '<strong>Subject:</strong> ' + subject + '<br>' +
            '<strong>Description:</strong> ' + description
        );
        
        // Clear form fields
        $('#content_links').val('');
        $('#admin_comments').val('');
        
        // Show modal
        $('#processRequestModal').modal('show');
    });

    // Handle View Processed button click
    $(document).on('click', '.view-processed-btn', function() {
        var subject = $(this).data('subject');
        var links = $(this).data('links') || 'No links provided';
        var comments = $(this).data('comments') || 'No comments provided';
        var processMethod = $(this).data('process-method') || 'Processed by admin';
        var processedBy = $(this).data('processed-by') || 'Unknown';
        var processedAt = $(this).data('processed-at') || 'Unknown';
        
        $('#viewSubject').text(subject);
        $('#viewLinks').text(links);
        $('#viewComments').text(comments);
        $('#viewProcessMethod').text(processMethod);
        $('#viewProcessedBy').text(processedBy);
        $('#viewProcessedAt').text(processedAt);
        
        $('#viewProcessedModal').modal('show');
    });

    function referSelect2Destroy($el) {
        if ($el.length && $el.hasClass('select2-hidden-accessible') && typeof $.fn.select2 === 'function') {
            $el.select2('destroy');
        }
    }

    function initReferModalSelect2() {
        if (typeof $.fn.select2 !== 'function') {
            return;
        }
        var $modal = $('#referRequestModal');
        var $user = $('#referred_user_ids');
        var $community = $('#referred_community_ids');
        referSelect2Destroy($user);
        referSelect2Destroy($community);
        $user.select2({
            width: '100%',
            dir: 'ltr',
            dropdownParent: $modal,
            minimumResultsForSearch: 0,
            placeholder: $user.data('placeholder') || 'Select hub users…',
            allowClear: true,
            closeOnSelect: false
        });
        $community.select2({
            width: '100%',
            dir: 'ltr',
            dropdownParent: $modal,
            minimumResultsForSearch: 0,
            placeholder: $community.data('placeholder') || 'Select communities…',
            allowClear: true,
            closeOnSelect: false
        });
    }

    $('#referRequestModal').on('shown.bs.modal', function () {
        initReferModalSelect2();
    });

    $('#referRequestModal').on('hidden.bs.modal', function () {
        referSelect2Destroy($('#referred_user_ids'));
        referSelect2Destroy($('#referred_community_ids'));
    });

    $('#referRequestForm').on('submit', function (e) {
        var u = $('#referred_user_ids').val() || [];
        var c = $('#referred_community_ids').val() || [];
        if (!Array.isArray(u)) { u = u ? [u] : []; }
        if (!Array.isArray(c)) { c = c ? [c] : []; }
        if (u.length + c.length < 2) {
            e.preventDefault();
            alert('Select at least two assignees in total (hub users and/or communities).');
            return false;
        }
    });

    $(document).on('click', '.refer-request-btn', function() {
        var requestId = $(this).data('id');
        var subject = $(this).data('subject');
        $('#referRequestForm').attr('action', {!! json_encode(url('admin/content-requests')) !!} + '/' + requestId + '/refer');
        $('#referRequestSubjectSummary').html('<strong>Subject:</strong> ' + $('<div/>').text(subject).html());
        $('#referral_notes').val('');
        $('#referred_user_ids').val(null).trigger('change');
        $('#referred_community_ids').val(null).trigger('change');
        $('#referRequestModal').modal('show');
    });

    $(document).on('click', '.copy-track-btn', function() {
        var url = $(this).data('url');
        if (!url) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Requester tracking link copied to clipboard.');
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = url;
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); alert('Link copied.'); } catch (e) { prompt('Copy this link:', url); }
            document.body.removeChild(ta);
        }
    });
});
</script>
@endsection 
