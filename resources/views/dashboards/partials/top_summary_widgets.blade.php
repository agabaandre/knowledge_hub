<div class="row">

 <?php foreach ($year_data as $row): ?>
    <!-- support1-section start -->
    <div class="col-xl-3 col-md-4">
        <div class="card" style="background-color: transparent!important;">
            <div class="card-body pb-0 bg-white">
                <h4 class="m-0"><?php echo number_format($row->kpi_value,0); ?></h4>
                <span class="text-primary"><?php echo $row->kpi_name; ?></span>
                <br>
                <small class="mb-3 mt-3 text-muted">Current (<?php echo $year; ?>) Vs Previous year (<?php echo $year-1; ?>)</small>
            </div>
           
            <div class="card-footer bg-primary text-white" style="padding: 5px!important; margin:0px!important;">
                <div class="row text-center  py-0">
                    <div class="col pb-0">
                        <h5 class="m-0 text-white"><?php echo number_format($row->kpi_value,0); ?></h5>
                        <span>Current Year</span>
                    </div>
                    <div class="col">
                        <h5 class="m-0 text-white"><?php echo number_format($row->previous_year,0); ?></h5>
                        <span>Previous Year</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>


</div>
