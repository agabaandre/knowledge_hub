<div class="row">

 <?php foreach ($year_data as $row): ?>
    <!-- support1-section start -->
    <div class="col-xl-4 col-md-6">
        <div class="card support-bar">
            <div class="card-body pb-0">
                <h2 class="m-0"><?php echo number_format($row->kpi_value,0); ?></h2>
                <span class="text-c-blue"><?php echo $row->kpi_name; ?></span>
                <p class="mb-3 mt-3">Current (<?php echo $year; ?>) Vs Previous year (<?php echo $year-1; ?>)</p>
            </div>
            <div id="support-chart1" style="height:100px;width:100%;"></div>
            <div class="card-footer bg-primary text-white">
                <div class="row text-center">
                    <div class="col">
                        <h4 class="m-0 text-white"><?php echo number_format($row->kpi_value,0); ?></h4>
                        <span>Current Year</span>
                    </div>
                    <div class="col">
                        <h4 class="m-0 text-white"><?php echo number_format($row->previous_year,0); ?></h4>
                        <span>Previous Year</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>


<!-- site-section end -->

 <?php require_once 'tabular_data.php'; ?>

</div>
