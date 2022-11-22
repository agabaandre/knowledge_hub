<div class="row">
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Add Subject</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo base_url(); ?>kpi/addsubject" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                        <div class="form-group row">
                            <label for="award_name" class="col-sm-3 col-form-label">
                                Subject Area</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" placeholder="Subject Area" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="aw_description" class="col-sm-3 col-form-label">
                                Display Index</label>
                            <div class="col-sm-9">
                                <select name="display_index" class="form-control codeigniterselect">
                                    <option value="" selected="selected">Select One...</option>
                                    <?php for ($i = 0; $i <= 20; $i++) { ?>
                                        <option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?></option>
                                    <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="awr_gift_item" class="col-sm-3 col-form-label">
                                Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="sub_description" cols="5" rows="5"> </textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="aw_description" class="col-sm-3 col-form-label">
                                Module</label>
                            <div class="col-sm-9">
                                <select name="module" class="form-control">
                                    <?php
                                    $path = 'application/modules/';
                                    $map  = directory_map($path);
                                    if (is_array($map) && sizeof($map) > 0)
                                        foreach ($map as $key => $value) {
                                    ?>
                                        <option value="<?php echo str_replace("/", "", $key); ?>" <?php if (str_replace("/", "", $key) != "data") {
                                                                                                        echo "disabled";
                                                                                                    } ?>><?php echo str_replace("/", "", $key); ?></option>
                                    <?php
                                        } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="aw_description" class="col-sm-3 col-form-label">
                                Font awesome Icon</label>
                            <div class="col-sm-9">
                                <select name="icon" class="form-control codeigniterselect">
                                    <option value="circle" selected>Circle</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="reset" class="btn btn-primary w-md m-b-5">Reset</button>
                            <button type="submit" class="btn btn-success w-md m-b-5">Save</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>