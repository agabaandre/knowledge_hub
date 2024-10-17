<?php
namespace App\Imports;

use App\Models\Country;
use App\Models\Publication;
use App\Models\PublicationCategory;
use App\Models\PublicationType;
use App\Models\SubThemeticArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class PublicationImport implements ToModel,WithHeadingRow
{

    public function model(array $row)
{
    $row = array_values($row);

    // Trim the sub-thematic description from the Excel row
    $title = $row[0];
    $link = $row[1];
    $description = !empty($row[2]) ? $row[2] : ' ';
    $cover = $row[3];
    $data_category = $row[4]; //resource_type /information
    $data_sub_category = trim($row[5]); //information sub-cat
    $subtheme = trim($row[6]);
    $country = trim($row[7]);
    $citation_link = $row[8];
    $associated_authors = $row[9];
    $sub_theme_id = $this->get_subtheme($subtheme);
    $pub_cat_id = $this->get_category($data_sub_category);
    $member_state_id = $this->get_memberstate($country);

    // Get the file type using a helper
    $file_type = get_file_type(null, $link);
    $file_type_id = $file_type ? $file_type->id : null;



    // Define how to create a model from the Excel row data
    // if cover is null implement the cover from the db
    return new Publication([
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
        'geographical_coverage_id' => $member_state_id,
        'file_type_id' => $file_type_id,
        'cover_is_exteranl' => 1,
        'is_approved'=>1,
    ]);
}
public function get_subtheme($subt){
        $query = SubThemeticArea::where(DB::raw('TRIM(description)'), 'LIKE', "%$subt%");
        $sub_theme = $query->first();
       return  $sub_theme ? $sub_theme->id : null;
}
    public function get_category($cat)
    {
        $query = PublicationCategory::where(DB::raw('TRIM(category_name)'), 'LIKE', "%$cat%");
        $cat = $query->first();
        return  $cat ? $cat->id : null;
    }
    public function get_memberstate($country)
    {
        $query = Country::where(DB::raw('TRIM(name)'), 'LIKE', "$country");
        $country = $query->first();
        return $country ? $country->id : null;
    }
    
}