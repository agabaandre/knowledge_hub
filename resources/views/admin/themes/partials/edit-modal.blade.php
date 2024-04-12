<div class="modal" id="edit-theme-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Edit Thematic Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editThemeForm" action="{{ url('/admin/themes/save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields for editing sub-thematic area -->
                    <input type="hidden" name="id" id="theme_id">

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="theme_description" name="description" required>
                    </div>


                </div>
                <div class="modal-footer">
                    <!-- Button to submit the form -->
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('modal-scripts')
    <script>
        // JavaScript for specific modal
        $('#edit-theme-modal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget);

            var id = button.data('id'); // assuming you have data-id attribute on the trigger button

            // Assuming you want to populate the form fields with existing data
            var description = button.data('description');

            console.log({
                id,
                description
            })

            // Populate the form fields with the retrieved data
            $('#theme_id').val(id);
            $('#theme_description').val(description);
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#edit-theme-modal').on('hidden.bs.modal', function() {
            $('#theme_id').val('');
            $('#theme_description').val('');
        });
    </script>
@endpush
