<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">{{ ui_t('admin_themes.create_theme_title', 'Create thematic area') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ url('admin/themes/save') }}" method="post" id='themes' class='themes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">


          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="name">{{ ui_t('admin_themes.thematic_area_label', 'Thematic Area') }}</label>
              <input type="text" placeholder="{{ ui_t('admin_themes.thematic_area_label', 'Thematic Area') }}" class="form-control" id="name" name="description" required>
            </div>
          </div>
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="theme_detailed_description">{{ ui_t('admin_themes.description_optional', 'Description') }} <small class="text-muted">{{ ui_t('admin_themes.optional', '(optional)') }}</small></label>
              <textarea class="form-control" id="theme_detailed_description" name="detailed_description" rows="3" placeholder="{{ ui_t('admin_themes.theme_detailed_placeholder', 'Optional detailed description for this thematic area') }}"></textarea>
            </div>
          </div>
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="display_order">{{ ui_t('admin_themes.display_order', 'Display Order') }}</label>
              <input type="number" min="0" step="1" class="form-control" id="display_order" name="display_order" value="0" required>
              <small class="text-muted">{{ ui_t('admin_themes.display_order_help', 'Lower numbers appear first in theme selection lists.') }}</small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="icon">{{ ui_t('admin_themes.icon', 'Icon') }}</label>
              @php $defaultThemeIcon = \App\Models\ThemeticArea::DEFAULT_ICON; @endphp
              <select class="form-control select2-fa-icons" id="icon" name="icon" required>
                <option value="">{{ ui_t('admin_themes.select_icon', 'Select icon class') }}</option>
                @foreach(($faIconOptions ?? []) as $iconClass)
                  <option value="{{ $iconClass }}" @if($iconClass === $defaultThemeIcon) selected @endif>{{ $iconClass }}</option>
                @endforeach
              </select>
              <small class="text-muted d-block mt-1">
                {{ ui_t('admin_themes.using_font_awesome', 'Using Font Awesome :version.', ['version' => $faVersion ?? '5.3.1']) }}
                <a href="{{ $faCheatsheetUrl ?? 'https://fontawesome.com/v5.3.1/icons?d=gallery&m=free' }}" target="_blank" rel="noopener noreferrer">{{ ui_t('admin_themes.open_cheatsheet', 'Open cheatsheet') }}</a>
              </small>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" type="button">{{ ui_t('general.cancel', 'Cancel') }}</button>
        <button class="btn btn-primary" type="submit">{{ ui_t('general.save_record', 'Save Record') }}</button>
      </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

@push('modal-scripts')
<script>
  var defaultThemeIcon = @json(\App\Models\ThemeticArea::DEFAULT_ICON);

  $('#create-modal').on('show.bs.modal', function () {
    $('#create-modal .select2-fa-icons').select2({
      placeholder: @json(ui_t('admin_themes.select_icon', 'Select icon class')),
      width: '100%',
      dir: 'ltr'
    });
    var iconSelect = $('#create-modal #icon');
    if (iconSelect.find('option[value="' + defaultThemeIcon + '"]').length === 0) {
      iconSelect.append(new Option(defaultThemeIcon, defaultThemeIcon, true, true));
    }
    iconSelect.val(defaultThemeIcon).trigger('change');
  });
  $('#create-modal').on('hidden.bs.modal', function () {
    $('#create-modal #icon').val(defaultThemeIcon).trigger('change');
    $('#create-modal #display_order').val('0');
    $('#create-modal #theme_detailed_description').val('');
  });
</script>
@endpush
