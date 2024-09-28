<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateKpiData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW kpi_data AS
            SELECT 
                `k`.`id` AS `kpi_id`,
                SUM(`d`.`value`) AS `kpi_value`,
                `d`.`period` AS `period`,
                YEAR(CONCAT(`d`.`period`, '-01')) AS `period_year`,
                MONTH(CONCAT(`d`.`period`, '-01')) AS `period_month`,
                `k`.`name` AS `kpi_name`,
                `k`.`subject_area` AS `subject_area_id`,
                `k`.`description` AS `description`,
                `k`.`frequency` AS `frequency`,
                `c`.`name` AS `name`,
                `c`.`color` AS `color`,
                `c`.`id` AS `country_id`
            FROM 
                `knowledge_hub`.`data` `d`
            JOIN 
                `knowledge_hub`.`kpi` `k` ON `d`.`kpi_id` = `k`.`id`
            JOIN 
                `knowledge_hub`.`country` `c` ON `d`.`country_id` = `c`.`id`
            GROUP BY 
                `d`.`kpi_id`, `d`.`period`
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS kpi_data");
    }
}
