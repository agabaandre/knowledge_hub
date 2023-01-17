<div class="row">

<?php 

//print_r(json_encode(user_session())); 

//rerender regios user has access to
//render regions' countries
//filter data accordingly

?>

    <div class="col-md-12">

    <div class="card">
        <div class="card-body">

    
        <form class="filter_form" method="get" action="<?php echo base_url("rccdashboards/index"); ?>">
            <div class="row">
                <div class="form-group col-md-3">
                    <br>
                    <label>Region</label>
                    <select class="form-control select2" name="region_id" onchange="$('.filter_form').submit();">
                        <option value="">All</option>
                        <?php foreach ($regions as $region) : ?>
                            <option <?php echo (isset($filter['region_id']) && $filter['region_id'] == $region->id) ? "selected" : ""; ?> value="<?php echo $region->id; ?>"><?php echo $region->region_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <br>
                    <label>Member State</label>
                    <select class="form-control select2 countries" name="country_id" onchange="$('.filter_form').submit();">
                        <option value="">None</option>
                        <?php foreach($countries as $country): ?>
                        <option value="<?php echo $country->id; ?>"  <?php echo (isset($filter['country_id']) && $filter['country_id'] == $country->id) ? "selected" : ""; ?>>
                        <?php echo $country->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <div class="form-group col-md-3">
                <br>
                <label>Period</label>
                <select class="form-control select2" name="period_year" onchange="fetchData()">
                    <option value="">All</option>
                    <?php foreach ($years as $year) : ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <br>
                <label>Visualisation Type</label>
                <select class="form-control select2" onchange="switchChartType($(this).val())">
                    <option value="spline">Line Chart</option>
                    <option value="bar">Bar Graph</option>
                </select>
            </div>
            </div>
</form>

</div>
</div>
            
</div>
</div>


<?php 

$valid_data =0;

foreach($years_data as $widget_data):

$year_data = $widget_data['data'];

if(count($year_data)>0):

$valid_data++;

 ?>

<h3 class="theme-primary"><?php echo $widget_data['subject_area']; ?></h3>

<?php 

    require 'partials/top_summary_widgets.php';

?>

<div class="row">
    <div class="card-body">
        <div id="countries_summary<?php echo  $widget_data['subject_area_id']; ?>" style="width: 100%; height: 350px; display:flex; justify-content:center; align-items:center;">
         <h1 class="text-muted">Loading  Graph...</h1>
        </div>
    </div>
</div>

<?php 

endif;
endforeach; 

//if we have some data
if($valid_data == 0):
 ?>

<div class="row justify-content-center align-items-center py-3">
    <h3 class="text-muted"><i class="fa fa-info-circle"></i> Dataset empty! </h3>
</div>

<?php endif; ?>


<script type="text/javascript">
    var chartType = 'spline';
    var seriesData = null;
    var reRender = false;
    var chart = null;


    function switchChartType(type) {
        //var ui_chart = $('#countries_summary').highcharts();
        chart.series.forEach(function(item) {
            item.update({
                type: type
            });
        });
    }


    function fetchData(subjectAreaId) {


        $.ajax({
            data: $('.filter_form').serialize(),
            url: `<?php echo base_url(); ?>rccdashboards/kpi_comparison_data/${subjectAreaId}`,
            success: function(response) {
                
                console.log('data response',response);
                seriesData = JSON.parse(response);

                renderChart(subjectAreaId);
                //hideLoader();
            },
            error: function(error) {
                console.log(error);
                //hideLoader();
                alert("Server error stopped the operation");
            }
        });

    }


    function renderChart(subjectAreaId) {

        // [ line-basic-chart ] Start
        chart = Highcharts.chart(`countries_summary${subjectAreaId}`, {
            chart: {
                type: chartType,
            },
            colors: Array(seriesData.data.length).fill().map(() => `#${Math.floor(Math.random()*16777215).toString(16)}`),
            title: {
                text: 'Indicator Analysis'
            },
            subtitle: {
                text: 'Source: Africa CDC'
            },
            yAxis: {
                title: {
                    text: 'Indicator Value'
                }
            },
            xAxis: {
                categories: seriesData.labels
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            series: seriesData.data,
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
    }


    $(document).ready(function() {

        //showLoader();

        var widget_data = JSON.parse('<?php echo json_encode($years_data); ?>');

        console.log(widget_data);

        widget_data.forEach((subjectArea)=>{

          if(subjectArea.data.length>0)
          fetchData(subjectArea.subject_area_id);

        });

    });
</script>