<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="margin-right:50px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Add KPI</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(); ?>kpi/addkpi" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <div class="col-md-6 ml-auto">
                        <div class="form-group row">
                            <label for="kpiid" class="col-sm-3 col-form-label">
                                Indicator Identifier(KPI ID)</label>
                            <div class="col-sm-9">
                                <input type="text" name="kpi_id" placeholder="KPI-0" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="shortname" class="col-sm-3 col-form-label">
                                Short Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="short_name" placeholder="KPI Short Name" class=" form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="indiactor_statement" class="col-sm-3 col-form-label">
                                Indicator Statement</label>
                            <div class="col-sm-9">
                                <textarea name="indicator_statement" class="form-control" id=""></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="frequency" class="col-sm-3 col-form-label">
                                Frequency</label>
                            <div class="col-sm-9">
                                <select name="frequency" class="form-control codeigniterselect">
                                    <option value="Annualy" selected="selected">Annualy</option>
                                    <option value="Monthly" selected="selected">Monthly</option>
                                    <option value="Quarterly" selected="selected">Quarterly</option>
                                </select>
                            </div>
                        </div>
                        <?php if (settings() == 'category_two_menu.php') : ?>
                            <div class="form-group row">
                                <label for="cumulative" class="col-sm-3 col-form-label">
                                    Indicator Type</label>
                                <div class="col-sm-9">
                                    <select name="kpi_type" class="form-control codeigniterselect">
                                        <option value="1">Outcome</option>
                                        <option value="2">Output</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cumulative" class="col-sm-3 col-form-label">
                                    Objective</label>
                                <div class="col-sm-9">
                                    <select name="category_two_id" class="form-control codeigniterselect">
                                        <?php foreach ($category_twos as $obj) : ?>
                                            <option value="<?php echo $obj->id; ?>">
                                                <?php echo $obj->cat_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group row">
                            <label for="cumulative" class="col-sm-3 col-form-label">
                                Is Cumulative</label>
                            <div class="col-sm-9">
                                <select name="is_cumulative" class="form-control codeigniterselect">
                                    <option value="0" selected="selected">No</option>
                                    <option value="1" selected="selected">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-3 col-form-label">
                                Computation Method</label>
                            <div class="col-sm-9">
                                <textarea name="computation" col="6" rows="3" class="form-control" id=""></textarea>
                            </div>
                        </div>
                    </div>
                    <!--End divider -->
                    <div class="col-md-6 ml-auto">
                        <div class="form-group row">
                            <label for="description" class="col-sm-3 col-form-label">
                                Current Target</label>
                            <div class="col-sm-9">
                                <input type="number" name="current_target" class="form-control" id="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-3 col-form-label">
                                Indicator description</label>
                            <div class="col-sm-9">
                                <textarea name="description" col="10" rows="5" class="form-control" id=""></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-3 col-form-label">
                                Data Sources</label>
                            <div class="col-sm-9">
                                <textarea name="data_sources" class="form-control" id=""></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-sm-3 col-form-label">
                                Subject Area</label>
                            <div class="col-sm-9">
                                <select name="subject_area" class="form-control codeigniterselect">
                                    <?php $elements = Modules::run('Kpi/subjectData');
                                    foreach ($elements as $element) : ?>
                                        <option value="<?php echo $element->id ?>" selected="selected"><?php echo $element->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-sm-3 col-form-label">
                                Category</label>
                            <div class="col-sm-9">
                                <select name="category_two_id" class="form-control codeigniterselect">
                                    <?php $elements = Modules::run('Kpi/getCategoryTwos');
                                    foreach ($elements as $element) : ?>
                                        <option value="<?php echo $element->id ?>" selected="selected"><?php echo $element->cat_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!---End sub2-->
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