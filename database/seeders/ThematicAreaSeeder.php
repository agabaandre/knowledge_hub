<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThematicAreaSeeder extends Seeder
{
    public function run()
    {
        DB::table('thematic_area')->insert([
            [
                'id' => 1,
                'description' => 'Global Health Security Capacities',
                'icon' => 'fa-shield-halved',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-06-11 09:59'),
                'display_index' => 1
            ],
            [
                'id' => 2,
                'description' => 'Prevention',
                'icon' => 'fa-ban',
                'created_at' => Carbon::parse('2022-10-04 08:58'),
                'updated_at' => Carbon::parse('2024-06-11 10:10'),
                'display_index' => 2
            ],
            [
                'id' => 3,
                'description' => 'Detection',
                'icon' => 'fa-microscope',
                'created_at' => Carbon::parse('2022-10-04 09:00'),
                'updated_at' => Carbon::parse('2024-06-11 10:08'),
                'display_index' => 1
            ],
            [
                'id' => 4,
                'description' => 'Response',
                'icon' => 'fa fa-ambulance',
                'created_at' => Carbon::parse('2022-10-04 09:33'),
                'updated_at' => Carbon::parse('2022-10-04 09:33'),
                'display_index' => 3
            ],
            [
                'id' => 5,
                'description' => 'Health Systems',
                'icon' => 'fa-suitcase-medical',
                'created_at' => Carbon::parse('2022-10-04 09:36'),
                'updated_at' => Carbon::parse('2024-06-11 09:58'),
                'display_index' => 1
            ],
            [
                'id' => 6,
                'description' => 'Health Risk Environment',
                'icon' => 'fa-seedling',
                'created_at' => Carbon::parse('2022-10-04 09:38'),
                'updated_at' => Carbon::parse('2024-06-11 09:56'),
                'display_index' => 5
            ],
            [
                'id' => 7,
                'description' => 'Digital Health',
                'icon' => 'fa-desktop',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-10-13 02:10'),
                'display_index' => 4
            ],
            [
                'id' => 8,
                'description' => 'Workforce Development',
                'icon' => 'fa-users-cog',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-10-13 02:10'),
                'display_index' => 9
            ],
            [
                'id' => 9,
                'description' => 'Data Analytics',
                'icon' => 'fa-chart-line',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-10-13 02:10'),
                'display_index' => 10
            ],
            [
                'id' => 10,
                'description' => 'Community Engagement',
                'icon' => 'fa-hands-helping',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-10-13 02:10'),
                'display_index' => 8
            ],
            [
                'id' => 11,
                'description' => 'Emergency Preparedness',
                'icon' => 'fa-exclamation-triangle',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-10-13 02:10'),
                'display_index' => 7
            ],
            [
                'id' => 11,
                'description' => 'Health Policy and Governance',
                'icon' => 'fa-gavel',
                'created_at' => Carbon::parse('2024-10-13 02:10'),
                'updated_at' => Carbon::parse('2024-10-13 02:10'),
                'display_index' => 6
            ]
        ]);
    }
}
