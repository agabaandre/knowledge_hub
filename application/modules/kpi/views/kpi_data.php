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
                                </i>Add KPI DATA
                            </button>


                            <form action="<?php echo base_url() ?>kpi/updateKpi" method="post" id="kpi-form">

                        </div>
                        <div class="card-content">
                            <table id="kpi" class="table table-responsive table-striped table-bordered" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width:150px;">Period</th>
                                        <th style="width:300px;">Country</th>
                                        <th style="width:650px;">Indicator</th>
                                        <th style="width:100px;">Data Value</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($elements as $element) : ?>
                                        <tr class="table-row tbrow content strow">
                                            <td><?php echo $i ?></td>
                                            <td><?php echo  $element->period; ?></td>
                                            <td><?php echo $element->country->name; ?></td>
                                            <td><?php echo $element->kpi->name; ?></td>
                                            <td><?php echo $element->value; ?></td>
                                        </tr>
                                    <?php
                                        $i++;
                                    endforeach;
                                    if (count($elements) == 0) {
                                        echo "<tr><td colspan='8'><center><h3 class='text-warning'>No Data</h3></center></td></tr>";
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
    <?php $this->load->view('includes/add_data'); ?>
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