<div class="card">
  <div class="col-md-12" style=" background:white; border-radius: 5px;">
    <?php $i = 1;
    //print_r($facilitydata);
    ?>

    <div class="card-header with-border">
      <h5 class="card-title"> Constants</h5>
    </div>
  </div>
  <?php if ($this->session->flashdata('message')):?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong><?php echo $this->session->flashdata('message');?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php endif; ?>
  <hr style="border:1px solid rgb(140, 141, 137);" />
  <div class="col-md-12">
    <?php echo form_open_multipart(base_url('constants/saveconstants'), array('id' => 'constants')); ?>
      <?php foreach ($setting as $key => $value) { ?>
        <div id="">
          <label><?php echo strtoupper(str_replace("_", " ", $key)); ?></label>
          <textarea class="form-control" name="<?php echo $key; ?>" style="width:100%;" <?php if ($key == 'id') {
                                                                                          echo "style='display:none;'";
                                                                                        } ?>)><?php echo $value; ?></textarea>
        </div>
      <?php  } ?>


      <div id="footer-buttons" style="clear:both; margin-top:20px; margin-bottom:4px;">
        <button class="btn btn-primary" type="submit"><span class="add"></span>Save</button>
    </form>
  </div>
</div>

</div>
</div>