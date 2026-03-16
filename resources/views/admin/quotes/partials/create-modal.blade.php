<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Create Quote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/quotes/save') }}" method="post" id="quote-form" class="filetypes" enctype="multipart/form-data">
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="quote">Quote</label>
              <textarea placeholder="Enter Quote" rows="5" class="form-control newform" id="quote" name="quote" required></textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="quote_image">Image</label>
              <input type="file" class="form-control" id="quote_image" name="image" accept="image/*">
              <small class="text-muted">Optional. Shown with the quote on the front.</small>
              <div id="quote-image-preview" class="mt-2"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="link_url">Link to details page</label>
              <input type="url" class="form-control" id="link_url" name="link_url" placeholder="https://...">
              <small class="text-muted">Optional. "Read more" will link here.</small>
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
