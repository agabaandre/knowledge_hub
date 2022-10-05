<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Add Slider</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php echo form_open_multipart(base_url('slides/save'), array('id' => 'sliders', 'class' => 'sliders')); ?>
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="name">Caption</label>
                <input type="text" placeholder="Caption (optional)" class="form-control" id="caption" name="caption">
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="image">Image</label>
                <input type="file"  class="form-control" id="image" name="image" required>
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