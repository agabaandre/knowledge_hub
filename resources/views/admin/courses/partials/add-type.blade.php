<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Add Workforce Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/experts/types/save') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="form-group col-md-6">
              <label class="form-label" for="name">Type Name</label>
              <input type="text" placeholder="Type Name" class="form-control" id="type" name="type" required>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="desc">Description</label>
              <input type="text" placeholder="Description" class="form-control" id="desc" name="description">
          </div>

        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary" type="submit">Submit Record</button>
      </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
