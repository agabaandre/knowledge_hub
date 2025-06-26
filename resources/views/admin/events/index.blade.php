@extends('admin.layouts.tabular')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
            <h1>Events</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createEventModal">Create Event</button>
        </div>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Venue</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->venue }}</td>
                        <td>{{ $event->startdate }}</td>
                        <td>{{ $event->enddate }}</td>
                        <td>
                            <button class="btn btn-info" onclick="viewEvent({{ $event->id }})">View</button>
                            <button class="btn btn-warning" onclick="editEvent({{ $event->id }})">Edit</button>
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Create Event Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="createEventForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createEventModalLabel">Create Event</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="venue">Venue</label>
                                    <input type="text" class="form-control" id="venue" name="venue" required>
                                </div>
                                <div class="form-group">
                                    <label for="startdate">Start Date</label>
                                    <input type="datetime-local" class="form-control" id="startdate" name="startdate"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="enddate">End Date</label>
                                    <input type="datetime-local" class="form-control" id="enddate" name="enddate"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="organized_by">Organized By</label>
                                    <input type="text" class="form-control" id="organized_by" name="organized_by"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" id="status" name="status" required>
                                </div>
                                <div class="form-group">
                                    <label for="is_online">Is Online</label>
                                    <select class="form-control" id="is_online" name="is_online" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="contact_person">Contact Person</label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editEventForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_event_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_title">Title</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_venue">Venue</label>
                                    <input type="text" class="form-control" id="edit_venue" name="venue" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_startdate">Start Date</label>
                                    <input type="datetime-local" class="form-control" id="edit_startdate"
                                        name="startdate" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_enddate">End Date</label>
                                    <input type="datetime-local" class="form-control" id="edit_enddate" name="enddate"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_organized_by">Organized By</label>
                                    <input type="text" class="form-control" id="edit_organized_by"
                                        name="organized_by" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_status">Status</label>
                                    <input type="text" class="form-control" id="edit_status" name="status" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_is_online">Is Online</label>
                                    <select class="form-control" id="edit_is_online" name="is_online" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_contact_person">Contact Person</label>
                                    <input type="text" class="form-control" id="edit_contact_person"
                                        name="contact_person" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Handle create event form submission
        $('#createEventForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '/admin/events/save',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#createEventModal').modal('hide');
                    location.reload();
                },
                error: function(response) {
                    alert('Error creating event');
                }
            });
        });

        // Handle edit event form submission
        $('#editEventForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_event_id').val();
            $.ajax({
                url: '/admin/events/' + id + '/save',
                method: 'PUT',
                data: $(this).serialize(),
                success: function(response) {
                    $('#editEventModal').modal('hide');
                    location.reload();
                },
                error: function(response) {
                    alert('Error updating event');
                }
            });
        });

        // Load event data into the edit modal
        function editEvent(id) {
            $.get('/admin/events/' + id, function(event) {
                $('#edit_event_id').val(event.id);
                $('#edit_title').val(event.title);
                $('#edit_venue').val(event.venue);
                $('#edit_startdate').val(event.startdate.replace(' ', 'T'));
                $('#edit_enddate').val(event.enddate.replace(' ', 'T'));
                $('#edit_organized_by').val(event.organized_by);
                $('#edit_status').val(event.status);
                $('#edit_is_online').val(event.is_online ? '1' : '0');
                $('#edit_contact_person').val(event.contact_person);
                $('#editEventModal').modal('show');
            });
        }
    </script>
@endsection
