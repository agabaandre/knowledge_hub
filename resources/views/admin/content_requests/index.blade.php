@extends(admin_layout())

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-3">
                        <i class="fa fa-file-alt mr-2"></i>Content Requests
                    </h3>
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.content-requests.index') }}" class="mb-0">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="status" class="form-label small font-weight-bold">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
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
                                <button type="submit" class="btn btn-primary btn-sm mr-1">
                                    <i class="fa fa-filter mr-1"></i>Filter
                                </button>
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Subject</th>
                                    <th width="25%">Description</th>
                                    <th width="10%">Country</th>
                                    <th width="15%">Email</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Date</th>
                                    <th width="15%">Actions</th>
            </tr>
        </thead>
        <tbody>
                                @forelse($contentRequests as $index => $request)
                                    <tr>
                                        <td>{{ $contentRequests->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $request->subject }}</strong>
                                        </td>
                                        <td>
                                            <div style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                                {{ Str::limit(strip_tags($request->description), 100) }}
                                            </div>
                                        </td>
                                        <td>{{ $request->country->name ?? 'N/A' }}</td>
                                        <td>{{ $request->email ?? 'N/A' }}</td>
                                        <td>
                                            @if($request->isProcessed())
                                                <span class="badge badge-success">
                                                    <i class="fa fa-check-circle mr-1"></i>Processed
                                                </span>
                                                @if($request->processedBy)
                                                    <br><small class="text-muted">By: {{ $request->processedBy->name }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fa fa-clock mr-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $request->created_at->format('M d, Y') }}</small>
                                            @if($request->processed_at)
                                                <br><small class="text-muted">Processed: {{ $request->processed_at->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if(!$request->isProcessed())
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm process-request-btn" 
                                                            data-id="{{ $request->id }}"
                                                            data-subject="{{ $request->subject }}"
                                                            data-description="{{ strip_tags($request->description) }}"
                                                            title="Process Request">
                                                        <i class="fa fa-check mr-1"></i>Process
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-info btn-sm view-processed-btn" 
                                                            data-id="{{ $request->id }}"
                                                            data-subject="{{ $request->subject }}"
                                                            data-links="{{ $request->content_links }}"
                                                            data-comments="{{ $request->admin_comments }}"
                                                            title="View Processed Details">
                                                        <i class="fa fa-eye mr-1"></i>View
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.content-requests.edit', $request->id) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.content-requests.destroy', $request->id) }}" 
                                                      method="POST" 
                                                      style="display:inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this content request?');">
                            @csrf
                            @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                        </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No content requests found.</p>
                                            </div>
                    </td>
                </tr>
                                @endforelse
        </tbody>
    </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
    {{ $contentRequests->links() }}
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
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 0.25rem;">
                    <i class="fa fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle Process button click
    $('.process-request-btn').on('click', function() {
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
    $('.view-processed-btn').on('click', function() {
        var subject = $(this).data('subject');
        var links = $(this).data('links') || 'No links provided';
        var comments = $(this).data('comments') || 'No comments provided';
        
        $('#viewSubject').text(subject);
        $('#viewLinks').text(links);
        $('#viewComments').text(comments);
        
        $('#viewProcessedModal').modal('show');
    });
});
</script>
@endsection 
