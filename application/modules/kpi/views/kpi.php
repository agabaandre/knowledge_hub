<div class="row">

    <div class="card col-lg-12">
        <div class="card-header text-left">
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <!-- Button trigger modal -->
                        <div class="d-flex content-justify-right">
                            <button type="button" class="btn btn-success " data-toggle="modal" data-target="#staticBackdrop" style="margin-bottom:3px; width:150px;"><i class="fa fa-plus">
                                </i>Add KPI
                            </button>
                            <button type="submit" class="btn btn-success" style="margin-bottom:3px; width:150px;"><i class="fa fa-circle">
                                </i>Update KPI
                            </button>

                            <form action="<?php echo base_url() ?>kpi/updateKpi" method="post" id="kpi-form">

                        </div>
                        <div class="card-content">
                            <table id="kpi" class="table table-responsive table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>KPI ID</th>
                                        <th>Indicator</th>
                                        <th>Subject Area</th>

                                        <th>Description</th>
                                        <th>Data Sources</th>
                                        <th>Computation</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($elements as $element) : ?>
                                        <tr class="table-row tbrow content strow">
                                            <td><?php echo $i ?></td>
                                            <td style="width:10%;"><input type="text" class="form-control" name="id[]" value="<?php echo $element->id; ?>" style="border:#000 none; width:70%;" readonly></td>
                                            <td><?php echo  $element->name; ?></td>
                                            <input type="hidden" name="subject_area[]" value="<?php echo $element->subject_area; ?>">
                                            <td style="width:20%;"><textarea name="short_name[]" rows=4 class="form-control" style="border:#000  none; width:90%;"><?php echo $element->short_name; ?></textarea></td>
                                            <td style="width:40%;"><textarea name="description[]" rows=4 class="form-control" style="border:#000  none; width:95%;"><?php echo $element->description; ?></textarea></td>
                                            <td style="width:15%;"><textarea name="data_sources[]" rows=4 class="form-control" style="border:#000  none; width:80%;"><?php echo $element->data_source; ?></textarea></td>
                                            <td style="width:25%;"><textarea name="computation[]" rows=5 class="form-control" style="border:#000  none; width:82%;"><?php echo $element->computation; ?></textarea></td>
                                        </tr>
                                    <?php
                                        $i++;
                                    endforeach;
                                    if (count($elements) == 0) {
                                        echo "<tr><td colspan='8'><center><h3 class='text-warning'>Please Add Indicators</h3></center></td></tr>";
                                    }
                                    ?>
                                    </tr>
                                </tbody>
                                </form>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('includes/kpi_modal'); ?>
    <script>
        $(document).ready(function() {
            $('#kpi').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ]
            });
        });
    </script>