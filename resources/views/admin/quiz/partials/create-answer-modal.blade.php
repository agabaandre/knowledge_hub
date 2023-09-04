<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Add Answer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/quiz/save_answer') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="name">Answer</label>
              <input type="text" placeholder="Enter Answer" class="form-control newform" id="answer" name="answer" required>
            </div>
          </div>
          <div class="row container">
            
               <label class="form-check-inline px-2">
                        <input type="radio" name="answer_type" value="wrong" checked class="form-check-input">Wrong Response
                </label>
                <label class="form-check-inline px-2">
                        <input type="radio" name="answer_type" value="correct"  class="form-check-input"> Correct Answer
                </label>
          </div>
        <div class="col-md-12 explanation" style="display: none;">
                <div class="mb-3">
                <label class="form-label" for="name">Answer Explanation</label>
                <textarea type="text" placeholder="Enter Explanation" class="form-control summernote" id="explanation" name="explanation" ></textarea>
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
