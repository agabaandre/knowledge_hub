<!--  Extra Large modal example -->
<div class="modal" id="create">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Add New Health Asset </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php echo form_open_multipart(base_url('healthassets/save'), array('id' => 'filetypes', 'class' => 'filetypes')); ?>

      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-6 py-1">
              <label class="form-label" for="name">Asset Type</label>
              <select name="asset_type_id" class="form-control" required>
                <option value="" selected disabled>Select</option>
                <?php foreach($types as $type): ?>
                <option  <?php echo (old('asset_type_id')==$type->id)?'selected':''; ?> value="<?php echo $type->id; ?>"><?php echo $type->type_name; ?></option>
                <?php endforeach; ?>
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

          <div class="col-md-12 py-1">
              <label class="form-label" for="name">Asset Name</label>
              <input name="asset_name" value="<?php echo old('asset_name') ?>" class="form-control" placeholder="Asset Name" required>
          </div>

          <div class="col-md-12 py-1">
              <label class="form-label" for="name">Asset Resource Url</label>
              <input name="asset_url" value="<?php echo old('asset_url') ?>" class="form-control" placeholder="Resource URL">
          </div>

          <div class="col-md-12 py-1">
              <label class="form-label" for="name">Asset Description</label>
              <textarea name="asset_desc" rows="5" class="form-control" placeholder="Description" required><?php echo old('asset_desc') ?></textarea>
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