<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create FAQ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/faqs/save') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">

            <div class="form-group col-md-12">
              <label class="form-label" for="name">Question</label>
              <input type="text" placeholder="Enter Question" class="form-control newform" id="question" name="question" required>
            </div>

            <div class="form-group col-md-12">
              <label class="form-label" for="name">Response/Answer</label>
              <textarea placeholder="Response/Answer" class="form-control summernote" id="answer" name="answer"></textarea>
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
