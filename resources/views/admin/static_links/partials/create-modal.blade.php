<div class="modal" id="create-static-link-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Static Link</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.static_links.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>
          <div class="form-group">
            <label for="order">Order</label>
            <input type="number" class="form-control" id="order" name="order" required>
          </div>
          <div class="form-group">
            <label for="link">Link</label>
            <input type="url" class="form-control" id="link" name="link" required>
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="open_in_new_tab" name="open_in_new_tab" value="1">
            <label class="form-check-label" for="open_in_new_tab">Open in new tab</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Link</button>
        </div>
      </form>
    </div>
  </div>
</div> 