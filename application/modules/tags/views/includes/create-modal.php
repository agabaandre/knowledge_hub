<!-- Create Tag Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="create-modal-label">Create New Tag</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open('tags/save'); ?>
        <div class="form-group">
          <label for="tag_name">Tag Name</label>
          <input type="text" class="form-control" name="tag_name" id="tag_name" placeholder="Enter tag name">
        </div>
        <!-- Button ALigned to right -->
        <div class="form-group">
          <button type="submit" class="btn btn-success float-right">Create Tag</button>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
