<div class="modal" id="edit-subtheme-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Edit Thematic Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editThemeForm" action="{{ url('/admin/subthemes/save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields for editing sub-thematic area -->
                    <input type="hidden" name="id" id="subtheme_id">

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="subtheme_description" name="description" required>
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="text" class="form-control" id="subtheme_icon" name="icon" required>
                    </div>

                    <div class="form-group">
                        <label for="thematic_area_id">Thematic Area</label>
                        <select class="form-control select2" id="thematic_area_id" name="thematic_area_id">
                            @foreach ($themes as $theme)
                                <option value="{{ $theme['id'] }}">{{ $theme['description'] }}</option>
                            @endforeach
                        </select>
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
        $('#edit-subtheme-modal').on('show.bs.modal', function(event) {
            // Get the button that triggered the modal
            var button = $(event.relatedTarget);

            // Extract data from the button
            var id = button.data('id');
            var description = button.data('description');
            var icon = button.data('icon');
            var thematic_area_id = button.data('thematic_area_id');

            console.log({
                id,
                description
            });

            // Populate the form fields with the retrieved data
            $('#subtheme_id').val(id);
            $('#subtheme_description').val(description);
            $('#subtheme_icon').val(icon);
            $('#thematic_area_id').val(thematic_area_id);

            // Initialize Select2 for thematic area select field
            $('.select2').select2({
                placeholder: 'Select a thematic area',
                width: '100%',
                dir: "ltr",
            });
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#edit-subtheme-modal').on('hidden.bs.modal', function() {
            $('#subtheme_id').val('');
            $('#subtheme_description').val('');
            $('#subtheme_icon').val('');
            $('#thematic_area_id').val('').trigger('change');
        });
    </script>
@endpush
