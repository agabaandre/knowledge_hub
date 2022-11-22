<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Author</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php echo form_open_multipart(base_url('authors/save'), array('id' => 'publications', 'class' => 'publications')); ?>
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-6">
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="name">Author Name</label>
                <input type="text" placeholder="Author Name" class="form-control" id="name" name="name" required>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="telephone">Telephone</label>
                <input type="text" placeholder="Telephone" class="form-control" id="telephone" name="telephone" required>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="text" placeholder="Email" class="form-control" id="email" name="email" required>
              </div>
            </div>
          </div>
          <div class="col-md-6">

            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="is_organsiation">Is Organization</label>
                <select class="form-control" id="is_organsiation" name="is_organsiation" required>

                  <option value="1">YES</option>
                  <option value="0">NO</option>

                </select>
              </div>

            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <textarea placeholder="Address" class="form-control" id="address" name="address" required></textarea>
              </div>
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