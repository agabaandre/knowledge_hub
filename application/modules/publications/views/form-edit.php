<!--  Extra Large modal example -->
<div class="row">

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left"><?php echo $title; ?></h4>
             <hr>
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

            <div class="modal-body">
                <input type="hidden" name="id" id="id" class="newform">
                <div class="row">

                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Title</label>
                                <textarea placeholder="Title" rows="6" class="form-control newform" id="title" name="title" required
                               >
                                <?php echo $publication->title; ?>
                            </textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Description</label>
                                <textarea id="publication_description" placeholder="Descripion" class="form-control newform" name="description" style="min-height: 400px;">
                                <?php echo $publication->description; ?>
                            </textarea>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Author</label>
                                <select class="form-control js-example-basic-single" name="author" required id="authors">
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

                        <div class="col-md-12">
                            <!-- Section Label -->
                            <label class="form-label mb-3" for="publication">Publication Document Type</label>
                            <div class="mb-3">
                                <label class="form-check-inline">
                                    <input type="radio" name="upload_type" value="upload" checked class="form-check-input"> Attachment
                                </label>
                                <label class="form-check-inline">
                                    <input type="radio" name="upload_type" value="link" class="form-check-input">External Link
                                </label>

                                <div id="file-input">
                                    <!-- <label class="form-label" for="publication">Publication Doc</label> -->
                                    <input placeholder="Attach publication document" type="file" name="file" class="form-control">
                                </div>

                                <div id="link-input" style="display:none;">
                                    <!-- <label class="form-label" for="publication">Publication URL Link</label> -->
                                    <input placeholder="Add publication link" type="text" name="link" class="form-control">
                                </div>
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
                                <label class="form-label" for="publication">Search Tags</label>
                                <select class="form-control select2" name="tags" required multiple>
                                    <option disabled>Select</option>
                                    <?php foreach ($tags as $tag) : ?>

                                        <option value="<?php echo $tag->id; ?>">
                                            <?php echo $tag->tag_text; ?>
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
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" class="form-control" name="cover" id="">

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
    $('input[name="upload_type"]').on('change', function() {
        if ($(this).val() == 'upload') {
            $('#file-input').show();
            $('#link-input').hide();
        } else {
            $('#file-input').hide();
            $('#link-input').show();
        }
    });

    $('#publications').submit(function(e) {
        e.preventDefault();

        var form = $(this);

        // Get the form data.]
        var formData = new FormData(form.get(0));

        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        html: response.message,
                        icon: 'success'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });

                    $('#publications').trigger("reset");

                } else {
                    Swal.fire({
                        title: 'Error!',
                        html: response.message,
                        icon: 'error'
                    });
                }
            }
        });



    });
</script>
