<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldCountries extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $african_countries = [
            ['iso' => 'DZ', 'name' => 'Algeria', 'nicename' => 'Algeria', 'iso3' => 'DZA','numcode' => 12, 'phonecode' => '213'],
            ['iso' => 'AO', 'name' => 'Angola', 'nicename' => 'Angola', 'iso3' => 'AGO', 'numcode' => 24, 'phonecode' => '244'],
            ['iso' => 'BJ', 'name' => 'Benin', 'nicename' => 'Benin', 'iso3' => 'BEN', 'numcode' => 204, 'phonecode' => '229'],
            ['iso' => 'BW', 'name' => 'Botswana', 'nicename' => 'Botswana', 'iso3' => 'BWA', 'numcode' => 72, 'phonecode' => '267'],
            ['iso' => 'BF', 'name' => 'Burkina Faso', 'nicename' => 'Burkina Faso', 'iso3' => 'BFA', 'numcode' => 854, 'phonecode' => '226'],
            ['iso' => 'BI', 'name' => 'Burundi', 'nicename' => 'Burundi', 'iso3' => 'BDI', 'numcode' => 108, 'phonecode' => '257'],
            ['iso' => 'CM', 'name' => 'Cameroon', 'nicename' => 'Cameroon', 'iso3' => 'CMR', 'numcode' => 120, 'phonecode' => '237'],
            ['iso' => 'CV', 'name' => 'Cape Verde', 'nicename' => 'Cabo Verde', 'iso3' => 'CPV', 'numcode' => 132, 'phonecode' => '238'],
            ['iso' => 'CF', 'name' => 'Central African Republic', 'nicename' => 'Central African Republic', 'iso3' => 'CAF', 'numcode' => 140, 'phonecode' => '236'],
            ['iso' => 'TD', 'name' => 'Chad', 'nicename' => 'Chad', 'iso3' => 'TCD', 'numcode' => 148, 'phonecode' => '235'],
            ['iso' => 'KM', 'name' => 'Comoros', 'nicename' => 'Comoros', 'iso3' => 'COM', 'numcode' => 174, 'phonecode' => '269'],
            ['iso' => 'CG', 'name' => 'Congo', 'nicename' => 'Congo', 'iso3' => 'COG', 'numcode' => 178, 'phonecode' => '242'],
            ['iso' => 'CD', 'name' => 'Congo, Democratic Republic of the', 'nicename' => 'Congo, Democratic Republic of the', 'iso3' => 'COD', 'numcode' => 180, 'phonecode' => '243'],
            ['iso' => 'DJ', 'name' => 'Djibouti', 'nicename' => 'Djibouti', 'iso3' => 'DJI', 'numcode' => 262, 'phonecode' => '253'],
            ['iso' => 'EG', 'name' => 'Egypt', 'nicename' => 'Egypt', 'iso3' => 'EGY', 'numcode' => 818, 'phonecode' => '20'],
            ['iso' => 'GQ', 'name' => 'Equatorial Guinea', 'nicename' => 'Equatorial Guinea', 'iso3' => 'GNQ', 'numcode' => 226, 'phonecode' => '240'],
            ['iso' => 'ER', 'name' => 'Eritrea', 'nicename' => 'Eritrea', 'iso3' => 'ERI', 'numcode' => 232, 'phonecode' => '291'],
            ['iso' => 'ET', 'name' => 'Ethiopia', 'nicename' => 'Ethiopia', 'iso3' => 'ETH', 'numcode' => 231, 'phonecode' => '251'],
            ['iso' => 'GA', 'name' => 'Gabon', 'nicename' => 'Gabon', 'iso3' => 'GAB', 'numcode' => 266, 'phonecode' => '241'],
            ['iso' => 'GM', 'name' => 'Gambia', 'nicename' => 'Gambia', 'iso3' => 'GMB', 'numcode' => 270, 'phonecode' => '220'],
            ['iso' => 'GH', 'name' => 'Ghana', 'nicename' => 'Ghana', 'iso3' => 'GHA', 'numcode' => 288, 'phonecode' => '233'],
            ['iso' => 'GN', 'name' => 'Guinea', 'nicename' => 'Guinea', 'iso3' => 'GIN', 'numcode' => 324, 'phonecode' => '224'],
            ['iso' => 'GW', 'name' => 'Guinea-Bissau', 'nicename' => 'Guinea-Bissau', 'iso3' => 'GNB', 'numcode' => 624, 'phonecode' => '245'],
            ['iso' => 'KE', 'name' => 'Kenya', 'nicename' => 'Kenya', 'iso3' => 'KEN', 'numcode' => 404, 'phonecode' => '254'],
            ['iso' => 'LS', 'name' => 'Lesotho', 'nicename' => 'Lesotho', 'iso3' => 'LSO', 'numcode' => 426, 'phonecode' => '266'],
            ['iso' => 'LR', 'name' => 'Liberia', 'nicename' => 'Liberia', 'iso3' => 'LBR', 'numcode' => 430, 'phonecode' => '231'],
            ['iso' => 'LY', 'name' => 'Libya', 'nicename' => 'Libya', 'iso3' => 'LBY', 'numcode' => 434, 'phonecode' => '218'],
            ['iso' => 'MG', 'name' => 'Madagascar', 'nicename' => 'Madagascar', 'iso3' => 'MDG', 'numcode' => 450, 'phonecode' => '261'],
            ['iso' => 'MW', 'name' => 'Malawi', 'nicename' => 'Malawi', 'iso3' => 'MWI', 'numcode' => 454, 'phonecode' => '265'],
            ['iso' => 'ML', 'name' => 'Mali', 'nicename' => 'Mali', 'iso3' => 'MLI', 'numcode' => 466, 'phonecode' => '223'],
            ['iso' => 'MR', 'name' => 'Mauritania', 'nicename' => 'Mauritania', 'iso3' => 'MRT', 'numcode' => 478, 'phonecode' => '222'],
            ['iso' => 'MU', 'name' => 'Mauritius', 'nicename' => 'Mauritius', 'iso3' => 'MUS', 'numcode' => 480, 'phonecode' => '230'],
            ['iso' => 'YT', 'name' => 'Mayotte', 'nicename' => 'Mayotte', 'iso3' => 'MYT', 'numcode' => 175, 'phonecode' => '262'],
            ['iso' => 'MA', 'name' => 'Morocco', 'nicename' => 'Morocco', 'iso3' => 'MAR', 'numcode' => 504, 'phonecode' => '212'],
            ['iso' => 'MZ', 'name' => 'Mozambique', 'nicename' => 'Mozambique', 'iso3' => 'MOZ', 'numcode' => 508, 'phonecode' => '258'],
            ['iso' => 'NA', 'name' => 'Namibia', 'nicename' => 'Namibia', 'iso3' => 'NAM', 'numcode' => 516, 'phonecode' => '264'],
            ['iso' => 'NE', 'name' => 'Niger', 'nicename' => 'Niger', 'iso3' => 'NER', 'numcode' => 562, 'phonecode' => '227'],
            ['iso' => 'NG', 'name' => 'Nigeria', 'nicename' => 'Nigeria', 'iso3' => 'NGA', 'numcode' => 566, 'phonecode' => '234'],
            ['iso' => 'RE', 'name' => 'Reunion', 'nicename' => 'Réunion', 'iso3' => 'REU', 'numcode' => 638, 'phonecode' => '262'],
            ['iso' => 'RW', 'name' => 'Rwanda', 'nicename' => 'Rwanda', 'iso3' => 'RWA', 'numcode' => 646, 'phonecode' => '250'],
            ['iso' => 'ST', 'name' => 'Sao Tome and Principe', 'nicename' => 'Sao Tome and Principe', 'iso3' => 'STP', 'numcode' => 678, 'phonecode' => '239'],
            ['iso' => 'SN', 'name' => 'Senegal', 'nicename' => 'Senegal', 'iso3' => 'SEN', 'numcode' => 686, 'phonecode' => '221'],
            ['iso' => 'SC', 'name' => 'Seychelles', 'nicename' => 'Seychelles', 'iso3' => 'SYC', 'numcode' => 690, 'phonecode' => '248'],
            ['iso' => 'SL', 'name' => 'Sierra Leone', 'nicename' => 'Sierra Leone', 'iso3' => 'SLE', 'numcode' => 694, 'phonecode' => '232'],
            ['iso' => 'SO', 'name' => 'Somalia', 'nicename' => 'Somalia', 'iso3' => 'SOM', 'numcode' => 706, 'phonecode' => '252'],
            ['iso' => 'ZA', 'name' => 'South Africa', 'nicename' => 'South Africa', 'iso3' => 'ZAF', 'numcode' => 710, 'phonecode' => '27'],
            ['iso' => 'SS', 'name' => 'South Sudan', 'nicename' => 'South Sudan', 'iso3' => 'SSD', 'numcode' => 728, 'phonecode' => '211'],
            ['iso' => 'SH', 'name' => 'Saint Helena', 'nicename' => 'Saint Helena', 'iso3' => 'SHN', 'numcode' => 654, 'phonecode' => '290'],
            ['iso' => 'SD', 'name' => 'Sudan', 'nicename' => 'Sudan', 'iso3' => 'SDN', 'numcode' => 729, 'phonecode' => '249'],
            ['iso' => 'SZ', 'name' => 'Swaziland', 'nicename' => 'Eswatini', 'iso3' => 'SWZ', 'numcode' => 748, 'phonecode' => '268'],
            ['iso' => 'TZ', 'name' => 'Tanzania, United Republic of', 'nicename' => 'Tanzania', 'iso3' => 'TZA', 'numcode' => 834, 'phonecode' => '255'],
            ['iso' => 'TG', 'name' => 'Togo', 'nicename' => 'Togo', 'iso3' => 'TGO', 'numcode' => 768, 'phonecode' => '228'],
            ['iso' => 'TN', 'name' => 'Tunisia', 'nicename' => 'Tunisia', 'iso3' => 'TUN', 'numcode' => 788, 'phonecode' => '216'],
            ['iso' => 'UG', 'name' => 'Uganda', 'nicename' => 'Uganda', 'iso3' => 'UGA', 'numcode' => 800, 'phonecode' => '256'],
            ['iso' => 'EH', 'name' => 'Western Sahara', 'nicename' => 'Western Sahara', 'iso3' => 'ESH', 'numcode' => 732, 'phonecode' => '212'],
            ['iso' => 'ZM', 'name' => 'Zambia', 'nicename' => 'Zambia', 'iso3' => 'ZMB', 'numcode' => 894, 'phonecode' => '260'],
            ['iso' => 'ZW', 'name' => 'Zimbabwe', 'nicename' => 'Zimbabwe', 'iso3' => 'ZWE', 'numcode' => 716, 'phonecode' => '263']];

        // dd($african_countries);
            DB::table('world_countries')->insert($african_countries);
    }

}
