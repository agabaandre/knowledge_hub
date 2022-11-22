<!--  Extra Large modal example -->
<div class="row">

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left"><?php echo $title; ?></h4>
        </div>

        <div class="card-body text-left">
            <div class="row justify-content-center">

                <!-- toast -->
                <div id="toast-3s" class="toast toast-3s fade hide" role="alert" aria-live="assertive" data-delay="3000" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">Message</strong>
                        <small class="text-muted"></small>
                        <button type="button" class="m-l-5 mb-1 mt-1 close" data-dismiss="toast" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="notification">

                    </div>
                </div>
                <!-- end toast -->
            </div>
            <?php echo form_open_multipart(base_url('publications/save'), array('id' => 'publications', 'class' => 'publications')); ?>

            <?php //print_r($subthemes);
            ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="id" class="newform">
                <div class="row">

                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Title</label>
                                <textarea placeholder="Title" rows="6" class="form-control newform" id="title" name="title" required></textarea>
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
                                <select class="form-control js-example-basic-single" name="author" required>
                                    <option disabled>Select</option>
                                    <?php foreach ($authors as $author) : ?>
                                        <option value="<?php echo $author->id; ?>">
                                            <?php echo $author->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Theme</label>
                                <select class="form-control js-example-basic-single" name="sub_thematic_area_id" required select2>
                                    <option disabled>Select</option>
                                    <?php foreach ($subthemes as $subtheme) : ?>
                                        <option value="<?php echo $subtheme->id; ?>">
                                            <?php echo $subtheme->description; ?>
                                        </option>
                                    <?php endforeach; ?>
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
                                <textarea placeholder="Descripion" class="form-control newform" id="description" name="description" required></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">File Type</label>
                                <select class="form-control select2" name="file_type" required>
                                    <option disabled>Select</option>
                                    <?php foreach ($filetypes as $filetype) : ?>
                                        <option value="<?php echo $filetype->id; ?>">
                                            <?php echo $filetype->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Geographical Coverage</label>
                                <select class="form-control js-example-basic-single select2" name="geographical_coverage" required>
                                    <option disabled>Select</option>
                                    <?php foreach ($geoareas as $geoarea) : ?>
                                        <option value="<?php echo $geoarea->id; ?>">
                                            <?php echo $geoarea->name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="display:none;">
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
                                <select class="form-control select2" name="is_active" required>
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
        // console.log(data);
        $.ajax({
            url: url,
            method: "post",
            data: data,
            success: function(res) {
                if (res == "OK") {

                    $('.notification').html("<center><font color='green'>Publication Added</font></center>");
                    $('.toast-3s').toast('show');
                } else {

                    $('.notification').html("<center>" + res + "</center>");
                    $('.toast-3s').toast('show');
                }


                // console.log(res);
            } //success
        }); // ajax
    });
</script>