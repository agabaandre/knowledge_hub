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
            <div class="card-header text-left">
                <h3 class="card-title">List of Communities</h3>
                <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addCommunityModal"
                    id="addCommunityButton">Add Community</button>
            </div>
            <div class="card-body text-left">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Community Name</th>
                            <th>Description</th>
                            <th>Creator</th>
                            <th>Is Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($communities as $community)
                            <tr>
                                <td>{{ $community->id }}</td>
                                <td>{{ $community->community_name }}</td>
                                <td>{{ $community->description }}</td>
                                <td>{{ $community->creator->name ?? 'N/A' }}</td>
                                <td>{{ $community->is_active ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a href="{{ route('admin.commsofpractice.details', $community->id) }}"
                                        class="btn btn-info btn-sm">View Members</a>
                                    <button class="btn btn-warning btn-sm editCommunityButton"
                                        data-id="{{ $community->id }}" data-name="{{ $community->community_name }}"
                                        data-description="{{ $community->description }}"
                                        data-active="{{ $community->is_active }}">Edit</button>
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
                            <input type="text" class="form-control" id="community_name" name="community_name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="is_active">Is Active</label>
                            <select class="form-control" id="is_active" name="is_active">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                            alert(response.message);
                            location.reload(); // Reload the page to see the changes
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
