<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Thematic Area</h5>
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
              <input type="text" placeholder="Thematic Area" class="form-control" id="name" name="name" required>
            </div>
          </div>
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="name">Icon</label>
              <input type="text" placeholder="Icon e.g fa-eye" class="form-control" id="icon" name="icon" required>
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
