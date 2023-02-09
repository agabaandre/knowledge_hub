<!-- Create Question Modal -->
<div class="modal fade" id="edit-quize-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="edit-modal-label">Update Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open('quize/save'); ?>

        <div class="form-group">
          <label for="question">Question</label>
          <textarea class="form-control" name="question" id="question" rows="3" placeholder="Enter Question"></textarea>
        </div>

        <div class="form-group">
          <label for="answer1">Answer 1</label>
          <input type="text" class="form-control" name="answer1" id="answer1" placeholder="Enter Answer 1">
        </div>

        <div class="form-group">
          <label for="answer2">Answer 2</label>
          <input type="text" class="form-control" name="answer2" id="answer2" placeholder="Enter Answer 2">
        </div>

        <div class="form-group">
          <label for="answer3">Answer 3</label>
          <input type="text" class="form-control" name="answer3" id="answer3" placeholder="Enter Answer 2">
        </div>

        <div class="form-group">
          <label for="correct_answer">Correct Answer</label>
          <select class="form-control" name="correct_answer" id="correct_answer">
            <option value="1">Answer 1</option>
            <option value="2">Answer 2</option>
            <option value="3">Answer 3</option>
          </select>
        </div>

        <!-- ID of question -->
        <input type="hidden" name="id" id="id">

        <!-- Button ALigned to right -->
        <div class="form-group">
          <button type="submit" class="btn btn-success float-right">Update Question</button>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>