@extends('admin.layouts.tabular')

@section('styles')
    @include('common.table')
@endsection

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">Communities of Practice</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
                <li class="breadcrumb-item active" aria-current="page">Communities of Practice</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Communities of Practice</h3>
                    <small class="text-muted">Create, edit and manage communities and their membership</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#addCommunityModal" id="addCommunityButton">
                        <i class="fa fa-plus mr-1"></i> Add Community
                    </button>
                </div>
            </div>
            <div class="card-body text-left">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                            <input type="text" id="filter_name" class="form-control" placeholder="Filter by name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filter_active" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <table class="table table-striped table-hover table-bordered" id="communities_table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th>Community</th>
                            <th>Description</th>
                            <th style="width:180px;">Creator</th>
                            <th style="width:110px;">Status</th>
                            <th style="width:170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($communities as $community)
                            <tr data-active="{{ $community->is_active ? 1 : 0 }}">
                                <td><span class="text-muted">#{{ $community->id }}</span></td>
                                <td class="font-weight-600">{{ $community->community_name }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($community->description), 120) }}</td>
                                <td>{{ $community->creator->name ?? 'N/A' }}</td>
                                <td>
                                    @if($community->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.commsofpractice.details', $community->id) }}" class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fa fa-users"></i> Members
                                    </a>
                                    <button class="btn btn-sm btn-outline-dark editCommunityButton"
                                        title="Edit"
                                        data-id="{{ $community->id }}" data-name="{{ $community->community_name }}"
                                        data-description="{{ $community->description }}"
                                        data-active="{{ $community->is_active }}">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Community Modal -->
    <div class="modal fade" id="addCommunityModal" tabindex="-1" role="dialog" aria-labelledby="addCommunityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCommunityModalLabel">Add Community of Practice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addCommunityForm">
                        @csrf
                        <!-- Hidden input for community ID -->
                        <input type="hidden" id="community_id" name="id">
                        <div class="form-group">
                            <label for="community_name">Community Name</label>
                            <input type="text" class="form-control" id="community_name" name="community_name" placeholder="e.g., Digital Health" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control summernote-sm" id="description" name="description" rows="4" placeholder="Briefly describe the community's focus"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="is_active">Is Active</label>
                            <select class="form-control" id="is_active" name="is_active">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark ml-2"><i class="fa fa-save mr-1"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
@include('partials.general.summernote')
<script>
$(function(){
    function initSN(){
        var $el = $('#description');
        if ($el.length && !$el.data('summernote')) {
            try { $el.summernote({ height: 150 }); } catch(e) {}
        }
    }
    initSN();
    $(document).on('shown.bs.modal', '#addCommunityModal', function(){ initSN(); });
});
</script>
@endsection
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Reset form when opening the modal for adding a new community
            $('#addCommunityButton').on('click', function() {
                $('#addCommunityModalLabel').text('Add Community of Practice');
                $('#addCommunityForm')[0].reset();
                $('#community_id').val(''); // Clear the ID field for new entries
            });

            // Populate the form when editing a community
            $('.editCommunityButton').on('click', function() {
                $('#addCommunityModalLabel').text('Edit Community of Practice');
                $('#community_id').val($(this).data('id')); // Set the ID for editing
                $('#community_name').val($(this).data('name'));
                $('#description').val($(this).data('description'));
                $('#is_active').val($(this).data('active'));
                $('#addCommunityModal').modal('show');
            });

            // Handle form submission
            $('#addCommunityForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ url('/admin/commsofpractice/save') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            location.reload();
                        } else {
                            alert(response.message || 'Operation failed');
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            // Simple client-side filters
            $('#filter_name, #filter_active').on('input change', function(){
                var name = ($('#filter_name').val() || '').toLowerCase();
                var status = $('#filter_active').val();
                $('#communities_table tbody tr').each(function(){
                    var row = $(this);
                    var matchesName = !name || row.find('td:nth-child(2)').text().toLowerCase().indexOf(name) > -1;
                    var matchesStatus = !status || row.data('active').toString() === status.toString();
                    row.toggle(matchesName && matchesStatus);
                });
            });
        });
    </script>
@endsection
