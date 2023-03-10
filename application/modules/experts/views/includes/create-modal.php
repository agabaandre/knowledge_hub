<!--  Extra Large modal example -->
<div class="modal" id="create">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Add New Expert </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php echo form_open_multipart(base_url('experts/save'), array('id' => 'filetypes', 'class' => 'filetypes')); ?>

      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-6 py-1">
              <label class="form-label" for="name">Expert Type</label>
              <select name="expert_type" class="form-control" required>
                <option value="" selected disabled>Select</option>
                <option>Rapid responder</option>
              </select>
          </div>

          <div class="col-md-6 py-1">
              <label class="form-label" for="name">Country</label>
              <select name="country_id" class="form-control select2" required style="width:280px">
                <option value="" selected disabled>Select</option>
                <?php foreach($countries as $country): ?>
                <option  <?php echo (old('country_id')==$country->id)?'selected':''; ?> value="<?php echo $country->id; ?>"><?php echo $country->name; ?></option>
                <?php endforeach; ?>
              </select>
          </div>

          <div class="col-md-6 py-1">
              <label class="form-label" for="name">First Name</label>
              <input name="first_name" value="<?php echo old('first_name') ?>" class="form-control" placeholder="First Name" required>
          </div>

          <div class="col-md-6 py-1">
              <label class="form-label" for="name">Last Name</label>
              <input name="last_name" value="<?php echo old('last_name') ?>" class="form-control" placeholder="Last Name" required>
          </div>

          <div class="col-md-6 py-1">
              <label class="form-label" for="name">Email</label>
              <input name="email" value="<?php echo old('email') ?>" class="form-control" placeholder="Email" required>
          </div>

          <div class="col-md-6 py-1">
              <label class="form-label" for="name">Phone Number</label>
              <input name="phone_number" value="<?php echo old('phone_number') ?>" class="form-control" placeholder="Phone Number" required>
          </div>

          

          <div class="col-md-12 py-1">
              <label class="form-label" for="name">Expertise Field</label>
              <select name="occupation" class="form-control" required>
                <option value="" selected disabled>Select</option>
                <option>ICT</option>
                <option>Statistics</option>
                <option>Public Health</option>
                <option>Administration</option>
              </select>
          </div>

          <div class="col-md-12 py-1">
              <label class="form-label" for="name">Job Title</label>
              <input name="job_title" value="<?php echo old('job_title') ?>" class="form-control" placeholder="Job Title">
          </div>
          

        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary" type="submit">Submit Record</button>
      </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>