<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">KPI DATA</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(base_url('kpi/data'), array('id' => 'kpidata', 'class' => 'kpidata')); ?>
                <div class="col-md-12 ml-auto">
                    <div class="form-group row">
                        <label for="shortname" class="col-sm-3 col-form-label">
                            Period</label>
                        <div class="col-sm-9">
                            <input type="text" id="date" name="period" class="form-control" placeholder="Period">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="subject" class="col-sm-3 col-form-label">
                            Country</label>
                        <div class="col-sm-9">
                            <select name="country_id" class="form-control select2">
                                <?php $elements = Modules::run('Kpi/get_countries');
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
                            <select name="kpi_id" class="form-control select2">
                                <?php $elements = Modules::run('Kpi/get_kpi');
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
                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">
                            Data Source</label>
                        <div class="col-sm-9">
                            <input name="data_source" type="text" col="6" rows="3" class="form-control" id="">
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