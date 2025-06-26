<!--  Extra Large modal example -->
<div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Tag</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ url('admin/tags/save') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-1">
              <label class="form-label" for="name">Tag</label>
              <input type="text" placeholder="Enter Tag" class="form-control newform" id="name" name="name" required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="name">Health Emergency?</label>
              <select placeholder="Enter Tag" class="form-control newform" id="is_health_emergency" name="is_health_emergency" required>
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="overview">Overview</label>
              <textarea class="form-control summernote" id="overview" name="overview"></textarea>
            </div>
          </div>

          <!-- <div  class="col-md-4"> -->
          <!-- </div> -->
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

<script>
function toggleOverviewRequired(modalSelector) {
    var $modal = $(modalSelector);
    $modal.find('#is_health_emergency').on('change', function() {
        if ($(this).val() == '1') {
            $modal.find('#overview').attr('required', true);
        } else {
            $modal.find('#overview').removeAttr('required');
        }
    }).trigger('change');
}

// For create modal
$('#create-modal').on('shown.bs.modal', function () {
    toggleOverviewRequired('#create-modal');
});

// For edit modal
$('#edit-tag-modal').on('shown.bs.modal', function () {
    toggleOverviewRequired('#edit-tag-modal');
});
</script>
