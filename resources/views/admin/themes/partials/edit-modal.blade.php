<div class="modal" id="edit-theme-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">{{ ui_t('admin_themes.edit_theme_title', 'Edit thematic area') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editThemeForm" action="{{ url('/admin/themes/save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields for editing sub-thematic area -->
                    <input type="hidden" name="id" id="theme_id">

                    <div class="form-group">
                        <label for="description">{{ ui_t('admin_themes.description', 'Description') }}</label>
                        <input type="text" class="form-control" id="theme_description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="theme_detailed_description">{{ ui_t('admin_themes.description_optional', 'Description') }} <small class="text-muted">{{ ui_t('admin_themes.optional', '(optional)') }}</small></label>
                        <textarea class="form-control" id="theme_detailed_description" name="detailed_description" rows="3" placeholder="{{ ui_t('admin_themes.theme_detailed_placeholder', 'Optional detailed description for this thematic area') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="theme_display_order">{{ ui_t('admin_themes.display_order', 'Display Order') }}</label>
                        <input type="number" min="0" step="1" class="form-control" id="theme_display_order" name="display_order" required>
                        <small class="text-muted">{{ ui_t('admin_themes.display_order_help', 'Lower numbers appear first in theme selection lists.') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="icon">{{ ui_t('admin_themes.icon', 'Icon') }}</label>
                        <select class="form-control select2-fa-icons" id="theme_icon" name="icon" required>
                            <option value="">{{ ui_t('admin_themes.select_icon', 'Select icon class') }}</option>
                            @foreach(($faIconOptions ?? []) as $iconClass)
                                <option value="{{ $iconClass }}">{{ $iconClass }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            {{ ui_t('admin_themes.using_font_awesome', 'Using Font Awesome :version.', ['version' => $faVersion ?? '5.3.1']) }}
                            <a href="{{ $faCheatsheetUrl ?? 'https://fontawesome.com/v5.3.1/icons?d=gallery&m=free' }}" target="_blank" rel="noopener noreferrer">{{ ui_t('admin_themes.open_cheatsheet', 'Open cheatsheet') }}</a>
                        </small>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ ui_t('general.cancel', 'Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ ui_t('general.update', 'Update') }}</button>
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
            var detailed_description = button.data('detailed_description');
            var icon = button.data('icon');
            var display_order = button.data('display_order');

            console.log({
                id,
                description,
                detailed_description,
                icon,
                display_order
            })

            // Populate the form fields with the retrieved data
            $('#theme_id').val(id);
            $('#theme_description').val(description);
            $('#theme_detailed_description').val(detailed_description || '');
            $('#theme_display_order').val(display_order ?? 0);
            var iconSelect = $('#theme_icon');
            if (icon && iconSelect.find('option[value="' + icon + '"]').length === 0) {
                iconSelect.append(new Option(icon, icon, false, false));
            }
            iconSelect.val(icon).trigger('change');

            $('#edit-theme-modal .select2-fa-icons').select2({
                placeholder: @json(ui_t('admin_themes.select_icon', 'Select icon class')),
                width: '100%',
                dir: "ltr",
            });
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#edit-theme-modal').on('hidden.bs.modal', function() {
            $('#theme_id').val('');
            $('#theme_description').val('');
            $('#theme_detailed_description').val('');
            $('#theme_display_order').val('0');
            $('#theme_icon').val('').trigger('change');
        });
    </script>
@endpush
