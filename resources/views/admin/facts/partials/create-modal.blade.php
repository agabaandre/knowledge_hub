<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Fact</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/facts/save') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">

            <div class="form-group col-md-12">
              <label class="form-label" for="name">Title</label>
              <input type="text" placeholder="Enter Title" class="form-control newform" id="title" name="title" required>
            </div>

            <div class="form-group col-md-12">
              <label class="form-label" for="name">Fact Summary</label>
              <textarea placeholder="Sumamry" class="form-control summernote-sm" id="title" name="summary"></textarea>
            </div>

            <div class="form-group col-md-12">
              <label class="form-label" for="name">Fact Details</label>
              <textarea placeholder="Details" class="form-control summernote" id="title" name="description"></textarea>
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
