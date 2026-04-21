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
                    <div class="form-group">
                        <label for="theme_display_order">Display Order</label>
                        <input type="number" min="0" step="1" class="form-control" id="theme_display_order" name="display_order" required>
                        <small class="text-muted">Lower numbers appear first in theme selection lists.</small>
                    </div>
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <select class="form-control select2-fa-icons" id="theme_icon" name="icon" required>
                            <option value="">Select icon class</option>
                            @foreach(($faIconOptions ?? []) as $iconClass)
                                <option value="{{ $iconClass }}">{{ $iconClass }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            Using Font Awesome {{ $faVersion ?? '5.3.1' }}.
                            <a href="{{ $faCheatsheetUrl ?? 'https://fontawesome.com/v5.3.1/icons?d=gallery&m=free' }}" target="_blank" rel="noopener noreferrer">Open cheatsheet</a>
                        </small>
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
            var icon = button.data('icon');
            var display_order = button.data('display_order');

            console.log({
                id,
                description,
                icon,
                display_order
            })

            // Populate the form fields with the retrieved data
            $('#theme_id').val(id);
            $('#theme_description').val(description);
            $('#theme_display_order').val(display_order ?? 0);
            var iconSelect = $('#theme_icon');
            if (icon && iconSelect.find('option[value="' + icon + '"]').length === 0) {
                iconSelect.append(new Option(icon, icon, false, false));
            }
            iconSelect.val(icon).trigger('change');

            $('#edit-theme-modal .select2-fa-icons').select2({
                placeholder: 'Select icon class',
                width: '100%',
                dir: "ltr",
            });
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#edit-theme-modal').on('hidden.bs.modal', function() {
            $('#theme_id').val('');
            $('#theme_description').val('');
            $('#theme_display_order').val('0');
            $('#theme_icon').val('').trigger('change');
        });
    </script>
@endpush
