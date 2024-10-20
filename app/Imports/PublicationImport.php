<?php
namespace App\Imports;

use App\Models\Country;
use App\Models\Publication;
use App\Models\PublicationCategory;
use App\Models\SubThemeticArea;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use App\Models\DataCategory;

class PublicationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $row = array_values($row);

        // Trim the sub-thematic description from the Excel row
        $title = $row[0];
        $link = $row[1];
        $description = !empty($row[2]) ? $row[2] : ' ';
        $cover = $row[3];
        $data_category = $row[4]; // resource_type /information
        $data_sub_category = trim($row[5]); // information sub-cat
        $subtheme = trim($row[6]);
        $country = trim($row[7]);
        $citation_link = $row[8];
        $associated_authors = $row[9];

        $sub_theme_id = $this->get_subtheme(trim($subtheme));
        $pub_cat_id = $this->get_category(trim($data_sub_category));
        $member_state_ids = $this->get_memberstate(trim($country));
        $data_category_id = $this->get_data_category_id(trim($data_category));

        // Get the file type using a helper
        $file_type = get_file_type(null, $link);
        $file_type_id = $file_type ? $file_type->id : null;
       
        // Create the publication model
        $publication = new Publication([
            'title' => fix_text_encoding($title),
            'publication' => $link,
            'cover' => $cover,
            'description' => fix_text_encoding($description),
            'citation_link' => $citation_link,
            'associated_authors' => fix_text_encoding($associated_authors),
            'sub_thematic_area_id' => $sub_theme_id,
            'is_active' => 'Active',
            'publication_catgory_id' => $pub_cat_id,
            'author_id' => current_user()->author_id,
            'geographical_coverage_id' => current_user()->country_id, // Assuming this is a pivot table now
            'file_type_id' => $file_type_id,
            'cover_is_exteranl' => 1,
            'is_approved' => 1,
            'user_id' => current_user()->id,
            'data_category_id' => $data_category_id,
        ]);

        // Save the publication to get its ID
        $publication->save();

        // Attach countries to the publication
        if (!empty($member_state_ids)) {
            $publication->countries()->attach($member_state_ids);
        }

        return $publication;
    }

    public function get_subtheme($subt)
    {
        $query = SubThemeticArea::where(DB::raw('TRIM(description)'), 'LIKE', "$subt");
        $sub_theme = $query->first();

        if(!$sub_theme){
            $sub_theme = SubThemeticArea::where(DB::raw('TRIM(description)'), 'LIKE', "Other")->first();
        }

        return $sub_theme ? $sub_theme->id : null;
    }

    public function get_category($cat)
    {
        $query = PublicationCategory::where(DB::raw('TRIM(category_name)'), 'LIKE', "%$cat%");
        $cat = $query->first();
        return $cat ? $cat->id : null;
    }

    public function get_memberstate($country)
    {
        if (strtolower($country) === 'all') {
            // Get all country IDs
            return Country::pluck('id')->toArray();
        }

        $countryNames = array_map('trim', explode(',', $country));
        $countryIds = Country::whereIn('name', $countryNames)->pluck('id')->toArray();

        if (!empty($countryIds)) {
            return $countryIds;
        }

        // If no countries matched, check for region
        $region = Region::where(DB::raw('TRIM(region_name)'), 'LIKE', "%$country%")->first();
        if ($region) {
            return $region->countries()->pluck('id')->toArray();
        }

        return [];
    }

    public function get_data_category_id($category)
    {
        $dataCategory = DataCategory::where(DB::raw('TRIM(category_name)'), 'LIKE', "%$category%")->first();
        return $dataCategory ? $dataCategory->id : null;
    }
}
