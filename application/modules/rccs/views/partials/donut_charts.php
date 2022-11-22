
<div class="col-md-3">

<?php foreach ($kpi_summary as $row): ?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header justify-content-center">
                <h5><?php echo $row->kpi_name; ?></h5>
            </div>
            <div class="card-body justify-content-center">
                <div data-label="<?php echo number_format($row->kpi_value,1); ?>" class="radial-bar radial-bar-50 radial-bar-lg radial-bar-primary">
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

</div>