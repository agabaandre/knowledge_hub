<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SeedBadgeTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $badges = [
            [
                'name' => 'Silver Contributor',
                'slug' => 'silver',
                'description' => 'Awarded for 5+ contributions in a month',
                'contribution_threshold' => 5,
                'badge_color' => '#C0C0C0',
                'image_path' => null,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold Contributor',
                'slug' => 'gold',
                'description' => 'Awarded for 10+ contributions in a month',
                'contribution_threshold' => 10,
                'badge_color' => '#FFD700',
                'image_path' => null,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Platinum Contributor',
                'slug' => 'platinum',
                'description' => 'Awarded for 20+ contributions in a month',
                'contribution_threshold' => 20,
                'badge_color' => '#E5E4E2',
                'image_path' => null,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diamond Contributor',
                'slug' => 'diamond',
                'description' => 'Hall of Honor - Awarded for 40+ contributions in a month',
                'contribution_threshold' => 40,
                'badge_color' => '#B9F2FF',
                'image_path' => null,
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('badge_types')->insert($badges);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('badge_types')->whereIn('slug', ['silver', 'gold', 'platinum', 'diamond'])->delete();
    }
}
