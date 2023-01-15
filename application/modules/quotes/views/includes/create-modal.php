<!-- Create Quote Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="create-modal-label">Create New Quote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open('quotes/save'); ?>
        
        <div class="form-group">
          <label for="quote">Quote</label>
          <textarea class="form-control" name="quote" id="quote" rows="3" placeholder="Enter Quote" id="quote_description"></textarea>
        </div>

        <!-- Button ALigned to right -->
        <div class="form-group">
          <button type="submit" class="btn btn-success float-right">Create Quote</button>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
