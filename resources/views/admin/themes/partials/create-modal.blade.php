<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Sub Thematic Area</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
              <label class="form-label" for="name">Thematic Area</label>
              <input type="text" placeholder="Thematic Area" class="form-control" id="name" name="description" required>
            </div>
          </div>
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="display_order">Display Order</label>
              <input type="number" min="0" step="1" class="form-control" id="display_order" name="display_order" value="0" required>
              <small class="text-muted">Lower numbers appear first in theme selection lists.</small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="icon">Icon</label>
              <select class="form-control select2-fa-icons" id="icon" name="icon" required>
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
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary" type="submit">Save Record</button>
      </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

@push('modal-scripts')
<script>
  $('#create-modal').on('show.bs.modal', function () {
    $('#create-modal .select2-fa-icons').select2({
      placeholder: 'Select icon class',
      width: '100%',
      dir: 'ltr'
    });
  });
  $('#create-modal').on('hidden.bs.modal', function () {
    $('#create-modal #icon').val('').trigger('change');
    $('#create-modal #display_order').val('0');
  });
</script>
@endpush
