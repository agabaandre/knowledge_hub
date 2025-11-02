<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetExistingCommunitiesToAllRegionsAllCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set all existing communities to "All Regions, All Countries" (null, null)
        // and ensure they are public by default
        DB::table('community_of_practices')->update([
            'region_id' => null,
            'country_id' => null,
            'is_public' => DB::raw('COALESCE(is_public, 1)') // Set to 1 if null, otherwise keep existing value
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse this migration as we don't know what the original values were
    }
}
