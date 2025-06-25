<div class="modal" id="edit-static-link-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Static Link</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="edit-static-link-form" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="form-group">
            <label for="edit_title">Title</label>
            <input type="text" class="form-control" id="edit_title" name="title" required>
          </div>
          <div class="form-group">
            <label for="edit_order">Order</label>
            <input type="number" class="form-control" id="edit_order" name="order" required>
          </div>
          <div class="form-group">
            <label for="edit_link">Link</label>
            <input type="url" class="form-control" id="edit_link" name="link" required>
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="edit_open_in_new_tab" name="open_in_new_tab" value="1">
            <label class="form-check-label" for="edit_open_in_new_tab">Open in new tab</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Link</button>
        </div>
      </form>
    </div>
  </div>
</div> 