<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Geoghraphical Area</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
          <form class="needs-validation" action="<?php echo base_url()?>geoareas/save" method="POST" enctype='multipart/form-data'>

         <div class="modal-body">
              <input type="hidden" name="id" id="id" class="newform">
              <div class="row">
                  <div class="col-md-12">
                      <div class="mb-3">
                          <label class="form-label" for="name">Area</label>
                          <input type="text" placeholder="Enetr area" class="form-control newform" id="name" name="name" required>
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


