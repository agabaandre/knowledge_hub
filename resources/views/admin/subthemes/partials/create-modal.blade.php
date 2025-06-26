<div class="modal" id="create-subtheme-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Create New Sub Theme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createThemeForm" action="{{ url('/admin/subthemes/save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields for creating a new sub-thematic area -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="new_subtheme_description" name="description" required>
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="text" class="form-control" id="new_subtheme_icon" name="icon" required>
                    </div>

                    <div class="form-group">
                        <label for="thematic_area_id">Thematic Area</label>
                        <select class="form-control select2" id="new_thematic_area_id" name="thematic_area_id">
                            @foreach ($themes as $theme)
                                <option value="{{ $theme['id'] }}">{{ $theme['description'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Button to submit the form -->
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modal-scripts')
    <script>
        // JavaScript for specific modal
        $('#create-subtheme-modal').on('show.bs.modal', function(event) {
            // Initialize Select2 for thematic area select field
            $('.select2').select2({
                placeholder: 'Select a thematic area',
                width: '100%',
                dir: "ltr",
            });
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#create-subtheme-modal').on('hidden.bs.modal', function() {
            $('#new_subtheme_description').val('');
            $('#new_subtheme_icon').val('');
            $('#new_thematic_area_id').val('').trigger('change');
        });
    </script>
@endpush
