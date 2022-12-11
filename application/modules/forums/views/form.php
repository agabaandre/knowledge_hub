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

            <div class="container">

        
            <?php echo form_open_multipart(base_url('forums/save'), array('id' => 'forum', 'class' => 'forum')); ?>

            <?php //print_r($subthemes);
            ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="id" class="">
                <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Discussion Title</label>
                                <input type="text" placeholder="Title" rows="1" class="form-control " id="title" name="title" required/>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description"> Description</label>
                                <textarea placeholder="Descripion" class="form-control " id="description" name="description" required></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile">
                                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>
            <div class="modal-footer">

                <button class="btn btn-success" type="submit">Submit</button>
            </div>

            <?php echo form_close(); ?>
            
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
