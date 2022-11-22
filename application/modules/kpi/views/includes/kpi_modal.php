<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Add KPI</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(base_url('kpi/create'), array('id' => 'kpi', 'class' => 'kpi')); ?>
                <div class="col-md-12 ml-auto">
                    <div class="form-group row">
                        <label for="shortname" class="col-sm-3 col-form-label">
                            KPI Short Name</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" placeholder="KPI Short Name" class=" form-control">
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
                        <label for="frequency" class="col-sm-3 col-form-label">
                            Frequency</label>
                        <div class="col-sm-9">
                            <select name="frequency" class="form-control codeigniterselect">
                                <option value="Annualy" selected>Annually</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">
                            Computation Method</label>
                        <div class="col-sm-9">
                            <textarea name="computation_method" col="6" rows="3" class="form-control" id=""></textarea>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">
                            Data Sources</label>
                        <div class="col-sm-9">
                            <textarea name="data_source" class="form-control" id=""></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="subject" class="col-sm-3 col-form-label">
                            Subject Area</label>
                        <div class="col-sm-9">
                            <select name="subject_area" class="form-control codeigniterselect">
                                <option value="" selected>SELECT VALUE</option>
                                <?php $elements = Modules::run('Kpi/get_subjects');
                                foreach ($elements as $element) : ?>
                                    <option value="<?php echo $element->id ?>"><?php echo $element->name ?></option>
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