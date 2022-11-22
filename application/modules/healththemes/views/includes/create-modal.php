<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Health Theme</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php echo form_open_multipart(base_url('healththemes/save'), array('id' => 'themes', 'class' => 'themes')); ?>

      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="description">Theme Description</label>
              <input type="text" placeholder="Theme Description" class="form-control newform" id="description" name="description" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="description">Icon (Use Font Awesome eg. fa-user)</label>
              <input type="text" placeholder="icon" class="form-control newform" id="icon" name="icon" required>
            </div>
          </div>

          <!-- <div  class="col-md-4"> -->
          <!-- </div> -->
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