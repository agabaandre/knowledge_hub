<div class="modal" id="edit-subtheme-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">{{ ui_t('admin_themes.edit_subtheme_title', 'Edit subtheme') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editThemeForm" action="{{ url('/admin/subthemes/save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields for editing sub-thematic area -->
                    <input type="hidden" name="id" id="subtheme_id">

                    <div class="form-group">
                        <label for="description">{{ ui_t('admin_themes.description', 'Description') }}</label>
                        <input type="text" class="form-control" id="subtheme_description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="subtheme_detailed_description">{{ ui_t('admin_themes.description_optional', 'Description') }} <small class="text-muted">{{ ui_t('admin_themes.optional', '(optional)') }}</small></label>
                        <textarea class="form-control" id="subtheme_detailed_description" name="detailed_description" rows="3" placeholder="{{ ui_t('admin_themes.subtheme_detailed_placeholder', 'Optional detailed description for this subtheme') }}"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="icon">{{ ui_t('admin_themes.icon', 'Icon') }}</label>
                        <select class="form-control select2-fa-icons" id="subtheme_icon" name="icon" required>
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

                    <div class="form-group">
                        <label for="thematic_area_id">{{ __('admin_nav.thematic_area') }}</label>
                        <select class="form-control select2" id="thematic_area_id" name="thematic_area_id">
                            @foreach ($themes as $theme)
                                <option value="{{ $theme['id'] }}">{{ $theme['description'] }}</option>
                            @endforeach
                        </select>
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
        $('#edit-subtheme-modal').on('show.bs.modal', function(event) {
            // Get the button that triggered the modal
            var button = $(event.relatedTarget);

            // Extract data from the button
            var id = button.data('id');
            var description = button.data('description');
            var detailed_description = button.data('detailed_description');
            var icon = button.data('icon');
            var thematic_area_id = button.data('thematic_area_id');

            console.log({
                id,
                description
            });

            // Populate the form fields with the retrieved data
            $('#subtheme_id').val(id);
            $('#subtheme_description').val(description);
            $('#subtheme_detailed_description').val(detailed_description || '');
            var iconSelect = $('#subtheme_icon');
            if (icon && iconSelect.find('option[value="' + icon + '"]').length === 0) {
                iconSelect.append(new Option(icon, icon, false, false));
            }
            iconSelect.val(icon).trigger('change');
            $('#thematic_area_id').val(thematic_area_id);

            // Initialize Select2 for thematic area select field
            $('.select2').select2({
                placeholder: @json(ui_t('admin_themes.select_thematic_area', 'Select a thematic area')),
                width: '100%',
                dir: "ltr",
            });
            $('#edit-subtheme-modal .select2-fa-icons').select2({
                placeholder: @json(ui_t('admin_themes.select_icon', 'Select icon class')),
                width: '100%',
                dir: 'ltr',
            });
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#edit-subtheme-modal').on('hidden.bs.modal', function() {
            $('#subtheme_id').val('');
            $('#subtheme_description').val('');
            $('#subtheme_detailed_description').val('');
            $('#subtheme_icon').val('');
            $('#thematic_area_id').val('').trigger('change');
        });
    </script>
@endpush
