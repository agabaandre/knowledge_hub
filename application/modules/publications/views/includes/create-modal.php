<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Health Theme</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
          <form class="needs-validation" action="<?php echo base_url()?>/publications/save" method="POST" enctype='multipart/form-data'>

         <div class="modal-body">
              <input type="hidden" name="id" id="id" class="newform">
              <div class="row">
                  <div class="col-md-12">
                      <div class="mb-3">
                          <label class="form-label" for="description">Description</label>
                          <textarea  placeholder="Description" class="form-control newform" id="description" name="description" required></textarea>
                      </div>
                  </div>

                  <div class="col-md-12">
                      <div class="mb-3">
                          <label class="form-label" for="publication">Publication</label>
                          <input type="text" placeholder="Publication" class="form-control newform" id="publication" name="publication" required>
                      </div>
                  </div>

                  <div class="col-md-12">
                      <div class="mb-3">
                          <label class="form-label" for="publication">Thematic Area</label>
                          <select class="form-control"  name="theme">
                            <option>Select</option>
                          </select>
                      </div>
                  </div>

                  <div class="col-md-12">
                      <div class="mb-3">
                          <label class="form-label" for="publication">Sub Thematic Area</label>
                          <select class="form-control"  name="sub_theme">
                            <option>Select</option>
                          </select>
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


