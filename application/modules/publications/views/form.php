<!--  Extra Large modal example -->
<div class="row">

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left"><?php echo $title; ?></h4>
        </div>

        <div class="card-body text-left">
            <?php echo form_open(base_url('publications/save'), array('id' => 'publications', 'class' => 'publications')); ?>

            <?php //print_r($publications); 
            ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="id" class="newform">
                <div class="row">
                    <!-- toast -->
                    <div class="toast toast-3s fade hide" role="alert" aria-live="assertive" data-delay="3000" aria-atomic="true">
                        <div class="toast-header">
                            <img src="../assets/images/favicon.ico" alt="" class="img-fluid m-r-5" style="width:20px;">
                            <strong class="mr-auto">Message</strong>
                            <small class="text-muted"></small>
                            <button type="button" class="m-l-5 mb-1 mt-1 close" data-dismiss="toast" aria-label="Close">
                                <span>×</span>
                            </button>
                        </div>
                        <div id="notification">

                        </div>
                    </div>
                    <!-- end toast -->
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Title</label>
                                <textarea placeholder="Title" class="form-control newform" id="title" name="description" required></textarea>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Publication URL Link</label>
                                <input type="text" placeholder="URL Link" class="form-control newform" id="publication" name="publication" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Author</label>
                                <select class="form-control" name="author_id" required>
                                    <option disabled>Select</option>
                                    <option value="<?php echo $author->id; ?>">
                                        <?php echo $author->name; ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Thematic Area</label>
                                <select class="form-control" name="theme">
                                    <option disabled>Select</option>
                                    <option value="<?php echo $theme->id; ?>">
                                        <?php echo $theme->name; ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Thematic Area</label>
                                <select class="form-control" name="sub_thematic_area_id" required>
                                    <option disabled>Select</option>
                                    <option value="<?php echo $sub_theme->id; ?>">
                                        <?php echo $sub_theme->name; ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- <div  class="col-md-4"> -->
                        <!-- </div> -->
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Description</label>
                                <textarea placeholder="Title" class="form-control newform" id="description" name="description" required></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">File Type</label>
                                <select class="form-control" name="file_type_id" required>
                                    <option disabled>Select</option>
                                    <option value="<?php echo $filetype->id; ?>">
                                        <?php echo $filetype->name; ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Geographical Coverage</label>
                                <select class="form-control" name="geographical_coverage_id" required>
                                    <option disabled>Select</option>
                                    <option value="<?php echo $geoarea->id; ?>">
                                        <?php echo $geoarea->name; ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile">
                                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                    <!-- <div class="invalid-feedback">Example invalid custom file feedback</div> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Status</label>
                                <select class="form-control" name="sub_thematic_area_id" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>

                                </select>
                            </div>
                        </div>

                        <!-- <div  class="col-md-4"> -->
                        <!-- </div> -->
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-primary" type="submit">Save Record</button>
            </div>

            <?php echo form_close(); ?>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>
    $('#publications').submit(function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        var url = '<?php echo base_url(); ?>publications/save'
        console.log(data);
        $.ajax({
            url: url,
            method: "post",
            data: data,
            success: function(res) {
                // if (res == "OK") {
                //     $('.notification').html("<center><font color='green'>Publication Added</font></center>");
                // } else {
                //     $('.notification').html("<center>" + res + "</center>");
                // }
                $('.toast-3s').toast('show');

                console.log(data);
            } //success
        }); // ajax
    });
</script>