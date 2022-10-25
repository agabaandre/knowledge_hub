<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">KPI DATA</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(); ?>kpi/data" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <div class="col-md-12 ml-auto">
                        <div class="form-group row">
                            <label for="shortname" class="col-sm-3 col-form-label">
                                Period</label>
                            <div class="col-sm-9">
                                <input type="date" name="short_name" placeholder="Period" class=" ">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-sm-3 col-form-label">
                                KPI</label>
                            <div class="col-sm-9">
                                <select name="subject_area" class="form-control codeigniterselect">
                                    <?php $elements = Modules::run('Kpi/k');
                                    foreach ($elements as $element) : ?>
                                        <option value="<?php echo $element->id ?>" selected="selected"><?php echo $element->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-sm-3 col-form-label">
                                Country</label>
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
                                KPI</label>
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
                            <label for="description" class="col-sm-3 col-form-label">
                                Value</label>
                            <div class="col-sm-9">
                                <input name="value" type="text" col="6" rows="3" class="form-control" id="">
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