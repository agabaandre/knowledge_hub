<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateKpiDataView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the view using a raw SQL statement
        DB::statement("select `k`.`id` AS `kpi_id`,sum(`d`.`value`) AS `kpi_value`,`d`.`period` AS `period`,year(concat(`d`.`period`,'-01')) AS `period_year`,month(concat(`d`.`period`,'-01')) AS `period_month`,`k`.`name` AS `kpi_name`,`k`.`subject_area` AS `subject_area_id`,`k`.`description` AS `description`,`k`.`frequency` AS `frequency`,`c`.`name` AS `country_name`,`c`.`color` AS `country_color`,`c`.`latitude` AS `latitude`,`c`.`longitude` AS `longitude`,`c`.`id` AS `country_id` from ((`knowledge_hub`.`data` `d` join `knowledge_hub`.`kpi` `k` on((`d`.`kpi_id` = `k`.`id`))) join `knowledge_hub`.`country` `c` on((`d`.`country_id` = `c`.`id`))) group by `d`.`kpi_id`,`d`.`period`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the view if it exists
        DB::statement("DROP VIEW IF EXISTS kpi_data_view");
    }
}
